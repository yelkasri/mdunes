<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('form.textarea');
class Theme_Previewtheme_form extends WForm_textarea {
function show(){
$tmid=WGlobals::getEID();
if(empty($tmid))$tmid=WGlobals::get('tmid');
$themeC=WClass::get('theme.helper');
$type=$themeC->getCol($tmid,'type');
$folder=$themeC->getCol($tmid,'folder');
$destfolder=$themeC->destfolder($type);
$cssfolder=JOOBI_DS_THEME.$destfolder.DS.$folder.DS.'css';
$systemFolderC=WGet::folder();
if($systemFolderC->exist($cssfolder)!=false){
WPage::addCSSFile($cssfolder.DS.'style.css');
}
$path=JOOBI_DS_THEME .$destfolder.DS.$folder.DS.'index.html';
$fileClass=WGet::file();
$size=$fileClass->size($path);
$data_in_the_file=$fileClass->read($path);
$row_data=explode( "\r\n".'[',$data_in_the_file );
if(!empty($row_data)){
foreach($row_data as $single_data){
$single_data=trim($single_data);
if(!empty($single_data)){
$this->value .=trim( nl2br($single_data));
}
}
}
parent::show();
return true;
}}