<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WRender_Legend_blueprint extends Theme_Render_class {
private static $_instance=array('publish'=>array(), 'standard'=>array(), 'notice'=>array());
private static $_instanceFalseAddCSS=array('publish'=>array(), 'standard'=>array(), 'notice'=>array());
private $_columniconcolor=true;
  public function render($data){
  if( is_string($data )){
  if('createLegend'==$data){
  $legendRemove=$this->value('legend.remove');
  if(!empty($legendRemove)) return '';
  $this->_columniconcolor=$this->value('table.columniconcolor');
  return $this->_createLegend();
  }else{
  return $this->_renderImage($data );
  }
  }else{
  if(!empty($data->createListingIcon )){
 $this->_columniconcolor=true;
 return "fa " . WPage::renderBluePrint('icon',$data->action ). " fa-lg";  }elseif(!empty($data->sortUpDown )){
  switch($data->action){
  case 'upGreen':
  return '<i class="fa fa-sort-asc text-success fa-lg"></i>';
  case 'upGray':
  return '<i class="fa fa-sort-asc text-gray fa-lg"></i>';
  case 'downGreen':
  return '<i class="fa fa-sort-desc text-success fa-lg"></i>';
  case 'downGray':
  return '<i class="fa fa-sort-desc text-gray fa-lg"></i>';
  case 'orderBy':
  if($data->direction=='desc'){
  return '<i class="fa fa-sort-amount-desc fa-lg"></i>';
  }else{
  return '<i class="fa fa-sort-amount-asc fa-lg"></i>';
  }  case 'saveOrder':
  return '<i class="fa fa-floppy-o fa-lg"></i>';
  default:
  return '';
  }  }
  if(!isset($data->class))$data->class='';
  if(!isset($data->group))$data->group='standard';
  if(!isset($data->order))$data->order=99;
  if(!isset($data->ID))$data->ID='';
  if(empty($data->text)) return '';  return $this->_storeLegend($data->image, $data->text, $data->group, $data->order, $data->class, $data->ID );
  }
  }
private function _storeLegend($image='',$text='',$group='standard',$order=99,$class='',$ID=''){
$group=strtolower($group);
$image=strtolower($image);
if(!isset(self::$_instanceFalseAddCSS[$group][$order][$image] )){
$ObjectT=new stdClass;
$ObjectT->text=$text;
self::$_instanceFalseAddCSS[$group][$order][$image]=$ObjectT;
}
return $this->_renderImage($image, $ID );
}
private function _renderImage($image,$ID=''){
$html='<i';
if(!empty($ID))$html .=' id="'.$ID.'"';
$html .=' class="fa '.WPage::renderBluePrint('icon',$image ). ' fa-lg">';
$html .='</i>';
return $html;
}
 private function _createLegend(){
$legendsA=$this->_storeLegend();
if(!empty(self::$_instanceFalseAddCSS)){
foreach( self::$_instanceFalseAddCSS as $key=> $item){
if(!empty($item)){
foreach($item as $key2=> $item2){
if(!empty($item2)){
foreach($item2 as $key3=> $item3){
if(!isset( self::$_instance[$key][$key2][$key3] )){
self::$_instance[$key][$key2][$key3]=self::$_instanceFalseAddCSS[$key][$key2][$key3];
}}}}}}}$legendsA=self::$_instance;
if(empty($legendsA ) || ! is_array($legendsA)){
return '';
}
$HTMLs=array();
$icons=false;
$notices=false;
foreach($legendsA as $group=> $orders){
if(!empty($orders) && is_array($orders)){
ksort($orders);
foreach($orders as $order=> $legend){
foreach($legend as $image=> $text){
if(empty($text->text)) continue;
if($image !='/'){
$img='<span class="legend-text"><i class="fa '.WPage::renderBluePrint('icon',$image ). ' fa-lg"></i>'.$text->text.'</span>';
$HTMLs[]=$img;
$icons=true;
}else{
$HTMLnotices[]=$text;
$notices=true;
}}}}}
$html='';
if($notices){
$img='<span class="legend-text"><i class="fa fa-lightbulb-o fa-lg"></i></span>';
$html .=$img . WText::t('1213285196AFGY'). ' : '.implode(' | ' , $HTMLnotices);
}
if($icons)$html .=WText::t('1206732369EREW'). ' : '.implode(' | ' , $HTMLs );
$div=new WDiv($html );
$div->classes='legend' ;
return $div->make();
}
}