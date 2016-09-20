<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_upload_controller extends WController {
function upload(){
$appsUploadC=WClass::get('apps.upload');
$status=$appsUploadC->uploadINstallPackage();
if(!$status ) return true;
if(!defined('JOOBI_INSTALLING')) define('JOOBI_INSTALLING', 1 );
return true;
}
}