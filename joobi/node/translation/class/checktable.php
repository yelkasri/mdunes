<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Translation_Checktable_class extends WClasses {
 function transTableExist($languageCode,$onlyShortTable=true){
if($onlyShortTable)$languageCode=substr($languageCode, 0, 2 );
else {
}
 if($languageCode=='en') return true;
 $exits=WModel::get('translation.'.$languageCode, 'sid', null, false);
 return (empty($exits))?false : true;
 }
 function createTransTable($languageCode,$onlyShortTable=true){
 if(empty($languageCode)) return false;
 if($onlyShortTable)$languageCode=substr($languageCode, 0, 2 );
 if($this->transTableExist($languageCode, $onlyShortTable )) return true;
$tableName=JOOBI_DB_PREFIX.'translation_'.$languageCode;
$nameTable=$tableName;
 $realTable='CREATE TABLE IF NOT EXISTS `'.$nameTable.'` (';
$realTable .=' `imac` varchar(20) NOT NULL,';
$realTable .=' `text` text /*!40100 collate utf8_bin */ NOT NULL,';
$realTable .=' `auto` tinyint(3) unsigned NOT NULL default \'1\',';
$realTable .=' PRIMARY KEY  (`imac`),';
$realTable .=' FULLTEXT KEY `FTXT_translation_'.str_replace('-','_',$languageCode ). '_text` (`text`) ';
$realTable .=') ENGINE=MyISAM  /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/ ;';
 $translationM=WTable::get();
$status=$translationM->load('q',$realTable );
if(!$status ) return false;
$dbName='';
$tableO=WTable::get('translation_'.$languageCode, $dbName );
$createdTabelInfo=$tableO->showTable();
if(empty($createdTabelInfo)) return false;
$table=WModel::get('library.table');
$table->whereE('namekey','translation.en');
$refTable=$table->load('o');
if( substr($tableName, 0, strlen( JOOBI_DB_PREFIX ))==JOOBI_DB_PREFIX){
$refTable->name=substr($tableName, strlen( JOOBI_DB_PREFIX ));
}else{
$refTable->name=$tableName;
}
$refTable->namekey='translation.'.$languageCode;
$refTable->suffix=$languageCode;
$refTable->pkey='imac';
$pkey=$table->getPK();
unset($refTable->$pkey);
foreach($refTable as $key=> $val){
if($key=='export')$val=0;
$table->setVal($key, $val );
}
$table->setIgnore();
$table->insert();
$table->whereE('namekey','translation.'.$languageCode );
$dbtid=$table->load('lr','dbtid');
if(empty($dbtid)){
$message=WMessage::get();
$message->codeE('Trans table not inserted');
return false;
}
$model=WModel::get('library.model');
$model->whereE('namekey','translation.en');
$refModel=$model->load('o');
$refModel->dbtid=$dbtid;
$refModel->namekey=$refTable->namekey;
$refModel->suffix=$languageCode;
$refModel->path=0;
$refModel->folder='translation';
$refModel->pnamekey='';
unset($refModel->sid);
foreach($refModel as $key=> $val){
$model->setVal($key, $val );
}$model->setVal('pnamekey','translation.en');
if(!$model->insertIgnore()){
$message=WMessage::get();
$message->codeE('Trans Model not inserted');
return false;
}
$cache=WCache::get();
$cache->resetCache('Model','translation.'.$languageCode, 'cache');
WCache::getObject('translation.'.$languageCode, 'Model','cache', true, false, '', null, false, true);
return true;
 }
}