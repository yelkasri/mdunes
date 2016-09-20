<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_updateall_controller extends WController {
function updateall(){
$eid=WGlobals::getEID();
$appsInfoC=WCLass::get('apps.info');
$status=$appsInfoC->possibleUpdate($eid );
if(!$status){
$popuplink=WPage::link('controller=apps&task=club');
WPages::redirect($popuplink, false, false);
}
$eid=WExtension::get( JOOBI_MAIN_APP.'.application','wid');
WPages::redirect('controller=apps&task=show&eid='.$eid.'&update=all');
return true;
}
}