<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://soaringleads.com
 * @since      1.0.0
 *
 * @package    SoaringLeads\DCashWC
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    SoaringLeads\DCashWC
 * @author_name     Uriahs Victor <plugins@soaringleads.com>
 */
namespace SoaringLeads\DCashWC\Bootstrap;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class responsible for setting up text domain.
 *
 * @package SoaringLeads\DCashWC\Bootstrap
 */
class I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function loadPluginTextdomain() {

		load_plugin_textdomain(
			'integrate-dcash-with-woocommerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

}
