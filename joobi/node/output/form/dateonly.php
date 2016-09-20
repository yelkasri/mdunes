<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('form.datetime');
class WForm_Coredateonly extends WForm_datetime {
protected $inputType='datetime';
protected $dateFormat='dateonly';
function show(){
$this->noTimeZone=true;
return parent::show();
}
}
