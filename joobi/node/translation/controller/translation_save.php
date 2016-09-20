<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Translation_Save_controller extends WController {
function save(){
$trk=WGlobals::get( JOOBI_VAR_DATA );
$sid=$trk['s']['mid'];
$model=WModel::get($sid );
$tpkey='tpkey_'.$sid;
$teid='teid_'.$sid;
$tpkey=WGlobals::get($tpkey );
$teid=WGlobals::get($teid );
$lgid=WController::getFormValue('lgid',$sid );
$trk=WGlobals::get( JOOBI_VAR_DATA );
$submitedthings=$trk[$sid];
$copyofsub=$submitedthings;
if($model->_type==20){
$model->whereE($tpkey, $teid );
$model->whereE('lgid',$lgid);
$reus=$model->load('o');
if(!empty ($reus)){
unset($copyofsub['lgid']);
foreach($copyofsub as $toset=> $value){
$model->setVal($toset, $value );
}
$model->whereE('lgid',$lgid);
$model->whereE($tpkey, $teid);
$model->update();
$language=WLanguage::get($lgid, 'name');
}else{
$model->$tpkey=$teid;
foreach($submitedthings as $toset=> $value){
$model->$toset=$value;
}$model->save();
}}else{
return false;
return 'Error';
}
return true;
}
}