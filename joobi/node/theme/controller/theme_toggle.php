<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_toggle_controller extends WController {
function toggle(){
$eid=WGlobals::getEID();
$property=$this->getToggleValue('map');
if($property=='premium'){
$themeC=WClass::get('theme.helper');
$themeC->unPremium($eid );
$themeC->setPremium($eid );
$cache=WCache::get();
$cache->resetCache();
}
parent::toggle();
return true;
}}