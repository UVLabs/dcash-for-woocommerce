<?php
/**
 * Load Notices to admin notices hook.
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

use SoaringLeads\DCashWC\Notices\ReviewNotices;

/**
 * The Loader class.
 */
class Loader {

	/**
	 * Load our notices.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function loadNotices() {
		// ( new ReviewNotices() );
	}
}
