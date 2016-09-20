<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WView extends WElement {
public static $directionForm=array();public static $titleDisplay=array();
public static $viewID=array();
var $_eid=null;
var $_elements=null;var $_data=null;
var $form=true;
var $_extraContent=''; 
public $content=null;var $formObj=null;
public $firstFormName=null;
var $_firstLayout=null;
var $_model=null;var $_noShow=false;
var $_viewParams=null; var $level=0;
var $access=0;
var $firstForm=true;
var $css=array();
var $_top=array();  var $_bottom=array();  
public $pageTitle='';
public $nestedView=null;
public $viewClass=array();
public $elements=array();
private $menu2Remove=array();
protected $_removedPicklistsA=array(); 
private static $_formInstanceA=array();
protected $formDirection=null;
protected static $_removedElementsA=array();
protected static $_changeElementsA=array();
protected static $_removedConditionsA=array();
private static $_removedPicklistValuesA=array();
private static $_firstFormName=null;
private static $_formCount=0;
private static $_loadeA=array();
private static $_IDsA=array();
function __construct($layout){
static $returnId=null;
static $firstForm=true;
static $firstHeader=true;
static $firstLayout=true;
if(empty($layout->onlyLoadParent)){
$originalLayout=$layout->viewElements;
$params=$layout->viewParams;
if(isset($params)) parent::__construct( null, $params );
if(!isset($this->nestedView ))$this->nestedView=false;if(!empty($this->nestedView))$firstForm=false;
$this->addProperties($originalLayout );
if(!empty($this->fdirection )){self::$directionForm[$this->yid]='form-'.$this->fdirection;
if(empty($this->formDirection))$this->formDirection='form-'.$this->fdirection;
}elseif(!empty($this->formDirection )){
self::$directionForm[$this->yid]=$this->formDirection;
}else{
self::$directionForm[$this->yid]='';
}
if(!empty($this->notitle )){
if( 7==$this->notitle ) self::$titleDisplay[$this->yid]='placeHolder';
else self::$titleDisplay[$this->yid]='hide'; 
}else{
self::$titleDisplay[$this->yid]='';
}
if(empty(self::$directionForm[$this->yid])) self::$directionForm[$this->yid]='form-horizontal';
$this->sid=$originalLayout->sid;
if(empty($this->subtype))$this->subtype=WGlobals::get('jdoctype');
$this->subtype=(!empty($this->laysubtype))?$this->laysubtype : $this->subtype;
if(( ! (($this->nestedView || $this->menu==0 || $this->menu==1 ) && $this->type > 200 ) || $this->subtype==110 || $this->subtype==117 ) && $this->subtype!=250){$this->elements=$this->getElements($this->yid, $params );
}
WTools::getParams($this );
if( WRoles::isNotAdmin('manager') && ! $this->frontend){
$message=WMessage::get();
$myM='This view is not allowed in the frontend! ('.$this->namekey.')';
$myM .='<br />If you want to show this view you need to set the view attribute "frontend" to Yes. ';
$message->codeW($myM, null, false);
$message->userW('1206732370TAOI');
$this->_noShow=true;
return false;
}
$themeExist=self::initializeTheme();
if(true !==$themeExist ) return $themeExist;
if(!IS_ADMIN && $firstForm){
$outputSpaceC=WClass::get('output.space');
$spaceO=$outputSpaceC->findSpace();
if(!empty($spaceO)){
if(!empty($spaceO->themeFolder))$this->theme=$spaceO->themeFolder;
if(!empty($spaceO->namekey ))$this->space=$spaceO->namekey;
if(empty($spaceO->menu )){
$spaceO->menu=str_replace('.','_', WExtension::get($this->wid, 'namekey'));
$spaceO->menu .='_horizontalmenu_fe';
}}
if( WGlobals::getSession('frmwrk','remoteAccess', false)){
$this->horizmenu=true;
}if(!empty($spaceO->menu) && !empty($this->horizmenu)){
$task=WGlobals::get('task');
if('edit' !=$task && 'add' !=$task){
$correspondingPreferences='P'.strtoupper($spaceO->menu );
$preVAlue=WPref::load($correspondingPreferences );
if(!defined($correspondingPreferences ) || $preVAlue){
WGlobals::set('fe-horizontal-menu-yid',$spaceO->menu, 'global');
}}
}
}
if($firstForm){
WGlobals::set('firstFormViewType',$this->type, 'global');
}
WView::definePath((!empty($this->theme)?$this->theme : ''));
$resetForm=WGlobals::get('resetForm','no','global');
if($resetForm=='yes' && empty($this->nestedView)){
WGlobals::set('resetForm','no','global');
$firstForm=true;
}
self::$viewID[]=$this->yid;
if($this->form && $firstForm && empty($this->isVIewMenu) && ! in_array($this->type, array( 3, 204 ))){
$formId='wf_'.WGlobals::filter($this->controller.'_'.$this->namekey, 'jsnamekey');
WGlobals::set('parentFormid',$formId, 'global');
if(empty($this->firstFormName )){
$this->firstFormName=$formId;
$this->_firstLayout=$this->yid;
}
$this->formObj=WView::form($formId );
if(!empty($this->formDirection))$this->formObj->class=$this->formDirection;
if($this->type==2)$this->formObj->autoComplete=false;
if(isset($this->index2))$this->formObj->index2=$this->index2;
if(isset($this->reqtask))$this->formObj->reqtask=$this->reqtask;
if(isset($this->nojsvalid))$this->formObj->nojsvalid=$this->nojsvalid;
$itemid=null;
if( JOOBI_FORM_HASOPTION){
if(!empty($this->option)){
$option=$this->option;
}else{
$option=null;
if( WGlobals::get('appType','application','global')=='module'){
$pref=WPref::get($originalLayout->wid);
$itemid=$pref->getPref('itemid', null );
$option='com_'.$pref->getPref( JOOBI_URLAPP_PAGE, '');
if( strlen($option) < 6)$option=null;
}if(empty($option))$option=WApplication::name('com');
}$this->formObj->hidden( JOOBI_URLAPP_PAGE, $option );
}
$this->formObj->hidden('controller',$this->controller );
if(!IS_ADMIN){
if($itemid==null){
$itemid=WPage::getPageId();
}if(!empty($itemid))$this->formObj->hidden( JOOBI_PAGEID_NAME, $itemid );
}
$this->formObj->hidden('task','');
$this->formObj->hidden('boxchecked' , 0 );
if(isset($this->fname )){
$this->formObj->name=$this->fname;
}else{
$this->formObj->name=$formId;
}
$spoofValue=WPage::getSpoof( JOOBI_SPOOF );
$this->formObj->hidden($spoofValue , (string)JOOBI_SPOOF );
if(isset($this->controller))$this->formObj->controller=$this->controller;
}else{
$formId=$this->firstFormName=WGlobals::get('parentFormid','','global');
}
$this->task=WGlobals::get('task');
$this->_defaultTask=$this->task;
if($this->form && $firstForm){$firstForm=false;
}
}
return true;
}
public static function initializeTheme(){
static $initialize=true;
if($initialize){
$available=WPage::renderBluePrint('initialize');
if($available){
$initialize=false;
}else{
$outputErrorC=WClass::get('output.error');
return $outputErrorC->manageMissingTheme();
}}
return true;
}
public static function getHTML($yid,$controller=null,$params=null){
if(isset($controller->firstForm)){
if(!isset($params))$params=new stdClass;
$params->firstForm=$controller->firstForm;
unset($controller->firstForm);
}
$a=WView::get($yid, 'html',$params, $controller );
 return $a;
}
public static function get($yid,$return='html',$params=null,$controller=null,$showMessage=true,$dontCheckRole=false){
static $ctrid=0;
static $access=null;
if($params==null)$params=new stdClass;
$superOverride=(isset($params->superOverride))?$params->superOverride : null;
$superolid=(isset($params->superolid))?$params->superolid : null;
if(empty($yid)){
$a=null;
return $a;
}
if(!in_array($return, array('html','pdf','rss','csv','graph'))){return WView::ooView($yid, $return, $controller, $superOverride, $superolid, $showMessage, $dontCheckRole );
}else{
$params->docType=$return;
}
if(empty($controller)){
$controller=WController::get();
}
if(!empty($controller)){
if(empty($controller->wid)){
$controller->wid=WView::get($yid, 'wid');
}
$yidIsNumeric=is_numeric($yid );
if(empty($controller->layout) || (!empty($controller->layout->namekey) && (( ! $yidIsNumeric && $controller->layout->namekey !=$yid ) || ($yidIsNumeric && $controller->layout->yid !=$yid)) )){$layout=WView::ooView($yid, $return, $controller, $superOverride, $superolid, $showMessage, $dontCheckRole );
}else{
$layout=$controller->layout;
}
}else{$layout=WView::ooView($yid, $return, $controller, $superOverride, $superolid, $showMessage, $dontCheckRole );
}
if(!empty($layout)){
$myParams=new stdClass;
if(empty($params->onlyLoadParent )){
if(isset($controller->level))$params->level=$controller->level;
if(isset($controller->rolid))$params->rolid=$controller->rolid;
if(isset($controller->option))$params->option=$controller->option;
$params->task=(isset($controller->task))?$controller->task : '';
$params->folder=WExtension::get($layout->wid, 'folder');
$params->controller=(isset($controller->controller))?$controller->controller : '';
$params->nestedView=(isset($controller->nestedView))?$controller->nestedView : false; 
if(empty($layout->sid) && !empty($controller->sid))$layout->sid=$controller->sid;
if(isset($controller->firstFormName)){
$params->firstFormName=$controller->firstFormName;
}
if((isset($controller->ctrid))){
$ctrid=$controller->ctrid;
WGlobals::set('ctrid',$ctrid, 'global');
}$params->ctrid=$ctrid;
$myParams->viewElements=$layout;
$myParams->viewParams=$params;
}else{
$myParams->onlyLoadParent=true;
}
WTools::getParams($layout );
if(!empty($layout->pnamekey )){
$parentParams=new stdClass;
$parentParams->onlyLoadParent=true;
WView::get($layout->pnamekey, 'html',$parentParams );
if(empty($layout->path)){
$className=ucfirst( WExtension::get($layout->wid, 'folder')). '_'.ucfirst($layout->namekey ). '_view';
if(!class_exists($className )){
$hasInstance=true;
$parentWID=WView::get($layout->pnamekey, 'wid');
$parentCassName=ucfirst( WExtension::get($parentWID, 'folder')). '_'.ucfirst($layout->pnamekey ). '_view';
if(!class_exists($parentCassName )){
return false;
}
WText::load($parentWID );
WPref::get($parentWID );
$extendedClass='class '.$className.' extends '.$parentCassName.'{}';
eval($extendedClass );
$HTML=new $className($myParams );
}
}
}
if(empty($hasInstance)){
WText::load($layout->wid );
WPref::get($layout->wid );
if(!empty($layout->phpfile) && (empty($layout->isVIewMenu) || (!empty($layout->typeOriginal) && $layout->typeOriginal==$layout->type ))){
if($layout->type < 50){
WLoadFile('output.class.listings', JOOBI_DS_NODE );
}elseif($layout->type > 50 && $layout->type < 200){
WLoadFile('output.class.forms', JOOBI_DS_NODE );
}elseif($layout->type >=200 && $layout->type < 240){
WLoadFile('output.class.mlinks', JOOBI_DS_NODE );
}elseif($layout->type==249){
WLoadFile('output.class.customized', JOOBI_DS_NODE );
}
if(!empty($layout->wid)){
$myFolder=WExtension::get($layout->wid, 'folder');
}else{
$myFolderA=explode('_',$layout->namekey );
$myFolder=array_shift($myFolderA );
}
$layout->namekey=str_replace( array('.',' '), '_',$layout->namekey );
if(!empty($layout->custom))$base=JOOBI_DS_USER.'node'.DS;
else $base=null;
$HTML=WClass::get($myFolder.'.'.$layout->namekey, $myParams, 'view', false, $base );
if(empty($HTML)){
return false;
}
$status=$HTML->prepareQuery();
if(!$status ) return false;
}else{
if($layout->type < 50){
$HTML=WClass::get('output.listings',$myParams );
}elseif($layout->type > 50 && $layout->type < 200){
$HTML=WClass::get('output.forms',$myParams );
}elseif($layout->type < 255){
$HTML=WClass::get('output.mlinks',$myParams );
}else{
return '';
}}
}
if(isset($controller->sid) && isset($params->dynamicForm) && $params->dynamicForm)$HTML->sid=$controller->sid;
}else{
$HTML=null;
}
return $HTML;
}
public static function pagination($yid,$total,$limitstart,$limit,$sid=0,$name='',$defaultTask=''){
static $pagiA=array();
$key=$name.'-'.$yid;
if(!isset($pagiA[$key])){
WLoadFile('output.doc.pagination', JOOBI_DS_NODE );
$pagiA[$key]=new WPagination($total, $limitstart, $limit, $yid, $name, $sid, $defaultTask );
if(($pagiA[$key]->limit - $pagiA[$key]->limitstart) > PLIBRARY_NODE_MAXLIMIT){
$pagiA[$key]->limit=$pagiA[$key]->limitstart + PLIBRARY_NODE_MAXLIMIT;
}}
return $pagiA[$key];
}
public static function form($formHTMLId='',$reset=false,$firstFormName=false,$params=null){
if($reset){
$instance=array();
self::$_firstFormName=null;
WGlobals::set('parentFormid','','global');
$aa=null;
return $aa;
}
if($firstFormName ) return self::$_firstFormName;
if(empty($formHTMLId))$formHTMLId=WGlobals::get('parentFormid','','global');
if(!isset(self::$_formInstanceA[$formHTMLId])){
self::$_formInstanceA[$formHTMLId]=new WForm($formHTMLId, $params );
if(empty(self::$_firstFormName)) self::$_firstFormName=$formHTMLId;
}
return self::$_formInstanceA[$formHTMLId];
}
public static function picklist($picklistID,$onChange='',$params=null,$property=''){
static $loadedFile=true;
if($loadedFile){
WLoadFile('output.picklist.class', JOOBI_DS_NODE );
$loadedFile=false;
}if(!empty($property)){
$instance=new WPicklist_main($picklistID, '', false);return $instance->getPicklistProperties($property );
}
$isListing=(!empty($params->listing))?true : false;
$instance=new WPicklist_main($picklistID, $onChange, $isListing, $params );
return $instance;
}
public function getPicklistProperties($property){
if(!empty($this->_didLists[0]) && !empty($this->_didLists[0]->$property )) return $this->_didLists[0]->$property;
return null;
}
public static function checkDevice($devicevisible,$devicehidden){
if(!empty($devicevisible)){
$visibleA=explode('|_|',$devicevisible );
if(!in_array( JOOBI_APP_DEVICE_TYPE , $visibleA )) return false;
}if(!empty($devicehidden)){
$hiddenA=explode('|_|',$devicehidden );
if( in_array( JOOBI_APP_DEVICE_TYPE , $hiddenA )) return false;
}
return true;
}
public static function popupMemory($html='',$reset=false){
static $popUpA=array();
if($reset){
if(!empty($popUpA)){
$content=implode('',$popUpA );
$popUpA=array();
return $content;
}
return '';
}
$popUpA[]=$html;
}
public static function includeElement($element,$location=null,$custom=false,$force=false,$base=null){
if(isset( self::$_loadeA[$element] )) return true;
self::$_loadeA[$element]=true;
if(empty($location))$location=JOOBI_LIB_HTML;
$elementA=explode('.',$element );
if( sizeof ($elementA )==2){
$typeNode=$elementA[0];$typeName=$elementA[1];
$location=JOOBI_LIB_HTML;
$themeNode='output';
}else{
$typeNode=$elementA[1];$typeName=$elementA[2];
$themeNode=$elementA[0];
if(empty($base))$base=JOOBI_DS_NODE;
$location=$base;
}
if($custom){
$fClass=ucfirst($themeNode);
$classname=$fClass.'_Core'.$typeName.'_'.$typeNode;
}else{
$fClass=ucfirst($typeNode);
$classname='W'.$fClass.'_Core'.$typeName;
$classWithout='W'.$fClass.'_'.$typeName;
}
if(!class_exists($classname ) || $force){
WLoadFile($element, $location );
if($custom){
$classnameOrign=$fClass.'_'.$typeName.'_'.$typeNode;
}else{
$classnameOrign='W'.$fClass.'_'.$typeName;
}
if(!class_exists($classnameOrign )){
if(!defined('JOOBI_DS_THEME_JOOBI')) WView::definePath();
WLoadFile('node.'.$themeNode.'.'.$typeNode.'.'.$typeName, JOOBI_DS_THEME_JOOBI, true, false);
if(!class_exists($classnameOrign )){
if(!class_exists($classname )){
$evalClassExt='class '.$classname.'{}';
eval($evalClassExt );
}$evalClass='class '.$classnameOrign.' extends '.$classname.'{}';
eval($evalClass );
}}
}else{
 if(!empty($classWithout) && !class_exists($classWithout )){
$evalWOClass='class '.$classWithout.' extends '.$classname.'{}';
eval($evalWOClass );
}}
return true;
}
protected function prepareQuery(){return true;
}
protected function prepareView(){return true;
}
protected function prepareTheme(){
return true;
}
public static function generateID($type='x',$elementID){static $debug=null;
if(!isset($debug))$debug=WGet::isDebug();
if($debug){
self::$_IDsA[$type][$elementID]=$type.'_'.WGlobals::filter($elementID, 'jsnamekey');
}else{
$st='x';
switch($type){
case 'view':
$st='v';
break;
case 'form':
$st='f';
break;
case 'listing':
$st='l';
break;
case 'menu':
$st='m';
break;
case 'picklist':
$st='p';
break;
case 'hidden':
$st='h';
break;
case 'widget':
$st='w';
break;
case 'nested':
$st='n';
break;
case 'formWidget':
$st='h';
break;
default:
break;
}
self::$_IDsA[$type][$elementID]=$st . WGlobals::filter($elementID, 'jsnamekey');
}
return self::$_IDsA[$type][$elementID];
}
public static function retreiveID($type,$elementID){
if(isset(self::$_IDsA[$type][$elementID])) return self::$_IDsA[$type][$elementID];
return false;
}
public static function getURI($keepEid=true){
$uri=array();
$arguments=array( JOOBI_URLAPP_PAGE=>'','controller'=>'','task'=>'','eid'=>'', JOOBI_PAGEID_NAME=> '');
$keysA=WGlobals::getEntireSuperGlobal('get');
foreach($keysA as $key9=> $prop){
if($key9==JOOBI_VAR_DATA.'[s][pkey]') break;if($key9==JOOBI_VAR_DATA || $key9=='joobJSrequired') continue;
$arguments[$key9]=WGlobals::get($key9, '','get');
}
if(empty($arguments['controller']))$arguments['controller']=WGlobals::get('controller');
if(empty($arguments['task']))$arguments['task']=WGlobals::get('task');
$myOption=WApplication::name('com');
if($arguments[JOOBI_URLAPP_PAGE]==$myOption){
if(!IS_ADMIN && JOOBI_FRAMEWORK_TYPE=='joomla' && empty($arguments[JOOBI_PAGEID_NAME])){
$arguments[JOOBI_PAGEID_NAME]=WPage::getPageId();
}
if($keepEid){
$eid=WGlobals::getEID();
if(!empty($eid ))$arguments['eid']=$eid;
}else{
unset($arguments['eid'] );
}}
$finalURL=( WGlobals::get('is_popup', false, 'global')?JOOBI_INDEX2 : JOOBI_INDEX ). '?';
foreach($arguments as $prop=> $val){
if(!empty($val) && !empty($prop) && is_string($prop) && ( is_string($val) || is_numeric($val)))$finalURL .=$prop.'='.$val.'&';
}
$finalURL=trim($finalURL, '&');
return $finalURL;
}
function addContent($content){
$this->_extraContent .=$content;
}
public static function addJSAction($task,$formName,$param=array()){
$onclick=WPage::actionJavaScript($task, $formName, $param );
return $onclick;
}
public static function storeData(&$data,$store=false){
static $storeData=null;
if($store)$storeData=$data;
else $data=$storeData;
}
public static function ooView($yid,$return='html',$controller=null,$superOverride=null,$superolid=null,$showMessage=true,$dontCheckRole=false){
if(isset($superOverride )){
if(!in_array($superolid, WUser::roles())){
$message=WMessage::get();
$message->userE('1441751282FYFI');
return null;
}}
$caching=WPref::load('PLIBRARY_NODE_CACHING');
$caching=($caching > 5 )?'cache' : 'static';
$params=new stdClass;
$params->controller=$controller;
$params->superOverride=$superOverride;
$lgid=WUser::get('lgid');
$langedID=$yid.'-'.$lgid;
$tempdata=WCache::getObject($langedID, 'Views',$caching, true, false, '',$params, $showMessage );
if(empty($tempdata))$tempdata=WCache::getObject($langedID, 'Views','static', true, false, '',$params, $showMessage );
if(!empty($tempdata)){
$roleC=WRole::get();
$myRoleA=$roleC->getUserRoles();
if(!in_array($tempdata->rolid, $myRoleA ) && ! $dontCheckRole ) return null;
}
if( is_string($return) && $return !='html'){
if('data'==$return ) return $tempdata;
else return (isset($tempdata->$return))?$tempdata->$return : null;
}elseif(is_array($return) && !empty($return)){
$myObj=null;
foreach($return as $each){
if(isset($tempdata->$each))$myObj->$each=$tempdata->$each;
}
return $myObj;
}
if(!empty($tempdata->widgets)){
$outputwidgetsC=WClass::get('output.widgets');
$outputwidgetsC->preLoadWidgetsForView($tempdata->yid );
WGlobals::set('pageHasWidgets', true, 'global');
}
return $tempdata;
}
function setContent($area,$content,$title=null,$force=false){
$pageT=WPage::theme($this->namekey );
$pageT->setContent($area, $content, $title, $force );
}
function setTitle($area,$title=''){
$pageT=WPage::theme($this->namekey );
$pageT->setTitle($area, $title );
}
private function getViewParent($class){
$parentClass=strtolower( get_parent_class($class ));
if( in_array($parentClass, array('output_listings_class','output_mlinks_class','output_forms_class','wview'))){
return $parentClass;
}else{
return WView::getViewParent($parentClass );
}}
public function removePicklists($picklists){
if(!is_array($picklists))$picklists=array($picklists );
$this->_removedPicklistsA=array_merge($this->_removedPicklistsA, $picklists );
}
public function removePicklistValues($picklistName,$elementListA){
if( is_string($elementListA))$elementListA=array($elementListA );
if(!is_array($elementListA)) return true;
if(empty(self::$_removedPicklistValuesA[$picklistName])) self::$_removedPicklistValuesA[$picklistName]=array();
self::$_removedPicklistValuesA[$picklistName]=array_merge( self::$_removedPicklistValuesA[$picklistName], $elementListA );
}
public static function getRemovedElements($className=''){
if(isset(self::$_removedPicklistValuesA[$className])) return self::$_removedPicklistValuesA[$className];
return false;
}
public function changeElements($namekey,$property,$value=null){
if(empty($namekey) || empty($property)) return false;
$newChangeO=new stdClass;
$newChangeO->namekey=$namekey;
$newChangeO->property=$property;
$newChangeO->value=$value;
self::$_changeElementsA[]=$newChangeO;
}
public function removeConditions($elementListA){
if(!is_array($elementListA))$elementListA=array($elementListA );
self::$_removedConditionsA=array_merge( self::$_removedConditionsA, $elementListA );
}
public function removeMenus($elementList,$removeFromSecurityCheck=true){
if(is_array($elementList ))$this->menu2Remove=array_merge($this->menu2Remove, $elementList );
elseif  ( is_string($elementList ))$this->menu2Remove[]=$elementList;
}
public function initilizeElements(){
self::$_removedElementsA=array();
self::$_changeElementsA=array();
self::$_removedConditionsA=array();
self::$_removedPicklistValuesA=array();
}
public function removeElements($elementListA,$removeFromSecurityCheck=true,$parentClass=null){
if( is_string($elementListA))$elementListA=array($elementListA );
if(!is_array($elementListA)) return true;
if(empty($parentClass))$parentClass=WView::getViewParent($this );
if('wview'==$parentClass)$parentClass='output_mlinks_class';
if(!$removeFromSecurityCheck){
$this->removeElementsProcess( array($parentClass=> $elementListA ), $removeFromSecurityCheck, $parentClass );
}else{
if(empty(self::$_removedElementsA[$parentClass])) self::$_removedElementsA[$parentClass]=array();
self::$_removedElementsA[$parentClass]=array_merge( self::$_removedElementsA[$parentClass], $elementListA );
}
}
protected function removeElementsProcess($elementListA,$removeFromSecurityCheck=true,$parentClass=null){
if(empty($elementListA)) return true;
$newElementA=(!empty($this->elements )?$this->elements : array());
$parentA=array();
if(empty($parentClass))$parentClass=WView::getViewParent($this );
if(empty($elementListA[$parentClass])) return true;
$elementList=$elementListA[$parentClass];
if(empty($elementList)) return true;
switch($parentClass){
case 'output_listings_class':
$parentColumn='lid';
break;
case 'output_mlinks_class':
$parentColumn='mid';
$removeFromSecurityCheck=false;
break;
case 'output_forms_class':
$parentColumn='fid';
break;
case 'wview':$parentColumn='mid';
$removeFromSecurityCheck=false;
break;
default:
return false;
break;
}
foreach($this->elements as $key=> $oneEelement){
$oneEelement->namekey=trim($oneEelement->namekey );
if( in_array($oneEelement->namekey, $elementList )){
$parentA[]=$oneEelement->$parentColumn;
unset($newElementA[$key] );
if($removeFromSecurityCheck && !empty($this->securityCheck[$oneEelement->sid])){
if( substr($oneEelement->map, 0, 2 )=='p['){
$secKey=array_search($oneEelement->map, $this->securityCheckParams['p'][$oneEelement->sid] );
if(isset($secKey) && $secKey !==false){
unset($this->securityCheckParams['p'][$oneEelement->sid][$secKey] );
}continue;
}
if( substr($oneEelement->map, 0, 2 )=='j[' && isset($this->securityCheckParams['j'])){
$secKey=array_search($oneEelement->map, $this->securityCheckParams['j'][$oneEelement->sid] );
if(isset($secKey) && $secKey !==false){
unset($this->securityCheckParams['j'][$oneEelement->sid][$secKey] );
}continue;
}
$secKey=array_search($oneEelement->map, $this->securityCheck[$oneEelement->sid] );
if(isset($secKey) && $secKey !==false){
unset($this->securityCheck[$oneEelement->sid][$secKey] );
sort($this->securityCheck[$oneEelement->sid] );
if(empty($this->securityCheck[$oneEelement->sid])){
unset($this->securityCheck[$oneEelement->sid] );
}}
}
}
}
$stillHaveParents=false;
do {
$stillHaveParents=WView::removeElementsParents($newElementA, $parentA, $this->securityCheck, $this->securityCheckParams );
} while ($stillHaveParents );
$this->elements=$newElementA;
return true;
}
private static function removeElementsParents(&$elementList,&$removeThoseParentA,&$securityCheck,&$securityCheckParams){
if(empty($elementList) || !is_array($elementList) || empty($removeThoseParentA)) return false;
$stillHaveParents=false;
$newElementA=$elementList;
$parentA=array();
foreach($elementList as $key=> $oneEelement){
if( in_array($oneEelement->parent, $removeThoseParentA )){
if(!empty($oneEelement->fid))$parentA[]=$oneEelement->fid;
unset($newElementA[$key] );
if(isset($oneEelement->sid) &&  !empty($securityCheck[$oneEelement->sid])){
if( substr($oneEelement->map, 0, 2 )=='p['){
$secKey=array_search($oneEelement->map, $securityCheckParams['p'][$oneEelement->sid] );
if(isset($secKey) && $secKey !==false){
unset($securityCheckParams['p'][$oneEelement->sid][$secKey] );
}continue;
}
if( substr($oneEelement->map, 0, 2 )=='j[' && isset($securityCheckParams['j'])){
$secKey=array_search($oneEelement->map, $securityCheckParams['j'][$oneEelement->sid] );
if(isset($secKey) && $secKey !==false){
unset($securityCheckParams['j'][$oneEelement->sid][$secKey] );
}continue;
}
$secKey=array_search($oneEelement->map, $securityCheck[$oneEelement->sid] );
if(isset($secKey) && $secKey !==false){
unset($securityCheck[$oneEelement->sid][$secKey] );
if(empty($securityCheck[$oneEelement->sid])) unset($securityCheck[$oneEelement->sid] );
}}$stillHaveParents=true;
}}
$removeThoseParentA=$parentA;
$elementList=$newElementA;
return $stillHaveParents;
}
protected function changeElementsProcess(){
if(empty( self::$_changeElementsA )) return false;
foreach( self::$_changeElementsA as $oneChange){
foreach($this->elements as $key=> $oneElement){
if($oneElement->namekey==$oneChange->namekey){
$property=$oneChange->property;
$this->elements[$key]->$property=$oneChange->value;
}}
}
}
public function make($notUsed=null,$notUsed2=null){
if($this->_noShow ) return '';
$createStatus=$this->create();
if($createStatus===false){
return false;
}
static $ViewIDsA=array();
$pageT=WPage::theme();
if(!empty($this->cssfile)){
$folder=WExtension::get($this->wid, 'folder');if(empty($folder))$folder=$this->folder;
WPage::addCSSFile('node/'.$folder .'/css/style.css');
}
if(!empty($this->_top))$this->content='<div class="jtopextra">'.implode( "", $this->_top ). '</div>'.$this->content;
if(!empty($this->_bottom))$this->content .='<div class="jbotextra">'.implode( "", $this->_bottom ). '</div>';
if($this->nestedView===false && WGlobals::get('wz_page_tile', false, 'global') && WGlobals::get('appType','','global')=='application'){
$this->_setPageTitle();
}
if($this->nestedView===false){
if($this->menu > 0){
$topMenu='';
$bottomMenu='';
if($this->menu > 1 && $this->type !=204){
$menuHTML=$this->makeMenu(true);
if($this->menu > 20){$bottomMenu=$menuHTML;
}
if($this->menu < 30){$topMenu=$menuHTML;
}
}
if($this->menu < 30){
if(empty($this->faicon ) && !empty($this->sid)){
$this->faicon=WModel::get($this->sid, 'faicon');
}
$viewO=new stdClass;
$viewO->action='header';
if(!empty($menuHTML))$viewO->headerMenus=$menuHTML;
$viewO->view=$this;
$viewHeader=WPage::renderBluePrint('view',$viewO );
if(!empty($viewHeader ))$pageT->setContent('headerMenu',$viewHeader );
}else{
$viewO=new stdClass;
$viewO->action='noheader';
$viewHeader=WPage::renderBluePrint('view',$viewO );
if(!empty($viewHeader)){
if(!empty($viewHeader ))$pageT->setContent('headerMenu',$viewHeader );
}}
if($this->menu > 20){
$pageT->setContent('bottomMenu',$bottomMenu );
}
}elseif( JOOBI_FRAMEWORK_TYPE=='mobile'){
$viewO=new stdClass;
$viewO->action='noheader';
$viewHeader=WPage::renderBluePrint('view',$viewO );
if(!empty($viewHeader)){
if(!empty($viewHeader )){
$pageT->setContent('headerMenu',$viewHeader );
}}
}
}
if(!empty($this->bctrail) && $this->nestedView===false){
$outputBreadCrumbC=WClass::get('output.breadcrumb');
$breadcrumb=$outputBreadCrumbC->displayBreadCrumb($this->_model, $this->_eid, $this->controller, $this->task );
$pageT->setContent('breadcrumbs',$breadcrumb );
}
if(!empty($this->filtersHTML))$this->viewClass[]='viewFilter';
if($this->menu < 30)$this->viewClass[]='viewHeader';
if( 2==$this->type)$this->viewClass[]='viewListing'; elseif( 51 <=$this->type || 150 >=$this->type){
$this->viewClass[]='viewForm';
}else{
$this->viewClass[]='viewShow';
}
if(!empty($this->classes))$this->viewClass[]=$this->classes;
if( is_object($this->formObj)){
if( JOOBI_FORM_HASRETURNID){
if(!isset($returnId)){
$returnId=WView::getURI();
}$this->formObj->hidden('returnid', base64_encode($returnId));
}
$this->content .=$this->_extraContent;
$this->formObj->addContent($this->content );
$finalDIV=new WDiv($this->formObj->make());
$finalDIV->classes='viewForm clearfix';
if(empty($ViewIDsA[$this->formObj->id] )){
$ViewIDsA[$this->formObj->id]=1;
$viewID=$this->formObj->id;
}else{
$countView=$ViewIDsA[$this->formObj->id] + 1;
$ViewIDsA[$this->formObj->id]=$countView;
$viewID=$this->formObj->id.'_'.$countView;
}
if(!empty($this->formObj->id )){
$viewID=WView::generateID('view', substr($viewID, 3 ));
WPage::addJSScript( "jCore.dv='#" . $viewID . "';" . WGet::$rLine );
$finalDIV->id=$viewID;
}
$this->content=$finalDIV->make();
if(( WRoles::isNotAdmin('manager') && WPref::load('PLIBRARY_NODE_WIZARDFE'))
|| ( WRoles::isAdmin('manager') && WPref::load('PLIBRARY_NODE_WIZARD'))
){
$tourO=WObject::get('output.tour');
$tourO->createTourFromView($this );
}
if(isset($this->bctrail) && $this->bctrail>1){
if(!empty($breadcrumb))$this->content .=$breadcrumb;
}
}
if($this->nestedView===false && $this->type !=204){$legendHTML=WPage::renderBluePrint('legend','createLegend');
if(!empty($legendHTML )){
$pageT->setContent('legend',$legendHTML );
}
WPage::declareJS();
$this->content .=WView::popupMemory('', true);
}
return $this->content;
}
public function makeMenu($renderMenu=true){
$ajaxNeedMenu=WGlobals::get('ajaxNeedMenu','','global');
if(!empty($ajaxNeedMenu)){
return '';
}
$menuController=new stdClass;
$menuController->wid=$this->wid;
$menuController->sid=$this->sid;
$menuController->controller=$this->controller;
$menuLayout=new stdClass;
$menuLayout->yid=$this->yid;
$menuLayout->sid=$this->sid;
$menuLayout->subtype=$this->subtype;
$menuLayout->nestedView=$this->nestedView;
$menuLayout->menu=$this->menu;
$menuLayout->firstFormName=$this->firstFormName;
if(isset($this->name))$menuLayout->name=$this->name;
$menuLayout->form=$this->form;
$menuLayout->viewType=$this->type;
$menuLayout->frontend=$this->frontend;
$menuLayout->menuSize=(!empty($this->msize)?$this->msize : 16 ); $menuLayout->namekey=$this->namekey;
if(isset($this->phpfile) && JOOBI_FRAMEWORK_TYPE=='mobile')$menuLayout->phpfile=$this->phpfile;
if($this->type > 51 && $this->type < 150)$menuLayout->typeForm=true;
$menuLayout->wid=$this->wid;
$menuLayout->type=204;
$menuLayout->typeOriginal=$this->type;
$menuLayout->isVIewMenu=true;
$menuController->layout=&$menuLayout;
$params=new stdClass;
$params->sid=$this->sid;
$params->elementsData=$this->_data;
if(isset($this->superOverride))$params->superOverride=$this->superOverride;
if(isset($this->superolid))$params->superolid=$this->superolid;
if(isset($this->rolid))$params->originrolid=$this->rolid;
if(isset($this->namekey))$params->namekey=$this->namekey;$viewMenuV=WView::get($this->yid, 'html',$params, $menuController );
if(empty($viewMenuV)){
$message=WMessage::get();
$message->codeE('WView::get returned null in file '.__FILE__.'  line about 1215' , array(), 3 );
return false;
}
if(!empty($this->menu2Remove))$viewMenuV->removeElements($this->menu2Remove );
if( JOOBI_FRAMEWORK_TYPE=='mobile'){
if(!$renderMenu && (($this->type >=200 && $this->type < 240 ) || 2==$this->type )){return $viewMenuV->getOnlyElements();
}
}
$menuHTML=$viewMenuV->make();
return $menuHTML;
}
function getFieldsRelation($ids,$prefix=''){
$sql=WTable::get('dataset' , $prefix.'foreign','fkid', null, 'library');
$sql->select('map');
$sql->select('sid');
$sql->select('ref_sid');
$sql->whereIn('ref_sid',$ids );
$sql->whereIn('sid',$ids );
return $sql->load('ol');
}
function lisToObject($dValues){
if(!empty($dValues)){
foreach($dValues as $dValue){
$property=$dValue->map;
$this->_data->$property=$dValue->initial;
}}}
public static function getPngCss($image){
$browser=WPage::browser();
if(isset($browser->name) && $browser->name=='msie' && version_compare($browser->version, "7.0", "<")){
return 'filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, sizingMethod=crop src=\''.$image.'\');background-image: none;';
}
return 'background-image: url(\''.$image.'\'); ';
}
function addProperties($data,$notUsed1=null,$notUsed2=null,$notUsed3=null){
if( is_object($data) && !empty($data)){
$keys=get_object_vars($data );
foreach($keys as $property=> $value){
if(isset($data->$property))$this->$property=$value;
}
}elseif(is_array($data) && !empty($data)){
foreach($data as $key=> $value){
$this->$key=$value;
}
}
}
function addData($data){
$this->_data=$data;}
function params2View($params){
if(empty($params)) return;
foreach($params as $key=> $value){
$layout=explode('_',$key );
if($layout[0]==$this->namekey){
}
}
}
public static function getLegend($image='',$text='',$group='standard',$order=99,$class=''){
$data=new stdClass;
$data->image=$image;
$data->text=$text;
$data->group=$group;
$data->order=$order;
$data->class=$class;
return WPage::renderBluePrint('legend',$data );
}
public static function checkTerms($count=1,$text='',$className='numCkeched'){
if(empty($text))$text=WText::t('1417893375KBQT');
$js="jCore.termCtn='$count';" . WGet::$rLine;
$js .="jCore.termMsg='$text';" . WGet::$rLine;
WPage::addJSScript($js );
return 'termsChecked(this.checked);';
}
public static function getDefaultTheme(){
if( method_exists('WPage','cmsDefaultTheme'))$themeWID=WPage::cmsDefaultTheme();
else $themeWID='joomla32';
if( IS_ADMIN){
$themeType=2;
}else{
$outputSpaceC=WClass::get('output.space');
$spaceO=$outputSpaceC->findSpace();
$themeType=$spaceO->themeType;
if( 3==$themeType && ! IS_ADMIN & 'wordpress'==JOOBI_FRAMEWORK_TYPE)$themeType=1;
}
$pageTheme=WPage::theme();
$folder=$pageTheme->getFolder($themeType );
 return $folder;
}
public static function definePath($theme=''){
static $themeFolder=null;
if(!defined('JOOBI_URL_THEME_JOOBI') || ! defined('JOOBI_DS_THEME_JOOBI')){
if( defined('JOOBI_INSTALLING')){
APIApplication::installThemePath();
return;
}
if(!empty($theme) && !IS_ADMIN && $theme !='site'){
$themeFolder=$theme;
$pageT2=WPage::theme('main');
$pageT2->setFolder($themeFolder );
}else{
$themeFolder=WView::getDefaultTheme();
}
if( defined('JOOBI_URL_THEME_JOOBI')) return $themeFolder;
if( IS_ADMIN){
define('JOOBI_URL_THEME_JOOBI', JOOBI_SITE_PATH . JOOBI_FOLDER.'/user/theme/admin/'.$themeFolder.'/');define('JOOBI_DS_THEME_JOOBI', JOOBI_DS_USER.'theme'.DS.'admin'.DS.$themeFolder . DS );}else{
$outputSpaceC=WClass::get('output.space');
$spaceO=$outputSpaceC->findSpace();
$themeType=$spaceO->themeTheme;
define('JOOBI_URL_THEME_JOOBI', JOOBI_SITE_PATH . JOOBI_FOLDER.'/user/theme/'.$themeType.'/'.$themeFolder.'/');define('JOOBI_DS_THEME_JOOBI', JOOBI_DS_USER.'theme'.DS.$themeType.DS.$themeFolder . DS );}
define('JOOBI_URL_JOOBI_IMAGES', JOOBI_URL_THEME_JOOBI .  'images/');
}
}
public static function themeIsOverWritten(){
if(!defined('JOOBI_DS_THEME_JOOBI')) return false;
static $hasOverwrite=null;
if(!isset($hasOverwrite)){
if( defined('JOOBI_SUPPORTED_TEMPLATE') && JOOBI_SUPPORTED_TEMPLATE){
$hasOverwrite=true;
}else{
$overwritePath=WPage::getTemplate('path').DS.'joobi';
$hasOverwrite=( file_exists($overwritePath)?true : false);
}}
return $hasOverwrite;
}
public static function retreiveOneValue($data,$columnName,$modelName=null,$mapList=null){
static $modelID=array();
if(!empty($modelName)){
if(!is_numeric($modelName)){
if(!isset($modelID[$modelName])){
$modelID[$modelName]=WModel::get($modelName, 'sid');
}$sid=$modelID[$modelName];
}else{
$sid=$modelName;
}$dynmap=$columnName.'_'.$sid;
return (isset($data->$dynmap)?$data->$dynmap : '');
}
if(empty($mapList)){
static $mapList=array();
if(!empty($data)){
foreach($data as $oneData=> $other){
$pos=strrpos($oneData, '_');
if(empty($pos)){
$searchedMap=$oneData;
}else{
$searchedMap=substr($oneData, 0, $pos );
}if(!isset($mapList[$searchedMap]))$mapList[$searchedMap]=$oneData;
}}}
if(isset($mapList[$columnName])){
$ltypeMap=$mapList[$columnName];
}else{
return '';
}
if( is_object($data)){
return (isset($data->$ltypeMap)?$data->$ltypeMap : null);
}elseif(is_array($data)){
return (isset($data[$ltypeMap])?$data[$ltypeMap] : null);
}else{
return null;
}
}
protected static function getSQLparent($yid,$table,$id,$params=null,$name=true,$description=false,$message=false,$textLink=false){
$caching=WPref::load('PLIBRARY_NODE_CACHING');
$langToUse=WUser::get('lgid');
$nameCache='Zx'. $table . $yid .'-'.$langToUse;
$getModel=true;
if($caching > 5){
$cache=WCache::get();
$result=$cache->get($nameCache, 'Views');
if(!empty($result))$getModel=false;
}
$params->superOverride=null;
if($getModel){
$elementsM=WModel::get('library.'.$table, 'object');
if($name || $description){
$elementsM->makeLJ('library.'.$table.'trans',$elementsM->getPK());
$lgid=WUser::get('lgid');
$elementsM->whereLanguage( 1, $lgid );
}
if($name)$elementsM->select('name', 1 );
if($description)$elementsM->select('description', 1 );
if($message)$elementsM->select('message', 1 );
if($textLink)$elementsM->select('textlink', 1 );
$elementsM->select($id );
$elementsM->select($params->select );
$elementsM->select('rolid');
$extensionID=WGlobals::get('extensionID', null, 'global');
$level=WGlobals::getCandy($extensionID );
if(empty($level))$level=WGlobals::getCandy();
if(!in_array($table, array('viewforms','viewmenus')))$elementsM->where('level','<=',$level );
$elementsM->whereE('yid',$yid );
$elementsM->whereE('publish' , true);
if(isset($params->area)){
$elementsM->orderBy('area','DESC');$elementsM->orderBy('hidden','DESC');
$elementsM->orderBy('ordering');
}else{
$paramOrdering=$elementsM->getParam('ordrg','');
if(!empty($paramOrdering)){
if($table!='viewmenus')$elementsM->orderBy('hidden','DESC');
$elementsM->orderBy('ordering','ASC');
}
}
if(isset($params->notIN)){
foreach($params->notIN as $column=> $arrayOfValues){
$elementsM->whereIn($column, $arrayOfValues, 0, true);
}}$elementsM->setLimit( 1000 );
$result=$elementsM->load('ol');
if($caching >5){
$cache->set($nameCache, $result, 'Views');
}
}
if(!empty($result)){
$roleC=WRole::get();
$myRoleA=$roleC->getUserRoles();
foreach($result as $myKey=> $oneResult){
if(!in_array($oneResult->rolid, $myRoleA )) unset($result[$myKey] );
}}
return $result;
}
private function _setPageTitle(){
if(empty($this->pageTitle))$this->pageTitle=WGlobals::get('titleheader','','','string');
$realTitlePage='';
if(!empty($this->name) && ( IS_ADMIN || PLIBRARY_NODE_PAGETITLE )){
$realTitlePage=$this->name;
if(!empty($this->pageTitle))$realTitlePage .=' - '.$this->pageTitle;
}else{
$realTitlePage=$this->pageTitle;
}
if(!empty($realTitlePage) && empty($this->pagetitle )) APIPage::setTitle($realTitlePage );
}
private function _removeOrfenChildrenElement(&$elements,&$toRemoveIDs,$PKname){
$stillSome=false;
if(!empty($elements)){
foreach($elements as $key=> $oneElement){
if( in_array($oneElement->parent, $toRemoveIDs )){
$toRemoveIDs[]=$oneElement->$PKname;
unset($elements[$key] );
$stillSome=true;
}}if($stillSome)$this->_removeOrfenChildrenElement($elements, $toRemoveIDs, $PKname );
}
}
}
class WViews {
public static $WMessageLog=array();
public function getSQL($langedID,$showMessage=true,$params=null){
$controller=$params->controller;
$superOverride=$params->superOverride;
$explodedLangedIDA=explode('-',$langedID );
$yid=$explodedLangedIDA[0];
$lgid=$explodedLangedIDA[1];
$reload=WViews::checkExistFileForInserting($yid );
if(empty($yid)){
WMessage::log('-ERROR yid==false==--'.$yid ,  'warning_view');
WMessage::log( self::$WMessageLog, 'warning_view');
return false;
}
$tempdata=WViews::getSQLFromDB($yid, $lgid, $showMessage );
if(empty($tempdata)) return $tempdata;
if(!empty($tempdata->reload)){
$folder=WExtension::get($tempdata->wid, 'folder', null, null, false);
$tempYid='#'.$folder.'#'.$tempdata->namekey;
$reload=WViews::checkExistFileForInserting($tempYid );
$tempdata=WViews::getSQLFromDB($yid, $lgid, $showMessage );
if(!empty($tempdata)){
$controllerM=WModel::get('library.view','object');
$controllerM->whereE('yid',$tempdata->yid );
$controllerM->setVal('reload', 0 );
$controllerM->update();
}
}
$tempdata->id=$tempdata->yid;
return $tempdata;
}
private static function getSQLFromDB($yid,$lgid,$showMessage=true){
$viewM=WModel::get('library.view','object');
if( is_numeric($yid)){
$viewM->whereE('yid',$yid );
}else{
$viewM->whereE('namekey',$yid );
}
$viewM->select('*');
$viewM->makeLJ('library.viewtrans','yid');
$viewM->whereLanguage( 1, $lgid );
$viewM->select( array('name','wname','wdescription'), 1 );
$viewM->whereE('publish', 1 );
if(isset($controller->wid)){
$namekey=WExtension::get($controller->wid, 'folder');
}else{
$namekey=WGlobals::get('extensionKEY', null, 'global');
if(empty($namekey)){
$extensionID=WGlobals::get('extensionID', null, 'global');
$namekey=(!empty($extensionID)?WExtension::get($extensionID, 'namekey') : '');}}
$viewM->where('level','<=' , WGlobals::getCandy());
$tempdata=$viewM->load('o');
if(empty($tempdata)){
if($showMessage){
$message=WMessage::get();
$message->codeE('The view does not exist OR you do not have proper access level, yid: '.$yid , array(), 3 );
}
WMessage::log('The view does not exist OR you do not have proper access level, yid: '.$yid, 'error_view');
WMessage::log( self::$WMessageLog, 'error_view');
return null;
}
return $tempdata;
}
public static function checkExistFileForInserting(&$yid,$folder='view'){
self::$WMessageLog=array();
self::$WMessageLog['yid']=$yid;
if( is_numeric($yid)){
return false;
}elseif( is_string($yid)){
if( substr($yid, 0, 1 ) !='#')
{
$yidOld=$yid;
$yidArr=explode('_',$yid);
$yid="#" . $yidArr[0] . "#" . $yidOld;
}}else{
return false;
}
self::$WMessageLog['yid 2']=$yid;
$viewNamekeyA=explode('#',$yid );
static $_cache=array();
if(isset($viewNamekeyA[2]) && ! empty($viewNamekeyA[2])){
$_cache[]=$viewNamekeyA[2];
}
if(!isset($viewNamekeyA[2]) && in_array($viewNamekeyA[1], $_cache)){
$yid=$viewNamekeyA[1];
self::$WMessageLog['yid 3 ']=$yid;
return false;
}else{
 $yid=$viewNamekeyA[2];
}
$file=JOOBI_DS_NODE . $viewNamekeyA[1].DS.'data'.DS.$folder.DS.$viewNamekeyA[2].'.cca';
if(!file_exists($file)){
return false;
}
static $readingFileC=null;
if(!isset($readingFileC)){
$readingFileC=WClass::get('library.readingfile');
}
$phpObjectFromFile=$readingFileC->createPhpObjectFromFile($file);
if(!empty($phpObjectFromFile)){
$insertIntoDBFromFile=$readingFileC->doInstallationIntoDb($phpObjectFromFile, $folder);
if($insertIntoDBFromFile  && Library_Readingfile_class::$isFinishSuccessfull){
  $readingFileC->insertIntoPopulateTable();
$statusDel=$readingFileC->createFlagFile($file );
if(!$statusDel){
}}else{
return false;
}
}
return true;
}
}