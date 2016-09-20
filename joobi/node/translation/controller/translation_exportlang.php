<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_exportlang_controller extends WController {
function exportlang(){
$trk=WGlobals::get( JOOBI_VAR_DATA );
$originlgid=$trk['x']['olgid'];
$destinationlgid=$trk['x']['lgid'];
$export=$trk['x']['export'];
$wid=$trk['x']['wid'];
$format=(!empty($trk['x']['format']))?$trk['x']['format'] : 'ini';
$translationExportlangC=WClass::get('translation.exportlang');
$contentExist=$translationExportlangC->getText2Translate($originlgid, $destinationlgid, $wid, $export );
if($contentExist){
if($format=='html'){
return $translationExportlangC->generateHtmlFile();
}else{return $translationExportlangC->generateIniFile();
}}
$message=WMessage::get();
$message->userN('1227580123BQHZ');
return true;
}
}