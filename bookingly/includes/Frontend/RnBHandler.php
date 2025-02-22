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

    public function calculate_cost_args($args, $post_form, $inventory_id, $product_id) {
  if (isset($post_form['check_in_date']) && isset($post_form['check_out_date'])) {
    $args['check_in_date'] = $post_form['check_in_date'];
    $args['check_out_date'] = $post_form['check_out_date'];
    $args['room_type'] = $post_form['room_type'];
  }
  return $args;
}

public function calculate_duration_cost($prices, $inventoryId, $productId, $durations, $pricing, $args) {
  if (isset($args['check_in_date']) && isset($args['check_out_date'])) {
    $checkInDate = new \DateTime($args['check_in_date']);
    $checkOutDate = new \DateTime($args['check_out_date']);
    $interval = $checkInDate->diff($checkOutDate)->days;

    $price = [
      'costByDays' => $interval * $pricing['daily_rate'],
      'costByHours' => 0
    ];

    return $price;
  }
  return $prices;
}

public function prepared_form_data($data, $post_form) {
  if (isset($post_form['check_in_date']) && isset($post_form['check_out_date'])) {
    $data['check_in_date'] = $post_form['check_in_date'];
    $data['check_out_date'] = $post_form['check_out_date'];
    $data['room_type'] = $post_form['room_type'];
  }
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
