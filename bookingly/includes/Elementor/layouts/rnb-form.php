<?php 
if(!is_product()) {
    return ;
}
global $product;
$product_id = $product->get_ID();

$displays = redq_rental_get_settings($product_id, 'display');
$conditional_data = redq_rental_get_settings($product_id, 'conditions');
$conditional_data = $conditional_data['conditions'];
$displays = $displays['display'];
rnb_template_hooks();

do_action('rnb_before_add_to_cart_form');

if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
 ?>
 <style>
    /* write here for only elementor editor css  */
    .rnb-pricing-plan-button .rnb-pricing-plan .rnb-pricing-plan-link {
        display: flex;
        width: 100%;
        align-items: center;
        justify-content: space-between;
        flex-direction: row-reverse;
        background-color: #f8f7f7;
        color: #777;
        padding: 8px 20px;
        font-size: 16px;
        font-weight: 600;
    }
    .price-showing {
        background-color: #f8f7f7;
        padding: 10px 20px 0 20px;
        margin-bottom: 20px;
    }

    .rnb-cart .date-time-picker input {
        border: 1px solid #777 !important;
    }

    .rnb-cart #quote-content-confirm {
        margin-top: 24px !important;
    }

    .rnb-cart .rnb-popup {
        display: none;
    }
    
 </style>
 
 <?php  }  ?>
<form class="cart rnb-cart rnb-content-with-elementor" method="post" enctype='multipart/form-data' novalidate>
    <?php

    if ($conditional_data['booking_layout'] === 'layout_one') :
        /**
         * rnb_before_add_to_cart_form hook.
         *
         * @hooked rnb_price_flip_box - 10
         * @hooked rnb_pickup_locations - 10
         * @hooked rnb_return_locations - 10
         * @hooked rnb_pickup_datetimes - 10
         * @hooked rnb_payable_resources - 10
         * @hooked rnb_payable_persons - 10
         * @hooked rnb_payable_deposits - 10
         */
        do_action('rnb_main_rental_content');
    endif;

    /**
     * woocommerce_before_add_to_cart_button hook.
     *
     */
    do_action('woocommerce_before_add_to_cart_button');
    ?>

    <input type="hidden" name="currency-symbol" class="currency-symbol" value="<?php echo get_woocommerce_currency_symbol(); ?>">
    <input type="hidden" class="product_id" name="add-to-cart" value="<?php echo esc_attr($product_id); ?>" />
    <input type="hidden" class="quote_price" name="quote_price" value="0" />
<!-- New fields for hotel booking -->
<label for="check-in-date">Check-In Date</label>
<input type="date" name="check_in_date" id="check-in-date">

<label for="check-out-date">Check-Out Date</label>
<input type="date" name="check_out_date" id="check-out-date">

<label for="room-type">Room Type</label>
<select name="room_type" id="room-type">
  <option value="single">Single</option>
  <option value="double">Double</option>
  <option value="suite">Suite</option>
</select>

    <?php
    if ($conditional_data['booking_layout'] === 'layout_one') :
        /**
         * rnb_plain_booking_button hook.
         *
         * @hooked rnb_direct_booking - 10
         * @hooked rnb_request_quote - 20
         */
        do_action('rnb_plain_booking_button');
    else :
        /**
         * rnb_modal_booking hook.
         *
         * @hooked rnb_modal_booking - 10
         */
        do_action('rnb_modal_booking');
    endif;

    /**
     * woocommerce_after_add_to_cart_button hook.
     *
     */
    do_action('woocommerce_after_add_to_cart_button');
    ?>
</form>
<?php 
do_action('woocommerce_after_add_to_cart_form');
