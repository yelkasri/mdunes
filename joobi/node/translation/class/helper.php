<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Translation_Helper_class extends WClasses {
public function updateLanguages(){
$this->_updateLangSite('admin');
$this->_updateLangSite('site');
$this->_checkPublishedLanguage();
return true;
}
function processINI($lines,$removeTR=true){
$transData=array(); $metadata_keys=array('language','code','version','application','collation','method','origin');
$comments=array('##','--');
$regex='#('.implode('|',$metadata_keys ). '): (.*)#i';
$metadata=array();
$last=''; foreach($lines as $line){
if( substr($line, 0, 1 )=='#') continue;
if( in_array( substr($line,0,2), $comments )){
if( preg_match($regex,substr($line,2),$matches)){
$metadata[$matches[1]]=trim($matches[2]);}continue;
}
$parts=explode('=',$line, 2 );
if( count($parts)==2 && preg_match('#^[a-z0-9 ]+$#i' , $parts[0] )){
if(!empty($last))$transData[$last]=rtrim($transData[$last]);
if($removeTR && substr($parts[0], 0, 2 )=='TR')$last=substr($parts[0], 2 ); else $last=$parts[0]; $transData[$last]=$parts[1];
}elseif(!empty($last)){
$transData[$last] .="\n" . $line;
}
}
if(!empty($last))$transData[$last]=rtrim($transData[$last] );
$resultObj=new stdClass;
$resultObj->metaData=$metadata;
$resultObj->transData=$transData;
return $resultObj;
}
private function _updateAppsTrans($lgid=1,$wids=null){
if( is_string($lgid )){
$lgid=WLanguage::get($this->language, 'lgid');
}
if(!is_array($lgid ))$allLgids=array($lgid );
else $allLgids=$lgid;
if(!empty($wids ) && !is_array($wids )  && is_string($wids )){
$wids=WExtension::get($wids,  'wid');
}else{
$appsM=WModel::get('install.apps');
$appsM->whereE('type', 1 );
$appsM->whereE('publish', 1 );
$wids=$appsM->load('lra','wid');
}
if(!is_array($wids ))$wids=array($wids );
if(empty($allLgids )  || empty($wids )) return false;
$uid=WUser::get('uid');
$toInsert=array();
foreach($wids as $appWid){
foreach($allLgids as $oneLang){
$toInsert[]=array($appWid, $oneLang, time(), $uid );
}}
$appsTransM=WModel::get('apps.translations');
$appsTransM->whereIn('wid',$wids, 0, true);
$appsTransM->whereIn('lgid',  $allLgids, 0, true);
$appsTransM->delete();
$appsTransM->setReplace();
$appsTransM->insertMany( array('wid','lgid','modified','modifiedby'), $toInsert );
$appsTransM->insert();
return true;
}
private function _checkPublishedLanguage(){
$langM=WModel::get('library.languages');
$langM->whereE('publish', 1 );
$langM->where('code','!=','en');
$langM->setLimit( 500 );
$allLangPublish=$langM->load('ol',array('lgid','code'));
$langToUnPublish=array();
foreach($allLangPublish as $langPublish){
$langPublish->code=substr($langPublish->code, 0, 2 );
$modelsid=WModel::get('translation.'.$langPublish->code, 'sid', null, false);
if(empty($modelsid)){
$langToUnPublish[]=$langPublish->lgid;
}}
if(!empty($langToUnPublish)){
$langM=WModel::get('library.languages');
$langM->setVal('publish', 0 );
$langM->whereIn('lgid',$langToUnPublish );
$langM->setLimit( 5000 );
$langM->update();
}
if(empty($allLangPublish ))$allLangPublish=array( 1 );
$allLgid=array();
foreach($allLangPublish as $lang ) if(!empty($lang->lgid))$allLgid[]=$lang->lgid;
$allLgid[]=WLanguage::get('en','lgid');
$this->_updateAppsTrans($allLgid );
if(!empty($langToUnPublish)){
$appsTransM=WModel::get('apps.translations');
$appsTransM->resetAll();
$appsTransM->whereIn('lgid',$langToUnPublish );
$appsTransM->delete();
}
}
private function _updateLangSite($site='admin'){
$CMSLangs=WApplication::availLanguages('lgid',$site );
$langM=WModel::get('library.languages');
$langM->whereE('avail'.$site, true);
$langM->setLimit( 500 );
$allLangPublish=$langM->load('lra','lgid');
$unpublish=array_diff($allLangPublish, $CMSLangs );
$publish=array_diff($CMSLangs, $allLangPublish );
if(!empty($unpublish)){
$langM->whereIn('lgid',$unpublish );
$langM->setVal('avail'.$site, false);
$langM->setLimit( 500 );
$langM->update();
}
if(!empty($publish)){
$langM->whereIn('lgid',$publish );
$langM->setVal('avail'.$site, true);
$langM->setLimit( 500 );
$langM->update();
}
return true;
}
}