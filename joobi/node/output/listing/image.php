<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WListing_CoreImage extends WListings_default{
function create(){
static $indentWidth=null;
$maxWidth=(!empty($this->element->imgmaxwidth)?$this->element->imgmaxwidth : 50 );
$maxHeight=(!empty($this->element->imgmaxheight)?$this->element->imgmaxheight : 50 );
$fileID=WModel::get('files','sid');
if(empty($this->data->name)){
if($fileID==$this->modelID)$fileMID=$fileID;
else $fileMID=WModel::get('images','sid');
$path='path_'.$fileMID;
$name='name_'.$fileMID;
$type='type_'.$fileMID;
$thumbnail='thumbnail_'.$fileMID;
$storage='storage_'.$fileMID;
$mwidth='width_'.$fileMID;
$twidth='twidth_'.$fileMID;
$mheight='height_'.$fileMID;
$theight='theight_'.$fileMID;
$secureLocation='secure_'.$fileMID;
if(!isset($this->data->$twidth))$this->data->$twidth=$maxWidth;
if(!isset($this->data->$theight))$this->data->$theight=$maxHeight;
if(!isset($this->data->$type))$this->data->$type='jpg';
if(!isset($this->data->$thumbnail))$this->data->$thumbnail=1;
if(!isset($this->data->$storage))$this->data->$storage=0;
}else{
$path='path';
$name='name';
$type='type';
$storage='storage';
$thumbnail='thumbnail';
$mwidth='width';
$twidth='twidth';
$mheight='height';
$theight='theight';
$secureLocation='secure';
}
  if((empty($this->data->$path) && $this->data->$type !='url') || empty($this->data->$name)){
if($fileID !=$this->modelID){
$path='path';
$name='name';
$type='type';
$thumbnail='thumbnail';
$storage='storage';
$mwidth='width';
$twidth='twidth';
$mheight='height';
$theight='theight';
}
if(empty($this->element->dftimg)) return true;
static $defaultImg=array();
if(!isset($defaultImg[$this->element->dftimg])){
$filesHelperC=WClass::get('files.helper');
$defaultImg[$this->element->dftimg]=$filesHelperC->getInfo($this->element->dftimg );
if(empty($defaultImg[$this->element->dftimg])){
$defaultImg[$this->element->dftimg]=false;
$mess=WMessage::get();
$IMAGE=$this->element->dftimg;
$mess->codeE('Could not find the default image "'.$IMAGE.'" in the database');
if($IMAGE=='vendorx'){
$fileM=WModel::get('files','object');
$fileM->setVal('name','vendorx');
$fileM->setVal('path','images|vendors');
$fileM->setVal('type','png');
$fileM->setVal('mime','image/png');
$fileM->setVal('width', 120 );
$fileM->setVal('height', 120 );
$fileM->setVal('twidth', 80 );
$fileM->setVal('theight', 80 );
$fileM->setVal('core', 1 );
$fileM->insertIgnore();
}
return true;
}}$img=$defaultImg[$this->element->dftimg];
}else{
$img=$this->data;
}
if(empty($img)) return '';
if(!in_array( strtolower($img->$type), array('png','jpg','jpeg','gif','tiff'))){
if('url'== $img->$type){
$url=$img->$name;
}else{
$filesMediaC=WClass::get('files.media');
$url=$filesMediaC->iconImages($img->$type );}
$imageDataO=WPage::newBluePrint('image');
$imageDataO->location=$url;
$imageDataO->text=$this->name;
$imageDataO->width=30;
$imageDataO->height=30;
$imageDataO->id='pic'.$this->name . $this->line;
$this->content=WPage::renderBluePrint('image',$imageDataO );
return true;
}
if(isset($this->element->thumb)){
if( WRoles::isAdmin('manager')){
$width=$img->$twidth;
$heigth=$img->$theight;
if($width > $maxWidth){
$rate=$width / $maxWidth;
$width=$maxWidth;
$heigth=$heigth / $rate;
}if($heigth > $maxHeight){
$rate=$heigth / $maxHeight;
$heigth=$maxHeight;
$width=$width / $rate;
}}else{
$width=$img->$twidth;
$heigth=$img->$theight;
}
}else{
$width=$img->$mwidth;
$heigth=$img->$mheight;
}
if($width > $maxWidth || $heigth > $maxHeight){
$width=$maxWidth;
$heigth=$maxHeight;
}
$myNewImageO=WObject::get('files.file');
$myNewImageO->name=$img->$name;
$myNewImageO->type=$img->$type;
$myNewImageO->secure=(!empty($img->$secureLocation)?$img->$secureLocation : 0 );
$myNewImageO->basePath=(!empty($img->$secureLocation)?JOOBI_URL_SAFE : JOOBI_URL_MEDIA );
$myNewImageO->folder=(empty($img->folder)?(!empty($img->$secureLocation)?'safe' : 'media') : $img->folder );
$myNewImageO->path=$img->$path;
$myNewImageO->fileID=$this->value;
$myNewImageO->thumbnail=((isset($this->element->thumb) && $img->$thumbnail )?true : false);
if(!empty($img->$storage))$myNewImageO->storage=$img->$storage;
$url=$myNewImageO->fileURL(true);
$contentImg='';
if(empty($this->element->indentationDone)){
if(!empty($this->data->indentTreeNumber ) && !empty($this->element->treeindent )){
if(!isset($indentWidth))$indentWidth=$width;
$indentationWidth=$indentWidth * ($this->data->indentTreeNumber - 1 );
if(!empty($indentationWidth))$contentImg .='<div style="width:'.$indentationWidth.'px;float:left;">&nbsp;</div>';
$contentImg .='<div style="float:left;">';
$this->element->indentationDone=true;
}else{
$depth=$this->getValue('depth');
if(!empty($depth)){
$closeDiv=true;
if(!isset($indentWidth))$indentWidth=$width;
$indentationWidth=$indentWidth * ($depth - 1 );
if(!empty($indentationWidth))$contentImg .='<div style="width:'.$indentationWidth.'px;float:left;">&nbsp;</div>';
$contentImg .='<div style="float:left;">';
$this->element->indentationDone=true;
}}}
if($width < 10 || $heigth < 10){
$width=30;
$heigth=30;
}
$imageDataO=WPage::newBluePrint('image');
$imageDataO->location=$url;
$imageDataO->text=$img->$name;
$imageDataO->width=$width;
$imageDataO->height=$heigth;
$imageDataO->id='pic'.$this->name . $this->line;
$contentImg .=WPage::renderBluePrint('image',$imageDataO );
if(!empty($closeDiv) || (!empty($this->data->indentTreeNumber ) && !empty($this->element->treeindent ))){
$contentImg .='</div>';
}
$class=(isset($this->element->classes ))?$this->element->classes : 'thumb';
if(empty($this->element->lien) && empty($this->element->imgfull)){
$this->content=$contentImg;
}elseif(empty($this->element->lien) && !empty($this->element->imgfull)){
$myNewImageO->thumbnail=false;
$mainUrl=$myNewImageO->fileURL();
WPage::addJSLibrary('joobibox');
$this->content=WPage::createPopUpLink($mainUrl, $contentImg, $img->$mwidth, $img->$mheight, $class, 'ln'.$this->name.$this->line );
}else{
$this->content=$contentImg;
}
if(isset($this->element->pname)){
$this->content .='<br /><small>'.$img->$name.'.'.$img->$type.'</small>';
}
return true;
}
public function advanceSearch(){
return false;
}
public function searchQuery(&$model,$element,$searchedTerms=null,$operator=null){
}
}