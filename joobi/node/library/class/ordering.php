<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
abstract class Library_Ordering_class {
public static function getOrderedList($parent,$data,$type=1,$categoryRoot=false,&$childOrderParent,$reassignParent=true){
$children=array();
$gotParent=false;
WGlobals::set('getOrderedList', true, 'global');
if($reassignParent){
$reassignedParentID=0; 
$parentList=array();
$itemList=array();
foreach($data as $value){
$prt=$parent['parent'];
$parentList[$value->$prt]=true;
$pkey=$parent['pkey'];
$itemList[$value->$pkey]=true;
}
$parentDoesNOTExist=array();
if(!empty($parentList)){
unset($parentList[0] );
foreach($parentList as $oneParentList=> $valNOTUSED){
if(!in_array($oneParentList, array_keys($itemList)))$parentDoesNOTExist[$oneParentList]=true;
}}if(!empty($parentDoesNOTExist))$parentDoesNOTExist=array_keys($parentDoesNOTExist);
else $reassignParent=false;
}
foreach($data as $value){
$prt=$parent['parent'];
$pt=$value->$prt;
if($pt > 0)$gotParent=true;
if($reassignParent){
if( in_array($pt, $parentDoesNOTExist))$pt=$reassignedParentID;
}$list=(isset($children[$pt])?$children[$pt] : array());
array_push($list, $value );
$children[$pt]=$list;
}
if(!isset($children[0])){
if(!isset($parent['ordering']))  $parent['ordering']=$parent['name'];
if(empty($data[0])){
return $data;
}
$prt=$parent['parent'];
$myParentValue=$data[0]->$prt;
$parentIDTable=array();
$newArray=array();
$newArrayAlreadyUsedID=array();
$totalItm=count($data );
foreach($data as $nonKey=> $child){
$prt=$parent['parent'];
$pkey=$parent['pkey'];
$parentIDTable[$child->$pkey]=$child->$prt;
}
foreach($data as $nonKey=> $child){
$pkey=$parent['pkey'];
$prt=$parent['parent'];
$nm=$parent['name'];
$ord=$parent['ordering'];
$myParentValue=$child->$prt;
$ct=0;
while (isset($parentIDTable[$myParentValue]) && $parentIDTable[$myParentValue] !=0 && $ct <=$totalItm){
$ct++;
$myParentValue=$parentIDTable[$myParentValue];
};
if($myParentValue!=0 && !isset($newArrayAlreadyUsedID[$myParentValue])){
$gostItem=new stdClass;
$newArrayAlreadyUsedID[$myParentValue]=true;
$gostItem->$pkey=$myParentValue;
$gostItem->$nm='';
$gostItem->$prt=0;
$gostItem->$ord=1;
$gostItem->ghost87=true;$newArray[0][]=$gostItem ;
$parentIDTable[$myParentValue]=0;
}
}
foreach($children as $childKey=> $child){
$newArray[$childKey]=$child;
}
$children=$newArray;
}
$totalIndent=0;
if($gotParent)$list=WOrderingTools::treeRecurse($parent, 0, '',array(), $children, 999, 0, $type, $categoryRoot, $childOrderParent, $totalIndent );
else $list=$data;
return $list;
}
public static function treeRecurse($parent,$id,$indent,$list,&$children,$maxlevel=999,$level=0,$type=1,$categoryRoot=false,&$childOrderParent,$totalIndent){
$spacerCharacter='¦';
if(isset($children[$id]) && $level <=$maxlevel){
$spacer='';
$noIndent=false;
if($categoryRoot){
$prt=$parent['parent'];
$myTest=$children[$id][0]->$prt;
if($myTest=='1'){
$noIndent=true;
$spacer='&nbsp; ';
}elseif($myTest=='0'){
$noIndent=true;
}}
if($type==2 || $noIndent){
$pre ='';
$spacer .='';
$indent='';
}elseif($type==1){
$pre ='¦&nbsp;&nbsp; ';$spacer=$spacerCharacter.'&nbsp;&nbsp; ';}else{
$pre ='- ';
$spacer='&nbsp; ';}
foreach($children[$id] as $v){
$pkey=$parent['pkey'];
$prt=$parent['parent'];
$nm=$parent['name'];
$ord='';
if(isset($parent['ordering'])){
$ord=$parent['ordering'];
$childOrderParent[$v->$prt][$v->$ord]=$v->$pkey;
}
$id=$v->$pkey;
if(!isset($v->$nm)){
continue;
}
if($v->$prt==0){
$txt=$v->$nm;
}else{
$txt=$pre . $v->$nm;
}
$pt=$v->$nm;
$list[$id]=$v;
$list[$id]->$nm="$indent$txt";
$list[$id]->indentTreeNumber=$totalIndent;
$nextCountIndent=$totalIndent + 1;
$nextIndent=$indent . $spacer;
if(!isset($list[$id])){
$keys=array_keys($list);
$parentID=array_shift($keys);
$gostItem=new stdClass;
$gostItem->$pkey=$parentID;
$gostItem->$nm='';
if(!empty($ord))$gostItem->$ord=99;
$gostItem->$prt=0;$gostItem->ghost87=true;
$newArray=array();
$newArray[$id]=array($gostItem );
foreach($list as $childKey=> $child){
$newArray[$childKey]=$child;
}
$list=$newArray;
}
$list=WOrderingTools::treeRecurse($parent, $id, $nextIndent, $list, $children, $maxlevel, $level+1, $type, $categoryRoot, $childOrderParent, $nextCountIndent );
}
}
return $list;
}
}