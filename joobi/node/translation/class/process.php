<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Translation_Process_class extends WClasses {
var $importStep=0;
private $_dontForceInsert=false;
var $finish_tag=true;
var $tag=true;
var $autoFieldInit=-1;
var $allTables=array();
var $handleMessage=true;
var $importLangs=null;
function __construct(){
parent::__construct();
if(isset($_SESSION['joobi']['import_step'])){
$this->importStep=(int)$_SESSION['joobi']['import_step'];
}
if(!defined('DICTIONNARY_STEP')) define('DICTIONNARY_STEP', 0 );
if(!defined('POPULATION_STEP')) define('POPULATION_STEP', 1 );
if(!defined('ERROR_STEP')) define('ERROR_STEP', 99 );
if(!defined('FINISH_STEP')) define('FINISH_STEP', 10 );
}
public function setDontForceInsert($bool=true){
$this->_dontForceInsert=$bool;
}
public function setTag($bool=true){
$this->tag=$bool;
}
public function setHandleMessage($bool=true){
$this->handleMessage=$bool;
}
function setFinishTag($bool=true){
$this->finish_tag=$bool;
}
public function setStep($step){
$this->importStep=$step;
}
public function setImportLang($obj=null){
if(!empty($obj ) && !is_array($obj ))$obj=array($obj );
Install_Node_install::accessInstallData('set','importLangs',$obj );
}
public function importexec(){
switch($this->importStep){
case DICTIONNARY_STEP:
$this->_dictionnaryImport();
break;
case POPULATION_STEP:
$this->_populate();
break;
default:
break;
}
}
public function triggerPopulate(){
return $this->_populate();
}
private function _dictionnaryImport(){
$autotrigger=WGlobals::get('run', 0 );
$file=JOOBI_DS_TEMP . $autotrigger;
$fileHandler=WGet::file();
if(!$fileHandler->exist($file)){
$this->_message('Could not find the translation file '.$file );
return false;
}
$translationHandler=WClass::get('translation.importlang');
if(!$translationHandler->importDictionary($fileHandler->read($file))){
if(!empty($translationHandler->message ))$this->_message($translationHandler->message );
else $this->_message('Could not import the translation file '.$file);
}
$codeLanguage=$translationHandler->getLanguage();
$this->_setLanguages($codeLanguage );
$this->_message( WText::t('1240842525LJTR') , POPULATION_STEP );
}
private function _setLanguages($codeLanguage){
$languages=WLanguage::get($codeLanguage, array('code','lgid'));
$this->importLang=array($languages );
}
private function _loadAllTables($loadDBTID){
static $tableModel=null;
if(!isset($tableModel))$tableModel=WModel::get('library.table');
$tableModel->whereIn('dbtid',$loadDBTID );
$tableModel->select('pkey');
$tableModel->select('dbtid');
$tableModel->select('name',0,'tablename');
$tableModel->setDistinct();
$allTables=$tableModel->load('ol');
foreach($allTables as $myTable){
$name='`'. JOOBI_DB_PREFIX . $myTable->tablename .'`';  
if(empty($this->allTables[$myTable->dbtid]))$this->allTables[$myTable->dbtid]=new stdClass;
$this->allTables[$myTable->dbtid]->name=$name;
$explode6=explode(',',$myTable->pkey);
$arraDiifA=array_diff($explode6, array('lgid'));
$this->allTables[$myTable->dbtid]->pkey=reset($arraDiifA );
}
}
private function _populate(){
$populateModel=WModel::get('translation.populate');
$populateModel->makeLJ('library.columns','dbcid');
$populateModel->makeLJ('library.table','dbtid','dbtid', 1, 2 );
$populateModel->select('dbtid',1);
$populateModel->select('name',1);
$populateModel->select('dbcid');
$populateModel->where('dbtid','>', 0, 1 );
$populateModel->where('type','=', 20, 2 );
$populateModel->groupBy('dbcid');
$populateFieldsA=$populateModel->load('ol');
if(empty($populateFieldsA)) return false;
$transTable=array();
foreach($populateFieldsA as $oneField){
if(!isset($transTable[$oneField->dbtid]))$transTable[$oneField->dbtid]=array();
$transTable[$oneField->dbtid][]=$oneField;
}
$this->_prepareUpdate($transTable );
if(empty($this->importLangs )){
$languageModel=WModel::get('library.languages');
$languageModel->whereE('publish', 1 );
$languageModel->setLimit( 500 );
$this->importLangs=$languageModel->load('ol',array('code','lgid'));
}
$tablePopulate=$populateModel->makeT();
$priority2directedit=true;
if(empty($priority2directedit))$this->_dontForceInsert=true;
foreach($populateFieldsA as $oneField){
if(empty($oneField->name) || empty($oneField->dbcid)) continue;
if(empty($this->allTables[$oneField->dbtid]->name ) || empty($this->allTables[$oneField->dbtid]->pkey)){
$tableName=WModel::get($oneField->dbtid, 'namekey');
continue;
}
foreach($this->importLangs as $language){
if(empty($language->lgid)) continue;
$languageCodeQuery=!empty($language->code )?$language->code : 'en';
$modelTranslation=WModel::get('translation.'. $languageCodeQuery );
if(!method_exists($modelTranslation,'getTableID')) continue;
$transTableName=trim($modelTranslation->makeT());
if(empty($transTableName)) continue;
$query='UPDATE IGNORE '.$this->allTables[$oneField->dbtid]->name.' AS A LEFT JOIN '.$tablePopulate.' AS B ';
$query .='ON A.`'.$this->allTables[$oneField->dbtid]->pkey.'`=B.`eid` AND B.`dbcid`='.(int)$oneField->dbcid.' ';
$query .='LEFT JOIN '.$transTableName.' AS C ON C.`imac`=B.`imac` ';
$query .='SET A.`'.$oneField->name.'`=C.`text`, A.`auto`=C.`auto` ';
$query .='WHERE ';
if($this->_dontForceInsert)$query .='A.`auto` <=1 AND ';
$query .='C.imac IS NOT NULL AND A.`lgid`='.$language->lgid;
$populateModel->load('q',$query );
}
$query='UPDATE IGNORE '.$this->allTables[$oneField->dbtid]->name.' AS A LEFT JOIN '.$this->allTables[$oneField->dbtid]->name.' AS B ';
$query .='ON A.`'.$this->allTables[$oneField->dbtid]->pkey.'`=B.`'.$this->allTables[$oneField->dbtid]->pkey.'` AND B.`lgid`=1 ';
$query .='SET A.`'.$oneField->name.'`=B.`'.$oneField->name.'` WHERE A.`lgid` !=1 AND B.`'.$oneField->name.'` !=\'\' AND (A.`'.$oneField->name.'` IS NULL OR A.`'.$oneField->name.'`=\'\')';
$populateModel->load('q',$query );
}
$trans='Translations successfully installed';
$this->_message($trans, FINISH_STEP );
}
private function _prepareUpdate($fields){
$queries=array();
if(empty($fields)) return false;
$sqlForeignM=WModel::get('library.foreign');
$sqlForeignM->whereE('publish', 1 );
$sqlForeignM->where('map','!=','lgid');
$sqlForeignM->where('map2','!=','lgid');
$sqlForeignM->whereIn('dbtid', array_keys($fields));
$sqlForeignM->select('dbtid');
$sqlForeignM->select('ref_dbtid');
$sqlForeignM->groupBy('dbtid');
$tableHandle=$sqlForeignM->load('ol');
if(empty($tableHandle)){
WMessage::log($fields, 'translation-update-error');
return true;
}
foreach($tableHandle as $oneTable){
$loadDBTID[$oneTable->ref_dbtid]=$oneTable->ref_dbtid;
$loadDBTID[$oneTable->dbtid]=$oneTable->dbtid;
}
$this->_loadAllTables($loadDBTID );
$languageModel=WModel::get('library.languages');
foreach($tableHandle as $oneTable){
if(!isset($this->allTables[$oneTable->ref_dbtid]->name) || empty($this->allTables[$oneTable->ref_dbtid]->name)) continue;
$columns='';
$values='';
$columnsList=array($this->allTables[$oneTable->dbtid]->pkey, 'lgid','auto','fromlgid');
foreach($fields[$oneTable->dbtid] as $field){
if(!in_array($field->name,$columnsList)){
$columns .=',`'.$field->name.'`';
$values.=',\'\'';
$columnsList[]=$field->name;
}
}
$query1='INSERT IGNORE '.$this->allTables[$oneTable->dbtid]->name.' (`'.$this->allTables[$oneTable->dbtid]->pkey.'`,`lgid`,`auto`,`fromlgid`'.$columns.')';
$query2='( SELECT A.`'.$this->allTables[$oneTable->dbtid]->pkey.'`,B.`lgid`,'.$this->autoFieldInit.',1'.$values.' FROM '.$this->allTables[$oneTable->ref_dbtid]->name.' AS A, '.$languageModel->makeT().' AS B WHERE B.publish=1 ) ';
$query=$query1. $query2;
$languageModel->load('q',$query );
}
return true;
}
private function _message($text,$step=99){
if(!$this->handleMessage) return true;
@ob_clean();
if($this->tag){
echo 'BIGMSG['.$text.'] ';
}else{
echo $text;
}
switch($step){
case ERROR_STEP:
unset($_SESSION['joobi']['import_step']);
echo 'JERROR';
break;
case FINISH_STEP:
unset($_SESSION['joobi']['import_step']);
if($this->finish_tag){
$cC=WCache::get();
$cC->resetCache();
WGlobals::setSession('JoobiUser', null, null );
$mess=WMessage::get();
$mess->userS($text );
$mess->store();
echo 'SETUPPAGE['.WPage::routeURL('controller=translation&task=importedit','admin','default').']';
}
break;
default:
$_SESSION['joobi']['import_step']=$step;
break;
}
exit();
}
}
