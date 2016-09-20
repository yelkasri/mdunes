<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_Translation_listing_lang_view extends Output_Listings_class {
function prepareQuery(){
$lgid=WGlobals::get('lgid');
$sid=0;
if(!empty($lgid)){
if($lgid !=1){
$lang=WLanguage::get($lgid, 'code');
$sid=WModel::getID('translation.'.$lang );
if(!empty($sid)){
WGlobals::setSession('translationSID','sid',$sid );
}
}else{
WGlobals::setSession('translationSID','sid', 0 );
}
}
if(empty($sid))$sid=WGlobals::getSession('translationSID','sid', 0 );
if(!empty($sid)){
$this->sid=$sid;
foreach($this->elements as $key=> $val){
if(!empty($this->elements[$key]->sid))$this->elements[$key]->sid=$sid;
if($this->elements[$key]->map=='nbchars') unset($this->elements[$key]);
}
}
$search=WGlobals::get('search'.$this->yid, null, null, 'string');
if( substr($search, 0, 2 )=='TR'){
WGlobals::set('search'.$this->yid, substr($search, 2 ));
}
return true;
}
function prepareView(){
$lgid=WGlobals::get('lgid');
if( 1 !=$lgid){
$transEN=WModel::get('translation.en');
$totalEN=$transEN->total();
$lang=WLanguage::get($lgid, 'code');
$transOTHER=WModel::get('translation.'.$lang );
if(!empty($transOTHER)){
$totalOTHER=$transOTHER->total();
if($totalOTHER < $totalEN){
$this->userN('1463158760NTEE');
}else{
$this->removeMenus( array('translation_listing_lang_addvocabulary','translation_listing_lang_divider'));
}
}else{
$this->removeMenus( array('translation_listing_lang_addvocabulary','translation_listing_lang_divider'));
}
}else{
$this->removeMenus( array('translation_listing_lang_addvocabulary','translation_listing_lang_divider'));
}
return true;
}
}