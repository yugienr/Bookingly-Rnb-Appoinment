<?php


function is_bookingly_product($product_id)
{
    if (!function_exists('rnb_get_inventory_by_product')) {
        return false;
    }

    $response = rnb_get_inventory_by_product($product_id);

    if (empty($response['success'])) {
        return false;
    }

    $types = [];
    $inventories = $response['inventories'];

    foreach ($inventories as $inventory_id) {
        $types[] = get_post_meta($inventory_id, 'pricing_type', true);
    }

    return count($types) === 1 && $types[0] === 'appointment_pricing';
}

/**
 * Get post meta if it exists and is not empty; otherwise, return a default value.
 *
 * @param int $post_id The ID of the post.
 * @param string $key The meta key to retrieve.
 * @param mixed $default The default value to return if the meta key doesn't exist or is empty.
 * @return mixed The value of the meta key if it exists and is not empty, or the default value.
 */
function bookingly_post_meta($post_id, $key, $default)
{
    $meta_value = get_post_meta($post_id, $key, true);
    return !empty($meta_value) ? $meta_value : $default;
}

/**
 * Get parent slots
 *
 * @param string $taxonomy
 * @return array
 */
function bookingly_get_parent_slots($taxonomy = 'booking_slot')
{
    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
        'parent'     => 0,
    ]);

    if (empty($terms)) {
        return [];
    }

    $results = [
        '' => esc_html__('Choose', 'bookingly'),
    ];
    foreach ($terms as $term) {
        $results[$term->term_id] = $term->name;
    }

    return $results;
}

/**
 * Get children terms
 *
 * @param int $parent_id
 * @param string $taxonomy
 * @return array
 */
function bookingly_get_children($parent_id, $taxonomy = 'booking_slot')
{
    $child_terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
        'child_of'   => $parent_id,
    ]);

    $results = [];

    foreach ($child_terms as $key => $term) {
        $results[$key] = [
            'id'     => $term->term_id,
            'name'   => $term->name,
            'slug'   => $term->slug,
            'parent' => $term->parent,
        ];

        $start_time = get_term_meta($term->term_id, 'booking_slot_start', true);
        $end_time = get_term_meta($term->term_id, 'booking_slot_end', true);
        $price = get_term_meta($term->term_id, 'booking_slot_price', true);

        $results[$key]['start_time'] = $start_time;
        $results[$key]['end_time'] = $end_time;
        $results[$key]['price'] = $price;
    }

    return $results;
}

/**
 * Get all slots by a inventory
 *
 * @param int $inventory_id
 * @param string $taxonomy
 * @return array
 */
function bookingly_get_slots($inventory_id, $taxonomy = 'booking_slot')
{
    $results = [];
    $slots = get_the_terms($inventory_id, 'booking_slot');

    foreach ($slots as $key => $term) {
        $results[$key] = bookingly_get_slot_details_by_id($term->term_id);
    }

    return $results;
}

/**
 * Get slots by id
 *
 * @param int $term_id
 * @return array
 */
function bookingly_get_slot_details_by_id($term_id)
{
    $taxonomy = 'booking_slot';
    $term = get_term_by('id', $term_id, $taxonomy);

    if (empty($term)) {
        return [];
    }

    $results = [
        'id'     => $term->term_id,
        'name'   => $term->name,
        'slug'   => $term->slug,
        'parent' => $term->parent,
    ];

    $full_name  = get_term_meta($term->term_id, 'booking_slot_full_name', true);
    $start_time = get_term_meta($term->term_id, 'booking_slot_start', true);
    $end_time   = get_term_meta($term->term_id, 'booking_slot_end', true);
    $price      = get_term_meta($term->term_id, 'booking_slot_price', true);

    $results['full_name'] = $full_name;
    $results['start_time'] = $start_time;
    $results['end_time'] = $end_time;
    $results['price'] = $price ? $price : 0;

    return $results;
}

function bookingly_get_settings($product_id, $type, $posted_keys)
{
    $results      = [];
    $is_local     = get_post_meta($product_id, "rnb_settings_for_{$type}", true) === 'local';
    $settings_map = bookingly_get_settings_map($type);

    foreach ($settings_map as $common_key => $keys) {
        if (!in_array($common_key, $posted_keys)) {
            continue;
        }

        $local_key = $keys['local'];
        $global_key = $keys['global'];

        if ($is_local && !empty($local_key)) {
            $results[$common_key] = get_post_meta($product_id, $local_key, true);
        } else if (!empty($global_key)) {
            $results[$common_key] = get_option($global_key);
        }
    }

    return $results;
}

/**
 * Returns the settings map based on the type.
 *
 * @param string $type Type of the settings.
 * @return array Associative array of settings keys.
 */
function bookingly_get_settings_map($type)
{
    switch ($type) {
        case 'general':
            return bookingly_get_general_keys();
        case 'display':
            return bookingly_get_display_keys();
        case 'conditions':
            return bookingly_get_conditional_keys();
        case 'validations':
            return bookingly_get_validation_keys();
        case 'layout_two':
            return bookingly_get_layout_two_keys();
        default:
            return bookingly_get_labels_keys();
    }
}

function bookingly_get_general_keys()
{
    return [];
}

function bookingly_get_display_keys()
{
    return [];
}


function bookingly_get_conditional_keys()
{
    $settings_map = [
        'free_appointment' => [
            'local' => '_bookingly_allow_free_appointment',
            'global' => 'bookingly_allow_free_appointment'
        ],
        'pre_block_days' => [
            'local' => 'redq_rental_starting_block_dates',
            'global' => 'rnb_staring_block_days'
        ]
    ];

    return $settings_map;
}


function bookingly_get_validation_keys()
{
    return [];
}


function bookingly_get_layout_two_keys()
{
    return [];
}

function bookingly_get_labels_keys()
{
    return [];
}
/**
 * Get the current month and year based on WordPress timezone settings.
 *
 * @return array An associative array containing the current 'month' and 'year'.
 */
function get_current_month_year() {
    // Set the timezone to WordPress settings
    $timezone = get_option('timezone_string');
    if ($timezone) {
        date_default_timezone_set($timezone);
    } else {
        // If timezone_string is not set, use the gmt_offset
        $gmt_offset = get_option('gmt_offset');
        date_default_timezone_set('Etc/GMT' . ($gmt_offset < 0 ? $gmt_offset : '+' . $gmt_offset));
    }

    // Get the current month and year
    $month = date('F'); // Full month name (e.g., January)
    $year = date('Y');  // Full year (e.g., 2025)

    return [
        'month' => $month,
        'year'  => $year,
    ];
}

function bookingly_get_layout_type(){
    global $post;
    if ($post) {
        $is_local = get_post_meta($post->ID, 'rnb_settings_for_conditions', true) === 'local';
        if ($is_local) {
            return get_post_meta($post->ID, '_bookingly_layout_type', true) ?: 'full_calender';
        }
    }
    return get_option('bookingly_layout_type', 'full_calender');
}