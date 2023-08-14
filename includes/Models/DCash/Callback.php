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

use SoaringLeads\DCashWC\Helpers\Functions;
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
	 *
	 * @var WC_Order
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
	 * Save a request sent back from the DCash API to the DB.
	 *
	 * @param mixed $event_data
	 * @return void
	 * @since 1.0.0
	 */
	public function saveEvent( $event_data ): void {
		$table = $this->wpdb->prefix . 'dcash_callback_events';

		$data = array(
			'payment_id' => $event_data['payment_id'] ?? '',
			'state'      => $event_data['state'] ?? '',
			'data'       => serialize( $event_data ),
		);

		$this->wpdb->insert( $table, $data );
	}

	/**
	 * Add settling transaction order note.
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
		$this->order->payment_complete( $this->payment_id );
	}

	/**
	 * Update an order status.
	 *
	 * Based on the state received from the DCash API.
	 *
	 * @param array $request_data
	 * @return void
	 * @since 1.0.0
	 */
	public function updateOrderStatus( array $request_data ) {

		$state      = $request_data['state'];
		$payment_id = $request_data['payment_id'];

		$this->saveEvent( $request_data );

		$this->payment_id = $payment_id;

		try {
			$this->order = Functions::getOrderByPaymentID( $payment_id );
		} catch ( \Throwable $th ) {
			// Log
			return;
		}

		switch ( $state ) {
			case 'settling_commerce_transaction':
				// TODO This runs too early and at this point the order might not be in the DB yet.
				// Move to storing events and then later processing them.
				// $this->addSettlingTransactionNote();
				break;
			case 'complete':
				$this->setPaymentComplete();
				break;

			default:
				// code...
				break;
		}

	}

}
