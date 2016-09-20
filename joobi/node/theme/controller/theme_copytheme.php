<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_copytheme_controller extends WController {
function copytheme(){
$tmid=WGlobals::getEID(true);
$modelId=WModel::get('theme','sid');
$selectedThemes=WGlobals::get('tmid_'.$modelId );
if(!empty($selectedThemes)){
if( sizeof($selectedThemes) > 1)$this->userN('1309502072SWIE');
}
$inListing=WGlobals::get('listing', 0 );
if(!empty($inListing)){
WGlobals::set('listing',$inListing, 'session');
}
$this->setView('theme_clone');
return true;
}
}