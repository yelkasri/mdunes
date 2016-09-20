<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WRender_Tooltips_classData {
public $tooltips='';
public $text='';
public $title='';
public $id='';
public $class='';
}
class WRender_Tooltips_blueprint extends Theme_Render_class {
protected static $_method=null;
protected static $_placement='';
protected static $_html=true;
protected static $_trigger='';
protected static $_location='';
  public function render($data){
  if(empty($data->tooltips)) return '';
  static $count=0;
  if(!isset(self::$_method)){
self::$_method=$this->value('tooltip.method');
self::$_placement=$this->value('tooltip.placement');
self::$_html=$this->value('tooltip.html');
self::$_trigger=$this->value('tooltip.trigger');
if( self::$_trigger=='hover focus' && self::$_method=='tooltip'){
self::$_trigger='';
}elseif( self::$_trigger=='click' && self::$_method=='popover'){
self::$_trigger='';
}
self::$_location=$this->value('tooltip.location');
if( JOOBI_FRAMEWORK=='joomla30') JHtml::_('bootstrap.tooltip');
  }
$data->tooltips=htmlspecialchars($data->tooltips );
if(!empty($data->title))$data->title=WGlobals::filter($data->title, 'string');
if(!empty($data->id)){
$data->id='c'.$data->id;
}else{
$count++;
$data->id=WView::generateID('label',$count );
}
  $html='<label';
  if(!empty($data->id))$html .=' id="'.$data->id.'"';
  $html .=' class="hasTooltip';
  if(!empty($data->class))$html .=' '.$data->class;
  $html .='"';
  $html .=' data-toggle="'.self::$_method.'"';
  if(!empty(self::$_placement))$html .=' data-placement="'.self::$_placement.'"';
   if(!empty(self::$_html)){
  $html .=' data-html="true"';
  }
  if(!empty(self::$_trigger))$html .=' data-trigger="'.self::$_trigger.'"';
  if('popover'==self::$_method){
  if(!empty($data->title))$html .=' title="'.$data->title.'"';
  $html .=' content="'.$data->tooltips.'"';
  }else{
  if(!empty($data->id))$html .=' for="'.$data->id.'"';
  $html .=' title="'.$data->tooltips.'"';
  }
  $html .='>';
  $html .=$data->text;
  $html .='</label>'.WGet::$rLine;
  return $html;
  }
}
