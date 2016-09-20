<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_Corelabel extends WForms_default {
function create(){
$this->_wrapper='<div id="'.$this->idLabel;
$this->_wrapper .=(isset($this->element->style ))?'" style="'.$this->element->style  : '';
$this->_wrapper .=(isset($this->element->align ))?'" align="'.$this->element->align  : '';
$this->_wrapper .='" class="'.$this->class;
$this->_wrapper .='">'.$this->element->description.'</div>';
return true;
}
function add(&$frame,$params=null,$HTML=null){
if( __CLASS__ !=strtolower('WForm_'.$this->element->typeName)){
$frame->td_colspan=2;
$frame->cell($this->_wrapper );
$frame->line();
}else{
$this->content=$this->_wrapper;
return parent::add($frame, $params, $HTML );
}
}
}