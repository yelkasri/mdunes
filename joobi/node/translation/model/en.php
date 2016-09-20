<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_En_model extends WModel {
function addValidate(){
$this->imac=$this->x['imac'];
if(!is_numeric( substr($this->imac, 0, 1 ))){
$this->userW('1463158760NTED');
$this->imac='999'.$this->imac;
}
$this->nbchars=strlen($this->text );
return true;
}
function validate(){
$this->auto=2;
return true;
}
function extra(){
$namekeyA=explode('.',$this->getModelNamekey());
$translationPopulateC=WClass::get('translation.populate');
$translationPopulateC->updateTranslation($this->imac, $this->text, $namekeyA[1] );
return true;
}
}