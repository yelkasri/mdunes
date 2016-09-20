<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_Translationviewlanguages_picklist extends WPicklist {
function create(){
$eid=WGlobals::getEID();
$sid=(!empty($this->_params->elementSID)?$this->_params->elementSID : WGlobals::get('elementSID', 0 ));
if(empty($sid)) return false;
$model=WModel::get($sid );
if(!$model){
$mess=WMessage::get();
$mess->codeE('We could not instanciate the model based on the information provided to the picklist');
return false;
}
$myArraDiff=array_diff($model->getPKs(), array('lgid'));
$mainPK=reset($myArraDiff );
$model->whereE($mainPK, $eid );
$model->where('auto','>', 0 );
$model->setLimit( 500 );
$translations=$model->load('ol', array ('lgid','auto'));
$autos=array();
if(!empty($translations )){
foreach($translations as $translation){
$autos[$translation->lgid]=$translation->auto;
}
}
$currentLgid=WGlobals::get('lgid', 1 );
$this->setDefault($currentLgid, false);
if(!empty($autos[$currentLgid])){
$this->classes='trans_done';
}else{
$this->classes='trans_not_done';
}
$model=WModel::get('library.languages');
$model->whereE('publish', 1 );
$model->setLimit( 500 );
$results=$model->load('ol', array ('lgid','name'));
foreach($results as $result){
$obj=new stdClass;
$obj->class='trans_not_done';
if(!empty($autos[$result->lgid]))$obj->class='trans_done';
$this->addElement($result->lgid, $result->name, $obj );
}
return true;
}}