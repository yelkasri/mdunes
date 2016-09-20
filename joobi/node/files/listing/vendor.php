<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_CoreVendor_listing extends WListings_default{
function create() {
	if ( WRoles::isNotAdmin( 'storemanager' ) ) return false;
	if ( empty($this->value) ) $this->value = 1;
	static $vendorC=null;
	if ( !isset($vendorC) ) $vendorC = WClass::get('vendor.helper', null, 'class', false );
	if ( !WExtension::exist( 'vendors.node' ) ) {
		 $vendorO = $vendorC->getVendor($this->value);
		 $vendor = ( isset( $vendorO->name ) && !empty( $vendorO->name ) ) ? $vendorO->name : WUser::get('name', $vendorO->uid);
	} else {
		$vendor = ( !empty($vendorC) ) ? $vendorC->showVendName($this->value, 0, true) : '';
	}
	$this->content = $vendor;
	return true;
}}