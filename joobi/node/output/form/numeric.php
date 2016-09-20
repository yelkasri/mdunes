<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('form.text');
class WForm_Corenumeric extends WForm_text {
protected $inputType='number';protected $inputClass='inputbox';
function create(){
if((int)$this->value==$this->value)$this->value=(int)$this->value;
return parent::create();
}
function show(){
if((int)$this->value==$this->value)$this->value=(int)$this->value;
return parent::show();
}
}