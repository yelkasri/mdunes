<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Images_Watermark_class extends WClasses {
private $imageInfoO=null;
private $imageWidth=null;
private $imageHeight=null;
public function processWatermark(&$imageInfoO,$imageWidth,$imageHeight){
if(empty($imageInfoO)) return false;
$this->imageInfoO=&$imageInfoO;
$this->imageWidth=$imageWidth;
$this->imageHeight=$imageHeight;
if( WPref::load('PIMAGES_NODE_WATERMARKUSEIMAGE')){
$this->watermarkImage();
}
if( WPref::load('PIMAGES_NODE_WATERMARKUSETEXT')){
$this->_watermarkText();
}
}
private function _watermarkText(){
static $validFont=null;
if(!isset($validFont)){
$fontFile=WPref::load('PIMAGES_NODE_WATERMARKTEXTFONTFILE');
if(empty($fontFile )){
$validFont=JOOBI_DS_MEDIA.'fonts'.DS.'monofont.ttf';
}else{
$waterImgPath=WPref::load('PIMAGES_NODE_WATERMARKTEXTFONTFILE');
$waterImgPath=JOOBI_DS_ROOT . trim( str_replace( array('/','\\'), DS, $waterImgPath ), DS );
$fileC=WGet::file();
if(!$fileC->exist($waterImgPath )){
$message=WMessage::get();
$message->userE('1338404981IUWW');
$validFont=JOOBI_DS_MEDIA.'fonts'.DS.'monofont.ttf';
}else{
$validFont=$waterImgPath;
}}}
if(empty($validFont)) return false;
$imgSRC=$this->_imageCreate($this->imageInfoO->source, $this->imageInfoO->type );
if(empty($imgSRC)) return false;
$statusMerge=$this->_renderTextOnImage($imgSRC, $validFont );
if(!$statusMerge ) return false;
switch ( strtolower($this->imageInfoO->type)){
case 'gif':
return (( imagegif($imgSRC, $this->imageInfoO->source )==true)?true: false);
break;
case 'jpg':
case 'jpeg':
return (( imagejpeg($imgSRC, $this->imageInfoO->source )==true)?true: false);
break;
case 'png':
return (( imagepng($imgSRC, $this->imageInfoO->source, 9 )==true)?true: false);
break;
}
return false;
}
private function watermarkImage(){
static $validImg=null;
static $watermarkImageR=null;
static $watermarkWidth=null;
static $watermarkHeight=null;
if(!isset($validImg)){
$waterImgPath=WPref::load('PIMAGES_NODE_WATERMARKIMAGEFILE');$waterImgPath=JOOBI_DS_ROOT . trim( str_replace( array('/','\\'), DS, $waterImgPath ), DS );
$fileC=WGet::file();
if(!$fileC->exist($waterImgPath )){
$validImg=true;
$message=WMessage::get();
$message->userE('1338338300MXQW');
}else{
$validImg=$waterImgPath;
$explodeMeA=explode('.',$waterImgPath );
$watermarkImageR=$this->_imageCreate($validImg, array_pop($explodeMeA));
if(empty($watermarkImageR))$validImg=true;
$watermarkWidth=imagesx($watermarkImageR );
$watermarkHeight=imagesy($watermarkImageR );
}}
if($validImg===true || empty($validImg)) return false;
$imgSRC=$this->_imageCreate($this->imageInfoO->source, $this->imageInfoO->type, true);
if(empty($imgSRC)) return false;
$this->calculatePosition( WPref::load('PIMAGES_NODE_WATERMARKIMAGEPOSITION'), $watermarkWidth, $watermarkHeight );
$statusMerge=imagecopy($imgSRC, $watermarkImageR, $this->destinationX, $this->destinationY, 0, 0, $watermarkWidth, $watermarkHeight );
if(!$statusMerge ) return false;
switch ( strtolower($this->imageInfoO->type)){
case 'gif':
return (( imagegif($imgSRC, $this->imageInfoO->source )==true)?true: false);
break;
case 'jpg':
case 'jpeg':
return (( imagejpeg($imgSRC, $this->imageInfoO->source )==true)?true: false);
break;
case 'png':
return (( imagepng($imgSRC, $this->imageInfoO->source, 9 )==true)?true: false);
break;
}
return false;
}
private function calculatePosition($position,$watermarkWidth,$watermarkHeight){
$maginX=5;
$maginY=5;
switch($position){
case 1:
$this->destinationX=$maginX;
$this->destinationY=$maginY;
break;
case 2:
$this->destinationX=round(($this->imageWidth / 2) - ($watermarkWidth / 2));
$this->destinationY=$maginY;
break;
case 3:
$this->destinationX=$this->imageWidth - $watermarkWidth - $maginX;
$this->destinationY=$maginY;
break;
case 4:
$this->destinationX=$maginX;
$this->destinationY=round(($this->imageHeight / 2) - ($watermarkHeight / 2));
break;
case 5:
$this->destinationX=round(($this->imageWidth / 2) - ($watermarkWidth / 2));
$this->destinationY=round(($this->imageHeight / 2) - ($watermarkHeight / 2));
break;
case 6:
$this->destinationX=$this->imageWidth - $watermarkWidth - $maginX;
$this->destinationY=round(($this->imageHeight / 2) - ($watermarkHeight / 2));
break;
case 7:
$this->destinationX=$maginX;
$this->destinationY=$this->imageHeight - $watermarkHeight - $maginY;
break;
case 8:
$this->destinationX=round(($this->imageWidth / 2) - ($watermarkWidth / 2));
$this->destinationY=$this->imageHeight - $watermarkHeight - $maginY;
break;
case 9:
default:
$this->destinationX=$this->imageWidth - $watermarkWidth - $maginX;
$this->destinationY=$this->imageHeight - $watermarkHeight - $maginY;
break;
}
}
private function _renderTextOnImage(&$source_gd_image,$font){
$maginX=5;
$text=WPref::load('PIMAGES_NODE_WATERMARKTEXTVALUE');
if(empty($text)) return false;
$size=WPref::load('PIMAGES_NODE_WATERMARKTEXTSIZE');
$color=WPref::load('PIMAGES_NODE_WATERMARKTEXTCOLOR');
$opacity=WPref::load('PIMAGES_NODE_WATERMARKTEXTOPACITY');
$rotation=WPref::load('PIMAGES_NODE_WATERMARKTEXTROTATION');
  $source_width=imagesx($source_gd_image );
$source_height=imagesy($source_gd_image );
$bb=$this->imagettfbbox_fixed($size, $rotation, $font, $text );
$x0=min($bb[ 0 ], $bb[ 2 ], $bb[ 4 ], $bb[ 6 ] ) - $maginX;
$x1=max($bb[ 0 ], $bb[ 2 ], $bb[ 4 ], $bb[ 6 ] ) + $maginX;
$y0=min($bb[ 1 ], $bb[ 3 ], $bb[ 5 ], $bb[ 7 ] ) - $maginX;
$y1=max($bb[ 1 ], $bb[ 3 ], $bb[ 5 ], $bb[ 7 ] ) + $maginX;
$bb_width=abs($x1 - $x0 );
$bb_height=abs($y1 - $y0 );
switch ( WPref::load('PIMAGES_NODE_WATERMARKTEXTPOSITION')){
case 1:
$bpy=-$y0;
$bpx=-$x0;
break;
case 2:
$bpy=-$y0;
$bpx=$source_width / 2 - $bb_width / 2 - $x0;
break;
case 3:
$bpy=-$y0;
$bpx=$source_width - $x1;
break;
case 4:
$bpy=$source_height / 2 - $bb_height / 2 - $y0;
$bpx=-$x0;
break;
case 5:
$bpy=$source_height / 2 - $bb_height / 2 - $y0;
$bpx=$source_width / 2 - $bb_width / 2 - $x0;
break;
case 6:
$bpy=$source_height / 2 - $bb_height / 2 - $y0;
$bpx=$source_width - $x1;
break;
case 7:
$bpy=$source_height - $y1;
$bpx=-$x0;
break;
case 8:
$bpy=$source_height - $y1;
$bpx=$source_width / 2 - $bb_width / 2 - $x0;
break;
case 9;
$bpy=$source_height - $y1;
$bpx=$source_width - $x1;
break;
}
$alpha_color=imagecolorallocatealpha(
$source_gd_image,
hexdec( substr($color, 0, 2 )),
hexdec( substr($color, 2, 2 )),
hexdec( substr($color, 4, 2 )),
127 * ( 100 - $opacity ) / 100
);
return imagettftext($source_gd_image, $size, $rotation, $bpx, $bpy, $alpha_color, $font, $text );
  }
  private function imagettfbbox_fixed($size,$rotation,$font,$text){
$bb=imagettfbbox($size, 0, $font, $text );
$aa=deg2rad($rotation );
$cc=cos($aa );
$ss=sin($aa );
$rr=array();
for($i=0; $i < 7; $i +=2){
$rr[ $i + 0 ]=round($bb[ $i + 0 ] * $cc + $bb[ $i + 1 ] * $ss );
$rr[ $i + 1 ]=round($bb[ $i + 1 ] * $cc - $bb[ $i + 0 ] * $ss );
}
return $rr;
  }
private function _imageCreate($sourcePath,$imageType,$watermark=false){
switch ( strtolower($imageType)){
case 'gif':
$imgSRC=@imagecreatefromgif($sourcePath );
break;
case 'jpg':
case 'jpeg':
$imgSRC=@imagecreatefromjpeg($sourcePath );
break;
case 'png':
$imgSRC=@imagecreatefrompng($sourcePath );
if($watermark ) @imagealphablending($imgSRC, true);
else @imagealphablending($imgSRC, false);
imagesavealpha($imgSRC, true);
break;
default:
return false;
break;
}
return $imgSRC;
}
}