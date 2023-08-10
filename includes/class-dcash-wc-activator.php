<?php
/**
 * Fired during plugin activation
 *
 * @link       https://soaringleads.com
 * @since      1.0.0
 *
 * @package    SoaringLeads\DCashWC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    SoaringLeads\DCashWC
 * @author     Uriahs Victor <plugins@soaringleads.com>
 */
class RootActivator {

	/**
	 * Method fired on plugin activation.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::dcash_wc_add_default_settings();
	}

	/**
	 * Add our default settings to the site DB.
	 *
	 * @return void
	 */
	private static function dcash_wc_add_default_settings() {

		$installed_at = get_option( 'dcash_wc_installed_at_version' );
		$install_date = get_option( 'dcash_wc_first_install_date' );

		// Create date timestamp when plugin was first installed.
		if ( empty( $install_date ) ) {
			add_option( 'dcash_wc_first_install_date', time(), '', 'yes' );
		}

		// Create entry for plugin first install version.
		if ( empty( $installed_at ) ) {
			add_option( 'dcash_wc_installed_at_version', DCASH_WC_VERSION, '', false );
		}

	}

}
