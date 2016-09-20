<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Mlinks_class extends WView {
var $_task=null;
var $_layout=null;
private $_type=0;private $menuFormID='';
function create(){
if(empty($this->elements)){
$this->content='';
return '';
}
if( WRoles::isNotAdmin('manager')){
$subExit=WExtension::exist('subscription.node');
if($subExit){
$subscriptionDesignrestrictionC=WClass::get('subscription.designrestriction');
if( method_exists($subscriptionDesignrestrictionC, 'checkViewRestriction')){
$restrictedElementsA=$subscriptionDesignrestrictionC->checkViewRestriction();
if(!empty($restrictedElementsA)){
$this->removeMenus($restrictedElementsA );
}}}}
if(!$this->prepareView()){
return false;
}
$this->removeElementsProcess( self::$_removedElementsA, true, 'output_mlinks_class');
$this->changeElementsProcess();
if(empty($this->elements)){
$this->content='';
return '';
}
$menuO=WPage::newBluePrint('menu');
$level=WGlobals::getCandy();
foreach($this->elements as $oneM){
if( JOOBI_APP_DEVICE_SIZE){
if(!empty($oneM->xsvisible)){
if( JOOBI_APP_DEVICE_SIZE < $oneM->xsvisible ) continue;
}if(!empty($element->xshidden)){
if( JOOBI_APP_DEVICE_SIZE < $oneM->xshidden ) continue;
}}
if( JOOBI_APP_DEVICE_TYPE){
if((!empty($oneM->devicevisible) || !empty($oneM->devicehidden)) &&
 ! WView::checkDevice($oneM->devicevisible, $oneM->devicehidden ))  continue;
}
if(!isset($oneM->level))$oneM->level=0;
if( 115 !=$this->subtype && $oneM->level > $level){
continue;
}
$isPopUp=WGlobals::get('is_popup', false, 'global');
if($isPopUp){
if($oneM->type==60){
$oneM->type=50;
WTools::getParams($oneM );
unset($oneM->popheight );
unset($oneM->popwidth );
$oneM->action='controller='.$oneM->action;
}}
$menuO->elements[]=$oneM;
}
$menuO->subtype=$this->subtype;
$menuO->firstFormName=$this->firstFormName;
$menuO->wid=$this->wid;
$menuO->namekey=$this->namekey;
$menuO->type=$this->type;
if(!empty($this->elementsData))$menuO->elementsData=$this->elementsData;
if(!empty($this->viewType))$menuO->viewType=$this->viewType;
$menuO->nestedView=$this->nestedView;
if(!empty($this->menuSize))$menuO->menuSize=$this->menuSize;
$menuO->formID=(isset($this->formObj->name)?$this->formObj->name : $this->firstFormName );
if(!empty($this->formObj))$menuO->formInfoO=$this->formObj;
if(!empty($this->typeForm))$menuO->typeForm=$this->typeForm;
$this->content=WPage::renderBluePrint('menu',$menuO );
}
function make($notUsed=null,$notUsed2=null){
if($this->_noShow ) return '';
$this->create();
return $this->content;
}
protected function getElements($yid,$params=null){
$extraParams=new stdClass;
$extraParams->select=array('type','action','position','parent','params','level','private','yid','rolid','ordering','namekey','icon','faicon','color','xsvisible','xshidden','devicevisible','devicehidden');
return parent::getSQLparent($yid, 'viewmenus','mid',$extraParams, true, true, true);
}
public function getOnlyElements(){
$this->removeElementsProcess( self::$_removedElementsA, true, 'output_mlinks_class');
$this->changeElementsProcess();
return $this->elements;
}
public static function loadButtonFile(&$button){
$obj=null;
if(!isset($button->type))$button->type='';
if(isset($button->params )){
WTools::getParams($button );
}
if($button->type>0 && $button->type<40){
if($button->type==16){
$type='Help';
}elseif($button->type==17){
if(true || ( WRoles::isNotAdmin('manager') && !WPref::load('PLIBRARY_NODE_WIZARDFE'))){
$notGood=null;
return $notGood;
}
$button->action='wizard';
$button->name=WText::t('1206732391QBUR');
$type='Default';
}else{
$type='Default';
}
}elseif($button->type>39 && $button->type<50)$type='Custom';
elseif($button->type>49 && $button->type<55)$type='Link';
elseif($button->type>54 && $button->type<60)$type='ExtLink';
elseif($button->type>59 && $button->type<65){
$type='Popup';
}elseif($button->type>64 && $button->type<70)$type='Ajax';
elseif($button->type>79 && $button->type<90)$type='Confirm';
elseif($button->type>89 && $button->type<100)$type='Separator';
elseif($button->type==102){
$type='customized';
}elseif($button->type==103){
$type='jscancel';
} else return $obj;
static $uid=null;
static $myEID=null;
if(!isset($uid))$uid=WUser::get('uid');
if(!isset($myEID))$myEID=WGlobals::getEID();
if(!empty($button->private)){
$mainModel=WModel::get($this->sid, 'mainmodel');
$classOwnershipC=WClass::get($mainModel.'.ownership', null, 'class', false);
if(empty($classOwnershipC)){
}else{
if(!$classOwnershipC->isOwner($myEID )) return $obj;
}
if($button->private>0){
}else{
if($myEID==$uid ) return $obj;
}}
if(!empty($button->checknew)){
if(empty($myEID )){
if($button->checknew==1){
return $obj;
}
}else{
if($button->checknew==2){
return $obj;
}}}
$copyDeleteB=in_array($button->action, array('copy','delete','deleteall','copyall'));
if((!empty($button->lslct)) ||
(!empty($button->action) && $copyDeleteB )){
$radioCheckNoNeedListConfirm=WGlobals::get('radioCheckNoNeedListConfirm', false);
if($radioCheckNoNeedListConfirm)$button->lslct=false;
else $button->lslct=true;
if($copyDeleteB)$button->confirm=true;
} else $button->lslct=false;
if('Default'==$type){
$className='WButtons_'.$type;
}else{
$className='WButton_'.$type;
}
if($type !='Default' && !isset($menuFile[strtolower($type)] )){
$menuFile[strtolower($type)]=true;
$oldaction=$button->action;
if(!empty($button->pnamekey) && empty($button->filef)){
$explodePtA=explode('.',$button->pnamekey );
if( count($explodePtA ) > 1){
$button->filef=$explodePtA[1];
$button->currentClassName=ucfirst( WExtension::get( WView::get($button->yid, 'wid'), 'folder')). '_'.ucfirst($explodePtA[1] ) .'_button';
}}
if(!empty($button->filef)){
$mwid=WView::get($button->yid, 'wid');
$nodeName=WExtension::get($mwid, 'folder');
$button->action=$nodeName.'.'.$button->filef;
}
WView::includeElement('button.'.strtolower($type));
$button->action=$oldaction;
}
if(!class_exists($className)){
return $obj;
}
$buttonObject=new $className();
$buttonObject->buttonO=&$button;
if(!empty($button->currentClassName)){
WLoadFile($explodePtA[0].'.button.'.$explodePtA[1] , JOOBI_DS_NODE );
$parentClassName=ucfirst($explodePtA[0] ). '_'.ucfirst($explodePtA[1] ) .'_button';
WView::includeElement($explodePtA[0].'.button.'.$explodePtA[1], null, true, true);
if(!class_exists($button->currentClassName )){
eval('class '.$button->currentClassName.' extends '.$parentClassName.' {}');
}
}
return $buttonObject;
}
}
class WButtons_standard extends WElement {
public $buttonO=null;public $viewInfoO=null; 
public $buttonJS=false;
public $content='';
protected $noJSonButton=false; 
protected $_target=false;
protected $_text='';
protected $_extra='';
protected $_isPopUp=false;
private static $_countButton=0;
public function initialiseMenu(&$viewInfoO){
$this->viewInfoO=&$viewInfoO;
if(isset($this->viewInfoO->subtype)){
if( in_array($this->viewInfoO->subtype, array( 110, 115, 117, 120 )))$this->noJSonButton=true;
elseif( 90==$this->buttonO->type){
$this->noJSonButton=true;
}}
if($this->buttonO->type==9)$this->buttonO->href='javascript:history.back();';
if( strpos($this->buttonO->action, '(') !==false){
$outputLinkC=WClass::get('output.link');
if(!empty($this->button)){
static $cachedMapList=array();
if(!isset($cachedMapList[$this->yid])){
$mapList=array();
foreach($this->button as $keyD=> $oneV){
$pos=strpos($keyD, '_');
$mapKey=substr($keyD, 0, $pos);
if(!empty($mapKey))$mapList[$mapKey]=$keyD;
}}else{
$mapList=$cachedMapList[$this->yid];
}}else{
$mapList=array();
}
$outputLinkC->wid=$this->viewInfoO->wid;
$viewInfo=(!empty($this->viewInfoO->elementsData)?$this->viewInfoO->elementsData : null );
$this->buttonO->action=$outputLinkC->convertLink($this->buttonO->action, $viewInfo, '', null, $mapList, false);
}
static $autosaveDeclared=false;
if($this->buttonO->action=='apply' && ! $autosaveDeclared )  {
$task=WGlobals::get('task'); $autosave=WGlobals::get('autosave');
if($task=='edit' && $autosave){
WPage::renderBluePrint('saveauto',$this->viewInfoO->formID );
$this->buttonO->id='buttonsave';
$autosaveDeclared=true;
}}
if('edit'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
$outputDirectEditC=WClass::get('output.directedit');
$this->buttonO->directEditHTML=$outputDirectEditC->editView('menu',$this->buttonO->yid, $this->buttonO->mid, 'menu');
}elseif('translate'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
$outputDirectEditC=WClass::get('output.directedit');
$this->buttonO->directEditHTML=$outputDirectEditC->translateView('menu',$this->buttonO->yid, $this->buttonO->mid, $this->buttonO->name );
}
}
public function make($data=null,$obj=null){
$status=$this->create();
if($status===false){$this->content='';
return false;
}
if(!$this->noJSonButton && empty($this->buttonO->buttonJS))$this->setButtonJS();
$this->declareCSS();
if(!empty($this->buttonO->href ))$this->link=$this->buttonO->href;
if(empty($this->content))$this->buildButton();
return $this->content;
}
protected function declareCSS(){
}
protected function buildButton(){
}
protected function setButtonJS(){
static $isPoPup=null;
if(empty($this->viewInfoO->formID)) return;
if(!isset($isPoPup))$isPoPup=WGlobals::get('is_popup', false, 'global');
$paramsArray=array();
$controller='';
$action=$this->buttonO->action;
if(!(strpos($action, '.')===false)){
$actions=explode('.',$action);
$action=array_pop($actions);
$controller=implode('.',$actions);
$paramsArray['controller']=$controller;
}
if( WGet::isDebug()){
if($action=='apply'){
$namekey='Button_Apply_'.$this->viewInfoO->formID.'_save';
}else{
self::$_countButton++;
$namekey='Button'.self::$_countButton.'_'.WGlobals::filter($action.'_'.$this->viewInfoO->formID, 'jsnamekey');
}}else{
self::$_countButton++;
if($action=='apply'){
$namekey='A1_'.self::$_countButton++.'_save';
}else{
$namekey='Bt_'.self::$_countButton;
}}
if($action=='save'){
$paramsArray['enterSubmit']=true;
}
if((!empty($action))){
if($action=='wizard'){
$paramsArray['wizard']=true;
$paramsArray['ajxUrl']='controller=output&task=wizard';
$this->buttonO->href=false;
}elseif($action=='help'){
$paramsArray['wizard']=true;
$paramsArray['ajxUrl']='controller=output&task=help';
$this->buttonO->href=false;
}}
if(!empty($this->buttonO->lslct)){
$paramsArray['select']=true;
$paramsArray['action']=$this->buttonO->name;
}
if(!empty($this->buttonO->fullDisable)){
$paramsArray['disableAll']=true;
}
if(!empty($this->buttonO->confirm) || $action=='delete' || $action=='deleteall'){
$paramsArray['confirm']=true;
$paramsArray['action']=$this->buttonO->name;
if(!empty($this->buttonO->message))$paramsArray['conf_'.$this->buttonO->action ]=$this->buttonO->message;
}
$saveAction=strpos($action, 'save');
$this->submission=(($action=='save' || $action=='apply') || ($this->buttonO->type > 200 ) || !($saveAction===false))?true : false;
$this->validation=(!empty($this->buttonO->formvalidation) || $this->submission )?true : false;$scriptjs=WPage::getScript();
if((isset($this->viewInfoO->viewType) && $this->viewInfoO->viewType>50 && $this->viewInfoO->viewType<200 ))$this->typeForm=true;
if(!empty($this->typeForm) ){
if(isset($scriptjs['js']['onThefly']['validation']) && $this->validation){ 
$paramsArray['validation']=true;
}}
$jscript=WPage::getScript();
if(isset($jscript['js'])
&& is_array($jscript['js'])
&& isset($jscript['js']['onDOMready']['phpedit'])
&& is_array($jscript['js']['onDOMready']['phpedit'])){
$paramsArray['phpedit']=true;
}
if(($isPoPup && !empty($this->buttonO->ajax)) || !empty($this->buttonO->pageajax)){
$paramsArray['ajx']=true;
if(!empty($this->buttonO->callback)){
$paramsArray['ajxComplete']=$this->buttonO->callback;
}else{
$paramsArray['ajxComplete']='jext'.$action;
$paramsArray['controller']=WGlobals::get('controller');
}
$paramsArray['ajxUrl']='';
if(!empty($this->buttonO->refresh))$paramsArray['refresh']=true;
elseif(!empty($this->buttonO->popclose))$paramsArray['popclose']=true;
} else if($isPoPup && !empty($this->buttonO->refresh)){$paramsArray['refresh']=true;
}elseif($isPoPup && !empty($this->buttonO->popclose)){ $paramsArray['popclose']=true;
}
if(empty($this->buttonO->notdisable))$paramsArray['disable']=true;
$loadingIcon=( WRoles::isNotAdmin('manager') && !empty($this->viewInfoO->menuSize) && $this->viewInfoO->menuSize=='divfe')?false : 'this';
$supportedAjaxA=array('deleteall','delete','copyall','copy','core','publish');
if( WPref::load('PLIBRARY_NODE_AJAXPAGE') && in_array($action, $supportedAjaxA )){
$paramsO=WObject::newObject('output.jsaction');
$paramsO->form=$this->viewInfoO->formID;
$paramsO->namekey=$namekey;
$paramsO->isButton=true;
$valueA=array();
if(!empty($paramsArray['confirm'])){
$paramsO->confirm=true;
$paramsO->confirmName=$this->buttonO->name;
}
if(!empty($paramsArray['select'])){
$paramsO->select=true;
$paramsO->selectName=$this->buttonO->name;
}
if(!empty($paramsArray['validation'])){
$paramsO->validation=true;
}
if('apply'==$action){
$task=WGlobals::get('task'); if('edit'==$task || 'preference'==$task && WGlobals::getEID()){
$paramsO->autoSave=true;
}
}
$this->buttonO->buttonJS=WPage::jsAction($action, $paramsO, $valueA );
}else{
$this->buttonO->buttonJS='return '.WPage::actionJavaScript($action, $this->viewInfoO->formID, $paramsArray, 'this',$namekey, false, $loadingIcon );
}
}
}