<?php

namespace Bookingly\Admin;

/**
 * Admin menu class
 */
class Menu
{
    /**
     * Initialize menu
     */
    function __construct()
    {
        add_filter('rnb_admin_submenu',  [$this, 'admin_submenu'], 10, 2);
        add_filter('rnb_doc_details',  [$this, 'doc_details'], 10, 1);
    }

    public function admin_submenu($submenu, $parent_slug)
    {
        $submenu['slot'] =  [
            'page_title' => __('Appointment Slots', 'bookingly'),
            'menu_title' => __('Appointment Slots', 'bookingly'),
            'capability' => 'manage_options',
            'menu_slug'  => 'edit-tags.php?taxonomy=booking_slot&post_type=inventory',
            'callback'   => '',
            'position'   => null,
        ];

        return $submenu;
    }

    public function doc_details($docs)
    {
        $docs[] = [
            'name' =>  'Booking Slots',
            'link' => 'https://bookingly-docs.vercel.app/',
        ];

        return $docs;
    }
}
