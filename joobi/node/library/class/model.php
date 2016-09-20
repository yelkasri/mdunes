<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WModel extends WTable {
var $_new=null;
var $_saveState=false; 
var $_ftype='files';
var $_propagateToChild=false;var $_childRemoveNotPresent=false;var $_childOnlyAddNew=false;
var $_samePrimaryKey=false;
var $_parentKey=null;var $_parentId=null;
public $_keepAttributesOnDelete=false;var $_keepAttributesOnSave=false; 
var $_doNotDeleteValidate=false;
var $_getChildIds=false;
var $_checkModel=false; 
public $_pubProperties=null;
public $_validate=true;
public $_singleDelete=false;
var $_noCoreCheck=false;
public $_optimizeImg=true;
private $_skipMessage=false;
public $_saveOrder=false;
public $_fileInfo=array();
protected $_loadElementBeforeDelete=false;
private static $WMessageLog=array();
protected $_makeAudit=false;
protected $_dontExportA=array();
function __construct($params=null){
if(isset($params )){
if(is_array($params )){
foreach($params as $key=> $val )
{
$param='_'.$key;
 if(!isset($this->$param))$this->$param=$val;
}
}elseif( is_object($params )){
foreach( get_object_vars($params) as $key=> $val )
{
if(!isset($this->$key))$this->$key=$val;
}
}}
}
public static function get($path=null,$return='objectfile',$params=null,$showMessage=true,$modelClassName='WModel'){
static $needToLoadMainTableFlag=null;
if(!is_numeric($path) && ! is_string($path)) return false;
if('object'==$return || null==$return)$return='objectfile';
$path=strtolower($path );
if(empty($path)){
$oil=null;
return $oil;
}
$caching=( defined('PLIBRARY_NODE_CACHING')?PLIBRARY_NODE_CACHING : 1 );
$caching=($caching > 0 )?'cache' : 'static';
if(!isset($needToLoadMainTableFlag)){
$libraryCacheC=WCache::get();
$needToLoadMainTableFlag=$libraryCacheC->get('ModelLoadAll','Model',$caching );
if(!$needToLoadMainTableFlag){
WLoadFile('load', JOOBI_LIB_CORE.'model'. DS );
$loadModels=new WModel_Load();
$loadModels->load();
$libraryCacheC->set('ModelLoadAll', true ,'Model',$caching );
}}
$tempdata=WCache::getObject($path, 'Model',$caching, true, false, '', null, $showMessage );
if(empty($tempdata)){
$tempdata=WCache::getObject($path, 'Model','static', true, false, '', null, $showMessage );
}
if(empty($return ))$return='objectfile';
if(empty($tempdata)){
if($showMessage){
WMessage::log('model namekey: '.$path, 'error-loading-model');
WMessage::log( print_r( debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ), true), 'error-loading-model');
}
if( in_array($return, array('objectfile','object','objectfields'))){
$a=new WModel();
$a->setQueryType();
return $a;
}else{
return '';
}
}
if(isset($tempdata->$return)){
return $tempdata->$return;
}elseif($return=='data') return $tempdata;
elseif($return=='dataparams'){
return $tempdata;
}
if(!empty($tempdata->pnamekey) && in_array($return, array('objectfile','objectfields'))){
WModel::get($tempdata->pnamekey, 'objectfile', null, false);
if(empty($tempdata->path)){
$className=self::_getClassName($tempdata );
if(!class_exists($className )){
$tempdataParent=WModel::get($tempdata->pnamekey, 'data', null, false);
$classNameParent=self::_getClassName($tempdataParent );
$extendedClass='class '.$className.' extends '.$classNameParent.'{}';
eval($extendedClass );
}
}}
if($tempdata->type==40 )  WLoadFile('category.model.node', JOOBI_DS_NODE );
if(( ! $tempdata->path || $return=='object')){ 
if(empty($className)){
if($tempdata->type==40){
if($modelClassName !='WModel'){
$newClass=new WGenCategoryModel($params );
}else{
$newClass=new Category_Node_model($params );
}
} else  {
$newClass=new $modelClassName($params );
}
}else{
$newClass=new $className($params );
}
$_myFctTemp=substr($tempdata->folder, 0, strpos($tempdata->folder, '.'));
$newClass->_myFct=(!empty($_myFctTemp))?$_myFctTemp : $tempdata->folder;
}else{ 
$namekey=$tempdata->namekey;
$myPos=strpos($namekey, '.');
if($myPos===false){
if($tempdata->type==20){ $myFuntion=substr($tempdata->namekey, 0, -5 );
$myFile='trans';
$className=ucfirst($myFuntion). '_Trans_model';
}else{
$myFuntion=$namekey;
$myFile='node';
$namekey .='.node';
$className=str_replace('.','_',$namekey ). '_model';
}}else{
$myFuntion=substr($namekey, 0 , $myPos );
$myFile=substr($namekey, $myPos+1 );
$className=str_replace('.','_',$namekey ). '_model';
}
$exists=WLoadFile($myFuntion.'.model.'.$myFile, JOOBI_DS_NODE );
if($exists && class_exists($className )){
$newClass=new $className($params );
}else{
if($tempdata->type==40)$newClass=new Category_Node_model($params );
else {
$newClass=new $modelClassName($params );
}
}$newClass->_myFct=$myFuntion;
}
$newClass->_infos=$tempdata;
if(!empty($newClass->_infos))$newClass->_hasModelAndConnection=true;
$newClass->setAs($tempdata->sid );
$newClass->_infos->verifyFields=true;
if($tempdata->type==30){$newClass->_infos->childRemoveNotPresent=true;
$newClass->_infos->childOnlyAddNew=true;
$newClass->_infos->uniqueSilent=true;
$newClass->_infos->verifyFields=false;
}elseif($tempdata->type==20){$useMultipleLang=defined('PLIBRARY_NODE_MULTILANG')?PLIBRARY_NODE_MULTILANG : 0;
if($useMultipleLang){
$newClass->lgid=WUser::get('lgid');
}else{
$useMultipleLangENG=defined('PLIBRARY_NODE_MULTILANGENG')?PLIBRARY_NODE_MULTILANGENG : 1;
$newClass->lgid=($useMultipleLangENG?1: WApplication::userLanguage());
}
}
if('objectfields'==$return && WExtension::exist('design.node')){
$designModelfieldsC=WClass::get('design.modelfields');
$newClass->_FieldsA=$designModelfieldsC->getAllFields($newClass->_infos->sid, true, 'all');
if(!empty($newClass->_FieldsA )){
foreach($newClass->_FieldsA as $field){
if( in_array($field->form, array('output.file','output.media','output.image'))){
if(!empty($field->params)) WTools::getParams($field );
$previewid=new stdClass;
if('output.image'==$field->form){
$previewid->fileType='images';
}elseif('output.file'==$field->form){
$previewid->fileType='files';
}else{
$previewid->fileType='medias';
}$previewid->folder='media';
$previewid->path='field';
$previewid->secure=false;
if(!empty($field->format))$previewid->format=$field->format;
if(!empty($field->maxsize))$previewid->maxSize=$field->maxsize * 1028;
if(!empty($field->maxheight))$previewid->maxHeight=$field->maxheight;
if(!empty($field->maxwidth))$previewid->maxWidth=$field->maxwidth;
if(!empty($field->storage))$previewid->storage=$field->storage;
$newClass->_fileInfo[$field->column]=$previewid;
}}}}
$newClass->setDBConnector(true);
return $newClass;
}
public function instanceExist(){
return (isset($this->_infos)?true : false);
}
public static function modelExist($model){
$sid=WModel::get($model ,'sid', null, false);
return (!empty($sid)?true : false);
}
public static function getID($name){
return WModel::get($name ,'sid', null, false);
}
public static function getElementData($modelName,$eid,$property='data',$lgid=null){
if(empty($modelName)) return false;
$instanceModel=WModel::get($modelName, 'object', null, false);
if(empty($instanceModel)) return false;
return $instanceModel->getInformationInstance($modelName, $eid, $property, $lgid );
}
public function getInformationInstance($modelName,$eid,$property='data',$lgid=null){
$instance=$this->_createFileInstance('loadinfo');
return $instance->getModelInformation($modelName, $eid, $property, $lgid );
}
public function getModelID(){
return (isset($this->_infos->sid)?$this->_infos->sid : 0 );
}
public function hasMemory(){
return (!empty($this->_infos->cachedata)?true : false);
}
public function loadMemory($id,$lgid=null,$convertParams=true){
if(empty($this->_infos->cachedata)){
$namekey=(!empty($this->_infos->namekey)?$this->_infos->namekey : 'nodeDefinedModel');
$this->codeE('The model '.$namekey.' does not support data caching.');
return false;
}
$name=$this->getModelNamekey();
$nameTrans=WModel::modelExist($name.'trans');
if($nameTrans){
$this->makeLJ($name.'trans');
if(empty($lgid)){
$lgid=WUser::get('lgid');
}$key='lg-'.$id.'-'.$lgid;
$this->whereLanguage();
}else{
$key=$id;
}
$this->remember($key, true, 'Model_'.$this->_infos->tablename );
if( is_numeric($id)){
$this->whereE($this->getPK(), $id );
}else{
$this->whereE('namekey',$id );
}
$resultO=$this->load('o');
if($convertParams && !empty($resultO->params)){
WTools::getParams($resultO, 'params');
}
return $resultO;
}
public function getModelNamekey(){
return (!empty($this->_infos->namekey)?$this->_infos->namekey : '');
}
public function getModelInfo($property=null){
if(empty($property )) return $this->_infos;
if(isset($this->_infos->$property)) return $this->_infos->$property;
return false;
}
public function getChild($model,$property='',$special=''){
$key='C'.WModel::get($model, 'sid');
if(empty($property)) return isset($this->$key)?$this->$key : null;
if(empty($special)){
if(!isset($this->$key)) return null;
if( is_object($this->$key)){
return isset($this->$key->$property)?$this->$key->$property: null;
}elseif(is_array($this->$key)){
foreach($this->$key as $oneValue7){
return isset($oneValue7->$property)?$oneValue7->$property: null;
}}
}else{
$hui=$special[$property];
return (isset($this->$hui)?$this->$key->$hui : null );
}
}
public function setChild($model,$property,$value=null){
if(empty($property)) return false;
$modelID=WModel::get($model, 'sid');
if(empty($modelID)) return false;
$key='C'.$modelID;
if(!isset($this->$key)){
$this->$key=new stdClass;
}
if( is_object($this->$key)){
$this->$key->$property=$value;
}elseif(is_array($this->$key)){
foreach($this->$key as $arrKey=> $arrVal){
$arrVal->$property=$value;
$hui=$key[$arrKey];
$this->$hui=$arrVal;
}
if(!empty($this->C)) unset($this->C );
}
return true;
}
public function unsetChild($model,$property='',$element=''){
$key='C'.WModel::get($model,'sid');
if(empty($property))unset($this->$key );
elseif(empty($element)) unset($this->$key->$property );
else {
$hui=$property[$element];
unset($this->$key->$hui );
}
}
public static function getSpecialChild($object,$model,$property){
$key='C'.WModel::get($model,'sid');
return isset($object->$key->$property )?$object->$key->$property: null;
}
public static function unsetSpecialChild($object,$model){
$key='C'. WModel::get($model,'sid');
unset($object->$key );
}
public function setAudit($audit=true){
$auditAllowed=PLIBRARY_NODE_AUDIT;
if(empty($auditAllowed)) return false;
$this->_makeAudit=$audit;
}
public function getAudit(){
return $this->_makeAudit;
}
public function makeLJ($model,$cond1='',$cond2='',$as1=0,$as2=null,$firstOperator='=',$notUsed=''){
$qSet=WModel::get($model, 'object');
if(!is_object($qSet)){
$mainModelID=' id '.$this->getModelID();
if(isset($this->prefix))$mainModelID=$this->prefix . (isset($this->table) && $this->table!=''?'_'.$this->table : '');
$mess=WMessage::get();
$mess->codeE('Could not find the model '.$model.' for the left join you are trying to do with the model '.$mainModelID, array(), 'query');
if( strrpos('library.',$model ) !==false){$cache=WCache::get();
$cache->resetCache();
WMessage::log('reset cache because model not found','cache-error');
WPages::redirect('previous');
}return false;
}
if(empty($cond1))$cond1=($qSet->multiplePK()?$this->getPK() : $qSet->getPK());
$tablename=$qSet->getTableName();
if(empty($tablename)){
$this->_failedQuery=true;
return false;
}
parent::makeLJ($tablename, $qSet->getDBName(), $cond1, $cond2, $as1, $as2, $firstOperator );
}
public function getX($property,$default=null){
$x=$this->_saveState?'_x' : 'x';
if(isset($this->$x)){
$a=$this->$x;
return isset($a[$property] )?$a[$property] : $default;
}return $default;
}
public function save($validate=true,$doNotUse=null){$saveInstance=$this->_createFileInstance('save');
return $saveInstance->save($validate, $this );
}
public function saveItemMoveFile($source='',$destination='',$validate=true,$property=null,$optimize=null){
if('http'==substr($source, 0, 4 )){
$type='url';
$name=$source;
}
$fileNameA=explode('.',$source );
$type=array_pop($fileNameA );
if('youtube'==$type){
$name=implode('.',$fileNameA );
}
if(!empty($type) && !empty($name)){
$uid=(isset($this->uid)?$this->uid : 0 );
$vendid=(isset($this->vendid)?$this->vendid : 0 );
$filesHelperC=WClass::get('files.helper');
$filid=$filesHelperC->saveURLFile($name, $uid, $vendid, $type );
if(empty($filid)) return false;
$this->$property=$filid;
return $this->save();
}
$saveInstance=$this->_createFileInstance('save');
return $saveInstance->saveFileLib($this, $source, $destination, $validate, $property, $optimize );
}
public function validateDate($property,$moreThanNow=false,$default=0){
if(!isset($this->$property)) return false;
if(!empty($this->$property)){
if($this->$property=='0000-00-00 00:00' || $this->$property=='0000-00-00')$this->$property=$default;
elseif(!is_int($this->$property))$this->$property=WApplication::stringToTime($this->$property );
}else{
$this->$property=$default;
}
if($moreThanNow && $this->$property > 0 && $this->$property < ( time() - 86400 )){
$this->$property=0;
$DATE=$property;
$this->adminE('The date ('.$DATE.') has been cancelled because it is in the past!');
}
return true;
}
public function validate(){
return true;
}
public function addValidate(){
return true;
}
public function editValidate(){
return true;
}
public function addExtra(){
return true;
}
public function editExtra(){
return true;
}
public function extra(){
return true;
}
public function copy($eid=null){
$saveInstance=$this->_createFileInstance('save');
return $saveInstance->copy($eid, $this );
}
public function copyValidate(){
return true;
}
public function copyExtra(){
return true;
}
public function copyAll($eid=0){
$saveInstance=$this->_createFileInstance('save');
return $saveInstance->copyAll($eid, $this );
}
public function delete($eid=null,$deleteTrans=false){
$saveInstance=$this->_createFileInstance('delete');
return $saveInstance->delete($eid, $deleteTrans, $this );
}
public function deleteValidate($eid=0){
if($this->_loadElementBeforeDelete)$this->_x=$this->load($eid );
return true;
}
public function deleteExtra($eid=0){
return true;
}
public function deleteAll($eid=0){
$saveInstance=$this->_createFileInstance('delete');
return $saveInstance->deleteAll($eid, $this );
}
public function isOwner(){
$pKey=$this->getPK();
$eid=$this->$pKey;
if(empty($eid)) return false;
$modelM=WModel::get($this->getModelID());
if(is_array($eid))$modelM->whereIn($pKey, $eid );
else $modelM->whereE($pKey, $eid );
$modelM->whereE('uid', Wuser::get('uid'));
return $modelM->exist();
}
public function canView(){
return false;
}
public function canAdd(){
return $this->isOwner();
}
public function canEdit(){
return $this->isOwner();
}
public function canSave(){
return $this->isOwner();
}
public function canDelete(){
return $this->isOwner();
}
public function canCopy(){
return $this->isOwner();
}
public function checkAccess($as=0,$bkbefore=0,$bkafter=0,$operator=0,$column='rolid'){
 $this->whereIn($column, WUser::roles(), $as, false, $bkbefore, $bkafter, $operator );
}
public function deleteParent($eid=null){
return parent::delete($eid );
}
public function noVerify(){
$this->_infos->verifyFields=false;
}
public function getFormData(){
$trk=WGlobals::get( JOOBI_VAR_DATA, array(), '','array');
if(!empty($trk)){
$count=sizeof($trk);
$key=key($trk );
if($count==1 && empty($key)){$truc=$trk[ $key ];
}else $truc=$trk;
$this->addProperties($truc );
}
}
public function noValidate(){
$this->_validate=false;
}
public function singleDelete(){
$this->_singleDelete=true;
}
public function addModel($model){
$myModel=WModel::get($model,'object');
if(empty($myModel)){
$message=WMessage::get();
$message->codeE('Could not find the model '.$model,array(),'query');
return;
}
parent::addTable($myModel->getTableName(), $myModel->getDBName());
}
public function getPossibleTypes(){
return false;
}
public function getDefaultController(){
return null;
}
public function getAdvSearchKey(){
return null;
}
public function getItemTypeColumn(){
return null;
}
public function optimizeImage($optimize=null){
if( is_bool($optimize))$this->_optimizeImg=$optimize;
}
public function check(){
$this->_checkModel=true;
}
public function skipMessage($status=true){
$this->_skipMessage=$status;
}
public function loadSkip(){
return $this->_skipMessage;
}
public function setModelSaveOrder($set=true){
$this->_saveOrder=$set;
}
public function getModelSaveOrder(){
return $this->_saveOrder;
}
public function getFields($justMap=false,$checkval=true){
static $FKresultA=array();
$myKey=serialize($this->getTableId());
if(!isset($FKresultA[$myKey])){
$caching=( defined('PLIBRARY_NODE_CACHING')?PLIBRARY_NODE_CACHING : 1 );
$caching=($caching > 0 )?'cache' : 'static';
$FKresultA[$myKey]=WCache::getObject($myKey, 'Model',$caching, false, true, 'Columns');
}
if(empty($FKresultA[$myKey])) return false;
$onlyColumnA=array();
if($justMap){
foreach($FKresultA[$myKey] as $oneColumn){
$onlyColumnA[]=$oneColumn->name;
}
return $onlyColumnA;
}
$remove=array('params','modified','created','author','uid','core','publish','rolid','checkedout','rolid','level','x','p','c','u','wfiles');
$onlyTruc=false;
if(isset($this->_pubProperties )){
$pKey=$this->getPK();
if(!empty($pKey ))$remove[]=$pKey;
$trucA=array_diff($this->_pubProperties, $remove );
if(empty($trucA)) return '';
$onlyTruc=true;
}
foreach($FKresultA[$myKey] as $oneColumn){
if($checkval && empty($oneColumn->checkval)) continue;
if($onlyTruc){
if( in_array($oneColumn->name, $trucA ))$onlyColumnA[]=$oneColumn;
}else{
$onlyColumnA[]=$oneColumn;
}
}
return $onlyColumnA;
}
public function columnExists($columnName){
$allComumnsA=$this->getFields(false, false);
if(empty($allComumnsA)) return false;
foreach($allComumnsA as $oneColumn){
if($oneColumn->name==$columnName ) return true;
}
return false;
}
public function getTranslatedName($sid=0,$lgid=0){
if(empty($sid))$sid=$this->getModelID();
if($lgid==0){
$lgid=WUser::get('lgid');
}
$key=$sid.'-'.$lgid;
$modelNameTrans=WGlobals::getSession('modelTrans',$key, null );
if(empty($modelNameTrans)){
$modelTransM=WModel::get('library.modeltrans');
$modelTransM->whereE('sid',$sid );
$modelTransM->whereE('lgid',$lgid );
$modelNameTrans=$modelTransM->load('lr','name');
WGlobals::setSession('modelTrans',$key, $modelNameTrans );
}
return $modelNameTrans;
}
public function genNamekey($suffix='',$maxsize=100,$prefix='',$style='alphanumeric'){
$saveInstance=$this->_createFileInstance('save');
$string=$saveInstance->genNamekey($suffix, $maxsize, $prefix, $style );
if( in_array($this->_infos->namekey, array('views.forms','views.menus','views.listings'))){
$string .='-U';
}
if(empty($this->namekey))$this->namekey=$string;
return $string;
}
public function getLeftJoin(){
if( count($this->_as_cd) <2 ) return true;
$reversedAs=array_flip($this->_as_cd );
$baseSID=$reversedAs[0];
foreach($reversedAs as $mySid){
if(!empty($mySid)){
$MyModel=WModel::get($mySid,'object');
$tablesIds[$MyModel->getTableID()]=$MyModel->getTableID();
$modelTable[$MyModel->getTableID()]=$mySid;
}}
if( count($tablesIds) <2 ) return true;
$myKey=serialize($tablesIds);
if(!isset($FKresultA[$myKey])){
$caching=( defined('PLIBRARY_NODE_CACHING')?PLIBRARY_NODE_CACHING : 1 );
$caching=($caching > 0 )?'cache' : 'static';
$FKresultA[$myKey]=WCache::getObject($myKey, 'Model',$caching, true, true, 'ForeignKeys');
}
if(empty($FKresultA[$myKey] ) || count($FKresultA[$myKey] ) < ( count($tablesIds)-1 )){
$message=WMessage::get();
if( count($FKresultA[$myKey] )< ( count($tablesIds)-1 )){
$message->codeW('You have more model Left Joined that there is Foreign Key into your model!', null, false);
$message->codeW('You need to either had some Foeign Key or remove some of your left join / elements.', null, false);
}else{
$modelMe=WModel::get($baseSID);
$tableName=$modelMe->getTableName();
$message->codeW('The foreign key for the table '.$tableName.' is not defined for the following relation:'. implode(',',$tablesIds ), null, false);
$message->codeW('It might also be that you have a form or listing element with the wrong value', null, false);
}
return true;
}
$usedSID=array();
$usedSID[]=$baseSID;
$myindex=1;
$alreadyChoosen=array();
$alreadyChoosen[]=$baseSID;
$newOrderPath=array();
$allPaths=$FKresultA[$myKey];
foreach($FKresultA[$myKey] as $anyKey=> $onePath){
if(!empty($this->_removeFK )){
$continueNow=false;
foreach($this->_removeFK as $FKdbtid=> $FKref_dbtid){
if($onePath->dbtid==$FKdbtid && $onePath->ref_dbtid==$FKref_dbtid){
unset($allPaths[$anyKey] );
$continueNow=true;
break;
}}if($continueNow ) continue;
}
if($modelTable[$onePath->dbtid]==$baseSID || $modelTable[$onePath->ref_dbtid]==$baseSID){
$newOrderPath[]=$onePath;
unset($allPaths[$anyKey] );
}}
$FKresultA[$myKey]=array_merge($newOrderPath, $allPaths );
foreach($FKresultA[$myKey] as $singlePath){
if($modelTable[$singlePath->dbtid]==$baseSID OR in_array($modelTable[$singlePath->dbtid], $usedSID)){
$chosenSid=$modelTable[$singlePath->ref_dbtid];
$qSetpKey1=$singlePath->name;
$qSetpKey2=$singlePath->name2;
$mainSid=$modelTable[$singlePath->dbtid];
}else{
$chosenSid=$modelTable[$singlePath->dbtid];
$qSetpKey1=$singlePath->name2;
$qSetpKey2=$singlePath->name;
$mainSid=$modelTable[$singlePath->ref_dbtid];
}
if( in_array($chosenSid, $alreadyChoosen)) continue;
$alreadyChoosen[]=$chosenSid;
$myindex++;
$usedSID[]=$chosenSid;
$qTrans=WModel::get($chosenSid, 'data');
$myAssOfchosenSid=$this->getAs($chosenSid);
$this->makeLJ($chosenSid, $qSetpKey1, $qSetpKey2, $this->getAs($mainSid), $myAssOfchosenSid );if($qTrans->type==20 ){
$makeWhereOnLanguage=true;
if((!empty($this->_whereValues[$myAssOfchosenSid]->champ )
&& $this->_whereValues[$myAssOfchosenSid]->champ=='lgid')){
$makeWhereOnLanguage=false;
}elseif(!empty($this->_whereOnValues[$myAssOfchosenSid])){
foreach($this->_whereOnValues[$myAssOfchosenSid] as $oneWHereOnNow){
if($oneWHereOnNow->champ=='lgid')$makeWhereOnLanguage=false;
}}
if($makeWhereOnLanguage){
$this->whereLanguage($this->getAs($chosenSid));
}
}
}
return true;
}
public function loadExtSite($type=null,$selects=null){
WLoadFile('external', JOOBI_LIB_CORE.'model'. DS );
$saveInstance=new WModel_external;
return $saveInstance->load($this, $type, $selects );
}
public function dontExportA(){
return $this->_dontExportA;
}
public function addPredefined(&$model){
$pk=$this->getPK();
if(isset($model->$pk )){
$typeValue=$model->$pk;
$predefined=WModel::getElementData($this->getModelID(), $typeValue, 'predefined');
if(empty($predefined)) return false;
$jsonObj=json_decode($predefined );
if(empty($jsonObj)) return false;
foreach($jsonObj as $k=> $v){
if('yn_'==substr($k, 0, 3) && !empty($v )){
$prop=substr($k, 3 );
$valProp='vl_'.$prop;
if(isset($jsonObj->$valProp)){
if('p_'==substr($prop, 0, 2 )){
$model->p[$prop]=$jsonObj->$valProp;
}else{
$model->$prop=$jsonObj->$valProp;
}}
}}
}
}
public function getSQL($path,$showMessage=true){
static $reloadOnlyOnce=true;
$reload=WModel::checkExistFileForInserting($path );
if(empty($path)){
WMessage::log('-ERROR path==false==--'.$path ,  'warning_model');
WMessage::log( self::$WMessageLog, 'warning_model');
return false;
}
$tempdata=WModel::getSQLFromDB($path, $showMessage );
if(empty($tempdata)) return $tempdata;
if($reloadOnlyOnce && !empty($tempdata->reload)){
$namekeyExplodeA=explode('.',  $tempdata->namekey );
$reloadOnlyOnce=false;
$folder=WExtension::get($namekeyExplodeA[0].'.node','folder');
if(!empty($folder )){
$tempYid='#'.$folder.'#'.$tempdata->namekey;
$reload=WModel::checkExistFileForInserting($tempYid );
$tempdata=WModel::getSQLFromDB($path, $showMessage );
if(!empty($tempdata)){
$controllerM=WModel::get('library.model','object');
$controllerM->whereE('sid',$tempdata->sid );
$controllerM->setVal('reload', 0 );
$controllerM->update();
}}
}
if(empty($tempdata->dbname))$tempdata->dbname=JOOBI_DB_NAME;
if(empty($tempdata->addon))$tempdata->addon=WGet::DBType(); 
if(!empty($tempdata->params)){
WTools::getParams($tempdata, 'params');
}
$tempdata->pkey=trim($tempdata->pkey );
$allPK=explode(',',$tempdata->pkey );
$tempdata->primaryKeys=$allPK;
if( sizeof($allPK) > 1){
$tempdata->mpk=true;
}else{
$tempdata->mpk=false;
}
$sid=$tempdata->sid;
if(empty($tempdata->mainmodel)){
$explodeNamekeyA=explode('.',$tempdata->namekey );
$tempdata->mainmodel=$explodeNamekeyA[0];
}
$tempdata->id=$tempdata->sid;
return $tempdata;
  }
  private static function _getClassName($tempdata){
  $namekey=$tempdata->namekey;
  $myPos=strpos($namekey, '.');
    if($myPos===false){
    if($tempdata->type==20){   $myFuntion=substr($tempdata->namekey, 0, -5 );
  $myFile='trans';
  $className=ucfirst($myFuntion). '_Trans_model';
  }else{
      $namekey .='.node';
  $className=str_replace('.','_',$namekey ). '_model';
  }  }else{
      $className=str_replace('.','_',$namekey ). '_model';
  }  
  return $className;
  }    
