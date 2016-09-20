<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Output_Checktype_class extends WClasses {
public function removeUnnecessaryElements(&$object){
$checkThoseFormsA=array();
$checkThoseFieldsA=array();
foreach($object->elements as $multiform){
if(!empty($multiform->checktype)){
$checkThoseFormsA[$multiform->fdid]=$multiform->namekey;
$checkThoseFieldsA[$multiform->fid]=$multiform->fdid;
}
}
if(empty($checkThoseFieldsA)) return false;
$modelFieldTypeM=WModel::get('design.modelfieldstype');
$modelFieldTypeM->whereIn('fdid',$checkThoseFieldsA );
$modelFieldTypeM->orderBy('fdid');
$allFieldAndTypeA=$modelFieldTypeM->load('ol');
if(empty($allFieldAndTypeA)) return false;
$perFieldA=array();
foreach($allFieldAndTypeA as $oneField){
$perFieldA[$oneField->fdid][$oneField->typeid]=$oneField->typeid;
}
$typeColumn=$object->_model->getItemTypeColumn();
if( is_object($typeColumn)){
$newModelM=WModel::get($typeColumn->model );
$newModelM->whereE($typeColumn->pk, $object->getValue($typeColumn->pk ));
$newModelM->select($typeColumn->seek );
$currentType=$newModelM->load('lr');
}else{
$currentType=$object->getValue($typeColumn );
}
if(empty($currentType)) return false;
$hasFieldA=array();
foreach($perFieldA as $fdidVal=> $oneFIeld){
$hasType=false;
foreach($oneFIeld as $myType){
if($myType==$currentType){
$hasType=true;
}
}
if(!$hasType){
$object->removeElements($checkThoseFormsA[ $fdidVal ] );
}
}
}
 }
