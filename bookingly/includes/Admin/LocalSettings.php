<?php

namespace Bookingly\Admin;

class LocalSettings
{
    /**
     * Class initialize
     */
    function __construct()
    {
        add_action('rnb_after_local_conditions_settings', [$this, 'local_conditional_settings'], 5, 1);
    }

    public function local_conditional_settings($post_id)
    {
        $free_appointment = bookingly_post_meta($post_id, '_bookingly_allow_free_appointment', 'yes');
        woocommerce_wp_checkbox(
            [
                'id'          => '_bookingly_allow_free_appointment',
                'label'       => esc_html__('Allow Free Appointment', 'redq-rental'),
                'desc_tip'    => true,
                'description' => sprintf(__('Checked: Customer will able to place appointment order without price. UnChecked: Customer will not able to place order wil not able to place order without price', 'redq-rental')),
                'cbvalue'     => 'yes',
                'value'       => esc_attr($free_appointment),
            ]
        );
        $layout_type = bookingly_post_meta($post_id, '_bookingly_layout_type', 'full_calender');
        woocommerce_wp_select([
            'id'          => '_bookingly_layout_type',
            'label'       => esc_html__('Appointment Layout Type', 'redq-rental'),
            'description' => esc_html__('This will be applicable in the time picker field in product page', 'redq-rental'),
            'desc_tip'    => true,
            'options'     => [
                'full_calender'   => esc_html__('Full Calender', 'redq-rental'),
                'weekly_calender' => esc_html__('Weekly Calendar', 'redq-rental'),
            ],
            'value'       => esc_attr($layout_type),
        ]);
    }
}
