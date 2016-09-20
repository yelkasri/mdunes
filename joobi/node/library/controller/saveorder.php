<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class WController_Saveorder {
 public function saveorder($valuesToOrder,$sidToOrder,$valueGroup=''){
 if(empty($valuesToOrder)) return true;
$model=WModel::get($sidToOrder );
$model->log();
$groupingMap=$model->getParam('grpmap','');
if($model->multiplePK()){
foreach($model->getPKs() as $modpkey){
if($modpkey !=$groupingMap){
$pKey=$modpkey;
break;
}
}
}else{
$pKey=$model->getPK();
}
$parent=($groupingMap!='parent' && !$model->multiplePK() && $model->columnExists('parent'));
if($parent)$model->select('parent');
if(!empty($groupingMap)){
$model->select($groupingMap );
if($model->multiplePK()){
$actualGroup=empty($valueGroup)?WGlobals::get($groupingMap,0) : $valueGroup;
if(!empty($actualGroup)){
$model->whereE($groupingMap,$actualGroup);
}else{
$message=WMessage::get();
$message->codeE('We can not order a cross table if the grouping map "'.$groupingMap.'" is not passed through the form',array(),0);
return false;
}
}
}
$model->select('ordering');
$model->select($pKey);
$mySpecialKeys=array_keys($valuesToOrder);
$model->whereIn($pKey, $mySpecialKeys);
if($parent)$model->whereIn('parent',$mySpecialKeys,0,null,0,0,1);
if(!empty($groupingMap))$model->orderBy($groupingMap);
$model->setLimit( 10000 );
$actualElements=$model->load('ol');
$actualValues=array();
foreach($actualElements as $value){
$actualValues[$value->$pKey]=$value;
if(!isset($valuesToOrder[$value->$pKey]))$valuesToOrder[$value->$pKey]=$value->ordering;
}
asort($valuesToOrder);
$orderId=array();
foreach($valuesToOrder as $eid=> $order){
if(!isset($actualValues[$eid])) continue;
$value=$actualValues[$eid];
$groupValue=empty($groupingMap)?0 : $value->$groupingMap;
$num=$order;
while(isset($orderId[$groupValue][$num]))$num++;
$value->newOrder=$num;
$orderId[$groupValue][$num]=$value;
}
if($parent){
foreach($orderId as $groupingValue=> $orderbyGroup){
if(count($orderbyGroup)<2) continue;
$params=array();
$params['pkey']=$pKey;
$params['parent']='parent';
$params['name']='newOrder';
$childOrderParent=array();
$orderId[$groupingValue]=WOrderingTools::getOrderedList($params, $orderId[$groupingValue], 2, false, $childOrderParent );
}
}
$redo=array();
foreach($orderId as $groupValue=> $orderGroupId){
if(empty($orderGroupId)) continue;
$firstElement=reset($orderGroupId);
$orderNum=$firstElement->newOrder;
if($orderNum < 1 OR $orderNum>=90)$orderNum=1;
foreach($orderGroupId as $element){
if(($element->newOrder !=$orderNum ) OR ($element->newOrder !=$element->ordering )){
$redo[]=array($element->$pKey, $orderNum, $groupValue );
}$orderNum++;
}
}
$alreadyReorded=array();
foreach($redo as $values){
$model=WModel::get($sidToOrder );
$myKeyRedo=$pKey.'='.$values[0];
if(!empty($values[2]) && $groupingMap)$myKeyRedo .='-'.$groupingMap.'='.$values[2];
if(!isset($alreadyReorded[$myKeyRedo])){
$alreadyReorded[$myKeyRedo]=true;
if($model->getParam('validtoggle', false)){$model->$pKey=$values[0];
if(!empty($values[2]) && $groupingMap)$model->$groupingMap=$values[2];
$model->setLimit(1);
$model->ordering=$values[1];
$model->save();
}else{$model->whereE($pKey,$values[0]);
if(!empty($values[2]) && $groupingMap)$model->whereE($groupingMap,$values[2]);
$model->setLimit(1);
$model->update( array('ordering'=>$values[1]));
}
}}
return true;
 }
public function reOrder($order,$sidToOrder){
$eid=WGlobals::getEID();
if(empty($eid)) return false;
$model=WModel::get($sidToOrder );
if($model->getParam('grptable',false)){
$model=WModel::get($model->getParam('grptable'));
}
$groupMap=$model->getParam('grpmap',false);
if($model->multiplePK()){
foreach($model->getPKs() as $modpkey){
if($modpkey !=$groupMap){
$k=$modpkey;
break;
}
}
}else{
$k=$model->getPK();
}
$parent=($groupMap !='parent' && !$model->multiplePK() && $model->columnExists('parent'));
$model->select($k);
$model->select('ordering');
if($parent)$model->select('parent');
if($groupMap){
$model->select($groupMap);
if($model->multiplePK()){
$actualGroup=WGlobals::get($groupMap,0);
if(!empty($actualGroup)){
$model->whereE($groupMap,$actualGroup);
}
else{
$message=WMessage::get();
$message->codeE('We can not order a cross table if the grouping map "'.$groupMap.'" is not passed through the form submission',array(),0);
return false;
}
}
}
$model->whereE($k, $eid );
$oldElement=$model->load('o');
$dirn=$order;
$model->select($k );
$model->select('ordering');
if($dirn<0){
$sign='<';
$order='DESC';
}elseif($dirn>0){
$sign='>';
$order='ASC';
}else{
$sign='=';
$order='DESC';
}
$model->where('ordering',$sign, $oldElement->ordering );
if($parent)  $model->whereE('parent',$oldElement->parent);
if($groupMap)$model->whereE($groupMap, $oldElement->$groupMap );
$model->orderBy('ordering',$order);
$row=$model->load('o');
$status=true;
if(!empty($row)){
if($model->getParam('validtoggle', false)){
$model->resetAll();
$model->ordering=$row->ordering;
$model->$k=$oldElement->$k;
if($groupMap && !empty($oldElement->$groupMap))$model->$groupMap=$oldElement->$groupMap;
$model->setLimit(1);
$model->save();
$model->resetAll();
$model->ordering=$oldElement->ordering;
$model->$k=$row->$k;
if($groupMap && !empty($oldElement->$groupMap))$model->$groupMap=$oldElement->$groupMap;
$model->setLimit(1);
$status=$model->save();
}else{
$model->setVal('ordering',$row->ordering );
$model->whereE($k, $oldElement->$k );
if($groupMap AND !empty($oldElement->$groupMap))$model->whereE($groupMap,$oldElement->$groupMap);
$model->setLimit(1);
$model->update();
$model->setVal('ordering',$oldElement->ordering);
$model->whereE($k, $row->$k );
if($groupMap AND !empty($oldElement->$groupMap))$model->whereE($groupMap,$oldElement->$groupMap);
$model->setLimit(1);
$status=$model->update();
}}
if($parent){
$model->select('ordering');
$model->select($k);
if($groupMap)$model->whereE($groupMap, $oldElement->$groupMap );
$model->setLimit( 10000 );
$results=$model->load('ol');
$eids=array();
foreach($results as $myresult){
$eids[$myresult->$k]=$myresult->ordering;
}
return $this->saveorder($eids,$model->getParam('grptable',$model->getModelID()));
}
return $status;
}
 }