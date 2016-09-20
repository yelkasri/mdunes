<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WTypes {
public $node='';
private $_listName=null;
function __construct($name){
$this->_listName=$name;
}
public function getName($i){
$list=$this->_listName;
WText::load($this->node.'.node');
$myArray=$this->translatedType();
if(isset($this->$list)){
$myArray=$this->$list;
}else{
$mess=WMessage::get();
$mess->codeE('The type specified does not exist: '.$list.'  : value: '.$i );
return '';
}
if(isset($myArray[ $i ] )){
return $myArray[ $i ];
}else{
return false;
}
}
public function getTranslatedName($i){
$myArray=$this->translatedType();
if(empty($myArray)){
$list=$this->_listName;
if(isset($this->$list)){
$myArray=$this->$list;
}else{
$mess=WMessage::get();
$mess->codeE('The type specified does not exist: '.$list.'  : value: '.$i );
return '';
}}
if(isset($myArray[ $i ] )){
return $myArray[ $i ];
}else{
return false;
}
}
public function getValue($name,$caseSensitive=true){
$list=$this->_listName;
if(isset($this->$list)){
if(!$caseSensitive){
$lowername=strtolower($name );
foreach($this->$list as $k=> $v){
if(strtolower($v)==$lowername) return $k;
}return $name;}
$i=array_search($name, $this->$list );
if($i !==false)
return $i;
}
return $name;
}
public function allNames(){
$list=$this->_listName;
if(isset($this->$list)){
return array_values($this->$list );
}else{
return '';
}
}
public function inNames($string){
$list=$this->_listName;
if(isset($this->$list)){
return in_array($string, $this->$list);
}else{
return '';
}
}
public function inValues($search){
$list=$this->_listName;
if(isset($this->$list)){
return in_array($search, array_keys($this->$list));
}else{
return '';
}
}
public function allValues(){
$list=$this->_listName;
if(isset($this->$list)){
return array_keys($this->$list);
}else{
return '';
}
}
public function getCount(){
$list=$this->_listName;
return count($this->$list);
}
public function getList($translated=false){
if($translated)$allTypesA=$this->translatedType();
if(!empty($allTypesA)) return $allTypesA;
$list=$this->_listName;
return (isset($this->$list)?$this->$list : array());
}
protected function translatedType(){
return array();
}
}