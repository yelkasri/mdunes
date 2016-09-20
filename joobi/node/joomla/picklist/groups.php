<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Joomla_Groups_picklist extends WPicklist {
	function create() {
		$roleAddon = WAddon::get( 'api.'. JOOBI_FRAMEWORK . '.role' );
		$column = $roleAddon->getColumnName();
		$defaultRoleID = $this->getValue( $column );
		$this->setDefault( $defaultRoleID, true );
		$allCMSRolesA = $roleAddon->getRoles();
		if ( empty($allCMSRolesA) ) return false;
		foreach( $allCMSRolesA as $oneUserRole ) {
			$this->addElement( $oneUserRole->id, $oneUserRole->name );
		}
		return true;
	}}