<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_lang_addvocabulary_controller extends WController {
function addvocabulary(){
$sid=WGlobals::getSession('translationSID','sid', 0 );
if(!empty($sid))$namekey=WModel::get($sid, 'namekey');
if(!empty($namekey) && 'translation.en' !=$namekey){
$translationEnM=Wmodel::get('translation.en');
$translationEnM->select( array('imac','text'));
$query=$translationEnM->printQ();
$translationOtherM=WModel::get($sid );
$translationOtherM->setIgnore();
$translationOtherM->insertSelect( array('imac','text'), $query );
$expoA=explode('.',$namekey );
$lgid=WLanguage::get( array_pop($expoA ), 'lgid');
WPages::redirect('controller=translation-lang&lgid='.$lgid );
}
WPages::redirect('controller=translation-lang');
return true;
}
}