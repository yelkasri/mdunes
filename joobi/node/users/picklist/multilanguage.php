<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Multilanguage_picklist extends WPicklist {
function create(){
$model=WModel::get('library.languages');
if( WGlobals::getCandy()==0 || !defined('PMEMBERS_NODE_EXTLANG') || !PMEMBERS_NODE_EXTLANG){
$model->whereE('main',1);
}
$language2Use=( WRoles::isAdmin()?'admin' : 'site');
$langClient=WApplication::mainLanguage('lgid', false, array(), $language2Use );
if(!$this->onlyOneValue()){
$model->setLimit( 500 );
$results=$model->load('ol',array('lgid','name','code','real'));
$alldefaults=array();
foreach($results as $result){
if(isset($langClient) && $langClient==$result->lgid)$alldefaults[]=$result->lgid;
$this->addElement($result->lgid , $result->name.' ('.$result->real.')');
}
$this->setDefault($alldefaults);
}else{
$defaults=$this->getDefault();
if(!empty($defaults)){
$model->whereIn('lgid',$defaults);
$result=$model->load('o',array('lgid','name','real'));
if(empty($result)) return '';
$cont=$result->name.' ('.$result->real.')';
$this->addElement($result->lgid  , $cont );
return true;
}else{
return WText::t('1206732410ICCI');
}
}
}
}