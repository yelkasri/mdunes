<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Output_Doc_customized extends Output_Doc_Document {
private $_tableDetailsO=null;
public function renderContent(){
if(empty($this->customContent)) return '';
$this->_tableDetailsO=WPage::renderBluePrint('listing');
$this->_tableDetailsO->transform->createHead($this->htmlObj );
$html=$this->_tableDetailsO->transform->wrapTable($this->customContent );
if(!empty($this->htmlObj->pagiHTML ) && ($this->htmlObj->pagination > 9 || ($this->htmlObj->pagination==1 && $this->htmlObj->pageNavO->limit > 10 )
|| ( WRoles::isNotAdmin('manager') && $this->htmlObj->pagination==1 ))){
$finalDIV=new WDiv($this->htmlObj->pagiHTML );
$finalDIV->style='margin:0 auto;position:relative;display:table;padding-top:15px;';
$html .=$finalDIV->make();
}
if(isset($this->htmlObj->formObj )){
if(!$this->htmlObj->manualDataB){
$pKey='';
if(empty($this->htmlObj->_pkey)){
$myModel=WModel::get($this->htmlObj->sid );
foreach($myModel->getPKs() as $onePK){
if($myModel->getParam('grpmap','') !=$onePK){
$pKey=$onePK;
break;
}
}
}else{
$pKey=$this->htmlObj->_pkey;
}
$this->htmlObj->formObj->hidden( JOOBI_VAR_DATA.'[s][pkey]' , $pKey );
 $this->htmlObj->formObj->hidden( JOOBI_VAR_DATA.'[s][mid]' , $this->htmlObj->sid );
}
if(isset($this->htmlObj->currentOrder)){
$currentOrder=$this->htmlObj->currentOrder.'|'.$this->htmlObj->currentOrderDir;
$this->htmlObj->formObj->hidden('sorting' , $currentOrder );
}
}else{
$form=WView::form($this->htmlObj->formName );
if(isset($this->htmlObj->currentOrder)){
$currentOrder=$this->htmlObj->currentOrder .'|'.$this->htmlObj->currentOrderDir;
$form->hidden('sorting' , $currentOrder );
}
}
return $html;
}
}
