<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://soaringleads.com
 * @since      1.0.0
 *
 * @package    SoaringLeads\DCashWC
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    SoaringLeads\DCashWC
 * @author_name     Uriahs Victor <plugins@soaringleads.com>
 */
namespace SoaringLeads\DCashWC\Bootstrap;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class responsible for methods to do with frontend enqueing of JS and CSS.
 *
 * @package SoaringLeads\DCashWC\Bootstrap
 * @since 1.0.0
 */
class FrontendEnqueues {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->plugin_name = DCASH_WC_PLUGIN_NAME;
		$this->version     = DCASH_WC_VERSION;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueueStyles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, DCASH_WC_PLUGIN_ASSETS_PATH_URL . 'public/css/dcash-wc-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueueScripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, DCASH_WC_PLUGIN_ASSETS_PATH_URL . 'public/js/dcash-wc-public.js', array( 'jquery' ), $this->version, false );
	}

}
