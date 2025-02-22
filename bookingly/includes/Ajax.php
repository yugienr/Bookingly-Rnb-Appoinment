<?php

namespace Bookingly;
use Carbon\Carbon;

/**
 * Ajax class
 */
class Ajax
{
    /**
     * Initialize ajax class
     */
    public function __construct()
    {
        add_action('wp_ajax_get_available_slot', [$this, 'get_available_slot']);
        add_action('wp_ajax_nopriv_get_available_slot', [$this, 'get_available_slot']);
        add_action('woocommerce_order_status_changed', [$this, 'update_rnb_meta_on_cancel'], 10, 4);
    }

    /**
     * Get available slots
     *
     * @return void
     */
public function get_available_slot() {
    check_ajax_referer('bookingly-slots', 'nonce');
    
    if (!isset($_POST['check_in_date'], $_POST['check_out_date'], $_POST['inventoryId'], $_POST['productId'])) {
        wp_send_json_error(['message' => __('Missing parameters', 'bookingly')]);
        return;
    }

    $checkInDate = $_POST['check_in_date'];
    $checkOutDate = $_POST['check_out_date'];
    $inventoryId = $_POST['inventoryId'];
    $productId = $_POST['productId'];
    $date = $checkInDate; // Define $date variable

    global $wpdb;
    $query = $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}rnb_availability
        WHERE check_in_date >= %s AND check_out_date <= %s
        AND product_id=%d AND inventory_id=%d",
        $checkInDate, $checkOutDate, $productId, $inventoryId
    );
    $results = $wpdb->get_results($query);

    $bookedSlots = [];
    foreach ($results as $result) {
        $rentalItem = wc_get_order_item_meta($result->item_id, 'rnb_hidden_order_meta', true);
        $postedData = isset($rentalItem['posted_data']) ? $rentalItem['posted_data'] : [];
        $bookedSlots[] = isset($postedData['slot_id']) ? $postedData['slot_id'] : null;
    }

    $current_date = current_time("Y-m-d");
    $current_time = current_time("H.i");  
    $allowed_times_settings = redq_rental_get_settings($productId, 'validations', 'allowed_times');
    $daily_allowed_time = isset($allowed_times_settings['validations']['openning_closing']) ? $allowed_times_settings['validations']['openning_closing'] : [];
    $custom_blocked_dates = $this->get_custom_blocked_dates($productId);

    $slots = []; // Define $slots variable
    $final_slots = array_filter($slots, function ($slot) use ($current_date, $current_time, $bookedSlots, $date, $daily_allowed_time, $custom_blocked_dates) {
        $day_of_week = strtolower(date('D', strtotime($date)));
        
        // Convert slot time from decimal to HH:MM format for comparison
        $slot_hour = floor($slot['start_time']); 
        $slot_minute = ($slot['start_time'] - $slot_hour) * 60;
        $slot_time = sprintf("%02d:%02d", $slot_hour, $slot_minute);

        // Convert slot time to decimal for blocked date comparison
        $slot_time_decimal = $slot['start_time'];
        
        // Get allowed min/max times for the day
        $allowed_min = isset($daily_allowed_time[$day_of_week]['min']) ? $daily_allowed_time[$day_of_week]['min'] : '00:00';
        $allowed_max = isset($daily_allowed_time[$day_of_week]['max']) ? $daily_allowed_time[$day_of_week]['max'] : '24:00';

        // Convert allowed times to decimal for proper comparison
        $allowed_min_parts = explode(':', $allowed_min);
        $allowed_max_parts = explode(':', $allowed_max);
        $allowed_min_decimal = (float)$allowed_min_parts[0] + ((float)$allowed_min_parts[1] / 60);
        $allowed_max_decimal = (float)$allowed_max_parts[0] + ((float)$allowed_max_parts[1] / 60);
        
        // Check if slot is within allowed time range
        $is_within_allowed_time = ($slot_time_decimal >= $allowed_min_decimal && $slot_time_decimal <= $allowed_max_decimal);

        // Check if slot falls within any custom blocked dates
        $is_blocked = false;
        foreach ($custom_blocked_dates as $blocked) {
            if ($blocked['date'] === $date) {
                // Convert blocked times to decimal
                $blocked_start_parts = explode(':', $blocked['start_time']);
                $blocked_end_parts = explode(':', $blocked['end_time']);
                $blocked_start_decimal = (float)$blocked_start_parts[0] + ((float)$blocked_start_parts[1] / 60);
                $blocked_end_decimal = (float)$blocked_end_parts[0] + ((float)$blocked_end_parts[1] / 60);

                if ($slot_time_decimal >= $blocked_start_decimal && $slot_time_decimal <= $blocked_end_decimal) {
                    $is_blocked = true;
                    break;
                }
            }
        }
        
        return !in_array($slot['id'], $bookedSlots) &&
            ($date > $current_date || ($date == $current_date && $slot_time_decimal > (float)str_replace('.', '', $current_time) / 100)) &&
            $is_within_allowed_time &&
            !$is_blocked;
    });

    usort($final_slots, function($a, $b) {
        return floatval($a['start_time']) - floatval($b['start_time']); 
    });

    $templateArgs = [
        'slots' => $final_slots,
        'date' => $date,
    ];

    ob_start();
    bookingly_get_template('bookingly/slots.php', $templateArgs);
    $slotsFormatted = ob_get_clean();

    wp_send_json_success([
        'message'        => __('Perform your operation', 'bookingly'),
        'slots'          => $slots,
        'slotsFormatted' => $slotsFormatted,
        'selected_date'  => $date
    ]);
}


        /**
         * Update rental meta data when order is cancelled
         * 
         * @param int $order_id Order ID
         * @param string $old_status Previous order status
         * @param string $new_status New order status
         * @param object $order Order object
         * @return void
         */
        public function update_rnb_meta_on_cancel($order_id, $old_status, $new_status, $order) {
            if ($new_status !== 'cancelled') {
                return;
            }
            foreach ($order->get_items() as $item) {
                wc_update_order_item_meta($item->get_id(), 'rnb_hidden_order_meta', '');
            }
        }
        private function get_custom_blocked_dates( $productId ) {
            global $wpdb;
            $blocked_dates = $wpdb->get_results($wpdb->prepare(
                "SELECT pickup_datetime, return_datetime 
                FROM {$wpdb->prefix}rnb_availability 
                WHERE product_id = %d 
                AND block_by = 'CUSTOM'
                AND delete_status = 0 ",
                $productId
            ));
            $dates = [];
            if (!empty($blocked_dates)) {
                foreach ($blocked_dates as $blocked) {
                    $start = new \DateTime($blocked->pickup_datetime);
                    $end = new \DateTime($blocked->return_datetime);
                    $interval = new \DateInterval('P1D');
                    $period = new \DatePeriod($start, $interval, $end);
                    
                    foreach ($period as $date) {
                        $dates[] = [
                            'date' => $date->format('Y-m-d'),
                            'start_time' => $start->format('H:i'),
                            'end_time' => $end->format('H:i')
                        ];
                    }
                    // Include the end date
                    $dates[] = [
                        'date' => $end->format('Y-m-d'),
                        'start_time' => $start->format('H:i'), 
                        'end_time' => $end->format('H:i')
                    ];
                }
            }
            return array_unique($dates, SORT_REGULAR);
        }
}
