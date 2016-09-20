<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Joomla_Users_model extends WModel {
function validate() {
	$this->_triggerPlugin('BeforeNew');
	return true;
}
function addExtra() {
	$this->_triggerPlugin('AfterNew');
	return true;
}
function editValidate() {
	$userM = WModel::get('users');
	$this->_triggerPlugin('BeforeEdit');
	return true;
}
function extra() {
	return true;
}
function editExtra() {
	return true;
}
function deleteValidate($eid) {
	$this->_triggerPlugin('BeforeDelete');
	return true;
}
function deleteExtra($eid) {
	$this->_triggerPlugin('AfterDelete');
	return true;
}
function _triggerPlugin($action) {
	$modelPassed=null;
	foreach($this as $key => $value) {
		$letter = substr( $key, 0, 1 );
		if ($letter != '_' || $key=='_uid' || $key=='_id' || $key=='_x') $modelPassed->$key=& $this->$key;
	}
	if ( !isset($modelPassed->uid) && isset( $modelPassed->_uid) ) $modelPassed->uid = $modelPassed->_uid;
	WController::trigger( 'users', $action, $modelPassed );
}
}