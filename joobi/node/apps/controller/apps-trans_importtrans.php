<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_trans_importtrans_controller extends WController {
function importtrans(){
$appsSID=WModel::getID('apps');
$wid=self::getFormValue('wid','apps');
$trlgid=self::getFormValue('trlgid');
if(empty($trlgid) || empty($wid)){
$this->codeE('Error in the form.');
WPage::redirect('previous');
}
$fileA=$this->secureFileUpload('ini','importfile');
if(empty($fileA )){
$this->userE('1446691217SHNF');
WPage::redirect('previous');
}
$fileLocation=$fileA['location'];
$fileClass=WGet::file();
$content=$fileClass->read($fileLocation );
$translationExportlangC=WClass::get('translation.exportlang');
$contentTranslations=$translationExportlangC->generateManualContent($content, $wid, $trlgid );
$translationImportlangC=WClass::get('translation.importlang');
$translationImportlangC->importDictionary($contentTranslations, true);
$translationProcessC=WClass::get('translation.process');
$translationProcessC->setDontForceInsert(false);
$translationProcessC->handleMessage=false;
$translationProcessC->triggerPopulate();
$this->userS('1357567981LCEH');
$extensionHelperC=WCache::get();
$extensionHelperC->resetCache();
WPages::redirect('controller=apps-trans');
return true;
}
}