<?php

namespace Bookingly\Admin;

class GlobalSettings
{
    /**
     * Class initialize
     */
    function __construct()
    {
        add_filter('rnb_global_conditions_settings', [$this, 'global_conditional_settings'], 10, 1);
    }

    public function global_conditional_settings($settings)
    {
        $settings[] = [
            'title' => __('Extra settings for bookingly', 'bookingly'),
            'type'  => 'title',
            'id'    => 'rnb_extra_bookingly_conditionals_options',
        ];

        $settings[] = [
            'title'    => __('Allow Free Appointment', 'bookingly'),
            'desc'     => __('Checked: Customer will able to place appointment order without price. UnChecked: Customer will not able to place order wil not able to place order without price', 'bookingly'),
            'id'       => 'bookingly_allow_free_appointment',
            'desc_tip' => true,
            'default'  => 'yes',
            'type'     => 'checkbox',
        ];
        $settings[] = [
           'title'    => __('Layout Type', 'redq-rental'),
                        'desc'     => __('This will be applicable in the time picker field in product page', 'redq-rental'),
                        'id'       => 'bookingly_layout_type',
                        'type'     => 'select',
                        'class'    => 'wc-enhanced-select',
                        'css'      => 'min-width:150px;',
                        'desc_tip' => true,
                        'options'  => array(
                            'full_calender' => __('Full Calender', 'redq-rental'),
                            'weekly_calender' => __('Weekly Calendar', 'redq-rental'),
                        ),
                        'autoload' => true,
                        'default'  => 'full_calender',
        ];
        $settings[] = [
            'type'  => 'sectionend',
            'id'    => 'rnb_extra_bookingly_conditionals_end',
        ];

        return $settings;
    }
}
