<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_SQL_class {
public function getVersion(){
$dbType=WGet::DBType(); 
if(empty($dbType ))$dbType='framework';
if($dbType=='mysql' && PHP_VERSION > '5.5')$dbType='framework';
if($dbType=='framework'){
$db=WAddon::get('api.'.JOOBI_FRAMEWORK.'.sql');
}else{
$db=WAddon::get('library.'.$dbType, $params );
}
return $db->dbVersion();
}
public function splitSql($content,$limit=';',$avoid=null,$comments=true){
if($content===false) return false;
if($avoid==null)$avoid=array('"','`',"'" );
$array=array();
if($comments)$content=preg_replace( array('`^[ \t]*#.*$`m','`^[ \t]*--.*$`m'), '', trim($content));
$len=strlen($content);
$h=0;$stack=0;
$stringuse='';
for($i=0; $i<$len; $i++){
switch($stack){
case 0:
switch($content[$i]){
case $limit:
$array[]=trim(substr($content,$h,$i+1-$h));
$h=$i+1;
default:
if( in_array($content[$i], $avoid )){
$stack++;
$stringuse=$content[$i];
}break;
}break;
case 1:
if($content[$i]==$stringuse && $content[$i-1] !="\\"){
$stack--;
}default:
break;
}
}
return $array;
}
public function sorter($a,$b){
$a=explode('.',$a);
$b=explode('.',$b);
foreach($a as $k=> $v)
{
if($v==$b[$k])
{
continue;
}
elseif($v>$b[$k])
{
return 1;
}
return -1;
}
return 0;
}
}