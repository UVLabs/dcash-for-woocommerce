<?php
/**
 * File responsible for defining base model methods.
 *
 * Author:          Uriahs Victor
 * Created on:      14/08/2023 (d/m/y)
 *
 * @link    https://uriahsvictor.com
 * @since   1.0.0
 * @package Models
 */

namespace SoaringLeads\DCashWC\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use wpdb;

/**
 * Base Model Class.
 *
 * Defines base model methods that should be inherited by all model classes.
 *
 * @package SoaringLeads\DCashWC\Models
 * @since 1.0.0
 */
class BaseModel {

	/**
	 * WP DB instance.
	 *
	 * @var wpdb
	 * @since 1.0.0
	 */
	protected \wpdb $wpdb;

	/**
	 * Class constructor.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}

}
