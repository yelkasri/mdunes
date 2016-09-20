<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_Translation_form_lang_view extends Output_Forms_class {
function prepareQuery(){
$sid=WGlobals::getSession('translationSID','sid', 0 );
if(!empty($sid)){
$this->sid=$sid;
foreach($this->elements as $key=> $val){
if(!empty($this->elements[$key]->sid))$this->elements[$key]->sid=$sid;
}
}
return true;
}
function prepareView(){
$task=WGlobals::get('task');
if('add'==$task){
$this->removeElements('translation_form_lang_code');
}else{
$this->removeElements('translation_form_lang_code_new');
}
return true;
}
}