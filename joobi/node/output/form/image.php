<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('form.file');
class WForm_Coreimage extends WForm_file {
var $resizedWidth=0;
var $resizedHeight=0;
function show(){
$fileID=$this->value;
static $imagesArchive=array();
if(empty($this->value)){
if(isset($this->element->dftimg)){
if(!empty($imagesArchive[$this->element->dftimg])){
$img=$imagesArchive[$this->element->dftimg];
}else{
$filesHelperC=WClass::get('files.helper');
$img=$filesHelperC->getInfo($this->element->dftimg );
$imagesArchive[$this->element->dftimg]=$img;
}
}else{
$this->content='';
}
}elseif(!empty($imagesArchive[$this->value])){
$img=$imagesArchive[$this->value];
}else{
$filesHelperC=WClass::get('files.helper');
$img=$filesHelperC->getInfo($this->value );
$imagesArchive[$this->value]=$img;
}
if(isset($img)){
$useThumnailsPath=false;
if(isset($this->element->thumb) && $img->thumbnail){
$useThumnailsPath=true;
$width=$img->twidth;
$heigth=$img->theight;
}else{
$width=$img->width;
$heigth=$img->height;
}
if(!empty($this->resizedWidth) && !empty($this->resizedWidth))$this->_setImageSize($width, $heigth );
$myNewImageO=WObject::get('files.file');
$myNewImageO->name=$img->name;
$myNewImageO->type=$img->type;
$myNewImageO->basePath=JOOBI_URL_MEDIA;
$myNewImageO->path=$img->path;
$myNewImageO->folder=$img->folder;
$myNewImageO->fileID=$fileID;
$myNewImageO->thumbnail=$useThumnailsPath;
if(!empty($img->storage))$myNewImageO->storage=$img->storage;
$thumbnail_url=$myNewImageO->fileURL(true);
WGlobals::set('imageURL',$thumbnail_url );
$imageDataO=WPage::newBluePrint('image');
$imageDataO->location=$thumbnail_url;
$imageDataO->text=$img->name;
$imageDataO->width=$width;
$imageDataO->height=$heigth;
$imageDataO->id=$this->element->namekey;
$contentImg=WPage::renderBluePrint('image',$imageDataO );
$class=(isset($this->element->classes ))?$this->element->classes : 'a';
if(( !isset($this->element->link) && !isset($this->element->imgfull)) || JOOBI_FRAMEWORK_TYPE=='mobile'){
$this->content=$contentImg.'<br />';
}elseif(!isset($this->element->link) && isset($this->element->imgfull)){
WPage::addJSLibrary('joobibox');
$myNewImageO->thumbnail=false;
$url=$myNewImageO->fileURL();
$this->content .=WPage::createPopUpLink($url, $contentImg, ($img->width*1.15), ($img->height*1.15), $class, $this->idLabel );
}else{
$this->content='<a id="'.$this->idLabel.'" class="'.$class.'" href="'.WPage::routeURL($this->element->link ). '">';
$this->content .=$contentImg.'</a><br />';
}}
return true;
}
private function _setImageSize(&$originalWidth,&$originalHeight){
$image=new stdClass;
$image->imageWidth=$originalWidth;
$image->imageHeight=$originalHeight;
$width=$this->resizedWidth;
$height=$this->resizedHeight;
$objSize=new stdClass;
$objSize->width=$width;
$objSize->height=$height;
$imageSize=$objSize;
$width=$imageSize->width;
$height=$imageSize->height;
$image->imageWidth=(!empty($originalWidth)?$originalWidth : $width );
$image->imageHeight=(!empty($originalHeight)?$originalHeight : $height );
$php5=( defined('PHP_ROUND_HALF_DOWN')?true : false);
if($image->imageHeight > $height){
$ratio=$height / $image->imageHeight;
$image->imageWidth=$php5?round($image->imageWidth * $ratio, 0, PHP_ROUND_HALF_DOWN ) : round($image->imageWidth * $ratio, 0 );
$image->imageHeight=$height;
}
if($image->imageWidth > $width){
$ratio=$width / $image->imageWidth;
$image->imageHeight=$php5?round($image->imageHeight * $ratio, 0, PHP_ROUND_HALF_DOWN ) : round($image->imageHeight * $ratio, 0 );
$image->imageWidth=$width;
}
$originalWidth=$image->imageWidth;
$originalHeight=$image->imageHeight;
return;
}
}