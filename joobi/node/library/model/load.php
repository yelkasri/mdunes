<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WModel_Load {
function load(){
static $caching=null;
if(!isset($caching)){
$caching=WPref::load('PLIBRARY_NODE_CACHING');
$caching=($caching > 0 )?'cache' : 'static';
}
$modelToLoad=array('apps','apps.level','apps.userinfos',
'users','users.role',
'role',
'library.table','library.foreign','library.columns',
'library.model','library.modeltrans',
'library.languages','translation.reference','translation.en',
'library.controller','library.action','library.controlleraction',
'library.view','library.viewtrans','library.viewlistings','library.viewlistingstrans','library.viewmenus','library.viewmenustrans',
'library.viewpicklist','library.picklist','library.viewforms','library.viewformstrans',
'library.viewfilters');
$keyListModel=array('sid','dbtid','namekey','folder','path','level','rolid','params','publish','fields','reload','audit','faicon','pnamekey');
$keyListModel[]='incoming';
$keyListModel[]='outgoing';
$keyListTable=array('name','prefix','group','suffix','pkey','type','dbid','domain','export','exportdelete');
$keyListTableAlias=array('tablename','tableprefix','tablegroup','tablesuffix');
$config=WGet::loadConfig();
$dbprefix=substr( JOOBI_DB_NAME, 0, strrpos(JOOBI_DB_NAME,'_') +1 );
$databaseInfos=new stdClass;
$databaseInfos->model=new stdClass;
$databaseInfos->model->_infos=new stdClass;
$databaseInfos->table=new stdClass;
$databaseInfos->database=new stdClass;
foreach($config->model as $key=> $value){
$databaseInfos->model->_infos->$key=$value;
}
$databaseInfos->model->table=$config->model['tablename'];
$databaseInfos->model->database=empty($config->model['dbname'])?'' : str_replace('#__',$dbprefix, $config->model['dbname']);
$databaseInfos->table->table=$config->table['tablename'];
$databaseInfos->table->database=empty($config->table['dbname'])?'' : str_replace('#__',$dbprefix, $config->table['dbname']);
$databaseInfos->database->table=$config->db['tablename'];
$databaseInfos->database->database=empty($config->db['dbname'])?'' : str_replace('#__',$dbprefix, $config->db['dbname']);
$modelM=WTable::get($databaseInfos->model->table, $databaseInfos->model->database, null, $databaseInfos->model );
$modelM->select($keyListModel );
$modelM->makeLJ($databaseInfos->table->table,$databaseInfos->table->database,'dbtid');
$modelM->select($keyListTable, 1, $keyListTableAlias );
$modelM->whereIn('namekey',$modelToLoad );
$modelM->setLimit( 100 );
$allModels=$modelM->load('ol');
if(empty($allModels)) return false;
$registry=WCache::get();
foreach($allModels as $tempdata){
if(empty($tempdata->dbname))$tempdata->dbname=JOOBI_DB_NAME;
if(empty($tempdata->addon))$tempdata->addon=WGet::DBType(); 
if(!empty($tempdata->params)){
WTools::getParams($tempdata, 'params');
}
if(!empty($tempdata->predefined)){
WTools::getJSON($tempdata, 'predefined');
}
$tempdata->pkey=trim($tempdata->pkey );
$allPK=explode(',',$tempdata->pkey );
$tempdata->primaryKeys=$allPK;
if( sizeof($allPK) > 1){
$tempdata->mpk=true;
}else{
$tempdata->mpk=false;
}
$registry->set('k-'.$tempdata->namekey, $tempdata->sid , 'Model',$caching ) ;
$registry->set('d-'.$tempdata->sid, $tempdata , 'Model',$caching );
}
}
}