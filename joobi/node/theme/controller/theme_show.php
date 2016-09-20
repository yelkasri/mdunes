<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_show_controller extends WController {
function show(){
$eid=WGlobals::getEID();
$themeM=WModel::get('theme');
$themeM->whereE('tmid',$eid );
$core=$themeM->load('lr','core');
if($core){
$message=WMessage::get();
$message->userW('1329173545MEGR');
}
return true;
}}