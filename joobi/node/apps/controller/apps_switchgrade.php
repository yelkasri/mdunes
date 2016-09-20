<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_switchgrade_controller extends WController {
function switchgrade(){
$level=WGlobals::get('level', 0, '','int');
$wid=WGlobals::get('eid', 0, '','int');
$extensionInfoC=WClass::get('apps.info');
$extensionUserinfosM=WModel::get('apps.userinfos');
if($level==0){
$extensionUserinfosM->whereE('wid',$wid );
$extensionUserinfosM->whereE('enabled', 1 );
$extensionUserinfosM->setVal('enabled','0');
$extensionUserinfosM->update();
$this->_resetCMSCache($wid );
$message=WMessage::get();
$message->userS('1235976558LLLB');
WPages::redirect('controller=apps');
return true;
}
$extensionUserinfosM->whereE('wid',$wid );
$extensionUserinfosM->whereE('level',$level );
$infosO=$extensionUserinfosM->load('o');
if(!empty($infosO)){
$token=$infosO->token;
if($infosO->enabled < 0){
$message=WMessage::get();
$message->userE('1235976558LLLC');
WPages::redirect('controller=apps');
return true;
}elseif($infosO->expire < time()){ 
$message=WMessage::get();
$message->userN('1235976558LLLD');
WPages::redirect('controller=apps');
return true;
}
$ltypeTolicence=(!empty($infosO->ltype))?$infosO->ltype : 101 ;
}else{
$ltypeTolicence=101;
$token='';
}
$status=$extensionInfoC->requestCandy($ltypeTolicence, $wid , $level, $token, true);
$this->_resetCMSCache($wid );
if($status){
$message=WMessage::get();
$message->userS('1245769651NPXC');
}
WPages::redirect('controller=apps');
return true;
}
function _resetCMSCache($wid){
$extensionHelperC=WCache::get();
$extensionHelperC->resetCache();
}
}