<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Category_Node_model extends WModel {
public $_returnId=true;
protected $_deleteLeaf=false;
protected $_parent=null; protected $_lft=null;protected $_rgt=null;
protected $_recalculate=false; 
protected $_under=null;public $_keepAttributesOnDelete=true;
protected $_parentIdentifier='root';
protected $_dontExportA=array('lft','rgt');
function addValidate(){
$parentMap=$this->getParam('parentmap','parent');
if(empty($this->$parentMap )){
if(empty($this->_parentIdentifier )){
$this->codeE('Category_Node_model: '.$this->_infos->namekey.'. The parent Identifier is not set the parent could not be set.');
}else{
$modelM=WModel::get($this->_infos->sid );
$modelM->whereE('namekey',$this->_parentIdentifier );
$this->$parentMap=$modelM->load('lr',$this->getPK());
if(!isset($this->$parentMap )){
$this->$parentMap=1; $this->userE('1406069322OHER');
WMessage::log('category parent not defined : '.$parentMap, 'ERROR-Category_Node_model');
WMessage::log( print_r( debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ), true) , 'ERROR-Category_Node_model');
WMessage::log($this , 'ERROR-Category_Node_model');
}}
}
$this->_parent=$this->$parentMap;
return true;
}
function editValidate(){
$mypkey=$this->getPK();
$parentMap=$this->getParam('parentmap','parent');
if(empty($this->$parentMap)){
$COLUMN=$parentMap;
$this->codeE('You need to put a form element for the column: '.$COLUMN );
}else{
$this->_parent=$this->$parentMap;
}
if(!empty($this->$mypkey)){
$catM=WModel::get($this->getModelID());
$catM->whereE($mypkey, $this->$mypkey );
$oldVersion=$catM->load('o',array($parentMap, 'lft','rgt'));
if(empty($this->$parentMap)){
$this->$parentMap=$oldVersion->$parentMap;
}elseif($oldVersion->$parentMap !=$this->$parentMap){
if($this->$parentMap==$this->$mypkey){
$message=WMessage::get();
$message->userW('1213020899EZVY');
return false;
}
$catM=WModel::get($this->getModelID());
$catM->whereE($mypkey, $this->$parentMap );
$wantedparent=$catM->load('o',array('lft','rgt',$parentMap));
if(($wantedparent->lft > $oldVersion->lft) && ($wantedparent->rgt < $oldVersion->rgt)){
$message=WMessage::get();
$message->userW('1220361707FSCI');
return false;
}
$this->_recalculate=true;
}}return true;
}
function addExtra(){
$myParent=$this->getParam('parentmap','parent');
if(empty($this->$myParent)) return true;
$parentModel=$this->getParam('modelParent',0);
if(empty($parentModel)){
    if(!$this->getParam('skipdepth',false))$this->select('depth');
$parent=$this->load($this->$myParent, array($myParent , 'lft','rgt'));
if(empty($parent)){
  $mess=WMessage::get();
  $mess->codeE('Could not load the parent information with the name '.$myParent.' for the save of a category.');
return false;
}
}else{
    $model=WModel::get($parentModel);
  if(!$this->getParam('skipdepth',false))$this->select('depth');
  $parent=$model->load($this->$myParent, array($myParent, 'lft','rgt'));
  $model->whereE($this->getPK(), $parent->$myParent );
  $model->updatePlus('rgt', 2);
  $model->update();
}if(empty($parent)){
  $mess=WMessage::get();
  $mess->codeE('Could not load the parent model with the name '.$myParent.' for the save of a category.');
  return false;
}
if(!empty($parentModel)){
  $this->whereE($myParent, $parent->$myParent);
}
$this->where('rgt','>=',$parent->rgt);
$this->updatePlus('rgt', 2);
$this->returnId(false);
$this->update();
if(!empty($parentModel)){
  $this->whereE($myParent, $parent->$myParent);
}
$this->where('lft','>=',$parent->rgt);
$this->updatePlus('lft', 2);
$this->returnId(false);
$this->update();
$this->lft=$parent->rgt;
$this->rgt=$parent->rgt+1;
if(!$this->getParam('skipdepth',false))$this->depth=$parent->depth+1;
$this->$myParent=$this->_parent;
$id=$this->getPK();
$this->whereE($id, $this->$id);
$this->setLimit(1);
$this->update();
WGlobals::set('catdepth'.$this->getModelID(), null, 'session');
return true;
}
function editExtra($eid=null){
if($this->_recalculate)$this->redoTree();
return true;
}
 function deleteValidate($eid=0){
if(empty($eid)){
return true;
}
if($eid==1 ) return false;
$parentMap=$this->getParam('parentmap','parent');
$myPK=$this->getPK();
$this->whereE($myPK, $eid );
$result=$this->load('o',array('lft','rgt',$parentMap ));
if(empty($result)) return true;
$this->_lft=$result->lft;
$this->_rgt=$result->rgt;
$this->_parent=$result->$parentMap;
$return=true;
if(!$this->_deleteLeaf){
if($result->rgt - $result->lft !=1){
$mess=WMessage::get();
$NAME=$this->getTranslatedName();
$mess->userW('1235560364LCKV',array('$NAME'=>$NAME,'$NAME'=>$NAME));
$this->whereE($parentMap, $eid );
$this->setVal($parentMap, $this->_parent );
$this->setIgnore();
$return=$return && $this->update();
$this->setIgnore(false);
$this->where('rgt','<',$this->_rgt);
$this->where('lft','>',$this->_lft);
$this->updatePlus('rgt',-1);
$this->updatePlus('lft', -1);
if(!$this->getParam('skipdepth',false))$this->updatePlus('depth',-1);
$return=$return && $this->update();
}}else{
$modelChildM=WModel::get($this->_infos->sid );
$modelChildM->whereE($parentMap, $eid );
$modelChildM->deleteAll();
}
$this->where('rgt','>',$this->_rgt);
$this->updatePlus('rgt',-2);
$return=$return && $this->update();
$this->where('lft','>',$this->_rgt );
$this->updatePlus('lft', -2 );
$return=$return && $this->update();
return $return;
 }
 function deleteExtra($eid=0){
return true;
  }
