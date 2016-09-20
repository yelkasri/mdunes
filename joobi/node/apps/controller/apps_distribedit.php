<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_distribedit_controller extends WController {
function distribedit(){
$editDistrb=WPref::load('PINSTALL_NODE_DISTRIB_EDIT');
$pref=WPref::get('install.node');
if($editDistrb){
$pref->updatePref('distrib_edit', 0 );
}else{
$pref->updatePref('distrib_edit', 1 );
}
WPages::redirect('controller=apps&task=preferences');
return true;
}
}