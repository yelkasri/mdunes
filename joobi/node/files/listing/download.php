<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_CoreDownload_listing extends WListings_default{
function create() {
	if ( $this->getValue( 'secure', 'files') ) {	
		$fileOwnerUID = $this->getValue( 'uid', 'files' );
		$uid = WUser::get( 'uid' );
		$roleHelper = WRole::get();
		$hasRole = WRole::hasRole( 'storemanager' );
		if ( $uid != $fileOwnerUID && !$hasRole ) {
			$this->content = WText::t('1326149408DUCB');
			return true;
		}
		$link = WPage::routeURL( 'controller=files&task=securedownload&eid='. $this->getValue( 'filid' ) );
	} else {
		$typeOfFile = $this->getValue( 'type', 'files');
		if ( $typeOfFile == 'url' ) {
			$link = $this->getValue( 'name', 'files');
		} else {
			$path = JOOBI_URL_MEDIA;
			$path .= str_replace( '|', '/', $this->getValue( 'path', 'files') ) . '/';
			$link =  $path . $this->getValue( 'name', 'files') . '.' . $this->getValue( 'type', 'files');
		}
	}
	$this->content = '<a target="_blank" href="'.$link.'">'.WText::t('1206961905BHAV').'</a>';
	return true;
}}