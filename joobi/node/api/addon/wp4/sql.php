<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Wp4_Sql_addon extends WClasses {
private $nameQuote='`';
private $valueQuote="'";
private $utf=null;
private $errorNum=0;
private $errorMsg='';
public $sql=null;
private $_resource=null;
private $force=false;
private $_indexResult=false;
protected $_dbInstance=null;
function __construct(){
global $wpdb;
 $this->_dbInstance=$wpdb;
}
function __destruct(){
return true;
}
public function selectDB(){
return $this->_dbInstance->select( JOOBI_DB_NAME );
}
public function hasUTF(){
return true;
}
public function setUTF(){
}
public function version(){
return $this->_dbInstance->db_version();
}
  function indexResult($map){
  $this->_indexResult=$map;
  }
public function valueQuote($text,$dontAddQuote=false){
$valueQuote=$dontAddQuote?'' : $this->valueQuote;
if( is_numeric($text)) return $valueQuote . $text . $valueQuote;
return $valueQuote . esc_sql($text ). $valueQuote;
}
public function escape($arg){
return esc_sql($arg );
}
public function nameQuote($text){
return $this->nameQuote . $text . $this->nameQuote;
}
public function setQuery($query){
$query=str_replace('#__', JOOBI_DB_PREFIX, $query );
$this->_query=$query;
}
public function query(){
$this->_query=str_replace('#__', JOOBI_DB_PREFIX, $this->_query );
return $this->_dbInstance->query($this->_query );
}
public function lastID(){
return 'SELECT LAST_INSERT_ID()';
}
public function loadResult(){
$result=$this->_dbInstance->get_var($this->_query );
return ( is_string($result) && $result!==null?stripslashes($result) : $result );
}
public function loadResultArray(){
if($this->_indexResult){
$resultQuery=$this->_dbInstance->get_results($this->_query );
if(empty($resultQuery)) return array();
$getOtherValue=$resultQuery[0];
$otherValue='';
foreach($getOtherValue as $oneKey=> $oneMap){
if($oneKey !=$this->_indexResult){
$otherValue=$oneKey;
break;
}}
$myIndex=$this->_indexResult;
$result=array();
foreach($resultQuery as $oneREsult){
$result[$oneREsult->$myIndex]=$oneREsult->$otherValue;
}
}else{
$result=$this->_dbInstance->get_col($this->_query );
}
$this->_indexResult=false;
return $result;
}
public function loadObject(){
return $this->_dbInstance->get_row($this->_query );
}
public function loadObjectList(){
$indexResult=is_string($this->_indexResult )?$this->_indexResult : '';
$result=$this->_dbInstance->get_results($this->_query );return $result;
}
public function affectedRows(){
return $this->_dbInstance->rows_affected;
}
public function getErrorNum(){
}
public function getErrorMsg(){
}
public function dbVersion(){
return $this->_dbInstance->db_version();
}
public function emptyTable($table){
return  'TRUNCATE TABLE '.$table;
}
public function union($query1,$query2){
return $query1.' UNION '.$query2;
}
public function deleteTable($table,$ifExist=false){
$query=($ifExist )?' IF EXISTS ' : ' ';
return  'DROP TABLE' .$query.$table;
}
public function createTable($table,$engine='INNODB'){
$query='CREATE TABLE '.$table;
$query .=' ( `id` INT )';
$query .=' ENGINE='.$engine.' /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;';
return $query;
}
public function showTable($table){
return  'SHOW CREATE TABLE '.$table;
}
public function optimizeTable($table){
return 'OPTIMIZE TABLE '.$table;
}
public function repairTable($table){
return 'REPAIR TABLE '.$table;
}
public function showDBTables($database,$fullInfo=false){
$query=($fullInfo)?' STATUS': 'S' ;
return 'SHOW TABLE'.$query.' FROM '.$this->nameQuote($database );
}
public function showDBs(){
return 'SHOW DATABASES';
}
 function showColumns($table){
 return 'SHOW COLUMNS FROM '.$table;
}
public function showIndexes($table){
return  'SHOW INDEX FROM '.$table;
}
public function createDB($database){
return 'CREATE DATABASE '.$database;
}
public function deleteDB($database){
return 'DROP DATABASE ' .$database;
}
public function dropPK($table){
return 'ALTER TABLE '.$table.' DROP PRIMARY KEY;';
}
public function dropIndex($table,$index){
return 'ALTER TABLE '.$table.' DROP INDEX '.$this->nameQuote($index).';';
}
public function dropKey($table,$key){
return 'ALTER TABLE '.$table.' DROP KEY '.$this->nameQuote($key).';';
}
public function insertInetAton($value){
return 'INET_ATON('.$value.')';
}
public function dropColumn($table,$column){
$query='ALTER TABLE '.$table;
$query .='DROP '.$this->nameQuote($column);
return $query;
}
public function changeColumn($table,$next,$previous=null){
$query='ALTER TABLE '.$table;
if(empty($previous)){
$query .=' ADD ';
}else{
$query .=' CHANGE '.$this->nameQuote($previous['name']).' ';
}
$query .=$this->nameQuote($next['name']).' '.$next['type'].' ';
if(!empty($next['size'])){
$query .=' ('.$next['size'].') ';
}
if(!empty($next['attributes'])){
$query .=$next['attributes'].' ';
}
if($next['mandatory']){
$query .=' NOT NULL ';
}
if(isset($next['default']) && strlen($next['default'])>0 && !(!empty($next['autoinc']) && !empty($previous))){
$query .=' DEFAULT \''.$next['default'].'\'';
}
if(!empty($next['autoinc']) && !empty($previous)){
$query .=' AUTO_INCREMENT ';
}
$query .=';';
return $query;
}
public function addPK($table,$arrayPK){
$pkeys='';
foreach($arrayPK as $myPK){
if(!empty($pkeys))$pkeys .=',';
$pkeys .=$this->nameQuote($myPK);
}
return 'ALTER TABLE '.$table.' ADD PRIMARY KEY ('.$pkeys.');';
}
public function addIndex($table,$indexName,$indexFields){
$fields='';
foreach($indexFields as $fieldName){
if(!empty($fields))$fields .=',';
$fields .= $this->nameQuote($fieldName) ;
}
return 'ALTER TABLE '.$table.' ADD INDEX '.$this->nameQuote($indexName).' ('.$fields.');' ;
}
public function addKey($table,$uniqueKey,$uniqueFields){
$fields='';
foreach($uniqueFields as $fieldName){
if(!empty($fields))$fields .=',';
$fields .= $this->nameQuote($fieldName) ;
}
return 'ALTER TABLE '.$table.' ADD UNIQUE KEY '.$this->nameQuote($uniqueKey).' ('.$fields.');' ;
}
}