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
	 * Check if sandbox mode is enabled for DCash payments.
	 *
	 * This is necessary for testing DCash beta payments.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function sandboxModeEnabled(): bool {

		$settings_key = DCASH_WC_GATEWAY_SETTINGS_KEY;
		$option_name  = 'woocommerce_' . $settings_key . '_settings';
		$settings     = get_option( $option_name );
		$sandbox      = $settings['sandbox'] ?? '';

		return filter_var( $sandbox, FILTER_VALIDATE_BOOLEAN );
	}
}
