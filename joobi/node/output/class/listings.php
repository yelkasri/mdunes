<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Listings_class extends WView {
public $name='';public $filtersHTML=null;public $colNb=0; public $pKeyMap=null;public $multiplePK=false;
public $champsA=array();public $pageNavO=null;public $picklistDefaultA=null;public $manualDataB=false;
public $limitStart=0;
public $limitMax=0;
public $totalItems=0;public $searchWord='';
public $searchOnB=false;
private $_totalCount=0;
public $headNumberFooter=''; public $headListFooter=''; public $headSeachFooter=''; public $pagiHTML='';
public $pagiIncrement=null;
public $myParentA=array();public $parentB=false;public $totalElements=null;public $mapListA=array();
public $currentOrder=null;public $currentOrderDir=null;
public $orderingMap=null;public $orderingByGroup=null;private $_orderingExist=false;
public $queryInfoWhere=null; public $sid=0;
public $wid=0;
public $search=false;public $searchedColumnA=array();
public $advSearch=false;public $advSeachableA=array();
public $pagination=false;
public $dropdown=false;
function create(){
WText::t('1414741455RKJF');
WText::t('1216230542PQQV');
if(isset($this->report) && $this->report){
$trk=WGlobals::get( JOOBI_VAR_DATA );
if(!empty($trk['x']['jdoctype']))$documentTypeInURL=$trk['x']['jdoctype'];
if(empty($documentTypeInURL))$documentTypeInURL=WGlobals::get('jdoctype');
if(empty($documentTypeInURL))$documentTypeInURL=WGlobals::get('nested_jdoctype');if(empty($documentTypeInURL))$documentTypeInURL=WGlobals::getSession('graphFilters','jdoctype');
$this->subtype=$documentTypeInURL;
}
WLoadFile('document', JOOBI_LIB_HTML.'doc'. DS );
switch($this->subtype){
case '230':$listingI=$this->_createDocFileInstance('rss');
break;
case '240':$listingI=$this->_createDocFileInstance('csv');
break;
case '250':
$listingI=$this->_createDocFileInstance('customized');
break;
case '220':case '270':
if(!in_array( JOOBI_APP_DEVICE_TYPE , array('bw')) ) return false;
$listingI=$this->_createDocFileInstance('googlegraph');
break;
case '210':
case'200':
default:
$listingI=$this->_createDocFileInstance('htmllistings');
break;
}
$this->formName=(!empty($this->formObj->name))?$this->formObj->name : $this->firstFormName;
$view=$this->type;
$this->_context=$this->yid;
$currentOrder=WGlobals::getUserState( "wiev-$this->yid-fltr_rdr", 'sorting','','string');$currentOrderArr=explode('|',$currentOrder );
$this->currentOrder=$currentOrderArr[0];
$this->currentOrderDir=(isset($currentOrderArr[1]))?$currentOrderArr[1] : 'DESC';
 $maxLimit=(!empty($this->maxitem))?$this->maxitem : JOOBI_LIST_LIMIT;
$this->limitMax=WGlobals::getUserState("wiev-$this->yid-limit", 'limit'.$this->yid, $maxLimit, 'int');
if(!empty($this->report))$this->limitMax=2000;
$pageNumber=WGlobals::get('pagenb'.$this->yid, 0, '','int');
if(!empty($pageNumber)){
$maxItems=WGlobals::get('total'.$this->yid, 0, '','int');
if($maxItems > $pageNumber * $this->limitMax){
$this->limitStart=($pageNumber-1) * $this->limitMax;
}}
if(empty($this->limitStart))$this->limitStart=WGlobals::getUserState( "wiev-$this->yid-limitstart", 'limitstart'.$this->yid, 0, 'int') ;
$previousTOtal=WGlobals::getUserState( "wiev-$this->yid-total", 'total'.$this->yid, 0, 'int');
$resetLimit=false;
$searchreset=WGlobals::get('wz_search_reset');
$memory=WGlobals::getMemoryUsed();
if(empty($searchreset)){
$removeMe=WText::t('1206732365OQJI'). '...';
$old=WGlobals::get('search-'.$this->yid, null, $memory, 'string');
if($old==$removeMe)$old=null;
$new=WGlobals::get('search', null, 'get','string');
if(empty($new))$new=WGlobals::get('search'.$this->yid, null, 'post','string');
if( strlen($new )==1)$new=$removeMe;
if($new==$removeMe)$new=null;
if($old===null && $new===null){
WGlobals::set('search-'.$this->yid, '',$memory );
$this->searchWord='';
}elseif($new===null){
$this->searchWord=$old;
}else{
  if($new !=$old)$resetLimit=true;
  WGlobals::set('search-'.$this->yid, $new, $memory );
  $this->searchWord=$new;
}
}else{
WGlobals::set('search-'.$this->yid, null, $memory );
}
if($resetLimit || ($previousTOtal < $this->limitMax && !empty($previousTOtal))){
$this->limitStart=0;
WGlobals::set( "wiev.$this->yid.limitstart" ,0,'session');
}
if(!empty($this->searchWord)){
$this->searchOnB=true;
}
if(!empty($this->dropdown) && $this->docType=='html'){
$listingI->createPicklist($this, $this->_removedPicklistsA );
}
if(!empty($this->report))$listingI->createReportFilter($this );
if(!isset($this->_data )){
$select=array();
if(!empty($this->sid))$this->_model=WModel::get($this->sid, 'object');if(!isset($this->_model)){
return false;
}
if($this->_model->multiplePK())$this->multiplePK=true;
$groupMap=$this->_model->getParam('grpmap','xyzxzyxzy'); 
if(!empty($this->_model)){
$ordered=array();
$k=1;
$initialOrder='order'.$k;
$whereAs=0;
while (isset($this->$initialOrder)):
$direction='direction'.$k;
$ordered[$k-1][0]=$this->$initialOrder;
$ordered[$k-1][1]=$this->$direction;
$ordered[$k-1][2]=0;
$k++;
$initialOrder='order'.$k;
endwhile;
if(isset($this->queryInfoWhere)){
$paramWhere=(is_array($this->queryInfoWhere))?$this->queryInfoWhere : array($this->queryInfoWhere );
foreach($paramWhere as $key=> $whereObj){
$whereAs=(isset($whereObj->as ))?$whereObj->as : 0;
if(isset($whereObj->orderBy)){$this->_model->orderBy($whereObj->map, $whereObj->direction, $whereAs );
}else{
$whereCondition=(isset($whereObj->condition ))?$whereObj->condition : '=';
$this->_model->where($whereObj->map , $whereCondition, $whereObj->value , $whereAs );
}
}
}
$flagParams=array();
if(!empty($this->elements)){
if($this->nestedView===true){$addToForm=array('radioyn');
$securityForm=array();
}
foreach($this->elements as $lKid=> $myListing){
if(empty($myListing->sid)) continue;
$textlink2=(!empty($myListing->textlink)?$myListing->textlink : '');
WTools::getParams($myListing );
if(!empty($textlink2)){
$myListing->textlink=$textlink2;
}
if(!empty($myListing->requirednode)){
$nodeExist=WExtension::exist($myListing->requirednode );
if(empty($nodeExist)){
unset($this->elements[$lKid] );
continue;
}}
if($this->nestedView===true && in_array($myListing->type, $addToForm)){
$securityForm[$myListing->sid][]=$myListing->map;
}
$this->_model->getAs($myListing->sid );
}
if($this->nestedView===true && !empty($securityForm)){WGlobals::set('securityForm',$securityForm, 'global');
}
if(!empty($this->champsA)){
$sizeChamps=sizeof($this->champsA);
$valueChamp=array();
for($index=0; $index < $sizeChamps; ++$index){
if(empty($this->champsA[$index]->sid)) continue;
$valueChamp[$this->champsA[$index]->map]=$this->_defaultPickList[ $this->champsA[$index]->map ];
if(!empty($valueChamp[$this->champsA[$index]->map])){
$this->_model->getAs($this->champsA[$index]->sid);
}}}
if(!empty($this->filters)){
$fitlersClass=WClass::get('output.filters');
$fitlersClass->addFilterToView($this, $this->_model, self::$_removedConditionsA );
}if(!empty($this->champsA)){
for($index=0; $index < $sizeChamps; ++$index){
$valFilterDrop=(isset($this->champsA[$index]->map) && isset($valueChamp[$this->champsA[$index]->map])?$valueChamp[$this->champsA[$index]->map] : '');
if(!empty($valFilterDrop) && $valFilterDrop !='0'){
if(isset($this->champsA[$index]->operation)){
$this->_model->where($this->champsA[$index]->map, $this->champsA[$index]->operation, $valFilterDrop, $this->_model->getAs($this->champsA[$index]->sid), null, $this->champsA[$index]->bkbefore, $this->champsA[$index]->bkafter, $this->champsA[$index]->operator );
}else{
if( is_numeric($valFilterDrop )){$this->_model->whereE($this->champsA[$index]->map, $valFilterDrop, $this->_model->getAs($this->champsA[$index]->sid), null, $this->champsA[$index]->bkbefore, $this->champsA[$index]->bkafter, $this->champsA[$index]->operator );
}else{
$valFilterDropA=explode(',',$valFilterDrop );
if(!empty($valFilterDropA))$this->_model->whereIn($this->champsA[$index]->map, $valFilterDrop, $this->_model->getAs($this->champsA[$index]->sid), null, $this->champsA[$index]->bkbefore, $this->champsA[$index]->bkafter, $this->champsA[$index]->operator );
}}}}}
if(!empty($this->extstid )){
$externalSiteQuery=true;
$this->_model->extstid=$this->extstid;
}
if(!$this->multiplePK){
if(!isset($this->_pkey))$this->_pkey=$this->_model->getPK();
$this->pKeyMap=$this->_pkey .'_'. $this->_model->_infos->sid;
}else{
if(!isset($this->_pkey)){
$this->_pkey=$this->_model->getPKs();
}$this->pKeyMap=array();
foreach($this->_pkey as $onlyOneKey){
$this->pKeyMap[]=$onlyOneKey .'_'. $this->_model->_infos->sid;
}}
$parentMap=$this->_model->getParam('parentmap','parent');
$parentName=$this->_model->getParam('parentname','name');
foreach($this->elements as $nbList=> $listing){
$typeSplitA=explode('.',$listing->type );
if(empty($typeSplitA[1])){
$listing->typeNode='output';
$listing->typeName=$typeSplitA[0];
}else{
$listing->typeNode=$typeSplitA[0];
$listing->typeName=$typeSplitA[1];
}
$map=$listing->map;
$sid=$listing->sid;
$dataMap=$map.'_'.$sid;
if($map=='params'){
$mapParams=$dataMap;
}
$this->mapListA[$map]=$dataMap;
if($listing->typeName=='order' || ($listing->map=='ordering' && $listing->typeName=='customized')){$this->_orderingExist=true;
}
if(!empty($listing->treeindent)){
$this->treeindent=true;
}
if(!empty($listing->extstid )){
$extstid=$listing->extstid;
$externalSiteQuery=true;
}else{
$extstid=null;
}
if($listing->typeName=='input'){
$inputSID=WModel::get($sid, 'data');
if( strpos($inputSID->pkey, ',')===false){
$dataMapBis9=$inputSID->pkey.'_'.$sid;
if(!empty($sid))$this->_model->select($inputSID->pkey, $this->_model->getAs($sid), $dataMapBis9, 0, $extstid );
}}
if($listing->parent !=0)$this->parentB=true;
if(!empty($map)){$selectMe=true;
$mapSubStr=substr($map, 0, 2);
if($mapSubStr=='p['){
$dataMap='params_'.$sid;
$map='params';
}elseif($mapSubStr=='j['){
$dataMap='predefined_'.$sid;
$map='predefined';
}elseif($mapSubStr=='x['){
$selectMe=false;
$map=substr($map, 2, strlen($map)-3 );
$dataMap=$map.'_'.$sid;
}elseif($map==$parentMap){
$this->myParentA['parent']=$dataMap;
    $namekeyModel=WModel::get($this->_model->getModelID(), 'namekey');
$namekeyTrans=$namekeyModel.'trans';
$sidTrans=WModel::get($namekeyTrans, 'sid', null, false);
if(!empty($sidTrans)){
$parentModel=$namekeyTrans;
}else{
$parentModel=$this->_model->getParam('prtname','');
}
$parentSID=(!empty($parentModel)?WModel::get($parentModel,'sid') : $sid );
$this->myParentA['name']=$parentName.'_'.$parentSID;
}elseif($listing->typeName=='order' || ($listing->map=='ordering' && $listing->typeName=='customized')){$this->orderingMap=$dataMap;
}
if($map==$groupMap){
$this->orderingByGroup=$dataMap;
}
if(!empty($listing->search) || !empty($listing->advsearch)){
if(!empty($listing->sid) && !empty($listing->map) && substr($listing->map, 0, 2)!='x['){
$listing->asi=$this->_model->getAs($sid);
$listing->yid=$this->yid;
$listing->nodeID=$this->wid;
if(!empty($listing->advsearch)){
$this->advSeachableA[]=$listing;
$this->advSearch=true;
}if(!empty($listing->search)){
$this->searchedColumnA[]=$listing;
$this->search=true;
}}else{
$message=WMessage::get();
$message->codeE('A column cannot be advanced searchable if it does not have a sid or a valid map.');
}}
if(!array_key_exists($dataMap, $select ) && $selectMe){
if($listing->typeName =='image'){
$fileSID=WModel::get('files','sid');
$select=array('name','type','path','height','width','theight','twidth','thumbnail','storage','secure');
if(!empty($fileSID))$this->_model->select($select, $this->_model->getAs($fileSID ));
}
if($listing->typeName =='removefk'){
if(!empty($listing->filef)){
$myLocAr=explode(',',$listing->filef);
$firstDBTID=WModel::get($myLocAr[0],'dbtid');
$secondDBTID=WModel::get($myLocAr[1],'dbtid');
$this->_model->_removeFK[$firstDBTID]=$secondDBTID;
}else{
$message=WMessage::get();
$message->codeE('You need to specify what property needs to be removed in the file field: listing namekey:'.@$listing->namekey );
}}
$distinct=(isset($listing->dsict))?$listing->dsict : 0;
if($distinct==19){
WLoadFile('output.class.report' , JOOBI_DS_NODE );
$reportnosetinterval=!empty($this->reportnosetinterval)?$this->reportnosetinterval: false;
Output_Report_class::reportQuery($this->task, $this->_model, false, $map, $sid, $dataMap, $this->yid, $this->firstFormName, $reportnosetinterval );
}else{
if(!empty($sid))$this->_model->select($map, $this->_model->getAs($sid), $dataMap, $distinct, $extstid );
}$select[$dataMap]=$this->_model->getAs($sid). '.'.$map;
}
if(isset($listing->order )){
$ordered[$k+$listing->order][0]=$map;
$ordered[$k+$listing->order][1]=(isset($listing->direction) && $listing->direction )?'DESC' : 'ASC' ;
$ordered[$k+$listing->order][2]=$this->_model->getAs($sid );
}
}
if(isset($listing->accflip)){
if(!in_array($listing->rolid,WUser::roles())){
$listing->hidden=true;
}}
if($listing->hidden==true) unset($this->elements[$this->colNb]);
else {
$myNewElements[$this->colNb]=$listing;
$this->colNb++;
}
}$this->elements=$myNewElements;
$AdvSearchStatus=WGlobals::getUserState('wiev-'.$this->yid.'-adv_srch','viewIDadv','','string');
if($this->searchOnB && !$AdvSearchStatus){
if(!empty($this->searchedColumnA)){
$outputHelperC=WClass::get('output.helper');
$this->_mywordssearched=$outputHelperC->convertSearchTerms($this->searchWord );
foreach($this->searchedColumnA as $oneSeach){
$columnInstance=Output_Doc_Document::loadListingElement($oneSeach );
$columnInstance->searchQuery($this->_model, $oneSeach, $this->searchWord, 'OR');
}
}
}
if($AdvSearchStatus && !empty($this->advSeachableA )){
$newOrderA=array();
$noOrderA=array();
foreach($this->advSeachableA as $oneAdvSearch){
if(!empty($oneAdvSearch->advordering) && !isset($newOrderA[$oneAdvSearch->advordering])){
$newOrderA[$oneAdvSearch->advordering]=$oneAdvSearch;
}else{
$noOrderA[]=$oneAdvSearch;
}
}
$this->advSeachableA=array_merge($newOrderA, $noOrderA );
foreach($this->advSeachableA as $oneAdvSearch){
$columnInstance=Output_Doc_Document::loadListingElement($oneAdvSearch );
$columnInstance->searchQuery($this->_model, $oneAdvSearch );
}
}
if(!empty($usedHFilters)){
foreach($usedHFilters as $onefilter){
$modelSIDHF=WModel::get($onefilter->model, 'sid');
if(!empty($modelSIDHF))$this->_model->whereE($onefilter->map, $onefilter->value, $this->_model->getAs($modelSIDHF ));
}}
$listsOrder=$this->currentOrderDir=='DESC'?'ASC' : 'DESC';
if(!empty($this->currentOrder)){
if( array_key_exists($this->currentOrder, $select )){
$orderKey1=explode('.' , $select[$this->currentOrder]);
$orderKey=$orderKey1[1]; }else{
$orderKey=$this->currentOrder;
$orderKey1[0]=0;
$orderKey1[1]=$orderKey;
}
if( in_array($orderKey1[0].'.'.$orderKey1[1] , $select)){
$this->_model->orderBy($orderKey, $this->currentOrderDir, $orderKey1[0], true);
}
}
if(!empty($ordered)){
$size=sizeof($ordered);
$finish=false;
$index=0;
$countMe=0;
$firstIndex=null;
do {
if(isset($ordered[$index][0])){
$flag=true;
$newOrderMap=$ordered[$index][0].'_'.$this->sid;
$index3=0;
$orderKey2=explode('_' , $newOrderMap);
$orderKey=implode('_' ,array_slice($orderKey2, 0, sizeof($orderKey2)-1));
$this->_model->select($orderKey, $ordered[$index][2], $orderKey.'_'.$this->_model->getSidFromAs($ordered[$index][2])); $this->_model->orderBy($orderKey, $ordered[$index][1], $ordered[$index][2], true);
$countMe++;
if(!isset($firstIndex))$firstIndex=$index;
if($countMe >=$size ) break;
}++$index;
} while ($index<10 ); 
if(empty($this->currentOrder)){
$this->currentOrder=$ordered[$firstIndex][0];
$this->currentOrderDir=$ordered[$firstIndex][1];
}}
if(!isset($this->pKeyMap)){
$this->pKeyMap=$this->_model->getPK(). '_'.$this->sid;
if(!$this->_model->multiplePK()){
$this->_model->select($this->_model->getPK(), 0, $this->pKeyMap );
}else{
if($this->multiplePK){
foreach($this->_model->getPKs() as $primKey){
$this->_model->select($primKey, 0, $primKey.'_'.$this->sid );
}}}}
$extraTotal='';
if(!empty($this->champsA)){
foreach($this->champsA as $ckey=>$cval){
$extraTotal .=$cval->sid . $cval->map;
}}
if(!isset($this->orderingByGroup) && $this->_orderingExist){
if($this->_model->getParam('grpmap',false)){
if($this->_model->getParam('grptable',false)){
$orderSID=WModel::get($this->_model->getParam('grptable'), 'sid');
if(!empty($orderSID))$this->_model->select($this->_model->getParam('grpmap'), $this->_model->getAs($orderSID ));
$this->orderingByGroup=$this->_model->getParam('grpmap');
}else{
$this->_model->select($this->_model->getParam('grpmap'));
$this->orderingByGroup=$this->_model->getParam('grpmap');
}
}else{
$this->orderingByGroup=(!empty($this->pKeyMap))?$this->pKeyMap : $this->_pkey;
}}
$this->_model->setLimit($this->limitMax, $this->limitStart );
if(!empty($this->pagination)){
$this->_model->setReset(false);
}
if(empty($externalSiteQuery))$this->_model->getLeftJoin();
if(!empty($externalSiteQuery)){
$this->_data=$this->_model->loadExtSite('ol');
}else{
$this->_data=$this->_model->load('ol');
}$this->totalItems=count($this->_data );
if($this->totalItems > 25 && $this->menu > 1 && $this->menu < 20){
$this->menu +=10;
}
if(!empty($this->pagination) && ($this->totalItems >=$this->limitMax ||  $this->limitStart>0 )){
$this->_model->romoveSelect('special');
$this->_totalCount=$this->_model->total();
}else{
$this->_totalCount=$this->totalItems;
}
$this->pageNavO=WView::pagination($this->yid, $this->_totalCount, $this->limitStart, $this->limitMax, $this->sid ,$this->name, $this->_defaultTask );
}else{
$mess=WMessage::get();
$meYID=$this->yid;
if(!empty($this->namekey))$meYID .='  unique ID: '.$this->namekey;
$mess->codeW('No listing element found for the view: '.$meYID );
return false;
}}else{
$mess=WMessage::get();
$mess->codeW('No data found 2');
return false;
}
}else{
if(!empty($this->elements)){
foreach($this->elements as $nbList=> $listing){
$typeSplitA=explode('.',$listing->type );
if(empty($typeSplitA[1])){
$listing->typeNode='output';
$listing->typeName=$typeSplitA[0];
}else{
$listing->typeNode=$typeSplitA[0];
$listing->typeName=$typeSplitA[1];
}}}
$this->manualDataB=true;
$countedData=count($this->_data );
if($this->_totalCount < $countedData)$this->_totalCount=$countedData;
if(empty($this->_totalCount)){
}
$this->totalItems=$this->_totalCount;
$qSet=null;
}
if(isset($this->pageNavO->limit)){
$this->totalElements=($this->totalItems > $this->pageNavO->limit )?$this->pageNavO->limit : $this->totalItems;
}else{
$this->totalElements=$this->totalItems;
}
WTools::getParams($this );
if(empty($this->_data ) && (isset($this->nolist ))){
return false;
}
if(empty($this->elements ) && $this->subtype !=250 ) return false;
if(!isset($this->_model) && empty($this->_data)){
return false;
}
if( WRoles::isNotAdmin('manager')){
$subExit=WExtension::exist('subscription.node');
if($subExit){
$subscriptionDesignrestrictionC=WClass::get('subscription.designrestriction');
if( method_exists($subscriptionDesignrestrictionC, 'checkViewRestriction')){
$restrictedElementsA=$subscriptionDesignrestrictionC->checkViewRestriction();
if(!empty($restrictedElementsA)){
$this->removeElements($restrictedElementsA );
$this->removeMenus($restrictedElementsA );
}}}
}
$listingI->initializeView($this );
if(!$this->prepareView()){
return false;
}
$this->removeElementsProcess( self::$_removedElementsA, true, 'output_listings_class');
$this->changeElementsProcess();
$form=WView::form($this->formName );
$form->hidden('total'.$this->yid, $this->_totalCount );
if( WRoles::isAdmin('manager'))$form->hidden('task','');
$listingI->subtype=$this->subtype;
if(!empty($this->customContent))$listingI->customContent=$this->customContent;
$listingI->htmlObj=&$this;
if(!empty($mapParams))$listingI->paramsMapName=$mapParams;
if(!empty($this->htmlfile)){
$viewThemeI=WPage::theme($this->namekey, 'html');if(!empty($viewThemeI)){
$viewThemeI->type=49;$viewThemeI->folder=$this->folder;
$viewThemeI->wid=$this->wid;
$viewThemeI->file=$this->namekey.'.php';
$viewThemeI->setData($this->_data, 'data');
if(!empty($this->_data))$viewThemeI->setData($this->_data, 'rowContent');
if(!empty($this->htmlfile))$viewThemeI->htmlfile=$this->htmlfile;
$setTitleA=array();
$mainBodyHTML=$viewThemeI->display('',$setTitleA );
}else{
$mainBodyHTML='';
}
}else{
$mainBodyHTML=$listingI->renderContent();
}
if(isset($this->reportFilterContent ))$this->content .=$this->reportFilterContent;
if(isset($this->filtersHTML ))$this->content .=$this->filtersHTML;
$this->content .=$mainBodyHTML;
if(!empty($this->hdata)){
$myHDatas=explode(',',$this->hdata );
if(!empty($myHDatas )){
foreach($myHDatas as $myHData){
$pos=strpos($myHData, ':');
if($pos===false){$myMap4=$myHData;
}else{$hfilterMmodel=substr($myHData, 0, $pos );
$hfilterMap=substr($myHData, $pos+1 );
$myMap4=$hfilterMap.'_'.WModel::get($hfilterMmodel, 'sid');
}$myMap4Val=WGlobals::get($myMap4 );
if(empty($myMap4Val) && isset($hfilterMap))$myMap4Val=WGlobals::get($hfilterMap );
if(!empty($myMap4Val))$form->hidden($myMap4, $myMap4Val );
}}}
if(!empty($usedHFilters)){
foreach($usedHFilters as $myFitlerH){
$form->hidden($myFitlerH->map , $myFitlerH->value );
}}
if(!empty($this->_eidsListing) && !empty($form->securityFields )){
$form->hidden('joobieids', 0 );
$form->hidden('joobieids'.$this->yid, trim( implode(',',$this->_eidsListing), ','));
foreach($form->securityFields as $key=> $arguments){
$dataSecured=array();
$dataSecured[]=$key;
$dataSecured[]=$arguments;
$dataSecured[]=array_values($this->_eidsListing );
$securedString=WTools::secureMe($dataSecured );
$form->hidden(str_replace(array('[',']'),'','jsec_'.$key), $securedString );
}}
$directEditIcon='';
if('edit'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
$outputDirectEditC=WClass::get('output.directedit');
$directEditIcon=$outputDirectEditC->editView('view',$this->yid, $this->yid );
}elseif('translate'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
$outputDirectEditC=WClass::get('output.directedit');
$directEditIcon=$outputDirectEditC->translateView('view',$this->yid, $this->yid, $this->name );
}
WGlobals::set('directEditIcon',$directEditIcon, 'global');
return parent::create();
}
function setTotalCount($totalCount){
$this->_totalCount=$totalCount;
}
protected function getValue($columnName,$modelName=null){
}
protected function setValue($columnName,$value,$modelName=null){
if(empty($columnName)) return false;
if(!empty($modelName))$sid=WModel::get($modelName, 'sid', null, false);
$map=$columnName;
if(!empty($sid))$map .='_'.$sid;
$this->_data->$map=$value;
}
function setCount($total){
if(!empty($total))$this->_totalCount=$total;
}
private function _createDocFileInstance($localtion){
static $instance=array();
if(!isset($instance[$localtion] )){
WLoadFile($localtion, JOOBI_LIB_HTML.'doc'. DS );
$className='Output_Doc_'.$localtion;
$instance[$localtion]=new $className;
}
if(isset($instance[$localtion]->subTotalColmun))$instance[$localtion]->subTotalColmun=array();
return $instance[$localtion];
}
protected function getElements($yid,$params=null){
$extraParams=new StdClass;
$extraParams->select=array('namekey','map','rolid','sid','did','type','ordering','parent','hidden','search','advsearch','advordering','xsvisible','xshidden','devicevisible','devicehidden','params');
if(isset($params->_extras['unpublished'])){
$extraParams->notIN['map']=$params->_extras['unpublished'];
}
$modelM=WModel::get('library.viewlistingstrans');
$hasTextLink=$modelM->columnExists('textlink');
return parent::getSQLparent($yid, 'viewlistings','lid',$extraParams, true, true, false, $hasTextLink );
}
}