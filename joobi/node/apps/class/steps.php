<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_Steps_class extends Library_Progress_class {
public $useAjax=true;
public $userMaxExecTime=0;public $userMaxLoop=0;
protected $_stepDuration=1;
protected $_maxIncrement=500;
protected $_showBar=true;
protected $_showProgressStatus=true;
protected $_showError=true;
protected $_showTimeEstimate=true;
protected $_showTimeCompletion=true;
protected $_stepA=array(
 0=> 'initialize',1=> 'downloadPackage'
,2=> 'extraPackage'
,3=> 'moveFiles',4=> 'createTables'
,5=> 'customFunction'
,6=> 'installLanguages'
,7=> 'final'
);
protected $_stepRatioA=array(
'initialize'=> 1
,'downloadPackage'=> 10
,'extraPackage'=> 15
,'moveFiles'=> 20
,'createTables'=> 30
,'customFunction'=> 10
,'installLanguages'=> 10
,'final'=> 4
);
protected $_stepTotalIncrementA=array(
'initialize'=> 1
,'downloadPackage'=> 10
,'extraPackage'=> 10
,'moveFiles'=> 1
,'createTables'=> 10
,'customFunction'=> 10
,'installLanguages'=> 1
,'final'=> 1
);
public function init(){
$this->_firstMessage=WText::t('1427652792LBWK');
$this->_showMessage=WPref::load('PINSTALL_NODE_INSTALLDETAILS');
$this->userMaxExecTime=0;
$this->userMaxLoop=0;
return true;
}
public function runStep(){
$this->setStep('inProgress');
$installProcessC=WClass::get('install.processnew');
if(!class_exists('Install_Node_Install')) require_once( JOOBI_DS_NODE.'install'.DS.'install'.DS.'install.php');
WPref::get('install.node');
WText::load('install.node');
$mess='';
Install_Processnew_class::logMessage('Starting Install for step nb : '.$this->_progressO->stepNb.' step name: '.$this->_progressO->currentStep );
Install_Processnew_class::logMessage('Increment value:'.$this->_progressO->increment );
switch($this->_progressO->currentStep){
case 'initialize':
$installRequirementsC=WClass::get('install.requirements');
$status=$installRequirementsC->checkRequirements();
if(!$status){
$link=WPages::link('controller=install-requirements');
$link='<br><a href="'.$link.'">'.WText::t('1432597449CAIM'). '</a>';
$this->setReturnedAction('WAjxRefresh', WText::t('1432597449CAIN'). $link, 'failed');
$this->completeProcess();
break;
}
$cacheC=WCache::get();
$cacheC->resetCache();
$systemFolderC=WGet::folder();
$systemFolderC->delete( JOOBI_DS_USER.'logs');
Install_Processnew_class::logMessage('Starting Install for step:'.$this->_progressO->currentStep );
Install_Processnew_class::logMessage('Step 1 : Initialization');
if($this->_progressO->increment==1)$this->setReturnedAction('WAjxAppend','<br>'.$this->_progressO->stepNb. '. '.WText::t('1427652792LBWL'), 'append');
$status=$this->_displayMessage($installProcessC->getListOfPackagesStep());
if(!$status){
Install_Processnew_class::logMessage('The function getListOfPackagesStep() return false');
$this->setReturnedAction('WAjxRefresh', WText::t('1429238504IKNA'), 'failed');
$this->completeProcess();
break;
}
$numberPacakges=$installProcessC->getNumberPackage();
Install_Processnew_class::logMessage('Step 1 : Total number of packages: '.$numberPacakges );
if(empty($numberPacakges)){
$this->setReturnedAction('WAjxRefresh', WText::t('1428532729DJHL'), 'html');
$this->completeProcess();
Install_Processnew_class::logMessage('There is no new package to install');
break;
}
$langA=$installProcessC->getLanguages();
if( count($langA ) <=1){
if(empty($langA) || $langA[0]->code=='en'){
$langA=array();
}}
$this->_stepTotalIncrementA['downloadPackage']=$numberPacakges;
$this->_stepTotalIncrementA['extraPackage']=$numberPacakges;
$this->_stepTotalIncrementA['createTables']=$numberPacakges;
$this->_stepTotalIncrementA['customFunction']=$numberPacakges;
$this->_stepTotalIncrementA['installLanguages']=$numberPacakges;
$this->_progressO->maxIncr=$numberPacakges + 2;
if(empty($langA)){
$this->_stepA[6]='final';
unset($this->_stepA[7] );
unset($this->_stepRatioA['installLanguages'] );
unset($this->_stepTotalIncrementA['installLanguages'] );
}
$this->setStep('complete');
break;
case 'downloadPackage':
if($this->_progressO->increment==1)$this->setReturnedAction('WAjxAppend','<br>'.$this->_progressO->stepNb.'. '.WText::t('1427652792LBWM'), 'append');
Install_Processnew_class::logMessage('call getDownloadPackages increment: '.$this->_progressO->increment, 'install');
$this->_displayMessage($installProcessC->getDownloadPackages($this->_progressO->increment ));
if($this->_progressO->increment==$this->_stepTotalIncrementA['downloadPackage']){
$langM=WClass::get('translation.helper');
$langM->updateLanguages();
$systemFolderC=WGet::folder();
$folder=JOOBI_DS_USER.'installfiles';
if($systemFolderC->exist($folder)){
$systemFolderC->delete($folder );
Install_Processnew_class::logMessage('Delete installfiles folder: '.$folder, 'install');
}
$this->setReturnedAction('WAjxRefresh', WText::t('1427652792LBWN'), 'html');
$this->setStep('complete');
}
break;
case 'extraPackage':
if($this->_progressO->increment==1)$this->setReturnedAction('WAjxAppend','<br>'.$this->_progressO->stepNb. '. '.WText::t('1427652792LBWO'), 'append');
Install_Processnew_class::logMessage('-- Start extraPackage','install');
$this->_displayMessage($installProcessC->extractPackages($this->_progressO->increment ));
Install_Processnew_class::logMessage('-- Finish extraPackage','install');
if($this->_progressO->increment==$this->_stepTotalIncrementA['downloadPackage']){
$this->setReturnedAction('WAjxRefresh', WText::t('1427652792LBWP'), 'html');
$this->setStep('complete');
}
break;
case 'moveFiles':
if($this->_progressO->increment==1)$this->setReturnedAction('WAjxAppend','<br>'.$this->_progressO->stepNb. '. '.WText::t('1427652792LBWQ'), 'append');
Install_Processnew_class::logMessage('-- Start moveFiles','install');
$this->_displayMessage($installProcessC->moveFiles($this->_progressO->increment ));
Install_Processnew_class::logMessage('-- Finish moveFiles','install');
$this->setStep('complete');
break;
case 'createTables':
if($this->_progressO->increment==1)$this->setReturnedAction('WAjxAppend','<br>'.$this->_progressO->stepNb. '. '.WText::t('1427652792LBWR'), 'append');
Install_Processnew_class::logMessage('-- Start createTables','install');
$this->_displayMessage($installProcessC->createTables($this->_progressO->increment ));
Install_Processnew_class::logMessage('-- End createTables','install');
if($this->_progressO->increment==$this->_stepTotalIncrementA['createTables']){
$this->setReturnedAction('WAjxRefresh', WText::t('1427652792LBWS'), 'html');
$this->setStep('complete');
}
break;
case 'customFunction':
if($this->_progressO->increment==1)$this->setReturnedAction('WAjxAppend','<br>'.$this->_progressO->stepNb. '. '.WText::t('1427652793DOXK'), 'append');
Install_Processnew_class::logMessage('-- Start customFunction','install');
$this->_displayMessage($installProcessC->customFunction($this->_progressO->increment ));
Install_Processnew_class::logMessage('-- End customFunction','install');
if($this->_progressO->increment==$this->_stepTotalIncrementA['customFunction']){
$this->setReturnedAction('WAjxRefresh', WText::t('1427652793DOXL'), 'html');
$this->setStep('complete');
}
break;
case 'installLanguages':
Install_Processnew_class::logMessage('-- Start installLanguages','install');
$importLangs=Install_Node_install::accessInstallData('get','importLangs');
Install_Processnew_class::logMessage('Language to install: ','install');
Install_Processnew_class::logMessage($importLangs, 'install');
if(empty($importLangs)){
$this->setStep('complete');
Install_Processnew_class::logMessage('No Language to install: ','install');
break;
}
if($this->_progressO->increment==1)$this->setReturnedAction('WAjxAppend','<br>'.$this->_progressO->stepNb. '. '.WText::t('1427652793DOXM'), 'append');
Install_Processnew_class::logMessage('downloadLanguages : increment '.$this->_progressO->increment, 'install');
$this->_displayMessage($installProcessC->installLanguages($this->_progressO->increment ));
Install_Processnew_class::logMessage('-- End installLanguages','install');
if($this->_progressO->increment==$this->_stepTotalIncrementA['installLanguages']){
$this->setReturnedAction('WAjxRefresh', WText::t('1427652793DOXN'), 'html');
$this->setStep('complete');
}break;
case 'final':
Install_Processnew_class::logMessage('-- Start Final','install');
if($this->_progressO->increment==1)$this->setReturnedAction('WAjxAppend','<br>'.$this->_progressO->stepNb. '. '.WText::t('1427652793DOXO'), 'append');
$installProcessC->finalizeInstall();
$installProcessC->clean();
$finalMessage=WText::t('1427652793DOXP');
$namekey=$installProcessC->getLastExtension();
$type=WExtension::get($namekey, 'type');
Install_Processnew_class::logMessage('Last extension: '.$namekey, 'install');
Install_Processnew_class::logMessage('Last extension type: '.$type, 'install');
if($type==1){
$multiple=WGlobals::getSession('installProcess','what','single');
if('single' !=$multiple){
$name=WText::t('1429064415HVOW');
$folder=WExtension::get( JOOBI_MAIN_APP.'.application','folder');
}else{
$name=WText::t('1427833134LQDZ'). ' '.WExtension::get($namekey, 'name');
$folder=WExtension::get($namekey, 'folder');
}
$finalMessage .='<br><br>';
$objButtonO=WPage::newBluePrint('button');
$objButtonO->text=$name;
$objButtonO->type='infoLink';
$objButtonO->link=WPage::routeURL('controller='.$folder, '', false, false, false, $folder );
$objButtonO->color='success';
$html=WPage::renderBluePrint('button',$objButtonO );
Install_Processnew_class::logMessage($html, 'install');
$finalMessage .=$html.'<br>';
}
$this->setReturnedAction('WAjxRefresh',$finalMessage, 'html');
Install_Processnew_class::logMessage('-- End Final','install');
$this->setStep('complete');
break;
default:
$this->setReturnedAction('WAjxRefresh','Step not defined!','failed');
$this->setStep('complete');
break;
}
Install_Processnew_class::logMessage('Step finished : '.$this->_progressO->currentStep );
}
private function _displayMessage($messageA=array()){
if(empty($messageA)) return false;
$status=true;
foreach($messageA as $actionO){
if($actionO->action=='append'){
$this->setReturnedAction('WAjxAppend',$actionO->message, 'append');
}else{
$this->setReturnedAction('WAjxRefresh',$actionO->message, 'html');
}
if(!empty($actionO->status)){
$this->setStep($actionO->status );
if('failed'==$actionO->status)$status=false;
}
}
return $status;
}
}