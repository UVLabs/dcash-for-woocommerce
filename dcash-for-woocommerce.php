<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://soaringleads.com
 * @since             1.0.0
 * @package           SoaringLeads\DCashWC
 *
 * @wordpress-plugin
 * Plugin Name:       DCash for WooCommerce
 * Plugin URI:        https://soaringleads.com/
 * Description:       Accept DCash payments on your WooCommerce store.
 * Version:           1.0.0
 * Author:            Uriahs Victor
 * Author URI:        https://soaringleads.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires PHP: 7.4
 * Text Domain:       dcash-for-woocommerce
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'DCASH_WC_VERSION' ) ) {
	define( 'DCASH_WC_VERSION', '1.0.0' );
}


/**
 * Check PHP version
 */
if ( function_exists( 'phpversion' ) ) {

	if ( version_compare( phpversion(), '7.4', '<' ) ) {
		add_action(
			'admin_notices',
			function() {
				echo "<div class='notice notice-error is-dismissible'>";
				/* translators: 1: Opening <p> HTML element 2: Opening <strong> HTML element 3: Closing <strong> HTML element 4: Closing <p> HTML element  */
				echo sprintf( esc_html__( '%1$s%2$s DCash for WooCommerce NOTICE:%3$s PHP version too low to use this plugin. Please change to at least PHP 7.4. You can contact your web host for assistance in updating your PHP version.%4$s', 'dcash-for-woocommerce' ), '<p>', '<strong>', '</strong>', '</p>' );
				echo '</div>';
			}
		);
		return;
	}
}

/**
 * Check PHP versions
 */
if ( defined( 'PHP_VERSION' ) ) {
	if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
		add_action(
			'admin_notices',
			function() {
				echo "<div class='notice notice-error is-dismissible'>";
				/* translators: 1: Opening <p> HTML element 2: Opening <strong> HTML element 3: Closing <strong> HTML element 4: Closing <p> HTML element  */
				echo sprintf( esc_html__( '%1$s%2$s DCash for WooCommerce NOTICE:%3$s PHP version too low to use this plugin. Please change to at least PHP 7.4. You can contact your web host for assistance in updating your PHP version.%4$s', 'dcash-for-woocommerce' ), '<p>', '<strong>', '</strong>', '</p>' );
				echo '</div>';
			}
		);
		return;
	}
}

// Composer autoload.
require dirname( __FILE__ ) . '/vendor/autoload.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-dcash-wc-activator.php
 */
if ( ! function_exists( 'activate_prefix' ) ) {
	/**
	 * Code to run when plugin is activated.
	 *
	 * @return void
	 */
	function activate_prefix() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-dcash-wc-activator.php';
		RootActivator::activate();
	}
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-dcash-wc-deactivator.php
 */
if ( ! function_exists( 'deactivate_prefix' ) ) {
	/**
	 * Code to run when plugin is deactivated.
	 *
	 * @return void
	 */
	function deactivate_prefix() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-dcash-wc-deactivator.php';
		RootDeactivator::deactivate();
	}
}

register_activation_hook( __FILE__, 'activate_prefix' );
register_deactivation_hook( __FILE__, 'deactivate_prefix' );

define( 'DCASH_WC_BASE_FILE', basename( plugin_dir_path( __FILE__ ) ) );
define( 'DCASH_WC_PLUGIN_NAME', 'dcash-wc' );
define( 'DCASH_WC_PLUGIN_DIR', __DIR__ . '/' );
define( 'DCASH_WC_PLUGIN_ASSETS_DIR', __DIR__ . '/assets/' );
define( 'DCASH_WC_PLUGIN_ASSETS_PATH_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
define( 'DCASH_WC_PLUGIN_PATH_URL', plugin_dir_url( __FILE__ ) );

$debug = false;

// Add SL_DEV_DEBUGGING to your wp-config.php file on your test environment. Feel free to rename constant.
if ( defined( 'SL_DEV_DEBUGGING' ) ) {
	$debug = true;
}

define( 'DCASH_WC_DEBUG', $debug );

$plugin_instance = \SoaringLeads\DCashWC\Bootstrap\Main::getInstance();
$plugin_instance->run();
