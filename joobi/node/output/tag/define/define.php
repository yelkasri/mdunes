<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Output_Define_tag {
  private $definedVar=array('SITENAME'=>'JOOBI_SITE_NAME','SITE'=>'JOOBI_SITE');
function process($object){
$replacedTagsA=array();
if( WRoles::isAdmin('manager'))$this->definedVar['ROOT']='JOOBI_DS_ROOT';
else $this->definedVar['ROOT']='';
foreach($object as $TAG=> $myTagO){
if(empty($myTagO->name))$myTagO->name=$myTagO->_type;
$myTagO->name=strtoupper($myTagO->name );
if($myTagO->name=='SITEURL')$myTagO->name='SITE';
elseif($myTagO->name=='SITEURLADMIN')$myTagO->name='SITE_ADMIN';
if(!isset($this->definedVar[$myTagO->name])){
$VALUE=$myTagO->name;
$message=WMessage::get();
$message->userW('1212843293BKVE',array('$TAG'=>$TAG));
$message->userW('1213369322GXOW',array('$VALUE'=>$VALUE));
$VALUES=implode(' | ',array_keys($this->definedVar));
$message->userW('1213369322GXOX',array('$VALUES'=>$VALUES));
$message->userW('1380025960NDBN');
continue;
}
$myTagO->wdgtContent=defined($this->definedVar[$myTagO->name])?constant($this->definedVar[$myTagO->name] ) : '';
$replacedTagsA[$TAG]=$myTagO;
}
return $replacedTagsA;
}
}