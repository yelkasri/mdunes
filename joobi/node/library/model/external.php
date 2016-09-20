<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WModel_External {
  function load($query,$type=null,$selects=null){
$query->extstid;
 $this->_select=null;
 $this->_orderby=null;
 $this->_whereValues=null;
 $this->_as_cdC=null;
$selectGrp=array();
$extSelect=0;
foreach($query->_select as $key=> $value){
$extSelect=substr(strrchr($key, '.'), 1);
if($extSelect==3){$selectGrp[$extSelect][$key]=$value;
}elseif($extSelect==7){$selectGrp[$extSelect][$key]=$value;
}else{$selectGrp[$extSelect][$key]=$value;
}}$this->_select=$selectGrp;
$as_cd=$query->_as_cd;
$asCdA=null;
$extModel=null;
foreach($as_cd as $modelID=> $alias){
if(!isset($extModel)){$extModel=WModel::get('model');
$extModel->whereE('sid',$modelID);
$namekey=$extModel->load('lr','namekey');
}
$asCdA[$namekey]->local=$modelID;
}$this->_as_cdC=$asCdA;
$this->_as_cd=$as_cd;
$whereGrp=array();
if(!empty($query->_whereValues)){
foreach($query->_whereValues as $key=> $data){
if(isset($data->extstida)){
if(($data->extstida)==3){
$whereGrp[$data->extstida][]=$data;
}elseif(($data->extstida)==7){
$whereGrp[$data->extstida][]=$data;
}}else{
$whereGrp[0]=$data;
}}}
$this->_whereValues=$whereGrp;
$select=$this->_select;
foreach($select as $key=> $data){
if(empty($key)) unset($select[0]);
}
$this->_select=$select;
$thisSelect=null;
$thisWhere=null;
$this->_select=$thisSelect;$this->_whereValues=$thisWhere;
$data=$this;
$query=$query->load($type, $selects);
$retdata=$this->connect($data);
$mergedata=null;
return $mergedata;
  }
  function connect($data){
$netcom=WNetcom::get();
}}