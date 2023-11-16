<?php
/**
 * Review Notices.
 *
 * Notices to review the plugin.
 *
 * Author:          Uriahs Victor
 *
 * @link    https://soaringleads.com
 * @since   1.0.0
 * @package Notices
 */

namespace SoaringLeads\DCashWC\Notices;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SoaringLeads\DCashWC\Notices\Notice;
use SoaringLeads\DCashWC\Traits\PluginInfo;

/**
 * Class UpsellsNotices.
 */
class ReviewNotices extends Notice {

	use PluginInfo;

	/**
	 * Class constructor
	 *
	 * @return void
	 */
	public function __construct() {
		$this->createReviewPluginNotice();
	}

	/**
	 * Create leave review for plugin notice.
	 *
	 * @return void
	 */
	public function createReviewPluginNotice() {

		$days_since_installed = $this->getDaysSinceInstalled();

		// Show notice after 3 weeks.
		if ( $days_since_installed < 21 ) {
			return;
		}

		$content = array(
			'title' => __( 'Has DCash for WooCommerce Helped You?', 'integrate-dcash-with-woocommerce' ),
			'body'  => __( 'Hey! its Uriahs Victor, Sole Developer working on DCash for WooCommerce. Has the plugin benefitted your website? If yes, then would you mind taking a few seconds to leave a kind review? Reviews go a long way and they really help keep me motivated to continue working on the plugin and making it better.', 'integrate-dcash-with-woocommerce' ),
			'cta'   => __( 'Sure!', 'integrate-dcash-with-woocommerce' ),
			'link'  => 'https://wordpress.org/support/plugin/integrate-dcash-with-woocommerce/reviews/#new-post',
		);

		$this->createNoticeMarkup( 'leave_review_notice_1', $content );
	}


}
