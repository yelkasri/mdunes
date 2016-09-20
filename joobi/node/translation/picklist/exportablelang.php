<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_Exportablelang_picklist extends WPicklist {
function create(){
$lgM=WModel::get('library.languages');
$lgM->whereE('publish','1');
$lgM->whereE('code','en',0,null,0,0,1);
$lgM->setLimit( 500 );
$results=$lgM->load('ol',array('lgid','real','name'));
foreach($results as $myResult){
$cont=$myResult->name.' ('.$myResult->real.')';
$this->addElement($myResult->lgid , $cont );
}
}}