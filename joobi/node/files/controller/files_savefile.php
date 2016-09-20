<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_savefile_controller extends WController {
	function savefile() {
		$trk = WGlobals::get( JOOBI_VAR_DATA );
		$uploadFile = $trk['x'];
		$filesM = WModel::get('files');
		$filesFancyuploadC = WClass::get( 'files.fancyupload' );
		$fancyFileUpload = $filesFancyuploadC->check();
		if ( $fancyFileUpload ) {
			$files = $this->getUploadedFiles();
			$map = 'x[file';
			if ( !isset($files['type'][0][0][$map]) ) return false;
			$filesM->type = $files['type'][0][0][$map];
			$filesM->_name = $files['name'][0][0][$map];
			$filesM->_tmp_name = $files['tmp_name'][0][0][$map];
			$filesM->_error = $files['error'][0][0][$map];
			$filesM->_size = $files['size'][0][0][$map];
		} else {
			$files = WGlobals::get( JOOBI_VAR_DATA, array(), 'FILES', 'array' );
			$filesM->type = $files['type']['x']['file'];
			$filesM->_name = $files['name']['x']['file'];
			$filesM->_tmp_name = $files['tmp_name']['x']['file'];
			$filesM->_error = $files['error']['x']['file'];
			$filesM->_size = $files['size']['x']['file'];
		}
		$message = WMessage::get();
		$filesM->alias = $uploadFile['name1'];
		$filesM->thumbnail = 0;
		$filesM->secure = ($uploadFile['target']) ? true : false;
		if ( !empty($uploadFile['storage']) ) $filesM->_storage = $uploadFile['storage'];
		$filesM->_path =  (!$uploadFile['target'])?  'download': '';
		$filesM->_folder = (!$uploadFile['target'])? 'media' : 'safe';
		$filesM->_format = WPref::load( 'PITEM_NODE_DWLDFORMAT' );			$filesM->_maxSize = WPref::load( 'PITEM_NODE_DWLDMAXSIZE' ) * 1028;	
		$typeA = explode( '/', $filesM->type );
		$type = $typeA[0];
		$filesM->_fileType = ( $type == 'image' ) ? 'images' : 'files';
		if ( empty($this->controller) ) $this->controller = 'files';
		if ( empty($this->index) ) $this->index = 'default';
		$filesM->setFormat( WPref::load( 'PITEM_NODE_DWLDFORMAT' ) );
		$filesM->returnId();
		$status = $filesM->save();
		if ( !$status || empty($filesM->filid) ) return true;
		$filid =  $filesM->filid;
		$name = $uploadFile['name1'];
		$createItem = $uploadFile['createitem'];
		if ( !empty($createItem) ) {					if ( WExtension::exist( 'download.node' ) ) {
				$downloadM = WModel::get( 'download' );
				$downloadM->filid = $filid;
				$downloadM->setChild( 'downloadtrans', 'name', $name );
				$downloadM->setChild( 'downloadtrans', 'description', $name );
				$downloadM->save();
			}			
		}
		if ( $status ) $message->userS('1318337621NXST');
		else $message->userE('1314849832QOQC');
		$cacheC = WCache::get();
		$cacheC->resetCache( 'Model_product_node' );
		WPages::redirect( 'controller=' . $this->controller . '&task=listing', '', $this->index );
		return true;
	}
}