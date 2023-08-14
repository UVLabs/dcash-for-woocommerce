<?php
/**
 * File responsible for defining Helper functions.
 *
 * Author:          Uriahs Victor
 * Created on:      12/08/2023 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @since   1.0.0
 * @package Helpers
 */

namespace SoaringLeads\DCashWC\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WC_Order;

/**
 * Class responsible for creating helper functions.
 *
 * @package SoaringLeads\DCashWC\Helpers
 * @since 1.0.0
 */
class Functions {

	/**
	 * Generate a Unique Payment ID to use for the DCash payment request.
	 *
	 * This payment ID is used to differentiate one payment from another.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function generatePaymentID(): string {
		$bytes       = random_bytes( 3 );
		$rand_string = bin2hex( $bytes );
		$prefix      = 'SL_' . $rand_string . '_';
		return uniqid( $prefix );
	}

	/**
	 * Get a DCash order by the DCash Payment ID.
	 *
	 * @return WC_Order
	 * @since 1.0.0
	 */
	public static function getOrderByPaymentID( $payment_id ) {

		$args = array(
			'meta_key'     => 'dcash_payment_id', // Postmeta key field
			'meta_value'   => $payment_id, // Postmeta value field
			'meta_compare' => '=', // Possible values are ‘=’, ‘!=’, ‘>’, ‘>=’, ‘<‘, ‘<=’, ‘LIKE’, ‘NOT LIKE’, ‘IN’, ‘NOT IN’, ‘BETWEEN’, ‘NOT BETWEEN’, ‘EXISTS’ (only in WP >= 3.5), and ‘NOT EXISTS’ (also only in WP >= 3.5). Values ‘REGEXP’, ‘NOT REGEXP’ and ‘RLIKE’ were added in WordPress 3.7. Default value is ‘=’.
		);

		$order = '';

		$order = wc_get_orders( $args );

		if ( is_array( $order ) && ! empty( $order ) ) {
			return $order[0]; // TODO Maybe use array_merge instead to remove top level index and return whole array
		} else {
			// Log
			// Most likely order not yet saved to DB.
		}

		return $order;
	}
}
