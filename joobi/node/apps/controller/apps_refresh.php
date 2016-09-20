<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_refresh_controller extends WController {
function refresh(){
$netcomServerC=WClass::get('netcom.server');
$myDistribServer=$netcomServerC->checkOnline();
if($myDistribServer===false) return true;
$appsInfoC=WCLass::get('apps.info');
$url=$appsInfoC->myURL();
if( WPage::validSite($url, true)){
$appsInfoC=WCLass::get('apps.info');
$token=$appsInfoC->getPossibleCode('all','token');
if(empty($token) || true===$token){
$appsInfoC->requestTest();
}
}
$refresh=WClass::get('apps.refresh');
if(!$refresh->getDataAndRefresh(false)){
return false;
}
$apps=WGlobals::get('apps');
if(!empty($apps)){
$appsM=WModel::get('apps');
$appsM->whereE('publish' , 1 );
$appsM->whereE('type', 1 );
$appsList=$appsM->load('lra','namekey');
$appslevelM=WModel::get('apps.level');
foreach($appsList as $app){
if($app==JOOBI_MAIN_APP.'.application') continue; 
$appslevelM->wid=WExtension::get($app, 'wid');
$appslevelM->level=50;
$appslevelM->namekey=$app . 50;
$appslevelM->setIgnore();
$appslevelM->insert();
}
$apps=explode('.',$apps );
$app=$apps[0];
if( strpos($app, '_')===false)$app.='.application';
else $app=str_replace('_','.',$app );
$application=WExtension::get($app, 'data');
if(!empty($application )){
WPref::get($application->namekey );
if(!WPref::load('P'.strtoupper($application->folder ). '_APPLICATION_INSTALLED')){
$exist=WController::get($application->folder.'.setup', null, null, false);
$prefO=WPref::get($application->folder.'.application');
$prefO->updatePref('installed', 1 );
if($exist){
$url=WPage::routeURL('controller='.$application->folder.'&task=setup','smart','default', false, true, $application->folder );
WPages::redirect($url );
}
}
}
}
$cache=WCache::get();
$cache->resetCache();
$mess=WMessage::get();
$mess->userS('1229651871ADBI');
$appsRefreshC=WClass::get('apps.refresh');
$appsRefreshC->checkNewDistributionServer(true);
return true;
}
}