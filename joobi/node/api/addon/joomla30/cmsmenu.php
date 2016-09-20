<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Api_Joomla30_Cmsmenu_addon {
public function cmsLinks($wid=0,$prefix='',$recursive=true){
static $model=null;
static $types=null;
static $alreadyDoneChild=array();
static $alreadyDoneWID=array();
if(is_string($wid) && $wid=='clean'){
unset($model);
unset($types);
unset($alreadyDoneChild);
unset($alreadyDoneWID);
return true;
}
$widKey=serialize($wid);
if(isset($alreadyDoneWID[$widKey])) return true;
$alreadyDoneWID[$widKey]=true;
if(!isset($model)){
$model=WModel::get('install.apps');
$types=WType::get('apps.type');
}if(is_array($wid)){
$model->whereIn('wid',$wid);
}elseif(!empty($wid )){
if( is_int($wid)){
$model->whereE('wid',$wid );
}else{
$model->whereE('namekey',$wid );
}}else{
$model->whereIn('type',array( 1, 25, 50 ));
}$model->whereE('publish',1);
$model->makeLJ('apps.userinfos','wid');
$model->whereOn('enabled','=',1);
$model->select('level',1);
$model->setLimit( 10000 );
$exts=$model->load('ol',array('wid','namekey','folder','name','type','publish','status','params','destination','parent','modified'));
if(is_array($exts) && count($exts) >0){
foreach($exts as $ext){
if(in_array($ext->wid,$alreadyDoneChild)){
continue;
}$alreadyDoneChild[]=$ext->wid;
if(empty($ext->folder)) continue;
$addon=WAddon::get('install.'.JOOBI_FRAMEWORK );
$method=str_replace(' ','_', strtolower($types->getName($ext->type )) );
if(!empty($prefix)){
$method=$prefix.'_'.$method;
}
if( method_exists($addon, $method )){
if(empty($ext->level)){
$ext->level=250;
}
foreach( get_object_vars($ext ) as $key=> $val){
$addon->$key=$val;
}
$addon->path=JOOBI_DS_JOOBI . (!empty($ext->destination)?str_replace('|',DS,$ext->destination). DS : ''). $ext->folder;
if(!$addon->$method()){
$mess=WMessage::get();
$namekey=$ext->namekey;
$mess->codeE('Could not refresh the host CMS menu for the extension '.$namekey);
}
}if($recursive){
$sql=WModel::get('install.appsdependency');
$sql->whereE('wid',$ext->wid);
$sql->setLimit( 1000 );
$childs=$sql->load('lra','ref_wid');
if(is_array($childs) && count($childs)>0){
$this->cmsLinks($childs);
}}}
}
$mess=WMessage::get();
$mess->userS('1418698684IOVP');
return true;
}
public function createLanguagefile($namekey){
$explodeNameA=explode('_',$namekey );
$typeY=array_shift($explodeNameA );
$type=($typeY=='mod'?'module' : 'plugin');
$componentM=WModel::get('joomla.extensions');
$componentM->whereE('element',$namekey );
if(!empty($type))$componentM->whereE('type',$type );
$extO=$componentM->load('o');
$paramsO=json_decode($extO->manifest_cache );
if(!empty($paramsO->name)){
$content=strtoupper($namekey). '="'.$paramsO->name.'"';
$languageCode=APIApplication::cmsUserLang();
$fileName=$languageCode.'.'.$namekey.'.ini';
$location=JOOBI_DS_ROOT.'language'.DS.$languageCode . DS;
$filesC=WGet::file();
$filesC->write($location . $fileName, $content );
$eid=WGlobals::get('id');
WPages::redirect('index.php?option=com_modules&view=module&layout=edit&id='.$eid );
}
}
public function loadExtension($id,$option=null){
static $extensionA=array();
if(empty($id)) return false;
if(isset($extensionA[$id]))  return $extensionA[$id];
if(empty($option)){
$option=WGlobals::get('goty');
}if( substr($option, 0, 4 )=='com_')$option=substr($option, 4 );
if(empty($option))$option=WApplication::getApp();
$extensionO=null;
if('modules'==$option || 'advancedmodules'==$option){
$modulesT=WTable::get('modules','','id');
$modulesT->whereE('id',$id );
$extensionO=$modulesT->load('o');
if(empty($extensionO)){
$message=WMessage::get();
$message->userE('1377734767DOBZ');
return false;
}
$namekey=substr($extensionO->module, 4 );
$extensionO->namekey=str_replace('_','.',$namekey );
}else{
}
$extensionA[$id]=$extensionO;
return $extensionA[$id];
}
public function editExtensionPreferences($id=0,$option=''){
if(empty($id)){
$id=WGlobals::get('id');
$dontSetEID=true;
}
if(empty($option))$option=WGlobals::get('goty');
if( substr($option, 0, 4 )=='com_')$option=substr($option, 4 );
if('modules'==$option || 'advancedmodules'==$option){
$framework_type=92;
}else{
$framework_type=91;
}
$extensionO=$this->loadExtension($id, $option );
if(empty($extensionO)) return false;
$mainWidgetM=WModel::get('library.widget');
$mainWidgetM->whereE('framework_id',$id );
$mainWidgetM->whereE('framework_type',$framework_type );
$widgertId=$mainWidgetM->load('lr','widgetid');
if(empty($widgertId)){
$mainWidgetM->returnId();
$mainWidgetM->framework_id=$id;
$mainWidgetM->framework_type=$framework_type;
$mainWidgetM->namekey=$extensionO->namekey.'-'.$id;
$mainWidgetM->alias=$extensionO->title;
$mainWidgetM->core=0;
$mainWidgetM->publish=$extensionO->published;
$namekey=str_replace('_','.',$extensionO->namekey );
$mainWidgetC=WClass::get('main.widget', null, 'class', false);
if(!empty($mainWidgetC))$mainWidgetM->wgtypeid=$mainWidgetC->getWidgetTypeID( str_replace('.module','',$namekey ));
$mainWidgetM->setChild('library.widgettrans','name',$extensionO->title );
$installInstallC=WClass::get('install.install');
$mainWidgetM->params=$installInstallC->getModuleInitialParams($extensionO->namekey );
$mainWidgetM->setIgnore();
$mainWidgetM->save();
$widgertId=$mainWidgetM->widgetid;
}else{
$mainWidgetM->whereE('widgetid',$widgertId );
$mainWidgetM->setVal('publish',$extensionO->published );
$mainWidgetM->setVal('alias',$extensionO->title );
$mainWidgetM->update();
}
if(empty($widgertId)) return false;
WGlobals::setEID($widgertId );
return true;
}
public function modulePreferences(){
$string='a:3:{i:0;a:2:{s:8:"nodename";s:5:"param";s:10:"attributes";a:5:{s:4:"name";s:11:"preferences";s:5:"label";s:11:"'. WText::t('1206732392OZUQ').'";s:11:"description";s:58:"'. WText::t('1380567636DDVU').'";s:4:"type";s:11:"preferences";s:2:"id";s:11:"preferences";}}i:1;a:2:{s:8:"nodename";s:5:"param";s:10:"attributes";a:4:{s:4:"type";s:4:"text";s:4:"name";s:15:"moduleclass_sfx";s:5:"label";s:39:"COM_MODULES_FIELD_MODULECLASS_SFX_LABEL";s:11:"description";s:38:"COM_MODULES_FIELD_MODULECLASS_SFX_DESC";}}i:2;a:2:{s:8:"nodename";s:5:"param";s:10:"attributes";a:6:{s:4:"type";s:4:"list";s:4:"name";s:5:"cache";s:5:"label";s:31:"COM_MODULES_FIELD_CACHING_LABEL";s:11:"description";s:30:"COM_MODULES_FIELD_CACHING_DESC";s:7:"default";s:1:"0";s:8:"children";a:1:{i:0;a:3:{s:8:"nodename";s:6:"option";s:10:"attributes";a:1:{s:5:"value";s:1:"0";}s:9:"nodevalue";s:33:"COM_MODULES_FIELD_VALUE_NOCACHING";}}}}}';
return $string;
}
public function installModulePreferencesFile(){
$fileName=DS.'preferences.php';
$path=__DIR__ . $fileName;
$mainAppPath=JOOBI_DS_ROOT.'components'.DS.'com_'.JOOBI_MAIN_APP.DS.'fields'.DS.$fileName;
$fileS=WGet::file();
if(!$fileS->exist($mainAppPath )){
$fileS->copy($path, $mainAppPath );
}
}
}