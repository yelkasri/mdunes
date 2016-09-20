<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('form.text');
class WForm_Coreemail extends WForm_text {
protected $inputClass='inputbox';
protected $inputType='email';
function create(){
$this->setValidationMessage('type', WText::t('1342303504IJSM'));
if($this->element->required){
$this->setRequireEmail('email','1');
}
return parent::create();
}
function show(){
$outputEMailC=WClass::get('output.emailclock');
$this->content=$outputEMailC->cloakmail($this->idLabel, $this->eid , $this->value, 0 );
$formObject=WView::form($this->formName );
$formObject->hidden($this->map, '');
$idHidden=WView::generateID('hiddenemail',$this->map );
$this->content .=$outputEMailC->cloakmail($idHidden, $this->eid , $this->value, 0, true);
return true;
}
}