<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://soaringleads.com
 * @since      1.0.0
 *
 * @package    SoaringLeads\DCashWC
 * @author_name     Uriahs Victor <plugins@soaringleads.com>
 */

namespace SoaringLeads\DCashWC\Bootstrap;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class responsible for methods to do with admin enqueing of JS and CSS.
 *
 * @package SoaringLeads\DCashWC\Bootstrap
 * @since 1.0.0
 */
class AdminEnqueues {

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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueueStyles() {
		wp_enqueue_style( $this->plugin_name, DCASH_WC_PLUGIN_ASSETS_PATH_URL . 'admin/css/dcash-wc-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueueScripts() {
		wp_enqueue_script( $this->plugin_name, DCASH_WC_PLUGIN_ASSETS_PATH_URL . 'admin/js/dcash-wc-admin.js', array( 'jquery' ), $this->version, false );
	}

}