private static function getSQLFromDB($path,$showMessage){
$keyListModel=array('sid','dbtid','namekey','folder','path','level','rolid','params','publish',
'fields','reload','audit','faicon','pnamekey');
$keyListModel[]='incoming';
$keyListModel[]='outgoing';
$keyListTable=array('name','prefix','group','suffix','pkey','type','dbid','domain','export','noaudit');
$keyListTableAlias=array('tablename','tableprefix','tablegroup','tablesuffix');
static $databaseInfos=null;
if(!isset($databaseInfos)){
$config=WGet::loadConfig();
$databaseInfos=new stdClass;
$databaseInfos->model=new stdClass;
$databaseInfos->model->_infos=new stdClass;
$databaseInfos->table=new stdClass;
$databaseInfos->database=new stdClass;
foreach($config->model as $key=> $value){
$databaseInfos->model->_infos->$key=$value;
}
$dbprefix=substr( JOOBI_DB_NAME, 0, strrpos( JOOBI_DB_NAME, '_') +1 );
$databaseInfos->model->table=$config->model['tablename'];
$databaseInfos->model->database=empty($config->model['dbname'])?'' : str_replace('#__',$dbprefix,$config->model['dbname'] );
$databaseInfos->table->table=$config->table['tablename'];
$databaseInfos->table->database=empty($config->table['dbname'])?'' : str_replace('#__',$dbprefix,$config->table['dbname'] );
$databaseInfos->database->table=$config->db['tablename'];
$databaseInfos->database->database=empty($config->db['dbname'])?'' : str_replace('#__',$dbprefix,$config->db['dbname'] );
}
$modelM=WTable::get($databaseInfos->model->table, $databaseInfos->model->database, null, $databaseInfos->model );
$modelM->select($keyListModel );
$modelM->makeLJ($databaseInfos->table->table, $databaseInfos->table->database, 'dbtid');
$modelM->select($keyListTable, 1, $keyListTableAlias );
if( is_numeric($path)){
$modelM->whereE('sid',$path );
}else{
$modelM->whereE('namekey',$path );
}
$tempdata=$modelM->load('o');
if(empty($tempdata->namekey)){
$aaa=null;
return $aaa;
}
return $tempdata;
}
private static function checkExistFileForInserting(&$path,$folder='model'){
self::$WMessageLog=array();
self::$WMessageLog['model-ID']=$path;
if( is_numeric($path)){
return false;
}elseif( is_string($path)){
if( substr($path, 0, 1 ) !='#'){
$yidOld=$path;
$yidArr=explode('.',$path);
$path="#".$yidArr[0]."#".$yidOld;
}}else{
return false;
}
$viewNamekeyA=explode('#',$path );
static $_cache=array();
if(isset($viewNamekeyA[2]) && ! empty($viewNamekeyA[2])){
$_cache[]=$viewNamekeyA[2];
}
if(!isset($viewNamekeyA[2]) && in_array($viewNamekeyA[1], $_cache )){
$path=$viewNamekeyA[1];
self::$WMessageLog['path 3 ']=$path;
return false;
}else{
 $path=$viewNamekeyA[2];
}
$file=JOOBI_DS_NODE . $viewNamekeyA[1].DS.'data'.DS.$folder.DS.$viewNamekeyA[2].'.cca';$fileFlag=JOOBI_DS_USER.'node'.DS.$viewNamekeyA[1].DS.'data'.DS.$folder.DS.$viewNamekeyA[2].'.cca';if(!file_exists($fileFlag)){
return false;
}
static $readingFileC=null;
if(!isset($readingFileC)){
$readingFileC=WClass::get('library.readingfile');
}
$phpObjectFromFile=$readingFileC->createPhpObjectFromFile($file );
if(!empty($phpObjectFromFile)){
$insertIntoDBFromFile=$readingFileC->doInstallationIntoDb($phpObjectFromFile, $folder );
if($insertIntoDBFromFile  && Library_Readingfile_class::$isFinishSuccessfull){
  $readingFileC->insertIntoPopulateTable();
$statusDel=$readingFileC->createFlagFile($file );
if(!$statusDel){
}}else{
return false;
}
}else{
}
return true;
}
  public function getSQLForeignKeys($myKey){
$tablesIds=unserialize($myKey);
$libraryForeignM=WModel::get('library.foreign','object');
$libraryForeignM->whereIn('dbtid' , $tablesIds );
$libraryForeignM->whereIn('ref_dbtid' , $tablesIds );
$libraryForeignM->where('dbtid','!=','ref_dbtid', 0, 0 );
$libraryForeignM->whereE('publish',1);
$libraryForeignM->makeLJ('library.columns','feid','dbcid', 0, 1 );
$libraryForeignM->makeLJ('library.columns','ref_feid','dbcid', 0, 2 );
$libraryForeignM->select( array('dbtid','ref_dbtid'));
$libraryForeignM->select('name', 1 );
$libraryForeignM->select('name', 2, 'name2');
$libraryForeignM->orderBy('ordering','ASC');
$libraryForeignM->setDistinct();
$libraryForeignM->setLimit( 100 );
$result=$libraryForeignM->load('ol');
return $result;
  }
  public function getSQLColumns($myKey){
  $tablesIds=unserialize($myKey);
$sqlColumnsM=WModel::get('library.columns');
$sqlColumnsM->whereE('dbtid',$tablesIds );
$sqlColumnsM->select( array('dbcid','name','type','size','attributes','pkey','checkval'));
return $sqlColumnsM->load('ol');
  }
private function _createFileInstance($localtion='save'){
static $instance=array();
if(!isset($instance[$localtion] )){
WLoadFile($localtion, JOOBI_LIB_CORE.'model'.DS );
$className='WModel_'.$localtion;
$instance[$localtion]=new $className;
}
return $instance[$localtion];
}
}