<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_Filetype_picklist extends WPicklist {
function create() {
	static $filesM = null;
	$filesM = WModel::get( 'files' );
	$filesM->select( 'type' );
	$filesM->groupBy( 'type' );
	$types = $filesM->load('lra');
	foreach( $types as $type ) {
		if ( empty($type) ) continue;
		$this->addElement( $type, $type );
	}
	return true;
}}