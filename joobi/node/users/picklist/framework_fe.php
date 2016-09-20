<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Framework_fe_picklist extends WPicklist {
function create(){
if(!defined('PUSERS_NODE_FRAMEWORK_FE')) WPref::get('users.node', false, true, false);
if( defined('PUSERS_NODE_FRAMEWORK_FE'))$usedOption=PUSERS_NODE_FRAMEWORK_FE;
if(empty($usedOption))$usedOption=JOOBI_FRAMEWORK;
$usersAddon=WAddon::get('users.'.$usedOption );
if(empty($usersAddon)) return false;
$joomlaO=$usersAddon->getPicklistElement();
if(!empty($joomlaO))$joomla=array($joomlaO );
else $joomla=array();
$exist=WExtension::exist('contacts.node');
if($exist){
$jomsocial=new stdClass;
$jomsocial->option='contacts';
$jomsocial->label='Contacts';
$jomsocial->extension='contacts';
$joomla[]=$jomsocial;}
if('joomla'==JOOBI_FRAMEWORK_TYPE){
$exist=WApplication::isEnabled('community', true);
if($exist){
$jomsocial=new stdClass;
$jomsocial->option='jomsocial';
$jomsocial->label='JomSocial';
$jomsocial->extension='com_community';
$joomla[]=$jomsocial;}}
$exist=false;
$defaultExist=false;
foreach($joomla as $community){
$this->addElement($community->option , $community->label );
if($community->option==PUSERS_NODE_FRAMEWORK_FE)$defaultExist=true;
}
if($defaultExist){
$this->setDefault( PUSERS_NODE_FRAMEWORK_FE, true);
}else{
$this->setDefault('joomla', true);
}
return true;
}
}