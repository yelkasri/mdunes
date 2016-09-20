<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
 class WController_Toggle {
 public function toggle(&$controller){
 $ajax=WGlobals::get('ajax');
$valueTo=$controller->getToggleValue('value');
$property=$controller->getToggleValue('map');
$modelNameM=$controller->getToggleValue('model');
$field=$controller->getToggleValue('field');
if( PLIBRARY_NODE_SECLEV > 1){
$secure=$controller->getToggleValue('secure');
$paramA=array();
$paramA['zsid']=$modelNameM;
$paramA['zmap']=$property;
if(empty($secure)){
$message=WMessage::get();
$message->userE('1417556518JIXD');
WPages::redirect('previous');
}elseif(!WTools::checkSecure($paramA, $secure )){
$message=WMessage::get();
$message->userE('1417556518JIXE');
WPages::redirect('previous');
}
}
if(!empty($field) && ! $property ) return false;
 WTools::checkRobots();
$status=false;
$mpk=false; 
 $eid=WGlobals::getEID();
if(empty($eid)){
$mpk=WGlobals::get('mpk');
if($mpk){
$multpleEID=array();
$myIdGot=WGlobals::get('myId');
$myIdGotA=explode(':',$myIdGot );
$pkWithValues=new stdClass;
foreach($myIdGotA as $onePKNow){
$onlyValueA=explode('|',$onePKNow );
if(!empty($onlyValueA[1])){
$multpleEID[]=$onlyValueA[1];
$myMapA=explode('_',$onlyValueA[0] );
$hui=$myMapA[0];
$pkWithValues->$hui=$onlyValueA[1];
}
}
if(empty($multpleEID)) return false;
}else{
return false;
}
}
$childInherit=array('publish');
if($ajax)$eid=WGlobals::get('myId');
$controller->_model=WModel::get($modelNameM, 'objectfile', null, false);
if(!$controller->_model->isReady()){
return false;
}
$controller->_model->setAudit('toggle');
$modifedColumnExist=$controller->_model->columnExists('modified');
$controller->_model->$property=$valueTo;
$pkey=$controller->_model->getPK();
if($controller->_model->getParam('validtoggle', false)){
if($modifedColumnExist)$controller->_model->modified=time();
if(!$controller->_model->multiplePK()){
$controller->_model->$pkey=$eid;
}else{
foreach($pkWithValues as $column56=> $value56){
$controller->_model->$column56=$value56;
}
}
$status=$controller->_model->save();
}else{
if($modifedColumnExist)$controller->_model->setVal('modified', time());
if(!$controller->_model->multiplePK()){
if($controller->_model->getAudit()){
$securityAuditA=WClass::get('security.audit', null, 'class', false);
if(!empty($securityAuditA))$securityAuditA->toggleUpdate($controller->_model->getModelID(), $eid, $property, $valueTo );
}
if(strpos($property,'[') !==false){
$controller->_model->whereE($pkey,$eid);
$allParams=$controller->_model->load('lr',array('params'));
list($p,$paramName)=explode('[',$property);
$paramName=trim($paramName,']');
$addedParam=new stdClass;
$addedParam->$paramName=$valueTo;
$this->_updateParams($allParams, $addedParam );
$controller->_model->whereE($pkey,$eid);
$controller->_model->setLimit(1);
$status=$controller->_model->update( array('params'=> $allParams ));
}else{
$controller->_model->whereE($pkey, $eid );
$controller->_model->setLimit(1);
$status=$controller->_model->update( array($property=> $valueTo ));
}
}else{
if(!empty($pkWithValues)){
foreach($pkWithValues as $column56=> $value56){
if($column56!='lgid'){
$controller->_model->whereE($column56, $value56 );
}else{
$controller->_model->whereE('lgid', WUser::get('lgid'));
}
}
$status=$controller->_model->update( array($property=> $valueTo ));
}else{
return false;
}
}
}
$prevID='';
if($status && $property==$controller->_model->getParam('premmap',false)){
$groupPremium=$controller->_model->getParam('premgroup', false);
if(!empty($groupPremium)){
$controller->_model->whereE($pkey, $eid );
$typeValue=$controller->_model->load('lr',$groupPremium );
$controller->_model->whereE($groupPremium, $typeValue );
}
$controller->_model->where($pkey, '!=',$eid );
$controller->_model->setVal($property , 0 );
if($modifedColumnExist)$controller->_model->setVal('modified', time());
$status=$controller->_model->update();
}
WGlobals::setEID( 0 );
if($ajax)$this->_toggleAjax($valueTo, $eid, $prevID );
else return $status;
 }
 public function customToggle($value,$eid,$prevID){
$this->_toggleAjax($value, $eid, $prevID );
 }
 private function _toggleAjax($value,$eid,$prevID){
 ob_start();
 $elemType=WGlobals::get('elemType');
 $divId=WGlobals::get('divId');
 $namekey=WGlobals::get('namekey');
 $em=WGlobals::get('em');
 $conf=WGlobals::get('confirm');
$confmsg=WGlobals::get('confirmmsg');
 if($elemType=='publish' || $elemType=='yesno'
 || $elemType=='lock'
 || $elemType=='approve'
 || $elemType=='core'){
 $valueTo=0;
 $class='';
 $nameTag='';
if($elemType=='publish'){
switch($value){
case'1':
$class='publish';
$nameTag=WText::t('1206732372QTKN');
break;
case'0':
$class ='unpublish';
$nameTag=WText::t('1206732372QTKO');
$valueTo=1;
break;
case'2':
$class ='pending';
$nameTag=WText::t('1206732372QTKP');
break;
case'-1':
$class ='archive';
$nameTag=WText::t('1209746189NUCP');
break;
case'-2':
$class ='disabled';
$nameTag=WText::t('1206732372QTKL');
break;
default:
if($this->value>2){
$class ='unpublish';
$nameTag=WText::t('1206732372QTKO');
$valueTo=1;
}else{
$class ='publish';
$nameTag=WText::t('1206732372QTKN');
}
break;
}
}elseif($elemType=='yesno'){
if(!empty($value)){
$class ='yes';
$nameTag=WText::t('1206732372QTKI');
}else{
$class ='cancel';
$nameTag=WText::t('1206732372QTKJ');
$valueTo=1;
}
}elseif($elemType=='core'){
if(!empty($value )){
$class ='lock';
$nameTag=WText::t('1206732412DACF');
}else{
$class ='unlock';
$nameTag=WText::t('1240888718QMAD');
$valueTo=1;
}
}elseif($elemType=='lock'){
if(empty($value )){
$class ='disabled';
$nameTag=WText::t('1401377882ETOG');
$valueTo=1;
}else{
$class ='enabled';
$nameTag=WText::t('1206732411EGRI');
}
}else{
if(isset($value) && $value>0){
$class ='lock';
$nameTag=WText::t('1310529911CSAV');
}else{
$class ='unlock';
$nameTag=WText::t('1246518570RHDZ');
$valueTo=1;
}
}
$joobiRun=JOOBI_JS_APP_NAME.'.run(\''.$namekey.'\',';
$joobiRun .="{'em':'". $em."','zval':" . $valueTo . ",'divId':'".$divId."','title':'". $nameTag."','elemType':'". $elemType."','myId':'". $eid."'";
if($conf){
$joobiRun.=",'confirm':1";
if($confmsg)$joobiRun .=",'confirmmsg':'".$confmsg."'";
}
$joobiRun .="}";
$joobiRun .=');';
if($conf)$joobiRun='if(confirm(\''.$confmsg.'\')){return '.$joobiRun.'}';
$legendO=new stdClass;
$legendO->createListingIcon=true;
$legendO->action=$class;
$lengdIcon=WPage::renderBluePrint('legend',$legendO );
ob_get_clean();
echo "mydiv=jQuery('#a'+div);";
echo "mydiv.attr('title','" .  $nameTag . " " . WText::t('1206732372QTKR'). "');";
echo 'mydiv.attr("onclick","'.$joobiRun.'");';
echo "mydivI=jQuery('#'+div);";
echo "prvC=mydivI.attr('class');";
echo "mydivI.toggleClass(prvC,false);";
echo "mydivI.toggleClass('" . $lengdIcon .  "',true);";
 }elseif($elemType=='dyninput'){
 ob_get_clean();
 echo "mydiv=jQuery('#$divId');";
 echo "mydiv.css('display','block');";
 echo "mydiv.html('$value');";
 echo "div2=jQuery('#N$divId');";
 echo "div2.css('display','none');";
 }elseif($elemType=='level'){
 switch ((int)$value){
case 25:
$color='orange';
$groupname='PLUS';
$valueTo=50;
break;
case 50:
$color='red';
$groupname='PRO';
$valueTo=0;
break;
case 0:
default:
$color='black';
$groupname='CORE';
$valueTo=25;
break;
}
$joobiRun=JOOBI_JS_APP_NAME.'.run(\''.$namekey.'\',';
$joobiRun.="{'em':'". $em."','zval':".$valueTo.",'divId':'".$divId."','elemType':'". $elemType."','myId':'". $eid."'";
$joobiRun.="}";
$joobiRun.=');';
ob_get_clean();
echo "mydiv=jQuery('#'+div);";
echo 'mydiv.attr("onclick","'.$joobiRun.'");';
echo "mydiv.css('color','".$color."');";
echo "mydiv.css('cursor','pointer');";
 echo "mydiv.html('$groupname'); ";
 }else{
 }
 exit();
 }
private function _updateParams(&$params,$object){
$myParams=array();
if(!empty($params)){
$myParamsstr=explode( "\n", $params );
foreach($myParamsstr as $myParamsstrOne){
$strpos=strpos($myParamsstrOne, '=');
$substr=substr($myParamsstrOne, 0, $strpos);
if(!empty($substr))$myParams[$substr]=substr($myParamsstrOne, $strpos+1 );
}
}
if(empty($object ))  return true;
foreach($object as $objKey=> $objVal){
$myParams[$objKey]=$objVal;
}
$finalA=array();
foreach($myParams as $psKey=>$psVal){
if(!empty($psKey))$finalA[]=$psKey.'='.$psVal;
}
$params=implode( "\n", $finalA );
return true;
}
 }