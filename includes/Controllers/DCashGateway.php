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

		$total   = WC()->cart->get_cart_contents_total();
		$api_key = $this->get_option( 'api_key' );

		?>
			<div id='dcash-button' />
			<!-- Move this script to own file -->
			<script>
				function show_dcash_button() {
					const paymentID = Math.floor(Math.random() * 10000)
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
}

