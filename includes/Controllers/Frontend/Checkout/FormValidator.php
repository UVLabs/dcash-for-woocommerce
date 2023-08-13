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
		$errors = new WP_Error();
		$this->validate_checkout( $fields, $errors );
		return $errors; // Updated by reference.
	}

}
