<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_format_save_controller extends WController {
function save(){
$lgid=WController::getFormValue('lgid','library.languages');
if(empty($lgid)) return false;
$languagesM=WModel::get('library.languages');
$languagesM->whereE('lgid',$lgid );
$localeconv=$languagesM->load('lr','localeconv');
$localeconvO=unserialize($localeconv );
$trk=WGlobals::get( JOOBI_VAR_DATA );
$XValuesA=$trk['x'];
foreach($XValuesA as $oneKey=> $onePAram){
if($onePAram !=$localeconvO->$oneKey){
$localeconvO->$oneKey=$onePAram;
}
}
if($localeconvO->dp==$localeconvO->ts){
$this->userE('1448549004LJKW');
return false;
}
$Newlocaleconv=serialize($localeconvO );
if(empty($Newlocaleconv)){
$this->userE('1382065678OBIQ');
return true;
}$languagesM=WModel::get('library.languages');
$languagesM->whereE('lgid',$lgid );
$languagesM->setVal('localeconv',$Newlocaleconv );
$languagesM->setVal('core', 0 );
$status=$languagesM->update();
if(!empty($status)){
$this->userS('1377884821GSJD');
}
$cache=WCache::get();
$cache->resetCache('Language');
return $status;
}
}