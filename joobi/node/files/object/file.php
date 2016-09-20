<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_File_object {
	public $name = ''; 	public $type = ''; 	public $fileID = ''; 	public $basePath = '';		public $path = '';
	public $thumbnail = false;	
	public $secure = false; 	
	public $storage = null;	
	public function fileURL($thumbnail=false) {
		$fileInstance = WGet::file( $this->storage );
		if ( empty($fileInstance) ) return false;
		$fileInstance->setFileInformation( $this );
		return $fileInstance->fileURL( $thumbnail );
	}
	public function isImage() {
				$fileInstance = WGet::file( $this->storage );
		if ( empty($fileInstance) ) return false;
		if ( in_array( $this->type, array( 'png', 'gif', 'jpeg', 'jpg' ) ) ) return true;
		else false;
	}	
}