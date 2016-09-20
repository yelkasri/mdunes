<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WRender_Breadcrumb_blueprint extends Theme_Render_class {
  public function render($data){
  $listA=$data->listA;
  if(empty($listA)) return '';
  if('cart'==$data->type){
  $navigationUsecmsBreadcrumb=$this->value('navigationbrdcrumbcartusecms');
  }else{
  $navigationUsecmsBreadcrumb=$this->value('navigationbrdcrumbcatalogusecms');
  }
  if($navigationUsecmsBreadcrumb){
$app=JFactory::getApplication();
$pathway=$app->getPathWay();
foreach($listA as $oneItem){
if(!empty($oneItem->link)){
$oneItem->link='#';
}
if(!empty($oneItem->stretch)){
$oneItem->name='&nbsp;&nbsp;&nbsp;&nbsp;'.$oneItem->name.'&nbsp;&nbsp;&nbsp;&nbsp;';
}
$pathway->addItem($oneItem->name, $oneItem->link );
}
return '';
  }else{
  if('cart'==$data->type){
$html='<div class="wizard basketTrail">';
  $count=count($listA);
  $width=round( 95 / $count );
foreach($listA as $key=> $oneE){
$html .='<a';
if(!empty($oneE->current ))$html .=' class="current"';
if(!empty($oneE->link ))$html .=' href="'.$oneE->link.'"';
$html .=' style="width:'.$width. '%;"';
$html .='>';
if(!empty($oneE->showNumber )) '<span class="badge">'.$key.'</span>';
$html .='<span class="trail">'.$oneE->name.'</span></a>';
}
$html .='</div>';
  }else{
  $html='<div class="breadcrumb clearfix"><ol class="breadcrumb">';
  $count=count($listA);
  $width=round( 95 / $count );
foreach($listA as $key=> $oneE){
$html .='<li';
if(!empty($oneE->current ))$html .=' class="active"';
$html .='>';
$html .='<a';
if(!empty($oneE->link ))$html .=' href="'.$oneE->link.'"';
$html .='>';
if(!empty($oneE->showNumber )) '<span class="badge">'.$key.'</span>';
$html .='<span class="trail">'.$oneE->name.'</span></a>';
$html .='</li>';
}
$html .='</ol></div>';
  }
return $html;
  }
  }
}
