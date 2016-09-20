<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WRender_Image_classData {
public $location='';
public $border=0;
public $text='';
public $width=0;
public $height=0;
public $id=null;
public $name=null;
public $align=null;
public $style=null;
public $class=null;
}
class WRender_Image_blueprint extends Theme_Render_class {
private static $_imageStyle=null;
private static $_imageResponsive=null;
  public function render($data){
  if(empty($data->location)){
  $this->codeE('Image location not specified!');
  return '';
  }
  if(!isset( self::$_imageStyle )){
  self::$_imageStyle=$this->value('image.style');
  self::$_imageResponsive=$this->value('image.responsive');
  }
  if(!empty($data->class )){
  $newClass=$data->class.' ';
  }else{
  $newClass='';
  }
  if( self::$_imageResponsive)$newClass .='img-responsive ';
  if(!empty(self::$_imageStyle))$newClass .='img-'.self::$_imageStyle;
  $newClass=trim($newClass );
  $html='<img src="'.$data->location.'" border="'.$data->border.'"';
  if(!empty($data->text ))$html .=' alt="'.WGlobals::filter($data->text, 'string'). '"';
  if(!empty($newClass)){
  $html .=' class="'.$newClass.'"';
  }else{
  $html .='';
  }
  if(!empty($data->id )){
  $html .=' id="'.$data->id.'"';
  }
  if(!empty($data->name )){
  $html .=' name="'.$data->name.'"';
  }
  if(!empty($data->align )){
  $html .=' align="'.$data->align.'"';
  }
  if(!empty($data->width) || !empty($data->height)){
  $style='';
  if(!empty($data->width))$style .='width:'.$data->width.'px;';
  if(!empty($data->height))$style .='height:'.$data->height.'px;';
  if(!empty($data->style))$style .=$data->style;
  if(!empty($style))$html .=' style="'.$style.'"';
  }
  $html .='/>';
return $html;
  }
}