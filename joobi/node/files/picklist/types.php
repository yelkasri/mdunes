<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_Types_picklist extends WPicklist {
function create() {
		$fileTypePicklist = WGlobals::get( 'mediaUploadFileTypePicklist', '', 'global' );
	if ( empty($fileTypePicklist) ) $fileTypePicklist = 'files_type';
	$filesTypeP = WView::picklist( $fileTypePicklist );
	$list = $filesTypeP->getList();
	if ( empty($list) ) return false;
	$defaultValue = ( !empty($this->defaultValue) ? current( $this->defaultValue ) : '' );
	$defaultValueFile = 'file';
	if ( !empty($defaultValue) ) {
		if ( !$filesTypeP->inValues($defaultValue) ) {
			$defaultValueFile = $defaultValue;
		}
	}
	foreach( $list as $key => $val ) {
		if ( $key != 'file' ) $this->addElement( $key , $val );
		else {
			$this->addElement( $defaultValueFile , $defaultValueFile );
		}
	}
	return true;
}
}