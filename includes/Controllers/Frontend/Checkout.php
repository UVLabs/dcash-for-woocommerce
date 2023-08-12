<?php
/**
 * File responsible for defining WC Checkout controller methods.
 *
 * Author:          Uriahs Victor
 * Created on:      12/08/2023 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @since   1.0.0
 * @package Controllers
 */

namespace SoaringLeads\DCashWC\Controllers\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class responsible for defining WC checkout controller methods.
 *
 * @package SoaringLeads\DCashWC\Controllers\Frontend
 * @since 1.0.0
 */
class Checkout {

	/**
	 * Filter our Place order checkout button to add the hidden class to it.
	 *
	 * @param string $btn_html The HTML of the WC place order button.
	 * @return string
	 * @since 1.0.0
	 */
	public function filterPlaceOrderBtn( string $btn_html ): string {

		$class_attribute_position = strpos( $btn_html, 'class=' );
		if ( empty( $class_attribute_position ) ) {
			return $btn_html;
		}

		$btn_html = str_replace( 'class="', 'class="hidden ', $btn_html );
		return $btn_html;
	}

}
