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
		$bytes       = random_bytes( 5 );
		$rand_string = bin2hex( $bytes );
		$prefix      = 'SL_' . $rand_string . '_';
		return uniqid( $prefix );
	}
}
