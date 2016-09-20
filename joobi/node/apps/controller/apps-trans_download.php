<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_trans_download_controller extends WController {
function download(){
$wid=WGlobals::getEID();
$lgid=WGlobals::get('trlgid');
$translationExportlangC=WClass::get('translation.exportlang');
$translationExportlangC->getText2Translate($lgid, $lgid, $wid, false);
$text=$translationExportlangC->createLanguageString();
$logFilePath='language_'.WExtension::get($wid, 'name'). '_'.WLanguage::get($lgid, 'code'). '.ini';
@ob_clean();
header( "content-type: text/plain" );
header( "content-disposition: attachment; filename=" . basename($logFilePath). ";" );
header( "content-transfer-encoding: binary" );
header( "content-length: " . strlen($text));
header('Cache-control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header( "Expires: 0" );
echo $text;
exit;
return true;
}
}