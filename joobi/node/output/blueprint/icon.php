<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WRender_Icon_classData {
public $location='';
public $icon='';
public $text='';
public $size='';
public $color='';
public $animation='';
}
class WRender_Icon_blueprint extends Theme_Render_class {
private static $_columniconcolor=null;
private static $_starcolor=null;
private static $_starsize=null;
private static $_count=0;
private $_data=null;
  public function render($data){
  if(empty($data)) return '';
  $this->_data=$data;
  if(!isset( self::$_columniconcolor )){
  self::$_columniconcolor=$this->value('table.columniconcolor');
  self::$_starcolor=$this->value('catalog.starcolor');
  self::$_starsize=$this->value('catalog.starsize');
  }
  if( is_string($this->_data)){
  return $this->_getIcon($this->_data );
  }
  if(empty($this->_data->icon)){
  $this->codeE('Icon not specified!');
  return '';
  }
    $html='<i class="fa '.$this->_getIcon($this->_data->icon );
  if(!empty($data->animation)){
  $html .=' '.$data->animation;
  }
  if(!empty($this->_data->size)){
  switch($this->_data->size){
  case 'medium':
  break;
  case 'large':
  $html .=' fa-lg';
  break;
  case 'xlarge':
  $html .=' fa-2x';
  break;
  case 'xxlarge':
  case '2xlarge':
  $html .=' fa-3x';
  break;
  case '3xlarge':
  $html .=' fa-4x';
  break;
  case '4xlarge':
  $html .=' fa-5x';
  break;
  default:
  break;
  }  }  $html .='"';
  if(!empty($this->_data->text)){
  $html .=' rel="tooltip" title="'.WGlobals::filter($this->_data->text, 'string'). '"';
  if(empty($this->_data->id )){
  self::$_count++;
  $this->_data->id='gmi'.self::$_count;
  }  }
  if(!empty($this->_data->id )){
  $html .=' id="'.$this->_data->id.'"';
  }
  $html .='></i>';
return $html;
  }
private function _getIcon($icon){
switch($icon){
case 'publish':
$html='fa-power-off';
if( self::$_columniconcolor)$html .=' text-success';
break;
case 'unpublish':
$html='fa-times-circle';
if( self::$_columniconcolor)$html .=' text-danger';
break;
case 'yes':
$html='fa-check ';
if( self::$_columniconcolor)$html .=' text-success';
break;
case 'no':
$html='fa-times';
if( self::$_columniconcolor)$html .=' text-success';
break;
case 'edit':
$html='fa-pencil-square-o';
if( self::$_columniconcolor)$html .=' text-primary';
break;
case 'show':
$html='fa-eye';
if( self::$_columniconcolor)$html .=' text-info';
break;
case 'delete':
$html='fa-trash-o';
if( self::$_columniconcolor)$html .=' text-danger';
break;
case 'copy':
$html='fa-files-o';
if( self::$_columniconcolor)$html .=' text-info';
break;
case 'lock':
$html='fa-lock';
if( self::$_columniconcolor)$html .=' text-success';
break;
case 'unlock':
$html='fa-unlock';
if( self::$_columniconcolor)$html .=' text-warning';
break;
case 'enabled':
$html='fa-unlock';
if( self::$_columniconcolor)$html .=' text-success';
break;
case 'disabled':
$html='fa-lock';
if( self::$_columniconcolor)$html .=' text-danger';
break;
case 'pending':
$html='fa-unlock';
if( self::$_columniconcolor)$html .=' text-warning';
break;
case 'archive':
$html='fa-archive';
if( self::$_columniconcolor)$html .=' text-warning';
break;
case 'unarchive':
$html='fa-folder-open';
if( self::$_columniconcolor)$html .=' text-info';
break;
case 'disabled':
case 'cancel':
$html='fa-times';if( self::$_columniconcolor)$html .=' text-danger';
break;
case 'refresh':
$html='fa-refresh';
if( self::$_columniconcolor)$html .=' text-primary';
break;
case 'preferences':
$html='fa-cog';
if( self::$_columniconcolor)$html .=' text-primary';
break;
case 'fleche':
$html='fa-chevron-right';
if( self::$_columniconcolor)$html .=' text-primary';
break;
case 'down':
$html='fa-arrow-down';
if( self::$_columniconcolor)$html .=' text-warning';
break;
case 'loading':
$html='fa-spinner fa-spin';
if( self::$_columniconcolor)$html .=' text-primary';
break;
case 'attachment':
$html='fa-paperclip';
if( self::$_columniconcolor)$html .=' text-primary';
break;
case 'profile':
$html='fa-user';
if( self::$_columniconcolor)$html .=' text-danger';
break;
case 'lock':
$html='fa-lock';
if( self::$_columniconcolor)$html .=' text-success';
break;
case 'unlock':
$html='fa-unlock';
if( self::$_columniconcolor)$html .=' text-danger';
break;
case 'preview':
$html='fa-eye';
if( self::$_columniconcolor)$html .=' text-info';
break;
case 'followup':
$html='fa-exclamation-circle';
if( self::$_columniconcolor)$html .=' text-warning';
break;
case 'sendmessage':
$html='fa-envelope';
if( self::$_columniconcolor)$html .=' text-success';
break;
case 'dontsendmessage':
$html='fa-times-circle';
if( self::$_columniconcolor)$html .=' text-danger';
break;
case 'push-pin':
$html='fa-thumb-tack';
if( self::$_columniconcolor)$html .=' text-warning';
break;
case 'star-blank':
$html='fa-star-o';
if('primary'==self::$_starcolor)$html .=' text-info';
else $html .=' text-'.self::$_starcolor;
$this->_data->size=self::$_starsize;
break;
case 'star-half':
$html='fa-star-half-o';
$html .=' text-'.self::$_starcolor;
  $this->_data->size=self::$_starsize;
break;
case 'star':
$html='fa-star';
$html .=' text-'.self::$_starcolor;
$this->_data->size=self::$_starsize;
break;
default:
$html=('fa' !=substr($icon, 0, 2 )?'fa-'.$icon : $icon );
if(!empty($this->_data->color)){
$html .=' text-'.$this->_data->color;
}else{
if( self::$_columniconcolor)$html .=' text-primary';
}
break;
}
return $html;
}
}