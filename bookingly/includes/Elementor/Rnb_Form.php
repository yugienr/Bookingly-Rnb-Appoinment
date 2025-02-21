<?php
namespace Bookingly\Elementor;

/**
 * Elementor Classes.
 *
 * @package Rnb Form
 */




use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 *
 * Elementor widget for Rnb Builder.
 *
 * @since 1.0.0
 */
class Rnb_Form extends Widget_Base
{
    /**
     * Retrieve the widget name.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'rnb_form';
    }

    /**
     * Retrieve the widget title.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return __('Rnb Form', 'rnb-builder');
    }
    public function get_style_depends()
    {
        return ['bookingly-style', 'flatpickr.min'];
    }
    public function get_script_depends()
    {
        return ['flatpickr.min', 'flatpickr.locale', 'select2.min', 'bookingly-script'];
    }
    /**
     * Retrieve the widget icon.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'eicon-form-horizontal';
    }

    /**
     * Retrieve the list of categories the widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * Note that currently Elementor supports only one category.
     * When multiple categories passed, Elementor uses the first one.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['rnb-builder-widgets'];
    }

    /**
     * Register Copyright controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls()
    {
        $this->register_rnb_form_controls();
    }

    /**
     * Register Copyright General Controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_rnb_form_controls()
    {
        $this->start_controls_section(
            'section_title',
            [
                'label' => __('Pricing Info', 'rnb-builder'),
            ]
        );
        $this->add_control(
            'rnbb_pricing_info_color',
            [
                'label' => esc_html__( 'Primary color', 'rnb-builder' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .rnb-pricing-plan-button .rnb-pricing-plan .rnb-pricing-plan-link' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .price-showing .woocommerce-Price-amount.amount' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .hourly-general' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .price-showing .item-pricing .rnb-pricing-wrap .day-ranges-pricing-plan span' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
            'rnbb_pricing_info_color_secondary',
            [
                'label' => esc_html__( 'Secondary color', 'rnb-builder' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .item-pricing h5' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'rnbb_pricing_info_color_typography',
				'selector' => '{{WRAPPER}} .rnb-content-with-elementor .rnb-pricing-plan-button .rnb-pricing-plan .rnb-pricing-plan-link, {{WRAPPER}} .rnb-pricing-plan-button button',
			]
		);
        $this->end_controls_section();

            $this->start_controls_section(
                'bookingly_style',
                [
                    'label' => __('Bookingly Style', 'rnb-builder'),
                ]
            );
            $this->add_control(
                'rnb_bookingly_border_color',
                [
                    'label' => esc_html__( 'Border color', 'rnb-builder' ),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} form.rnb-cart .bookingly-calendar .flatpickr-calendar .flatpickr-innerContainer .flatpickr-rContainer .flatpickr-days .dayContainer span.flatpickr-day' => 'color: {{VALUE}}',
                        '{{WRAPPER}} form.rnb-cart .bookingly-calendar .flatpickr-calendar .flatpickr-months .flatpickr-prev-month' => 'background-color: {{VALUE}}',
                        '{{WRAPPER}} form.rnb-cart .bookingly-calendar .flatpickr-calendar .flatpickr-months .flatpickr-next-month' => 'background-color: {{VALUE}}',
                        '{{WRAPPER}} form.rnb-cart .bookingly-calendar .flatpickr-calendar .flatpickr-months .flatpickr-month' => 'border-color: {{VALUE}}',
                        '{{WRAPPER}} form.rnb-cart .bookingly-calendar .flatpickr-calendar' => 'border-color: {{VALUE}}',
                        '{{WRAPPER}} form.rnb-cart .bookingly-slots ul li label' => 'border-color: {{VALUE}}',
                    ],
                ]
            );
            $this->add_control(
                'rnb_bookingly_selected_color',
                [
                    'label' => esc_html__( 'Selected color', 'rnb-builder' ),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} form.rnb-cart .bookingly-calendar .flatpickr-calendar .flatpickr-innerContainer .flatpickr-rContainer .flatpickr-days .dayContainer span.selected' => 'background-color: {{VALUE}}',
                    ],
                ]
            );
            $this->add_control(
                'rnb_bookingly_selected_color_border',
                [
                    'label' => esc_html__( 'Selected color border', 'rnb-builder' ),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} form.rnb-cart .bookingly-calendar .flatpickr-calendar .flatpickr-innerContainer .flatpickr-rContainer .flatpickr-days .dayContainer span.selected' => 'border-color: {{VALUE}}',
                        '{{WRAPPER}} form.rnb-cart .bookingly-calendar .flatpickr-calendar .flatpickr-innerContainer .flatpickr-rContainer .flatpickr-days .dayContainer span.today' => 'border-color: {{VALUE}}',
                    ],
                ]
            );
            $this->end_controls_section();
            $this->start_controls_section(
            'rnbb_input_label',
            [
                'label' => __('Input Label', 'rnb-builder'),
            ]
        );
            $this->add_control(
                'rnbb_input_label_color',
                [
                    'label' => esc_html__( 'Text Color', 'rnb-builder' ),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .rnb-content-with-elementor .date-time-picker h5, .date-time-picker h5' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .rnb-content-with-elementor .rnb-component-wrapper h5' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .rnb-content-with-elementor .rnb-component-wrapper > h5 h5' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .rnb-content-with-elementor .rnb-component-wrapper .attributes .custom-block' => 'color: {{VALUE}}',
                    ],
                ]
            );
        $this->end_controls_section();
        $this->start_controls_section(
            'rnbb_input',
            [
                'label' => __('Input', 'rnb-builder'),
            ]
        );
            $this->add_group_control(
                \Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'rnbb_input_border_color',
                    'selector' => '{{WRAPPER}} .rnb-content-with-elementor .date-time-picker input,
                                   {{WRAPPER}} .rnb-content-with-elementor .redq-quantity input,
                                   {{WRAPPER}} .rnb-content-with-elementor .chosen-container.chosen-container-single,
                                   {{WRAPPER}} .rnb-content-with-elementor .additional-person .chosen-container.chosen-container-single,
                                   {{WRAPPER}} .rnb-custom-field-option input',
                ]
            );
            $this->add_control(
                'rnbb_input_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'rnb-builder' ),
                    'type' => \Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'selectors' => [
                        '{{WRAPPER}} .rnb-content-with-elementor .date-time-picker input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .rnb-content-with-elementor .redq-quantity input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ],
                ]
            );    
        $this->end_controls_section();
        $this->start_controls_section(
            'rnbb_submit_button',
            [
                'label' => __('Button', 'rnb-builder'),
            ]
        );
            $this->add_control(
                'rnbb_submit_button_color_normal',
                [
                    'label' => esc_html__( 'Text Color', 'rnb-builder' ),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .rnb-content-with-elementor .single_add_to_cart_button' => 'color: {{VALUE}}',
                    ],
                ]
            );
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'rnbb_submit_typography',
                    'selector' => '{{WRAPPER}} .rnb-content-with-elementor .single_add_to_cart_button',
                ]
            );
            $this->start_controls_tabs(
                'rnbb_add_to_cart_button'
            );
            $this->start_controls_tab(
                'rnbb_add_to_cart_disable',
                [
                    'label' => esc_html__( 'Disable', 'rnb-builder' ),
                ]
            );
            $this->add_group_control(
                \Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'rnb_builder_background_disabled',
                    'types' => [ 'classic', 'gradient'],
                    'selector' => '{{WRAPPER}} .rnb-content-with-elementor button.button.alt:disabled',
                ]
            );    
    		$this->end_controls_tab();
            $this->start_controls_tab(
                'rnbb_add_to_cart_normal',
                [
                    'label' => esc_html__( 'Normal', 'rnb-builder' ),
                ]
            );
            $this->add_group_control(
                \Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'rnb_builder_background_normal',
                    'types' => [ 'classic', 'gradient'],
                    'selector' => '{{WRAPPER}} .rnb-content-with-elementor .single_add_to_cart_button.redq_add_to_cart_button',
                ]
            );    
    		$this->end_controls_tab();
            $this->start_controls_tab(
                'rnbb_add_to_cart_hover',
                [
                    'label' => esc_html__( 'Hover', 'rnb-builder' ),
                ]
            );
            $this->add_group_control(
                \Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'rnb_builde_background_hover',
                    'types' => [ 'classic', 'gradient'],
                    'selector' => '{{WRAPPER}} .rnb-content-with-elementor .single_add_to_cart_button.redq_add_to_cart_button:hover',
                ]
            );    
    		$this->end_controls_tab();
    
        $this->end_controls_section();
    }

    /**
     * Render Copyright output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {
        $settings    = $this->get_settings_for_display();
        global $product;
        $bookingly_product = false;
        if(function_exists('is_bookingly_product')){
            $bookingly_product = is_bookingly_product($product->get_id());
        }
        if(function_exists('is_bookingly_product') && $bookingly_product && \Elementor\Plugin::$instance->editor->is_edit_mode()){
            include __DIR__ . '/layouts/bookingly-editor.php';
        }else{
            include __DIR__ . '/layouts/rnb-form.php';
        }

    }

    /**
     * Render shortcode widget as plain content.
     *
     * Override the default behavior by printing the shortcode instead of rendering it.
     *
     * @since 1.0.0
     * @access public
     */
    public function render_plain_content()
    {
        // In plain mode, render without shortcode.
        echo esc_attr($this->get_settings('shortcode'));
    }

    /**
     * Render shortcode widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 1.3.0
     * @access protected
     */
    protected function content_template()
    {
    }
    protected function editor_is_bookingly_product(){
        global $product;
        $bookingly_product = false;
        if(function_exists('is_bookingly_product')){
            $bookingly_product = is_bookingly_product($product->get_id());
        }
        return $bookingly_product;
    }
}
