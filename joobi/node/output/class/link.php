<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Output_Link_class {
var $_absoluteLink='';
private $_index=false;
var $_itemid=true;
var $_ssl=false;
var $_model=null;
private $_propertyList=array();
public function convertLink($lien,$object=null,$extras='',$model=null,$mapList=array(),$route=true){
if(empty($lien)) return '';
if('#'==$lien ) return '#';
$this->_model=$model;
$this->_propertyList=$mapList;
$newLink=$this->_processLink($lien, $object );
if($route){
$option=null;
if(!empty($this->wid) AND WGlobals::get('appType','application','global')=='module'){
$pref=WPref::get($this->wid);
if($this->_itemid===true)$this->_itemid=$pref->getPref('itemid',true);
$option=$pref->getPref( JOOBI_URLAPP_PAGE, '');
if( strlen($option) < 3)$option=null;
}
return WPage::routeURL($newLink . $extras, $this->_absoluteLink, $this->_index, $this->_ssl, $this->_itemid, $option ); 
}else{
return $newLink;
}
}
public function setIndex($type=''){
$this->_index=$type;
}
private function _processLink($lien,$object){
$bracketSpos=strpos($lien, '(');
if($bracketSpos===false){
return $lien;
}
$bracketEpos=strpos($lien, ')')-1;
$tag=substr($lien, $bracketSpos+1, $bracketEpos-$bracketSpos );
$explodedTag=explode('=',$tag );
$map=$explodedTag[0];
$extraLien='';
if(isset($explodedTag[1])){
$val=$explodedTag[1];
switch($map){
case 'page':
$extraLien=JOOBI_URLAPP_PAGE. '='.$val;
break;
case 'index':
static $popuploaded=false;
$this->_index=$val;
if(!$popuploaded && $val=='popup'){
WExtension::includes('joobibox');
$popuploaded=true;
}
break;
case 'link':
$this->_absoluteLink=$val;
break;
case 'itemid':
$this->_itemid=$val;
break;
case 'ssl':
$this->_ssl=$val;
break;
default:
$connector=($map=='#')?'#': '&'.$map.'=';
$extraLien='';
switch($val){
case 'eid':
$eid=WGlobals::getEID();
if(!empty($eid)){
$extraLien=$connector . $eid;
}else{
if(isset($this->_model)){
if($this->_model->multiplePK()){
foreach($this->_model->getPKs() as $primkey){
$specialMap=$primkey.'_'.$this->_model->getModelID();
if(!isset($object->$specialMap)) continue;
$extraLien .='&'.$primkey.'='.$object->$specialMap;
}
}else{
$pKey=$this->_model->getPK();
if(isset($object->$pKey))$eid=$extraLien=$connector . $object->$pKey;
}
}
}
break;
case 'returnid':
$returnId=WView::getURI();
$realVal=base64_encode($returnId);
if(!empty($realVal))$extraLien=$connector . $realVal;
break;
default:
if( is_numeric($val)){
$extraLien=$connector . $val;
}else{
$semiPos=strpos($val, ':');
if($semiPos===false){
$realVal=WGlobals::get($val, '','','string');
if(!empty($realVal)){
$finalValue=$realVal;
}elseif(isset($this->_propertyList[$val])){
$realVal=$this->_propertyList[$val];
$finalValue=(isset($object->$realVal))?$object->$realVal : '';
}else{ 
break;
}
}else{
$modelColumnA=explode(':',$val );
$UKSID=WModel::get($modelColumnA[0], 'sid');
$realVal=$modelColumnA[1] .'_'.$UKSID;
if(isset($object->$realVal)){
$finalValue=$object->$realVal;
}else{
$finalValue='';
}
}
if(!is_int($finalValue)){
$finalValue=WGlobals::filter($finalValue, 'string');
$finalValue=str_replace( array('.&nbsp;','<sup>L</sup>','&nbsp;','- '), array(''), $finalValue );
}
$extraLien=$connector. $finalValue;
}
break;
}
break;
}
}elseif($map=='returnid'){
$extraLien='&returnid='.base64_encode( WView::getURI());
}elseif('noframe'==$map){
$extraLien=URL_NO_FRAMEWORK;
}
$newlien=substr($lien, 0, $bracketSpos ). $extraLien;
$endLink=substr($lien, $bracketEpos+2 );
if(!empty($endLink)){
$endLink=$this->_processLink($endLink, $object );
}
$newlien .=$endLink;
return $newlien;
}
}
