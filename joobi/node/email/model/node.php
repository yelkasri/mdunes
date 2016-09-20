<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Node_model extends WModel {
function addValidate(){
if(empty($this->wid)){
$this->wid=WExtension::get('design.node','wid');
}
return true;
}
function validate(){
$this->core=0;
return true;
}
function editExtra(){
$caching=WPref::load('PLIBRARY_NODE_CACHING');
if(!empty($this->mgid) && $caching > 5){
$cache=WCache::get();
$cache->resetCache('Model_'.$this->_infos->tablename );
}
return true;
}
public function secureTranslation($sid,$eid){
$translationC=WClass::get('email.translation', null, 'class', false);
if(empty($translationC)) return false;
if(!$translationC->secureTranslation($this, $sid, $eid )) return false;
return true;
}
}