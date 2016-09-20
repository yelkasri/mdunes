<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Process_class extends WClasses {
 private $_pattern="#{ *widget:[^{}]*}#i"; private $_supportOldTags=false;
 private $_oldPattern="#{ *jtag:[^{}]*}#i";
 private static $_pregMatch=null;
 private static $_pregMatchOldTags=null;
            private $_tagExtensions=array();
private $_member=null; 
 private $_parameters=null;
 private $_CSStyle='';
 private $_replaceWithEmpty=false;
 private $_tagsG=array();
 private $_tagsU=array();
 private $_widgetParamsReturnedA=array();
private $_listTagNoAjaxA=array('alias','search');
 public function addParameter($name,$value=null){
 if(empty($this->_parameters))$this->_parameters=new stdClass;
if(empty($name)){
return;
}
if(is_array($name) || is_object($name)){
foreach($name as $paramName=> $paramValue){
$this->_parameters->$paramName=$paramValue;
}
}else{
$this->_parameters->$name=$value;
}
}
public function setParameters($params){
if(empty($this->_parameters)){
$this->_parameters=new stdClass;
$this->_parameters=$params;
}elseif(!empty($params )){
foreach($params as $key=> $val){
$this->_parameters->$key=$val;
}}}
public function replaceTags(&$object,$member=null,$number=7,$replaceWithEmpty=false){
$found=true;
$this->_replaceWithEmpty=$replaceWithEmpty;
$onlyNewTags=WPref::load('PLIBRARY_NODE_ONLYNEWTAGS');
if(!empty($onlyNewTags ))$this->_supportOldTags=false;
$this->_loadExtensions('widget:time');
while($number>0 && $found){
unset($this->_tagsU);
unset($this->_tagsG);
$found=$this->_findTags($object );
if(!empty($this->_tagsG))$this->_replaceTagsU($object, $this->_tagsG );
if(!empty($this->_tagsU)){$this->_member=(isset($member))?$member : WUser::get();
$this->_replaceTagsU($object, $this->_tagsU, true);
}
$number--;
}
}
public function getInlineCSS(){
return $this->_CSStyle;
}
private function _replaceTagsU(&$object,$listTagsA,$userTag=false){
static $serialRole=null;
if(empty($listTagsA)) return;
$tagsA=array();
$returnedParams=array();
foreach($listTagsA as $key=> $oneTagA){
$tagInfo=current($oneTagA);
if(!empty($tagInfo->remoteURL)){
$tagsA=array_merge($tagsA, $this->_processRemoteTag($oneTagA ));
continue;
}
$indexKey='widget:'.$key;
if(!$this->_loadExtensions($indexKey)) continue;
$WIDGET=$this->_tagExtensions[$indexKey];
$tagC=WAddon::get($WIDGET, null, 'tag');
if( is_object($tagC) && method_exists($tagC, 'process')){
if(!$userTag && !empty($tagC->usertag)){
$this->_tagsU[$key]=$oneTagA;
continue;
}
if($userTag)$tagC->user=$this->_member;
$tagC->params=$this->_parameters;
$cleanOneTagA=array();
$alreadyDoneA=array();
foreach($oneTagA as $cleaKey=> $cleanValue){
$nK=urldecode($cleaKey);
if(!empty($cleanValue->useajax) && !empty($cleanValue->widgetID)
&& ! in_array($cleanValue->_type, $this->_listTagNoAjaxA )){
$myAjaxID='wdgtAjaxLd'.$cleanValue->widgetID;
$url=WPages::linkAjax('controller=apps-tag&id='.$cleanValue->widgetID );
$cleanValue->wdgtContent='<div id="'.$myAjaxID.'"></div>';
$js='jQuery("#'.$myAjaxID.'").load(\''.$url.'\');';
WPage::addJSScript($js );
$wdgtO=new stdClass();
$wdgtO->widgetID=$cleanValue->widgetID;
$wdgtO->nodeID=$cleanValue->nodeName.'.node';
$wdgtO->formName=$cleanValue->formName;
$wdgtO->yid=$cleanValue->widgetSlug;
$wdgtO->widgetSlug=$cleanValue->widgetSlug;
WGlobals::setSession('renderWidget','wdgt'.$cleanValue->widgetID, $wdgtO );
$alreadyDoneA[$nK]=$cleanValue;
continue;
}
if(!empty($cleanValue->usecache) && !empty($cleanValue->widgetID)){
if(!isset($serialRole)){
$role=WUsers::roles();
$serialRole=serialize($role );
}
$key='html_'.$cleanValue->widgetID.'_'.$serialRole;
$cache=WCache::get();
$html=$cache->get($key, 'Widgets');
if(!empty($html)){
$cleanValue->wdgtContent=$html;
$alreadyDoneA[$nK]=$cleanValue;
continue;
}
}
$cleanValue->_tag=urldecode($cleanValue->_tag );
$cleanOneTagA[$nK]=$cleanValue;
}
if(!empty($cleanOneTagA)){
$resultFomProcessA=$tagC->process($cleanOneTagA );
foreach($resultFomProcessA as $rK=> $rV){
if(!empty($rV->usecache) && !empty($rV->widgetID)){
if(!isset($serialRole)){
$role=WUsers::roles();
$serialRole=serialize($role );
}
$key='html_'.$rV->widgetID.'_'.$serialRole;
$cache=WCache::get();
$cache->set($key, $rV->wdgtContent, 'Widgets');
}
}
} else $resultFomProcessA=array();
if(!empty($alreadyDoneA)){
foreach($alreadyDoneA as $k=> $v)$resultFomProcessA[$k]=$v;
}
$currentVAl=current($cleanOneTagA);
if(!empty($currentVAl) && !empty($currentVAl->widgetID )){
$this->_widgetParamsReturnedA[$currentVAl->widgetID]=$currentVAl;
}elseif(!empty($currentVAl) && !empty($currentVAl->widgetid )){
$this->_widgetParamsReturnedA[$currentVAl->widgetid]=$currentVAl;
}
if(is_array($resultFomProcessA) && !empty($resultFomProcessA))$tagsA=array_merge($tagsA, $resultFomProcessA );
if( method_exists($tagC , 'getInlineCSS')){
$this->_CSStyle .=$tagC->getInlineCSS();
}continue;
}
$TAG='widget.'.$key;
$message=WMessage::get();
$message->codeE('Tag Error: Cannot load the Tag '.$TAG , array(), 0 );
}
$tagsReplaced=array();
$replacedA=array();
if(!empty($tagsA)){
foreach($tagsA as $tag=> $replace){
$tagsReplaced[]=$tag;
$replacedA[]=$replace;
}}
$this->_replacement($tagsReplaced, $replacedA, $object );
}
public function returnParams($widgetO=null){
if(empty($widgetO) || empty($widgetO->widgetid)){
return $this->_widgetParamsReturnedA;
}elseif(!empty($this->_widgetParamsReturnedA[$widgetO->widgetid] )){
foreach($widgetO as $k=> $v)$this->_widgetParamsReturnedA[$widgetO->widgetid]->$k=$v;
return $this->_widgetParamsReturnedA[$widgetO->widgetid];
}elseif(!empty($widgetO )){
return $widgetO;
}else{
return null;
}
}
private function _findTags(&$object){
if(empty($object) || is_numeric($object)) return false;
if(is_array($object) || is_object($object)){
$result=false;
foreach($object as $objectfils){
$result=$this->_findTags($objectfils) || $result;
}return $result;
}elseif(!is_string($object)){
return false;
}
preg_match_all($this->_pattern, $object, $resultsA );
$finalTagsA=array();
if($this->_supportOldTags){
preg_match_all($this->_oldPattern, $object, $resultsOldA );
if(!empty($resultsOldA[0])){
$finalTagsA=$resultsOldA[0];
}}
if(empty($resultsA[0] ) && empty($finalTagsA)){
return false;
}
$finalTagsA=array_merge($finalTagsA, $resultsA[0] );
foreach($finalTagsA as $TAG){
if(!$this->_organizeTags($TAG )){
$message=WMessage::get();
$message->userW('1212843293BKVE',array('$TAG'=>$TAG));
}}
return true;
}
private function _loadExtensions($wantedTag){
if(empty($wantedTag) || 'widget:'==$wantedTag ) return false;
if(empty($this->_tagExtensions)){
$caching=WPref::load('PLIBRARY_NODE_CACHING');
if($caching > 0){
$cache=WCache::get();
$this->_tagExtensions=$cache->get('loadedx3z7xTags','Widgets');
}
}
if(isset($this->_tagExtensions[$wantedTag] )) return true;
else {
$defaultTags=array('widget:alias'=>'main.alias','widget:param'=>'output.param','widget:time'=>'main.time','widget:area'=>'output.area',
 'widget:name'=>'users.user','widget:user'=>'users.user','widget:username'=>'users.user','widget:firstname'=>'users.user','widget:lastname'=>'users.user','widget:email'=>'users.user',
'widget:site'=>'output.define','widget:sitename'=>'output.define','widget:siteurl'=>'output.define');
$appsM=WModel::get('install.widgetype','object');
$appsM->whereE('publish', 1 );
$appsM->checkAccess();
$loadedTag=$appsM->load('lra','namekey');
if(!empty($loadedTag)){
foreach($loadedTag as $oneTag){
$explodeTagA=explode('.',$oneTag );
$tagName='widget:'.$explodeTagA[1];
if(!isset($defaultTags[$tagName] ))$defaultTags[$tagName]=$oneTag;
}}else{
$loadedTag=array();
}
$this->_tagExtensions=$defaultTags;
$caching=WPref::load('PLIBRARY_NODE_CACHING');
if($caching > 0){
if(empty($cache))$cache=WCache::get();
$cache->set('loadedx3z7xTags',$this->_tagExtensions, 'Widgets');
}
}
if(isset($this->_tagExtensions[$wantedTag])) return true;
$message=WMessage::get();
$message->codeE('No tag is linked to the type '.$wantedTag, array(), 0 );
return false;
}
private function _replacement($tagsReplaced,$replaced,&$object){
if( is_numeric($object)) return $object;
if( is_string($object)){
if(!$this->_replaceWithEmpty)$object=$this->_stringReplace($tagsReplaced, $replaced, $object );
else  $object=str_replace($tagsReplaced, '',$object );
return $object; }
if(is_array($object) || is_object($object)){
foreach($object as $key=> $suite)$object[$key]=$this->_replacement($tagsReplaced, $replaced, $suite );
}
}
private function _stringReplace($tagsReplaced,$replaced,$object){
if(empty($tagsReplaced)) return $object;
$mainString=$object;
foreach($tagsReplaced as $key=> $replaceMe){
$endOfSeach=false;
$newString='';
$start=0;
$taglen=strlen($replaceMe );
do {
$position=strpos($mainString, $replaceMe, $start );
if($position !==false){
$newString .=substr($mainString, $start, $position-$start );
$newString .=$replaced[$key]->wdgtContent;
$start=$position + $taglen;
}else{
$newString .=substr($mainString, $start );
$endOfSeach=true;
}
} while( !$endOfSeach );
$mainString=$newString;
}
return $mainString;
}
private function _organizeTags($tag){
$mytag=new stdClass;
$mytag->_tag=$tag;
$tag=html_entity_decode( trim($tag,"{..}"));
if( substr($tag, 0, 7 )=='widget:'){
preg_match('#:[^|]*#', trim($tag), $result );
if(empty($result[0])) return false;
$type=strtolower( trim($result[0],":"));
$mytag->_type=$type;
if(!isset(self::$_pregMatch)) self::$_pregMatch=base64_decode('IyAqW158XSsgKj0gKigiKD86KD8hIikuKSoifFtefF0rKSM=');
preg_match_all( self::$_pregMatch, $tag, $result2 );
}elseif($this->_supportOldTags){
preg_match('#:[^ ]*#', trim($tag), $result );
WMessage::log($tag, 'refactor-old-jtag');
if(empty($result[0])) return false;
$type=strtolower( trim($result[0],":"));
$mytag->_type=$type;
if(!isset(self::$_pregMatchOldTags)) self::$_pregMatchOldTags=base64_decode('IyAqW14gXSsgKj0gKigiKD86KD8hIikuKSoifFteIF0rKSM=');
preg_match_all( self::$_pregMatchOldTags, $tag, $result2 );
}
if(empty($result2[0])){
$this->_tagsG[$type][$mytag->_tag]=$mytag;
return true;
}
foreach($result2[0] as $parameter){
$valeurs=explode( "=", trim($parameter));
if( count($valeurs) !=2){
continue;
}
$propertyName=WGlobals::stringFilter($valeurs[0], '','noencoding');
$value=WGlobals::stringFilter( trim($valeurs[1],'"'), '','noencoding');
$mytag->$propertyName=$value;
}
$this->_tagsG[$type][$mytag->_tag]=$mytag;
return true;
}
private function _processRemoteTag($givenTagsA){
$replacedTagsA=array();
foreach($givenTagsA as $tag=> $myTagO){
$tagString='{widget:'.$myTagO->_type;
foreach($myTagO as $pKey=> $pValue){
if( in_array($pKey, array('_tag','_type','remoteURL')) ) continue;
$tagString .=' '.$pKey.'='.$pValue;
}$tagString .=' }';
$URL=trim($myTagO->remoteURL, '/'). '/'.JOOBI_INDEX.'?'.JOOBI_URLAPP_PAGE.'='.WApplication::getAppLink( JOOBI_MAIN_APP ). '&controller=apps-tag&isPopUp=true&id='.base64_encode($tagString );
$schedulerClass=WClass::get('scheduler.triggerurl');
$result=$schedulerClass->launchAndGetResult($URL, 30, true);
if(!is_string($result))$result='';
$replacedTagsA[$tag]=$result;
}
return $replacedTagsA;
}
 }