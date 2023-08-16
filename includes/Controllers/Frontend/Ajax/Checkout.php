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
use WP_Error;

/**
 * Class responsible for creating methods that handle Ajax requests from the checkout page.
 *
 * @package SoaringLeads\DCashWC\Controllers\Frontend\Ajax
 * @since 1.0.0
 */
class Checkout {

	/**
	 * Prepare and format any errors that should be shown on the frontend.
	 *
	 * @param WP_Error $errors WooCommerce checkout errors.
	 * @return string
	 * @since 1.0.0
	 */
	private function prepareCheckoutErrors( WP_Error $errors ): string {

		$errors_blob = '';
		foreach ( $errors->errors as $error_id => $error_array ) {
			$errors_list = '';
			$attributes  = '';
			foreach ( $errors->error_data as $error_data_id => $error_data_array ) {
				if ( $error_data_id === $error_id ) {
					foreach ( $error_data_array as $html_attribute => $value ) {
						$attributes .= 'data-' . $html_attribute . '=' . "'$value'";
					}
				}
			}
			$errors_list  = implode( $error_array );
			$errors_blob .= "<li $attributes>$errors_list</li>";
		}

		return $errors_blob;
	}

	/**
	 * Handler for validating the Checkout page form.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function validateForm(): void {

		$nonce = sanitize_text_field( wp_unslash( ( $_REQUEST['dCashWCNonce'] ?? '' ) ) );

		if ( empty( wp_verify_nonce( $nonce, 'woocommerce-process_checkout' ) ) ) {
			wp_send_json_error( '<li>' . __( 'Nonce verification failed. Please try refreshing the page and placing the order again. Contact us if this issue persists.', '' ) . '</li>' );
		}

		$fields = wp_unslash( ( $_REQUEST['checkoutFormFields'] ?? array() ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- This is a multidimensional array. We're sanitizing all values in array_walk_recursive below.

		array_walk_recursive(
			$fields,
			function( &$value, $key ) {
				$value = sanitize_text_field( $value );
			}
		);

		$errors     = ( new FormValidator() )->validate( $fields );
		$errors_str = $this->prepareCheckoutErrors( $errors );

		if ( false === $errors->has_errors() ) {
			wp_send_json_success();
		} else {
			wp_send_json_error( $errors_str );
		}
	}
}
