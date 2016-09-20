<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WLoadFile( 'files.controller.files_saveurl' );
class Files_attach_saveurl_controller extends Files_saveurl_controller {
function saveurl() {
	$this->controller = 'files-attach';
	$this->index = 'popup';
	return parent::saveurl();
}}