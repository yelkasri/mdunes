<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('form.textarea');
class Theme_Editfile_form extends WForm_textarea {
function create(){
$fileNameEncoded=WGlobals::get('file');  
$file=unserialize(base64_decode($fileNameEncoded));
$filetype=WGlobals::get('filetype');
$tmid=WGlobals::getEID();
$themeC=WClass::get('theme.helper');
$type=$themeC->getCol($tmid,'type');
$folder=$themeC->getCol($tmid,'folder');
$destfolder=$themeC->destfolder($type);
WGlobals::set('idLabel',$this->idLabel);
if(false===$themeC->getFileContent($tmid, $filetype, $file )){
$FILENAME=str_replace( JOOBI_DS_JOOBI, '',$file );
$this->userE('1316671955LOQA',array('$FILENAME'=>$FILENAME));
WPages::redirect('controller=theme&task=show&eid='.$tmid );
}
$this->value=$themeC->getFileContent($tmid, $filetype, $file );
return parent::create();
}
}