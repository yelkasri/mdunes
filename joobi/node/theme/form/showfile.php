<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_CoreShowfile_form extends WForms_default {
function create(){
$fileNameEncoded=WGlobals::get('file');  
$file=unserialize( base64_decode($fileNameEncoded));
$filetype=WGlobals::get('filetype');
$tmid=WGlobals::getEID();
$themeC=WClass::get('theme.helper');
$type=$themeC->getCol($tmid,'type');
$folder=$themeC->getCol($tmid,'folder');
$destfolder=$themeC->destfolder($type );
WGlobals::set('idLabel',$this->idLabel );
if(!$themeC->getFileContent($tmid, $filetype, $file )){
$warnings=WMessage::get();
$FILENAME=str_replace( JOOBI_DS_JOOBI, '',$file );
$warnings->userE('1316671955LOQA',array('$FILENAME'=>$FILENAME));
WPages::redirect('controller=theme&task=show&eid='.$tmid );
}
$fileContent=$themeC->getFileContent($tmid, $filetype, $file );
$replacedContent=str_replace( array("\n\r", "\r\n", "\n", "\r"), '<br />',$fileContent );
$replacedContent=str_replace( array( "\t" ), '&nbsp;&nbsp;&nbsp;',$replacedContent );
$this->content=$replacedContent;
return true;
}}