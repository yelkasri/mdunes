<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WListing_Coreorder extends WListings_default{
public function createHeader(){
if(empty($this->element->align))$this->element->align='center';
if(empty($this->element->width))$this->element->width='16px';
return false;
}
function create(){
static $myReferenceIdTable=array();
if(isset($this->orderingMap))$orderingMap=$this->orderingMap;
$orderingByGroup=(isset($this->orderingByGroup))?$this->orderingByGroup : 'ordering_'.$this->modelID;
if(empty($myReferenceIdTable))$myReferenceIdTable=$this->myReferenceIdTable;
if($this->categoryRoot){$jj=$this->i+1;
$parentMapKey=$this->myParent['parent'];
$orderingMapKey=$this->myParent['ordering'];
if(isset($this->childOrderParent) && isset($this->childOrderParent[$this->data->$parentMapKey])){
$indexMinus=$this->childOrderParent[$this->data->$parentMapKey][$this->data->$orderingMapKey - 1];
$indexPlus=$this->childOrderParent[$this->data->$parentMapKey][$this->data->$orderingMapKey + 1];
}else{
$indexMinus=0;
$indexPlus=0;
}
}else{$jj=$this->i;
$indexMinus=(isset($myReferenceIdTable[$jj-1])?$myReferenceIdTable[$jj-1] : 0 );
$indexPlus=(isset($myReferenceIdTable[$jj+1])?$myReferenceIdTable[$jj+1] : 0 );
}
$htmlData='';
if(!empty($this->data->$orderingMap )){
if(isset($this->data->$orderingMap)){
$taskUp=(isset($this->listData[$indexMinus]) && isset($this->listData[$indexMinus]->$orderingMap) && $this->data->$orderingMap < $this->listData[$indexMinus]->$orderingMap)?'orderdown' : 'orderup';
$taskDown=(isset($this->listData[$indexPlus]) && isset($this->listData[$indexPlus]->$orderingMap) && $this->data->$orderingMap > $this->listData[$indexPlus]->$orderingMap)?'orderup' : 'orderdown';
}else{
$taskUp='orderdown';
$taskDown='orderup';
}
$enabled=true;
$vv=(isset($this->data->$orderingByGroup)?$this->data->$orderingByGroup : 0 );
$vb=(isset($this->listData[$indexPlus]->$orderingByGroup)?$this->listData[$indexPlus]->$orderingByGroup : 0 );
$vd=(isset($this->listData[$indexMinus]->$orderingByGroup)?$this->listData[$indexMinus]->$orderingByGroup : 0 );
$vh=(isset($this->data->$orderingMap)?$this->data->$orderingMap : 0 );
$htmlData .='<div style="float:left;">'. $this->_orderIcon($this->i, $this->nb, ($vv==$vb ), $taskDown, $enabled, $this->formName ). '</div>';
$disabled=($this->currentOrder==$orderingMap? '' : 'disabled="disabled" class="disabled"');
$htmlData .= '<div style="float:left;"><input type="text" name="order[]" size="3" value="'.$vh.'" '.$disabled.' style="text-align:center" /></div>';
$htmlData .='<div style="float:left;">'.$this->_orderIcon($this->i, $this->nb, ($vv==$vd ), $taskUp, $enabled, $this->formName ). '</div>';
}
$this->content='<div style="display:inline-flex;">'.$htmlData.'</div>';
return true;
}
private function _orderIcon($i,$n,$condition=true,$task='orderdown',$enabled=true,$formName=''){
static $downGreen=array();
static $downGray=array();
static $jsNamekeyA=array();
if($task !='orderdown'){
$taskDir='up';
$alt=WText::t('1242282450QJCO');
$complexCondition=(($i > 0 || ($i + $this->limitstart > 0)) && $condition );
}else{
$taskDir='down';
$alt=WText::t('1242282450QJCN');
$complexCondition=(($i < $n -1 || $i + $this->limitstart < $this->total - 1) && $condition );
}
$html=WGet::$rLine.'&nbsp;';
if($complexCondition){
if($enabled){
if(empty($jsNamekeyA[$taskDir])){
if( WGet::isDebug()){
$jsNamekeyA[$taskDir]='Order'.$taskDir.'_'.WGlobals::filter($formName.'_'.$task.'_'.$taskDir, 'jsnamekey');
}else{
$jsNamekeyA[$taskDir]='WZY_'.WGlobals::count('f');
}}
if( WPref::load('PLIBRARY_NODE_AJAXPAGE')){
$paramsO=WObject::newObject('output.jsaction');
$paramsO->form=$formName;
$paramsO->namekey=$jsNamekeyA[$taskDir];
$mapKey=$this->pkeyMap;
if(!is_string($mapKey) || empty($this->data->$mapKey)) return '';
$valueA=array('eid'=> $this->data->$mapKey, 'order'=>'em'.$i, 'zact'=> $taskDir );
if(!empty($this->htmlObj->nestedView)){
$valueA['vWjx']=$this->yid;
$valueA['fRmjx']=$formName;
}
$joobiRun=WPage::jsAction('order',$paramsO, $valueA );
}else{
$joobiRun='return '.WPage::actionJavaScript($task, $formName, array('zsid'=> $this->element->sid, 'zact'=> $taskDir, 'lstg'=>true), 'em'.$i, $jsNamekeyA[$taskDir], 'order');
}
$html=WGet::$rLine.'<a href="#" onclick="'.$joobiRun.'" title="'.$alt.'">';
if(!isset($downGreen[$taskDir] )){
$legendO=new stdClass;
$legendO->sortUpDown=true;
$legendO->action=$taskDir.'Green';
$legendO->alt=$alt;
$downGreen[$taskDir]=WPage::renderBluePrint('legend',$legendO );
}
$html .=$downGreen[$taskDir];
$html.='</a>'.WGet::$rLine;
}else{
if(!isset($downGray[$taskDir] )){
$legendO=new stdClass;
$legendO->sortUpDown=true;
$legendO->action=$taskDir.'Gray';
$legendO->alt=$alt;
$downGray[$taskDir]=WPage::renderBluePrint('legend',$legendO );
}$html=$downGray[$taskDir];
}}else{
$html='<div style="width:25px;">&nbsp;</div>'.WGet::$rLine;
}
return $html;
}
public function advanceSearch(){
return false;
}
public function searchQuery(&$model,$element,$searchedTerms=null,$operator=null){
}
}
