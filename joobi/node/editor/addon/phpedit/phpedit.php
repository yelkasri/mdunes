<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Editor_phpEdit_addon extends Editor_Get_class {
	function getContent($fieldName) {
				$content = WGlobals::get( $fieldName, '', 'POST', 'htmlentity' );
		return $content;
	}
}