<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Output_Doc_htmllistingsdiv {
public function renderContentDiv($obj){
if(!empty($obj->htmlObj->pagination) || !empty($obj->htmlObj->dropdown)) WPage::addJSLibrary('rootscript');
$myFilters='';
$obj->tableDetailsO->transform->createHead($obj->htmlObj );
$myPagination=$obj->htmlObj->pagiHTML;
$myLegend='';
if(true){
$legendHTML=WPage::renderBluePrint('legend','createLegend');
if(!empty($legendHTML )){
$obj=new stdClass;
$obj->class='legend';
$filterDIV=new WDiv($legendHTML );
$filterDIV->classes='legend';
$myLegend=$filterDIV->make();
}
}
$mainDIV=new WDiv($this->_createDivBody($obj ));
if(!empty($obj->htmlObj->controller)){
$mainDIV->id=$obj->htmlObj->controller . $obj->htmlObj->yid;
}else{
$mainDIV->id='random'.rand( 100000, 999999 );
}
$mainDIV->classes=isset($obj->htmlObj->classes)?trim($obj->htmlObj->classes ) : 'jmain';
if(isset($obj->htmlObj->style))$mainDIV->style=trim($obj->htmlObj->style );
$myMainContent=$mainDIV->make();
$html=$myFilters . $myMainContent;
if(isset($obj->htmlObj->formObj )){
if(isset($obj->htmlObj->formObj->name)){
if(isset($obj->htmlObj->_pkey))$obj->htmlObj->formObj->hidden( JOOBI_VAR_DATA.'[s][pkey]',$obj->htmlObj->_pkey );
 $obj->htmlObj->formObj->hidden( JOOBI_VAR_DATA.'[s][mid]',$obj->htmlObj->sid );
if(isset($obj->htmlObj->currentOrder)){
$currentOrder=$obj->htmlObj->currentOrder .'|'.$obj->htmlObj->currentOrderDir;
$obj->htmlObj->formObj->hidden('sorting' , $currentOrder );
}
}else{
$form=WView::form($obj->htmlObj->formName );
if(isset($obj->htmlObj->currentOrder)){
$currentOrder=$obj->htmlObj->currentOrder .'|'.$obj->htmlObj->currentOrderDir;
$form->hidden('sorting' , $currentOrder );
}
}
}
return $html;
}
private function _createDivHead($t){
$message=WMessage::get();
$message->codeE('This is not used any more and does not work properly if this code is called please report this to development');
if(isset($obj->htmlObj->rhclss))$classes=$obj->htmlObj->rhclss;
if(isset($obj->htmlObj->rhsty))$stylish=$obj->htmlObj->rhsty;
$myline='';
if($obj->htmlObj->_countElms!=0)$ratio=100/$obj->htmlObj->_countElms;
foreach($t as $listing){
switch($listing->typeName){
case 'rownumber':
case'yesno':
case'publish':
case'radio':
case'level':
case'checkbox':
if(!isset($listing->width))$listing->width=$ratio/3;
case'order':
if(!isset($listing->width))$listing->width=$ratio/2;
case'access':
case'butedit':
case'butdelete':
case'butcopy':
if(!isset($listing->align))$listing->align='center';
break;
default:
continue;
}
if(!empty($listing->ovly)) continue;
if($listing->name=='ID' || $listing->map=='level'){
if(!isset($listing->width))$listing->width=$ratio/4;
if(!isset($listing->align))$listing->align='center';
}
$listingType=$listing->typeName;
if(!empty($listing->name)){
$header=$listing->name;
}else{
$header='&nbsp;';
}
$mapsid=$listing->map.'_'.$listing->sid;
$ordering=true;
if($mapsid[0]=='_' || (isset($listing->nosort) && $listing->nosort)){
$ordering=false;
}
if($listing->parent==0){
switch ($listingType){
case 'text':
case 'type':
$listing->width=$ratio*3/2;
$header=$obj->_ordering($header, $mapsid ,$ordering, $listing->description) ;
break;
case 'input':
break;
case 'rownumber':
$header='#';
break;
case 'checkbox':
(string)$limitstart=($obj->htmlObj->limitStart=='')?'0' : $obj->htmlObj->limitStart;
$joobiRun=WPage::actionJavaScript('toggleAllBox',$obj->htmlObj->formName, array('nosubmit'=>true,'limitstart'=>$limitstart),'this.checked');
$header='<input type="checkbox" id="check'.$listing->map.'" name="allboxes" value="" onclick="'.$joobiRun.'" />';
break;
case 'radio':
$header='';
break;
case 'order':
if($listing->map=='ordering'){
$obj->htmlObj->colNb++;
$cell=$obj->_ordering($header, $mapsid ,$ordering, $listing->description);
$t->cell($cell , true);
$num=$obj->htmlObj->totalElements-1;
if($this->htmlObj->currentOrder==$mapsid){
$joobiRun=WPage::actionJavaScript('order',$this->htmlObj->formName, array('zact'=>'all','zsid'=> $listing->sid, 'lstg'=>true, 'total'=>$num ));
}else{
$joobiRun=WPage::actionJavaScript($this->htmlObj->_defaultTask, $this->htmlObj->formName, array('lstg'=>true,'sorting'=>true), $mapsid.'|ASC', false);
}
$header='<a href="#" onclick="'.$joobiRun.'" alt="ordering" title="'.WText::t('1206732363AOLD').'"><span class="jpng-16-save" alt="'.WText::t('1206732363AOLD').'"></span></a>';
WPage::addCSSScript('.jpng-16-save{'. WView::getPngCss(JOOBI_URL_JOOBI_IMAGES. 'toolbar/16/save.png').'width:16px;height:16px;display:block;}');
$t->th_w='1%';
}else{
unset($header );
}
break;
case'translation':
break;
default:
$header=$obj->_ordering($header, $mapsid,$ordering, $listing->description );
break;
}
if(isset($header)){
if('edit'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
$outputDirectEditC=WClass::get('output.directedit');
$header=$outputDirectEditC->editView('listing',$obj->htmlObj->yid, $listing->lid, 'listing'). $header;
}elseif('translate'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
$outputDirectEditC=WClass::get('output.directedit');
$header=$outputDirectEditC->translateView('listing',$obj->htmlObj->yid, $listing->lid, $listing->name ). $header;
}
if(empty($header))$header='&nbsp;';
$cellDIV=new WDiv($header );
$cellDIV->align=(isset($listing->align))?$listing->align : null;
$cellDIV->height=(isset($listing->height))?$listing->height : null;
$cellDIV->width=(isset($listing->width))?$listing->width : null;
if(isset($listing->classes))$cellDIV->classes=trim($listing->classes );
if(isset($listing->style))$cellDIV->style=trim($listing->style );
$cellDIV->float='left';
$myline .=$cellDIV->make(). $obj->htmlObj->crlf;
}
}
}
$myDIV=new WDiv($myline );
$myDIV->classes=(isset($obj->htmlObj->rclss))?trim($obj->htmlObj->rclss ) : 'jhead';
if(isset($obj->htmlObj->rsty))$myDIV->style=trim($obj->htmlObj->rsty );
$myhead=$myDIV->make().'<br/><br/><br/>';
return $myhead;
}
private function _createDivBody($obj){
$myListing='';
$divheadFinal='';
if(!empty($obj->htmlObj->_data)){
$obj->rowNumber=0;
if(!empty($obj->htmlObj->myParentA)){
$parent=$obj->htmlObj->myParentA;
}else{
$obj->allDataRowsA=&$obj->htmlObj->_data;
$obj->numberRows=$obj->htmlObj->totalElements;
}
if($obj->htmlObj->parentB){
$elements=array();
foreach($obj->htmlObj->elements as $value){
$pt=$value->parent;
$list=(isset($elements[$pt])?$elements[$pt] : array());
array_push($list, $value );
$elements[$pt]=$list;
}
reset($elements);
if( key($elements)==0)$elements=array_reverse($elements, true);
}else{
$elements[0]=$obj->htmlObj->elements;
}
$obj->rowOffset=$obj->htmlObj->pageNavO->limitstart;
$pKey=$obj->htmlObj->pKeyMap;
$obj->htmlObj->_countElms=0;
$obj->htmlObj->_divHeaders=array();
$paramsMapName=$obj->paramsMapName;
foreach($obj->allDataRowsA as $row){
if(!empty($row->$paramsMapName)){
WTools::getParams($row, $paramsMapName );
}
$tempContent=array();
$mySingleDiv='';
foreach($elements as $elemtKey=>$elemtVal){
$newold=false;
if($obj->htmlObj->_countElms==0 && $newold){
foreach($elemtVal as $key=>$listing){
if($listing->typeName !='hidden' && $listing->parent==0){
$obj->htmlObj->_divHeaders[$key]=$listing;
$obj->htmlObj->_countElms++;
}
}
$divheadFinal=$obj->htmlObj->_createDivHead($obj->htmlObj->_divHeaders);
}
$linkFilters='';
if(isset($obj->htmlObj->_defaultPickList )){
foreach($obj->htmlObj->_defaultPickList as $pickKey=> $pickVal){
if(!empty($pickVal))$linkFilters .='&'.$pickKey.'='.$pickVal;
}
}
$myline='';
foreach($elemtVal as $key=> $listing){
if($listing->typeName=='hidden') continue;
$map=$listing->map;
$sid=$listing->sid;
if(!empty($tempContent[$listing->lid]->content)){
$htmlData=$tempContent[$listing->lid]->content;
unset($tempContent[$listing->lid]->content);
}else{
if( substr($map, 0, 2)=='p['){
$dataMap='params_'.$sid;
$myParams=$row->$dataMap;
$arrayP=new stdClass;
$arrayP->params=$myParams;
WTools::getParams($arrayP );
$Temp_map=substr($map, 2, strlen($map)-3);
if(isset($arrayP->$Temp_map))$obj->cellValue=$arrayP->$Temp_map;
else $obj->cellValue=null;
}else{
$dataMap=$map.'_'.$sid;
$obj->cellValue=(isset($row->$dataMap))?$row->$dataMap : '';
}
$obj->complexMap=$dataMap;
$htmlData=$obj->callListingElement($listing, $row );
}
if(!empty($listing->lienrolid)){
if(!isset($roleHelper))$roleHelper=WRole::get();
$viewLinkNow=$roleHelper->hasRole($listing->lienrolid );
}else{
$viewLinkNow=true;
}
if($viewLinkNow && !empty($htmlData) && !empty($listing->lien) && !(isset($obj->htmlObj->_params->_extras['unlinked'])
 && is_array($obj->htmlObj->_params->_extras['unlinked']) && in_array($listing->map, $obj->htmlObj->_params->_extras['unlinked']))){
$outputLinkC=WClass::get('output.link');
if(!empty($listing->popuplink) || WGlobals::get('is_popup', false, 'global')){
$outputLinkC->setIndex('popup');
}
$outputLinkC->wid=$obj->htmlObj->wid;
if(empty($listing->dontConvertLien))$link=$outputLinkC->convertLink($listing->lien, $row, $linkFilters, $obj->htmlObj->_model, $obj->htmlObj->mapListA );
else $link=$listing->lien;
if(!empty($tempContent[$listing->lid]->overlay )){
$toolTipsO=WPage::newBluePrint('tooltips');
$toolTipsO->tooltips=$tempContent[$listing->lid]->overlay;
$toolTipsO->title=$htmlData;
$toolTipsO->text=$htmlData;
$toolTipsO->id=$listing->lid;
$htmlData=WPage::renderBluePrint('tooltips',$toolTipsO );
unset($tempContent[$listing->lid]->overlay );
}
$linkClass='';
$poph='70%';
$popw='75%';
if(empty($listing->popuplink)  || WGlobals::get('is_popup', false, 'global')){
$linkClass='list-link';
$standardLink=true;
}else{
 if(!empty($listing->poph))$poph=$listing->poph;
 if(!empty($listing->popw))$popw=$listing->popw;
$standardLink=false;
}
$linkExtras='';
if(isset($listing->lienValidation)){
$linkExtras=$listing->lienValidation;
}
if(!empty($listing->target)){
$linkExtras .=' target="'. $listing->target .'"';
}
$htmlData=WPage::createPopUpLink($link, $htmlData, $popw, $poph, $linkClass, '','',$standardLink, $linkExtras );
}elseif(!empty($tempContent[$listing->lid]->overlay )){
$toolTipsO=WPage::newBluePrint('tooltips');
$toolTipsO->tooltips=$tempContent[$listing->lid]->overlay;
$toolTipsO->title=$htmlData;
$toolTipsO->text=$htmlData;
$toolTipsO->id=$listing->lid;
$htmlData=WPage::renderBluePrint('tooltips',$toolTipsO );
}
if($listing->parent==0){
if(empty($htmlData))$htmlData='&nbsp;';
if(isset($listing->anchor )){
$complextAnchorMap=$obj->htmlObj->mapListA[$listing->anchor];
if(isset($row->$complextAnchorMap)){
$anchorValue=$row->$complextAnchorMap;
if(!is_int($anchorValue))$anchorValue=preg_replace('#[^a-z0-9]#i','-',$anchorValue);
$htmlData='<a class="anchor" name="'. $anchorValue.'">'.$htmlData;
}
}
$cellDIV=new WDiv($htmlData );
if($newold){
$pkey=$obj->htmlObj->_pkey.'_'.$obj->htmlObj->sid;
$rowid=@$obj->htmlObj->_data[$obj->rowNumber]->$pkey;
$cellDIV->id='jcell_'.$obj->htmlObj->yid.'_'.$obj->htmlObj->_divHeaders[$key]->name.'_'.$rowid;
$cellDIV->align=(isset($obj->htmlObj->_divHeaders[$key]->align))?$obj->htmlObj->_divHeaders[$key]->align : null;
$cellDIV->height=(isset($obj->htmlObj->_divHeaders[$key]->height))?$obj->htmlObj->_divHeaders[$key]->height : null;
$cellDIV->width=(isset($obj->htmlObj->_divHeaders[$key]->width))?$obj->htmlObj->_divHeaders[$key]->width : null;
$cellDIV->float='left';
}
if(isset($listing->classes))$cellDIV->classes=trim($listing->classes );
if(isset($listing->style))$cellDIV->style=trim($listing->style );
$myline .=$cellDIV->make(). $obj->htmlObj->crlf;
}else{
if(!empty($listing->ovly)){
if(!isset($tempContent[$listing->parent]))$tempContent[$listing->parent]->overlay='';
$tempContent[$listing->parent]->overlay .=$htmlData;
}else{
if(isset($listing->addt) && $listing->addt)$htmlData=$listing->name.$htmlData;
if(!empty($listing->spcspace))$htmlData=$htmlData.'&nbsp;';
elseif(!empty($listing->spc))$htmlData=$htmlData . $listing->spc ;
if(!isset($tempContent[$listing->parent]->content)){
if(!isset($tempContent[$listing->parent]))$tempContent[$listing->parent]=new stdClass;
$tempContent[$listing->parent]->content='';
}
$cellDIV=new WDiv($htmlData );
if(isset($listing->classes))$cellDIV->classes=trim($listing->classes );
if(isset($listing->style))$cellDIV->style=trim($listing->style );
$tempContent[$listing->parent]->content .= $cellDIV->make();
}
}
}
$mySingleDiv .=$myline;
}
$obj->rowNumber++;
$myDIV=new WDiv($mySingleDiv );
$myDIV->classes=(isset($obj->htmlObj->rclss))?trim($obj->htmlObj->rclss ) : 'jrow clearfix';
if(isset($obj->htmlObj->rsty))$myDIV->style=trim($obj->htmlObj->rsty );
$newold=$newold ?'<br/><br/>':'';
$myListing .=$myDIV->make().$newold;
}
}
return $divheadFinal.$myListing;
}
}
