<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_Node_install {
public function install(&$object){
$this->_populateAppsTrans();
return true;
}
private function _populateAppsTrans(){
$appsM=WModel::get('install.apps');
$appsM->select('wid');
$appsM->whereE('type', 1 );
$appsM->whereE('publish', 1 );
$wids=$appsM->load('lra');
if(empty($wids)) return false;
$langM=WModel::get('library.languages');
$langM->whereE('publish', 1 );
$lgids=$langM->load('lra','lgid');
if(empty($lgids)) return false;
$appsTransM=WModel::get('apps.translations');
foreach($wids as $wid){
foreach($lgids as $lgid){
$appsTransM->setVal('wid',$wid );
$appsTransM->setVal('lgid',$lgid );
$appsTransM->setVal('modified', time());
$appsTransM->setVal('modifiedby', WUser::get('uid'));
$appsTransM->setIgnore();
$appsTransM->insert();
}}
}
}