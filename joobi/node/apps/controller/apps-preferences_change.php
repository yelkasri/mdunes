<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_preferences_change_controller extends WController {
function change(){
$namekey=WGlobals::get('name');
$node=WGlobals::get('node');
$value=WGlobals::get('value');
$message=WMessage::get();
if(empty($node)){
$message->userE('1338581054ANGS');
return false;
}
if(empty($namekey)){
$message->userE('1338581054ANGT');
return false;
}
$myPref=WPref::get($node );
if(!empty($myPref)){
$myPref->updatePref($namekey, $value);
$message->userS('1338581054ANGU');
}
WPages::redirect('previous');
return true;
}}