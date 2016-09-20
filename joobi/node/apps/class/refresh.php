<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_Refresh_class extends WClasses {
private $data=null;
private $repository=null;
private $refreshTables=array('apps','library.languages','appstrans','apps.version','apps.level','apps.leveltrans','install.appsdependency','apps.info');
private $fields_to_unset_for_extension_node=array('version','level','license','trans','pref','rolid','access');
private $newExtension=null;
private $_myDistribServer=null;
public function firstRefresh(){
$netcomServerC=WClass::get('netcom.server');
$myDistribServer=$netcomServerC->checkOnline();
if($myDistribServer===false) return false;
if( WPref::load('PLIBRARY_NODE_UDPATECHECK') > time()) return false;
$nextDay=time() + ( 24 * 60 * 60 );$pref=WPref::get('library.node', false, false);
$pref->updatePref('updatecheck',$nextDay );
$refresh=WClass::get('apps.refresh');
if($this->getDataAndRefresh(true)){
$appPref=WPref::get('apps.node');
$appPref->updatePref('firstrefresh', 1 );
}
return true;
}
public function getDataAndRefresh($newExtension=true){
WMessage::log('getDataAndRefresh 1  '.$newExtension, 'getDataAndRefresh-trace');
WTools::increasePerformance();
WMessage::log('getDataAndRefresh 2  '.$newExtension, 'getDataAndRefresh-trace');
if($newExtension){
$extensions=$this->_getAllExtensions();
}
WMessage::log('getDataAndRefresh 3  '.$newExtension, 'getDataAndRefresh-trace');
if(!isset($this->_myDistribServer)){
$distribserver=WPref::load('PAPPS_NODE_DISTRIBSERVER');
if($distribserver==11){
$this->userW('1338581028LZCR');
return false;
}
if( WExtension::exist('main.node')){
$mainUpdateC=WClass::get('main.update', null, 'class', false);
if(!empty($mainUpdateC))$mainUpdateC->checkDistribChoice();
}
$distribserver=WPref::load('PAPPS_NODE_DISTRIBSERVER');
if($distribserver==99){
$this->_myDistribServer=WPref::load('PINSTALL_NODE_DISTRIB_WEBSITE_BETA');
}else{
$this->_myDistribServer=WPref::load('PINSTALL_NODE_DISTRIB_WEBSITE');
}
}
WMessage::log('getDataAndRefresh 4  '.$newExtension, 'getDataAndRefresh-trace');
$netcom=WNetcom::get();
$object=new stdClass;
$object->refresh=5;
$object->cmsFramework=JOOBI_FRAMEWORK_TYPE;
$this->data=$netcom->send($this->_myDistribServer, 'repository','refresh3_new',$object );
WMessage::log('getDataAndRefresh 5  '.$newExtension, 'getDataAndRefresh-trace');
if(empty($this->data)){
$mess=WMessage::get();
$mess->userE('1206732397TAYG');
return false;
}
$this->refreshTables=array('apps','library.languages','appstrans','apps.level','apps.leveltrans','apps.info');
WMessage::log('getDataAndRefresh 7  '.$newExtension, 'getDataAndRefresh-trace');
if(!$this->refresh()){
return false;
}
WMessage::log('getDataAndRefresh 8  '.$newExtension, 'getDataAndRefresh-trace');
if($newExtension){
$this->_checkNewExtensions($extensions );
}
WMessage::log('getDataAndRefresh 9  '.$newExtension, 'getDataAndRefresh-trace');
return true;
}
public function checkAppsUpdate($again=false){
if($again){
$prefM=WPref::get('library.node');
$nextDay=time() + 60480 - 3600;
$prefM->updatePref('updatecheck',$nextDay );
}
$this->getDataAndRefresh(true);
if(!empty($this->newExtension)){
$libraryMessageQueueC=WClass::get('main.messagequeue', null, 'class', false);
$appExist=false;
$widgetExist=false;
$myAdmins=WUser::getRoleUsers('sadmin',array('uid','name','email'));
if(!empty($libraryMessageQueueC)){
$listApplication=array();
$listApplicationnew='';
$listApplicationupdate='';
foreach($this->newExtension as $extension){
$appExist=true;
$myExt=$extension;
$params=new stdClass;
$params->application=$myExt->name;
$params->version=$extension->userlversion;
if($extension->new){
$myExt->wid=0;
$listApplicationnew .=$params->application.' '.$params->version .'<br />';
}else{
$listApplicationupdate .=$params->application.' '.$params->version .'<br />';
}
$text='';
if($extension->new){
$APPLICATION=$myExt->name;
$text=str_replace(array('$APPLICATION'), array($APPLICATION),WText::t('1413479920LDPJ'));
}else{
$APPLICATION=$myExt->name .' '.$extension->userlversion;
$text=str_replace(array('$APPLICATION'), array($APPLICATION),WText::t('1413479920LDPK'));
}
$libraryMessageQueueC->addMessageToQueue($text, $myAdmins );
}}
if($appExist){
if(!empty($myAdmins)){
$mail=WMail::get();
$mail->keepAlive();
foreach($myAdmins as $admin){
WPref::get('apps.node');
$myPref=PAPPS_NODE_NOTEMAIL;
if($myPref){$mail->clear();
$params=new stdClass;
$params->applicationupdates=$listApplicationupdate;
$params->newapplications=$listApplicationnew;
$params->site=JOOBI_SITE_NAME;
$mail->setParameters($params );
$mail->sendNow($admin, 'main_update_apps_available', false);
}
}$mail->keepAlive(false);
}
}
}else{
if($again){
$hasUpdate=$this->checkNewDistributionServer(false);
}
}
return true;
}
public function checkNewDistributionServer($report=true){
$appsM=WModel::get('apps');
$appsM->whereE('publish', 1 );
$appsM->whereIn('type',array( 1, 150 ));
$appsM->where('version','!=' , 0 );
$appsM->where('version','<','lversion', 0, 0 );
$NotYetUpdatedA=$appsM->load('lra','wid');
if(!empty($NotYetUpdatedA )){
$allowUpdate=false;
}else{
$allowUpdate=true;
}
$sentData=new stdClass;
$sentData->site=rtrim( JOOBI_SITE, "/" );
$sentData->apiVersion='siteType';$sentData->version=WExtension::get( JOOBI_MAIN_APP.'.application','version');
$sentData->mainApp=JOOBI_MAIN_APP;
$sentData->distrib_production=WPref::load('PINSTALL_NODE_DISTRIB_WEBSITE');
$sentData->platform=JOOBI_FRAMEWORK_TYPE;$sentData->platformVersion=JOOBI_FRAMEWORK;$sentData->cmsVersion=APIApplication::version('short');
$sentData->phpVersion=@phpversion();
$sentData->ip=@gethostbyname( parse_url( JOOBI_SITE, PHP_URL_HOST ));
$sentData->dbType=WGet::DBType();
if( defined('JOOBI_DB_VERSION'))$sentData->dbVersion=JOOBI_DB_VERSION;
$netcom=WNetcom::get();
$receivedData=$netcom->send('http://www.joobiserver.com','repository','checkDistribSite',$sentData );
$hasUpdate=false;
if(!empty($receivedData)){
if(!empty($receivedData->type) && 'success'==$receivedData->type && !empty($receivedData->data)){
if('noNewDistrib'==$receivedData->data){
}else{
$GoodDAta=unserialize($receivedData->data );
if(is_array($GoodDAta)){
$installPref=WPref::get('install.node');
foreach($GoodDAta as $oneDistribSite){
if(empty($oneDistribSite->url)) continue;
$platformA=explode('_',$oneDistribSite->platform );
if(empty($platformA[1]))$platformA[1]='production';
switch($platformA[1]){
case 'beta':
$lastSet=WPref::load('PINSTALL_NODE_DISTRIB_WEBSITE_BETA_TIME');
if($allowUpdate && $lastSet < ( time() - 2592000 )){$installPref->updatePref('distrib_website_beta',$oneDistribSite->url );
$installPref->updatePref('distrib_website_beta_time', time());
}break;
case 'production':
default:
$prodcution=WPref::load('PINSTALL_NODE_DISTRIB_WEBSITE');
if($prodcution !=$oneDistribSite->url){
$hasUpdate=true;
if($allowUpdate){
$this->_myDistribServer=$oneDistribSite->url;
$installPref->updatePref('distrib_website',$oneDistribSite->url );
if($report)$this->userN('1392062121NBKP');
}}break;
}
}
if($allowUpdate && $hasUpdate){
$cache=WCache::get();
$cache->resetCache('Preference');
$this->checkAppsUpdate(false);
}
}else{
}
}
}
}
if($allowUpdate ) return $hasUpdate;
else {
if($hasUpdate){
$this->userW('1448462950OCEG');
}else{
$this->userW('1448462950OCEH');
}
return false;
}
}
public function refresh($data=null,$repository=false){
WMessage::log('refresh 71  ','getDataAndRefresh-trace');
if(!empty($data)){
$this->data=&$data;
$this->repository=$repository;
}
WMessage::log('refresh 72  ','getDataAndRefresh-trace');
$response=new stdClass;
if( count($this->data) !=count($this->refreshTables)){
$mess=WMessage::get();
$mess->codeE('The data received from the server is malformed. Please contact the support.');
$response=new stdClass;
$response->type='error';
$response->code='APPREFRESH101';
$response->message='The data received from the server is malformed. Please contact the support.';
return $response;
}
WMessage::log('refresh 73  ','getDataAndRefresh-trace');
$this->_setColumnNames();
WMessage::log('refresh 74  ','getDataAndRefresh-trace');
if(!$this->_insertData2()){
$mess=WMessage::get();
$mess->codeE('Could not update the data');
$response->type='error';
$response->code='APPREFRESH202';
$response->message=WText::t('1206732395DOFD');
return $response;
}
WMessage::log('refresh 76  ','getDataAndRefresh-trace');
if($repository){
$cacheC=WCache::get();
$cacheC->resetCache();
}
WMessage::log('refresh 78  ','getDataAndRefresh-trace');
$response->type='success';
$response->code='APPREFRESH303';
$response->message=WText::t('1347915878MZME');
WMessage::log('refresh 79  ','getDataAndRefresh-trace');
return $response;
}
private function _checkNewExtensions(&$oldExtensionsA){
$newExtensionsA=$this->_getAllExtensions();
if(empty($newExtensionsA )) return; 
$this->newExtension=array();
foreach($newExtensionsA as $new_extension){
$found=false;
foreach($oldExtensionsA as $k=> $extension){
if($new_extension->folder==$extension->folder){
$found=$k;
break;
}}
if($found===false){
$obj=new stdClass;
$obj->namekey=$new_extension->namekey;
$obj->name=$new_extension->name;
$obj->wid=$new_extension->wid;
$obj->userlversion=$new_extension->userlversion;
$obj->new=true;
$obj->levels=array();
if(is_array($new_extension->levels)){
$obj->levels=$new_extension->levels;
}
$obj->version=$new_extension->lversion;
$this->newExtension[]=$obj;
continue;
}
$new_levels=array();
if(is_array($new_extension->levels)){
if(is_array($extension->levels)){
foreach($new_extension->levels as $level){
if(!in_array($level,$extension->levels))
$new_levels[]=$level;
}
}else{
$new_levels=$new_extension->levels;
}}
$new=false;
if( version_compare($new_extension->lversion, $new_extension->version, '>'))$new=true;
if(!empty($new_levels) || $new){
$obj=new stdClass;
$obj->namekey=$new_extension->namekey;
$obj->name=$new_extension->name;
$obj->wid=$new_extension->wid;
$obj->userlversion=$new_extension->userlversion;
$obj->new=false;
if(!empty($new_levels)){
$obj->levels=$new_levels;
}
if($new)$obj->version=$new_extension->lversion;
$this->newExtension[]=$obj;
}
}
}
private function _getAllExtensions(){
static $extensions=array();
if(!empty($extensions)) return $extensions;
$sql=WModel::get('apps','object');
$sql->makeLJ('apps.info','wid');
$sql->select( array('wid','version','lversion','folder','namekey','type','name'));
$sql->select( array('userversion','userlversion'), 1 );
$sql->whereE('publish', 1 );
$sql->whereIn('type',array( 1, 2 ));
$sql->setLimit( 1000 );
$extensions=$sql->load('ol');
if(empty($extensions)) return $extensions;
$extensionLevelM=WModel::get('apps.level','object');
foreach($extensions as $k=> $extension){
$extensionLevelM->whereE('wid',$extension->wid);
$extensionLevelM->setLimit( 50 );
$extensions[$k]->levels=$extensionLevelM->load('lra','level');
}return $extensions;
}
private function _insertData2(){
$wid_convert=array();
$lgid_convert=array();
$lwid_convert=array();
WMessage::log('_insertData2 711  ','getDataAndRefresh-trace');
foreach($this->refreshTables as $key=> $table){
$data=&$this->data[$key];
if(!is_array($data) || count($data)==0){
continue;
}
WMessage::log('_insertData2 712  ','getDataAndRefresh-trace');
$model=WModel::get($table, 'object');
WMessage::log('_insertData2 713  ','getDataAndRefresh-trace');
if(empty($model )){
WMessage::log('_insertData2 714  model not found : '.$table , 'getDataAndRefresh-trace');
continue;
}
$model->_noCoreCheck=true;
switch ($table){
case 'apps':
$namekeys=array();
foreach($data as  $k=> $v){
$namekeys[ $v['namekey'] ]=$v['wid'];
}
$model->whereIn('namekey', array_keys($namekeys));
$model->setLimit( 5000 );
$wids=$model->load('ol',array('wid','namekey'));
if(empty($wids)){
WMessage::log('install-issue ','refresh_extensions_issue');
WMessage::log($namekeys , 'refresh_extensions_issue');
WMessage::log($wids , 'refresh_extensions_issue');
WMessage::log($table , 'refresh_extensions_issue');
WMessage::log($data , 'refresh_extensions_issue');
WMessage::log($this->refreshTables , 'refresh_extensions_issue');
continue;
}
$namekeys_found=array();
foreach($wids as $wid){
$namekeys_found[$wid->namekey]=$wid->wid;
$wid_convert[ $namekeys[$wid->namekey] ]=$wid->wid;
}
$data_to_insert=array();
$namekeys_inserted=array();
foreach($data as $k=> $v){
if(isset($v['level'])){
unset($v['level']);
}
if(isset($namekeys_found[$v['namekey']])){
$model->whereE('wid',$namekeys_found[$v['namekey']]);
if(isset($v['publish'])){
unset($v['publish']);
}
unset($v['wid']);
$model->update($v);
unset($data[$k]);
}else{
$namekeys_inserted[$v['namekey']]=$v['wid'];
unset($v['wid']);
$v['publish']=0;
$data_to_insert[$k]=$v;
}}
if(!empty($data_to_insert)){
$firstElement=reset($data_to_insert);
$selects=array_keys($firstElement);
$model->setIgnore();
$model->insertMany($selects, $data_to_insert );
$model->whereIn('namekey',array_keys($namekeys_inserted));
$wids=$model->load('ol',array('wid','namekey'));
foreach($wids as $wid){
$wid_convert[$namekeys_inserted[$wid->namekey]]=$wid->wid;
}unset($data_to_insert);
unset($wids);
}
break;
case 'languages':
case 'library.languages':
$codes=array();
foreach($data as  $k=> $v){
$codes[$v['code']]=$v['lgid'];
}$model->whereIn('code',array_keys($codes));
$model->setLimit( 500 );
$lgids=$model->load('ol',array('lgid','code'));
foreach($lgids as $lgid){
$lgid_convert[$codes[$lgid->code]]=$lgid->lgid;
}
break;
case 'appstrans':
if(empty($wid_convert)) continue;
foreach($data as $k=> $v){
$data[$k]['wid']=$wid_convert[$v['wid']];
$data[$k]['lgid']=$lgid_convert[$v['lgid']];
}
$firstElement=reset($data);
$selects=array_keys($firstElement);
$model->setReplace();
$model->insertMany($selects, $data );
break;
case 'apps.version':
if(empty($wid_convert)) continue;
if(!empty($this->repository)){
foreach($data as  $k=> $v){
if(!isset($wid_convert[$v['wid']] )){
WMessage::log('Could not find the old wid '.$v['wid'].' in the convert array','refresh');
static $show_convert=true;
if($show_convert){
WMessage::log($wid_convert,'refresh');
$show_convert=false;
}unset($data[$k]);
}else{
$data[$k]['wid']=$wid_convert[$v['wid']];
$model->whereE('wid',$data[$k]['wid']);
$model->setLimit(50000);
$versions=$model->load('ol',array('version','status','beta'));
$max=0;
$max_version=0;
foreach($versions as $version){
if(version_compare($max, $version->version, '<')){
$max=$version->version;
$max_version=$version;
}}
if(is_object($max_version)){
if($max_version->status==25){
$data[$k]['status']=100;
}else{
$data[$k]['status']=75;
}}
if(isset($data[$k]['final'])){
unset($data[$k]['final']);
}if(isset($data[$k]['vsid'])){
unset($data[$k]['vsid']);
}
}}}else{
foreach($data as  $k=> $v){
$data[$k]['wid']=$wid_convert[$v['wid']];
unset($data[$k]['status']);
if(isset($data[$k]['vsid'])){
unset($data[$k]['vsid']);
}}}
$firstElement=reset($data);
$selects=array_keys($firstElement);
if(!empty($data)){
$newInsertA=array();
foreach($data as $oneRow){
if(empty($oneRow)) continue;
$model->whereE('wid',$oneRow['wid'] );
$model->whereE('version',$oneRow['version'] );
$vsidN=$model->load('lr','vsid');
if(empty($vsidN)){
$newInsertA[]=$oneRow;
}else{
$model->whereE('wid',$oneRow['wid'] );
$model->whereE('version',$oneRow['version'] );
foreach($oneRow as $keyH=> $valH){
if( in_array($keyH, array('wid','version')) ) continue;
$model->setVal($keyH, $valH );
}$model->update();
}
}
if(!empty($newInsertA)){
$model->resetAll();
$model->setIgnore();
$model->insertMany($selects, $newInsertA );
}
}
break;
case 'apps.level':
if(empty($wid_convert)) continue;
$namekeys=array();
foreach($data as  $k=> $v){
$data[$k]['wid']=$wid_convert[$v['wid']];
$namekeys[$v['namekey']]=$v['lwid'];
unset($data[$k]['lwid']);
if(isset($data[$k]['publish'])){
unset($data[$k]['publish']);
}
if(isset($data[$k]['status'])){
unset($data[$k]['status']);
}}
$firstElement=reset($data);
$selects=array_keys($firstElement);
$model->setReplace();
$model->insertMany($selects, $data );
$model->whereIn('namekey',array_keys($namekeys));
$model->setLimit(50000);
$lwids=$model->load('ol',array('lwid','namekey'));
foreach($lwids as $lwid){
$lwid_convert[$namekeys[$lwid->namekey]]=$lwid->lwid;
}
break;
case 'apps.leveltrans':
if(empty($wid_convert)) continue;
foreach($data as  $k=> $v){
$data[$k]['lwid']=$lwid_convert[$v['lwid']];
if(isset($lgid_convert[$v['lgid']])){
$data[$k]['lgid']=$lgid_convert[$v['lgid']];
}}
$firstElement=reset($data);
$selects=array_keys($firstElement);
$model->setReplace();
$model->insertMany($selects, $data );
break;
case 'apps.dependency':
case 'install.appsdependency':
if(empty($wid_convert)) continue;
$parent_wids=array();
foreach($data as  $k=> $v){
$data[$k]['wid']=$wid_convert[$v['wid']];
if(!is_numeric($v['ref_wid'])){
$extensionM=WModel::get('apps','object');
$extensionM->whereE('namekey',$v['ref_wid']);
$childWid=$extensionM->load('lr','wid');
if(empty($childWid)){
WMessage::log('Could not find the extension '.$v['ref_wid'].' which is needed by the extension '.$data[$k]['wid']. ' as a virtual dependency. The virtual dependency won\'t be added to the distrib server','error-refresh');
WMessage::log($v, 'error-refresh');
unset($data[$k]);
continue;
}else{
$data[$k]['ref_wid']=$childWid;
}
}else{
$data[$k]['ref_wid']=$wid_convert[$v['ref_wid']];
}$parent_wids[]=$data[$k]['wid'];
}
$model->whereIn('wid',$parent_wids);
$model->delete();
$firstElement=reset($data);
$selects=array_keys($firstElement);
$model->setIgnore();
$model->insertMany($selects, $data );
break;
case 'apps.info':
if(empty($wid_convert)) continue;
$wids=array();
foreach($data as  $k=> $v){
$data[$k]['wid']=$wid_convert[$v['wid']];
$wids[]=$data[$k]['wid'];
}
$model->whereIn('wid',$wids);
$model->setLimit(50000);
$userversions=$model->load('ol',array('wid','userversion'));
$model->whereIn('wid',$wids);
$model->delete();
if(!empty($userversions)){
$wids=array();
foreach($userversions as $v){
$wids[$v->wid]=$v->userversion;
}
foreach($data as  $k=> $v){
if(isset($wids[$v['wid']])){
$data[$k]['userversion']=$wids[$v['wid']];
}else{
$data[$k]['userversion']=$data[$k]['userlversion'];
}}}
$firstElement=reset($data);
$selects=array_keys($firstElement);
$model->setIgnore();
$model->insertMany($selects, $data );
break;
}
unset($data);
unset($this->data[$key]);
}
return true;
}
private function _insertData(&$tables){
foreach($tables as $k=> $table){
$data=&$this->data[$k];
if(!is_array($data) || count($data)==0) continue;
$sid=WModel::get($table,'sid');
$pkey=WModel::get($table,'pkey');
if(empty($sid)){
$mess=WMessage::get();
$mess->codeE('Could not get the id for the model '.$table);
return false;
}
if(isset($data[0]['parent'])){
$this->_sort($k,$pkey);
}
$first_row=reset($data);
if(!isset($first_row['row_done'])){
if(!$this->_updateForeignKeysValues($sid,$data,$tables,$table)){
$mess=WMessage::get();
$mess->codeE('Could not update the foreign keys with their new values');
return false;
}}
$model=WModel::get($sid,'object');
$cons=$model->getConstraints('uk');
if(empty($cons)){
$cons=array($model->getConstraints('pk'));
}else{
foreach($cons as $k=> $v){
$cons[$k]=array_keys($v);
}}
if(!$this->_insertDataOfOneTable($table,$data,$cons,$pkey)){
$mess=WMessage::get();
$mess->codeE('Could not update the data of the table '.$table);
return false;
}
}
return true;
}
private function _insertDataOfOneTable($table,&$data,&$cons,$pkey)
{
$notmpk=!strpos($pkey,',');
if($notmpk)
{
$fields=array($pkey);
}else{
$fields=explode(',',$pkey);
}
foreach($data as $k=> $v)
{
if(isset($v['row_done']) && $v['row_done'])
{
continue;
}
$sql=WModel::get($table,'object');
foreach($v as $name=> $value)
{
if($name=='parent')
{
if(!$this->_updateParentValue($table,$value,$k,$v,$data,$pkey))
{
return false;
}
}
elseif(in_array($name,$fields))
{
$data[$k][$name]=array($v[$name]);
}
$sql->$name=$value;
}
$found=false;
if(!$this->_checkUnique($table,$data,$sql,$cons,$fields,$k,$found))
{
$mess=WMessage::get();
$mess->codeE('An error occurred while checking unique keys for the table '.$table);
return false;
}
$stillsomething=true;
if($found){
$stillsomething=false;
$querydata=get_object_vars($sql);
foreach($querydata as $querykey=> $queryname){
if($querykey[0]!='_' && !in_array($querykey,$fields))
$stillsomething=true;
}
}
if($stillsomething){
$sql->setIgnore(true);
if($found){
$result=$sql->update();
}else{
$result=$sql->insert();
}
if(!$result)
{
$mess=WMessage::get();
$mess->codeE('Could not save the row '.$k.' of the model '.$table);
}
}
if($notmpk && !$found)
{
$data[$k][$pkey][]=$sql->$pkey;
}
$data[$k]['row_done']=true;
}
return true;
}
private function _updateParentValue($table,&$value,$k,&$v,&$data,$pkey){
if($value==0)
{
return true;
}
foreach($data as $k2=> $v2)
{
if(is_array($v2[$pkey]) && $v2[$pkey][0]==$v['parent'])
{
$data[$k]['parent']=$v2[$pkey][1];
$v['parent']=$v2[$pkey][1];
$value=$v['parent'];
break;
}
elseif($k2 > $k)
{
WMessage::log('Could not find the new parent value of the column parent for the row '.$k.' of the model '.$table,'refresh');
}
}
return true;
}
private function _checkUnique($table,&$data,&$sql,&$cons,&$fields,$k,&$found){
if(count($cons)==0){
return true;
}
$useNamekey=false;
foreach($cons as $check){
foreach($check as $c)
{
if($c=='namekey'){
$useNamekey=true;
break 2;
}
}
}
if($useNamekey){
$check=array('namekey');
$this->_checkSingleUK($table,$data,$sql,$cons,$fields,$k,$found,$check);
}else{
foreach($cons as $check){
if(!$this->_checkSingleUK($table,$data,$sql,$cons,$fields,$k,$found,$check)){
return true;
}
}
}
return true;
}
private function _checkSingleUK($table,&$data,&$sql,&$cons,&$fields,$k,&$found,&$check){
$sql2=WModel::get($table,'object');
$found=false;
$values_string='';
$fields_string='';
foreach($check as $c){
if(!isset($sql->$c)){
return true;
}
$sql2->whereE($c,$sql->$c);
$values_string.=$sql->$c.',';
$fields_string.=$c.',';
}
$sql2->returnId(true);
$tmp=$sql2->load('o');
$count=count($fields);
if(is_object($tmp)){
$found=true;
foreach($fields as $field){
$data[$k][$field][]=$tmp->$field;
unset($sql->$field);
$sql->whereE($field, $tmp->$field);
}
foreach($cons as $check2){
foreach($check2 as $c){
if(isset($sql->$c)){
if($count==1 || !in_array($c,$fields)){
unset($sql->$c);
}
}
}
}
$this->_processStateFields($sql,$table,$tmp);
return false;
}else{
if($count==1){
foreach($fields as $field){
unset($sql->$field);
}
}
$sql->returnId(true);
$values_string=rtrim($values_string,',');
$fields_string=rtrim($fields_string,',');
WMessage::log('Could not find the entry with the values '.$values_string.' for the fields '.$fields_string,'refresh');
}
return true;
}
private function _setColumnNames(){
foreach($this->refreshTables as $k=> $table){
$data=&$this->data[$k];
if(!is_array($data) || count($data) < 2)
{
continue;
}
$names=array_shift($data);
foreach($data as $k=> $v)
{
foreach($names as $l=> $name)
{
if($table !='apps' || !in_array($name,$this->fields_to_unset_for_extension_node)){
$data[$k][$name]=$v[$l];
}unset($data[$k][$l]);
}
}
}
return true;
}
private function _updateForeignKeysValues($sid,&$data,&$tables,$table){
$model=WModel::get($sid,'object');
$sql=WModel::get('library.foreign','object');
$sql->makeLJ('library.table','dbtid'); $sql->makeLJ('library.columns','feid','dbcid'); $sql->makeLJ('library.table','ref_dbtid','dbtid'); $sql->makeLJ('library.columns','ref_feid','dbcid'); $sql->whereE('dbtid',$model->getTableId());
$sql->where('dbtid','!=','ref_dbtid',0,0);$sql->whereE('publish',1);
$sql->where('namekey','!=','role',3);
$sql->select(array('dbtid','ref_dbtid','feid','ref_feid','ondelete','onupdate','namekey'));
$sql->select(array('name','prefix','dbid','export','namekey'),1,array('tableName','tablePrefix','DBID','export','tableNamekey'));
$sql->select(array('name'),2,array('columnName'));
$sql->select(array('name','prefix','dbid','export','namekey'),3,array('refTableName','refTablePrefix','refDBID','refExport','refTableNamekey'));
$sql->select(array('name'),4,array('refColumnName'));
$foreigns=$sql->load('ol');
if(is_array($foreigns ) && count($foreigns ) >0){
foreach($foreigns as $foreign){
$foreign_namekey=$foreign->refTableNamekey;
$map=$foreign->columnName;
$ref_map=$foreign->refColumnName;
$ref_table=&$this->data[array_search($foreign_namekey,$tables)];
foreach($data as $l=> $v)
{
$found=false;
foreach($ref_table as $v2)
{
if(!is_array($v2[$ref_map]))
{
$mess=WMessage::get();
$mess->codeE('The model '.$table.'('.$map.') need the model '.$foreign_namekey.'('.$ref_map.') be inserted before it because of the foreign key '.$foreign->namekey);
return false;
}
if($v[$map]==$v2[$ref_map][0])
{
if(!isset($v2[$ref_map][1]))
{
$value=$v[$map];
$mess=WMessage::get();
$mess->codeE('The model '.$table.' need to update the field '.$map.' with the field '.$ref_map.' of the model '.$foreign_namekey.'. a match has been found for the value '.$value. ' but the replacement value is missing.');
return false;
}
$data[$l][$map]=$v2[$ref_map][1];
$found=true;
break;
}
}
if(!$found)
{
$value=$v[$map];
$mess=WMessage::get();
$mess->codeE('Could not find the new value ('.$value.') of the column '.$map.' for the row '.$l.' of the model '.$table);
WMessage::log('Could not find the new value ('.$value.') of the column '.$map.' for the row '.$l.' of the model '.$table,'refresh');
WMessage::log($this->data,'refresh');
WMessage::log($data,'refresh');
return false;
}
}
}
}
return true;
}
private function _processStateFields(&$sql,$table,$indb){
switch($table){
case 'apps.version':
if(isset($this->repository) && $this->repository)
{
if(isset($sql->final))
unset($sql->final);
if($indb->status==25)
{
$sql->status=100;
}
else
{
$sql->status=75;
}
}
else
{
unset($sql->status);
}
break;
case 'apps':
if(isset($sql->publish))
unset($sql->publish);
break;
case 'languages':
case 'library.languages':
if(isset($sql->publish))
unset($sql->publish);
break;
case 'apps.level':
unset($sql->publish);
unset($sql->status);
break;
}
}
private function _sort($data_key,$pkey){
$notsorted=true;
while($notsorted){
$notsorted=false;
$data=$this->data[$data_key];
foreach($data as $k=> $v){
if($v['parent'] !=0){
$redo=false;
foreach($data as $k2=> $v2){
if($v['parent']==$v2[$pkey]){
if($k < $k2){
$this->data[$data_key][$k]=$v2;
$this->data[$data_key][$k2]=$v;
$notsorted=true;$redo=true;
}break;
}}if($redo===true){
break;
}}}}}
}
