<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
### Copyright (c) 2006-2016 Joobi. All rights reserved.
### license GNU GPLv3 , link joobi.info/license
class WDiscoverEntry {
	public static function discover() {
		if ( defined('JVERSION') ) {
			$version = explode( '_', JVERSION );
			if ( version_compare( $version[0], '3.0.0', '>='  ) ) {
				define('JOOBI_FRAMEWORK', 'joomla30' );
				return true;
			}		} elseif ( defined('ABSPATH') ) {
			define('JOOBI_FRAMEWORK', 'wp4' );
			return true;
		}
				if ( ! defined('JOOBI_FRAMEWORK') ) {
			die( 'unknown Framework' );
		}
		return false;
	}
}