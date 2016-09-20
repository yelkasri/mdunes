<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_attach_delete_controller extends WController {
function delete() {
	$status = parent::delete();
	$pid= WGlobals::get('pid');
	$map= WGlobals::get('map');
	$model = WGlobals::get('model');
	if ( $status ) {
		$message = WMessage::get();
		$message->userS('1369749799PPLZ');
	}
	WPages::redirect( 'controller=files-attach&task=listing&pid='.$pid .'&map='.$map .'&model='.$model ) ;
	return true;
}}