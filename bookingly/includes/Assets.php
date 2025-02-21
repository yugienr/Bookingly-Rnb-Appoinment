<?php

namespace Bookingly;

use Carbon\Carbon;
use Bookingly\Traits\AppointmentTrait;

/**
 * Assets class handler
 */
class Assets
{
    use AppointmentTrait;
    public $lang = "en";

    public $general = [];

    public $conditions = [];

    /**
     * Initialize assets
     */
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'frontend_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
    }

    /**
     * Bookingly scripts
     *
     * @return array
     */
    public function get_scripts()
    {
        $product_id = get_the_ID();

        $this->general = redq_rental_get_settings($product_id, 'general')['general'];
        $this->conditions = redq_rental_get_settings($product_id, 'conditions')['conditions'];
        $this->lang = !empty($this->general['lang_domain']) ? $this->general['lang_domain'] : 'en';

        return [
            'flatpickr.min' => [
                'src'     => BOOKINGLY_ASSETS . '/js/flatpickr.min.js',
                'version' => filemtime(BOOKINGLY_PATH . '/assets/js/flatpickr.min.js'),
                'type'    => 'frontend',
                'deps'    => ['jquery']
            ],
            'flatpickr.locale' => [
                'src'     => 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/l10n/' . $this->lang . '.js',
                'version' => filemtime(BOOKINGLY_PATH . '/assets/js/flatpickr.min.js'),
                'type'    => 'frontend',
                'deps'    => ['flatpickr.min']
            ],
            'select2.min' => [
                'src'     => BOOKINGLY_ASSETS . '/js/select2.min.js',
                'version' => filemtime(BOOKINGLY_PATH . '/assets/js/select2.min.js'),
                'type'    => 'frontend',
                'deps'    => ['jquery']
            ],
            'slick.min' => [
                'src'     => BOOKINGLY_ASSETS . '/js/slick.min.js',
                'version' => filemtime(BOOKINGLY_PATH . '/assets/js/slick.min.js'),
                'type'    => 'frontend',
                'deps'    => ['jquery']
            ],
            'bookingly-script' => [
                'src'     => BOOKINGLY_ASSETS . '/js/frontend.js',
                'version' => filemtime(BOOKINGLY_PATH . '/assets/js/frontend.js'),
                'type'    => 'frontend',
                'deps'    => ['jquery', 'flatpickr.min', 'select2.min']
            ],
            'bookingly-admin' => [
                'src'     => BOOKINGLY_ASSETS . '/js/admin.js',
                'version' => filemtime(BOOKINGLY_PATH . '/assets/js/admin.js'),
                'type'    => 'admin',
                'deps'    => ['jquery']
            ],
            
        ];
    }

    /**
     * Bookingly styles
     *
     * @return array
     */
    public function get_styles()
    {
        return [
            'flatpickr.min' => [
                'src'     => BOOKINGLY_ASSETS . '/css/flatpickr.min.css',
                'version' => filemtime(BOOKINGLY_PATH . '/assets/css/flatpickr.min.css'),
                'type' => 'frontend',
            ],
            'select2.min' => [
                'src'     => BOOKINGLY_ASSETS . '/css/select2.min.css',
                'version' => filemtime(BOOKINGLY_PATH . '/assets/css/select2.min.css'),
                'type' => 'frontend',
            ],
            'slick' => [
                'src'     => BOOKINGLY_ASSETS . '/css/slick.css',
                'version' => filemtime(BOOKINGLY_PATH . '/assets/css/slick.css'),
                'type' => 'frontend',
            ],
            'bookingly-style' => [
                'src'     => BOOKINGLY_ASSETS . '/css/bookingly.css',
                'version' => filemtime(BOOKINGLY_PATH . '/assets/css/bookingly.css'),
                'type' => 'frontend',
            ]
        ];
    }

    /**
     * Register assets
     */
    public function frontend_scripts()
    {
        $product_id = get_the_ID();

        $bookingly_product = is_bookingly_product($product_id);
        if (empty($bookingly_product) && !isset($_GET['elementor-preview'])) {
            return false;
        }

        $scripts = $this->get_scripts();
        $styles = $this->get_styles();

        foreach ($scripts as $handle => $script) {
            if ($script['type'] !== 'frontend') {
                continue;
            }
            $deps = isset($script['deps']) ? $script['deps'] : false;
            $version = isset($script['version']) ? $script['version'] : BOOKINGLY_VERSION;

            wp_register_script($handle, $script['src'], $deps, $version, true);
        }

        $this->handle_localize_scripts($product_id);

        foreach ($styles as $handle => $style) {
            if ($style['type'] !== 'frontend') {
                continue;
            }
            $deps = isset($style['deps']) ? $style['deps'] : false;
            $version = isset($style['version']) ? $style['version'] : BOOKINGLY_VERSION;

            wp_register_style($handle, $style['src'], $deps, $version);
        }
    }

    public function handle_localize_scripts($product_id)
    {
        $blocked_dates = [];

        $format = $this->conditions['date_format'] ? $this->conditions['date_format'] : 'd/m/Y';

        if (function_exists('redq_rental_handle_holidays')) {
            $holidays = redq_rental_handle_holidays($product_id);

            $starting = redq_rental_staring_block_days($product_id);
            if (count($starting)) {
                $holidays = array_merge($starting, $holidays);
            }

            foreach ($holidays as $date) {
                $date_obj = Carbon::createFromFormat($format, $date);
                $blocked_dates[] = $date_obj->format("Y-m-d");
            }
        }

        $conditions = rnb_get_settings($product_id, 'conditions', ['time_interval']);
        $interval = $conditions['time_interval'];

        $custom_block_dates =  $this->get_custom_block_dates_by_product($product_id, $interval);

        $blocked = array_merge($blocked_dates, $custom_block_dates);
        if (function_exists('rnb_convert_dates_in_common_format')) {
            $blocked = rnb_convert_dates_in_common_format($blocked);
        }

        wp_localize_script("bookingly-script", "bookingly_data", [
            'lang'          => $this->lang ?? 'en',
            'weekends'      => $this->conditions['weekends'],
            'first_day'     => $this->general['day_of_week_start'] ? $this->general['day_of_week_start'] : 1,
            'blocked_dates' => $blocked
        ]);
    }

    public function admin_scripts()
    {
        $scripts = $this->get_scripts();
        $styles = $this->get_styles();

        foreach ($scripts as $handle => $script) {
            if ($script['type'] !== 'admin') {
                continue;
            }
            $deps = isset($script['deps']) ? $script['deps'] : false;
            $version = isset($script['version']) ? $script['version'] : BOOKINGLY_VERSION;

            wp_register_script($handle, $script['src'], $deps, $version, true);
        }

        foreach ($styles as $handle => $style) {
            if ($style['type'] !== 'admin') {
                continue;
            }
            $deps = isset($style['deps']) ? $style['deps'] : false;
            $version = isset($style['version']) ? $style['version'] : BOOKINGLY_VERSION;

            wp_register_style($handle, $style['src'], $deps, $version);
        }
    }
}
