<?php

namespace Bookingly\Frontend;

use Bookingly\Traits\AppointmentTrait;

/**
 * Shortcode class
 */
class RnBHandler
{
    use AppointmentTrait;

    /**
     * Initialize class
     */
    public function __construct()
    {
        add_filter('rnb_calculate_rental_cost_args', [$this, 'calculate_cost_args'], 10, 4);
        add_filter('rnb_rental_duration_costs', [$this, 'calculate_duration_cost'], 10, 6);
        add_filter('rnb_prepared_form_data', [$this, 'prepared_form_data'], 10, 2);
        add_filter('rnb_inventory_quantity_by_date', [$this, 'inventory_quantity_by_date'], 10, 3);
        add_filter('rnb_format_rental_item_data', [$this, 'format_rental_item_data'], 10, 4);
        add_filter('rearrange_breakdown_summary', [$this, 'rearrange_breakdown_summary'], 10, 4);
        add_filter('rnb_allow_free_booking', [$this, 'allow_free_booking'], 10, 3);
    }

    public function calculate_cost_args($args, $post_form, $inventory_id, $product_id)
    {
        if (!(isset($post_form['slot_id']) && !empty($post_form['slot_id']))) {
            return $args;
        }

        $args['appointment_slot'] = $this->get_appointment_slot_data($post_form['slot_id']);

        return $args;
    }

    public function calculate_duration_cost($prices, $inventoryId, $productId, $durations, $pricing, $args)
    {
        if (!(isset($args['appointment_slot']) && !empty($args['appointment_slot']))) {
            return $prices;
        }

        $result = $this->calculate_appointment_slot_price($args['appointment_slot']);

        $price = [
            'costByHours' => $result['price'],
            'costByDays' => 0
        ];

        return $price;
    }

    public function prepared_form_data($data, $post_form)
    {
        if (!(isset($post_form['slot_id']) && !empty($post_form['slot_id']))) {
            return $data;
        }

        $result = $this->calculate_appointment_slot_price($post_form['slot_id']);
        if (empty($result)) {
            return $data;
        }

        $data['appointment_info'] = $result;

        return $data;
    }

    public function inventory_quantity_by_date($quantity, $product_id, $args)
    {
        if(is_bookingly_product($product_id)) {
            return 1;
        }
        return $quantity;
    }

    public function format_rental_item_data($formatted_data, $product_id, $posted_data, $settings)
    {
        if (!isset($posted_data['appointment_info'])) {
            return $formatted_data;
        }

        $results = [];
        $ignored_keys = ['inventory', 'pickup_datetime', 'return_datetime', 'duration'];

        $data = $this->prepare_appointment_info_data($posted_data, $settings);
        $formatted_data['appointment_data'] = $data;

        foreach ($formatted_data as $key => $data) {
            if (in_array($key, $ignored_keys)) {
                continue;
            }
            $results[$key] = $data;
        }

        return array_reverse($results);
    }

    public function rearrange_breakdown_summary($summary, $item_data, $product_id, $posted_data)
    {
        if (isset($summary['total'])) {
            $total = $summary['total'];
            unset($summary['total']);
            $summary['total'] = $total;
        }

        return $summary;
    }

    public function allow_free_booking($free_booking, $cost, $product_id)
    {
        $appointment_product = is_bookingly_product($product_id);
        if (empty($appointment_product)) {
            return $free_booking;
        }

        $conditions = bookingly_get_settings($product_id, 'conditions', ['free_appointment']);
        if (isset($conditions['free_appointment']) && $conditions['free_appointment'] === 'yes') {
            return true;
        }

        if (empty($cost)) {
            return false;
        }

        return $free_booking;
    }

}
