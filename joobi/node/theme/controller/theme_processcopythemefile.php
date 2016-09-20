<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_processcopythemefile_controller extends WController {
function processcopythemefile(){
$tmid=WGlobals::get('eid');
$type=WGlobals::get('type'); 
$trk=WGlobals::get( JOOBI_VAR_DATA );
$fileType=WGlobals::get('filetype'); 
$fileToCopy=WGlobals::get('filename');
$fileToCopy=unserialize(base64_decode($fileToCopy));
$filename=$trk['x']['filenamecopy']; 
$fileClass=WGet::file();
$themeC=WClass::get('theme.helper');
$type=$themeC->getCol($tmid,'type');
$folder=$themeC->getCol($tmid,'folder');
$destfolder=$themeC->destfolder($type);
$fileDest=JOOBI_DS_THEME.$destfolder.DS.$folder.DS.$fileType;
if($fileType=='view')$fileExt='php';
elseif($fileType=='css')$fileExt='css';
else $fileExt='js';
if($fileClass->exist($fileDest.DS.$filename.'.'.$fileExt)){
$message=WMessage::get();
$message->userE('1307005978SBLO');
$this->setView('theme_copy_file');
return true;
}
if($filename=='index'){
$message=WMessage::get();
$message->userE('1307012876GLGV');
$this->setView('theme_copy_file');
return true;
}
$fileClass->copy($fileDest.DS.$fileToCopy, $fileDest.DS.$filename.'.'.$fileExt );
return true;
}}