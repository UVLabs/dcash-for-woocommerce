<?php
/**
 * File responsible for creating methods that validate the checkout form.
 *
 * Author:          Uriahs Victor
 * Created on:      13/08/2023 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @since   1.0.0
 * @package Controllers
 */

namespace SoaringLeads\DCashWC\Controllers\Frontend\Checkout;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_Error;

/**
 * Form Validator class.
 *
 * Methods for validating the checkout form
 *
 * @package SoaringLeads\DCashWC\Controllers\Frontend\Checkout
 */
class FormValidator extends \WC_Checkout {

	/**
	 * Validate the Checkout page form.
	 *
	 * @param mixed $fields
	 * @return WP_Error
	 * @since 1.0.0
	 */
	public function validate( $fields ): WP_Error {
		// See WC_Checkout::get_posted_data().
		$fields['ship_to_different_address']          = filter_var( ( $fields['ship_to_different_address'] ), FILTER_VALIDATE_BOOLEAN );
		$fields['woocommerce_checkout_update_totals'] = filter_var( ( $fields['woocommerce_checkout_update_totals'] ), FILTER_VALIDATE_BOOLEAN );
		$fields['terms']                              = (int) isset( $fields['terms'] );
		$fields['terms-field']                        = (int) isset( $fields['terms-field'] );
		$fields['createaccount']                      = (int) ( $this->is_registration_enabled() ? ! empty( $fields['createaccount'] ) : false );

		$errors = new WP_Error();

		do_action( 'dcash_wc_before_checkout_validate', $fields, $errors );

		$this->validate_checkout( $fields, $errors );

		do_action( 'dcash_wc_after_checkout_validate', $fields, $errors );

		// Allow filtering.
		$errors = apply_filters( 'dcash_wc_checkout_errors', $errors, $fields );

		return $errors; // Updated by reference.
	}

}
