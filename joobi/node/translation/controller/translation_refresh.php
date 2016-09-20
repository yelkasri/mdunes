<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Translation_Refresh_controller extends WController {
function refresh(){
$trk=WGlobals::get( JOOBI_VAR_DATA );
$sid=$trk['s']['mid'];
$model=WModel::get($sid);
$tpkey='tpkey_'.$sid;
$teid='teid_'.$sid;
$tpkey=WGlobals::get($tpkey);
$type=WGlobals::get('type');
$map=WGlobals::get('map');
$teid=WGlobals::get($teid);
$fid=WGlobals::get('fid');
$idLabel=$map.'_'.$sid.'_'.$fid;
$lgid=WController::getFormValue('lgid',$sid );
if(!empty($trk)){
$model->whereE($tpkey, $teid);
$model->whereE('lgid',$lgid);
$content=$model->load('o');
if(!empty ($content)){
return 'MSG['.$content-> $map.'] ID['.$idLabel.']';
}else{
$rez=WLanguage::get($lgid );
$return='MSG['.'No translation for'.' '.$rez->name.'] ID['.$idLabel.']';
return $return;
}}else{
$return='MSG[] ID[div'.WGlobals::get('formname'). ']';
return $return;
}}
}