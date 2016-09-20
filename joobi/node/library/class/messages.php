<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WMessage {
private static $_queueMessage=array();
private static $_latestType=null;
private $_sucessM=null; private $_failedM=null; 
private $_logMessage=''; 
function __construct(){
$this->_failedM='An error occurred';
$this->_sucessM='Successful';
}
public static function get(){
static $instance=null;
if(!isset($instance))$instance=new WMessage();
return $instance;
}
public function setS($successM){
$this->_sucessM=$successM;
}
public function setE($_failedM){
$this->_failedM=$_failedM;
}
public function enqueueMessage($first){
$myMessage=WMessage::get();
$myMessage->addM($first, 'success');
}
public function userS($mess,$variable=array()){
$this->addM($mess, 'success',$variable );
}
public function userN($mess,$variable=array()){
$this->addM($mess, 'notice',$variable );
}
public function userW($mess,$variable=array()){
$this->addM($mess, 'warning',$variable );
}
public function userE($mess,$variable=array()){
$this->addM($mess, 'error',$variable );
}
public function userT($mess,$variable=array()){
$this->addM($mess, 'text',$variable );
}
public function userB($soundName='finish'){
$this->addM($soundName, 'beep');
}
public function reTryS($mess,$variable=array()){
$this->addM($mess, 'success',$variable );
$this->historyS('', null, true);
}
public function reTryN($mess,$variable=array()){
$this->addM($mess, 'notice',$variable);
$this->historyN('1304525968YBL');
}
public function reTryW($mess,$variable=array()){
$this->addM($mess, 'warning',$variable);
$this->historyW('1304525968YBL');
}
public function reTryE($mess,$variable=array()){
$this->addM($mess, 'error',$variable);
return $this->historyE('1304525968YBL');
}
private function codeM($mess,$variable=array(),$type='warning'){
$this->log($mess, 'error_messages_codeM');
if( defined('JOOBI_INSTALLING') || (( JOOBI_DEBUGCMS || WPref::load('PLIBRARY_NODE_DBGERR')) && WRole::hasRole('manager'))){
$this->addM($mess, $type, $variable, true);
}else{
WMessage::log($mess, 'dev-codeE');
}
return true;
}
public function codeW($mess,$variable=array(),$showLine=2){
if($showLine)$mess .=WMessage::showLine($showLine);
$this->codeM($mess, $variable, 'warning');
}
public function codeN($mess,$variable=array(),$showLine=2){
if($showLine)$mess .=WMessage::showLine($showLine);
$this->codeM($mess, $variable, 'notice');
}
public function codeE($mess,$variable=array(),$showLine=2){
if(!empty($showLine))$mess .=WMessage::showLine($showLine);
$this->codeM($mess, $variable, 'error');
}
public function codeS($mess,$variable=array(),$showLine=0){
return true;
if($showLine)$mess .=WMessage::showLine($showLine);
$this->codeM($mess, $variable, 'success');
}
private function adminM($mess,$variable=array(),$type='warning'){
static $message=array();
if(!defined('JOOBI_INSTALLING')){
$roleC=WRole::get();
$status=WRole::hasRole('admin');
$this->log($mess, 'error_messages');
if(!$status){
return;
}}
if(!is_string($mess)){
WMessage::log($mess, 'should-bestring-message');
}
$key=md5($mess); if(!isset($message[$key])){
$this->addM($mess, $type, $variable );
$message[$key]=true;
}
}
public function adminW($mess,$variable=array(),$showLine=0){
if($showLine)
$mess .=WMessage::showLine($showLine);
$this->adminM($mess, $variable, 'warning');
}
public function adminN($mess,$variable=array(),$showLine=0){
if($showLine)
$mess .=WMessage::showLine($showLine);
$this->adminM($mess, $variable, 'notice');
}
public function adminE($mess,$variable=array(),$showLine=0){
if(!empty($showLine))$mess .=WMessage::showLine($showLine);
$this->adminM($mess, $variable, 'error');
}
public function adminS($mess,$variable=array(),$showLine=0){
if($showLine)
$mess .=WMessage::showLine($showLine);
$this->adminM($mess, $variable, 'success');
}
public function persistantM($text,$uid=0,$params=null){
if(!WExtension::exist('main.node')) return false;
$messageC=WClass::get('main.messagequeue', null, 'class', false);
return $messageC->addMessageToQueue($text, $uid, $params );
}
public function historyW($mess='',$variable=array(),$redirectInController=false){
return $this->historyM($mess, $variable, $redirectInController, 'W');
}
public function historyN($mess='',$variable=array(),$redirectInController=false){
return $this->historyM($mess, $variable, $redirectInController, 'N');
}
public function historyE($mess='',$variable=array(),$redirectInController=false){
return $this->historyM($mess, $variable, $redirectInController, 'E');
}
public function historyS($mess='',$variable=array(),$redirectInController=false){
return $this->historyM($mess, $variable, $redirectInController, 'S');
}
private function historyM($mess='',$variable=array(),$redirectInController=false,$typeError='E'){
if( WGlobals::get('wajx', 0, null, 'int') || WGlobals::get('noHistoryRedirect', false, 'global') || 'netcom'==JOOBI_FRAMEWORK_TYPE){
$fct='user'.$typeError;
$this->$fct($mess, $variable );
return false;
}
if('S' !=$typeError){
WMessage::log('An error triggered the history redirect, most of the time this is fine. But if there is a loop this is a good start to look. Message: '.$mess, 'ERROR-history-redirect');
}
$text=self::loadVocab($mess );
if($text !==false){
$mess=$text;
if(!empty($variable)){
$mess=str_replace( array_keys($variable), array_values($variable), $mess );
}}
if(!empty($mess)){
if( in_array($typeError, array('W','E','N','S'))){
$functionName='user'.$typeError;
$this->$functionName($mess );
} else $this->userE($mess );
}
$trk=WGlobals::get( JOOBI_VAR_DATA );
WGlobals::setSession('historyRedirect', JOOBI_VAR_DATA, $trk );
$token=WPage::getSpoof( JOOBI_SPOOF );
$spoof=WGlobals::get($token, 0, 'post');
WGlobals::setSession('historyRedirect',$spoof, $token );
WPages::redirect('previous', false, true, '301');
}
private function _convertArray($variable=array(),$beginingString=''){
$extraURL='';
foreach($variable as $map=> $val1){
if(!is_array($val1)){
if($map!='password' && $map!='repassword')$extraURL .=$beginingString .'['.$map.']='. $val1;
}
else $extraURL .=$this->_convertArray($val1, $beginingString.'['.$map.']');
}return $extraURL;
}
private function addM($message='',$type='',$variableA=array(),$code=false){
static $messageStoreA=array();
$messageMD5=( !is_string($message)?serialize($message) : $message );
if(!empty($variableA)){
$messageMD5 .='-'.serialize($variableA );
}
$typeMD5=( !is_string($type)?serialize($type) : $type );
$key=md5($messageMD5.'|'.$typeMD5 );if(!isset($messageStoreA[$key])){
$messageStoreA[$key]=true;
}else{
return true;
}
if(!empty($message)){
self::$_latestType=$type;
$obj=new stdClass;
$obj->message=( !is_string($message)?print_r($message, true) : $message );
$obj->variable=$variableA;
$obj->code=$code;
self::$_queueMessage[$type][]=$obj;
}
}
public function pop(){
return array_pop( self::$_queueMessage[self::$_latestType] );
}
public function cleanBuffer($file='',$clearMessage=true){
$content='';
$size=@ob_get_length();
if(!empty($size)){
$content=ob_get_contents();
@ob_end_clean();
}
if($clearMessage){
$mess=WMessage::get();
$content .=$mess->getM();
if(!empty($content)){
WMessage::log($content, 'bufferNotEmpty-'.$file );
WMessage::log( debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ), 'bufferNotEmpty-'.$file );
}}
}
function reset(){
self::$_queueMessage=array();
}
public static function translate($message='',$variable=array()){
static $alreadyLoaded=array();
$serial=base64_encode($message);
if(!isset($alreadyLoaded[$serial])){
$text=self::loadVocab($message );
$alreadyLoaded[$serial]=$text;
}else{
$text=$alreadyLoaded[$serial];
}
if($text===false) return $message;
$text=str_replace(array_keys($variable), array_values($variable), $text);
return $text;
}
private function loadMessages($msgSessionId=''){
if(empty($msgSessionId))$msgSessionId=WUser::getSessionId();
$libraryCacheC=WCache::get();
$oldMessage=$libraryCacheC->get('messageID'.$msgSessionId, 'Messages','cache');
if(isset($oldMessage) && is_array($oldMessage)){
foreach($oldMessage as $typekey=> $typeVal){
if(!empty($typeVal)){
foreach($typeVal as $jkey=> $jval){
if(!empty(self::$_queueMessage[$typekey])){
$addMe=true;
foreach( self::$_queueMessage[$typekey] as $indexVal=> $queueVal){
if($queueVal->message==$jval->message){
$addMe=false;
break;
}}if($addMe ) self::$_queueMessage[$typekey][]=$jval;}else{
self::$_queueMessage[$typekey][]=$jval;}}}}
$libraryCacheC->resetCache('Messages','messageID'.$msgSessionId );
}
}
public function getMessageList(){
return self::$_queueMessage;
}
public function setMessageList($addToQueue,$fromServer=false){
if(!empty($addToQueue) && is_array($addToQueue)){
foreach($addToQueue as $type=> $messages){
if(!empty($messages)){
foreach($messages as $oneMessage){
if(!empty($oneMessage)){
$HMessage=($fromServer )?'XML-RPC SERVER MESSAGE: '.$oneMessage->message : $oneMessage->message;
$this->addM($HMessage, $type, $oneMessage->variable );
}}
}
$this->addM($messages, $type );
}
}
}
public function getM($keepQueue=false,$msgSessionId=null,$noTranslation=false){
if( JOOBI_FRAMEWORK !='netcom') WPage::addJSLibrary('rootscript');
$this->loadMessages($msgSessionId );
if(empty(self::$_queueMessage) && WRoles::isAdmin('manager')){
$hidemenu=WGlobals::get('hidemainmenu', 0 );
$appType=WGlobals::get('appType','','global');
}
if(!empty(self::$_queueMessage)){
if(!defined('JOOBI_INSTALLING') || WExtension::exist('main.node')){
$vocabulary=self::loadVocab();
$allVocab=array();
if(is_array($vocabulary)){
foreach($vocabulary as $vocab){
$allVocab[$vocab->imac]=$vocab->text;
}}
foreach( self::$_queueMessage as $type=> $allMessages){
foreach($allMessages as $k=> $oneM){
$imac=$oneM->message;
if(isset($allVocab[$imac])) self::$_queueMessage[$type][$k]->message=$allVocab[$imac];
}}
}
$newArraySort=array("error"=> '', "warning"=> '', "notice"=>'', "success"=> '');
foreach($newArraySort as $index=> $message){
if(empty(self::$_queueMessage[$index])) unset($newArraySort[$index]);
else $newArraySort[$index]=self::$_queueMessage[$index];
}$newArraySort=array_merge($newArraySort, self::$_queueMessage );
$html=WPage::renderBluePrint('alert',$newArraySort );
}else{
$html=WPage::renderBluePrint('alert',array());
}
if(!$keepQueue){
self::$_queueMessage=array();
}
return $html;
}
private static function loadVocab($imac=null){
static $static_code=null;
if(!isset($static_code)){
$lgid=WUser::get('lgid');
$static_code=WLanguage::get($lgid, 'code');
}
$model=WModel::get('translation.en','object', null, false);
if(!is_object($model) || empty($model)){
return false;
}
$model->select('imac');
if(empty($imac)){
if(empty( self::$_queueMessage )) return array();
$imacs=array();
foreach( self::$_queueMessage as $typeArray){
foreach($typeArray as $message){
if(!$message->code){
$imacs[]=$message->message;
}
}
}
if(empty($imacs)) return array();
$model->whereIn('imac',$imacs);
}else{
$model->whereE('imac',$imac);
}
if($static_code !='en'){
$existsTR=WModel::get('translation.'.$static_code, 'sid', null, false);
if(!empty($existsTR)){
$model->makeLJ('translation.'.$static_code, 'imac');
if(!is_object($model)){
return false;
}
$model->select('text', 1);
$model->select('text',0,'textref');
}else{
$model->select('text');
}}else{
$model->select('text');
}
if( empty ($imac)){
$results=$model->load('ol');
if($static_code !='en'){
foreach($results as $key=> $mytrans){
if(empty($mytrans->text))$results[$key]->text=$mytrans->textref;
unset($results[$key]->textref);
}}
return $results;
}else{
$result=$model->load('o');
if(is_object($result)){
if($static_code !='en' && empty($result->text)){
$result->text=$result->textref;
}
return $result->text;
}
return false;
}
}
public function M($condition){
if($condition){
$this->addM($this->_sucessM, 'success');
}else{
$this->addM($this->_failedM, 'error');
}
return $condition;
} 
public static function showLine($entries=1,$html=true){
$mess='';
if( WPref::load('PLIBRARY_NODE_ERRORSHOWLINE')){
$arrayBackTrace=debug_backtrace(false);
}else{
return $mess;
}
if( is_string($entries)){
$found=false;
if($entries=='query'){
$entries=array('wmodel','wtable','wquery','sql_mysql_addon',
'install_database_class','wmodel_save','wmodel_delete','wmodel_load');
}else{
$entries=array( strtolower($entries));
}
foreach($arrayBackTrace as $i=> $backtraceItem){
if(!empty($backtraceItem['class']) && in_array( strtolower($backtraceItem['class'] ), $entries )){
$found=true;
}elseif($found){
return self::getLineMessage($arrayBackTrace[$i-1], $html );
}}
$entries=1;
WMessage::log($arrayBackTrace, 'query_class_not_found', true, false);
}
if(!is_array($entries))$entries=array($entries );
foreach($entries as $entry){
$mess .=self::getLineMessage($arrayBackTrace[$entry],$html );
}
return $mess;
}
private static function getLineMessage(&$backtraceItem,$html){
$mess='';
if(!isset($backtraceItem['file']) || !isset($backtraceItem['line']))
return $mess;
$fileName=str_replace( JOOBI_DS_ROOT, DS, $backtraceItem['file'] );
if($html){
$mess .='<br/>';
}else{
$mess .="\r\n";
}
$mess .='# On line: '.$backtraceItem['line'].'  In the file '.$fileName;
$mess2='';
if(!empty($backtraceItem['class'])){
$mess2 .=' class '.$backtraceItem['class'];
}
if(!empty($backtraceItem['function'])){
$mess2 .=' function '.$backtraceItem['function'];
}
if($mess2 !=''){
$mess.' called from '.$mess2;
}
return $mess;
}
public function store($msgSessionId=''){
if(empty($msgSessionId))$msgSessionId=WUser::getSessionId();
if(!empty(self::$_queueMessage)){
$libraryCacheC=WCache::get();
$libraryCacheC->set('messageID'.$msgSessionId, self::$_queueMessage, 'Messages','cache');
}
}
public function addText($message){
$this->_logMessage .=$message.' '.WMessage::showLine(1,false). "\r\n";
}
public function addVar($message){
if(is_object($message) || is_array($message)){
$message=print_r($message,true);
}
$this->_logMessage .=$message.' '.WMessage::showLine(1,false). "\r\n";
}
public static function log($message,$location='system-logs',$deleteBefore=false,$entries=1,$showTime=true,$showMemory=false){
if(!is_string($location)){
$location='wrong-location';
}
if( defined('PLIBRARY_NODE_LOGSOFF')){
if( PLIBRARY_NODE_LOGSOFF ) return true;
}
if( is_string($message)){
$messageSize=strlen($message );
if(!WTools::checkMemory($messageSize )) return false;
}else{
$messageSize=0;
}
$file=WMessage::getLogLocation($location );
$filehandler=WGet::file();
$filehandler->displayMessage(false);
$fileExist=$filehandler->exist($file );
if($deleteBefore && $fileExist && !$filehandler->delete($file )) return false;
if(!$fileExist || $deleteBefore){
$beginning='#<'.'?php die(\'Access Not Permitted\'); ?>'."\n\r";
}else{
$beginning='';
}
$size=$filehandler->size($file );
if($size > 10000000  && !$filehandler->delete($file )) return false;
if( is_object($message ) || is_array($message ))$message=print_r($message, true);
$beginning .= '##';
$details='';
if($showTime){
$details .=' date: '.date( WTools::dateFormat('date-time-second', true)). ' ';
}
if($messageSize > 100000)$showMemory=true;
if($showMemory)$details .='  current memory used: '.WTools::returnBytes( memory_get_usage(), true). ' ';
if(!empty($details ))  $beginning .=' ('.$details.')';
if($messageSize > 4000000){
$message='Message too big to be saved! Message size: '.$messageSize;
$messageSize=30;}
$breaks=array('<br>','<br/>','<br />','<BR>','<BR/>','<BR />');
if($messageSize < 500000)$message=str_replace($breaks, "\r\n", $message );
if(isset($this ) && isset($this->_logMessage )){
$message=$this->_logMessage."\r\n".$message;
$this->_logMessage='';}
$details='';
if($entries)$details .=self::showLine($entries, false);
if($entries && isset($_SERVER[ 'REQUEST_URI' ] ) && $location !='install'){
$details .="\r\n# On the page: ". $_SERVER[ 'REQUEST_URI' ];
}
if(!empty($details ))  $details .= "\r\n";
$beginning .=$details ."## \r\n";
$writeStatus=$filehandler->write($file, $beginning . $message . "\r\n". '## End ##'."\r\n", 'append');
 return $writeStatus;
}
public static function getLogLocation($location,$type='filesystem'){
$file=$location.'.log';
switch($type){
case 'url':
return JOOBI_URL_USER.'logs/'. $file;
case 'filesystem':
default:
return JOOBI_DS_USER.'logs'.DS.$file;
}}
public function notify($command){
$data=new stdClass;
$data->url=JOOBI_SITE;
$data->command=$command;
$netcom=WNetcom::get();
return $netcom->send( WPref::load('PAPPS_NODE_REQUEST'), 'license','notification',$data, false);
}
public function exitNow($message='',$log=true){
if(empty($message))$message='Access restricted';
if($log)$this->log($message, 'joobi-exitnow');
echo $message;
exit();
}
}