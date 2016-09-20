<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_Uninstall_class extends WClasses {
 public function uninstallApps($eid,$method=''){
 if(empty($method)) WPages::redirect('controller=apps');
 if(!in_array($method, array('allapp','everything'))){
 $method='oneapp';
 }
 $redirect=false;
 switch($method){
 case 'everything':
 $redirect=false;
 $this->_unInstallExtension( 0 );
  $this->_removeTables();
 $this->_removeJoobiFolder(true);
 break;
 case 'allapp':
 $redirect=false;
 $this->_unInstallExtension( 0 );
    $this->_removeJoobiFolder();
 break;
 case 'oneapp':
 $redirect='controller=apps';
 if('joomla'==JOOBI_FRAMEWORK_TYPE){
  $option=WApplication::getApp();
 $folder=WExtension::get($eid, 'folder');
 if($option==$folder)$redirect=false;
 } $this->_unInstallExtension($eid );
 default:
 break;
 }
 if($redirect){
 WPages::redirect($redirect );
 }else{
 if('joomla'==JOOBI_FRAMEWORK_TYPE){
 WPages::redirect( JOOBI_SITE.'administrator');
 }elseif('wordpress'==JOOBI_FRAMEWORK_TYPE){
 WPages::redirect( JOOBI_SITE.'wp-admin');
 } }
return true;
 }
 private function _unInstallExtension($wid=0){
  $apiUninstallC=WAddon::get('api.'.JOOBI_FRAMEWORK.'.uninstall');
 if(empty($apiUninstallC)) return false;
 $apiUninstallC->unInstallOneExtension($wid );
  $appsM=WModel::get('apps');
 if(!empty($wid))$appsM->whereE('wid',$wid );
 $appsM->whereE('type', 1 );
 $appsM->setVal('publish', 0 );
 $appsM->update();
 }
 private function _removeJoobiFolder($joobi=false){
  $folderS=WGet::folder();
 $folderS->delete( JOOBI_DS_ROOT.'cache');
 $folderS->create( JOOBI_DS_ROOT.'cache');
 if($joobi)$folderS->delete( JOOBI_DS_ROOT . JOOBI_FOLDER );
 }
 private function _removeTables($tableType=array()){
 $libraryTableM=WModel::get('library.model','object');
$libraryTableM->makeLJ('library.table','dbtid');
$libraryTableM->whereNotIn('domain',array( 0, 250 ), 1 );
if(!empty($tableType))$libraryTableM->whereNotIn('domain',$tableType, 1 );
$libraryTableM->select('name', 1 );
$libraryTableM->groupBy('dbtid');
$allModelA=$libraryTableM->load('lra');
if(!empty($allModelA)){
$table=WTable::get();
$queryA=array();
foreach($allModelA as $oneTable){
$queryA[]=$table->nameQuote( JOOBI_DB_PREFIX . $oneTable );
}$query='DROP TABLE IF EXISTS '.implode(',',$queryA );
$table->load('q',$query );
}
 }
}