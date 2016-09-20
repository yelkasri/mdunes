<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_fetchdemo_controller extends WController {
function fetchdemo(){
$app=WGlobals::get('app');
if(!empty($app )){
$extension=$app .'.application';
$appsInfoC=WCLass::get('apps.info');
$token=$appsInfoC->getPossibleCode('all','token');
if(empty($token) || true===$token){
$appsInfoC->requestTest();
}
}else{
$this->userE('1463622733DNAB');
}
WPages::redirect('previous');
return true;
}
}