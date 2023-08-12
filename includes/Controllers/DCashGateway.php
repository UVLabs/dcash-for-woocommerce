<?php
/**
 * File responsible for Gateway controller logic.
 *
 * Author:          Uriahs Victor
 * Created on:      10/08/2023 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @since   1.0.0
 * @package Controllers
 */

namespace SoaringLeads\DCashWC\Controllers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setup the DCash gateway configuration fields and settings.
 *
 * @package SoaringLeads\DCashWC\Controllers
 * @since 1.0.0
 */
class DCashGateway extends \WC_Payment_Gateway {

	/**
	 * Construct class.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->id                 = 'sl_dcash_gateway';
		$this->has_fields         = true;
		$this->method_title       = 'DCash';
		$this->method_description = 'Allow customers to pay with their DCash wallet.';
		$this->title              = 'Gateway by SoaringLeads';
		$this->description        = 'test tes tes';
		$this->initFormFields();
		$this->init_settings();

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		// Below URL not being found
		// wp_enqueue_script('sl-dcash-js',  'https://api.easterncaribbean.org/merchant-ecommerce/dcash-ecommerce.js', array('jquery'), '1.0.0', true);
	}

	/**
	 * Setup configuration settings.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function initFormFields(): void {
		$this->form_fields = array(
			'enabled'     => array(
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable', 'woocommerce' ),
				'default' => 'yes',
			),
			'api_key'     => array(
				'title'   => __( 'API Key', 'woocommerce' ),
				'type'    => 'password',
				'default' => '',
			),
			'title'       => array(
				'title'       => __( 'Title', 'woocommerce' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'description' => array(
				'title'   => __( 'Customer Message', 'woocommerce' ),
				'type'    => 'textarea',
				'default' => '',
			),
		);
	}

	/**
	 * Add gateway class to list of gateways so WooCommerce knows about it.
	 *
	 * @param array $methods
	 * @return array
	 * @since 1.0.0
	 */
	public function gatewayClass( array $methods ): array {
		$methods[] = 'SoaringLeads\DCashWC\Controllers\DCashGateway';
		return $methods;
	}

	/**
	 * Payment field that should show on checkout.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function payment_fields() {

		$total   = (float) WC()->cart->get_total( 'raw' );
		$api_key = $this->get_option( 'api_key' );
		esc_html_e( 'Pay with DCash' );
		?>
			<div id='sl-dcash-container'>
			<a id="sl-dcash-btn" style='text-decoration: none; display: none;'><img id="sl-dcash-btn-logo" src="<?php echo DCASH_WC_PLUGIN_ASSETS_PATH_URL . 'public/img/dcash-logo.png'; ?>"><div id="sl-dcash-btn-content">Pay with DCash</div></a>
			<div id='dcash-button' style='display: none'/>
			</div>
			<!-- Move this script to own file -->
			<script>
				function show_dcash_button() {
					const paymentID = 2475
					// const paymentID = Math.floor(Math.random() * 10000)
					console.log(paymentID);
					let paymentParams = {
						merchant_name: 'SoaringLeads',
						callback_url: "https://google.com",
						amount: <?php echo $total; ?>, // Cost of item; Must be FLOAT type, eg. 1.99
						payment_id: paymentID, // Your cart ID or invoice ID; Must be a UNIQUE string
						memo: "This is a test transaction. Cart ID " +  paymentID,
						// API Key received for DCash e-commerce
						api_key: '<?php echo $api_key; ?>',
						// Optional function called when payment is completed
						onPaid: function(details) {
							console.log('User paid:', details);
							jQuery('#place_order').trigger('click');
						},
						// Optional function called when payment window is cancelled
						onCancel: function() {
							console.log('User cancelled');
						},
						// Optional function called when payment window throws an error
						onError: function(err) {
							console.log('User error', err);
						}
					};
					// Send payment parameters and show button
					dcash.ecommerce.button.render(paymentParams, '#dcash-button');
				}
				show_dcash_button(); // To show the DCash button
			</script>
		<?php
	}

	/**
	 * Process the payment.
	 *
	 * @param int $order_id
	 * @return array
	 * @since 1.0.0
	 */
	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		// ray($_POST);

		// This always returns true but we can maybe find a better way to process the payment by using a callback
		// https://woocommerce.com/document/payment-gateway-api/#section-7
		$dcash_success = true;

		if ( $dcash_success ) {
			$order->payment_complete();
		} else {
			wc_add_notice( __( 'Payment error:', 'woothemes' ) . ' Something went wrong', 'error' );
			return;
		}

		// Empty cart
		$woocommerce->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
}

