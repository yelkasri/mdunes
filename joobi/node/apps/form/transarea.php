<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('form.textarea');
class Apps_CoreTransarea_form extends WForm_textarea {
function create(){
$wid=WGlobals::getEID();
$lgid=WGlobals::get('trlgid');
$translationExportlangC=WClass::get('translation.exportlang');
$translationExportlangC->getText2Translate($lgid, $lgid, $wid, false);
$this->value=$translationExportlangC->createLanguageString();
return parent::create();
}}