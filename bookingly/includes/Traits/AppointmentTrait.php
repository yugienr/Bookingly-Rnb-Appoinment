<?php

namespace Bookingly\Traits;

use Carbon\Carbon;

/**
 * Test Trait
 */
trait AppointmentTrait
{
    public function get_appointment_slot_data($term_id, $taxonomy = 'booking_slot')
    {
        if (!$term_id) {
            return;
        }

        return $term_id;
    }

    public function calculate_appointment_slot_price($slot_id)
    {
        return bookingly_get_slot_details_by_id($slot_id);
    }

    /**
     * Prepare appointment slot data for order
     *
     * @param array $data
     * @param array $settings
     * @return array
     */
    public function prepare_appointment_info_data($data, $settings)
    {
        $format = $settings['conditions']['date_format'];

        $date_obj = Carbon::createFromFormat('Y-m-d', $data['pickup_date']);
        $name = $date_obj->format($format);

        $appointment_info = $data['appointment_info'];
        if (isset($appointment_info['full_name'])) {
            $name .= ' at ' . $appointment_info['full_name'];
        }

        $results = [
            'type'        => 'single',
            'summary'     => false,
            'key'         => 'Date',
            'summary_key' => 'Date',
            'data'        => [
                'name' => $name
            ],
        ];

        return apply_filters('rnb_prepare_appointment_data', $results, $data, $settings);
    }

    public function get_custom_block_dates_by_product($productId, $intervalMinutes)
    {
        global $wpdb;

        $tableName = $wpdb->prefix . 'rnb_availability';
        $query = $wpdb->prepare(
            "SELECT pickup_datetime, return_datetime FROM $tableName WHERE product_id = %d AND block_by = 'CUSTOM'",
            $productId
        );

        $results = $wpdb->get_results($query);
        if (empty($results)) {
            return [];
        }

        $blockedDates = [];
        foreach ($results as $row) {
            $pickup = new Carbon($row->pickup_datetime);
            $return = new Carbon($row->return_datetime);

            $totalIntervalSeconds = $intervalMinutes * 60;
            $diffInSeconds = $return->diffInSeconds($pickup);
            $numFullDaysPlusInterval = floor(($diffInSeconds + $totalIntervalSeconds) / (24 * 60 * 60));

            for ($day = 0; $day < $numFullDaysPlusInterval; $day++) {
                $currentDay = clone $pickup;
                $currentDay->addDays($day)->startOfDay();

                $dateString = $currentDay->toDateString();
                if (!in_array($dateString, $blockedDates)) {
                    $blockedDates[] = $dateString;
                }
            }
        }

        return array_unique($blockedDates);
    }
}
