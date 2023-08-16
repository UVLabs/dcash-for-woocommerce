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

namespace SoaringLeads\DCashWC\Controllers\DCash;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SoaringLeads\DCashWC\Helpers\Functions;
use SoaringLeads\DCashWC\Helpers\Logger;

/**
 * Setup the DCash gateway configuration fields and settings.
 *
 * @package SoaringLeads\DCashWC\Controllers
 * @since 1.0.0
 */
class Gateway extends \WC_Payment_Gateway {

	/**
	 * Construct class.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->id                 = DCASH_WC_GATEWAY_SETTINGS_KEY;
		$this->has_fields         = true;
		$this->method_title       = 'DCash Payments';
		$this->method_description = 'Allow customers to pay with their DCash wallet.';
		$this->title              = 'DCash';
		$this->initFormFields();
		$this->init_settings();

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
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
			'sandbox'     => array(
				'title'   => __( 'Sandbox', 'dcash-for-woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable sandbox mode for testing payments.', 'woocommerce' ),
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
	 * @param array $methods the current registered payment methods.
	 * @return array
	 * @since 1.0.0
	 */
	public function gatewayClass( array $methods ): array {
		$methods[] = 'SoaringLeads\DCashWC\Controllers\DCash\Gateway';
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

		WC()->session->set( 'dcash_for_wc_payment_ID', false );
		WC()->session->set( 'dcash_for_wc_payment_ID', Functions::generatePaymentID() );
		$payment_id = WC()->session->get( 'dcash_for_wc_payment_ID' );

		if ( ! empty( $payment_id ) ) {
			$description = $this->get_option( 'description' ) ?: __( 'Pay using your DCash wallet.', 'dcash-for-woocommerce' );
			echo "<p style='margin-bottom: 0;'>" . esc_html( $description ) . '</p>';
		} else {
			echo "<br/><p style='font-weight: bold'>" . esc_html__( 'There was an issue setting Session Data. Please refresh the page and try again.', 'dcash-for-woocommerce' ) . '</p>';
			return;
		}

		$dcash_logo_path = DCASH_WC_PLUGIN_ASSETS_PATH_URL . 'public/img/dcash-logo.png';
		$dcash_btn_text  = __( 'Pay with DCash', 'dcash-for-woocommerce' );
		$callback_url    = home_url( '/', 'https' ) . 'wc-api/sl-dcash-callback-handler/';

		if ( DCASH_WC_DEBUG ) {
			$callback_url = 'https://rainbow:hungry@spiffy-book.localsite.io/wc-api/sl-dcash-callback-handler/';
		}

		/**
		 * Keep this In PHP to avoid client-side tampering.
		 * The JS script would be regenerated with the correct data everytime this method is fired.
		 */
		?>
			<input id='dcash-for-woocommerce-payment-id' type='hidden' name='dcash_for_wc_payment_id' value='<?php echo esc_attr( $payment_id ); ?>'/>
			<?php wp_nonce_field( 'dcash_for_woocommerce_payment_id', 'dcash-for-woocommerce-payment-id-nonce' ); ?>
			<div id='sl-dcash-btn-container'>
				<a id="sl-dcash-btn" style='text-decoration: none; display: none;'><img id="sl-dcash-btn-logo" src="<?php echo esc_attr( $dcash_logo_path ); ?>"><div id="sl-dcash-btn-content"><?php echo esc_html( $dcash_btn_text ); ?></div></a>
				<div id='dcash-button' style='display: none'></div>
			</div>

			<script>
				function show_dcash_button() {
					let paymentParams = {
						merchant_name: "<?php echo esc_js( $merchant ); ?>",
						callback_url: "<?php echo esc_js( $callback_url ); ?>",
						amount: <?php echo esc_js( $total ); ?>,
						payment_id: "<?php echo esc_js( $payment_id ); ?>",
						memo: "This is a test transaction. Payment ID: " + "<?php echo esc_js( $payment_id ); ?>",
						api_key: '<?php echo esc_js( $api_key ); ?>',
						onPaid: function(details) {
							console.log('User paid:', details);
							// jQuery('#place_order').trigger('click');
							document.querySelector('#place_order').click();
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
	 * @param int $order_id The order ID.
	 * @return array
	 * @since 1.0.0
	 */
	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new \WC_Order( $order_id );

		$dcash_payment_id       = sanitize_text_field( wp_unslash( ( $_POST['dcash_for_wc_payment_id'] ?? '' ) ) );
		$dcash_payment_id_nonce = sanitize_text_field( wp_unslash( ( $_POST['dcash-for-woocommerce-payment-id-nonce'] ?? '' ) ) );

		if ( empty( wp_verify_nonce( $dcash_payment_id_nonce, 'dcash_for_woocommerce_payment_id' ) ) ) {
			( new Logger() )->logError( 'Issue validating DCash Payment ID: ' . $dcash_payment_id . ', nonce: ' . $dcash_payment_id_nonce );
			return;
		}

		$updated = update_post_meta( $order_id, 'dcash_payment_id', $dcash_payment_id );

		if ( false === $updated ) {
			$text  = __( 'There was an issue completing the order. Please contact us as soon as possible to let us know about this issue. Your Order ID is:', 'dcash-for-woocommerce' ) . ' ' . $order_id . '. ';
			$text .= __( 'Please screenshot this notice and share it with us when reaching out.' );
			$msg   = '<strong>' . $text . '</strong>';
			wc_add_notice( $msg, 'error' );
			( new Logger() )->logWarning( 'An issue occurred while attaching the DCash Payment ID to the order. Payment ID: ' . $dcash_payment_id . ', Order ID: ' . $order_id );
			return;
		}

		// Empty cart.
		$woocommerce->cart->empty_cart();

		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
}

