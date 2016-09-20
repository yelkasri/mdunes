<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_Corelayout extends WForms_default {
protected $viewID=null;
public $extraClass='nested';
function create(){
if(isset($this->element->onlynew ) && ! $this->newEntry && $this->element->editItem && $this->element->onlynew==1 && !isset($this->fromShow)) return true;
if((int)$this->element->level > WGlobals::getCandy()){
return false;
}
if(empty($this->viewID )){
if(!empty($this->element->ref_yid)){
$this->viewID=$this->element->ref_yid;
}elseif(!empty($this->element->nestedview)){$this->viewID=WView::get($this->element->nestedview, 'yid', null, null, false);
if(empty($this->viewID)) return false;
}else{
return false;
}
}
$params=new stdClass;
$params->value=$this->value;
$controller=new stdClass;
$controller->controller=$this->controller->controller;
$controller->task=$this->controller->task;
$controller->rolid=$this->element->rolid;
$controller->wid=$this->wid;
$controller->level=$this->element->level;
$controller->sid=$this->modelID;
$controller->nestedView=($this->controller->type=='1'?true : 'show');
$controller->parentView=$this->yid;
$controller->parentFormOn=$this->form;
if(isset($this->formName))$controller->firstFormName=$this->formName;
$controller->app=$controller->controller;
$typeOfView=WView::get($this->viewID, 'type', null, null, false);
if($typeOfView !=2){
$params->_data=$this->data;
$params->_eid=$this->eid;
}
$layout=WView::get($this->viewID, 'html',$params, $controller );
if($layout===false) return false;
if(empty($layout)){
$message=WMessage::get();
$VIEWNAMEKEY=WView::get($this->viewID, 'namekey');
$message->codeW('The reference view specified does not exists in your nest view, check the reference view :'.$this->viewID );
return false;
}
$content=$layout->make();
if($content===false){
return (empty($this->content)?false : true);
}
$id=WView::generateID('nested',$this->viewID );
$this->content .='<div id="'.$id.'">'.$content.'</div>';
return true;
}
public function show(){
$this->fromShow=true;if(isset($this->element->onlynew ) && $this->newEntry && $this->element->editItem && $this->element->onlynew==2 ) return true;
return $this->create();
}
}
