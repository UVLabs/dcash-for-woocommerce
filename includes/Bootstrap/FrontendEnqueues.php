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

use SoaringLeads\DCashWC\Helpers\Functions;

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
		$dir                       = ( DCASH_WC_DEBUG === false ) ? 'build/' : '';
		$dcash_script_dependencies = array(
			'jquery',
			'wp-util',
		);
		if ( Functions::sandboxModeEnabled() === false ) {
			wp_enqueue_script( $this->plugin_name . '-live-script', DCASH_WC_PLUGIN_ASSETS_PATH_URL . 'public/js/lib/dcash-ecommerce.js', array( 'jquery' ), $this->version, false );
			array_push( $dcash_script_dependencies, "{$this->plugin_name}-live-script" );
		} else {
			wp_enqueue_script( $this->plugin_name . '-sandbox-script', DCASH_WC_PLUGIN_ASSETS_PATH_URL . 'public/js/lib/dcash-ecommerce-sandbox.js', array( 'jquery' ), $this->version, false );
			array_push( $dcash_script_dependencies, "{$this->plugin_name}-sandbox-script" );
		}

		wp_enqueue_script( $this->plugin_name, DCASH_WC_PLUGIN_ASSETS_PATH_URL . 'public/js/' . $dir . 'dcash-wc-public.js', $dcash_script_dependencies, $this->version, false );
	}

	/**
	 * Turn a script into a module so that we can make use of JS components.
	 *
	 * @param string $tag The entire <script> tag.
	 * @param string $handle The handle used to register the script.
	 * @param string $src The source of the script.
	 * @return string
	 * @since 1.0.0
	 */
	public function make_scripts_modules( string $tag, string $handle, string $src ) {

		$handles = array(
			$this->plugin_name,
		);

		if ( ! in_array( $handle, $handles, true ) ) {
			return $tag;
		}

		$id = $handle . '-js';

		$parts = explode( '</script>', $tag ); // Break up our string.

		foreach ( $parts as $key => $part ) {
			if ( false !== strpos( $part, $src ) ) { // Make sure we're only altering the tag for our module script.
				$parts[ $key ] = '<script type="module" src="' . esc_url( $src ) . '" id="' . esc_attr( $id ) . '">'; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- We're not enqueuing or outputting any script here.
			}
		}

		$tags = implode( '</script>', $parts ); // Bring everything back together.

		return $tags;
	}

}
