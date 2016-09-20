<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_uninstallconfirm_controller extends WController {
function uninstallconfirm(){
$uninstall_choice=WController::getFormValue('uninstall_choice');
$eid=WController::getFormValue('eid');
$appsUninstallC=WClass::get('apps.uninstall');
$appsUninstallC->uninstallApps($eid, $uninstall_choice );
return true;
}}