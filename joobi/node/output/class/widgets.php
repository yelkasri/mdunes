<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Widgets_class extends WClasses {
private static $_myWidgetA=array();
public function preLoadWidgetsForView($yid){
if(empty($yid)) return false;
$allWidgetsA=$this->_loadWidgetsFromYID($yid );
if(empty($allWidgetsA )){
$installWidgetsC=WClass::get('install.widgets');
$installStatus=$installWidgetsC->installViewWidgets($yid );
if($installStatus===false) return false;
$allWidgetsA=$this->_loadWidgetsFromYID($yid );
if(empty($allWidgetsA)) return false;
}
foreach($allWidgetsA as $oneWidget){
self::$_myWidgetA[$yid][$oneWidget->widgetid]=$oneWidget;
}
return true;
}
public function loadWidgetsFromNamekey($namekey){
if(empty($namekey)) return null;
$mainWIdgetsM=WModel::get('main.widget');
$mainWIdgetsM->rememberQuery(true, 'Widgets');
$mainWIdgetsM->makeLJ('main.widgettrans','widgetid');
$mainWIdgetsM->whereLanguage();
$mainWIdgetsM->makeLJ('main.widgettype','wgtypeid','wgtypeid', 0, 2 );
if( is_numeric($namekey)){
$mainWIdgetsM->whereE('widgetid',$namekey );
}elseif( is_string($namekey)){
$mainWIdgetsM->whereE('namekey',$namekey );
}else{
$mainWIdgetsM->openBracket();
$mainWIdgetsM->whereIn('namekey',$namekey );
$mainWIdgetsM->operator('OR');
$mainWIdgetsM->whereIn('widgetid',$namekey );
$mainWIdgetsM->closeBracket();
}
$mainWIdgetsM->whereE('publish', 1 );
$mainWIdgetsM->checkAccess();
$mainWIdgetsM->select('name', 1 );
$mainWIdgetsM->select('namekey', 2 );
$mainWIdgetsM->select('namekey', 0, 'namekeyWidget');
$allWidgetsA=$mainWIdgetsM->load('ol',array('widgetid','params'));
return $allWidgetsA;
}
public function createWidgetString(&$oneWidget,$overWritePArams=null){
if(empty($oneWidget->namekey)) return '';
$tag='{widget:';
$namekeyA=explode('.',$oneWidget->namekey );
$tag .=$namekeyA[1];
$newObj=new stdClass();
if(!empty($oneWidget->params)){
$newObj->params=$oneWidget->params;
WTools::getParams($newObj );
WTools::getParams($oneWidget );
}
if(!empty($oneWidget)){
foreach($oneWidget as $oneKey=> $oneVal){
$tag .='|'.$oneKey.'='.$oneVal;
}}
if(!empty($oneWidget->pagination) && !empty($oneWidget->yid)){
if(empty($newObj->nb))$newObj->nb=10;elseif($newObj->nb > 500)$newObj->nb=500;
}
if(!empty($overWritePArams)){
foreach($overWritePArams as $oneOverK=> $oneOverV){
$newObj->$oneOverK=$oneOverV;
}}
if(!empty($newObj)){
foreach($newObj as $oneKey=> $oneVal){
$tag .='|'.$oneKey.'='.$oneVal;
}}
$tag .='}';
return $tag;
}
public function renderWidget($widgetID,$nodeID,$formName,$yid,$formNamekey,$fid=0,$useAjax=true){
static $i=0;
$widgetO=$this->_loadWidgetsForView($widgetID, $yid );
if(empty($widgetO)) return '';
WTools::getParams($widgetO );
$name=$widgetO->name;
$html='';
$i++;
$widgetParams=WGlobals::get('pageWidgetParams', null, 'global');
if(empty($widgetParams ))$widgetParams=new stdClass;
$widgetParams->widgetID=$widgetO->widgetid;
if(!empty($fid)){
$widgetParams->formElementID=$fid;
$i=$fid;
}
$widgetParams->themeType='node';
$widgetParams->nodeName=WExtension::get($nodeID, 'folder');
if(isset($widgetO->usecache))$widgetParams->usecache=$widgetO->usecache;
if(isset($widgetO->useajax)){
if(!$useAjax ) unset($widgetO->useajax );
else $widgetParams->useajax=$widgetO->useajax;
}
if( WPref::load('PMAIN_NODE_DIRECT_EDIT_WIDGETS')){$directEditClass=WClass::get('output.directedit');
$html=$directEditClass->editWidget($widgetO->widgetid, $name );
}
$widgetParams->widgetSlug=str_replace('.','_',$formNamekey );
$widgetParams->widgetSlugID=WView::generateID('widget',$widgetID.'_'.$i );
$widgetSlugID=$widgetID;
$uniqueId='';
$controller=WGlobals::get('controller','', null, 'task');
$catalogAdvFilterC=WClass::get($controller.'.advfilter', null, 'class', false);
if(!empty($catalogAdvFilterC)){
$uniqueId=$catalogAdvFilterC->getUniqueString();
}if(!empty($uniqueId )){
$widgetSlugID .=$uniqueId;
}else{
$uniqueId=WGlobals::get($controller.'extraID','','global');
if(!empty($uniqueId)){
$widgetSlugID .='_'.$uniqueId;
}else{
$widgetSlugID .='_'.$i;
}
}
$widgetParams->widgetSlugIndex=WView::generateID('widget',$widgetSlugID );
$widgetParams->formName=$formName;
$cloneWdiget=clone $widgetO;
WTools::getParams($cloneWdiget );
if(!empty($cloneWdiget->sortingpresentation) && 'tab'==$cloneWdiget->sortingpresentation
&& !empty($cloneWdiget->availsort) && strpos($cloneWdiget->availsort, ',') !==false){
$orignalWidget=clone $widgetO;
$availableItemsA=WTools::preference2Array($cloneWdiget->availsort );
$data=new stdClass;
$data->frame='tab';
$params=new stdClass;
$params->id=$widgetParams->widgetSlugID;
$params->idText=$params->id;
$data->params=$params;
$this->_paneTab=WPage::renderBluePrint('frame',$data );
$this->_paneTab->startPane($params );
$itemsPagi=null;
if(!empty($cloneWdiget->pagination) && !empty($allWidgetParamsO->totalCount)){
$tagString=$this->createWidgetString($widgetO, $widgetParams );
$outputProcessC=WClass::get('output.process');
$outputProcessC->replaceTags($tagString );
$allWidgetParamsO=$outputProcessC->returnParams($widgetO );
$uniqueNameState=$widgetParams->widgetSlugIndex;
$startLimit=WGlobals::getUserState("wiev-$uniqueNameState-limitstart", 'limitstart'.$uniqueNameState, 0, 'int');
$pagiI=WView::pagination($uniqueNameState, $allWidgetParamsO->totalCount, $startLimit, $allWidgetParamsO->nb, 0, 'Items','home');
$pagiI->setFormName($formName );
$pagiI->setWidget($allWidgetParamsO->widgetID, $fid );
$itemsPagi=$pagiI->getListFooter();
}
$originalSorting=$cloneWdiget->sorting;
$sortString='sorting='.$cloneWdiget->sorting;
foreach($availableItemsA as $oneTabSorting){
$sorting=trim($oneTabSorting);
WGlobals::set('choicesorting',$sorting );
$widgetBisO=clone $orignalWidget;
if(!empty($widgetBisO->params))$widgetBisO->params=str_replace($sortString, 'sorting='.$oneTabSorting, $widgetBisO->params );
$tagString=$this->createWidgetString($widgetBisO, $widgetParams );
$outputProcessC=WClass::get('output.process');
$outputProcessC->replaceTags($tagString );
$this->_addTab($sorting, $tagString, $itemsPagi, $params->id );
}
$html .=$this->_paneTab->endPane($params );
if(!empty($allWidgetParamsO->wrapper) && 'pane'==$allWidgetParamsO->wrapper){
$data=WPage::newBluePrint('widgetbox');
$data->content=$html;
if(!empty($allWidgetParamsO->showtitle))$data->title=$name;
$html=WPage::renderBluePrint('widgetbox',$data );
}
return $html;
}
$tagString=$html . $this->createWidgetString($widgetO, $widgetParams );
$outputProcessC=WClass::get('output.process');
$outputProcessC->replaceTags($tagString );
if(!empty($widgetO->useajax )){
return $tagString;
}
$allWidgetParamsO=$outputProcessC->returnParams($widgetO );
if(!empty($allWidgetParamsO->wrapper)){
switch($allWidgetParamsO->wrapper){
case 'pane':
case 'collapse':
$data=WPage::newBluePrint('widgetbox');
$data->content=$tagString;
if(!empty($allWidgetParamsO->showtitle))$data->title=$name;
if(!empty($allWidgetParamsO->extraHeader))$data->headerRightA[]=$allWidgetParamsO->extraHeader;
if(!empty($allWidgetParamsO->pagination)){
$uniqueNameState=$widgetParams->widgetSlugIndex;
$startLimit=WGlobals::getUserState( "wiev-$uniqueNameState-limitstart", 'limitstart'.$uniqueNameState, 0, 'int');
$pagiI=WView::pagination($uniqueNameState, $allWidgetParamsO->totalCount, $startLimit, $allWidgetParamsO->nb, 0, 'Items','home');
$pagiI->setFormName($formName );
$pagiI->setWidget($allWidgetParamsO->widgetID, $fid );
$itemsPagi=$pagiI->getListFooter();
if(!empty($itemsPagi)){
$data->headerCenterA[]=$itemsPagi;
$data->bottomCenterA[]=$itemsPagi;
}
}
$tagString=WPage::renderBluePrint('widgetbox',$data );
break;
default:
break;
}
}
return $tagString;
}
private function _loadWidgetsForView($widgetid,$yid=0){
if(empty($widgetid)) return false;
if(!empty( self::$_myWidgetA[$yid][$widgetid] )) return self::$_myWidgetA[$yid][$widgetid];
else {
$widgetO=WModel::getElementData('main.widget',$widgetid, array('widgetid','wgtypeid','params','rolid','publish'));
if(!empty($widgetO) && !empty($widgetO->publish) && WRoles::hasRole($widgetO->rolid )){
$widgetO->namekey=WModel::getElementData('main.widgettype',$widgetO->wgtypeid, 'namekey');
$widgetO->name=WModel::getElementData('main.widgettype',$widgetO->wgtypeid, 'name');
return self::$_myWidgetA[$yid][$widgetid]=$widgetO;
} else return null;
}
}
private function _addTab($sorting,$content,$pagination,$id){
$params=new stdClass;
$params->id=$id.'-'.$sorting;
$params->idText=$params->id;
$params->text=$this->_getSortingEquivalentName($sorting );
$this->_paneTab->startPage($params );
$this->_paneTab->content .='<div class="nagiPadding clearfix">';
$this->_paneTab->content .=$pagination;
$this->_paneTab->content .='</div>';
$this->_paneTab->content .=$content;
return $this->_paneTab->endPage($params );
}
private function _getSortingEquivalentName($sorting){
switch ($sorting){
case 'featured':
$text=WText::t('1256629159GBCH');
break;
case 'sold':
$text=WText::t('1304527165QGOS');
break;
case 'rated':
$text=WText::t('1257243215EFTI');
break;
case 'hits':
$text=WText::t('1242282415NZTN');
break;
case 'reviews':
$text=WText::t('1257243215EFTU');
break;
case 'highprice':
$text=WText::t('1305198010FCNE');
break;
case 'lowprice':
$text=WText::t('1305198010FCNF');
break;
case 'newest':
$text=WText::t('1304918557EIYL');
break;
case 'oldest':
$text=WText::t('1307606755CNOQ');
break;
case 'alphabetic':
$text=WText::t('1219769904NDIK');
break;
case 'reversealphabetic':
$text=WText::t('1307606756SRYP');
break;
case 'endingsoon':
$text=WText::t('1412376020TDFY');
break;
case 'recentlyviewed':
$text=WText::t('1420549772RZVB');
break;
case 'mytopviewed':
$text=WText::t('1420549772RZVC');
break;
case 'recentlyupdated':
$text=WText::t('1307606756SRYQ');
break;
case 'availabledate':
$text=WText::t('1415146133GKRN');
break;
case 'random':
$text=WText::t('1241592274CBNQ');
break;
case 'onsale':
$text=WText::t('1320230249NPSI');
break;
case 'justsold':
$text=WText::t('1308888986AJEG');
break;
default:
$message=WMessage::get();
$message->codeE('The following sorting is not defined: '.$sorting );
break;
}
return $text;
}
private function _loadWidgetsFromYID($yid){
$caching=WPref::load('PLIBRARY_NODE_CACHING');
$caching=($caching > 5 )?'cache' : 'static';
 $getModel=true;
if($getModel){
$mainWIdgetsM=WModel::get('main.widget');
$mainWIdgetsM->remember('Zx-widgets-'. $yid, true, 'Widgets');
$mainWIdgetsM->makeLJ('main.widgettrans','widgetid');
$mainWIdgetsM->whereLanguage();
$mainWIdgetsM->makeLJ('main.widgettype','wgtypeid','wgtypeid', 0, 2 );
$mainWIdgetsM->whereE('framework_type', 5 );
$mainWIdgetsM->whereE('framework_id',$yid );
$mainWIdgetsM->whereE('publish', 1 );
$mainWIdgetsM->select('name', 1 );
$mainWIdgetsM->select('namekey', 2 );
$mainWIdgetsM->checkAccess();
$allWidgetsA=$mainWIdgetsM->load('ol',array('widgetid','params'));
}
return $allWidgetsA;
}
 }