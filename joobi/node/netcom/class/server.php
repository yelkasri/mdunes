<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Netcom_Server_class extends WClasses {
public function checkOnline($onlyURL=true){
  static $available=null;
if(!isset($available)){
if(!ini_get('allow_url_fopen')){
$this->userW('1389293001AGCY');
$available=false;
return $available;
}
$distribserver=WPref::load('PAPPS_NODE_DISTRIBSERVER');
if($distribserver==11){
$this->userW('1338581028LZCR');
$available=false;
return false;
}
if($distribserver==99){
$myDistribServer=WPref::load('PINSTALL_NODE_DISTRIB_WEBSITE_BETA');
}elseif($distribserver==54){
$myDistribServer=WPref::load('PINSTALL_NODE_DISTRIB_WEBSITE_DEV');
}else{
$myDistribServer=WPref::load('PINSTALL_NODE_DISTRIB_WEBSITE');
}
if($onlyURL ) return $myDistribServer;
$testData='Hkjkdsiu567HKKDGtyuH';
$netcom=WNetcom::get();
$netResult=$netcom->send($myDistribServer, 'netcom','ping',$testData, false);
if($netResult !=$testData){
$this->userW('1369749852DBNC');
$available=false;
}else{
$available=$myDistribServer;
}
}
$available=rtrim($available, '/');
return $available;
}
}