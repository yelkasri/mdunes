<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Users_node_horizontalmenu_fe_view extends Output_Mlinks_class {
function prepareView(){
if(!WUser::isRegistered()){
$this->changeElements('main_user_horizontalmenu_fe_logout','name', WText::t('1206732411EGQX'));
$this->changeElements('main_user_horizontalmenu_fe_logout','action','users&task=login');
}
if(!in_array( JOOBI_APP_DEVICE_TYPE, array('ph','tb')) && ! WGlobals::getSession('frmwrk','remoteAccess', false)){
$this->removeElements( array('main_user_horizontalmenu_fe_catalog','main_user_horizontalmenu_fe_cart','main_user_horizontalmenu_fe_logout'));
}
return true;
}
}