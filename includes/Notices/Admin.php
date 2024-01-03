<?php
/**
 * Admin Notices.
 *
 * Houses all the notices to show in admin dashboard.
 *
 * @link    https://soaringleads.com
 * @since    1.0.0
 *
 * @package    SoaringLeads\DCashWC
 */

namespace SoaringLeads\DCashWC\Notices;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin notices class.
 *
 * @package SoaringLeads\DCashWC\Notices
 */
class Admin {

	/**
	 * Detect if site has HTTPS support.
	 *
	 * @since    1.0.0
	 */
	public function siteNotHttps() {

		if ( is_ssl() ) {
			return;
		}

		if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
			return;
		}

		?>

		<div class="notice notice-error is-dismissible">
		<?php
		/* translators: 1: Opening <p> HTML element 2: Opening <strong> HTML element 3: Closing <strong> HTML element 4: Closing <p> HTML element  */
		echo sprintf( esc_html__( '%1$s%2$s DCash for WooCommerce NOTICE:%3$s HTTPS not detected on this website. The plugin will not work. Please enable HTTPS on this website.%4$s', 'integrate-dcash-with-woocommerce' ), '<p>', '<strong>', '</strong>', '</p>' );
		?>
		</div>
		<?php
	}

}
