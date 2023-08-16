<?php
/**
 * File responsible for defining Utility methods.
 *
 * Author:          Uriahs Victor
 * Created on:      16/08/2023 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @since   1.0.0
 * @package Helpers
 */

namespace SoaringLeads\DCashWC\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Utilities class responsible for creating Helper Utility methods.
 *
 * @package SoaringLeads\DCashWC\Helpers
 * @since 1.0.0
 */
class Utilities {

	/**
	 * Get a DCash order by the DCash Payment ID.
	 *
	 * @param string $payment_id The DCash Payment ID to search for.
	 * @return WC_Order
	 * @since 1.0.0
	 */
	public static function getOrderByPaymentID( string $payment_id ) {

		$args = array(
			'meta_key'     => 'dcash_payment_id',
			'meta_value'   => $payment_id,
			'meta_compare' => '=', // Possible values are ‘=’, ‘!=’, ‘>’, ‘>=’, ‘<‘, ‘<=’, ‘LIKE’, ‘NOT LIKE’, ‘IN’, ‘NOT IN’, ‘BETWEEN’, ‘NOT BETWEEN’, ‘EXISTS’ (only in WP >= 3.5), and ‘NOT EXISTS’ (also only in WP >= 3.5). Values ‘REGEXP’, ‘NOT REGEXP’ and ‘RLIKE’ were added in WordPress 3.7. Default value is ‘=’.
		);

		$order = \wc_get_orders( $args ); // TODO write function to replace this when not available.

		if ( is_array( $order ) && ! empty( $order ) ) {
			return $order[0] ?? '';
		}

		return $order;
	}

}
