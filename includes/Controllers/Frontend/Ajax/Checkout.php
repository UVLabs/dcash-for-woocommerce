<?php
/**
 * File responsible for methods that handle Ajax requests from frontend.
 *
 * Author:          Uriahs Victor
 * Created on:      13/08/2023 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @since   1.0.0
 * @package Controllers
 */

namespace SoaringLeads\DCashWC\Controllers\Frontend\Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SoaringLeads\DCashWC\Controllers\Frontend\Checkout\FormValidator;

/**
 * Class responsible for creating methods that handle Ajax requests from the checkout page.
 *
 * @package SoaringLeads\DCashWC\Controllers\Frontend\Ajax
 * @since 1.0.0
 */
class Checkout {

	/**
	 * Handler for validating the Checkout page form.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function validateForm(): void {

		$fields = wp_unslash( ( $_REQUEST['checkoutFormFields'] ?? array() ) );
		$fields = array_map( 'sanitize_text_field', $fields );

		$errors = ( new FormValidator() )->validate( $fields );

		if ( false === $errors->has_errors() ) {
			wp_send_json_success();
		} else {
			wp_send_json_error( $errors->get_error_messages(), 200 );
		}
	}
}
