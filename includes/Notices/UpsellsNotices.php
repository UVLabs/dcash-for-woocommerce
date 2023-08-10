<?php
/**
 * Class responsible for upsell notices.
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
class UpsellsNotices extends Notice {

	use PluginInfo;

	/**
	 * Class constructor
	 *
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * Create initial pro released notice.
	 *
	 * @return void
	 */
	public function createProNotice() {

		$days_since_installed = $this->getDaysSinceInstalled();

		// Show notice after 4 days.
		if ( $days_since_installed < 3 ) {
			return;
		}

		$content = array(
			'title' => __( 'Try out the PRO version.', 'dcash-for-woocommerce' ),
			'body'  => __( 'Replace me with content.', 'dcash-for-woocommerce' ),
			'link'  => '',
		);

		$this->createNoticeMarkup( 'initial_pro_launch_notice', $content );
	}
}
