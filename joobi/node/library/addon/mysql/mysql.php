<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Mysql_addon extends WClasses {
private $nameQuote='`';
private $valueQuote="'";
private $utf=null;
private $errorNum=0;
private $errorMsg='';
public $sql=null;
private $_resource=null;
private $force=false;
private $_indexResult=false;
private static $_connector ; 
private $_links=array();
function __construct(){
static $dbid=array();
parent::__construct();
if(empty($this->dbhost) && !empty($this->dbid)){
if(!isset($dbid[$this->dbid])){
$config=WGet::loadConfig();
}else{
$this->addProperties($dbid[$this->dbid] );
}}
if(empty($this->dbuser)){
$this->dbuser=JOOBI_DB_USER;
$this->dbpassword=JOOBI_DB_PASS;
}
if(empty($this->dbhost ))$this->dbhost=JOOBI_DB_HOSTNAME;
if($this->_connected()){
if(empty($this->dbname))$this->dbname=JOOBI_DB_NAME;
$this->_resource=self::$_connector;
mysql_query( "SET @@SESSION.sql_mode='';", $this->_resource );
$this->utf=$this->hasUTF();
if($this->utf)$this->setUTF();
if($this->force){
$this->sql='CREATE DATABASE IF NOT EXISTS '.$this->nameQuote($this->dbname);
if(!$this->query()){
die('Could not create the database '.$this->dbname.':'.$this->errorNum.' '.$this->errorMsg);
}}
if(!$this->selectDB()) die('Could not select database: '. $this->dbname );
}else{
$this->_connector();
}
return $this->_resource;
}
private function _connected(){
if(isset( self::$_connector ) && is_resource(self::$_connector))
{
return @mysql_ping(self::$_connector);
}
else
{
return false ;
}
}
private function _connector(){
if(empty($this->dbname))$this->dbname=JOOBI_DB_NAME;
$detailMsg="";
if(empty($this->dbhost))$detailMsg .="<br>dbhost is epmty";
if(empty($this->dbuser))$detailMsg .="<br>dbuser is epmty";
if(empty($this->dbpassword))$detailMsg .="<br>dbpwd is epmty";
if(!($this->_links[ $this->dbhost.'.'.$this->dbuser ]=@mysql_connect($this->dbhost, $this->dbuser, $this->dbpassword, true))){
if(!($this->_links[ $this->dbhost.'.'.$this->dbuser ]=mysql_connect($this->dbhost, $this->dbuser, $this->dbpassword )) )
{
die('Could not connect to the database server mysql.'.$detailMsg.'<br> mysql err <br>'.mysql_error());
}
}
self::$_connector= $this->_links[ $this->dbhost.'.'.$this->dbuser ];
$this->_resource=$this->_links[ $this->dbhost.'.'.$this->dbuser ];
mysql_query( "SET @@SESSION.sql_mode='';", $this->_resource );
$this->utf=$this->hasUTF();
if($this->utf)$this->setUTF();
if($this->force){
$this->sql='CREATE DATABASE IF NOT EXISTS '.$this->nameQuote($this->dbname);
if(!$this->query()){
die('Could not create the database '.$this->dbname.':'.$this->errorNum.' '.$this->errorMsg);
}}
if(!$this->selectDB()) die('Could not select database: '. $this->dbname );
unset($this->dbpassword);
}
public function selectDB(){
return mysql_select_db($this->dbname , $this->_resource );
}
public function hasUTF(){
$verParts=explode('.',$this->version());
return ($verParts[0]==5 || ($verParts[0]==4 && $verParts[1]==1 && (int)$verParts[2] >=2));
}
public function setUTF(){
return mysql_set_charset('utf8',$this->_resource );
}
public function version(){
return mysql_get_server_info($this->_resource );
}
  function indexResult($map){
  $this->_indexResult=$map;
  }
public function valueQuote($text,$dontAddQuote=false){
$valueQuote=$dontAddQuote?'' : $this->valueQuote;
if( is_numeric($text) || is_bool($text) || is_null($text)) return $valueQuote . $text . $valueQuote;
elseif( is_string($text)) return $valueQuote . @mysql_real_escape_string($text, $this->_resource ). $valueQuote;
else {
return null;
}}
public function escape($arg){
return @mysql_real_escape_string($arg, $this->_resource );
}
public function nameQuote($text){
if(empty($text)) return '';
if(!is_string($text) || preg_match('|[^a-z0-9#_.-]|i',$text ) !==0){
$message=WMessage::get();
$ip=WUser::get('ip');
$ip=(!empty($ip)?' from the IP '.$ip : '');
$message->log('The '.getType($text). ' below has been detected as a possible SQL injection '.$ip.' :','sql_injection', false, 'query');
$message->log($text, 'sql_injection');
$message->log( debugB(), 'sql_injection');
$message->exitNow('We found an error in a query. Please contact the support with an archive of the logs in the folder your website /joobi/user/logs');
}
return $this->nameQuote . $text . $this->nameQuote;
}
public function setQuery($query){
$this->sql=$query;
}
public function query(){
if(!is_resource($this->_resource)){
return false;
}
$this->sql=str_replace('#__', JOOBI_DB_PREFIX, $this->sql );
$this->errorNum=0;
$this->errorMsg='';
$status=@mysql_query($this->sql, $this->_resource );
if(!$status){
$this->errorNum=(int) mysql_errno($this->_resource );
$this->errorMsg=(string) mysql_error($this->_resource );
return false;
}
return $status;
}
public function lastID(){
return 'SELECT LAST_INSERT_ID()';
}
public function loadResult(){
if(!($myQuery=$this->query())){
return null;
}
$result=null;
if( is_resource($myQuery)){
if($row=mysql_fetch_row($myQuery )){
$result=$row[0];
}}
return ( is_string($result) && $result!==null?stripslashes($result) : $result );
}
public function loadResultArray(){
if(!($myQuery=$this->query())){
return null;
}
$result=array();
if(is_resource($myQuery)){
while ($row=mysql_fetch_row($myQuery )){
$value=is_string($row[0])?stripslashes($row[0]) : $row[0];
if(isset($row[1]) AND !empty($this->_indexResult)){
$result[$row[1]]=$value;
}else{
$result[]=$value;
}
}
$this->_indexResult=false;
@mysql_free_result($myQuery );
}
return $result;
}
public function loadObject(){
if(!($myQuery=$this->query())){
return null;
}
$result=null;
if(is_resource($myQuery))
{
if($object=mysql_fetch_object($myQuery )){
$result=new stdClass;
foreach($object as $k=> $v)
{
$result->$k=is_string($v)?stripslashes($v) : $v;
}
}
}
return $result;
}
public function loadObjectList(){
if(!($myQuery=$this->query())){
return null;
}
$result=array();
if(is_resource($myQuery))
{
while ($object=mysql_fetch_object($myQuery )){
$tmp=new stdClass;
foreach($object as $k=> $v)
{
$tmp->$k=is_string($v)?stripslashes($v) : $v;
}
if(!empty($this->_indexResult)){
$var=$this->_indexResult;
$result[$tmp->$var]=$tmp;
}else{
$result[]=$tmp;
}
}
$this->_indexResult=false;
@mysql_free_result($myQuery );
}
return $result;
}
public function affectedRows(){
if(!is_resource($this->_resource)){
return false;
}
return mysql_affected_rows($this->_resource );
}
public function getErrorNum(){
return $this->errorNum;
}
public function getErrorMsg(){
return $this->errorMsg;
}
public function dbVersion(){
return @mysql_get_client_info();
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
return 'DROP DATABASE '.$database;
}
public function dropPK($table){
return 'ALTER TABLE '.$table.' DROP PRIMARY KEY;';
}
public function dropIndex($table,$index){
return 'ALTER TABLE '.$table.' DROP INDEX '.$this->nameQuote($index). ';';
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
$query .='('.$next['size'].') ';
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
}return 'ALTER TABLE '.$table.' ADD INDEX '.$this->nameQuote($indexName).' ('.$fields.');' ;
}
public function addKey($table,$uniqueKey,$uniqueFields){
$fields='';
foreach($uniqueFields as $fieldName){
if(!empty($fields))$fields .=',';
$fields .= $this->nameQuote($fieldName) ;
}return 'ALTER TABLE '.$table.' ADD UNIQUE KEY '.$this->nameQuote($uniqueKey).' ('.$fields.');' ;
}
}