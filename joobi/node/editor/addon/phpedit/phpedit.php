<?php 


* @license GNU GPLv3 */

class Editor_phpEdit_addon extends Editor_Get_class {
	function getContent($fieldName) {
				$content = WGlobals::get( $fieldName, '', 'POST', 'htmlentity' );
		return $content;
	}
}