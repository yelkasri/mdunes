<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
/**
* @copyright Copyright (c) 2006-2016 Joobi. All rights reserved.
* This file is released under the GPL (General Public License)
*/
class Inc_Lib_Archivetar_include {
/** Constructor */
	function __construct() {
		if ( !class_exists('Archive_Tar') ) {
//			WExtension::includes( 'lib.pear' );
			if ( file_exists( JOOBI_DS_INC . 'lib' . DS . 'archivetar' . DS . 'tar.php' ) ) {
				require( JOOBI_DS_INC . 'lib' . DS . 'archivetar' . DS . 'tar.php' );
			} else {
				$mess = 'The file: ' . JOOBI_DS_INC . 'lib' . DS . 'archivetar' . DS . 'tar.php   does not exist';
				$mess = "\n\r" . 'The file might have been removed by the server for security peurpose!!!!!!!!!!!!!!!!!!';
				WMessage::log( $mess, 'install' );
			}//endif
		}//endif
	}//endfct
}//endclass
