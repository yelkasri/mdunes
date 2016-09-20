<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_CorePublish_listing extends WListings_default{
function create(){
static $addLegend=array();
if(isset($this->value) && $this->value>0){
$class ='yes';
$nameTag=WText::t('1206732372QTKI') ;
$order=11;
$this->valueTo='-1';
}elseif(isset($this->value) && $this->value<0){
$class ='cancel';
$nameTag=WText::t('1206732372QTKJ') ;
$order=12;
$this->valueTo='1';
}else{
$this->content='<a href="'.WPage::routeURL('controller=translation&task=install&lgid='. $this->getValue('lgid')).'">'.WText::t('1260151225DTCK').'</a>';
return true;
}
$myImage=WView::getLegend($class, $nameTag, 'publish',  $order+10 );
$script=$this->elementJS();
if(isset($this->element->style))$style='style="'. $this->element->style .'" ';
else $style='';
if(!isset($this->element->infonly) && !isset($this->element->lien)){
$this->content='<a href="#" onclick="'.$script.'" title="'.  $nameTag.WText::t('1206732372QTKR').'">';
$this->content .=$myImage;
$this->content .='</a>';
}else{
$this->content=$myImage;
}
return true;
}
}