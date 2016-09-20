<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_processuploadthemefile_controller extends WController {
function processuploadthemefile(){
echo 'need to be implemented';
exit;
$tmid=WGlobals::get('eid');
$type=WGlobals::get('type');
$trkFile=WGlobals::get( JOOBI_VAR_DATA, '','files');
$trk=WGlobals::get( JOOBI_VAR_DATA );
$fileLocation=$trkFile['tmp_name']['x']['file'];
$fileToUpload=$trkFile['name']['x']['file'];
$error=$trkFile['error']['x']['file'];
$filetype=$trk['x']['filetype'];
if($error > 0){
$message=WMessage::get();
$message->userE('1417812852DFNC');
$this->setView('theme_upload_file');
return true;
}
if($filetype==1){
$themeFiletype='view';
}elseif($filetype==2){
$themeFiletype='css';
}else{
$themeFiletype='js';
}
$fileClass=WGet::file();
$themeC=WClass::get('theme.helper');
$type=$themeC->getCol($tmid,'type');
$folder=$themeC->getCol($tmid,'folder');
$destfolder=$themeC->destfolder($type);
$fileDest=JOOBI_DS_THEME.$destfolder.DS.$folder.DS.$themeFiletype;
$fileClass->upload($fileLocation, $fileDest.DS.$fileToUpload, false);
return true;
}}