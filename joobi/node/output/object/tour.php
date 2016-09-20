<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Tour_objectData {
public $target='';
public $placement='bottom';
public $title='';
public $content='';
}
class Output_Tour_object extends WClasses {
static public $hasTour=false;
private $_id='mytour';
private $_fileName='mytour';
private $_FolderName='mytour';
private $_stepHeadA=array();
private $_stepVarA=array();
private $_stepA=array();
private $_onEnd='';
private $_onClose='';
private $_rLine="\r\n";
public function createUserTour($controllerO){
if( self::$hasTour ) return false;
$uid=WUsers::get('uid');
if(empty($uid)) return false;
$isAdmin=( WRoles::isAdmin()?5 : 9 );
$myTour=WGlobals::get('myTour');
$showFinish=true;
if(!empty($myTour)){
$tourO=WModel::getElementData('output.tour',$myTour );
}else{
if( WRoles::isAdmin()){
$tourM=WModel::get('output.tour');
$tourM->rememberQuery(true, 'Model_tour_node');
$wid=WExtension::get( WApplication::getApp(). 'application','wid');
if(empty($wid)) return false;
$tourM->whereE('wid',$wid );
$tourM->whereE('isadmin',$isAdmin );
$tourM->whereE('publish', 1 );
$tourM->orderBy('core','DESC');
$tourO=$tourM->load('o');
$showFinish=true;
}else{
$tourM=WModel::get('output.tour');
$tourM->rememberQuery(true, 'Model_tour_node');
$tourM->whereE('rolid',$controllerO->rolid );
$tourM->whereE('isadmin',$isAdmin );
$tourM->whereE('publish', 1 );
$tourM->orderBy('core','DESC');
$tourO=$tourM->load('o');
}}
if(!empty($tourO)){
$designTourusersM=WModel::get('output.tourusers');
$designTourusersM->whereE('trid',$tourO->trid );
$designTourusersM->whereE('uid', WUsers::get('uid'));
$statusO=$designTourusersM->load('o',array('status','step'));
if(!empty($statusO) && 9==$statusO->status ) return false;
if(empty($statusO )){
$designTourusersM->setVal('trid',$tourO->trid );
$designTourusersM->setVal('uid', WUsers::get('uid'));
$designTourusersM->setVal('status', 3 );
$designTourusersM->insertIgnore();
}
$step=1;
$tourC=WClass::get($tourO->namekey );
if(!empty($tourC)){
if(!$tourC->displayTour($step )) return false;
}
$tourStepM=WModel::get('output.tourstep');
$tourStepM->makeLJ('output.toursteptrans','trstid');
$tourStepM->whereLanguage();
$tourStepM->rememberQuery(true, 'Model_tour_node');
$tourStepM->whereE('trid',$tourO->trid );
$tourStepM->whereE('publish', 1 );
$tourStepM->orderBy('ordering','ASC');
$tourStepA=$tourStepM->load('ol');
if(empty($tourStepA)) return false;
self::$hasTour=true;
$previousStep='';
$j=0;
$lastValidStep=0;
foreach($tourStepA as $key=> $oneStep){
if(!empty($tourC)){
if(!$tourC->renderTourStep($oneStep )){
if(!empty($oneStep->onnext)){
$this->_stepVarA[$lastValidStep]->onNext='function(){window.location="'.WPages::link($oneStep->onnext ). '";}';
}continue;
}}
$lastValidStep=$j;
$tourID=WView::generateID('tour',$oneStep->trstid );
$obj=new stdClass;
$obj->id=$tourID;
if(!empty($oneStep->target)){
$fid=WModel::getElementData('library.viewforms',$oneStep->target, 'fid');
$obj->tourID=WView::generateID('form',$fid );
}
if(empty($obj->tourID))$obj->tourID='toolbarBox';
$obj->title=$oneStep->name;
$obj->content=$oneStep->description;
$obj->placement='bottom';
$obj->width='500px';
if(!empty($oneStep->onnext)){
$previousStep=$oneStep->onnext;
$obj->onNext='function(){window.location="'.WPages::link($oneStep->onnext ). '";}';
}else{
$previousStep='';
}
if(!empty($previousStep)){
$obj->onPrev='function(){window.location="'.WPages::link($previousStep ). '";}';
}
if($showFinish){
$obj->showCTAButton=true;
$obj->ctaLabel=WText::t('1206961877KAKB');
$obj->onCTA='function(){'.JOOBI_JS_APP_NAME . ".tr('" . WPages::linkAjax('controller=output&task=tour&tract=finish&trid='.$tourO->trid )  . "');}";
}
$this->_stepVarA[]=$obj;
$j++;
}
$this->_id=$tourO->namekey;
if($this->_fileAlreadyExist()) return $this->_setupTourInPage();
$this->_onEnd='function(){'.JOOBI_JS_APP_NAME . ".tr('" . WPages::linkAjax('controller=output&task=tour&tract=end&trid='. $tourO->trid )  . "');}";
$this->_processStep();
$this->_createTour();
$this->_setupTourInPage();
}
return false;
}
public function createTourFromView($view){
if( self::$hasTour ) return false;
if(empty($view->yid)) return false;
if( WRoles::isNotAdmin('manager') && ! WPref::load('PLIBRARY_NODE_WIZARDFE')) return false;
if( WRoles::isAdmin('manager') && ! WPref::load('PLIBRARY_NODE_WIZARD')) return false;
$this->_id=$view->namekey;
if( WExtension::exist('design.node')){
$designTourusersM=WModel::get('output.tourusers');
$designTourusersM->whereE('yid',$view->yid );
$designTourusersM->whereE('uid', WUsers::get('uid'));
$statusO=$designTourusersM->load('o',array('status','step'));
if(!empty($statusO) && 9==$statusO->status ) return false;
if(empty($statusO )){
$designTourusersM->setVal('yid',$view->yid );
$designTourusersM->setVal('uid', WUsers::get('uid'));
$designTourusersM->setVal('status', 3 );
$designTourusersM->insertIgnore();
}}
if($this->_fileAlreadyExist()) return $this->_setupTourInPage();
$this->_processView($view );
$this->_processElements($view->elements, $view->type, $view->yid );
$this->_createTour();
$this->_setupTourInPage();
}
private function _processStep(){
if(empty($this->_stepVarA)) return false;
foreach($this->_stepVarA as $key=> $varStep){
if(empty($varStep->tourID)){
continue;
}
$stepO=WObject::newObject('output.tour');
$stepO->target=$varStep->tourID;
$stepO->title=$varStep->title;
$stepO->content=$varStep->content;
if(!empty($varStep->placement))$stepO->placement=$varStep->placement;
if(!empty($varStep->width ))$stepO->width=$varStep->width;
if(!empty($varStep->tabID )){
$currentTab=$varStep->tabID;
$nextTab=(isset($this->_stepVarA[$key+1]) && isset($this->_stepVarA[$key+1]->tabID)?$this->_stepVarA[$key+1]->tabID : 0 );
$previousTab=(isset($this->_stepVarA[$key-1]) && isset($this->_stepVarA[$key-1]->tabID)?$this->_stepVarA[$key-1]->tabID : 0 );
if(!empty($varStep->onNext)){
$stepO->onNext=$varStep->onNext;
} if(!empty($currentTab)){
if(!empty($nextTab) && $currentTab !=$nextTab){
$stepO->onNext="function(){jQuery('#" . WView::retreiveID('fid',$currentTab ). "').removeClass('active in');jQuery('#" . WView::retreiveID('fid',$nextTab ). "').addClass('active in');}";
}elseif(!empty($previousTab) && $currentTab !=$previousTab){
$stepO->onPrev="function(){jQuery('#" . WView::retreiveID('fid',$currentTab ). "').removeClass('active in');jQuery('#" . WView::retreiveID('fid',$previousTab ). "').addClass('active in');}";
}}
}else{
if(!empty($varStep->onNext)){
$stepO->onNext=$varStep->onNext;
}
if(!empty($varStep->onPrev)){
$stepO->onPrev=$varStep->onPrev;
}
if(!empty($varStep->onClose)){
$stepO->onClose=$varStep->onClose;
}
if(!empty($varStep->onEnd)){
$stepO->onEnd=$varStep->onEnd;
}
}
if(!empty($varStep->showCTAButton)){
$stepO->showCTAButton=$varStep->showCTAButton;
}
if(!empty($varStep->ctaLabel)){
$stepO->ctaLabel=$varStep->ctaLabel;
}
if(!empty($varStep->onCTA)){
$stepO->onCTA=$varStep->onCTA;
}
$this->_stepA[]=$stepO;
}
}
private function _createTour(){
if(empty($this->_stepA)) return false;
$this->_id=str_replace('.','_',  WGlobals::filter($this->_id, 'word'));
$tour='var tour={'.$this->_rLine.'id:"'.$this->_id.'",'.$this->_rLine;
$lgid=WUser::get('lgid');
$code=WLanguage::get($lgid, 'code');
if('en' !=$code){
$tour .='i18n:{'.$this->_rLine;
$tour .='nextBtn:"'.WText::t('1206732366OVME'). '",'.$this->_rLine;
$tour .='prevBtn:"'.WText::t('1206961882TDHA'). '",'.$this->_rLine;
$tour .='doneBtn:"'.WText::t('1242282449PIPC'). '",'.$this->_rLine;
$tour .='skipBtn:"'.WText::t('1446170235KOZQ'). '",'.$this->_rLine;
$tour .='closeTooltip:"'.WText::t('1228820287MBVC'). '"'.$this->_rLine;
$tour .='},'.$this->_rLine;
}
$tour .='steps:['.$this->_rLine;
$firstStep=true;
foreach($this->_stepA as $step){
if(!$firstStep)$tour .=',';
$firstStep=false;
$tour .='{'.$this->_rLine;
$first=true;
$step->title=addslashes($step->title );
$step->title=str_replace( array( "\n", "\r" ), '',$step->title );
$step->content=str_replace( array( "\n", "\r" ), '', addslashes($step->content ));
foreach($step as $prop=> $val){
if(!$first)$tour .=',';
$first=false;
if( in_array($prop, array('onNext','onPrev','onClose','onEnd','onCTA'))){
$tour .=$prop.':'.$val . $this->_rLine;
}else{
$tour .=$prop.':"'.$val.'"'.$this->_rLine;
}
}
$tour .='}'.$this->_rLine;
}
$tour .=']'.$this->_rLine;
if(!empty($this->_onClose)){
$tour .=',onClose:'.$this->_onClose . $this->_rLine;
}if(!empty($this->_onEnd)){
$tour .=',onEnd:'.$this->_onEnd . $this->_rLine;
}
$tour .='}'.$this->_rLine;
$tour .='hopscotch.startTour(tour);';
$fileS=WGet::file();
if(!$fileS->write($this->_FolderName . $this->_fileName, $tour )) return false;
return true;
}
private function _setupTourInPage(){
WPages::addCSSFile('css/hopscotch.css');
WPages::addJSFile('js/hopscotch.js');
$URL=substr($this->_FolderName, strlen( JOOBI_DS_ROOT ));
$URL=str_replace( DS, '/',$URL );
WPages::addJSFile( JOOBI_SITE . $URL . $this->_fileName, 'none');
self::$hasTour=true;
}
private function _processView($view){
if(empty($view->wname)) return false;
$direction=( APIPage::isRTL()?'left' : 'right');
$description=$view->wdescription;
if( strpos($description, '{widget:')){
$tag=WClass::get('output.process');
$tag->replaceTags($description, WUser::get('data'));
}
$obj=new stdClass;
$obj->tourID='toolbarBox';
$obj->title=$view->wname;
$obj->content=$description;
$obj->placement='bottom';
$obj->width='500px';
$obj->showCTAButton=true;
$obj->ctaLabel=WText::t('1206961877KAKB');
$obj->onCTA='function(){'.JOOBI_JS_APP_NAME . ".tr('" . WPages::linkAjax('controller=output&task=tour&tract=end&yid='.$view->yid )  . "');}";
$this->_stepHeadA[]=$obj;
}
private function _processElements($elementsA,$type=0,$yid=0){
if(empty($elementsA)) return false;
$direction=( APIPage::isRTL()?'left' : 'right');
$location=($type==2?'' : $direction );
$getTabs=false;
$skipA=array();
switch($type){
case 1:
case 61:
case 51:
case 151:
$field='fid';
$idType='form';
$skipA=array('joomla.multisites');
$getTabs=true;
break;
case 2:
$field='lid';
$idType='listing';
$getTabs=false;
break;
case 99:
$field='mid';
$idType='menu';
return false;
break;
default:
return false;
break;
}
$currentTab=0;
$firstTab=0;
$allTabsA=array();
foreach($elementsA as $element){
$prefix='';
if($getTabs && $element->type=='output.tab'){
$currentTab=$element->fid;
if(empty($firstTab))$firstTab=$currentTab;
else $allTabsA[]=$currentTab;
continue;
}elseif(!empty($element->hidden )
|| empty($element->description )
|| !empty($element->notitle )
|| !empty($element->spantit )
|| in_array($element->type, $skipA )
){
continue;
}elseif( in_array($element->type, array('output.select','output.selectpicklist','output.textarea','main.transarea','output.file','output.media','output.image'))
|| !empty($element->readonly )
){
$prefix='c';
}
$tourID=$prefix . WView::retreiveID($idType, $element->$field );
$obj=new stdClass;
$obj->tourID=$tourID;
$obj->title=$element->name;
$obj->content=$element->description;
$obj->placement=$location;
$obj->id=$element->$field;
if(!empty($currentTab))$obj->tabID=$currentTab;
$obj->showCTAButton=true;
$obj->ctaLabel=WText::t('1206961877KAKB');
$obj->onCTA='function(){'.JOOBI_JS_APP_NAME . ".tr('" . WPages::linkAjax('controller=output&task=tour&tract=end&yid='.$yid )  . "');}";
$this->_stepVarA[]=$obj;
}
if($getTabs && !empty($firstTab) && !empty($this->_stepHeadA)){
$this->_stepHeadA[0]->onNext="function(){";
foreach($allTabsA as $mTab)$this->_stepHeadA[0]->onNext .="jQuery('#" . WView::retreiveID('fid',$mTab ). "').removeClass('active in');";
$this->_stepHeadA[0]->onNext .="jQuery('#" . WView::retreiveID('fid',$firstTab ). "').addClass('active in');";
$this->_stepHeadA[0]->onNext .="}";
$this->_stepHeadA[0]->tabID=$firstTab;
}
$this->_stepVarA=array_merge($this->_stepHeadA, $this->_stepVarA );
if( WExtension::exist('design.node')){
$this->_onEnd='function(){'.JOOBI_JS_APP_NAME . ".tr('" . WPages::linkAjax('controller=output&task=tour&tract=end&yid='.$yid )  . "');}";
}
$this->_processStep();
}
private function _processMenus($menus){
}
private function _fileAlreadyExist(){
$this->_fileName='tr_'.WUser::get('uid'). '_'.WUser::get('lgid'). '_'.$this->_id.'.js';
$this->_FolderName=WApplication::cacheFolder().DS.'js'.DS.'u'.WUser::get('uid'). '_'.WUser::get('lgid'). DS;
$fileS=WGet::file();
if($fileS->exist($this->_FolderName . $this->_fileName )){
return true;
}
return false;
}
}