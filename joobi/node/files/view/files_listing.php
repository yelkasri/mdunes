<?php 


* @license GNU GPLv3 */

class Files_Files_listing_view extends Output_Listings_class {
protected function prepareView() {
	if ( WRoles::isNotAdmin( 'storemanager' ) ) $this->removeElements( array('files_listing_vendor') );
	return true;
}}