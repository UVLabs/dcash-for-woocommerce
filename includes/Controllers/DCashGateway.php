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

use SoaringLeads\DCashWC\Helpers\Functions;

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
		$this->method_title       = 'DCash Payments';
		$this->method_description = 'Allow customers to pay with their DCash wallet.';
		$this->title              = 'DCash';
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
				'title'   => __( 'Enable/Disable', 'dcash-for-woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable', 'woocommerce' ),
				'default' => 'no',
			),
			'api_key'     => array(
				'title'   => __( 'API Key', 'dcash-for-woocommerce' ),
				'type'    => 'password',
				'default' => '',
			),
			'merchant'    => array(
				'title'       => __( 'Merchant (Store name)', 'dcash-for-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'The merchant name to show above the DCash QR Code.', 'dcash-for-woocommerce' ),
				'default'     => 'Merchant',
				// 'desc_tip'    => true,
			),
			'description' => array(
				'title'       => __( 'Description', 'dcash-for-woocommerce' ),
				'type'        => 'textarea',
				'description' => __( 'The text to show when the customer selects the DCash payment method.', 'dcash-for-woocommerce' ),
				'default'     => 'Pay using your DCash wallet.',
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

		if ( empty( $api_key ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				echo "<p style='margin-bottom: 0;'>" . esc_html__( 'Please enter your DCash Merchant API Key to accept DCash payments.', 'dcash-for-woocommerce' ) . '</p>';
				return;
			} else {
				echo "<p style='margin-bottom: 0;'>" . esc_html__( 'Payment method not available yet, please check back later.', 'dcash-for-woocommerce' ) . '</p>';
				return;
			}
		}
		$merchant = $this->get_option( 'merchant' );

		WC()->session->set( 'sl_dcash_payment_ID', false );
		WC()->session->set( 'sl_dcash_payment_ID', Functions::generatePaymentID() );
		$payment_id = WC()->session->get( 'sl_dcash_payment_ID' );

		if ( ! empty( $payment_id ) ) {
			$description = $this->get_option( 'description' ) ?: __( 'Pay using your DCash wallet.', 'dcash-for-woocommerce' );
			echo "<p style='margin-bottom: 0;'>" . esc_html( $description ) . '</p>';
		} else {
			echo "<br/><p style='font-weight: bold'>" . esc_html__( 'There was an issue setting Session Data. Please refresh the page and try again.', 'dcash-for-woocommerce' ) . '</p>';
			return;
		}

		$dcash_logo_path = DCASH_WC_PLUGIN_ASSETS_PATH_URL . 'public/img/dcash-logo.png';
		$dcash_btn_text  = __( 'Pay with DCash', 'dcash-for-woocommerce' );

		/**
		 * Keep this In PHP to avoid client-side tampering.
		 * The JS script would be regenerated with the correct data everytime this method is fired.
		 */
		?>
			<div id='sl-dcash-container'>
			<a id="sl-dcash-btn" style='text-decoration: none; display: none;'><img id="sl-dcash-btn-logo" src="<?php echo esc_attr( $dcash_logo_path ); ?>"><div id="sl-dcash-btn-content"><?php echo esc_html( $dcash_btn_text ); ?></div></a>
			<div id='dcash-button' style='display: none'/>
			</div>

			<script>
				function show_dcash_button() {
					const paymentID = "<?php echo esc_js( $payment_id ); ?>";
					console.log(paymentID);
					let paymentParams = {
						merchant_name: "<?php echo esc_js( $merchant ); ?>",
						callback_url: "https://spiffy-book.localsite.io/",
						amount: <?php echo esc_js( $total ); ?>,
						payment_id: paymentID,
						memo: "This is a test transaction. Cart ID " +  paymentID,
						api_key: '<?php echo esc_js( $api_key ); ?>',
						onPaid: function(details) {
							console.log('User paid:', details);
							jQuery('#place_order').trigger('click');
						},
						onCancel: function() {
							console.log('User cancelled');
						},
						onError: function(err) {
							console.log('User error', err);
						}
					};
					// Send payment parameters and show button.
					dcash.ecommerce.button.render(paymentParams, '#dcash-button');
				}
				show_dcash_button(); // To show the DCash button.
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

		// This always returns true but we can maybe find a better way to process the payment by using a callback
		// https://woocommerce.com/document/payment-gateway-api/#section-7
		$dcash_success = true;

		// The fact that this is always true leaves it open to fake orders.
		// We should ideally set the payment as pending and use the callback from DCash to update to payment complete.
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

