<?php

namespace Bookingly\Admin;

/**
 * Handles the administrative functionality for the Bookingly plugin.
 */
class AdminGenerator
{
    /**
     * Constructor: Adds necessary hooks for the admin functionality.
     */
    function __construct()
    {
        add_filter('rnb_register_inventory_taxonomy', [$this, 'register_taxonomy'], 10, 1);
        add_filter('rnb_inventory_term_meta_args', [$this, 'term_meta_args'], 100, 1);
        add_filter('rnb_pricing_types', [$this, 'pricing_types'], 10, 1);
        add_filter('rnb_before_pricing_type_panel', [$this, 'appointment_pricing_panel'], 1, 1);
        add_action('woocommerce_process_product_meta', array($this, 'save_product_meta'), 10, 2);
    }

    /**
     * Registers a custom taxonomy for booking slots.
     *
     * @param array $taxonomy_args Existing taxonomy arguments.
     * @return array Modified taxonomy arguments.
     */
    public function register_taxonomy($taxonomy_args)
    {
        $taxonomy_args[] = [
            'taxonomy' => 'booking_slot',
            'label' => __('Booking Slots', 'bookingly'),
            'post_type' => 'inventory'
        ];

        return $taxonomy_args;
    }

    /**
     * Defines term metadata arguments for booking slots.
     *
     * @param array $args Current term meta arguments.
     * @return array Modified term meta arguments.
     */
    public function term_meta_args($args)
    {
        $args[] = [
            'taxonomy' => 'booking_slot',
            'args' => [
                'title'       => __('Slot Full Name<span class="bookingly-required">*<span>', 'bookingly'),
                'type'        => 'text',
                'id'          => 'booking_slot_full_name',
                'column_name' => __('Full Name', 'bookingly'),
                'placeholder' => __('Ex. 12pm - 1pm', 'bookingly'),
                'desc'        => __('This value will show as slot in front-end when someone click date from calendar.', 'bookingly'),
                'required'    => true,
            ]
        ];

        $args[] = [
            'taxonomy' => 'booking_slot',
            'args' => [
                'title'       => __('Start Time<span class = "bookingly-required">*<span>', 'bookingly'),
                'type'        => 'text',
                'id'          => 'booking_slot_start',
                'column_name' => __('Start Time', 'bookingly'),
                'placeholder' => __('Ex. 12', 'bookingly'),
                'desc'        => __('Enter this value as 24-hour format', 'bookingly'),
                'required'    => true,
            ]
        ];

        $args[] = [
            'taxonomy' => 'booking_slot',
            'args' => [
                'title'       => __('End Time<span class="bookingly-required">*<span>', 'bookingly'),
                'type'        => 'text',
                'id'          => 'booking_slot_end',
                'column_name' => __('End Time', 'bookingly'),
                'placeholder' => __('Ex. 13', 'bookingly'),
                'desc' => __('Enter this value as 24-hour format', 'bookingly'),
                'required'    => true,
            ]
        ];

        $args[] = [
            'taxonomy' => 'booking_slot',
            'args' => [
                'title'       => __('Slot Price', 'bookingly'),
                'type'        => 'text',
                'id'          => 'booking_slot_price',
                'column_name' => __('Cost', 'bookingly'),
                'placeholder' => __('Slot Price', 'bookingly'),
                'desc' => __('This value is option', 'bookingly'),
                'text_type'   => 'price',
                'required'    => false,
            ]
        ];

        $seen = [];
        $remove_duplicate = function ($entry) use (&$seen) {
            $hash = json_encode($entry);
            if (in_array($hash, $seen)) {
                return null;
            } else {
                $seen[] = $hash;
                return $entry;
            }
        };

        $filtered_args = array_filter(array_map($remove_duplicate, $args));
        $filtered_args = array_values($filtered_args);

        return $filtered_args;
    }

    /**
     * Adds a custom pricing type option.
     *
     * @param array $options Existing pricing type options.
     * @return array Modified pricing type options.
     */
    public function pricing_types($options)
    {
        $options['appointment_pricing'] = esc_html__('Appointment Pricing', 'bookingly');
        return $options;
    }

    /**
     * Renders the appointment pricing panel.
     *
     * @param int $post_id Post ID.
     */
    public function appointment_pricing_panel($post_id)
    {
        wp_enqueue_style('select2.min');
        wp_enqueue_script('select2.min');
        wp_enqueue_script('bookingly-admin');

        ob_start();
        include __DIR__ . '/views/slot-pricing-panel.php';
        echo ob_get_clean();
    }

    /**
     * Saves product metadata when a product is saved.
     *
     * @param int $post_id Post ID of the product.
     * @param WP_Post $post Post object.
     */
    public function save_product_meta($post_id, $post)
    {
        // if (isset($_POST['_bookingly_slot'])) {
        //     update_post_meta($post_id, '_bookingly_slot', $_POST['_bookingly_slot']);
        // }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        $meta_defaults = [
            '_bookingly_allow_free_appointment' => 'no',
            '_bookingly_layout_type' => 'full_calender',
        ];

        foreach ($meta_defaults as $key => $default) {
            $value = isset($_POST[$key]) ? $_POST[$key] : $default;
            update_post_meta($post_id, $key, sanitize_text_field($value));
        }
    }
}
