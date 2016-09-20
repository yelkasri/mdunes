<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_install_Finishinstaller_controller extends WController {
function finishinstaller(){
$installHandler=WClass::get('install.processnew');
$installHandler->clean();
$netcomServerC=WClass::get('netcom.server');
$available=$netcomServerC->checkOnline();
$message=WMessage::get();
$message->userB('finish');
$controller='apps';
$url='controller='.$controller;
$installedApplication=WGlobals::getSession('installRedirectInfo','redirectApp');
if(!empty($installedApplication)){
$appArr=explode('.application',$installedApplication );
if(!empty($appArr[0])){
$controller=strtolower($appArr[0]);
$controller=str_replace(' ','',$controller );
$url=WPage::routeURL( "controller=$controller", 'smart','default', false, false, JOOBI_MAIN_APP );
WGlobals::setSession('installRedirectInfo','alex',$url );
WGlobals::setSession('installRedirectInfo','redirectApp','');
}else{
WMessage::log(' error $installedApplication='.print_r($installedApplication, true),  'error_'.__FUNCTION__ );
WMessage::log(' error $appArr='.print_r($appArr, true),  'error_'.__FUNCTION__ );
}
}
if($available && $controller=='apps'){
WPages::redirect('controller='.$controller.'&task=refresh');
}else{
WPages::redirect($url );
}
return true;
}
}