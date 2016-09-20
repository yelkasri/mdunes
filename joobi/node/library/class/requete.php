<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WQuery extends WObj {
protected $_queringType=true;
var $_infos=null;
var $_log=false;
var $_logfile='queries';
var $_replacePrefix=false;
protected $_debug=false;
var $_microtime=null;
protected $_db=null;
private static $_onlyCheckUserOnce=true;
private static $_avoidLoopGetUser=null;
protected $_modelInstance=false;
function __construct($params=null,$setupDB=true){
parent::__construct();
if(!empty($params) && ( is_object($params) || is_array($params))){
foreach($params as $key=> $value)$this->$key=$value;
}
if(!isset($this->_infos))$this->_infos=new stdClass;
if(empty($this->_infos->tableprefix))$this->_infos->tableprefix=JOOBI_DB_PREFIX;
if(empty($this->_infos->dbname))$this->_infos->dbname=JOOBI_DB_NAME;
if(empty($this->_infos->addon))$this->_infos->addon=WGet::DBType(); 
if($setupDB )  $this->_db=WQuery::getDBConnector($this->_infos->addon, $this->_infos );
if( version_compare( PHP_VERSION, '5')==-1 ) register_shutdown_function( array( &$this, '__destruct'));
}
function __destruct(){
return true;
}
public function setDBConnector($modelType=false){
if(!isset($this->_db))$this->_db=WQuery::getDBConnector( WGet::DBType());$this->_modelInstance=$modelType;
}
  public static function &getDBConnector($dbType='',$params=null){
static $db=null;
if(empty($dbType ))$dbType='framework';
if($dbType=='mysql' && PHP_VERSION > '5.5')$dbType='mysqli';
if('framework'==$dbType){
$db=WAddon::get('api.'.JOOBI_FRAMEWORK.'.sql');
}else{
$db=WAddon::get('library.'.$dbType, $params );
}
return $db;
  }
public static function get($params=null,$setupDB=true,$notUsed1=null,$notUsed2=true,$notUsed3=null){
$instance=new WQuery($params, $setupDB );
$instance->_replacePrefix=true;
return $instance;
}
  public function selectDB(){
  return $this->_db->selectDB();
  }
public function makeT(){
return 'DATABASE';
}
function valueQuote($text,$dontAddQuote=false){
return $this->_db->valueQuote($text, $dontAddQuote );
}
function load($qType,$query){
$this->setQuery($qType, $query );
}
protected function setQuery($qType,$query){
if(!empty($this->_replacePrefix)){
$query=str_replace('`#__','`'.$this->getTablePrefix(), $query );
}
if( method_exists($this->_db, 'setQuery')){
$this->_db->setQuery($query );
}else{
$this->_db->sql=$query;
}
switch ($qType){
  case ('ol'):
$result=$this->_db->loadObjectList();
break;
  case ('o'):
$result=$this->_db->loadObject();
break;
  case ('lr'):
  case ('r'):
$result=$this->_db->loadResult();
break;
  case ('lra'): $result=$this->_db->loadResultArray();
break;
  case ('q'): $result=$this->_db->query();
break;
  case ('qr'): $result=$this->_db->loadObjectList();
break;
break;
  default:
$result=$this->_db->query();
break;
}return $this->_resultHandle($query, $result );
}
function getParam($param,$default=null){
if(isset($this->_infos->$param)){
return $this->_infos->$param;
}return $default;
}
function setParam($param,$value=true){
$this->_infos->$param=$value;
}
  function showDBs(){
$this->setQuery('lra',$this->_db->showDBs());
 return $this->_result;
  }
  function createDB($database){
return $this->load('q',$this->_db->createDB($this->_db->nameQuote($database)) );
  }
  function deleteDB($database){
return $this->load('q',$this->_db->deleteDB($this->_db->nameQuote($database)) );
  }
function existDB($database){
$dbs=$this->showDBs();
if( in_array($database,$dbs)){
return true;
}return false;
}
public function getTableName($addPrefix=true){
$tablePrefix='#__';
$prfx=($addPrefix?$tablePrefix : '');
$prefixLen=strlen($tablePrefix);
if(!empty($this->_infos->tablename)){
$tableName=$this->_infos->tablename;
}else{
return false;
}
if( substr($tableName, 0 , $prefixLen )==$tablePrefix){
return $prfx . substr($tableName, $prefixLen );
}else{
return $prfx . $tableName;
}
}
function getTablePrefix(){
return empty($this->_infos->tableprefix)?JOOBI_DB_PREFIX : $this->_infos->tableprefix;
}
function getDBName(){
return (!empty($this->_infos->dbname)?$this->_infos->dbname : '');
}
function showDBTables($fullInfo=false,$database=''){
$result=($fullInfo)?'ol' : 'lra';
if(empty($database))$database=$this->getDBName();
$query=$this->_db->showDBTables($database, $fullInfo );
$this->setQuery($result, $query );
return $this->_result;
}
public function getAddon(){
return $this->_infos->addon;
}
public function log($bool=true,$file='',$noUsed1=false,$noUsed2=1,$noUsed3=true,$noUsed4=false){
$this->_log=$bool;
if(!empty($file ))$this->_logfile=$file;
}
public function getErrorNum(){
return $this->_db->getErrorNum();
}
public function getErrorMsg(){
return $this->_db->getErrorMsg();
}
public function resetAll(){
}
  function lastId(){
$this->setQuery('lr',$this->_db->lastID());
return $this->_result;
  }
  function affectedRows(){
return $this->_db->affectedRows();
  }
protected function _resultHandle($query,$result=false){
$debugMe=( JOOBI_DEBUGCMS
 || (WPref::load('PLIBRARY_NODE_DBGERR'))
 || (defined('PLIBRARY_NODE_DBGQRY') && PLIBRARY_NODE_DBGQRY )
)?true : false;
$lgQ=WPref::load('PLIBRARY_NODE_LOGQRY');
$gtQ=WPref::load('PLIBRARY_NODE_LOGQRYGUEST');
$logQueries=$this->_log;
if(!$logQueries){
  if($lgQ){  $logQueries=$lgQ;
  }elseif(!IS_ADMIN && $gtQ){  $isRegsitered=WUser::isRegistered();
  $logQueries=( ! $isRegsitered )?$gtQ : $this->_log;
  }}
if($logQueries){
WMessage::log($this->_HTMLFormatSQL($query, 'none', true), $this->_logfile, false);
}
 $show=false;
 if(!WUser::$ready)$uid=0;
 else $uid=$this->_getUserIDOnce();
if($lgQ && !empty($uid)){
$show=true;
}elseif($gtQ && ! IS_ADMIN && $uid==0){
$show=true;
}
if($this->_debug
){
if(!isset($this->_debugTitle))$this->_debugTitle='';
$QueryTime=round((microtime(true)-$this->_microtime ) * 1000, 2);
$QueryExactTime=round((microtime(true) - (isset($this->_microtimeExactQuery)?$this->_microtimeExactQuery : 0 )) * 1000, 2 );
$tableInfo=(!empty($this->_infos->tablename)?' table name: '. $this->makeT() : '');
$this->_debugTitle='';
}else{
if($show){
$count=WGlobals::get('debugTracesCount', 1, 'global');
$SQLHTML=$count.'/ '. $this->_HTMLFormatSQL($query, 'blue'). '<br /><br />';
WGlobals::set('debugTraces',$SQLHTML, 'global','append');
WGlobals::set('debugTracesCount',$count+1, 'global');
}
}
$this->resetAll();
$number=$this->_db->getErrorNum();
$messageText=$this->_db->getErrorMsg();
if(empty($number) && empty($messageText)){
$this->_result=$result;
return true;
}
$message=WMessage::get();
$content='The Query Failed with the error number: '.$number. ' and message: '.$messageText. 'The query is : '.$query . WMessage::showLine('query', false);
$name='database';
if(!method_exists('WMessage','log')){
$content="\n\n".$content."\n\n";
if( defined('JOOBI_INSTALLING')){
$path=JOOBI_DS_ROOT.'error-queries.log';
}else{
$path=JOOBI_DS_USER.'logs'.DS.$name.'.log';
}
$file_handler=WGet::file();
if(is_object($file_handler)){
$file_handler->write($path,$content,'append');
}else{
file_put_contents($path,$content,FILE_APPEND);
}
}else{
$queryHTML=$this->_HTMLFormatSQL($query, 'darkred', false);
WGlobals::set('wz_query_line',$queryHTML, 'globals');
$messageText=$this->_db->getErrorMsg();
$messageHTML='<span style="color: rgb(153, 0, 0);">' .$messageText.'</span><br />';
if($debugMe){$errorMessage2Show='The Query Failed with the error number: '.$number. ' and message: '.$messageHTML. 'The query is :<br />'. $queryHTML . WMessage::showLine('query', true);
$message->codeE($errorMessage2Show, array(), false);
}else{
$message->userE('1206732351CUAZ');
if( WRoles::isAdmin('storemanager'))$message->userE('1212761155FLTM');
}
$queryText=$this->_HTMLFormatSQL($query, 'none', true);
$errorMessage2Show='The Query Failed with the error number: '.$number. ' and message: '.$messageText. "\n\rThe query is :\n\r" . $queryText . WMessage::showLine('query', true);
$message->log($errorMessage2Show, 'queries-failed', false);
}
$this->_result=false;
return false;
}
private function _getUserIDOnce(){
if( self::$_onlyCheckUserOnce){
self::$_onlyCheckUserOnce=false;
self::$_avoidLoopGetUser=WUser::get('uid');
}
return self::$_avoidLoopGetUser;
}
private function _HTMLFormatSQL($query,$color='none',$logFile=false){
$separator=($logFile?"\n\r" : '<br />');
  $replace=array('LEFT JOIN','WHERE','AND','FROM','ORDER BY','GROUP BY');
  $replaceBy=array($separator.'LEFT JOIN',$separator.'WHERE',$separator.'AND',$separator .'FROM',$separator .'ORDER BY',$separator .'GROUP BY');
  $query=str_replace($replace, $replaceBy, $query );
    $query=str_replace('#__', JOOBI_DB_PREFIX, $query );
  if(!$logFile && $color !='none')$query='<span style="color: '.$color.';">'.$query.'</span>';
  return $query;
}
}