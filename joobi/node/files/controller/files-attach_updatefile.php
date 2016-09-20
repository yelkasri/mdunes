<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WLoadFile( 'files.controller.files_updatefile' );
class Files_attach_updatefile_controller extends Files_updatefile_controller {
	function updatefile() {
		$this->controller = 'files-attach';
		$this->index = 'popup';
		return parent::updatefile();
	}}