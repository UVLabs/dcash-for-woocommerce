<?php
/**
 * File responsible for defining methods that handle callback from DCash API.
 *
 * Author:          Uriahs Victor
 * Created on:      14/08/2023 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @since   1.0.0
 * @package Controllers
 */

namespace SoaringLeads\DCashWC\Controllers\DCash;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SoaringLeads\DCashWC\Helpers\Logger;
use WC_Action_Queue;

/**
 * DCash callback class.
 *
 * Class responsible for defining methods that handle the callback request sent back by the DCash API.
 *
 * @package SoaringLeads\DCashWC\Controllers\DCash
 * @since 1.0.0
 */
class Callback {

	/**
	 * Schedule an action when request is received from DCash gateway.
	 *
	 * We need this to change the order status to completed when the callback response is received from the DCash API.
	 *
	 * @param array $request_data The response data sent back by the DCash API.
	 * @return void
	 * @since 1.0.0
	 */
	private function createScheduledAction( array $request_data ): void {

		if ( ! class_exists( 'WC_Action_Queue' ) ) {
			require_once WP_PLUGIN_DIR . '/woocommerce/includes/interfaces/class-wc-queue-interface.php';
			require_once WP_PLUGIN_DIR . '/woocommerce/includes/queue/class-wc-action-queue.php';
		}

		try {
			$action_scheduler = new WC_Action_Queue();
		} catch ( \Throwable $th ) {
			( new Logger() )->logCritical( "Could not instantiate WC_Action_Queue. Error: \n\n" . $th->getMessage() );
			return;
		}

		$result = $action_scheduler->schedule_single(
			time() + 60,
			'dcash_for_wc_update_order_status',
			array(
				'request_data' => $request_data,
			),
			'dcash-for-wc'
		);

		if ( empty( $result ) ) {
			( new Logger() )->logWarning( 'Issue creating scheduled event for updating the order status.' );
		}
	}

	/**
	 * Handle the request sent back by the DCash API.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function consumeRequest(): void {

		$response     = file_get_contents( 'php://input' );
		$request_data = json_decode( $response, true );

		if ( ! is_array( $request_data ) ) {
			( new Logger() )->logWarning( "Failed to convert DCash callback request to array. Response: \n\n" . print_r( $response, true ) );
			return;
		}

		$request_data = array_map( 'sanitize_text_field', wp_unslash( $request_data ) );

		$payment_id = $request_data['payment_id'] ?? '';

		if ( empty( $payment_id ) ) {
			( new Logger() )->logWarning( "DCash API callback request did not contain a payment_id array key. Response: \n\n" . print_r( $request_data, true ) );
			return;
		}

		$state = $request_data['state'] ?? '';

		if ( empty( $state ) ) {
			( new Logger() )->logWarning( "DCash API callback request did not contain a state array key. Response:  \n\n" . print_r( $request_data, true ) );
			return;
		}

		$this->createScheduledAction( $request_data );
	}

}
