<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WPagination {
public $total=null;
public $limit=null;
public $limitstart=null;
public $pages_total=0;
public $nestedView=false;
public $widgetID=0;
public $divID=0;
private $maxlimit=null;
private $pages_current=0;
private $pages_start=0;
private $pages_stop=0;
private $pagiStyle=null;
private $_hiddenValueA=array();
private $_displayedPages=9;private $_paginationType='linear';
function __construct($total,$limitstart,$limit,$yid,$name='',$sid=0,$defaultTask){
$this->_defaultTask=(!empty($defaultTask))?$defaultTask : WGlobals::get('task');
$this->total=$total;
$this->limitstart=$limitstart;
$this->limit=$limit;
$this->maxlimit=100;
$this->yid=$yid;
$this->sid=$sid;
$this->viewName=$name;
$level=WGlobals::getCandy();
if($level > 1)$this->_paginationType='exponential';
$this->pagiStyle=WPage::renderBluePrint('initialize','pagination');
if(empty($this->pagiStyle))$this->pagiStyle=( IS_ADMIN?11 : 1 );
}
public function setFormName($formName){
$this->formName=$formName;
}
public function setWidget($widgetID,$fid=0){
$this->widgetID=$widgetID;
$this->divID=$fid;
}
public function addHidden($map,$value){
$this->_hiddenValueA[$map]=$value;
}
public function displayNumber($formName,$task='',$increament=null,$maxDisplayNB=0){
$this->formName=$formName;
if(empty($task))$task=WGlobals::get('task');
$didlist=new stdClass;
$didlist->didLists=new stdClass;
$didlist->didLists->type=9;
$didlist->didLists->did=0;
$didlist->didLists->sid=0;
$didlist->didLists->outype=6;$didlist->didLists->map='limit'.$this->yid;
if(!empty($increament) && $increament > 1){$nextTimeStop=false;
$mxLt=( IS_ADMIN?WPref::load('PLIBRARY_NODE_MAXLIMIT') : 100 );
$maxLoop=$mxLt + 2;
for($i=1; $i < $maxLoop; $i++){
if($nextTimeStop ) break;
$oVal=pow($increament, $i );
if($oVal >=$this->total)$nextTimeStop=true;
$didlist->onedrop[$oVal]=$oVal;
}}else{
$myTypes=WType::get('output.limit');
$didlistA=$myTypes->limit;
$nextTimeStop=false;
$maxValue=($this->total > $this->limit )?$this->total : $this->limit;
if(!empty($maxDisplayNB) && $maxDisplayNB < $maxValue)$maxValue=$maxDisplayNB;
foreach($didlistA as $oVal){
if($nextTimeStop ) break;
if($oVal >=$maxValue)$nextTimeStop=true;
$didlist->onedrop[$oVal]=$oVal;
}
}
if( WGet::isDebug()){
$namekey='PaginationNumber_'.WGlobals::filter($this->formName.'_pg_'.WView::get($this->yid, 'namekey', null, null, false), 'jsnamekey');
}else{
$namekey='WZY_'.WGlobals::count('f');
}
if( WPref::load('PLIBRARY_NODE_AJAXPAGE')){
$paramsO=WObject::newObject('output.jsaction');
$paramsO->form=$this->formName;
$paramsO->namekey=$namekey;
$valueA=array('limitstart'.$this->yid=> 0 );
if(!empty($this->widgetID)){
$valueA['vWjx']=$this->divID;$valueA['vWdjx']=$this->widgetID;$valueA['fRmjx']=$this->formName;
}elseif(!empty($this->nestedView)){
$valueA['vWjx']=$this->yid;
$valueA['fRmjx']=$this->formName;
}$joobiRun=WPage::jsAction($task, $paramsO, $valueA ); 
}else{
$joobiRun='return '.WPage::actionJavaScript($task, $this->formName, array('limitstart'=>'limitstart'.$this->yid ), false, $namekey );}
$dropdownPL=WView::picklist( null, $joobiRun, $didlist );
$allVAluesA=$dropdownPL->getValues();
if(!empty($allVAluesA )){
foreach($allVAluesA as $val4){
if($val4 <=$this->limit){
$default=$val4;
}else{
break;
}}}
if(empty($default))$default=$this->limit;
$dropdownPL->defaults=$default;
if(empty($dropdownPL->formObj))$dropdownPL->formObj=new stdClass;
$dropdownPL->formObj->name=$formName;
$displayPicklist=$dropdownPL->display();return $displayPicklist;
}
public function getDisplayedPages(){
return $this->_displayedPages;
}
public function getListFooter(){
if($this->limit > 0){
$this->pages_total=ceil($this->total / $this->limit);
$this->pages_current=ceil(($this->limitstart + 1) / $this->limit);
}
if( 1 >=(int)$this->pages_total ) return '';
switch($this->pagiStyle){
case 1:
$this->_displayedPages=6;
break;
case 5:
$this->_displayedPages=8;
break;
case 11:
case 99:
default:
$this->_displayedPages=10;
break;
}if(empty($this->_displayedPages))$this->_displayedPages=6;
$this->pages_start=((floor(($this->pages_current  -1) / $this->_displayedPages)) * $this->_displayedPages +1);
if($this->pages_start + $this->_displayedPages -1 < $this->pages_total){
$this->pages_stop=$this->pages_start + $this->_displayedPages -1;
}else{
$this->pages_stop=$this->pages_total ;
}
if($this->pagiStyle > 10){
$pageof=' '. WText::t('1206732366OVLZ').' '.$this->pages_current." ".WText::t('1206732366OVMA').' '.$this->pages_total;
if(!empty($this->sid)){
$modelInstance=WModel::get($this->sid, 'object');
$modelName=$modelInstance->getTranslatedName($this->sid );
}else{
$modelName='';
}
if(empty($modelName )){
if(empty($this->viewName)){
$pageof .=' ('.$this->total.' '.WText::t('1206732366OVMB').')';
}else{
$pageof .=' ('.$this->total.' '.strtolower($this->viewName).')';
}}else{
$pageof .=' ('.$this->total.' '.strtolower($modelName).')';
}}else{
$pageof='';
}
$pageLinks=$this->_createLinks();
$currentForm=WView::form($this->formName );
$currentForm->hidden('limitstart'.$this->yid, $this->limitstart );
if(!empty($this->_hiddenValueA)){
foreach($this->_hiddenValueA as $myMap=> $myVal )$currentForm->hidden($myMap, $myVal );
}
$pageO=new stdClass;
$pageO->pageOf=$pageof;
$pageO->pageNumbersA=$pageLinks;
return WPage::renderBluePrint('pagination',$pageO );
}
private function _createLinks(){
$linkListA=array();
$linkListStartA=array();
switch($this->_paginationType){
case 'linear':
for($index=$this->pages_start; $index <=$this->pages_stop; $index++){
$linkListA[]=$this->_oneLink('','', false ,'pagiRight','pagiLeft','',$index );
}$myFirstPage=$this->pages_start;
$myLastPage=$this->pages_stop;
break;
case 'exponential':
$minimumNumberStandardPages=3;
if(empty($this->pages_total))$this->pages_total=1;
if($this->_displayedPages >=$this->pages_total){
$nbLinear=$this->pages_total;
$nbExponential=0;
}else{
$nbExponential=round( pow(10, 1-($this->_displayedPages / $this->pages_total  )) * $this->_displayedPages /10 );
if($nbExponential> ($this->_displayedPages - $minimumNumberStandardPages ))$nbExponential=$this->_displayedPages-$minimumNumberStandardPages;
$nbLinear=$this->_displayedPages - $nbExponential;
}
$buttonNumber=round(($this->_displayedPages * $this->pages_start ) / $this->pages_total);
  if($buttonNumber<1)$buttonNumber=1;$beforeLog=$afterLog=true;
if($this->pages_current >=$nbLinear
&&  $this->pages_current > ($this->_displayedPages/2 ) - $minimumNumberStandardPages ){
if(($this->pages_total - $this->pages_current ) > ($this->_displayedPages/2)  || ($this->pages_total - $nbLinear +1 ) >=$this->pages_current){
$beforeLog=true;
}else{
$afterLog=false;
}}else{
$beforeLog=false;
}if($beforeLog){if($afterLog){if(($this->pages_current / $this->pages_total)<=0.5){
$beforeLinearPage=floor($nbLinear/2);
if(!($nbLinear & 1)){
$beforeLinearPage--;
}
$startPage=$this->pages_current - $beforeLinearPage;
$stopPage=$this->pages_current + $nbLinear - $beforeLinearPage-1;
}else{
$beforeLinearPage=ceil($nbLinear/2)-1;if(!($nbLinear & 1)){
$beforeLinearPage++;
}
$startPage=$this->pages_current - $beforeLinearPage;
$stopPage=$this->pages_current + $nbLinear - $beforeLinearPage-1;
}}else{
$startPage=$this->pages_total - $nbLinear +1;
$stopPage=$this->pages_total;
}}else{
$startPage=1;
$stopPage=$nbLinear;
}
$myFirstPage=$startPage;
$myLastPage=$stopPage;
$linkListMiddleA=array();
for($index=$startPage; $index <=$stopPage; $index++){
$linkListMiddleA[]=$this->_oneLink('','', false , 'pagiRight','pagiLeft','',$index );
}
$inflexion=1;if($beforeLog){
$nbExponentialBefore=(($this->pages_current / $this->pages_total)<=0.5 )?floor($nbExponential/2) : ceil($nbExponential/2);$pagesStart=1;
$pagesStop=$startPage-1;
if($nbExponentialBefore >=$startPage)$nbExponentialBefore=$startPage-1;
$increment=($this->pages_current - floor($nbLinear/2)) / ($nbExponentialBefore + 1 );
if($pagesStop==1){$linkListA[]=$this->_oneLink('','', false ,'pagiRight','pagiLeft','', 1);
}else{
$alpha=($pagesStop + 1 - $pagesStart > 0 )?$inflexion / ($pagesStop+1 - $pagesStart ) : 1;
for($index=1; $index <=$nbExponentialBefore; $index++){
$indexUsed=$index*$increment;
$equation=(( exp(-$alpha) - exp(-$indexUsed *$alpha)) / ( exp(-$pagesStart*$alpha) - exp(-$pagesStop*$alpha)) );
$extraUnit=($equation>=1 )?0 : $pagesStart;$pagesID=floor(($pagesStop ) * $equation  + $extraUnit ) ;
if($pagesID < $myFirstPage)$myFirstPage=$pagesID;
$linkListA[]=$this->_oneLink('','', false ,'pagiRight','pagiLeft','',$pagesID );
}}
$nbExponential=$nbExponential - $nbExponentialBefore; }
$linkListA=array_merge($linkListA, $linkListMiddleA );
if($afterLog){
$pagesStart=$stopPage + 1;
$increment=($this->pages_total - $pagesStart )  / ($nbExponential + 1 );
if($this->pages_total - $pagesStart > 0)$alpha=$inflexion / ($this->pages_total - $pagesStart );
else $alpha=1;
for($index=1; $index <=$nbExponential; $index++){
$indexUsed=$pagesStart + $index*$increment;
$divider=( exp($this->pages_total*$alpha) - exp($pagesStart*$alpha));
if($divider> 0)$equation=(( exp($indexUsed*$alpha) - exp($pagesStart*$alpha)) / $divider );
else $equation=1;
$pagesID=round(($this->pages_total - $pagesStart ) * $equation +  $pagesStart );
if($myLastPage==$pagesID ) continue;
if($myLastPage < $pagesID)$myLastPage=$pagesID;
$linkListA[]=$this->_oneLink('','', false ,'pagiRight','pagiLeft','',$pagesID );
}}
break;
}
if($this->pagiStyle==1){
$startText='<<';
$prevText='<';
$endText='>>';
$nextText='>';
}else{
$startText=WText::t('1206732366OVMC');
$prevText=WText::t('1206732366OVMD');
$endText=WText::t('1206732366OVMF');
$nextText=WText::t('1206732366OVME');
}
$linkListStart='';
if($this->pages_current > 1){
$start='00';
$previous=($this->pages_current -2) * $this->limit;
if(!$previous){
$previous='00';
}
$off='';
if($myFirstPage!=1 || $this->pagiStyle>90 )$linkListStartA[]=$this->_oneLink($startText, $start, false, 'pagiRight','pagiLeft start',$off );
if(($this->pages_total > 2 && $this->_paginationType!='exponential')  || $this->pagiStyle>90 )$linkListStartA[]=$this->_oneLink($prevText, $previous,false,'prev','pagiRight',$off  );
}else{
$start='';
$previous='';
$off=' disabled';
}
$linkListA=array_merge($linkListStartA, $linkListA );
if($this->pages_current < $this->pages_total){
$end=($this->pages_total -1 ) * $this->limit;
$next=$this->pages_current * $this->limit;
$off='';
if(($this->pages_total > 2 && $this->_paginationType !='exponential') || $this->pagiStyle>90)$linkListA[]=$this->_oneLink($nextText, $next ,false,'next','pagiLeft',$off );
if($myLastPage<$this->pages_total || $this->pagiStyle>90)$linkListA[]=$this->_oneLink($endText, $end,false,'pagiRight','pagiLeft end',$off );
}else{
$end='';
$next='';
$off=' disabled';
}
return $linkListA;
}
private function _oneLink($text,$value,$current=false,$class='',$class2='',$off='',$index=null){
static $jsNamkeyA=array();
static $refNamekey='';
$pageO=new stdClass;
if($class=='next')$pageO->class='next';
elseif($class=='prev')$pageO->class='prev';
elseif($class2=='pagiLeft start')$pageO->class='start';
elseif($class2=='pagiLeft end')$pageO->class='end';
$pageO->classOne=$class;
$pageO->classTwo=$class2;
if(isset($index)){
$value=($index -1) * $this->limit;
if($index==$this->pages_current){
$pageO->current=true;
$text=$index;
$offset=0;
$current=true;
}else{
$text=$index;
}
}
$pageO->text=$text;
if(true){if($current){
}else{
$key=$this->yid;
if(!isset($jsNamkeyA[$key])){
if( WGet::isDebug()){
$jsNamkeyA[$key]='NaviPagi_'.WGlobals::filter($this->formName.'_nvPg_vw_'.WView::get($this->yid, 'namekey', null, null, false), 'jsnamekey');
}else{
$jsNamkeyA[$key]='WZY_'.WGlobals::count('f');
}
$refNamekey=$jsNamkeyA[$key];
}
if( WPref::load('PLIBRARY_NODE_AJAXPAGE')){
$paramsO=WObject::newObject('output.jsaction');
$paramsO->form=$this->formName;
$paramsO->namekey=$jsNamkeyA[$key];
$valueA=array('limitstart'.$this->yid=> $value );
if(!empty($this->widgetID)){
$valueA['vWjx']=$this->divID;$valueA['vWdjx']=$this->widgetID;$valueA['fRmjx']=$this->formName;
}elseif(!empty($this->nestedView)){
$valueA['vWjx']=$this->yid;
$valueA['fRmjx']=$this->formName;
}
$joobiRun=WPage::jsAction($this->_defaultTask, $paramsO, $valueA );
}else{
$JSPramsA=array('pagi'=>true, 'limitstart'=>'limitstart'.$this->yid );
$joobiRun='return '.WPage::actionJavaScript($this->_defaultTask, $this->formName, $JSPramsA, $value, $jsNamkeyA[$key] );
}
$pageO->linkOnClick=$joobiRun;
$pageO->off=$off;
}
return $pageO;
}
}
}