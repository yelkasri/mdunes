<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('form.text');
class WForm_Coremedia extends WForm_text {
protected $inputType='file';
private $_maxFileSizeShow=0;
private $_allowedFormatsA=array();
private $_maxFiles=1;
function create(){
if(!WPref::load('PLIBRARY_NODE_ALLOW_FILE_UPLOAD')) return false;
return parent::create();
}
function show(){
if(empty($this->value)) return false;
if(empty($this->element->imageWidth)){
$definedImgWidth=(int)WGlobals::get('maxImageWidth', 180 );
$this->element->imageWidth=$definedImgWidth;
}if(empty($this->element->imageHeight)){
$definedImgHeight=(int)WGlobals::get('maxImageHeight', 180 );
$this->element->imageHeight=$definedImgWidth;
}
$filesMediaC=WClass::get('files.media');
$this->content=$filesMediaC->renderHTML($this->value, $this->element ).'<br />';
WGlobals::set('media-type-show-view',$filesMediaC->fileType );
return true;
}
protected function _maxFileUpload(){
static $maxFileSize=null;
$allowedFiles='';
if(!empty($this->element->sid)){
$usedModelM=WModel::get($this->element->sid );
$this->_allowedFormatsA=(!empty($usedModelM->_fileInfo[$this->element->map]->format )?$usedModelM->_fileInfo[$this->element->map]->format : array());
if(empty($this->_allowedFormatsA) && !empty($this->element->format)){
$this->_allowedFormatsA=$this->element->format;
}
$allowedFiles='';
if(!empty($this->_allowedFormatsA)){
$allowedFormatS=(is_array($this->_allowedFormatsA)? implode(',',$this->_allowedFormatsA ) : $this->_allowedFormatsA );
$allowedFormatS=strtolower($allowedFormatS );
$allowedFormatS=str_replace(' ','',$allowedFormatS );
$allowedFormatS=str_replace(',',',',$allowedFormatS );
$allowedFiles='. '.WText::t('1360107637HVSN'). $allowedFormatS;
}else{
if('output.image'==$this->element->type){
$allowedFiles='. '.WText::t('1360107637HVSN'). ' jpg, png, gif';
}
}
}
$name='maxFileUpload-'.$this->element->namekey;
$maxFileUpload=WGlobals::get($name, 0, 'global');
if(empty($maxFileUpload)){
$maxFileUpload=WPref::getOne('imgmaxsize',$this->nodeID );
}
if(!empty($this->element->maxsize)){
$maxFileUpload=$this->element->maxsize;
}
$maxFileUpload=$maxFileUpload * 1024;
if(!isset($maxFileSize)){
$maxFileSize1=@ini_get('post_max_size');
$maxFileSize2=@ini_get('upload_max_filesize');
$maxFileSize=($maxFileSize2 > $maxFileSize1 )?$maxFileSize2 : $maxFileSize1;
}
if(!empty($maxFileUpload) && $maxFileUpload < WTools::returnBytes($maxFileSize)){
$this->_maxFileSizeShow=WTools::returnBytes($maxFileUpload, true);
}else{
$this->_maxFileSizeShow=WTools::returnBytes( WTools::returnBytes($maxFileSize ), true);
}
if(!empty($this->_maxFileSizeShow )){
return ' '.WText::t('1241675239ROHF'). ': '.$this->_maxFileSizeShow . $allowedFiles.'<br>';
}
return false;
}
public function exportConversion($value){
$filesHelperC=WClass::get('files.helper');
return $filesHelperC->getPath($value, true);
}
public function importConversion($value){
return 'filid_WZX_filid';
}
}