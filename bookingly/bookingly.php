<?php

/**
 * Plugin Name: Bookingly - Appointment Booking For WooCommerce & RnB
 * Plugin URI: https://redq.io
 * Description: Bookingly is a user-friendly WooCommerce appointment plugin crafted as an extension of RnB. This robust extension empowers you to seamlessly integrate any appointment system into your WordPress website.
 * Version: 1.0.8
 * Author: redqteam
 * Author URI: https://redq.io
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bookingly
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';


/**
 * Admin notice to install & activate dependency plugin
 */
$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
$required_plugins = ['woocommerce-rental-and-booking/redq-rental-and-bookings.php'];

if (count(array_intersect($required_plugins, $active_plugins)) !== count($required_plugins)) {
    add_action('admin_notices', 'bookingly_notice');
    function bookingly_notice()
    {
        $woocommerce_link = '<a href="https://codecanyon.net/item/rnb-woocommerce-rental-booking-system/14835145" target="_blank">RnB - WooCommerce Booking & Rental Plugin</a>';
        echo '<div class="error"><p><strong>' . sprintf(esc_html__('"Bookingly" requires "RnB - WooCommerce Booking & Rental" plugin to be installed and active. You can download %s from here.', 'bookingly'), $woocommerce_link) . '</strong></p></div>';
    }
    return;
}


/**
 * Main plugin class
 */
final class Bookingly
{
    /**
     * Plugin version
     * 
     * @var string
     */
    const version = '1.0.6';

    /**
     * contractor
     */
    private function __construct()
    {
        $this->define_constants();

        register_activation_hook(__FILE__, [$this, 'activate']);
        add_action('plugins_loaded', [$this, 'init_plugin'], 0);
        add_action('plugins_loaded', [$this, 'language_domain']);
        add_action('init', [$this, 'init_special_class']);
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'action_links'], 1);
        add_filter('plugin_row_meta',  [$this, 'row_meta'], 10, 2);
    }

    /**
     * Initialize singleton instance
     *
     * @return \Bookingly
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Define constants
     *
     * @return void
     */
    public function define_constants()
    {
        define('BOOKINGLY_VERSION', self::version);
        define('BOOKINGLY_FILE', __FILE__);
        define('BOOKINGLY_PATH', __DIR__);
        define('BOOKINGLY_URL', plugins_url('', BOOKINGLY_FILE));
        define('BOOKINGLY_ASSETS', BOOKINGLY_URL . '/assets');
        define('BOOKINGLY_DIR_PATH', plugin_dir_path(__FILE__));
        define('BOOKINGLY_ELEMENTOR', BOOKINGLY_DIR_PATH . 'includes/Elementor/');
    }

    /**
     * Plugin information
     *
     * @return void
     */
    public function activate()
    {
        $installer = new Bookingly\Installer();
        $installer->run();
    }

    public function init_special_class()
    {
    }

    /**
     * Load plugin files
     *
     * @return void
     */
    public function init_plugin()
    {
        new Bookingly\Assets();
        new Bookingly\Ajax();
        new Bookingly\Generator();
        if(!class_exists('Rnb_Builder') && defined('ELEMENTOR_PRO_VERSION')){
            new Bookingly\Load_Elementor();
        }
        if (is_admin()) {
            new Bookingly\Admin();
        } else {
            new Bookingly\Frontend();
        }
    }

    /**
     * Plugin text-domain
     *
     * @return null
     * @since 1.0.0
     */
    public function language_domain()
    {
        load_plugin_textdomain('bookingly', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * action_links
     *
     * @param array $links
     *
     * @return array
     */
    public function action_links($links)
    {
        $links[] = '<a href="' . admin_url('admin.php?page=wc-settings&tab=rnb_settings') . '" target="_blank">' . __('Settings', 'inspect') . '</a>';
        $links[] = '<a href="https://bookingly-docs.vercel.app/" target="_blank">' . __('Docs', 'redq-rental') . '</a>';
        $links[] = '<a href="https://redqsupport.ticksy.com/" target="_blank">' . __('Support', 'redq-rental') . '</a>';

        return $links;
    }

    /**
     * Row meta
     *
     * @param array $links
     * @param string $file
     * @return array
     */
    public function row_meta($links, $file)
    {
        if ('bookingly/bookingly.php' !== $file) {
            return $links;
        }

        $row_meta[] = '<a href="https://1.envato.market/NKzXeP" target="_blank">' . __('Compatible Theme By RedQ', 'redq-rental') . '</a>';
        $row_meta[] = '<a href="https://youtu.be/IPWetPsOHzk?list=PLUT1MYLrVpA_d_fzbuIDwFANBvo1ckApI" target="_blank">' . __('Tutorials', 'redq-rental') . '</a>';

        return array_merge($links, $row_meta);
    }
}

/**
 * Initialize main plugin
 *
 * @return \Bookingly
 */
function bookingly()
{
    return Bookingly::init();
}

bookingly();
