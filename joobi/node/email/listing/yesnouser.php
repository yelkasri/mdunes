<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_CoreYesnouser_listing extends WListings_default{
function create(){
static $addLegend=array();
$aValue=array( 0, 1, 2 );
$valueTo=array(2,0,1);
$aLabel=array( WText::t('1206732372QTKJ'), WText::t('1231383373PREE'),WText::t('1206732372QTKI'));
$aImg=array('cancel','preferences','yes');
$this->valueTo=$valueTo[$this->value];
$script=$this->elementJS();
if(isset($this->element->style))$style='style="'. $this->element->style .'" ';
else $style='';
$image=WView::getLegend($aImg[$this->value], $aLabel[$this->value], 'standard');
if(!isset($this->element->infonly) && !isset($this->element->lien)){
$this->content='<a href="#" onclick="'.$script.'" title="'.$aLabel[$this->value].'">';
$this->content .=$image;
$this->content .='</a>';
}else{
$this->content=$image;
}
return true;
}
}