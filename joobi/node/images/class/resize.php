<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Images_Resize_class extends WClasses{
public $source=null;
public $height=0;
public $width=0;
public $mime=null;
public $max_width=200;
public $max_height=200;
public $thumb_width=50;
public $thumb_height=50;
public $dest='';
public $type='';
public $size=null;
public function optimzeImg($path,$type=''){
$this->_getProp(true);
if(empty($type)){
$type=$this->_getImgType($path );
}
$type=strtolower($type);
switch ($type){
case 'gif':
$img_src=@imagecreatefromgif($path );
break;
case 'jpg':
case 'jpeg':
$img_src=@imagecreatefromjpeg($path );
break;
case 'png':
$img_src=@imagecreatefrompng($path );
break;
default:
WMessage::log('Image type not defined: '.$type , 'ERROR-image-optimization');
return false;
break;
}
if(!is_resource($img_src)){
$this->codeE('The image is not a ressource!?..');
WMessage::log('HACK : The image '.$img_src.' is not a ressource!?..','image-optimization-HACK');
return false;
}
if(!is_writable( dirname($path))){
@chmod( dirname($path), '755');
if(!is_writable( dirname($path))){
$message=WMessage::get();
$FOLDER=dirname($path);
$message->adminE('The image folder is not writeable: '.$FOLDER );
WMessage::log('HACK : The image folder is not writeable : '.$FOLDER, 'image-optimization-HACK');
return false;
}}
$this->source=$path;
$width=$this->_getImgWidth();
$height=$this->_getImgHeight();
$img_des=imagecreatetruecolor((int)$width, (int)$height );
$dest=$path;
switch ($type){
case 'gif':
imagefill($img_des, 0, 0, IMG_COLOR_TRANSPARENT );
imagealphablending($img_des, false);
imagesavealpha($img_des, true);
$result=@imageCopyResized($img_des, $img_src, 0, 0, 0, 0, $width, $height, $width, $height );
return (( imagegif($img_des, $dest )==true)?true: false);
break;
case 'jpg':
case 'jpeg':
$result=@imagecopyresampled($img_des, $img_src, 0, 0, 0, 0, $width, $height, $width, $height );
return (( imagejpeg($img_des, $dest )==true)?true: false);break;
case 'png':
imagealphablending($img_des, false);
imagesavealpha($img_des, true);
$result=@imagecopyresampled($img_des, $img_src, 0, 0, 0, 0, $width, $height, $width, $height );
return (( imagepng($img_des, $dest, 9 )==true)?true: false);
break;
default:
WMessage::log('Image type not defined #2: '.$type , 'ERROR-image-optimization');
return false;
break;
}
return false;
}
public function resizeImageDimentions(&$width,&$height,$maxWidth=30,$maxHeight=30){
$this->max_width=$maxWidth;
$this->max_height=$maxHeight;
$this->calcImageSize($width, $height );
$width=$this->thumb_width;
$height=$this->thumb_height;
}
public function resizeImage(){
$this->_getProp(true);
switch ( strtolower($this->type)){
case 'gif':
$img_src=@imagecreatefromgif($this->source );
break;
case 'jpg':
case 'jpeg':
$img_src=@imagecreatefromjpeg($this->source );
break;
case 'png':
$img_src=@imagecreatefrompng($this->source );
break;
default:
return false;
break;
}
if(!is_resource($img_src)){
$this->codeE('The image is not a ressource!?..');
WMessage::log('HACK : The image '.$img_src.' is not a ressource!?..','image-hack');
return false;
}
if(!is_writable( dirname($this->dest))){
@chmod( dirname($this->dest), '755');
if(!is_writable( dirname($this->dest))){
$message=WMessage::get();
$FOLDER=dirname($this->dest);
$message->userW('1212843259ITHW',array('$FOLDER'=>$FOLDER));
return false;
}}
$this->calcImageSize($this->_getImgWidth(), $this->_getImgHeight());
if(empty($this->thumb_width) || $this->thumb_width < 1)$this->thumb_width=50;
if(empty($this->thumb_height) || $this->thumb_height < 1)$this->thumb_height=50;
$img_des=imagecreatetruecolor((int)$this->thumb_width, (int)$this->thumb_height );
if( strtolower($this->type)=='png'){ imagealphablending($img_des, false);
imagealphablending($img_des, false);
imagesavealpha($img_des,true);
$transparent=imagecolorallocatealpha($img_des, 255, 255, 255, 127 );
imagefilledrectangle($img_des, 0, 0, $this->thumb_width, $this->thumb_height, $transparent);
}elseif( strtolower($this->type)=='gif'){ $whitebg=imagecolorallocate ($img_des, 255, 255, 255 );
imagefilledrectangle($img_des, 0, 0, $this->thumb_width, $this->thumb_height, $whitebg );
}
switch ( strtolower($this->type)){
case 'gif':
imagefill($img_des, 0, 0, IMG_COLOR_TRANSPARENT );
imagealphablending($img_des, false);
imagesavealpha($img_des, true);
$result=@imageCopyResized($img_des, $img_src, 0, 0, 0, 0, $this->thumb_width, $this->thumb_height, $this->_getImgWidth(), $this->_getImgHeight());
return (( imagegif($img_des, $this->dest )==true)?true: false);
break;
case 'jpg':
case 'jpeg':
$result=@imagecopyresampled($img_des, $img_src, 0, 0, 0, 0, $this->thumb_width, $this->thumb_height, $this->_getImgWidth(), $this->_getImgHeight());
return (( imagejpeg($img_des, $this->dest )==true)?true: false);
break;
case 'png':
imagealphablending($img_des, false);
imagesavealpha($img_des, true);
$result=@imagecopyresampled($img_des, $img_src, 0, 0, 0, 0, $this->thumb_width, $this->thumb_height, $this->_getImgWidth(), $this->_getImgHeight());
return (( imagepng($img_des, $this->dest, 9 )==true)?true: false);
break;
default:
return false;
break;
}
switch ($type){
case 'gif':
imagefill($img_des, 0, 0, IMG_COLOR_TRANSPARENT );
imagealphablending($img_des, false);
imagesavealpha($img_des, true);
$result=@imageCopyResized($img_des, $img_src, 0, 0, 0, 0, $width, $height, $width, $height );
return (( imagegif($img_des, $dest )==true)?true: false);
break;
case 'jpg':
case 'jpeg':
$result=@imagecopyresampled($img_des, $img_src, 0, 0, 0, 0, $width, $height, $width, $height );
return (( imagejpeg($img_des, $dest )==true)?true: false);break;
case 'png':
imagealphablending($img_des, false);
imagesavealpha($img_des, true);
$result=@imagecopyresampled($img_des, $img_src, 0, 0, 0, 0, $width, $height, $width, $height );
return (( imagepng($img_des, $dest, 9 )==true)?true: false);
break;
default:
WMessage::log('Image type not defined #2: '.$type , 'ERROR-image-optimization');
return false;
break;
}
return false;
 }
 private function _getImgType($path,$reset=false){
 $this->source=$path;
 $this->_getProp($reset);
 if(empty($this->mime)) return false;
 return substr($this->mime, 6 );
 } 
private function _getImgWidth($reset=false){
$this->_getProp($reset);
return $this->width;
}
private function _getImgHeight($reset=false){
$this->_getProp($reset);
return $this->height;
}
private function _getProp($reset=false){
static $dimensionA=null;
if($reset)$dimensionA=array();
if(empty($dimensionA)){
$dimensionA=@getimagesize($this->source);
if(empty($dimensionA)) return false;
$this->width=$dimensionA[0];
$this->height=$dimensionA[1];
$this->mime=$dimensionA['mime'];
}
}
private function resizeHeight($width,$height,$ratio=0){
if($ratio==0)$ratio=$height / $this->max_height;
$newHeight=$height / $ratio;
return array('ratio'=>$ratio ,'newHeight'=>intval($newHeight));}
private function resizeWidth($width,$height,$ratio=0){
if($ratio==0)$ratio=$width / $this->max_width;
$newWidth=$width / $ratio;
return array('newWidth'=>intval($newWidth),'ratio'=>$ratio );}
private function calcImageSize($width,$height){
if($this->max_width < 1 || $this->max_height < 1 ) return false;
  if($width > $this->max_width){
$newWidthObj=$this->resizeWidth($width, $height );
$width=$newWidthObj['newWidth'];
$newHeightObj=$this->resizeHeight($width, $height, $newWidthObj['ratio'] );
$height=$newHeightObj['newHeight'];
}
  if($height > $this->max_height){
$newHeightObj=$this->resizeHeight($width, $height );
$height=$newHeightObj['newHeight'];
$newWidthObj=$this->resizeWidth($width, $height, $newHeightObj['ratio'] );
$width=$newWidthObj['newWidth'];
}
$this->thumb_width=$width;
$this->thumb_height=$height;
}
}