<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Users_user_tag {
 var $usertag=true;
 var $_excludeA=array('password','question','answer');
function process($object){
static $adminStatus=null;
$replacedTagsA=array();
$message=WMessage::get();
$query=false;
foreach($object as $tag=> $myTagO){
if(empty($myTagO->select))$myTagO->select=$myTagO->_type;
$valeur=strtolower($myTagO->select );
if( in_array($valeur, $this->_excludeA )){
$FIELD=$valeur;
$message->userW('1212843293BKVD',array('$FIELD'=>$FIELD));
$myTagO->wdgtContent='';
$replacedTagsA[$tag]=$myTagO;
continue;
}
if(isset($this->user->$valeur)){
$myTagO->wdgtContent=$this->user->$valeur;
$replacedTagsA[$tag]=$myTagO;
continue;
}else{
if(!isset($adminStatus)){
$roleC=WRole::get();
$adminStatus=WRole::hasRole('admin');
}
if(!$adminStatus){
$myTagO->wdgtContent='';
$replacedTagsA[$tag]=$myTagO;
}
}
if(empty($this->user->uid)){
continue;
}
$this->user=WUser::get('data',$this->user->uid );
if('firstname'==$valeur){
$expldeNAmeA=explode(' ',$this->user->name );
$myTagO->wdgtContent=$expldeNAmeA[0];
$replacedTagsA[$tag]=$myTagO;
continue;
}elseif('lastname'==$valeur){
$expldeNAmeA=explode(' ',$this->user->name );
$myTagO->wdgtContent=array_pop($expldeNAmeA );
$replacedTagsA[$tag]=$myTagO;
continue;
}else{
if(isset($this->user->$valeur)){
$myTagO->wdgtContent=$this->user->$valeur;
$replacedTagsA[$tag]=$myTagO;
continue;
}
}
$replacedTagsA[$tag]=$myTagO;
$FIELD=$valeur;
$message->userW('1299148902FYMY',array('$FIELD'=>$FIELD));
}
return $replacedTagsA;
}
}