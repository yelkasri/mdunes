<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_install_controller extends WController {
public function install(){
WPage::addJSLibrary('rootscript');
WPage::addJSFile('js/install.1.1.js');
$lgid=WGlobals::get('lgid', 0 );
if(empty($lgid)) WPages::redirect('controller=translation&task=listing');
$obj=WLanguage::get($lgid, array('code','name','lgid'));
$langM=WModel::get('install.apps');
$langM->whereE('type',1);
$langM->whereE('publish',1);
$applications=$langM->load('ol',array('wid','namekey'));
$apps=array('wid'=>0,'level'=>0);
foreach($applications as $application){
$apps[]=array('namekey'=> $application->namekey );
}
$installWidget=WText::t('1260151224KRNI'). '<br/>'.
'<a href="'.WPage::routeURL('controller=translation&task=listing','smart','default',false,false, JOOBI_MAIN_APP ).'">'.WText::t('1260151166CSBW').'</a>';
WGlobals::setSession('webapps','widgetinstall',$installWidget );
$processC=WClass::get('install.process');
$processC->mode='translation';
$processC->list=array( 0=> $apps );
$processC->updatePref(2);
Install_Node_install::accessInstallData('set','importLangs',  array($obj ));
$link=WPage::routeURL('controller=translation&task=installexec','smart','popup');
$message=WText::t('1260151224KRNJ');
$javascript='jextinstup(\'BIGMSG['.$message.']\',\''.$link.'\',\'\');';
WPage::addJSScript($javascript, 'jquery');
return true;
}}