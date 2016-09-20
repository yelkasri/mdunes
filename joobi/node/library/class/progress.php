<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Progress_class extends WObj {
public $useAjax=true;
public $detailsRefresh='';public $detailsAppend='';
public $userMaxExecTime=0;public $userMaxLoop=0;
protected $_name='';
protected $_showBar=true;
protected $_showProgressStatus=true;
protected $_showError=false;
protected $_showTimeEstimate=true;
protected $_showTimeCompletion=true;
protected $_showMessage=true;
protected $_showDetails=true;
protected $_redirectURL='';
protected $_ready=false;
protected $_progressO=array();
protected $_maxExcecutionTime=30;protected $_maxExecTime=0;
protected $_maxTimeRatio=70;
protected $_stepDuration=1;
protected $_maxIncrement=20;
protected $_stepA=array();
protected $_stepRatioA=array();
protected $_stepTotalIncrementA=array();
protected $_statusSet=false;
protected $_fileName='';
protected $_firstMessage='';
private $_processIsFinished=false;
private $_folder='progress';
private $_progressHTML=null;
private $_ajaxReturnedA=array();
private $_elapseTime=0;
static private $_instanceA=array();
public function get($name,$id=""){
$this->_name=$name;
if(isset(self::$_instanceA[$name])) return self::$_instanceA[$name];
self::$_instanceA[$name]=WClass::get($name.'.steps', null, 'class', false);
if(empty(self::$_instanceA[$name])){
$this->codeE('The steps class is not defined for : '.$name );
return false;
}
self::$_instanceA[$name]->_name=$name;
self::$_instanceA[$name]->init();
if(empty(self::$_instanceA[$name]->userMaxExecTime)){
$maxExecTime=WTools::increasePerformance();
if($maxExecTime <=0)$maxExecTime=15;self::$_instanceA[$name]->_maxExecTime=$maxExecTime * ($this->_maxTimeRatio / 100 );
}else{
self::$_instanceA[$name]->_maxExecTime=self::$_instanceA[$name]->userMaxExecTime;
}
if( self::$_instanceA[$name]->_maxExecTime > self::$_instanceA[$name]->_maxExcecutionTime ) self::$_instanceA[$name]->_maxExecTime=self::$_instanceA[$name]->_maxExcecutionTime;
$this->_maxExecTime=self::$_instanceA[$name]->_maxExecTime;
if( count( self::$_instanceA[$name]->_stepA ) < 1){
$this->codeE('No steps defined');
return false;
}
self::$_instanceA[$name]->_fileName=$name.'_'.$id.'steps.stp';
$path=$this->getFilePath();
$fileS=WGet::file();
if($fileS->exist($path )){
$content=$fileS->read($path );
if(!empty($content)){
self::$_instanceA[$name]->_progressO=unserialize($content );}else{
self::$_instanceA[$name]->_progressO=new WProgress_object_value;
}
}else{
self::$_instanceA[$name]->_progressO=new WProgress_object_value;
}
self::$_instanceA[$name]->initEnd();
return self::$_instanceA[$name];
}
public function getFilePath(){
$path=JOOBI_DS_USER . $this->_folder.DS.self::$_instanceA[$this->_name]->_fileName;
return $path;
}
public function run($stepToRun=null){
if(!empty(self::$_instanceA[$this->_name]->_progressO->stepA))$this->stepA=self::$_instanceA[$this->_name]->_progressO->stepA;
if(!empty(self::$_instanceA[$this->_name]->_progressO->stepRatioA))$this->_stepRatioA=self::$_instanceA[$this->_name]->_progressO->stepRatioA;
if(!empty(self::$_instanceA[$this->_name]->_progressO->stepTotalIncA))$this->_stepTotalIncrementA=self::$_instanceA[$this->_name]->_progressO->stepTotalIncA;
if(!empty(self::$_instanceA[$this->_name]->_progressO->maxIncr))$this->_maxIncrement=self::$_instanceA[$this->_name]->_progressO->maxIncr;
self::$_instanceA[$this->_name]->_progressO->start=time();
if(empty(self::$_instanceA[$this->_name]->_progressO->currentStep)){
self::$_instanceA[$this->_name]->_progressO->currentStep=self::$_instanceA[$this->_name]->_stepA[0];
self::$_instanceA[$this->_name]->_progressO->stepNb++;
self::$_instanceA[$this->_name]->_progressO->orign=time();
}else{
if( self::$_instanceA[$this->_name]->_progressO->status=='complete'){
if(isset(self::$_instanceA[$this->_name]->_stepA[self::$_instanceA[$this->_name]->_progressO->stepNb])){
if(!isset(self::$_instanceA[$this->_name]->_progressO->_stepA[self::$_instanceA[$this->_name]->_progressO->stepNb])){
$this->codeE('this case should not happen it means there is a problem with the steps define for the process : '.$this->_name );
}else{
self::$_instanceA[$this->_name]->_progressO->currentStep=self::$_instanceA[$this->_name]->_progressO->_stepA[self::$_instanceA[$this->_name]->_progressO->stepNb];
}
self::$_instanceA[$this->_name]->_progressO->status='ready';
self::$_instanceA[$this->_name]->_progressO->stepNb++;
self::$_instanceA[$this->_name]->_progressO->increment=0;
}else{
self::$_instanceA[$this->_name]->_progressO->status='complete';
}
}else{
}
}
self::$_instanceA[$this->_name]->_ready=true;
if(!$this->_ready ) return false;
if(!empty($stepToRun) && $stepToRun !=self::$_instanceA[$this->_name]->_progressO->currentStep ) return true;
if(empty(self::$_instanceA[$this->_name]->_progressO->maxIncr)) self::$_instanceA[$this->_name]->_progressO->maxIncr=$this->_maxIncrement;
$maxStepTime=self::$_instanceA[$this->_name]->_progressO->start + self::$_instanceA[$this->_name]->_maxExecTime;
$maxIncrementTime=0;
$loopCount=0;$maxLoops=self::$_instanceA[$this->_name]->userMaxLoop;
do {
$startInc=time();
$loopCount++;
self::$_instanceA[$this->_name]->_progressO->increment++;
$this->runStep();
if(!empty($stepToRun) && $stepToRun !=self::$_instanceA[$this->_name]->_progressO->currentStep ) return true;
$incTime=time() - $startInc;
if($maxIncrementTime < $incTime)$maxIncrementTime=$incTime;
} while ( self::$_instanceA[$this->_name]->_progressO->status !='complete'
&& ( time() + $maxIncrementTime ) < $maxStepTime
&& ( 0==$maxLoops || $loopCount <=$maxLoops )
&& self::$_instanceA[$this->_name]->_progressO->increment <=self::$_instanceA[$this->_name]->_progressO->maxIncr );
if( self::$_instanceA[$this->_name]->_progressO->increment > self::$_instanceA[$this->_name]->_progressO->maxIncr){
$this->setReturnedAction('WAjxRefresh','The progress got stopped because we reach the maximum increment','html');
WMessage::log('The progress got stopped because we reach the maximum increment','install-progress-max-increment');
WMessage::log('increment : '.self::$_instanceA[$this->_name]->_progressO->increment  , 'install-progress-max-increment');
WMessage::log('maxIncr : '.self::$_instanceA[$this->_name]->_progressO->maxIncr  , 'install-progress-max-increment');
WMessage::log( self::$_instanceA[$this->_name] , 'install-progress-max-increment');
WMessage::log($this, 'install-progress-max-increment');
$this->setStep('failed');
}
if(empty($this->_stepRatioA)){
$ratio=( self::$_instanceA[$this->_name]->_progressO->stepNb / count($this->_stepA )) * 100;
}else{
if(empty($this->_stepTotalIncrementA)){
$ratio=0;
$count=1;
foreach($this->_stepRatioA as $step=> $stepRatio){
if($count <=self::$_instanceA[$this->_name]->_progressO->stepNb)$ratio +=$stepRatio;
else break;
$count++;
}
}else{
$ratio=0;
$count=1;
foreach($this->_stepRatioA as $step=> $stepRatio){
if($count < self::$_instanceA[$this->_name]->_progressO->stepNb){
 $ratio +=$stepRatio;
}elseif($count==self::$_instanceA[$this->_name]->_progressO->stepNb){
$ratio +=( self::$_instanceA[$this->_name]->_progressO->increment / $this->_stepTotalIncrementA[$step] * $stepRatio );
}else{
break;
}$count++;
}
}
}
if($ratio > 100){
$ratio=100;
}elseif($ratio < 0){
$ratio=0;
}
if($ratio < self::$_instanceA[$this->_name]->_progressO->ratio)$ratio=self::$_instanceA[$this->_name]->_progressO->ratio;
self::$_instanceA[$this->_name]->_progressO->ratio=$ratio;
if(!empty(self::$_instanceA[$this->_name]->_progressO->_stepA) && !isset(self::$_instanceA[$this->_name]->_progressO->_stepA[self::$_instanceA[$this->_name]->_progressO->stepNb])){
if( self::$_instanceA[$this->_name]->_progressO->stepNb >=count( self::$_instanceA[$this->_name]->_progressO->_stepA )){
self::$_instanceA[$this->_name]->_progressO->ratio=100;
self::$_instanceA[$this->_name]->_progressO->status='complete';
}else{
$this->codeE('this case should not happen it means there is a problem with the steps define for the process : '.$this->_name );
}}
if( self::$_instanceA[$this->_name]->_progressO->ratio >=100){
$this->_elapseTime=time() - self::$_instanceA[$this->_name]->_progressO->orign;
}
}
public function completeProcess(){
$this->_progressO->status='complete';
$this->_progressO->ratio=100;
return $this->finish();
}
public function finish(){
if(!$this->_ready ) return false;
$this->_progressO->finish=time();
if(!empty($this->_stepTotalIncrementA)){
if($this->_progressO->increment >=$this->_stepTotalIncrementA[$this->_progressO->currentStep]){
$this->setStep('complete');
$this->_progressO->increment=0;
}}else{
$this->setStep('complete');
}
if(!$this->_statusSet){
$this->_progressO->status='complete';
}
if(empty($this->_progressO->_stepA) && !empty($this->_stepA))$this->_progressO->_stepA=$this->_stepA;
if(empty($this->_progressO->_stepRatioA) && !empty($this->_stepRatioA))$this->_progressO->stepRatioA=$this->_stepRatioA;
if(empty($this->_progressO->_stepTotalIncrementA) && !empty($this->_stepTotalIncrementA))$this->_progressO->stepTotalIncA=$this->_stepTotalIncrementA;
$content=serialize($this->_progressO );
$path=JOOBI_DS_USER . $this->_folder.DS.$this->_fileName;
$fileS=WGet::file();
$status=$fileS->write($path, $content, 'overwrite');
if($this->_progressO->ratio >=100
|| $this->_progressO->stepNb > count($this->_progressO->_stepA )
|| $this->_progressO->status=='failed'
){
$dest=JOOBI_DS_USER . $this->_folder.DS.$this->_fileName.'.done';
$fileS->move($path, $dest, true);
$this->_processIsFinished=true;
}
return $status;
}
public function isCompleted(){
return $this->_processIsFinished;
}
public function display(){
$this->_progressHTML=WPage::newBluePrint('progressbar');
$html='';
if($this->_showBar){
$html=$this->displayBar();
if($this->useAjax){
$html='<div id="WAjxBar">'.$html.'</div>';
}}
if($this->_showError){
$this->_progressHTML->showError=$this->_showError;
$this->_progressHTML->type='error';
$this->_progressHTML->errorValue='<div id="WAjxError"></div>';
$this->_progressHTML->warningValue='<div id="WAjxWarning"></div>';
$html .=WPage::renderBluePrint('progressbar',$this->_progressHTML );
}
if($this->_showTimeEstimate || $this->_showTimeCompletion)$html .=$this->timeLeft();
return $html;
}
public function firstMessage(){
return $this->_firstMessage;
}
public function displayAjax(){
$returnA=array();
$this->_progressHTML=WPage::newBluePrint('progressbar');
$html='';
if($this->_showBar){
$obj=new WProgress_return;
$obj->id='WAjxBar';
$obj->text=$this->displayBar();
$returnA[]=$obj;
}
if($this->_showProgressStatus){
$obj=new WProgress_return;
$obj->id='WAjxStatus';
if($this->_progressO->ratio <=0){
$obj->text='<span class="label label-success">'.WText::t('1427465852GGWF'). '</span>';
}elseif($this->_progressO->ratio < 100){
$obj->text='<span class="label label-warning"><blink><i class="fa fa-spinner fa-spin"></i>'.WText::t('1427486118NNQO'). '</blink></span>';
}else{
$obj->text='<span class="label label-info"><i class="fa fa-thumbs"></i>'.WText::t('1427486118NNQP'). '</span>';
}$returnA[]=$obj;
}
if($this->_showTimeEstimate || $this->_showTimeCompletion){
$this->timeLeft();
if($this->_showTimeEstimate){
if($this->_progressO->ratio >=100){
$obj=new WProgress_return;
$obj->id='WAjxDuration';
$obj->text=WTools::durationToString($this->_elapseTime );
$returnA[]=$obj;
$obj=new WProgress_return;
$obj->id='WAjxDurationText';
$obj->text=WText::t('1427652903HUZG');
$returnA[]=$obj;
}else{
$obj=new WProgress_return;
$obj->id='WAjxDuration';
$obj->text=WTools::durationToString($this->_estimateLeft );
$returnA[]=$obj;
}
}
if($this->_showTimeCompletion){
$obj=new WProgress_return;
$obj->id='WAjxTime';
$obj->text=WApplication::date( WTools::dateFormat('time-min'), $this->_completionTime );
$returnA[]=$obj;
}
}
if(!empty($this->_ajaxReturnedA)){
foreach($this->_ajaxReturnedA as $oneAjax){
$returnA[]=$oneAjax;
}}
if($this->_progressO->status=='failed'){
$obj=new WProgress_return;
$obj->id='WAjxStatus';
$obj->text='<span class="label label-danger"><i class="fa fa-exclamation"></i>'.WText::t('1242282449PIPX'). '</span>';
$obj->status='failed';
$returnA[]=$obj;
}elseif($this->_progressO->ratio >=100){
$obj=new WProgress_return;
$obj->status='complete';
$returnA[]=$obj;
}
$message=WMessage::get();
$message->cleanBuffer('Library_Progress_class');
return json_encode($returnA );
}
public function displayBar(){
$this->_progressHTML->percentage=$this->_progressO->ratio;
$this->_progressHTML->type='bar';
$html=WPage::renderBluePrint('progressbar',$this->_progressHTML );
return $html;
}
public function timeLeft(){
if($this->_progressO->start==$this->_progressO->orign
|| ($this->_progressO->stepNb==1 && $this->_progressO->increment==1 )){
if(!empty($this->_stepTotalIncrementA)){
$totalNumnerOfStep=0;
foreach($this->_stepTotalIncrementA as $stp)$totalNumnerOfStep +=$stp;
}else{
$totalNumnerOfStep=count($this->_stepA );
}
if(empty($this->_progressO->ratio)){
$this->_completionTime='';
}else{
$this->_completionTime=time() + ($totalNumnerOfStep * $this->_stepDuration );
}
}else{
$speedSoFar=time() - $this->_progressO->orign;
$this->_completionTime=time() + round($speedSoFar * ( 100 - $this->_progressO->ratio ) / $this->_progressO->ratio );
}
if( is_numeric($this->_completionTime))$this->_estimateLeft=$this->_completionTime - time();
else $this->_estimateLeft='';
$html='';
if($this->_showProgressStatus){
$this->_progressHTML->showStatus=$this->_showProgressStatus;
$this->_progressHTML->statusText=WText::t('1206732392OZVH');
if($this->_progressO->ratio <=0){
$this->_progressHTML->statusValue='<span class="label label-success">'.WText::t('1427465852GGWF'). '</span>';
}elseif($this->_progressO->ratio < 100){
$this->_progressHTML->statusValue='<span class="label label-warning">'.WText::t('1427486118NNQO'). '</span>';
}else{
$this->_progressHTML->statusValue='<span class="label label-info">'.WText::t('1427486118NNQP'). '</span>';
}}
if($this->_showTimeEstimate || $this->_showTimeCompletion){
$this->_progressHTML->type='text';
$this->_progressHTML->showDuration=$this->_showTimeEstimate;
$this->_progressHTML->durationText=WText::t('1427120474PPMS');
if(!empty($this->_estimateLeft))$this->_progressHTML->durationValue=WTools::durationToString($this->_estimateLeft );
else $this->_progressHTML->durationValue='';
$this->_progressHTML->completedText=WText::t('1206732376KOVN');
$this->_progressHTML->showTime=$this->_showTimeCompletion;
$this->_progressHTML->timeText=WText::t('1427120474PPMT');
if(!empty($this->_completionTime) && is_numeric($this->_completionTime))$this->_progressHTML->timeValue=WApplication::date( WTools::dateFormat('time-min'), $this->_completionTime );
else $this->_progressHTML->timeValue='';
$this->_progressHTML->showDetails=($this->_showDetails || $this->_showMessage );
$this->_progressHTML->detailsText=WText::t('1206961936HCWR');
$this->_progressHTML->detailsValue='';
if($this->useAjax){
$this->_progressHTML->statusValue='<div id="WAjxStatus">'.$this->_progressHTML->statusValue.'</div>';
$this->_progressHTML->durationValue='<div id="WAjxDuration">'.$this->_progressHTML->durationValue.'</div>';
$this->_progressHTML->timeValue='<div id="WAjxTime">'.$this->_progressHTML->timeValue.'</div>';
$this->_progressHTML->detailsValue='';
if($this->_showDetails)$this->_progressHTML->detailsValue .='<div id="WAjxRefresh">'.$this->_progressHTML->detailsValue.'</div>';
if($this->_showMessage)$this->_progressHTML->detailsValue .='<div id="WAjxAppend"></div>';
}
$html .=WPage::renderBluePrint('progressbar',$this->_progressHTML );
}
return $html;
}
public function init(){
return true;
}
public function initEnd(){
return true;
}
public function maxExecutionTime(){
return $this->_maxExecTime;
}
public function redirectURL(){
return $this->_redirectURL;
}
public function debugStep($data){
}
protected function setStep($status){
$this->_statusSet=true;
if('failed' !=$this->_progressO->status)$this->_progressO->status=$status;
}
protected function addParams($property,$value){
if(empty($property)) return false;
if(!isset(self::$_instanceA[$this->_name]->_progressO->params)) self::$_instanceA[$this->_name]->_progressO->params=new stdClass;
self::$_instanceA[$this->_name]->_progressO->params->$property=json_encode($value );
}
protected function readParams($property,$default=null){
if(empty($property)) return false;
if(isset(self::$_instanceA[$this->_name]->_progressO->params->$property)){
$value=self::$_instanceA[$this->_name]->_progressO->params->$property;
return json_decode($value );
}
else return $default;
}
protected function setReturnedAction($htmlID,$text,$action=null,$status=null){
$obj=new WProgress_return;
$obj->id=$htmlID;
$obj->text=$text;
if(!empty($status))$obj->status=$status;
if(!empty($action))$obj->action=$action;
$this->_ajaxReturnedA[]=$obj;
}
}
class WProgress_object_value {
public $currentStep='';
public $stepNb=0;
public $status='ready';
public $increment=0;
public $maxIncr=0;
public $ratio=0;
public $orign=0;public $start=0;public $finish=0;
public $params=null;
}
class WProgress_return {
public $status='success'; public $id='';
public $action='html';public $text='';
}