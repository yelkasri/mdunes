<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WLoadFile( 'files.controller.files_savefile' );
class Files_attach_savefile_controller extends Files_savefile_controller {
function savefile() {
	$this->controller = 'files-attach';
	$this->index = 'popup';
	return parent::savefile();
}}