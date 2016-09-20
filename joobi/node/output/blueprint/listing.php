<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WRender_Listing_blueprint extends Theme_Render_class {
  public function render(&$data){
$obj=new stdClass;
  $obj->tableClass='table';
  $tableStriped=$this->value('table.striped');
  if($tableStriped)$obj->tableClass .=' table-striped';
  $tableHover=$this->value('table.hover');
  if($tableHover)$obj->tableClass .=' table-hover';
  $tableBorder=$this->value('table.border');
  if($tableBorder)$obj->tableClass .=' table-bordered';
  $tableCondensed=$this->value('table.condensed');
  if($tableCondensed)$obj->tableClass .=' table-condensed';
  $obj->tableCustomClass=false;
  $obj->tableStyle='';
  $obj->transform=new WRender_Listing_functionality_blueprint;
  $obj->transform->showButtonColor=$this->value('table.buttoncolor');
  $obj->transform->showButtonText=$this->value('table.buttontext');
  $obj->transform->showButtonIcon=$this->value('table.buttonicon');
  $obj->transform->showButtonIconPosition=$this->value('table.buttonposition');
  $obj->transform->showButtonIconColored=$this->value('table.buttoniconcolored');
return $obj;
  }
}
class WRender_Listing_functionality_blueprint extends WClasses {
public $showButtonColor=false;
public $showButtonText=false;
public $showButtonIcon=true;
public $showButtonIconPosition='';
public $showButtonIconColored=false;
private $htmlObj=null;
private $_AdvSearchStatus=false;
private $_magicHeader=false;
private $_jsRun=null;
public function tableStyle($obj){
WLoadFile('html-table', JOOBI_LIB_HTML_CLASS );
$table=new WTableau($obj );
return $table;
}
public function wrapTable($html){
return '<div class="table-responsive">'.$html.'</div>';
}
public function createHead($htmlObj){
$this->htmlObj=$htmlObj;
$viewID=$this->htmlObj->yid;
$this->_AdvSearchStatus=WGlobals::getUserState( "wiev-$viewID-adv_srch", 'viewIDadv','','string');
if( WGet::isDebug()){
$uniqueKey='SearchBox_'.WGlobals::filter($this->htmlObj->formName, 'jsnamekey');
}else{
$uniqueKey='WZY_'.WGlobals::count('f');
}
if( WPref::load('PLIBRARY_NODE_AJAXPAGE')){
$paramsO=WObject::newObject('output.jsaction');
$paramsO->form=$this->htmlObj->formName;
$paramsO->namekey=$uniqueKey;
$paramsO->goBtn=true;
$valueA=array('limitstart'.$viewID=> 0 );
if(!empty($htmlObj->nestedView)){
$valueA['vWjx']=$htmlObj->yid;
$valueA['fRmjx']=$this->htmlObj->formName;
}
$this->_jsRun=WPage::jsAction($this->htmlObj->_defaultTask, $paramsO, $valueA );
}else{
$this->_jsRun='return '.WPage::actionJavaScript($this->htmlObj->_defaultTask, $this->htmlObj->formName, array(), false, $uniqueKey );
}
$searchWord=WText::t('1206732365OQJI');
$this->_createListingHead($searchWord );
if($this->_magicHeader){
$filterHTML='';
$alreadyAddedPagination=false;
if(!empty($this->htmlObj->headSeachFooter )){
$filterHTML .=$this->htmlObj->headSeachFooter;
}
$filterPAginationDIV='';
if($this->htmlObj->pagination < 11){
if(!empty($this->htmlObj->pagiHTML)
&& $this->htmlObj->pageNavO->pages_total > $this->htmlObj->pageNavO->getDisplayedPages()){
$textMe='<div class="pull-left pageNb">'.WText::t('1206732366OVLZ'). ' # </div>'.WGet::$rLine;
$textMe .='<input type="text" class="form-control" value="" id="wz_pagenb" name="pagenb'.$this->htmlObj->yid.'" style="text-align:center;" size="2">'.WGet::$rLine;
$filterPAginationDIV .='<div class="pagi-element pull-left">'.$textMe.'</div>'.WGet::$rLine;
}
$filterPAginationDIV .=$this->htmlObj->pagiHTML;
$alreadyAddedPagination=true;
}
if(!empty($this->htmlObj->headNumberFooter ) || !empty($this->htmlObj->pagiHTML)){
$filterPAginationDIV .='<div class="pagi-element pull-left">'.$this->htmlObj->headNumberFooter.'</div>'.WGet::$rLine;
}
if(!$alreadyAddedPagination && !empty($this->htmlObj->headNumberFooter ) && $this->htmlObj->pagination > 4 && $this->htmlObj->pagination < 11 && !empty($this->htmlObj->pagiHTML)){
$filterPAginationDIV .=$this->htmlObj->pagiHTML;
}
if(!empty($filterPAginationDIV)){
$filterHTML .='<div class="filter-pagi pull-left">'.$filterPAginationDIV.'</div>'.WGet::$rLine;
}
if(!empty($this->htmlObj->headListFooter )){
$filterHTML .='<div class="filter-picklist btn-group pull-right">';
foreach($this->htmlObj->headListFooter as $onePicklist){
$filterHTML .='<div class="filter-one-picklist pull-left">'.$onePicklist.'</div>';
}
$filterHTML .='</div>'.WGet::$rLine;
}
if(!empty($filterHTML)){
$this->htmlObj->filtersHTML .='<div id="infoFilter" class="clearfix">'.$filterHTML.'</div>'.WGet::$rLine;
}
}
}
public function createBottom($htmlObj){
return '';
$html='<div class="clearfix">';
$html='<div class="bottomPagi center-block">';
$html .=$this->htmlObj->pagiHTML;
$html .='</div>';
$html .='</div>';
return $html;
}
private function _createListingHead($searchWord){
if(!empty($this->htmlObj->dropdown))$this->_magicHeader=true;
if($this->htmlObj->pagination){
if(!isset($this->htmlObj->pageNavO)){
$this->htmlObj->pageNavO=WView::pagination($this->htmlObj->yid, $this->htmlObj->totalItems, $this->htmlObj->limitStart, $this->htmlObj->limitMax, $this->htmlObj->sid, $this->htmlObj->name, $this->htmlObj->_defaultTask );
}
if(!empty($this->htmlObj->nestedView)){
$this->htmlObj->pageNavO->nestedView=$this->htmlObj->nestedView;
}
if(isset($this->htmlObj->formName) && $this->htmlObj->pageNavO->total > 5){
$this->htmlObj->headNumberFooter='<div class="pull-left pagi-display">'.WText::t('1206732366OVLY'). '</div>';
$this->htmlObj->headNumberFooter .='<div class="pull-left pagi-choice">'.$this->htmlObj->pageNavO->displayNumber($this->htmlObj->formName, $this->htmlObj->task, $this->htmlObj->pagiIncrement ). '</div>';
}
$this->_magicHeader=true;
if($this->htmlObj->pageNavO->total > $this->htmlObj->limitMax )
$this->htmlObj->pagiHTML=$this->htmlObj->pageNavO->getListFooter();
}
if(!empty($this->htmlObj->search) || !empty($this->htmlObj->advSearch)){
$this->_magicHeader=true;
$basicSearchBox='';
$this->htmlObj->headSeachFooter='';
if(!$this->_AdvSearchStatus){
$design=WText::t('1397594080AKCS');
$toolTips=WText::t('1397594080AKCT'). '<br>';
$toolTipsA=array();
foreach($this->htmlObj->searchedColumnA as $oneClo){
$toolTipsA[$oneClo->name]=true;
}
$toolTips .=implode(',', array_keys($toolTipsA));
$basicSearchBox .=WGet::$rLine.'<div class="searchbox btn-group pull-left">';
$basicSearchBox .='<label class="element-invisible" for="filter_search">'.$design.'</label>'.WGet::$rLine;
$basicSearchBox .='<input id="wz_search" class="hasTooltip" type="text" title=""';
if(empty($this->htmlObj->searchWord)){
$basicSearchBox .=' placeholder="'.$searchWord.'..."';
}else{
$basicSearchBox .=' value="'.$this->htmlObj->searchWord .'"';
}
$basicSearchBox .=' name="search'.$this->htmlObj->yid.'" data-original-title="'.WGlobals::filter($toolTips, 'string'). '">';
$basicSearchBox .='</div>'.WGet::$rLine;
$onClickReset='document.getElementById(\'wz_search\').value=\'\';';
}else{
$outputAdvSearchC=WClass::get('output.advsearch');
$advanceSearchHTML=$outputAdvSearchC->createAdvanceSearch($this->htmlObj->advSeachableA, $this->htmlObj->elements );
$this->htmlObj->headSeachFooter .='<div class="panel panel-info filter-advsearch"><div class="panel-heading"><h4 class="panel-title">'.WText::t('1382068744SDQH'). '</h4></div><div class="panel-body">'.$advanceSearchHTML.'</div></div>'.WGet::$rLine;
$onClickReset='';
foreach( Output_Doc_Document::$advSearchHTMLElementIdsA as $oneID){
$onClickReset .='document.getElementById(\''.$oneID.'\').value=\'\';';
}
}
$this->htmlObj->headSeachFooter .='<div class="filter-search btn-group pull-left">';
$this->htmlObj->headSeachFooter .=$basicSearchBox;
$this->htmlObj->headSeachFooter .=$this->_searchButtonGo($searchWord );
$this->htmlObj->headSeachFooter .=WGet::$rLine . $this->_searchButtonReset($onClickReset );
if(!empty($this->htmlObj->advSearch)){
$this->htmlObj->headSeachFooter .=WGet::$rLine . $this->_advSearchRendering();
}
$this->htmlObj->headSeachFooter .='</div>'.WGet::$rLine;
}
}
private function _searchButtonGo($searchWord){
$ButtonO=WPage::newBluePrint('button');
$ButtonO->type='button';
$ButtonO->buttonType='submit';
$ButtonO->id='zxgo'.$this->htmlObj->yid;
if($this->showButtonText)$ButtonO->text=WText::t('1206732365OQJJ');
if($this->showButtonColor)$ButtonO->color='success';
if($this->showButtonIconColored){
$ButtonO->coloredIcon=true;
$ButtonO->color='success';
}
$ButtonO->valueOn=true;
$ButtonO->tooltip=$searchWord;
if($this->showButtonIcon)$ButtonO->icon='fa-search';
$ButtonO->linkOnClick=$this->_jsRun;
return WPage::renderBluePrint('button',$ButtonO );
}
private function _searchButtonReset($clearMe){
$resetText=WText::t('1206732365OQJK');
$ButtonO=WPage::newBluePrint('button');
$ButtonO->type='button';
$ButtonO->buttonType='button';
$ButtonO->id='zxrst'.$this->htmlObj->yid;
if($this->showButtonText)$ButtonO->text=$resetText;
$ButtonO->valueOn=true;
if($this->showButtonColor)$ButtonO->color='danger';
if($this->showButtonIconColored){
$ButtonO->coloredIcon=true;
$ButtonO->color='danger';
}
$ButtonO->tooltip=$resetText;
if($this->showButtonIcon)$ButtonO->icon='fa-times';
$ButtonO->linkOnClick=$clearMe . $this->_jsRun;
$this->htmlObj->headSeachFooter .=WPage::renderBluePrint('button',$ButtonO );
}
private function _advSearchRendering(){
if(empty($this->htmlObj->formObj)) return false;
$paramsArray=array();
$paramsArray['controller']='output';
$paramsArray['disable']=true;
$paramsArray['ajxUrl']='controller=output&task=advsearch';
$this->_href=false;
$myJS='return '.WPage::actionJavaScript('advsearch',$this->htmlObj->formObj->id, $paramsArray, 'this',$this->htmlObj->formName.'_advSearchID', false);
if($this->_AdvSearchStatus){
$buttonText=WText::t('1381965412GYKW');
}else{
$buttonText=WText::t('1397594081EDOY'). '<br>';
$toolTipsA=array();
foreach($this->htmlObj->advSeachableA as $oneClo){
$toolTipsA[$oneClo->name]=true;
}
$buttonText .=implode(',', array_keys($toolTipsA));
}
$this->htmlObj->formObj->hidden('viewID',$this->htmlObj->yid );
$ButtonO=WPage::newBluePrint('button');
$ButtonO->type='button';
$ButtonO->buttonType='button';
$ButtonO->id='advSrch'.$this->htmlObj->yid;
if($this->showButtonText)$ButtonO->text=WText::t('1298294169DGIW');
$ButtonO->valueOn=true;
if($this->showButtonColor)$ButtonO->color='info';
if($this->showButtonIconColored){
$ButtonO->coloredIcon=true;
$ButtonO->color='info';
}
$ButtonO->tooltip=$buttonText;
if($this->showButtonIcon)$ButtonO->icon='fa-search-plus';
$ButtonO->linkOnClick=$myJS;
return WPage::renderBluePrint('button',$ButtonO );
}
}