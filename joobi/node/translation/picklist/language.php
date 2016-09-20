<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_Language_picklist extends WPicklist {
function create(){
$model=WModel::get('library.languages');
$model->whereE('publish', 1 );
if(!$this->onlyOneValue()){
$model->setLimit( 500 );
$results=$model->load('ol',array('lgid','name','real'));
$eid=WGlobals::getEID();
$lgid=(!empty($eid)?WUser::get('lgid',$eid ) : WUser::get('lgid'));
$this->setDefault($lgid, true);
foreach($results as $result){
$this->addElement($result->lgid , $result->name.' ('.$result->real.')');
}
}else{
$defaults=$this->getDefault();
$model->whereE('lgid',$defaults );
$result=$model->load('o',array('lgid','name','real'));
if(empty($result)) return '';
$cont=$result->name.' ('.$result->real.')';
$this->addElement($result->lgid , $cont );
}
return true;
}
}