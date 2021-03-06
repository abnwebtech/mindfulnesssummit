<?php
	/*	
	*	Goodlayers Function File
	*	---------------------------------------------------------------------
	*	This file include all of important function and features of the theme
	*	---------------------------------------------------------------------
	*/	
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

/**
 * This filter will bypass the Cart page when a product is added to cart and
 * send the customer directly to checkout.
 */
add_filter ('woocommerce_add_to_cart_redirect', 'woo_redirect_to_checkout');
function woo_redirect_to_checkout() {
	$checkout_url = WC()->cart->get_checkout_url();

	return $checkout_url;
}

/**
 * Remove the Coupon form from the Checkout page
 */
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

/**
 * Remove fields from the checkout page
 */
add_filter( 'woocommerce_checkout_fields' , 'mindsummit_custom_override_checkout_fields' );
function mindsummit_custom_override_checkout_fields( $fields ) {


	unset($fields['billing']['billing_company']);
	unset($fields['billing']['billing_address_1']);
	unset($fields['billing']['billing_address_2']);
	unset($fields['billing']['billing_city']);
	unset($fields['billing']['billing_postcode']);
	unset($fields['billing']['billing_country']);
	unset($fields['billing']['billing_state']);
	unset($fields['billing']['billing_phone']);
	unset($fields['order']['order_comments']);

	return $fields;
}


/**
 * Auto Complete all WooCommerce orders.
 */
add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_order' );
function custom_woocommerce_auto_complete_order( $order_id ) {
	if ( ! $order_id ) {
		return;
	}

	$order = wc_get_order( $order_id );
	$order->update_status( 'completed' );
}

/**
 * Remove the address from the Complete Order email
 */
remove_action( 'woocommerce_email_customer_details', array( 'WC_Emails', 'email_addresses' ), 20, 3 );


/**
 * Add Facebook tracking pixel to Thank You Page
 *
 * Actual adding is done in /themes/mindfulnesssummit/woocommerce/checkout/thankyou.php
 */
function add_facebook_tracking_pixel() {

	?>
	<!-- Facebook Pixel Code -->
	<script>
		!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
			n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
			n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
			t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
			document,'script','//connect.facebook.net/en_US/fbevents.js');

		fbq('init', '879433708806402');
		fbq('track', 'Purchase', {value: '149', currency: 'USD'});
		fbq('track', 'PageView');
	</script>
	<noscript><img height="1" width="1" style="display:none"
	               src="https://www.facebook.com/tr?id=879433708806402&ev=PageView&noscript=1"
			/></noscript>
	<!-- End Facebook Pixel Code -->
<?php

}

/**
 * When customer makes a purchase, remove the Free Plan if user has that membership
 */
add_action( 'wc_memberships_grant_membership_access_from_purchase', 'remove_free_membership_after_purchase', 20, 2 );
function remove_free_membership_after_purchase( $new_membership_plan, $args ) {

	$memberships = wc_memberships_get_user_memberships( $args['user_id'] );
	foreach ( $memberships as $membership ) {

		if ( $membership->plan_id == '3734' ){
			$_membership = wc_memberships_get_user_membership( $membership->id );
			$_membership->cancel_membership( 'User upgraded to Paid access. Order ID #' . $args['order_id'] );
		}
	}

}