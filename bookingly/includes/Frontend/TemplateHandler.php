<?php

namespace Bookingly\Frontend;

/**
 * Shortcode class
 */
class TemplateHandler
{
    /**
     * Initialize class
     */
    public function __construct()
    {
        add_filter('rnb_main_content_priority', [$this, 'bookingly_content_priority'], 10, 1);
        add_filter('rnb_resource_input_type', [$this, 'resource_input_type'], 10, 1);
        add_filter('rnb_persons_input_type', [$this, 'person_input_type'], 10, 1);
        add_filter('rnb_quantity_field_type', [$this, 'quantity_field_type'], 10, 1);
        add_action('rnb_main_rental_content', [$this, 'appointment_duration_handler'], 21);
        add_filter('woocommerce_product_price_class', [$this, 'product_price_class'], 10, 1);
        add_filter('rnb_product_price_class', [$this, 'product_price_class'], 10, 2);
        add_filter('rnb_validate_fields', [$this, 'validate_fields'], 10, 4);
    }

    /**
     * Set content priority
     *
     * @param array $priority
     * @return array
     */
    public function bookingly_content_priority($priority)
    {
        global $product;

        $bookingly_product = is_bookingly_product($product->get_id());
        if (empty($bookingly_product)) {
            return $priority;
        }

        return [
            'select_inventory'   => 5,
            'payable_resources'  => 10,
            'payable_persons'    => 15,
            'pickup_locations'   => 20,
            'return_locations'   => 25,
            'pickup_datetimes'   => 30,
            'return_datetimes'   => 35,
            'quantity'           => 40,
            'payable_categories' => 45,
            'payable_deposits'   => 50,
        ];
    }

    public function resource_input_type($type)
    {
        global $product;

        $bookingly_product = is_bookingly_product($product->get_id());
        if (empty($bookingly_product)) {
            return $type;
        }

        return 'select';
    }

    public function person_input_type($type)
    {
        global $product;

        $bookingly_product = is_bookingly_product($product->get_id());
        if (empty($bookingly_product)) {
            return $type;
        }

        return 'select-alt';
    }

    public function quantity_field_type($type)
    {
        global $product;

        $bookingly_product = is_bookingly_product($product->get_id());
        if (empty($bookingly_product)) {
            return $type;
        }

        return 'hidden';
    }

    public function appointment_duration_handler()
    {
        global $product;

        $bookingly_product = is_bookingly_product($product->get_id());
        if (empty($bookingly_product)) {
            return false;
        }

        remove_action('rnb_main_rental_content', 'rnb_pickup_datetimes', 30);
        remove_action('rnb_main_rental_content', 'rnb_return_datetimes', 35);

        wp_enqueue_script('flatpickr.min');
        wp_enqueue_script('flatpickr.locale');
        wp_enqueue_style('flatpickr.min');
        wp_enqueue_script('slick.min');
        wp_enqueue_style('slick');
        wp_enqueue_style('bookingly-style');
        wp_enqueue_script('bookingly-script');

        wp_enqueue_style('select2.min');
        wp_enqueue_script('select2.min');

        wp_localize_script('bookingly-script', 'bookingly', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bookingly-slots'),
        ]);


        $data = bookingly_get_settings($product->get_id(), 'conditions', ['free_appointment', 'pre_block_days']);


        bookingly_get_template('bookingly/duration.php');
    }

    public function product_price_class($class, $product = null)
    {
        if (is_null($product)) {
            global $product;
        }

        $bookingly_product = is_bookingly_product($product->get_id());
        if (empty($bookingly_product)) {
            return $class;
        }

        $class .= ' appointment-product-price ';

        return $class;
    }

    public function validate_fields($fields, $validations, $messages, $product_id)
    {
        $bookingly_product = is_bookingly_product($product_id);
        if (empty($bookingly_product)) {
            return $fields;
        }

        $fields = [
            [
                'selector' => "select[name='additional_adults_info']",
                'message' => esc_html__('Employee field is required', 'bookingly'),
                'titleTag' => 'h5',
            ],
            [
                'selector' => "select[name='pickup_location']",
                'message' => esc_html__('Location field is required', 'bookingly'),
                'titleTag' => 'h5',
            ],
            [
                'selector' => "select[name='extras[]']",
                'message' => esc_html__('Service field is required', 'bookingly'),
                'titleTag' => 'h5',
            ]
        ];

        return $fields;
    }
}