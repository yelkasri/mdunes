<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Api_Wp4_Sef_class extends WClasses {
private $appName=null;
public function buildSEF(&$query){
if(!empty($query[JOOBI_PAGEID_NAME]) && empty($query['controller'])){
return array();
}
$segmentsA=array();
if(!empty($query['controller'])){
$myControllerA=explode('-',$query['controller'] );
$myController=array_shift($myControllerA );
$mySEFC=WClass::get($myController. '.sef', null, 'class', false);
if(!empty($mySEFC)){
$segmentsA=$mySEFC->buildSEF($query );
return $segmentsA;
}
}
if(!empty($query['controller'])){
$segmentsA[]=$query['controller'];
unset($query['controller']);
}
if(!empty($query['task'])){
$segmentsA[]=$query['task'];
unset($query['task']);
}
if(!empty($query['eid'])){
$segmentsA[]=$query['eid'];
unset($query['eid']);
}
if(!empty($query['type'])){
$segmentsA[]=$query['type'];
unset($query['type']);
}
return $segmentsA;
}
public static function parseURL($string,&$vars){
$alreadyDone=false;
if(!empty($vars['controller'])){
$myControllerA=explode('-',$vars['controller'] );
$myController=array_shift($myControllerA );
$mySEFC=WClass::get($myController. '.sef', null, 'class', false);
if(!empty($mySEFC)){
$alreadyDone=$mySEFC->parseSEF($vars, $string );
}
}
if(!$alreadyDone){
if( is_numeric($string)){
$vars['eid']=$string;
}else{
if( substr($string, 0, 7)==JOOBI_PAGEID_NAME.':'){
$vars[JOOBI_PAGEID_NAME]=substr($string, 7 );
}else{
$vars['task']=$string;
}
}
}
}
}