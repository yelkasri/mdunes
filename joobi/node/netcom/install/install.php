<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Netcom_Node_install extends WInstall {
public function install(&$object){
if(!empty($this->newInstall ) || (property_exists($object, 'newInstall') && $object->newInstall)){
$file=WGet::file();
if(!$file->exist( JOOBI_DS_ROOT.'ws.php')){
$fileContent='<?php if(empty($_REQUEST["netcom"]))$_REQUEST["netcom"]="netcom";if(empty($_REQUEST["protocol"]))$_REQUEST["protocol"]="webservices";include("joobi/index.php");';
$file->write( JOOBI_DS_ROOT.'ws.php',$fileContent, 'overwrite');
}
}
}
}