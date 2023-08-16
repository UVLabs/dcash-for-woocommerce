<?php
/**
 * File responsible for defining model methods for Callback events.
 *
 * Author:          Uriahs Victor
 * Created on:      14/08/2023 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @since   1.0.0
 * @package Models
 */

namespace SoaringLeads\DCashWC\Models\DCash;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SoaringLeads\DCashWC\Helpers\Logger;
use SoaringLeads\DCashWC\Helpers\Utilities;
use SoaringLeads\DCashWC\Models\BaseModel;
use WC_Order;

/**
 * Callback Model Class.
 *
 * Class responsible for defining model methods that act on callback events sent back from the DCash API.
 *
 * @package SoaringLeads\DCashWC\Models\DCash
 * @since 1.0.0
 */
class Callback extends BaseModel {

	/**
	 * The WooCommerce order object.
	 *
	 * @var WC_Order
	 * @since 1.0.0
	 */
	protected $order;

	/**
	 * The order payment id.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected string $payment_id;

	/**
	 * Add settling transaction order note.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	private function addSettlingTransactionNote() {
		$this->order->add_order_note( __( 'Settling Transaction for Payment ID:', '' ) . ' ' . $this->payment_id );
	}

	/**
	 * Set an order as complete and ready to process.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	private function setPaymentComplete() {
		$this->order->add_order_note( __( 'Payment successful, ID:', '' ) . ' ' . $this->payment_id );
		$this->order->payment_complete( $this->payment_id );
	}

	/**
	 * Update an order status.
	 *
	 * Based on the state received from the DCash API.
	 *
	 * @param array $request_data The data from the DCash API.
	 * @return void
	 * @since 1.0.0
	 */
	public function updateOrderStatus( $request_data ) {

		$state      = $request_data['state'];
		$payment_id = $request_data['payment_id'];

		$this->payment_id = $payment_id;

		try {
			$order = Utilities::getOrderByPaymentID( $payment_id );
			if ( empty( $order ) ) {
				( new Logger() )->logError( 'Order object is empty. Unable to update order status for order with Payment ID: ' . $payment_id );
				return;
			}
			$this->order = $order;
		} catch ( \Throwable $th ) {
			( new Logger() )->logCritical( "There was a critical issue updating the order status. Error: \n\n" . $th->getMessage() );
			return;
		}

		switch ( $state ) {
			case 'settling_commerce_transaction':
				$this->addSettlingTransactionNote();
				break;

			case 'complete':
				$this->setPaymentComplete();
				break;

			default:
				( new Logger() )->logInfo( 'The state received by the DCash API is not yet accounted for. State: ' . $state );
				break;
		}

	}

}
