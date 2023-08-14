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
	 * Schedule an action to when request is received from DCash gateway.
	 *
	 * @param array $request_data
	 * @return void
	 */
	private function createScheduledAction( array $request_data ): void {

		if ( ! class_exists( 'WC_Action_Queue' ) ) {
			require_once WP_PLUGIN_DIR . '/woocommerce/includes/interfaces/class-wc-queue-interface.php';
			require_once WP_PLUGIN_DIR . '/woocommerce/includes/queue/class-wc-action-queue.php';
		}

		try {
			// code...
			$action_scheduler = new WC_Action_Queue();
		} catch ( \Throwable $th ) {
			// throw $th;
			// TODO Log
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
			// TODO Log this means setting the event failed.
		}
	}

	/**
	 * Handle the request sent back by the DCash API.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function consumeRequest(): void {
		$request_data = json_decode( file_get_contents( 'php://input' ), true );

		if ( ! is_array( $request_data ) ) {
			// TODO Handle this. Maybe add a WC error log.
			return;
		}

		$request_data = array_map( 'sanitize_text_field', wp_unslash( $request_data ) );

		$payment_id = $request_data['payment_id'] ?? '';

		if ( empty( $payment_id ) ) {
			// Log
			return;
		}

		$state = $request_data['state'] ?? '';

		if ( empty( $state ) ) {
			// Log
			return;
		}

		$this->createScheduledAction( $request_data );
	}

}
