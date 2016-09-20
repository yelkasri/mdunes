<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_Helper_class extends WClasses {
function getCMSModuleVersionUsingWid($wid){
$model=WModel::get('apps');
$model->whereE('wid',$wid);
$params=$model->load('o','params');
WTools::getParams($params );
return $this->getCMSModuleVersion($params->cmsname,$params->cmstype,$params->group,$params->level);
}
function getCMSModuleVersion($folderName,$type='component',$group='',$level=''){
$systemFolderC=WGet::folder();
$systemFolderC->displayMessage(false);
$fileHandler=WGet::file();
$folder='';
switch(JOOBI_FRAMEWORK){
case 'joomla30':
$files=array();
switch($type){
case 'component':
$adminFolder=JOOBI_DS_ROOT.'administrator'.DS.'components'.DS.$folderName;
$siteFolder=JOOBI_DS_ROOT.'components'.DS.$folderName;
$folder=$adminFolder;
if(!$systemFolderC->exist($folder)){
if(!$systemFolderC->exist($siteFolder)) return false;
else $folder=$siteFolder;
}
$files=$systemFolderC->files($folder,'!!\.xml$',true,true);
break;
case 'plugin':
$siteFolder=JOOBI_DS_ROOT.'plugins'.DS.$group . DS;
if(!$systemFolderC->exist($siteFolder)) return false;
if($fileHandler->exist($siteFolder.$folderName.'.xml')){
$files[]=$siteFolder.$folderName.'.xml';
}
break;
case 'module':
$folder=JOOBI_DS_ROOT .($group?'' : 'administrator'.DS ). 'modules'.DS.$folderName . DS;
if(!$systemFolderC->exist($folder)) return false;
$files=$systemFolderC->files($folder,'!!\.xml$',true,true);
break;
case 'template':
$folder=JOOBI_DS_ROOT .($group?'' : 'administrator'.DS ). 'templates'.DS.$folderName . DS;
if(!$systemFolderC->exist($folder)) return false;
$files=$systemFolderC->files($folder,'!!\.xml$',true,true);
break;
default:
break;
}
if(empty($files)){
return false;
}
switch(JOOBI_FRAMEWORK){
default:
if(!class_exists('JApplicationHelper')){
require(JOOBI_DS_ROOT.'libraries'.DS.'joomla'.DS.'application'.DS.'helper.php');
}
$version=false;
foreach($files as $file){
$class='SimpleXMLElement';
if(class_exists('JXMLElement')){
$class='JXMLElement';
}libxml_use_internal_errors(true);
$xml=simplexml_load_file($file, $class );
if(empty($xml)) continue;
if(!is_object($xml->document) || ($xml->document->name() !='install' && $xml->document->name() !='mosinstall')){
unset($xml);
continue;
}
$element=&$xml->document->name[0];
$data['name']=$element?$element->data() : '';
$xmlType=$element?$xml->document->attributes("type") : '';
if($xmlType!=$type){
continue;
}
$element=&$xml->document->version[0];
$xmlVersion=$element?$element->data() : '';
if(!empty($level)){
if($folderName=='com_acajoom'){
$manifest=&$xml->document;
$element=&$manifest->getElementByPath('administration/files');
$xmlLevel='free';
if(is_a($element, 'JSimpleXMLElement') && count($element->children())){
$xmlfiles=&$element->children();
foreach($xmlfiles as $xmlfile){
if(preg_match('#^(plus|pro)/#',$xmlfile->data(),$match)){
if($match[1]=='plus' && $xmlLevel!='pro'){
$xmlLevel='plus';
}elseif($match[1]=='pro'){
$xmlLevel='pro';
}
}
}
}
if($xmlLevel!=$level){
return false;
}
}else{
}
}
if(!empty($xmlVersion) && $xmlType==$type){
$version=$xmlVersion;
break;
}
}
break;
}
if(preg_match('#^[0-9\.]*#',$version,$match)){
return $match[0];
}
break;
default:
break;
}
return false;
}
function getEncoding(){
if(function_exists('zend_loader_enabled') && zend_loader_enabled()){$enc=21;
}else{
$enc=0;
}
return $enc;
}
public function getAppsDependencies($wid=null,$select='namekey',$recursive=true,$skipJCenter=true){
if(!isset($wid))$wid=JOOBI_MAIN_APP.'.application';
if( is_string($wid ))$wid=WExtension::get($wid, 'wid');
if(empty($wid )) return;
if(is_array($wid)){
$finalResultA=array();
foreach($wid as $asOne){
$tmpA=$this->getAppsDependencies($asOne, $select, $recursive, $skipJCenter );
if(!empty($tmpA)){
if(!is_array($tmpA))$tmpA=array($tmpA );
if(!empty($tmpA)){
foreach($tmpA as $tmWid)$finalResultA[]=$tmWid;
}}}return $finalResultA;
}
$list=array();
$this->_getDepsOne($wid, $list, $recursive, $skipJCenter );
$wids=array($wid );
foreach($list as $ext ){
foreach($ext as $deps){
$wids[]=$deps->ref_wid;
}}
$wids=array_unique($wids );
if($select=='wid') return $wids;
else {
$appsM=WModel::get('apps');
$appsM->select( array('wid','namekey'));
$appsM->whereIn('wid',$wids );
$extlistA=$appsM->load('ol',$select );
$refactorA=array();
foreach($extlistA as $oneEntry){
$refactorA[$oneEntry->wid]=$oneEntry->$select;
}
$finalA=array();
foreach($wids as $oneEntry){
$finalA[]=$refactorA[$oneEntry];
}
return $finalA;
}}
private function _getDepsOne($wid,&$list,$recursive=true,$skipJCenter=true){
if(empty($wid )) return false;
if(!is_int($wid )){
if(is_array($wid)){
$newWIDA=array();
foreach($wid as $oneWID){
if(!is_int($oneWID )){
$newWIDA[]=WExtension::get($oneWID, 'wid');
}else{
$newWIDA[]=$wid;
}}$wid=$newWIDA;
}else{
$wid=WExtension::get($wid, 'wid');
}
}
static $mainAppWID=null;
if(empty($mainAppWID))$mainAppWID=WExtension::get( JOOBI_MAIN_APP.'.application','wid');
$ref_wids=$this->_getDependencies($wid );
if(empty($ref_wids )) return true;
if($recursive){
foreach($ref_wids as $ext){
if(!empty($list[ $ext->ref_wid ] )) continue;
if($skipJCenter && $wid !=$mainAppWID){
continue;
}
$this->_getDepsOne($ext->ref_wid, $list, $recursive, $skipJCenter );
}}
$list[ $wid ]=$ref_wids;
return $list;
}
private function _getDependencies($wid){
$appsDependencyM=WModel::get('install.appsdependency','object');
$appsDependencyM->rememberQuery();
$appsDependencyM->whereE('wid',$wid );
$appsDependencyM->setLimit( 5000 );
$ref_wids=$appsDependencyM->load('ol',array('wid','ref_wid'));
return $ref_wids;
}
public function getAppsTables($application=null,$skipJCenter=true,$return='array'){
if(!isset($application))$application=JOOBI_MAIN_APP.'.application';
$depsNamekeys=$this->getAppsDependencies($application, 'wid', true, $skipJCenter );
$extensModelsM=WModel::get('model.extension');
$extensModelsM->makeLJ('model','sid','sid', 0, 1 );
$extensModelsM->makeLJ('library.table','dbtid','dbtid', 1, 2 );
$extensModelsM->select('name' , 2 );
$extensModelsM->setDistinct('name', 2 );
$extensModelsM->whereIn('wid',$depsNamekeys  );
$extensModelsM->whereE('publish', 1, 1 );
$extensModelsM->where('namekey','not LIKE','joomla%', 1 );
$extensModelsM->where('namekey','not LIKE','jomsocial%', 1 );
$tables=$extensModelsM->load('lra');
sort($tables );
$j15tables=array('banner','bannerclient','bannertrack','categories','components','contact_details','content','content_frontpage','content_rating','core_acl_aro','core_acl_aro_groups','core_acl_aro_map',
'core_acl_aro_sections','core_acl_groups_aro_map','core_log_items','core_log_searches','groups','menu','menu_types','messages','messages_cfg','migration_backlinks','modules','modules_menu',
'newsfeeds','plugins','poll_data','poll_date','poll_menu','polls','sections','session','stats_agents','templates_menu','users','weblinks');
$j16tables=array('assets','banner_clients','banner_tracks','banners','categories','contact_details','content','content_frontpage','content_rating','core_log_searches','extensions','languages','menu','menu_types',
'messages','messages_cfg','modules','modules_menu','newsfeeds','redirect_links','schemas','session','template_styles','update_categories','update_sites','update_sites_extensions','updates','user_profiles',
'user_usergroup_map','usergroups','users','viewlevels','weblinks');
$j17tables=array('assets','associations','banner_clients','banner_tracks','banners','categories','contact_details','content','content_frontpage','content_rating','core_log_searches','extensions','languages','menu',
'menu_types','messages','messages_cfg','modules','modules_menu','newsfeeds','redirect_links','schemas','session','template_styles','update_categories','update_sites','update_sites_extensions','updates',
'user_profiles','user_usergroup_map','usergroups','users','viewlevels','weblinks');
$appsTables=array();
foreach($tables as $tablename){
if(!in_array($tablename, $j15tables ) && !in_array($tablename, $j16tables ) && !in_array($tablename, $j17tables ))
$appsTables[]=$tablename;
}
if($return=='array') return $appsTables;
else  {
$tablesList='';
foreach($appsTables as  $table){
$tablesList .='`'.$table.'`, ';
}
$tablesList=rtrim($tablesList, ',');
return $tablesList;
}}
}