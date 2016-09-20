<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_duplicate_controller extends WController {
function duplicate(){
$folder=self::getFormValue('foldername');$folder=WGlobals::filter($folder, 'path');
$tmid=WGlobals::getEID();
$themeM=WModel::get('theme');
if(empty($folder)){
$this->userE('1298350435GKOB');
$this->setView('theme_clone');
return true;
}
$systemFolderC=WGet::folder();
$themeC=WClass::get('theme.helper');
$type=$themeC->getCol($tmid,'type');
$destfolder=$themeC->destfolder($type );
if($systemFolderC->exist( JOOBI_DS_THEME . $destfolder.DS.$folder)){
$this->userE('1298350435GKOC',array('$folder'=>$folder));
$this->setView('theme_clone');
return true;
}
parent::copyall();
$cache=WCache::get();
$cache->resetCache('Theme');
$inListing=WGlobals::get('listing', 0, 'session');
WGlobals::set('listing','','session');
$neweid=$this->_model->tmid;
if(empty($inListing)) WPages::redirect('controller=theme&task=show&eid='.$neweid.'&type='.$type );
return true;
}
}