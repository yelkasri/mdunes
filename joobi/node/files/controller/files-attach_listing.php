<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_attach_listing_controller extends WController {
function listing() {
	$pid = WGlobals::get('pid');
	$model= WGlobals::get('model');
	$map = WGlobals::get('map');
	if (empty($model)) $model = 'item';
	if (empty($map)) $map = 'filid';
	$downloadM = WModel::get( $model );
	$downloadM->makeLJ( 'files', 'filid' );
	$downloadM->select( array( 'filid', 'alias', 'type', 'name'), 1 );
	$downloadM->whereE( 'pid', $pid );
	$filidA = $downloadM->load( 'ol', $map );
	$message = WMessage::get();
	$removeCurrentA = array();
	if ( !empty($filidA) ) {
		foreach( $filidA as $fileInfo ) {
			if ( !empty($fileInfo->filid) ) {
				$removeCurrentA[] = $fileInfo->filid;
				if ( empty($fileInfo->alias) ) $fileInfo->alias = $fileInfo->name;
				$NAME = $fileInfo->name;
				$TYPE = $fileInfo->type;
				$message->userN('1369749800BILF',array('$NAME'=>$NAME,'$TYPE'=>$TYPE));
			} else {
				$message->userN('1315887071NUXP');
			}
		}
		WGlobals::set( 'removeCurrentA', $removeCurrentA );
	}
	WGlobals::set( 'listOfSelectedFilesA', $filidA, 'global' );
	WGlobals::setSession( 'files', 'popup', 1 );
	return true;
}}