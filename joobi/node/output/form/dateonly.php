<?php 


* @license GNU GPLv3 */

WView::includeElement('form.datetime');
class WForm_Coredateonly extends WForm_datetime {
protected $inputType='datetime';
protected $dateFormat='dateonly';
function show(){
$this->noTimeZone=true;
return parent::show();
}
}