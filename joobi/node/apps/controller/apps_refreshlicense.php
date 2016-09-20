<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_refreshlicense_controller extends WController {
function refreshlicense(){
$trk=WGlobals::get( JOOBI_VAR_DATA );
$SID=WModel::get('apps','sid');
$wid=$trk[$SID]['wid'];
$extensionM=WModel::get('apps');
$extensionM->makeLJ('apps.userinfos','wid');
$extensionM->whereE('wid',$wid );
$extensionM->select('license', 1 );
$extensionM->select('token', 1 );
$extensionM->select('level', 1 );
$extensionM->select('namekey', 0 );
$sx1=$extensionM->load('o');
$sx1de=base64_decode($sx1->license );
$sx1AR=explode('_',$sx1de );
if(!empty($sx1->token)){
$token=$sx1->token;
}elseif(isset($sx1AR[5])){
$token=$sx1AR[5];
}else{
$token='';
}
$typeLic=$sx1AR[1];
$extensionInfoC=WClass::get('apps.info');
$extensionInfoC->requestCandy($typeLic, $sx1->namekey, $sx1->level, $token, true);
return true;
}
}