<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_securedownload_controller extends WController {
function securedownload() {
	$filid = WGlobals::getEID();
	$roleHelper = WRole::get();
	$hasRole = WRole::hasRole( 'storemanager' );
	$itemDownloadC = WClass::get('files.download');
	$itemDownloadC->getFile( $filid, !$hasRole );
	return true;
}}