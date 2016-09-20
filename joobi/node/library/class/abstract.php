<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WElement extends WObj {
var $class=null;var $style=null;var $align=null;var $id=null;
public $content='';
public $crlf='';
function __construct($data=null,$params=null){
parent::__construct();
if(isset($data))$this->_data=$data;
if(isset($params )){
if(is_array($params )){
foreach($params as $key=> $val){
$param='_'.$key;
 if(!isset($this->$param))$this->$param=$val;
}
}elseif( is_object($params )){
$myObjs=get_object_vars($params);
foreach($myObjs as $key=> $val){
if(!isset($this->$key))$this->$key=$val;
}
}
}
$this->crlf=WGet::$rLine;
}
public function create(){
return true;
}
public function make($data=null,$obj=null){
if(isset($data))$this->_data=$data;
if(isset($obj))$this->addPropperties($obj);
if($this->create()){
return $this->display();
}else{
return '';
}}
public function add($content){
$this->content .=$content;
}
public function display(){
return $this->content;
}
}
class WClasses extends WObj {
function __construct($params=null){
parent::__construct();
if(empty($params)) return ;
if( is_object($params)){
foreach($params as $key=> $value){
$this->$key=$value;
}}elseif(is_array($params)){
foreach($params as $key=> $value){
$mykey='_'.$key;
$this->$mykey=$value;
}}
}
}
class WObj {
private static $_message=null;
function __construct(){
if(!isset(self::$_message)) self::$_message=WMessage::get();
}
function __destruct(){
return true;
}
public function s($property,$value=null,$private=true){
if(empty($property) || ! is_string($property)) return false;
if($private)$property='_'.$property;
$this->$property=$value;
if(!isset($value)) unset($this->$property );
return true;
}
public function g($property,$default=null,$private=true){
if(empty($property) || ! is_string($property)) return false;
if($private)$property='_'.$property;
if(isset($this->$property)) return $this->$property;
return $default;
}
public function userS($mess,$variable=array()){
self::$_message->userS($mess, $variable );
}
public function userN($mess,$variable=array()){
self::$_message->userN($mess, $variable );
}
public function userW($mess,$variable=array()){
self::$_message->userW($mess, $variable );
}
public function userE($mess,$variable=array()){
self::$_message->userE($mess, $variable );
}
public function adminW($mess,$variable=array(),$showLine=0){
self::$_message->adminW($mess, $variable, $showLine );
}
public function adminN($mess,$variable=array(),$showLine=0){
self::$_message->adminN($mess, $variable, $showLine );
}
public function adminE($mess,$variable=array(),$showLine=0){
self::$_message->adminE($mess, $variable, $showLine );
}
public function historyW($mess,$variable=array(),$redirectInController=false){
self::$_message->historyW($mess, $variable, $redirectInController );
}
public function historyN($mess,$variable=array(),$redirectInController=false){
return self::$_message->historyN($mess, $variable, $redirectInController );
}
public function historyE($mess,$variable=array(),$redirectInController=false){
return self::$_message->historyE($mess, $variable, $redirectInController );
}
public function historyS($mess,$variable=array(),$redirectInController=false){
return self::$_message->historyS($mess, $variable, $redirectInController );
}
public function codeE($mess,$variable=array(),$showLine=2){
return self::$_message->codeE($mess, $variable, $showLine );
}
public function log($message,$location='system-logs',$deleteBefore=false,$entries=1,$showTime=true,$showMemory=false){
self::$_message->log($message, $location, $deleteBefore, $entries, $showTime, $showMemory );
}
public function addProperties($data,$prefix='',$noArray=false,$propertyName=null){
$arrayInValue=array();
if(!empty($data)){
if(is_array($data)){
foreach($data as $key=> $value){
$newKey=$key;
$myValue=$value;
if(is_array($value) && $noArray){
$arrayInValue[]=$key;
}else{
if(!empty($propertyName))$this->$propertyName->$newKey=$value;
else $this->$newKey=$value;
}}}elseif( is_object($data)){
foreach($data as $property=> $value){
if( substr($property, 0, 1 ) !='_'){$property=$prefix.$property;
if(is_array($value) && $noArray){
$arrayInValue[]=$property;
}else{
if(!empty($propertyName))$this->$propertyName->$property=$value;
else $this->$property=$value;
}}}}}
return $arrayInValue;
}
}
