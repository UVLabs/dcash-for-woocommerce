<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://soaringleads.com
 * @since      1.0.0
 *
 * @package    SoaringLeads\DCashWC
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    SoaringLeads\DCashWC
 * @author_name     Uriahs Victor <plugins@soaringleads.com>
 */

namespace SoaringLeads\DCashWC\Bootstrap;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SoaringLeads\DCashWC\Bootstrap\Loader;
use SoaringLeads\DCashWC\Bootstrap\I18n;
use SoaringLeads\DCashWC\Bootstrap\AdminEnqueues;
use SoaringLeads\DCashWC\Bootstrap\FrontendEnqueues;
use SoaringLeads\DCashWC\Bootstrap\SetupCron;
use SoaringLeads\DCashWC\Controllers\DCash\Callback as CallbackHandler;
use SoaringLeads\DCashWC\Controllers\Frontend\Ajax\Checkout as CheckoutAjaxHandler;
use SoaringLeads\DCashWC\Controllers\DCash\Gateway as DCashGateway;
use SoaringLeads\DCashWC\Controllers\Frontend\Checkout\FilterHooks as CheckoutFilterHooks;
use SoaringLeads\DCashWC\Models\DCash\Callback as CallbackModel;

/*
use SoaringLeads\DCashWC\Notices\Loader as NoticesLoader;
use SoaringLeads\DCashWC\Notices\Notice;
*/

/**
 * Class Main.
 *
 * Class responsible for firing public and admin hooks.
 */
class Main {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Plugin instance
	 *
	 * @var mixed
	 */
	private static $instance;

	/**
	 * Gets an instance of our plugin.
	 *
	 * @return Main()
	 */
	public static function getInstance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	private function __construct() {
		$this->version = DCASH_WC_VERSION;

		$this->plugin_name = DCASH_WC_PLUGIN_NAME;

		$this->loadDependencies();
		$this->setLocale();
		$this->defineAdminHooks();
		$this->definePublicHooks();
		$this->setupGateway();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function loadDependencies() {
		$this->loader = new Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function setLocale() {
		$plugin_i18n = new I18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'loadPluginTextdomain' );
	}

	/**
	 * Setup the gateway settings and output.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	private function setupGateway() {
		// Setup Gateway settings.
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			require_once WP_PLUGIN_DIR . '/woocommerce/includes/abstracts/abstract-wc-settings-api.php';
			require_once WP_PLUGIN_DIR . '/woocommerce/includes/abstracts/abstract-wc-payment-gateway.php';
		}
		$this->loader->add_action( 'plugins_loaded', new DCashGateway(), '__construct', PHP_INT_MAX );
		$this->loader->add_filter( 'woocommerce_payment_gateways', new DCashGateway(), 'gatewayClass', PHP_INT_MAX );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function defineAdminHooks() {

		if ( ! is_admin() ) {
			return;
		}

		$plugin_admin         = new AdminEnqueues();
		$bootstrap_cron_setup = new SetupCron();

		/*
		// (uncomment if making use of notice class).
		$notice               = new Notice();
		$notices_loader       = new NoticesLoader();
		*/
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueueStyles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueueScripts' );

		$this->loader->add_filter( 'plugin_action_links', $this, 'addPluginActionLinks', PHP_INT_MAX, 2 );

		// Cron tasks.
		$this->loader->add_action( 'admin_init', $bootstrap_cron_setup, 'setCronTasks' );

		/*
		// Notices Loader (uncomment if making use of notice class).
		$this->loader->add_action( 'admin_notices', $notices_loader, 'loadNotices' );

		// Notices Ajax dismiss method (uncomment if making use of notice class).
		$this->loader->add_action( 'wp_ajax_dcash_wc_dismissNotice', $notice, 'dismissNotice' );
		*/
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function definePublicHooks() {

		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}

		$plugin_public = new FrontendEnqueues();
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueueStyles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueueScripts' );

		$checkout_page_controller = new CheckoutFilterHooks();
		$this->loader->add_filter( 'woocommerce_order_button_html', $checkout_page_controller, 'filterPlaceOrderBtn' );

		$checkout_ajax_controller = new CheckoutAjaxHandler();
		$this->loader->add_action( 'wp_ajax_nopriv_dCashValidateCheckout', $checkout_ajax_controller, 'validateForm' );
		$this->loader->add_action( 'wp_ajax_dCashValidateCheckout', $checkout_ajax_controller, 'validateForm' );

		$callback_controller = new CallbackHandler();
		$this->loader->add_action( 'woocommerce_api_sl-dcash-callback-handler', $callback_controller, 'consumeRequest' );

		$callback_model = new CallbackModel();
		$this->loader->add_action( 'dcash_for_wc_update_order_status', $callback_model, 'updateOrderStatus' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function getPluginName() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Loader    Orchestrates the hooks of the plugin.
	 */
	public function getLoader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * Add action Links for plugin
	 *
	 * @param array  $plugin_actions Current plugin actions.
	 * @param string $plugin_file Plugin file name.
	 * @return array
	 */
	public function addPluginActionLinks( $plugin_actions, $plugin_file ) {
		return $plugin_actions;
	}
}
