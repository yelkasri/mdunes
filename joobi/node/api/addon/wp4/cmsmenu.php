<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Api_Wp4_Cmsmenu_addon {
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
if(is_array($exts) && count($exts)>0){
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
return true;
}
public function createLanguagefile($namekey){
}
public function loadExtension($id){
$splitIDA=explode('-',$id );
$widgetNumber=array_pop($splitIDA );
$widgetTypeA=explode('_', implode('-',$splitIDA ));
array_shift($widgetTypeA );
$extensionO=new stdClass;
$extensionO->namekey=implode('.',$widgetTypeA );
return $extensionO;
}
public function editExtensionPreferences($id=0,$option=''){
if(empty($id)){
$id=WGlobals::get('id');
$dontSetEID=true;
}
$splitIDA=explode('-',$id );
$widgetNumber=array_pop($splitIDA );
$widgetTypeA=explode('_', implode('-',$splitIDA ));
array_shift($widgetTypeA );array_pop($widgetTypeA );
$widgetType=implode('.',$widgetTypeA );
$framework_type=93;
$mainWidgetM=WModel::get('library.widget');
$widgetO=$mainWidgetM->loadMemory($id );
$widgertId=(!empty($widgetO)?$widgetO->widgetid : 0 );
$title=WGlobals::get('title');
if(empty($widgertId)){
$mainWidgetM->returnId();
$mainWidgetM->framework_id=$widgetNumber;
$mainWidgetM->framework_type=$framework_type;
$mainWidgetM->namekey=$id;
$mainWidgetM->alias=$title;
$mainWidgetM->core=0;
$mainWidgetM->publish=1;
$mainWidgetC=WClass::get('main.widget', null, 'class', false);
if(!empty($mainWidgetC))$mainWidgetM->wgtypeid=$mainWidgetC->getWidgetTypeID($widgetType );
$mainWidgetM->setChild('library.widgettrans','name',$title );
$installInstallC=WClass::get('install.install');
$mainWidgetM->params=$installInstallC->getModuleInitialParams($widgetType.'.module');
$mainWidgetM->save();
$widgertId=$mainWidgetM->widgetid;
}else{
$mainWidgetM->whereE('widgetid',$widgertId );
$mainWidgetM->setVal('publish', 1 );
$mainWidgetM->setVal('alias',$title );
$mainWidgetM->update();
}
if(empty($widgertId)) return false;
if(!empty($dontSetEID)){
WGlobals::setEID($widgertId );
}
return true;
}
public function modulePreferences(){
return '';
}
public function installModulePreferencesFile(){
}
}