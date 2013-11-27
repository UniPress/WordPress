<?php

namespace WordPress;

class WPFeedCache extends \SimplePie_Cache {
	/**
	 * Create a new \SimplePie_Cache object
	 *
	 * @static
	 * @access public
	 */
	function create($location, $filename, $extension) {
		return new WPFeedCacheTransient($location, $filename, $extension);
	}
}
