<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Output_Filters_class extends WClasses {
private $_myFilters=null;
private $_as=null;
private $_sql=null;
private $_index=null;
public function addFilterToView(&$layout,&$sql,$conditionsToRemoveA=array()){
$this->_sql=&$sql;
$this->_layout=&$layout;
$this->_yid=$layout->yid;
$this->_myFilters=$this->_loadConditionsA('view');
if(empty($this->_myFilters)) return true;
$newFA=array();
foreach($this->_myFilters as $oneFilter){
if(!empty($oneFilter->requirednode)){
if(!WExtension::exist($oneFilter->requirednode )) continue;
}
if($oneFilter->level > WGlobals::getCandy()) continue;
if(!isset($oneFilter->isadmin))$oneFilter->isadmin=0;
if(!isset($oneFilter->rolid))$oneFilter->rolid=0;
if($oneFilter->isadmin > 0 && ! WRoles::isAdmin($oneFilter->rolid )) continue;
if($oneFilter->isadmin < 0 && ! WRoles::isNotAdmin($oneFilter->rolid )) continue;
$newFA[]=$oneFilter;
}
$this->_myFilters=$newFA;
if(!empty($conditionsToRemoveA )){
$newFA=array();
foreach($this->_myFilters as $oneFilter){
if(!in_array($oneFilter->namekey, $conditionsToRemoveA ))$newFA[]=$oneFilter;
}
$this->_myFilters=$newFA;
}
if(!empty($this->_myFilters))$this->_convert();
}
public function addFilterToList($lsid,&$sql){
static $filtersA=array();
$this->_sql=&$sql;
if(!isset($filtersA[$lsid] )){
$this->_lsid=$lsid;
$this->_myFilters=$this->_loadConditionsA('list');
if(!empty($this->_myFilters)){
$this->_myFilters[0]->bktbefore=$this->_myFilters[0]->bktbefore + 1;
$nbFitlers=count($this->_myFilters) - 1;
$this->_myFilters[$nbFitlers]->bktafter=$this->_myFilters[$nbFitlers]->bktafter + 1;
$this->_myFilters[0]->logicopr=0;
$filtersA[$lsid]=$this->_myFilters;
} else $filtersA[$lsid]=array();
}
if(!empty($filtersA[$lsid])){
$this->_myFilters=$filtersA[$lsid];
$this->_convert();
return true;
}else{
return false;
}
}
private function _loadConditionsA($type){
$filterM=WModel::get('library.viewfilters','object');
$filterM->select( array('sid',  'type','map','condopr','logicopr','ref_sid','refmap','typea','typeb','bktbefore','bktafter','namekey','params','requiresvalue','requirednode','level'));
if($filterM->columnExists('rolid'))$filterM->select( array('isadmin','rolid'));
else {
}
if($type=='list'){
$filterM->remember('cdt_lsid_'.$this->_lsid, 'Views');
$filterM->whereE('lsid',$this->_lsid );
}else{
$filterM->remember('cdt_yid_'.$this->_yid, 'Model_filters_node');
$filterM->whereE('yid',$this->_yid );
}
$filterM->whereE('publish', 1 );
$filterM->orderBy('ordering','ASC');
$filterM->setLimit( 100 );
return $filterM->load('ol');
}
private function _convert(){
$q='';
foreach($this->_myFilters as $filter){
if(!empty($filter->params)) WTools::getParams($filter );
switch($filter->type){
case 1:
if($filter->condopr !=8 && $filter->condopr !=7){
$value=$this->_convertType($filter->typeb, $filter );
if($value===null || $value===false) break;
if($filter->typea==$filter->typeb){
$secondfilter=new stdClass;
foreach($filter as $keyF=> $valF){
if($keyF!='bktbefore' && $keyF!='bktafter')$secondfilter->$keyF=$valF;
}
$secondfilter->bktbefore=0;
$secondfilter->bktafter=0;
}else{
$secondfilter=$filter;
}
}else{
$secondfilter=$filter;
$value=null;
}
$this->_convertType($filter->typea, $secondfilter, $value );
break;
case 7:
if(empty($filter->sid)) break;
if(($filter->typeb==90 && $this->_convertType($filter->typeb, $filter ))
|| $filter->typeb!=90){
$map=(!empty($filter->map)?$filter->map : 'rolid');
$this->_sql->checkAccess($this->_sql->getAs($filter->sid ), $filter->bktbefore, $filter->bktafter, $filter->logicopr, $map );
}
break;
case 8 :
if($filter->typeb==130){
WPref::load($filter->refmap );
$myRefMap=constant($filter->refmap );
}else{
$myRefMap=$filter->refmap;
}
$myRefMap=strtoupper($myRefMap );
switch($myRefMap){
case 'DESC':
case '1':
 $direction='DESC';
 break;
case 'ASC':
case '0':
case '':
$direction='ASC';
break;
default:
return false;
break;
}
$this->_sql->orderBy($filter->map, $direction, $this->_sql->getAs($filter->sid ));
break;
case 9 :
if( 90==$filter->typeb)$resultGood=$this->_convertType($filter->typeb, $filter );
else $resultGood=true;
if(false !==$resultGood)$this->_sql->groupBy($filter->map, $this->_sql->getAs($filter->sid));
break;
case 17:
break;
case 81:
$this->_sql->setDistinct();
break;
case 50:
if(!empty($filter->ref_sid)){
$val=$filter->refmap;
$ref_as=$this->_sql->getAs($filter->ref_sid);
}else{
$val=$this->_convertType($filter->typeb, $filter );
$ref_as=null;
}
$this->_sql->whereOn($filter->map, $this->_translateOpr($filter->condopr), $val, $this->_sql->getAs($filter->sid), $ref_as );
break;
default:
break;
}
}
}
private function _convertType($typeab,$filter,$value='0'){
$sid=$filter->sid;
$map=$filter->map;
$condition=$filter->condopr;
$q='';
switch($typeab){
case'32':
return WGlobals::getEID();
break;
case'1':
if(empty($filter->sid)){
break;
}
if(( is_numeric($value) && $value==0 || $value=='') && ($filter->typeb !=130 && $filter->typeb !=50 && $filter->typeb !=2 )){
$valObj=new stdClass;
$as=$this->_sql->getAs($filter->ref_sid );
$valObj->_as=$as;
$valObj->map=$filter->refmap;
if(!empty($filter->bktbefore))$this->_sql->openBracket($filter->bktbefore );
if(!empty($filter->bktafter))$this->_sql->closeBracket($filter->bktafter );
return $valObj;
}else{
switch($condition){
case '6':
 $this->_sql->where($filter->map, '!=','',$this->_sql->getAs($filter->sid), null, $filter->bktbefore, $filter->bktafter, $filter->logicopr );
break;
case '7':
 $this->_sql->isNull($filter->map, true, $this->_sql->getAs($filter->sid), $filter->bktbefore, $filter->bktafter, $filter->logicopr );
break;
case '8':
 $this->_sql->isNull($filter->map, false, $this->_sql->getAs($filter->sid), $filter->bktbefore, $filter->bktafter, $filter->logicopr );
break;
case '15':
if(!is_array($value))$value=explode(',', str_replace(' ','',$value ));
$this->_switchWhereIN($filter, $value, false);
break;
case '16':
if(!is_array($value))$value=explode(',', str_replace(' ','',$value ));
$this->_switchWhereIN($filter, $value, true);
break;
case '30':
case '31':
if(empty($value))$value=0;
$value='%'.$value.'%';
$this->_switchWhere($filter, $value);
break;
case '32':
case '34':
if(empty($value))$value=0;
$value=$value.'%';
$this->_switchWhere($filter, $value);
break;
case '33':
case '35':
if(empty($value))$value=0;
$value='%'.$value;
$this->_switchWhere($filter, $value);
break;
default:
if(empty($value))$value=0;
$this->_switchWhere($filter, $value );
break;
}
}
break;
case '2':
case '8':
if(!isset($filter->refmap)) return null;
if( is_numeric($filter->refmap) && $filter->refmap==0){
$myValue=0;
return (int)$myValue;
}
if(empty($filter->refmap)){
return '';
}
return $filter->refmap;
break;
case '21':
return time();
break;
case'33':
case'30':
$data=WForm::getPrev($filter->refmap );
if($typeab==33){
$formName=(!empty($this->_layout->formName)?$this->_layout->formName : WGlobals::get('parentFormid','','global'));
$myForm=WView::form($formName );
if(empty($data))$data=WGlobals::getSession('ViewFilter','filter-'.$formName.'-'.$filter->refmap );
if(is_array($data))$data=$data[0];
$myForm->hidden($filter->refmap, $data );
if(!empty($data)) WGlobals::setSession('ViewFilter','filter-'.$formName.'-'.$filter->refmap, $data );
}
if(empty($data)){
if(!empty($filter->requiresvalue)){
$message=WMessage::get();
$message->exitNow('Unauthorized access 967');
}
return null;
}
return $data;
break;
case'35':
return WGlobals::get($filter->refmap, null, 'global');
case'50':
static $obj=null;
if(!isset($obj))$obj=WGlobals::getSession('JoobiUser');
$refmap=$filter->refmap;
if(isset($obj->$refmap)){
return $obj->$refmap;
}else{
return false;
}
break;
case'51':
static $sessionVar=array();
$refmap=$filter->refmap;
if(!isset($sessionVar[$refmap])){
$sessionVar[$refmap]=WGlobals::get($refmap, null, 'session');
}
return $sessionVar[$refmap];
break;
case'52':
$refmap=$filter->refmap;
if(!empty($refmap)){
$arrayMap=explode('.',$refmap);
$domain=array_shift($arrayMap);
$variable=array_shift($arrayMap);
$sessDomain=WGlobals::getSession($domain, $variable, '');
if(!empty($arrayMap)){
$property=array_shift($arrayMap);
return $sessDomain->$property;
}else{
return $sessDomain;
}
}
return '';
break;
case'55':
return WUser::getSessionId();
break;
case'90':
$filterClass=WClass::get($filter->refmap, null, 'filter');
if( method_exists($filterClass, 'create')){
$filterClass->model=&$this->_sql;
$returnedArry=$filterClass->create();
if(!empty($returnedArry)){
return $returnedArry;
}
if($returnedArry===false) return false;
}
break;
case'100':
if( defined($filter->refmap )) return constant($filter->refmap);
else null;
break;
case'130':
if( strpos($filter->refmap, '.')===false){
return WPref::load($filter->refmap );
}else{
$prefA=explode('.',$filter->refmap );
$prefName=array_pop($prefA);
$prefExtension=implode('.',$prefA );
WPref::get($prefExtension );
$prefExtension=str_replace('.','_',$prefExtension );
$value=constant('P'. strtoupper($prefExtension .'_'. $prefName));
if(empty($value)) return false;
return $value;
}
break;
case'110':
return $filter->ref_sid;
break;
case '120':
return WModel::get($filter->ref_sid,'dbtid');
break;
case'150':
$q=WUser::get($filter->refmap );
if(empty($q)) return null;
return $q;
break;
case'5':
$q='';
break;
case'90':
$q='';
break;
case'7':
return '';
break;
default:
break;
}
return $q;
}
 private function _switchWhere($filter,$value){
 if(is_array($value)){
 return $this->_switchWhereIN($filter, $value, false);
 }
 $condition=$this->_translateOpr($filter->condopr );
if( is_object($value)){
$realvalue=$value->map;
$as2=$value->_as;
}else{
$realvalue=$value;
$as2=null;
}
$externalSiteA=isset($filter->extstida )?$filter->extstida : null;
$externalSiteB=isset($filter->extstidb )?$filter->extstidb : null;
$this->_sql->where($filter->map, $condition, $realvalue, $this->_sql->getAs($filter->sid), $as2, $filter->bktbefore, $filter->bktafter, $filter->logicopr, 'default',$externalSiteA, $externalSiteB );
 }
 private function _switchWhereIN($filter,$value,$notIn=false){
if(empty($value )) return;
$externalSiteA=isset($filter->extstida )?$filter->extstida : null;
$externalSiteB=isset($filter->extstidb )?$filter->extstidb : null;
$this->_sql->whereIn($filter->map, $value, $this->_sql->getAs($filter->sid), $notIn, $filter->bktbefore, $filter->bktafter, $filter->logicopr,false,$externalSiteA, $externalSiteB );
 }
private function _translateOpr($opr){
switch ($opr){
case'29':
$condition='=';
break;
case'1':
$condition='!=';
break;
case'2':
$condition='>';
break;
case'3':
$condition='>=';
break;
case'4':
$condition='<';
break;
case'5':
$condition='<=';
break;
case'7':
$condition='=';
break;
case'8':
$condition='!=';
break;
case'32':
case'33':
case'30':
$condition=' LIKE ';
break;
case'31':
case'34':
case'35':
$condition=' NOT LIKE ';
break;
default:
$condition='=';
break;
}
return $condition;
}
 }
