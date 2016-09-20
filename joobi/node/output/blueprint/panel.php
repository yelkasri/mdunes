<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WRender_Panel_classData {
public $type='panel';
public $id='panelID';
public $class='';
public $color='';
public $faicon='';
public $header='';
public $footer='';
public $body='';
public $headerRightA=array();
public $headerCenterA=array();
public $bottomRightA=array();
public $bottomCenterA=array();
public $style='';
}
class WRender_Panel_blueprint extends Theme_Render_class {
private static $_paneIcon=null;
private static $_paneColor=null;
private static $_isRTL=null;
public function render($data){
if(!isset( self::$_paneIcon )){
self::$_paneIcon=$this->value('pane.icon');
self::$_paneColor=$this->value('pane.color');
self::$_isRTL=WPage::isRTL();
}
$panelName=(!empty($data->type)?$data->type : 'panel');
$html='<div';
if(!empty($data->id)){
if('panelID'==$data->id){
static $count=1;
$data->id .=$count;
$count++;
}
$html .=' id="'.$data->id.'"';
}
$html .=' class="'.$panelName;
if( self::$_paneIcon && !empty($data->color))$html .=' panel-'.$data->color;
else $html .=' panel-default';
if(!empty($data->class))$html .=' '.$data->class;
$html .='">';
if(!empty($data->header) || !empty($data->headerRightA ) || !empty($data->headerCenterA )){
if(empty($data->headerRightA ) && empty($data->headerCenterA )){
$html .='<div class="'.$panelName.'-heading">';
if( self::$_paneIcon && !empty($data->faicon))$html .='<i class="fa '.$data->faicon.'"></i>';
$html .='<h4 class="panel-title">'.$data->header.'</h4>';
$html .='</div>';
}else{
$html .='<div class="'.$panelName.'-heading clearfix">';
if(!self::$_isRTL){
$left='left';
$right='right';
}else{
$left='right';
$right='left';
}
$verticalAlign=(!empty($data->headerRightA) || !empty($data->headerCenterA)?' headVertical' : '');
$html .='<div class="'.$panelName.'-title pull-'.$left . $verticalAlign.'">';
if( self::$_paneIcon && !empty($data->faicon))$html .='<i class="fa '.$data->faicon.'"></i>';
$html .='<h4 class="panel-title">'.$data->header.'</h4>';
$html .='</div>';
if(!empty($data->headerRightA)){
$html .='<div class="pull-'.$right.'">';
$html .='<div class="headWrap">';
$html .=implode('</div><div class="headWrap">',$data->headerRightA );
$html .='</div>';
$html .='</div>';
}
if(!empty($data->headerCenterA)){
$html .='<div class="pull-'.$right.'">';
$html .='<div class="headWrap">';
$html .=implode('</div><div class="headWrap">',$data->headerCenterA );
$html .='</div>';
$html .='</div>';
}
$html .='</div>';
}
}
$html .='<div class="'.$panelName.'-body">';
$html .=$data->body;
$html .='</div>';
if(!empty($data->footer) || !empty($data->bottomRightA ) || !empty($data->bottomCenterA )){
if(empty($data->bottomRightA ) && empty($data->bottomCenterA )){
$html .='<div class="'.$panelName.'-footer">';
$html .=$data->footer;
}else{
$html .='<div class="'.$panelName.'-footer clearfix">';
if(!self::$_isRTL){
$left='left';
$right='right';
}else{
$left='right';
$right='left';
}
if(!empty($data->footer)){
$html .='<div class="pull-'.$left.'">';
$html .=$html .=$data->footer;
$html .='</div>';
}
if(!empty($data->bottomRightA)){
$html .='<div class="pull-'.$right.'">';
$html .='<div class="BottomWrap">';
$html .=implode('</div><div class="BottomWrap">',$data->bottomRightA );
$html .='</div>';
$html .='</div>';
}
if(!empty($data->bottomCenterA)){
$html .='<div class="pull-'.$right.'">';
$html .='<div class="BottomWrap">';
$html .=implode('</div><div class="BottomWrap">',$data->bottomCenterA );
$html .='</div>';
$html .='</div>';
}
}
$html .='</div>';
}
$html .='</div>';
return $html;
}
}