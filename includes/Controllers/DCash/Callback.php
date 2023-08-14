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

use SoaringLeads\DCashWC\Models\DCash\Callback as CallbackModel;

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

		( new CallbackModel() )->updateOrderStatus( $request_data );

	}

}
