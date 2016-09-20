<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Output_Doc_htmllistings extends Output_Doc_Document {
public $subTotalName=array();
public $subTotalColmun=array();
public $subAverageColmun=array();
private $_graph3axeColumnUsed='';
private $_alreadyChecked3DGraph=false;
public $tableDetailsO=null;
public function initializeView($object=null){
WPage::renderBluePrint('listing',$object );
}
public function renderContent(){
if(!empty($this->htmlObj->myParentA)){
$this->htmlObj->myParentA['pkey']=$this->htmlObj->pKeyMap;
if(isset($this->htmlObj->orderingMap))$this->htmlObj->myParentA['ordering']=$this->htmlObj->orderingMap;
$parent=$this->htmlObj->myParentA;
$this->displayCategoryRoot=($this->htmlObj->_model->getType()==40 )?true: false;
$this->childOrderParent=array();
$shouldWeIndent=(!empty($this->htmlObj->treeindent))?2 : 1;
if( 40==$this->htmlObj->_model->getModelInfo('type')){
$this->allDataRowsA=$this->htmlObj->_data;
}else{
$this->allDataRowsA=WOrderingTools::getOrderedList($parent, $this->htmlObj->_data, $shouldWeIndent, $this->displayCategoryRoot, $this->childOrderParent );
}
$this->numberRows=count($this->allDataRowsA );
if(!$this->displayCategoryRoot && $this->numberRows < $this->htmlObj->totalElements){
$this->allDataRowsA=&$this->htmlObj->_data;
$this->numberRows=$this->htmlObj->totalElements;
}
}else{
}
$this->tableDetailsO=WPage::renderBluePrint('listing',$this->htmlObj );
if($this->subtype==210){
WLoadFile('htmllistingsdiv', JOOBI_LIB_HTML.'doc'. DS );
$instance=new Output_Doc_htmllistingsdiv;
$result=$instance->renderContentDiv($this );
return $result;
}else{
return $this->_renderContentTable();
}
}
private function _renderContentTable(){
if(!empty($this->htmlObj->pagination) || !empty($this->htmlObj->dropdown)) WPage::addJSLibrary('rootscript');
$obj=new stdClass;
if($this->tableDetailsO->tableCustomClass){
$obj->t_c=((isset($this->htmlObj->classes))?trim($this->htmlObj->classes ) : $this->tableDetailsO->tableClass );
}else{
$obj->t_c=$this->tableDetailsO->tableClass;
}
if(!empty($this->htmlObj->moduleclass_sfx)){
if(!empty($obj->t_c))$obj->t_c .=' '.trim($this->htmlObj->moduleclass_sfx );
else $obj->t_c .=trim($this->htmlObj->moduleclass_sfx );
}
if(isset($this->htmlObj->width))$obj->t_w=$this->htmlObj->width;
if(isset($this->htmlObj->cellspacing))$obj->t_spc=$this->htmlObj->cellspacing;
if(isset($this->htmlObj->cellpadding))$obj->t_p=$this->htmlObj->cellpadding;
if(isset($this->htmlObj->border))$obj->t_b=$this->htmlObj->border;
if(!empty($this->tableDetailsO->transform ) && method_exists($this->tableDetailsO->transform, 'tableStyle')){
$t=$this->tableDetailsO->transform->tableStyle($obj );
}
if(empty($t)){
WLoadFile('html-table', JOOBI_LIB_HTML_CLASS );
$t=new WTableau($obj );
}
if( method_exists($this->tableDetailsO->transform, 'preprocessMenus'))$this->tableDetailsO->transform->preprocessMenus($this->htmlObj );
if(!isset($this->htmlObj->head) || ! $this->htmlObj->head)$this->_createTableHead($t );
if(isset($this->htmlObj->hasImage))$t->hasImage=$this->htmlObj->hasImage;
if(isset($this->htmlObj->nbColumns))$t->nbColumns=$this->htmlObj->nbColumns;
$this->_createBody($t);
if(!empty($this->subTotalColmun) || !empty($listing->colaverage)){
$this->_createTotalRow($t );
}
$this->tableDetailsO->transform->createHead($this->htmlObj );
$html=$this->tableDetailsO->transform->wrapTable($t->make());
if(!empty($this->htmlObj->pagiHTML ) && ($this->htmlObj->pagination > 9 || ($this->htmlObj->pagination==1 && $this->htmlObj->pageNavO->limit > 10 )
|| ( WRoles::isNotAdmin('manager') && $this->htmlObj->pagination==1 ))){
$html .=$this->tableDetailsO->transform->createBottom($this->htmlObj );
}
if(isset($this->htmlObj->formObj )){
if(!$this->htmlObj->manualDataB){
$pKey='';
if(empty($this->htmlObj->_pkey)){
$myModel=WModel::get($this->htmlObj->sid, 'object');
foreach($myModel->getPKs() as $onePK){
if($myModel->getParam('grpmap','') !=$onePK){
$pKey=$onePK;
break;
}
}
}else{
$pKey=$this->htmlObj->_pkey;
}
$this->htmlObj->formObj->hidden( JOOBI_VAR_DATA.'[s][pkey]',$pKey );
 $this->htmlObj->formObj->hidden( JOOBI_VAR_DATA.'[s][mid]',$this->htmlObj->sid );
}
if(isset($this->htmlObj->currentOrder)){
$currentOrder=$this->htmlObj->currentOrder .'|'.$this->htmlObj->currentOrderDir;
$this->htmlObj->formObj->hidden('sorting' , $currentOrder );
}
}else{
$form=WView::form($this->htmlObj->formName );
if(isset($this->htmlObj->currentOrder)){
$currentOrder=$this->htmlObj->currentOrder .'|'.$this->htmlObj->currentOrderDir;
$form->hidden('sorting' , $currentOrder );
}
}
return $html;
}
private function _createTableHead(&$t){
if(isset($this->htmlObj->rhclss))$t->tr_c=$this->htmlObj->rhclss;
if(isset($this->htmlObj->rhsty))$t->tr_s=$this->htmlObj->rhsty;
$this->htmlObj->nbColumns=0;
foreach($this->htmlObj->elements as $listing){
WView::generateID('listing',$listing->lid );
if(!IS_ADMIN ) if(empty($listing->classes))$listing->classes=$listing->map;
$listing->nodeID=$this->htmlObj->wid;
$columnInstance=Output_Doc_Document::loadListingElement($listing );
if(empty($columnInstance)) continue;
$columnInstance->yid=$this->htmlObj->yid;
$header=$columnInstance->createHeader();
if(empty($header)){
$listingType=$listing->typeName;
if($listing->map=='ordering' && $listing->typeName=='customized')$listingType='order';
if(!empty($listing->ovly) || $listing->typeName=='hidden' || $listing->typeName=='hiddenspecial') continue;
if(!empty($listing->name)){
$header=$listing->name;
}else{
$header='&nbsp;';
}
$mapsid=$listing->map.'_'.$listing->sid;
$ordering=true;
if($mapsid[0]=='_' || (isset($listing->nosort) && $listing->nosort))$ordering=false;
}
if($listing->parent==0){
if( JOOBI_APP_DEVICE_SIZE){
if(!empty($listing->xsvisible)){
if( JOOBI_APP_DEVICE_SIZE < $listing->xsvisible ) continue;
}
if(!empty($listing->xshidden)){
if( JOOBI_APP_DEVICE_SIZE < $listing->xshidden ) continue;
}
}
if( JOOBI_APP_DEVICE_TYPE){
if(!WView::checkDevice($listing->devicevisible, $listing->devicehidden ))  continue;
}
$this->htmlObj->nbColumns++;
if('output.image'==$listing->type){
$this->htmlObj->hasImage=true;
}
if(!empty($listing->align) && $listing->align=='center'){
$t->th_c='centerHead';
}
switch ($listingType){
case 'text':
$header=$this->_ordering($header, $mapsid, $ordering, $listing->description );
break;
case 'input':
break;
case 'rownumber':
$t->th_w='5%';
$header='#';
break;
case 'checkbox':
(string)$limitstart=($this->htmlObj->limitStart=='')?'0' : $this->htmlObj->limitStart;
$joobiRun=WPage::actionJavaScript('toggleAllBox',$this->htmlObj->formName, array('nosubmit'=>true,'limitstart'=>$limitstart, 'checkid'=>'#check'.$listing->map ),'this.checked');
$header='<input type="checkbox" id="check'.$listing->map.'" name="allboxes" value="" onclick="'.$joobiRun.'" />';
break;
case 'radio':
$header=' ';
break;
case 'order':
if($listing->map=='ordering'){
$this->htmlObj->colNb++;
$cell=$this->_ordering($listing->name, $mapsid ,$ordering, $listing->description );
$t->th_c .=' tableOrdering';
$t->cell($cell , true);
$num=$this->htmlObj->totalElements-1;
if(isset($this->htmlObj->_model) && $this->htmlObj->_model->getType()==40){
$num--;
}
$myModel=WModel::get($listing->sid, 'object');
if($myModel->multiplePK()){
$groupMap=$myModel->getParam('grpmap','');
foreach($myModel->getPKs() as $pkey){
if($pkey !=$groupMap){
$myPKey=$pkey;
break;
}
}
$extraInfo='_'. $myPKey;
}else{
$myPKey=$myModel->getPK();
$extraInfo='';
}
if( WPref::load('PLIBRARY_NODE_AJAXPAGE')){
$paramsO=WObject::newObject('output.jsaction');
$paramsO->form=$this->htmlObj->formName;
if($this->htmlObj->currentOrder==$mapsid){
$valueA=array('zact'=>'all','total'=> $num );
if(!empty($this->htmlObj->nestedView)){
$valueA['vWjx']=$this->htmlObj->yid;
$valueA['fRmjx']=$this->htmlObj->formName;
}
$joobiRun=WPage::jsAction('order',$paramsO, $valueA );
}else{
$valueA=array('sorting'=> $mapsid.'|ASC');
if(!empty($this->htmlObj->nestedView)){
$valueA['vWjx']=$this->htmlObj->yid;
$valueA['fRmjx']=$this->htmlObj->formName;
}
$joobiRun=WPage::jsAction($this->htmlObj->_defaultTask, $paramsO, $valueA );
}
}else{
if($this->htmlObj->currentOrder==$mapsid){
$joobiRun='return '.WPage::actionJavaScript('order',$this->htmlObj->formName, array('zact'=>'all','zsid'=> $listing->sid, 'lstg'=>true, 'total'=>$num ));
}else{
$joobiRun='return '.WPage::actionJavaScript($this->htmlObj->_defaultTask, $this->htmlObj->formName, array('lstg'=>true,'sorting'=>true), $mapsid.'|ASC', false);
}
}
$header='<a href="#" onclick="'.$joobiRun.'" alt="ordering" title="'.WText::t('1206732363AOLD').'">';
$legendO=new stdClass;
$legendO->sortUpDown=true;
$legendO->action='saveOrder';
$legendO->alt=WText::t('1206732363AOLD');
$header .=WPage::renderBluePrint('legend',$legendO );
$header .='</a>';
$t->th_w='1%';
}else{
unset($header );
}
break;
case'translation':
break;
default:
$dontOrder=array('butdelete','butcopy','butedit',  'butdeleteall','butcopyall');
if(!in_array($listing->typeName, $dontOrder) || ((substr($listing->map, 0, 2)=='x[') || empty($listing->map) && $listing->sid==0)){
$header=$this->_ordering($header, $mapsid ,$ordering, $listing->description );
}
break;
}
if(isset($header)){
if('edit'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
$outputDirectEditC=WClass::get('output.directedit');
$header=$outputDirectEditC->editView('listing',$this->htmlObj->yid, $listing->lid, 'listing'). $header;
}elseif('translate'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
$outputDirectEditC=WClass::get('output.directedit');
$header=$outputDirectEditC->translateView('listing',$this->htmlObj->yid, $listing->lid, $listing->name ). $header;
}
$t->th_c=trim(isset($listing->classes)?trim($listing->classes ) : (isset($t->th_c)?$t->th_c : null ));
$t->th_s=(isset($listing->style))?trim($listing->style ) : null;
$t->th_a=(isset($listing->align))?$listing->align : null;
$t->th_h=(isset($listing->height))?$listing->height : null;
$t->th_w=(isset($listing->width))?$listing->width : null;
$t->cell($header , true);
}
}
}
$t->line();
$t->body('head');
}
private function _createBody(&$t){
if(!empty($this->htmlObj->_data)){
$this->rowNumber=0;
if(isset($this->htmlObj->rclss))$t->tr_c=$this->htmlObj->rclss;
if(isset($this->htmlObj->rsty))$t->tr_s=$this->htmlObj->rsty;
if(!empty($this->htmlObj->myParentA)){
$parent=$this->htmlObj->myParentA;
}else{
$this->allDataRowsA=&$this->htmlObj->_data;
$this->numberRows=$this->htmlObj->totalElements;
if(isset($this->htmlObj->manualDataB) && $this->htmlObj->manualDataB){
if(is_array($this->allDataRowsA) && !empty($this->allDataRowsA))$tempArray=array_chunk($this->allDataRowsA, $this->htmlObj->limitMax, true);
else return '';
$index=ceil(($this->htmlObj->limitStart + 1) / $this->htmlObj->limitMax ) -1;
if(empty($tempArray[$index])) return '';
$this->allDataRowsA=$tempArray[$index];
}
}
if($this->htmlObj->parentB){
$elements=array();
foreach($this->htmlObj->elements as $value){
$pt=$value->parent;
$list=(isset($elements[$pt])?$elements[$pt] : array());
array_push($list, $value );
$elements[$pt]=$list;
}
reset($elements);
if( key($elements)==0){
$elements=array_reverse($elements, true);
}
}else{
$elements[0]=$this->htmlObj->elements;
}
$this->rowOffset=isset($this->htmlObj->pageNavO->limitstart)?$this->htmlObj->pageNavO->limitstart : 0;
$linkFilters='';
if(isset($this->htmlObj->_defaultPickList )){
foreach($this->htmlObj->_defaultPickList as $pickKey=> $pickVal){
if(!empty($pickKey) && !empty($pickVal) && is_string($pickVal))$linkFilters .='&'.$pickKey.'='.$pickVal;
}
}
if(!$this->htmlObj->manualDataB){
if(!$this->htmlObj->multiplePK){
$pKey=$this->htmlObj->pKeyMap;
$pKey2=$this->htmlObj->_model->getPK(). '_'.$this->htmlObj->_model->getModelID();
}else{
$pKey=$this->htmlObj->pKeyMap;
}
$this->onlyOnceOrdering=true;
}else{
$pKey=$pKey2='';
}
$this->htmlObj->_eidsListing=array();
$paramsMapName=$this->paramsMapName;
if(!empty($this->allDataRowsA)){
$prt=(!empty($parent['parent'])?$parent['parent'] : '');
foreach($this->allDataRowsA as $rowKey=> $row){
if(!empty($row->$paramsMapName)){
WTools::getParams($row, $paramsMapName );
}
if(!$this->htmlObj->multiplePK){
if(!empty($row->$pKey)){
$this->htmlObj->_eidsListing[$row->$pKey]=$row->$pKey;
}elseif(!empty($row->$pKey2)){
$this->htmlObj->_eidsListing[$row->$pKey2]=$row->$pKey2;
}
}else{
foreach($pKey as $tryOnePK){
if(!empty($row->$tryOnePK)){
$this->htmlObj->_eidsListing[$row->$tryOnePK]=$row->$tryOnePK;
}
}
}
if(isset($row->ghost87)) continue;
if(isset($this->htmlObj->_model) && $this->htmlObj->_model->getType()==40){
if(!empty($this->htmlObj->myParentA) && empty($row->$prt)){
continue;
}elseif(isset($row->parent) && empty($row->parent)){
continue;
}
}
$tempContent=array();
$colNumPrevious=0;
foreach($elements as $elemtKey=> $elemtVal){
$doIndent=true;
foreach($elemtVal as $colNum=> $listing){
if($listing->hidden){
continue;
}
$listing->indentationDone=! $doIndent;
if( JOOBI_APP_DEVICE_SIZE){
if(!empty($listing->xsvisible)){
if( JOOBI_APP_DEVICE_SIZE < $listing->xsvisible ) continue;
}
if(!empty($listing->xshidden)){
if( JOOBI_APP_DEVICE_SIZE < $listing->xshidden ) continue;
}
}
if( JOOBI_APP_DEVICE_TYPE){
if(!WView::checkDevice($listing->devicevisible, $listing->devicehidden ))  continue;
}
if(empty($tempContent[$listing->lid]))$tempContent[$listing->lid]=new stdClass;
$this->cellValue=0;
if(isset($this->htmlObj->head) && $this->htmlObj->head ){
$t->td_w=(isset($listing->width))?$listing->width : null;
}
$map=$listing->map;
$sid=$listing->sid;
if(!IS_ADMIN ) if(empty($listing->classes))$listing->classes=$listing->map;
$t->td_c=(isset($listing->classes))?trim($listing->classes ) : null;
$t->td_s=(isset($listing->style))?trim($listing->style ) : null;
$t->td_a=(isset($listing->align))?$listing->align : null;
$t->td_h=(isset($listing->height))?$listing->height : null;
$listing->rowstyle=(isset($t->tr_c))?$t->tr_c : null;
$em=$this->rowNumber + $this->htmlObj->limitStart;
$t->tr_script=WPage::actionJavaScript('checkLine',$this->htmlObj->formName, array('nosubmit'=>true) , 'em'.$em );
$t->tr_id='jrow_'.$this->htmlObj->yid.'_'.$em;
if(!empty($tempContent[$listing->lid]->content)){
$htmlData=$tempContent[$listing->lid]->content;
unset($tempContent[$listing->lid]->content );
}else{
if( substr($map, 0, 2)=='p['){
$dataMap='params_'.$sid;
$myParams=$row->$dataMap;
$arrayP=new stdClass;
$arrayP->params=$myParams;
WTools::getParams($arrayP );
$Temp_map=substr($map, 2, strlen($map)-3);
if(isset($arrayP->$Temp_map))$this->cellValue=$arrayP->$Temp_map;
else $this->cellValue=null;
}else{
$dataMap=$map. (($sid>0)?'_'.$sid : '');
$this->cellValue=(isset($row->$dataMap))?$row->$dataMap : '';
}
if(($listing->typeName=='order' || $listing->typeName=='customized') && $listing->map=='ordering'){
$t->td_colspan=2;
$t->td_c='order';
}
if(!empty($this->htmlObj->autoselectpremium ))$listing->autoselectpremium=1;
if(!empty($this->htmlObj->nestedView ))$listing->nestedView=$this->htmlObj->nestedView;
$this->complexMap=$dataMap;
$htmlData=$this->callListingElement($listing, $row );
if(!empty($listing->graph3axe)){
if(empty($this->_graph3axeColumnUsed))$this->_graph3axeColumnUsed=$this->complexMap;
if(!isset($this->subTotalName[$colNum]))$this->subTotalName[$colNum]=array();
$_graph3axeColumnUsed=$this->_graph3axeColumnUsed;
$column3DValue=(!empty($row->$_graph3axeColumnUsed)?$row->$_graph3axeColumnUsed : 0 );
if(!isset($this->subTotalName[$colNum][$column3DValue])){
$this->subTotalName[$colNum][$column3DValue]=$htmlData;
}
}
if(!empty($listing->coltotal) || !empty($listing->colaverage)){
$_graph3axeColumnUsed=$this->_graph3axeColumnUsed;
$column3DValue=(!empty($row->$_graph3axeColumnUsed)?$row->$_graph3axeColumnUsed : 0);
if(!isset($this->subTotalColmun[$colNum]))$this->subTotalColmun[$colNum]=array();
if(!isset($this->subTotalColmun[$colNum][$column3DValue]))$this->subTotalColmun[$colNum][$column3DValue]=0;
$this->subTotalColmun[$colNum][$column3DValue] +=$this->cellValue;
if(!empty($listing->colaverage)){
if(!isset($this->subAverageColmun[$colNum]))$this->subAverageColmun[$colNum]=array();
if(!isset($this->subAverageColmun[$colNum][$column3DValue]))$this->subAverageColmun[$colNum][$column3DValue]=$this->numberRows; 
}
}
if('hiddenspecial'==$listing->typeName ) continue;
}
if(!empty($listing->lienrolid)){
if(!isset($roleHelper))$roleHelper=WRole::get();
$viewLinkNow=$roleHelper->hasRole($listing->lienrolid );
}else{
$viewLinkNow=true;
}
if($viewLinkNow && !empty($htmlData) && !empty($listing->lien)
&& !(isset($this->htmlObj->_params->_extras['unlinked'])
 && is_array($this->htmlObj->_params->_extras['unlinked'])
 && in_array($listing->map, $this->htmlObj->_params->_extras['unlinked']))){
$outputLinkC=WClass::get('output.link');
if(!empty($listing->popuplink) || WGlobals::get('is_popup', false, 'global')){
$outputLinkC->setIndex('popup');
}
$outputLinkC->wid=$this->htmlObj->wid;
if(!empty($listing->lienAjax))$link='#';
elseif(empty($listing->dontConvertLien))$link=$outputLinkC->convertLink($listing->lien, $row, $linkFilters, $this->htmlObj->_model, $this->htmlObj->mapListA );
else $link=$listing->lien;
if(!empty($tempContent[$listing->lid]->overlay )){
$toolTipsO=WPage::newBluePrint('tooltips');
$toolTipsO->tooltips=$tempContent[$listing->lid]->overlay;
$toolTipsO->title=$htmlData;
$toolTipsO->text=$htmlData;
$toolTipsO->id=WView::generateID('listing',$listing->lid.'_'.$elemtKey );
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
$linkExtras .=' target="'.$listing->target.'"';
}
if(!empty($listing->usebtn)){
$objButtonO=WPage::newBluePrint('button');
$objButtonO->text=$htmlData;
$objButtonO->type='infoLink';
$objButtonO->extraClasses='lstDv';
$objButtonO->link=$link;
$objButtonO->id=WView::generateID('listing',$listing->lid.'_b_'.$elemtKey );
if(!empty($listing->popuplink))$objButtonO->popUpIs=true;
if(!empty($listing->poph))$objButtonO->popUpHeight=$listing->poph;
if(!empty($listing->popw))$objButtonO->popUpWidth=$listing->popw;
if(!empty($listing->color))$objButtonO->color=$listing->color;
else $objButtonO->color='info';
$objButtonO->size='small';
if(!empty($listing->faicon))$objButtonO->icon=$listing->faicon;
$htmlData=WPage::renderBluePrint('button',$objButtonO );
}else{
$htmlData=WPage::createPopUpLink($link, $htmlData, $popw, $poph, $linkClass, '','',$standardLink, $linkExtras );
}
}elseif(!empty($tempContent[$listing->lid]->overlay )){
$toolTipsO=WPage::newBluePrint('tooltips');
$toolTipsO->tooltips=$tempContent[$listing->lid]->overlay;
$toolTipsO->title=$htmlData;
$toolTipsO->text=$htmlData;
$toolTipsO->id=WView::generateID('listing',$listing->lid.'_'.$elemtKey );
$htmlData=WPage::renderBluePrint('tooltips',$toolTipsO );
}
if($listing->parent==0){
if(isset($listing->anchor )){
$complextAnchorMap=$this->htmlObj->mapListA[$listing->anchor];
if(isset($row->$complextAnchorMap)){
$htmlData='<a class="anchor" name="'. $row->$complextAnchorMap.'">'.$htmlData.'</a>';
}
}
$t->cell($htmlData );
}else{
if(!empty($htmlData)){
if(!empty($listing->ovly)){
if(!isset($tempContent[$listing->parent])){
$tempContent[$listing->parent]=new stdClass;
$tempContent[$listing->parent]->overlay='';
}
$mySPC=(!empty($listing->spcspace)?'&nbsp;' : (isset($listing->spc))?$listing->spc : '<br />');
$count=(!empty($listing->parent) && count($elements[$listing->parent])>1 )?true : false;
if(empty($tempContent[$listing->parent]->overlay)){
$tempContent[$listing->parent]->overlay='';
if($count)$tempContent[$listing->parent]->overlay .=$listing->name.': '.$tempContent[$listing->parent]->overlay;
$tempContent[$listing->parent]->overlay .=$htmlData;
}else{
$tempContent[$listing->parent]->overlay .=$mySPC . $listing->name.': ';
$tempContent[$listing->parent]->overlay .=$htmlData;
}
}else{
if(!isset($listing->remt ) || !$listing->remt){
$htmlData='<span class="joobi-list-caption">'.$listing->name.': </span>'.$htmlData;
}
if(isset($listing->style) || isset($listing->classes) || isset($listing->align)){
$tag='<span';
if(isset($listing->style))$tag .=' style="'. trim($listing->style ). '"';
if(isset($listing->classes))$tag .=' class="'. trim($listing->classes ). '"';
if(isset($listing->align))$tag .=' align="'.$listing->align.'"';
$tag .='>'.$htmlData.'</span>';
$htmlData=$tag;
}else{
$htmlData='<span style="float:left;clear:both;">'.$htmlData.'</span>';
}
$textEmpty=($htmlData==WGet::$rLine );
$htmlData .=(!empty($listing->spcspace)?'&nbsp;' : (!empty($listing->spc)?('none'==$listing->spc?'' : $listing->spc ) : ($textEmpty?'': '<br />')) );
if(!isset($tempContent[$listing->parent]->content)){
$tempContent[$listing->parent]=new stdClass;
$tempContent[$listing->parent]->content='';
}
$tempContent[$listing->parent]->content .=$htmlData;
}
}
}
if(!empty($listing->indentationDone)){
$doIndent=false;
}
}
}
if(!empty($this->htmlObj->pKeyMap) && is_string($this->htmlObj->pKeyMap)){
$eidMap=$this->htmlObj->pKeyMap;
if(isset($row->$eidMap))$eid=$row->$eidMap;
else $eid=0;
}else{
$eid=0;
}
$this->rowNumber++;
$t->line($eid );
}
}
$t->body();
}
}
private function _createTotalRow($t){
static $formFile=array();
$oldSpan=0;
reset($this->allDataRowsA );
$row=current($this->allDataRowsA );
$subTotalName=!empty($this->subTotalName)?current($this->subTotalName) : array();
foreach($this->subTotalColmun as $colNum=> $totalEachColumn){
$grandeTotalA=array();
$grandeTotalVAlue=0;
$t->td_colspan=$colNum - $oldSpan;
$colSpan=$t->td_colspan;
if($t->td_colspan<2){
unset($t->td_colspan);
}
if(empty($oldSpan) || $colSpan==1){
unset($t->td_c);
$t->cell(' ');
}
$oldSpan=$colNum+1;
unset($t->td_colspan);
$t->td_c='total';
$listing=$this->htmlObj->elements[$colNum];
$t->td_a=(isset($listing->align ))?$listing->align : 'left';
foreach($totalEachColumn as $ThirdDIndex=> $colTotal){
$this->cellValue=(isset($this->subAverageColmun[$colNum][$ThirdDIndex]))?$colTotal/$this->subAverageColmun[$colNum][$ThirdDIndex] : $colTotal;
$colTotalValue=$this->callListingElement($listing, $row, 'total');
$grandeTotalVAlue +=$this->cellValue;
if(isset($subTotalName[$ThirdDIndex])){
$totalHTML='<div class="columnTotal">';
if(isset($subTotalName[$ThirdDIndex]))$totalHTML .=$subTotalName[$ThirdDIndex].': ';
$totalHTML .=$colTotalValue;
$totalHTML .='</div>';
$grandeTotalA[]=$totalHTML;
}
}
$this->cellValue=$grandeTotalVAlue;
$grandeTotalVAlue=$this->callListingElement($listing, $row, 'total');
$FinalTotalHTML=implode('',$grandeTotalA );
$FinalTotalHTML .='<div class="grandeTotal">';
if(isset($this->subAverageColmun[$colNum])){
$FinalTotalHTML .=WText::t('1374187342THQQ'). ': ';
}else{
$FinalTotalHTML .=WText::t('1206961912MJPF'). ': ';
}
$FinalTotalHTML .=$grandeTotalVAlue;
$FinalTotalHTML .='</div>';
$t->cell($FinalTotalHTML );
}
$t->line();
$t->body('foot');
}
private function _ordering($text,$orderMap,$ordering=false,$description=''){
static $jsNamekeyA=array();
$orderDir=($this->htmlObj->currentOrderDir=='ASC')?'DESC' : 'ASC';
if($ordering===false){
$pane=new WSpan($text );
if( defined('JOOBI_HEADER_LINK_CLASS'))$pane->classes=JOOBI_HEADER_LINK_CLASS;
$pane->make();
$h=$pane->display();
}else{
$task='sorting';
if(!isset($jsNamekeyA[$this->htmlObj->formName.'_'.$task])){
if( WGet::isDebug()){
$jsNamekeyA[$this->htmlObj->formName.'_'.$task]='Ordering_'.WGlobals::filter($this->htmlObj->formName.'_'.$task, 'jsnamekey');
}else{
$jsNamekeyA[$this->htmlObj->formName.'_'.$task]='WZY_'.WGlobals::count('f');
}
}
if( WPref::load('PLIBRARY_NODE_AJAXPAGE')){
$paramsO=WObject::newObject('output.jsaction');
$paramsO->form=$this->htmlObj->formName;
$paramsO->namekey=$jsNamekeyA[$this->htmlObj->formName.'_'.$task];
$valueA=array('sorting'=> $orderMap.'|'.$orderDir );
if(!empty($this->htmlObj->nestedView)){
$valueA['vWjx']=$this->htmlObj->yid;
$valueA['fRmjx']=$this->htmlObj->formName;
}
$joobiRun=WPage::jsAction($this->htmlObj->_defaultTask, $paramsO, $valueA );
}else{
$joobiRun='return '.WPage::actionJavaScript($this->htmlObj->_defaultTask, $this->htmlObj->formName, array('lstg'=>true,'sorting'=>true), $orderMap.'|'.$orderDir, $jsNamekeyA[$this->htmlObj->formName.'_'.$task] );
}
$h='<a ';
if( defined('JOOBI_HEADER_LINK_CLASS'))$h .='class="'.JOOBI_HEADER_LINK_CLASS.'" ';
$titleHeader=(!empty($description))?$description : WText::t('1206732366OVLX');
$h .='href="#" onclick="'.$joobiRun.'" title="'.WGlobals::filter($titleHeader, 'string').'">';
$h .=$text.' ';
if($this->htmlObj->currentOrder==$orderMap){
$image=( strtoupper($orderDir)=='ASC'?'desc' : 'asc');
$legendO=new stdClass;
$legendO->sortUpDown=true;
$legendO->action='orderBy';
$legendO->alt=$text;
$legendO->direction=$image;
$h .=WPage::renderBluePrint('legend',$legendO );
}
$h .='</a>';
}
return $h;
}
}
