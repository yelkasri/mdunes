<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Readingfile_class {
private $_someElementFailed=false;
private static $fileHandler=null;
private $nameMainTableName=array(
'layout_node',
'eguillage_node',
'dropset_node',
'model_node',
'extension_node',
'dataset_tables'
);
private $rootPKForFolder=array(
'view'=>'yid',
'controller'=>'ctrid',
'picklist'=>'did',
'model'=>'sid',
'apps'=>'wid',
'db'=>'dbtid'
);
private $TablesWithCoreFieldArr=array(
'layout_listings',
'layout_mlinks',
'layout_multiforms',
'layout_node',
'eguillage_node',
'eguillage_action',
'dropset_node',
'dropset_values',
'model_node',
'extension_node',
'dataset_tables'
);
private $convertTable2ModelA=array(
'eguillage_node'=> 'library.controller',
'eguillage_action'=> 'library.controlleraction',
'layout_node'=> 'library.view',
'layout_dropset'=> 'library.viewpicklist',
'layout_multiforms'=> 'library.viewforms',
'layout_mlinks'=> 'library.viewmenus',
'layout_listings'=> 'library.viewlistings',
'filters_node'=> 'library.viewfilters',
'layout_trans'=> 'library.viewtrans',
'eguillage_trans'=> 'library.controllertrans',
'layout_listingstrans'=> 'library.viewlistingstrans',
'layout_mlinkstrans'=> 'library.viewmenustrans',
'layout_multiformstrans'=>'library.viewformstrans',
'dropset_node'=> 'library.picklist',
'dropset_trans'=> 'library.picklisttrans',
'dropset_values'=> 'library.picklistvalues',
'dropset_valuestrans'=> 'library.picklistvaluestrans',
'model_node'=> 'library.model',
'model_trans'=> 'library.modeltrans',
'extension_node'=> 'apps',
'extension_trans'=> 'appstrans',
'extension_info'=> 'apps.info',
'theme_node'=> 'theme',
'theme_trans'=> 'themetrans',
'mailing_node'=> 'email',
'mailing_trans'=> 'emailtrans',
'dataset_tables'=> 'library.table',
'dataset_columns'=> 'library.columns',
'dataset_foreign'=> 'library.foreign',
'dataset_constraints'=> 'library.constraints',
'dataset_constraintsitems'=>'library.constraintsitems'
);
private $fileName=null;
static $populateData=array();
private static $cache=array();
public static $isFinishSuccessfull=true;
static $mainInsertedIdForAllFile=array();
public function __construct(){
if( self::$fileHandler==null){
  self::$fileHandler=WGet::file();
  self::$fileHandler->displayMessage(false);
}}
public function deleteFile($pathtofile){
return $this->createFlagFile($pathtofile );
}
public function createFlagFile($pathtofile){
if(empty($pathtofile) || !is_string($pathtofile)){
WMessage::log("-error message --".print_r("not valid params", true) ,  'error_'.__FUNCTION__ );
WMessage::log("-error pathtofile --".print_r($pathtofile, true) ,  'error_'.__FUNCTION__ );
WMessage::log("-error return --".print_r("false", true) ,  'error_'.__FUNCTION__ );
return false;
}
$newLocation=str_replace( JOOBI_DS_NODE, DS . JOOBI_DS_USER.'node'.DS, $pathtofile );
return self::$fileHandler->write($newLocation, '_');
}
public function doInstallationIntoDb($phpObject,$folder){
if(empty($phpObject)) return false;
$key=md5(serialize($phpObject ));
foreach($phpObject as $dbNameStr=> $insertDataObjOrArr){
$dbNameForSqlQuery=$dbNameStr;
if( is_object($insertDataObjOrArr)){
$NotUsedArrayA=array();
$insertId=$this->_setIntoDB($dbNameForSqlQuery, $insertDataObjOrArr, $folder, $NotUsedArrayA );
if(!$insertId){
if($this->_someElementFailed){
WMessage::log('First time failed: '.$dbNameStr, 'error_'.__FUNCTION__ );
$this->_someElementFailed=false;
$insertId=$this->_setIntoDB($dbNameForSqlQuery, $insertDataObjOrArr, $folder, $NotUsedArrayA );
}
if(!$insertId){
$traceMeO=new stdClass;
$traceMeO->insertId=$insertId;
$traceMeO->folder=$folder;
$traceMeO->dbNameForSqlQuery=$dbNameForSqlQuery;
$traceMeO->dbNameStr=$dbNameStr;
$traceMeO->insertDataObjOrArr=$insertDataObjOrArr;
$traceMeO->NotUsedArrayA=$NotUsedArrayA;
WMessage::log($traceMeO, 'error_'.__FUNCTION__ );
self::$isFinishSuccessfull=false;
return false;
}else{
self::$mainInsertedIdForAllFile[$key]=$insertId;
self::$cache[$folder][$insertDataObjOrArr->namekey]=$insertId;
}
}else{
self::$mainInsertedIdForAllFile[$key]=$insertId;
self::$cache[$folder][$insertDataObjOrArr->namekey]=$insertId;
}
  }elseif(is_array($insertDataObjOrArr)){
$sqlInstanceM=$this->_getInstanceDb($dbNameForSqlQuery );
$listNamekeysArr=array();
$maybeCrossTable=$sqlInstanceM->getType();
if($maybeCrossTable !=30){
$dataFromDBAboutThistableAndPKArr=$this->_getDataFromDBUsingMainPK($dbNameForSqlQuery, $key, $folder );
if(!empty($dataFromDBAboutThistableAndPKArr)){
$myPK=$sqlInstanceM->getPK();
foreach($dataFromDBAboutThistableAndPKArr as $oneObj){
if(!empty($oneObj->namekey) && isset($oneObj->$myPK)){
$listNamekeysArr[$oneObj->namekey]=$oneObj->$myPK;
}else{
WMessage::log( "-error phpObject --".print_r($phpObject, true) ,  'error-getnamekey');
WMessage::log( "-error oneObj --".print_r($oneObj, true) ,  'error-getnamekey');
WMessage::log( "-error dataFromDBAboutThistableAndPKArr --".print_r($dataFromDBAboutThistableAndPKArr, true) ,  'error-getnamekey');
}}}
}
foreach($insertDataObjOrArr as $oneObjForInsert){
$oneObjForInsert->fileMainInsertedKey=$key;
$insertId=$this->_setIntoDB($dbNameForSqlQuery, $oneObjForInsert, $folder, $listNamekeysArr );
if(!$insertId){
if($this->_someElementFailed){
$this->_someElementFailed=false;
$insertId=$this->_setIntoDB($dbNameForSqlQuery, $oneObjForInsert, $folder, $listNamekeysArr );
}
if(!$insertId){
$traceMeO=new stdClass;
$traceMeO->dbNameStr=$dbNameStr;
$traceMeO->insertDataObjOrArr=$insertDataObjOrArr;
$traceMeO->insertId=$insertId;
WMessage::log($traceMeO, 'error_'.__FUNCTION__ );
self::$isFinishSuccessfull=false;
return false;
}
}
}
if($maybeCrossTable !=30 && !empty($listNamekeysArr)){
$this->_deleteExtraDataFromDB($dbNameForSqlQuery , $listNamekeysArr );
}
}
}
return true;
}
public function insertIntoPopulateTable(){
$data=self::$populateData;
if(empty($data)) return true;
self::$populateData=array();
$translationPopulateM=WModel::get('translation.populate');
$result=$translationPopulateM->replaceMany( array('dbcid','eid','imac','wid'), $data );
if(!$result){
WMessage::log("errorinserting many was fault --".print_r($data, true), 'error_insertIntoPopulateTable');
}
}
public function createPhpObjectFromFile($pathtofile,$typefile="json"){
static $fileArr=array();
if( in_array($pathtofile, $fileArr )) return false;
if(!is_string($pathtofile) || empty($pathtofile) || !is_string($typefile)){
WMessage::log("-error message --".print_r("not valid params", true) ,  'error_'.__FUNCTION__ );
WMessage::log("-error pathtofile --".print_r($pathtofile, true) ,  'error_'.__FUNCTION__ );
WMessage::log("-error return  --".print_r("false", true) ,  'error_'.__FUNCTION__ );
return false;
}
$returnPhpObj=null;
if(!self::$fileHandler->exist($pathtofile)){
return false;
}
switch ($typefile){
case "json":
$returnPhpObj=$this->_getJSONFile($pathtofile );
break;
case "xml":
$returnPhpObj=$this->_getXMLFile($pathtofile );
break;
}
if(empty($returnPhpObj)){
$message=WMessage::get();
$message->codeE('We could not find or read the file '.$pathtofile );
}
$this->fileName=$pathtofile;
$fileArr[]=$pathtofile;
return $returnPhpObj;
}
private function _setIntoDB($dbNameForSqlQuery,$insertDataO,$folderName,&$alreadyExistNamekeysArr){
$sqlInstanceM=$this->_getInstanceDb($dbNameForSqlQuery );
if( in_array($dbNameForSqlQuery, $this->nameMainTableName )){
return $this->_insertMainObject($dbNameForSqlQuery, $insertDataO, $folderName );
}elseif($sqlInstanceM->getType()==30){
return $this->_insertCrossObject($dbNameForSqlQuery, $insertDataO, $folderName );
}else{
return $this->_insertDependencyObject($dbNameForSqlQuery, $insertDataO, $folderName, $alreadyExistNamekeysArr );
}
}
private function _insertMainObject($dbNameForSqlQuery,$insertDataO,$folderName){
$sqlInstanceM=$this->_getInstanceDb($dbNameForSqlQuery );
if(empty($sqlInstanceM)){
WMessage::log("-error dbNameForSqlQuery --".print_r($dbNameForSqlQuery, true) ,  'error_cannotcreate-sql-instance');
return false;
}
$modelNameStr=$this->_getInstanceDb($dbNameForSqlQuery, false);
$transModelName=$modelNameStr.'trans';
$tableTransName=array_search($transModelName, $this->convertTable2ModelA );
if(!empty($tableTransName)){
$sqlInstanceMTrans=$this->_getInstanceDb($tableTransName );
}
$dataInDbObj=$this->_getExistDataFromDb($dbNameForSqlQuery, $insertDataO );
if(empty($dataInDbObj)){
WMessage::log( "-error _getExistDataFromDb --" . print_r($dbNameForSqlQuery, true). print_r($insertDataO, true), 'error__insertMainObject');
return false;
}
$this->_unsetExtraPropertyFromInsertingObj($insertDataO );
$status=$this->_replaceNotNormalValueToNormal($dbNameForSqlQuery, $insertDataO );
if(empty($status)){
WMessage::log( "-error _replaceNotNormalValueToNormal --" . print_r($dbNameForSqlQuery, true). print_r($insertDataO, true), 'error__insertMainObject');
return false;
}
$transObject=new stdClass;
if(!empty($insertDataO->trans)){
$transObject=$insertDataO->trans;
unset($insertDataO->trans);
}
$sqlInstanceM->resetAll();
$mainPK=$this->rootPKForFolder[$folderName];
if(!$dataInDbObj->issetIntoDB){
$insertedID=$this->_processInsertingToDB($sqlInstanceM, $insertDataO, $mainPK, $dbNameForSqlQuery );
if($insertedID && !empty($transObject) && !empty($sqlInstanceMTrans)){
$transObject->$mainPK=$insertedID;
$transObject->lgid=1;
$transObject->fromlgid=1;
$statusInsertingToTransTable=$this->_processUpdatingDBTrans($sqlInstanceMTrans, $transObject, $mainPK, true);
if(!$statusInsertingToTransTable){
WMessage::log("-error transObject 1--".print_r($transObject, true),  'error-insertingTrans'); WMessage::log("-error mainPK --".print_r($mainPK, true) ,  'error-insertingTrans');
}else{
$transObject->eid=$insertedID;
if(!empty($insertDataO->wid)) self::$cache['wid'][$this->fileName]=$insertDataO->wid;
$this->_prepareObjectForInsertIntoPopulateTable($tableTransName, $transObject );
}
}
return $insertedID;
}
if($dataInDbObj->issetIntoDB && $dataInDbObj->CoreFieldValue){
$isUpdated= $this->_processUpdatingDB($sqlInstanceM , $insertDataO, $mainPK, $dbNameForSqlQuery);
if($isUpdated && !empty($transObject))
{
 $transObject->$mainPK=$dataInDbObj->$mainPK;
 $isUpdatedTransTable=$this->_processUpdatingDBTrans($sqlInstanceMTrans, $transObject, $mainPK, false);
 if(!$isUpdatedTransTable )
 {
WMessage::log("-error insertDataObjOrArr --".print_r($insertDataO, true) ,  'error_updatingMaintabletrans');
WMessage::log("-error mainPK --".print_r($mainPK, true) ,  'error_updatingMaintabletrans');
} return $dataInDbObj->$mainPK;
}
else if($isUpdated && empty($transObject))
{
return $dataInDbObj->$mainPK;
}
else
{
WMessage::log("-error insertDataObjOrArr --".print_r($insertDataO, true) ,  'error_updatingmaintable');
WMessage::log("-error mainPK --".print_r($mainPK, true) ,  'error_updatingmaintable');
return false;
}
}
return $dataInDbObj->$mainPK;
}
private function _insertDependencyObject($dbNameForSqlQuery,$insertDataO,$folderName,&$alreadyExistNamekeysArr){
$sqlInstanceM=$this->_getInstanceDb($dbNameForSqlQuery);
if(empty($sqlInstanceM)){
WMessage::log("-error dbNameForSqlQuery --".print_r($dbNameForSqlQuery, true) ,  'error_cannotcreate-sql-instance');
WMessage::log("-error sqlInstanceMTrans --".print_r($sqlInstanceMTrans, true) ,  'error_cannotcreate-sql-instance');
return false;
}
$modelNameStr=$this->_getInstanceDb($dbNameForSqlQuery, false);
$transModelName=$modelNameStr.'trans';
$tableTransName=array_search($transModelName, $this->convertTable2ModelA );
if(!empty($tableTransName)){
$sqlInstanceMTrans=$this->_getInstanceDb($tableTransName);
}else{
$sqlInstanceMTrans=null;
}
if(!empty($insertDataO->fileMainInsertedKey)){
$mainInsertedIDForAllFile=self::$mainInsertedIdForAllFile[$insertDataO->fileMainInsertedKey];
unset($insertDataO->fileMainInsertedKey);
}else{
$mainInsertedIDForAllFile='';
}
$mainPK=$this->rootPKForFolder[$folderName];
$myPK=$sqlInstanceM->getPK();
$myPKArr=explode(',',$myPK );
$myPKArrZero=$myPKArr[0];
$insertDataO->$mainPK=$mainInsertedIDForAllFile;
$this->_unsetExtraPropertyFromInsertingObj($insertDataO );
$status=$this->_replaceNotNormalValueToNormal($dbNameForSqlQuery, $insertDataO );
if(empty($status)){
WMessage::log( "-error _insertDependencyObject --" . print_r($dbNameForSqlQuery, true). print_r($insertDataO, true), 'error_insertDependencyObject');
return false;
}
$transObject=null;
if(!empty($insertDataO->trans)){
$transObject=$insertDataO->trans;
unset($insertDataO->trans);
}
$sqlInstanceM->resetAll();
if(!empty($alreadyExistNamekeysArr) && in_array($insertDataO->namekey, array_keys($alreadyExistNamekeysArr))){
$PKValueForThisTableAndThisObj=$alreadyExistNamekeysArr[$insertDataO->namekey]; unset($alreadyExistNamekeysArr[$insertDataO->namekey] );
$isUpdated=$this->_processUpdatingDB($sqlInstanceM ,$insertDataO, $mainPK, $dbNameForSqlQuery );
if($isUpdated && (!empty($transObject))){
 $transObject->$myPKArrZero=$PKValueForThisTableAndThisObj;
 $isUpdatedTransTable=$this->_processUpdatingDBTrans($sqlInstanceMTrans, $transObject, $myPKArrZero, false);
 if(!$isUpdatedTransTable){
WMessage::log("-error insertDataObjOrArr --".print_r($insertDataO, true) ,  'error_updatingDependancytabletrans');
WMessage::log($transObject ,  'error_updatingDependancytabletrans');
WMessage::log("-error mainPK --".print_r($mainPK, true) ,  'error_updatingDependancytabletrans');
 }
 return true;
}elseif($isUpdated && (empty($transObject))){
return true;
}else{
WMessage::log("-error insertDataObjOrArr --".print_r($insertDataO, true) ,  'error_updatingmaintable');
WMessage::log("-error mainPK --".print_r($mainPK, true) ,  'error_updatingmaintable');
return false;
}
}else{
$insertedID=$this->_processInsertingToDB($sqlInstanceM, $insertDataO, $myPKArrZero, $dbNameForSqlQuery );
if($insertedID && !empty($transObject) && !empty($sqlInstanceMTrans)){
$transObject->$myPKArrZero=$insertedID;
$transObject->lgid=1;
$transObject->fromlgid=1;
$statusInsertingToTransTable=$this->_processUpdatingDBTrans($sqlInstanceMTrans , $transObject, $myPKArrZero, true);
if(!$statusInsertingToTransTable){
WMessage::log("-error transObject 2--".print_r($transObject, true) ,  'error-insertingTrans');
WMessage::log("-error mainPK --".print_r($mainPK, true) ,  'error-insertingTrans');
}else{
$transObject->eid=$insertedID;
$this->_prepareObjectForInsertIntoPopulateTable($tableTransName, $transObject );
}
}
return true;
}
}
private function _insertCrossObject($dbNameForSqlQuery,$insertDataO,$folderName){
$sqlInstanceM=$this->_getInstanceDb($dbNameForSqlQuery);
if(empty($sqlInstanceM)){
WMessage::log("-error dbNameForSqlQuery --".print_r($dbNameForSqlQuery, true) ,  'error-cannotcreate-sql-instance');
return false;
}
if(empty($insertDataO->fileMainInsertedKey)){
WMessage::log( "fileMainInsertedKey not define for the following object" ,  'error-fileMainInsertedKey');
WMessage::log($insertDataO ,  'error-fileMainInsertedKey');
return false;
}
$mainInsertedIDForAllFile=self::$mainInsertedIdForAllFile[$insertDataO->fileMainInsertedKey];
unset($insertDataO->fileMainInsertedKey);
$mainPK=$this->rootPKForFolder[$folderName];
$myPK=$sqlInstanceM->getPK();
$myPKArr=explode(',',$myPK );
$insertDataO->$mainPK=$mainInsertedIDForAllFile;
$status=$this->_replaceNotNormalValueToNormal($dbNameForSqlQuery, $insertDataO );
if(empty($status)) return false;
$sqlInstanceM->resetAll();
static $_dontDeleteAnyMoreA=array();
$key=$mainPK.'-'.$insertDataO->$mainPK;
if(empty($_dontDeleteAnyMoreA[$key])){
$sqlInstanceM->whereE($mainPK, $insertDataO->$mainPK );
$_dontDeleteAnyMoreA[$key]=$sqlInstanceM->delete();
}
if(empty($_dontDeleteAnyMoreA[$key] )){
WMessage::log("-error mainPK --".print_r($mainPK, true) ,  'error_deletefromcrosstable');
WMessage::log("-error insertDataObjOrArr --".print_r($insertDataO, true) ,  'error_deletefromcrosstable');
WMessage::log("-error sqlInstanceM --".print_r($sqlInstanceM, true) ,  'error_deletefromcrosstable');
}
$sqlInstanceM->resetAll();
foreach($insertDataO as $sqlfieldname=> $sqlvalue){
$sqlInstanceM->$sqlfieldname=$sqlvalue;
}
$isInserted=$sqlInstanceM->insertIgnore();
if(!$isInserted){
WMessage::log("-error insertDataObjOrArr --".print_r($insertDataO, true) ,  'error_inserttocrosstable');
WMessage::log("-error sqlInstanceM --".print_r($sqlInstanceM, true) ,  'error_inserttocrosstable');
return false;
}
return true;
}
private function _processUpdatingDB($sqlInstanceM,$insertDataO,$mainPK,$dbNameForSqlQuery){
foreach($insertDataO as $sqlName=> $sqlValue){
if(($sqlName==$mainPK) ||  ($sqlName=='namekey')){
$sqlInstanceM->whereE($sqlName, $sqlValue);
continue;
}
if('core' !=$sqlName)$sqlInstanceM->setVal($sqlName, $sqlValue );
}
if( in_array($dbNameForSqlQuery, $this->TablesWithCoreFieldArr )){
$sqlInstanceM->whereE('core', 1 );
}
$status=$sqlInstanceM->update();
if(!$status){
WMessage::log("-error updateDataObjOrArr --".print_r($insertDataO, true) ,  'error_updating');
WMessage::log("-error mainPK --".print_r($mainPK, true) ,  'error_updating');
WMessage::log("-error dbNameForSqlQuery --".print_r($dbNameForSqlQuery, true) ,  'error_updating');
WMessage::log("-error sqlInstanceM --".print_r($sqlInstanceM, true) ,  'error_updating');
}return $status;
}
private function _loadTranslationFromImac($imacA,$lgid,$lgCode){
static $translationLanguageM=array();
if(empty($imacA)) return false;
if(empty($translationLanguageM[$lgid])){
$translationLanguageM[$lgid]=WModel::get('translation.'.$lgCode, 'object', null, false);
}
if(empty($translationLanguageM[$lgid])) return false;
$translationLanguageM[$lgid]->whereIn('imac',$imacA );
$translationLanguageM[$lgid]->select('imac');
$translationLanguageM[$lgid]->select('text');
$imacTextA=$translationLanguageM[$lgid]->load('ol');
return $imacTextA;
}
private function _processUpdatingDBTrans($sqlInstanceM,$insertDataO,$mainPK,$insert=false){static $installedLanguagesA=array();
if(empty($sqlInstanceM) || empty($insertDataO->$mainPK)) return false;
if(empty($installedLanguagesA)){
$languageM=WTable::get('language_node','main_userdata','lgid');
if(!$languageM->tableExist() || ! $languageM->isReady())$languageM=WTable::get('joobi_languages','main_userdata','lgid');
$languageM->whereE('publish', 1 );
$languageM->setLimit( 300 );
$installedLanguagesA=$languageM->load('ol',array('code','lgid'));
if(empty($installedLanguagesA)){
WMessage::log('No language could be found!!!!' ,  'error_processUpdatingDBTrans');
return false;
}
}
$TC2LoadA=array();
foreach($insertDataO as $sqlName=> $sqlValue){
if(empty($sqlValue)) continue;
if( substr($sqlName, 0, 3 )== 'TC_'){
$TC2LoadA[$sqlName]=$sqlValue;
}}
$oneLang=WLanguage::get('en',array('lgid','code'));
if(empty($oneLang )){
$oneLang=new stdClass;
$oneLang->lgid=1;
$oneLang->code='en';
}$status=$this->_insertTranslation($sqlInstanceM, $insertDataO, $TC2LoadA, $oneLang, $mainPK, $insert );
if(!empty($installedLanguagesA)){
foreach($installedLanguagesA as $oneLang){
$lgid=$oneLang->lgid;
if(empty($lgid)) continue;
$status=$this->_insertTranslation($sqlInstanceM, $insertDataO, $TC2LoadA, $oneLang, $mainPK, $insert );
}
}else{
WMessage::log('No language could be found!!!!' ,  'error_processUpdatingDBTrans');
}
if(!$status){
WMessage::log('status query failed',  '_processUpdatingDBTrans');
WMessage::log($insertDataO ,  '_processUpdatingDBTrans');
WMessage::log($mainPK ,  '_processUpdatingDBTrans');
WMessage::log($insertDataO ,  '_processUpdatingDBTrans');
}
return $status;
}
private function _insertTranslation($sqlInstanceM,$insertDataO,$TC2LoadA,$oneLang,$mainPK,$insert){
static $englishTranslatedA=array();
$langCode=$oneLang->code;
if(empty($oneLang->lgid)) return false;
$translatedA=$this->_loadTranslationFromImac($TC2LoadA, $oneLang->lgid, $langCode );
$orderTranslatedA=array();
if(!empty($translatedA)){
foreach($translatedA as $oneResultTrans){
$orderTranslatedA[$oneResultTrans->imac]=$oneResultTrans->text;
}}
if('en'==$langCode){
$englishTranslatedA=$orderTranslatedA;
}else{
foreach($englishTranslatedA as $keyEnglish=> $oneEnglish){
if(empty($orderTranslatedA[$keyEnglish]))$orderTranslatedA[$keyEnglish]=$oneEnglish;
}}
$sqlInstanceM->resetAll();
if(!empty($TC2LoadA)){
foreach($TC2LoadA as $keyName=> $oneIMAC){
$map=substr($keyName, 3 );
$val2Inserrt=(!empty($orderTranslatedA[$oneIMAC])?$orderTranslatedA[$oneIMAC] : '');
$sqlInstanceM->setVal($map, $val2Inserrt );
}}else{
foreach($insertDataO as $sqlName=> $sqlValue){
if( substr($sqlName, 0, 3 )== 'TC_'){
$newsqlName=substr($sqlName, 3 );
$sqlInstanceM->setVal($newsqlName, '');
}else{
continue;
}}
}
$sqlInstanceM->setVal('auto', 1 );
$sqlInstanceM->setVal('fromlgid', 1 );if($insert){
 $sqlInstanceM->setVal('lgid',$oneLang->lgid );
 $sqlInstanceM->setVal($mainPK,  $insertDataO->$mainPK );
  $status=$sqlInstanceM->replace();
}else{
$sqlInstanceM->setVal('lgid',$oneLang->lgid );
$sqlInstanceM->setVal($mainPK,  $insertDataO->$mainPK );
$sqlInstanceM->where('auto','=', 1 );$sqlInstanceM->whereE('lgid',$oneLang->lgid );
$sqlInstanceM->whereE($mainPK,  $insertDataO->$mainPK );
$status=$sqlInstanceM->updateInsert();
}
return $status;
}
private function _processInsertingToDB($sqlInstanceM,$insertDataO,$mainPK,$dbNameForSqlQuery){
if(empty($sqlInstanceM)) return false;
$insertedId=false;
foreach($insertDataO as $sqlName=> $sqlValue){
$sqlInstanceM->setVal($sqlName, $sqlValue );
}
$sqlInstanceM->returnId();
if( in_array($dbNameForSqlQuery, $this->TablesWithCoreFieldArr )){
$sqlInstanceM->whereE('core', 1 );
}
$status=$sqlInstanceM->replace();
if($status){
$insertedId=$sqlInstanceM->$mainPK;
}else{
WMessage::log("-error insertDataObjOrArr --".print_r($insertDataO, true),  'error_inserting');
WMessage::log("-error inserting --".print_r($sqlInstanceM, true),  'error_inserting');
}
return $insertedId;
}
private function _getExistDataFromDb($dbNameForSqlQuery,$insertDataO){
$returnObj=new stdClass;
$returnObj->CoreFieldValue=null;
$returnObj->issetIntoDB=false;
$sqlInstanceM=$this->_getInstanceDb($dbNameForSqlQuery );
$myPK=$sqlInstanceM->getPK();
$myPKArr=explode(',',$myPK );
$myPKArrZero=$myPKArr[0];
if(isset($insertDataO->namekey))
{$sqlInstanceM->whereE('namekey',$insertDataO->namekey );
}else{ if(!empty($insertDataO->$myPKArrZero)){
$sqlInstanceM->whereE($myPKArrZero, $insertDataO->$myPKArrZero );
}else{
WMessage::log("-error create select query with this insertDataObjOrArr  --".print_r($insertDataO, true) ,  'error_create_select_query');
WMessage::log("-error create select query with this myPKArr  --".print_r($myPKArr, true) ,  'error_create_select_query');
return false;
}
}
$selectArr=array($myPKArrZero);
if(in_array($dbNameForSqlQuery, $this->TablesWithCoreFieldArr)){
$selectArr[]='core';
}
$returnValueObj=$sqlInstanceM->load('o' , $selectArr );
if(!empty($returnValueObj)){
$returnObj->issetIntoDB=true;
$returnObj->$myPKArrZero=$returnValueObj->$myPKArrZero;
if(!empty($returnValueObj->core)){
$returnObj->CoreFieldValue=$returnValueObj->core;
}else{
$returnObj->CoreFieldValue=1;
}
}
return $returnObj;
}
private function _replaceNotNormalValueToNormal($dbNameForSqlQuery,&$insertDataO,$isTrans=false){
$status=true;
foreach($insertDataO as $sqlName=> $sqlValue){
if(empty($sqlValue)){
$sqlValue=0;
continue;
}
if($sqlName=='trans' && !$isTrans){
continue;
}
if($sqlValue[0]=="#"){
$sqlValue=$this->_procaessReplaceNotValidData($dbNameForSqlQuery, $sqlName, $sqlValue );
if($sqlValue===false || $sqlValue===null){
$sqlValue=0;
$status=true;
}else{
$insertDataO->$sqlName=$sqlValue;
}
}else{
$insertDataO->$sqlName=$sqlValue;
}
}
if(!$status)$this->_someElementFailed=true;
return $status;
}
private function _procaessReplaceNotValidData($dbNameForSqlQuery,$sqlName,$sqlValue){
$sqlValueOriginal=$sqlValue;
if(empty($sqlValue)){
return 0;
}
if($sqlValue==="#" || is_numeric($sqlValue)){
return $sqlValue;
}
$sqlValueArr=explode("#", $sqlValue);
$sqlValue=(empty($sqlValueArr[2]))?$sqlValueArr[1] : $sqlValueArr[2];
$returnValue=false;
switch($sqlName){
case "rolid":
$returnValue=WRole::getRole($sqlValue );
if(empty($returnValue))$returnValue=WRole::getRole('sadmin');
break;
case "wid":
$returnValue=WExtension::get($sqlValue, 'wid');
break;
case "ctrid":
$returnValue=WController::get($sqlValue, 'ctrid');
break;
case "yid":
case "ref_yid":
$returnValue=WView::get($sqlValueOriginal, 'yid');
if(empty($returnValue) && isset( self::$cache['view'][$sqlValue])){
$returnValue=self::$cache['view'][$sqlValue];
}break;
case "sid":
case "ref_sid":
$returnValue=WModel::get($sqlValue, 'sid');
break;
case "dbtid":
$returnValue=$this->_getTableID($sqlValue, 'table');
break;
case "dbcid":
$returnValue=$this->_getTableID($sqlValue, 'columns');
break;
case "ctid":
$returnValue=$this->_getTableID($sqlValue, 'constraints');
break;
case "did":
#joomla#joomla_roles
$returnValue=WView::picklist($sqlValueOriginal , '', null, 'did');
break;
case "lid":
break;
case "mid":
break;
case "fid":
break;
case "parent":
$sqlInstanceM=$this->_getInstanceDb($dbNameForSqlQuery );
$myPkey=$sqlInstanceM->getPK();
$sqlInstanceM->whereE('namekey',$sqlValue );
$returnValueObj=$sqlInstanceM->load('o',$myPkey );
if(!empty($returnValueObj))$returnValue=$returnValueObj->$myPkey;
else $returnValue=false;
break;
default :
$returnValue=false;
}
if(!$returnValue){
$traceMeO=new stdClass;
$traceMeO->sqlName='For the '.$sqlName;
$traceMeO->sqlValueOriginal=$sqlValueOriginal;
$traceMeO->sqlValue=$sqlValue;
$traceMeO->returnValue=$returnValue;
$traceMeO->returnValueType=gettype($returnValue );
$traceMeO->dbNameForSqlQuery=$dbNameForSqlQuery;
if(!empty($myPkey))$traceMeO->myPkey=$myPkey;
WMessage::log($traceMeO,  'error-processReplacement-NotValidData');
}
return $returnValue;
}
private function _getTableID($namekey,$tableName=''){
static $allTableInformationA=array();
$typeOfTable=array();
$typeOfTable['table']='dbtid';
$typeOfTable['columns']='dbcid';
$typeOfTable['constraints']='ctid';
if(!isset($allTableInformationA[$tableName][$namekey] )){
$sqlTableM=WModel::get('library.'.$tableName );
$sqlTableM->whereE('namekey',$namekey );
$allTableInformationA[$tableName][$namekey]=$sqlTableM->load('lr',$typeOfTable[$tableName] );
}
return $allTableInformationA[$tableName][$namekey];
}
private function _getJSONFile($pathtofile){
$contentFile=$this->_getContentFile($pathtofile);
if(empty($contentFile))
{
return $contentFile;
}else{return json_decode($contentFile);
}
}
private function _getXMLFile($pathtofile)
{
$contentFile=$this->_getContentFile($pathtofile );
if(empty($contentFile))
{
return $contentFile;
}else{return $contentFile;
}}
private function _getContentFile($pathtofile){
return self::$fileHandler->read($pathtofile);
}
private function _getInstanceDb($dbname,$returnInstane=true){
if( strpos($dbname, 'library.') !==false){return ($returnInstane)?WModel::get($dbname ) : $dbname;
}
if(isset($this->convertTable2ModelA[$dbname])){
$model=$this->convertTable2ModelA[$dbname];
$return=($returnInstane)?WModel::get($model ) : $model;
return $return;
}
WMessage::log("-error this->convertTable2ModelA--".print_r($this->convertTable2ModelA , true) ,  'error_'.__FUNCTION__.__FUNCTION__ );
WMessage::log("-error dbname--".print_r($dbname, true) ,  'error_'.__FUNCTION__.__FUNCTION__ );
WMessage::log("-error return  --".print_r("false", true) ,  'error_'.__FUNCTION__.__FUNCTION__ );
return false;
}
private function _unsetExtraPropertyFromInsertingObj(&$insertDataO){
if(!empty($insertDataO->parentnamekey))
{
$insertDataO->parent=$insertDataO->parentnamekey;
unset($insertDataO->parentnamekey);
}
}
private function _getDataFromDBUsingMainPK($dbNameForSqlQuery,$key,$folderName){
if(empty(self::$mainInsertedIdForAllFile[$key] ) || empty($this->rootPKForFolder[$folderName])){
WMessage::log("-error dbNameForSqlQuery  --".print_r($dbNameForSqlQuery, true) ,  'error_getDataFromDBUsingMainPK');
WMessage::log("-error key  --".print_r($key, true) ,  'error_getDataFromDBUsingMainPK');
WMessage::log("-error folderName  --".print_r($folderName, true) ,  'error_getDataFromDBUsingMainPK');
WMessage::log("-error folderName  --".print_r($folderName, true) ,  'error_getDataFromDBUsingMainPK');
return false;
}
$sqlInstanceM=$this->_getInstanceDb($dbNameForSqlQuery );
$sqlInstanceM->whereE($this->rootPKForFolder[$folderName], self::$mainInsertedIdForAllFile[$key] );
$tableName=$sqlInstanceM->getTableName(false);
return $sqlInstanceM->load('ol');
}
private function _deleteExtraDataFromDB($dbNameForSqlQuery,$listNamekeysArr){
if(empty($listNamekeysArr)) return true;
$sqlInstanceM=$this->_getInstanceDb($dbNameForSqlQuery );
$arrayKeyA=array_keys($listNamekeysArr);
if(empty($arrayKeyA)) return false;
$myPK=$sqlInstanceM->getPK();
$sqlInstanceM->whereIn('namekey',$arrayKeyA );
if( in_array($dbNameForSqlQuery, $this->TablesWithCoreFieldArr )){
$sqlInstanceM->whereE('core', 1 );
}
$allCoreA=$sqlInstanceM->load('lra',$myPK );
if(empty($allCoreA)) return false;
if( in_array($dbNameForSqlQuery, $this->TablesWithCoreFieldArr)){
$sqlInstanceM->setVal('core', 0 );
$sqlInstanceM->whereIn($myPK, $allCoreA );
$sqlInstanceM->update();
$sqlInstanceM->resetAll();
}
$sqlInstanceM->whereIn($myPK, $allCoreA );
$isDeleted=$sqlInstanceM->delete();
if(!$isDeleted){
WMessage::log( "-error _deleteExtraDataFromDB  --" ,  'error_deleteextradata');
WMessage::log($isDeleted ,  'error_deleteextradata');
WMessage::log( "-error dbNameForSqlQuery  --".print_r($dbNameForSqlQuery, true) ,  'error_deleteextradata');
WMessage::log("-error listNamekeysArr  --".print_r($listNamekeysArr, true) ,  'error_deleteextradata');
WMessage::log("-error sqlInstanceM  --".print_r($sqlInstanceM, true) ,  'error_deleteextradata');
}return $isDeleted;
}
private function _prepareObjectForInsertIntoPopulateTable($tableTransName,$transObj){
if(!isset(self::$cache['wid']) || empty($transObj->eid) || empty(self::$cache['wid'][$this->fileName])){
WMessage::log( "error function _prepareObjectForInsertIntoPopulateTable --" ,  'error_prepareObjectForInsertIntoPopulateTable');
WMessage::log($this->fileName,  'error_prepareObjectForInsertIntoPopulateTable');
WMessage::log($tableTransName,  'error_prepareObjectForInsertIntoPopulateTable');
WMessage::log($transObj,  'error_prepareObjectForInsertIntoPopulateTable');
WMessage::log( @self::$cache['wid'],  'error_prepareObjectForInsertIntoPopulateTable');
return false;
}
$eid=$transObj->eid;
if(empty($eid)) return false;
$dbcidA=$this->_getDbcid($tableTransName , $transObj );
$wid=self::$cache['wid'][$this->fileName];
if(is_array($dbcidA)){
foreach($dbcidA as $name=> $dbcidObj){
if(empty($dbcidObj->name)) continue;
$TC_property='TC_'.$dbcidObj->name;
if(empty($transObj->$TC_property) || empty($dbcidObj->dbcid)) continue;
$arr=array();
$arr[]=$dbcidObj->dbcid;
$arr[]=$eid;
$arr[]=$transObj->$TC_property;
$arr[]=$wid;
self::$populateData[]=$arr;
}
}  else {
WMessage::log("error tableTransName --".print_r($tableTransName, true) ,  'error_prepareObjectForInsertIntoPopulateTable');
WMessage::log("error transObj --".print_r($transObj, true) ,  'error_prepareObjectForInsertIntoPopulateTable');
WMessage::log("error dbcidA --".print_r($dbcidA, true) ,  'error_prepareObjectForInsertIntoPopulateTable');
WMessage::log("error eid --".print_r($eid, true) ,  'error_prepareObjectForInsertIntoPopulateTable');
WMessage::log("error imac --".print_r($imac, true) ,  'error_prepareObjectForInsertIntoPopulateTable');
WMessage::log("error wid --".print_r($wid, true) ,  'error_prepareObjectForInsertIntoPopulateTable');
}
}
private function _getDbcid($tableTransName,$transObj){
static $dbcidA=array();
$nameColumnA=array();
foreach($transObj as $key=> $value){
if( is_numeric($value) || ((strlen($value) > 1) && $value[0]=="#" )) continue;
if( substr($key, 0, 3 )=='TC_')$key=substr($key, 3 );
$nameColumnA[]=$key;
}
if(empty($nameColumnA)) return array();
$tableNameNoPrefix=str_replace('_','.',$tableTransName);
$key=$tableNameNoPrefix.'-'.serialize($nameColumnA );
if(isset($dbcidA[$key] )) return $dbcidA[$key];
$sqlColumnsM=WModel::get('library.columns');
$sqlColumnsM->makeLJ('library.table','dbtid');
$sqlColumnsM->whereE('namekey',$tableNameNoPrefix, 1 );
$sqlColumnsM->whereIn('name',$nameColumnA );
$dbcidObj=$sqlColumnsM->load('ol',array('dbcid','name'));
if(empty($dbcidObj)){
WMessage::log("error tableTransName --".print_r($tableTransName, true) ,  'error_getDbcid');
WMessage::log("error nameColumnA --".print_r($nameColumnA, true) ,  'error_getDbcid');
WMessage::log("error sqlColumnsM --".print_r($sqlColumnsM, true) ,  'error_getDbcid');
}
$dbcidA[$key]=$dbcidObj;
return $dbcidA[$key];
}
}