public function getChildNode($eid='1',$displayme=false){
static $lftrgtA=array();
if(empty($eid)) return false;
$this->whereE($this->getPK() , $eid );
$currcat=$this->load('o',array('lft','rgt'));
if(empty($currcat)) return true;
if(($currcat->lft +1)==$currcat->rgt){
return $displayme?array($eid ) : array();
}
$key=$currcat->lft.'.'.$currcat->rgt.'.'.$this->_infos->sid;
if(!isset($lftrgtA[$key])){
if($displayme){
$this->where('lft','>=',$currcat->lft );
$this->where('rgt','<=',$currcat->rgt );
}else{
$this->where('lft','>',$currcat->lft );
$this->where('rgt','<',$currcat->rgt );
}$lftrgtA[$key]=$this->load('lra',$this->getPK());
}
return $lftrgtA[$key];
}
  function getAllParents($curr='1',$idOnly=false){
  $cpar=WModel::get($this->getModelID());   $cpar->makeLJ($this->getModelID(), 'lft','lft', 0, 1, '>='); 
      $namekeyModel=WModel::get($this->getModelID(), 'namekey');
  if(!$idOnly){
$namekeyTrans=$namekeyModel.'trans';
$sidTrans=WModel::get($namekeyTrans, 'sid', null, false);
  }
if(!empty($sidTrans)){
$parentModel=$namekeyTrans;
}else{
$parentModel=$this->getParam('prtname','');
}
$myPK=$this->getPK();
if(!$idOnly){
  if(!empty($parentModel )){
  $cpar->makeLJ($parentModel, $myPK, $myPK, 1, 2 );
  $cpar->whereLanguage(2);
  $cpar->select('name',2);
  }else{
  $cpar->select('name', 1 );
  }  $cpar->select('namekey', 1 );
}
$cpar->whereE($myPK, $curr, 0 );
  $cpar->where('rgt','>=','rgt',1,0);
  $cpar->where('rgt','!=', 0 , 0 );
  $cpar->orderBy('lft','ASC', 1 );
$cpar->setDistinct();
$cpar->select($myPK,1);
if(!$idOnly){
$parentsA=$cpar->load('ol');
}else{
$parentsA=$cpar->load('lra');
}
  return $parentsA;
  }
  public function getItemDefaultCategory($eid){
  return 0;
  }
function redoTree(){
$catModel=WModel::get($this->getModelID());
$catModel->orderBy('lft','ASC');
$myPK=$catModel->getPK();
$parentMap=$catModel->getParam('parentmap','parent');
if(!$catModel->getParam('skipdepth',false))$catModel->select('depth');
$catModel->select( array($parentMap, $myPK, 'lft','rgt'));
$catModel->setLimit( 10000000 );
$myCats=$catModel->load('ol');
$this->_mesCats=array();
foreach($myCats as $cat){
$this->_mesCats[$cat->$parentMap][]=$cat;
if(empty($cat->$parentMap))$root=$cat;
}
$infos=new stdClass;
$infos->lft=1;
$infos->depth=0;
$infos=$this->_redoTree($root, $infos, $myPK, $parentMap );
}
private function _redoTree($myElement,$infos,$myPK,$parentMap){
$myLft=$infos->lft;
$myDepth=$infos->depth;
if(!empty($this->_mesCats[$myElement->$myPK])){
$infos->depth ++;
foreach($this->_mesCats[$myElement->$myPK] as $ChildCat){
$infos->lft ++;
$infos=$this->_redoTree($ChildCat,$infos,$myPK,$parentMap);
}
$infos->depth --;
}
$infos->lft++;
if($infos->lft !=$myElement->rgt OR $myLft !=$myElement->lft OR (!$this->getParam('skipdepth',false) AND $myDepth!=$myElement->depth)){
$this->setVal('lft',$myLft);
$this->setVal('rgt',$infos->lft);
if(!$this->getParam('skipdepth',false))$this->setVal('depth',$myDepth );
$this->whereE($myPK,$myElement->$myPK);
$this->setLimit(1);
$this->update();
}
return $infos;
}
}