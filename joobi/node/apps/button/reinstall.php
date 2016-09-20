<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_CoreReinstall_button extends WButtons_external {
function create(){
$wid=WGlobals::getEID();
$type=WExtension::get($wid, 'type');
if($type==350){
return false;
}else{
$appsShowC=WClass::get('apps.show');
$info=$appsShowC->checkInstalled($wid );
}
if(!empty($info )){
if($info->version !=$info->lversion ) return false;
$appsInfoC=WCLass::get('apps.info');
$status=$appsInfoC->possibleUpdate($wid );
if(!$status){
return false;
}
$this->setAction('reinstall');
$this->setIcon('refresh');
$this->setFullDisable();
$this->setTitle( WText::t('1310984711PXOI'));
}else{
return false;
}
}
}