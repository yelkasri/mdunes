<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_updatefile_controller extends  WController {
	function updatefile() {
		$trk = WGlobals::get( JOOBI_VAR_DATA );
		if ( empty($trk['x']) ) return false;
		$uploadFile = $trk['x'];
		$modelId = WModel::getID( 'files' );
		if ( empty($trk[$modelId]['alias']) ) return false;
		$name = $trk[$modelId]['alias'];
		$filid = $trk[$modelId]['filid'];
		$secure = $trk[$modelId]['secure'];
		$message = WMessage::get();
		$filesM = WModel::get('files');
		if ( !empty($filid) ) {
			$filesM->whereE( 'filid', $filid );
			$file = $filesM->load('o', array( 'alias', 'path', 'secure', 'name', 'type', 'thumbnail') );
		}
		$hasUploadFile = false;
		$filesFancyuploadC = WClass::get( 'files.fancyupload' );
		$fancyFileUpload = $filesFancyuploadC->check();
		$map = 'filid';
		$modelID = WModel::getID( 'files' );
		if ( $fancyFileUpload ) {
			$files = $this->getUploadedFiles();
			if ( !empty($files['tmp_name'][$modelID][0][$map]) ) {
				$filesM->type = $files['type'][$modelID][0][$map];
				$filesM->_name = $files['name'][$modelID][0][$map];
				$filesM->_tmp_name = $files['tmp_name'][$modelID][0][$map];
				$filesM->_error = $files['error'][$modelID][0][$map];
				$filesM->_size = $files['size'][$modelID][0][$map];
				$hasUploadFile = true;
			}
		} else {
			$files = WGlobals::get( JOOBI_VAR_DATA, array(), 'FILES', 'array' );
			if ( !empty($files['tmp_name'][$modelID][$map]) ) {
				$filesM->type = $files['type'][$modelID][$map];
				$filesM->_name = $files['name'][$modelID][$map];
				$filesM->_tmp_name = $files['tmp_name'][$modelID][$map];
				$filesM->_error = $files['error'][$modelID][$map];
				$filesM->_size = $files['size'][$modelID][$map];
				$hasUploadFile = true;
			}
		}
		$status = true;
		$uid = WUser::get('uid');
		$path =  ( $secure ) ? '' : 'download';
		$folders = ( $secure ) ? 'safe' : 'media';
				if ( $hasUploadFile ) {
			$this->_removeExistingFile( $file );
			if ( $fancyFileUpload ) {
				$filesM->type = $files['type'][$modelID][0][$map];
				$filesM->_name = $files['name'][$modelID][0][$map];
				$filesM->_tmp_name = $files['tmp_name'][$modelID][0][$map];
				$filesM->_error = $files['error'][$modelID][0][$map];
				$filesM->_size = $files['size'][$modelID][0][$map];
			} else {
				$filesM->type = $files['type'][$modelID][$map];
				$filesM->_name = $files['name'][$modelID][$map];
				$filesM->_tmp_name = $files['tmp_name'][$modelID][$map];
				$filesM->_error = $files['error'][$modelID][$map];
				$filesM->_size = $files['size'][$modelID][$map];
			}
			$filesM->secure = ( $secure ? true : false );
			$filesM->alias = $name;
			$filesM->filid = $filid;
			$filesM->thumbnail = 0;
			$filesM->_path =  $path;
			$filesM->_folder = $folders;
			$typeA = explode( '/', $filesM->type );
			$type = $typeA[0];
			$filesM->setFormat( WPref::load( 'PITEM_NODE_DWLDFORMAT' ) );
			$filesM->_fileType = ( $type == 'image' ) ? 'images' : 'files';
			$status = $filesM->save();
		} elseif ( !empty($uploadFile['url']) ) {				if ( !empty($filid) ) {
				$this->_removeExistingFile($file);
				$filesM->whereE( 'filid', $filid );
				$filesM->setVal('alias', $name);
				$filesM->setVal('name', $uploadFile['url']);
				$filesM->setVal('type','url');
				$filesM->setVal('secure', $secure);
				$filesM->setVal('modified',time());
				$filesM->setVal('uid', $uid );
				$filesM->setVal('thumbnail', $uid );
				$filesM->setVal('size', 0 );
				$filesM->setVal('width', 0 );
				$filesM->setVal('height', 0 );
				$filesM->setVal('twidth', 0 );
				$filesM->setVal('theight', 0 );
				$filesM->setVal('mime', '' );
				$filesM->setVal('modifiedby', $uid );
				$status = $filesM->update();
			}
		} elseif ( !empty($uploadFile['directory']) ) {				$this->_removeExistingFile( $file );
			$uploadFiles->directory = $uploadFile['directory'];
			$uploadFiles->extension = '';
			$uploadFiles->filename = $uploadFile['filename'];
			$uploadFiles->keepfile = $uploadFile['keep'];
			$uploadFiles->secure = $secure;
			$uploadFiles->filid = $filid;
			$uploadFiles->name = $name;
			$filesMediaC = WClass::get( 'files.media' );
			$status = $filesMediaC->uploadDirectory( $uploadFiles );
		} else {				if ( $file->secure != $secure ) {
				$message->historyN('1316669857KIWF');
				return true;
			} else {
				if ( !empty($filid) ) {
					$filesM->whereE('filid', $filid );
					$filesM->setVal('alias', $name );
					$filesM->setVal('secure', $secure );
					$filesM->setVal('modified',time() );
					$filesM->setVal('modifiedby', $uid );
					$status = $filesM->update();
				}			}		}
		if ( empty($this->controller) ) $this->controller = 'files';
		if ( empty($this->index) ) $this->index = 'default';
		if ( $status ) $message->userS('1442980567MLVC');
		else $message->userE('1314849832QOQC');
		if ( $this->controller == 'files' ) {
			WPages::redirect( 'controller=' . $this->controller . '&task=listing&search=' . $filid );
					} else {
					}		
		$cacheC = WCache::get();
		$cacheC->resetCache( 'Model_product_node' );
		WPages::redirect( 'controller=' . $this->controller . '&task=listing' );
		return true;
	}
	private function _removeExistingFile($file) {
			if (!empty($file->path) || !empty($file->secure)){
				$basePath = JOOBI_DS_USER;
				$parts = explode('|', $file->path);
				$file->path = implode(DS, $parts);
				$folders = ($file->secure)? 'safe' : 'media';
				$completePath = $basePath . $folders . DS . $file->path . DS;
				$fullfile = $completePath . $file->name . '.' . $file->type;
				if ($file->thumbnail) {
					$thumbnail = $completePath . 'thumbnails' . DS . $file->name . '.' . $file->type;
					if ( file_exists($thumbnail) ) unlink($thumbnail);
				}				if ( file_exists($fullfile) )  unlink($fullfile);
			}
	}
}