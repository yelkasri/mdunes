<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_saveserver_controller extends WController {
	function saveserver() {
		$trk = WGlobals::get( JOOBI_VAR_DATA );
		$uploadFile = $trk['x'];	
		$uploadFiles->directory = $uploadFile['directory']; 	
		$uploadFiles->extension = $uploadFile['extension']; 	
		$uploadFiles->keepfile = $uploadFile['tkeepfile']; 	
		$uploadFiles->secure = $uploadFile['secure'];
		$uploadFiles->showUploadedFiles = 1; 
		$uploadFiles->controller = ( !empty($this->controller) )? $this->controller : 'files';
		$uploadFiles->index = ( !empty($this->index) )? $this->index : 'default';
		$filesMediaC = WClass::get( 'files.media' );	 
		$status = $filesMediaC->uploadDirectory($uploadFiles);
		$cacheC = WCache::get();
		$cacheC->resetCache( 'Model_product_node' );
		WPages::redirect('controller='.$uploadFiles->controller.'&task=listing', '', $uploadFiles->index);
	}	
}