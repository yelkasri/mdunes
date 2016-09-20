<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Install_Uninstall_class {
	function uninstall(){
		$this->removeCMSLinks();
	}
	function removeCMSLinks(){
		WPage::refreshFrameworkMenu( 0, 'uninstall' );
		return true;
	}
	function getTablesToRemove(){
		$sql = WModel::get( 'library.model', 'object' );
		$sql->makeLJ( 'library.table','dbtid' );
		$sql->where('domain','<','100',1);
		$this->tables = $sql->load('lra','namekey');
		return true;
	}
	function removeTables() {
		if ( ! is_array($this->tables) || count($this->tables)==0 ) {
			return true;
		}
		foreach( $this->tables as $tables){
			$sql = WModel::get($tables);
			$sql->deleteTable(true);
		}
		return true;
	}
	function deleteJoobiFolder(){
		$folder_handler = WGet::folder();
		$folder_handler->delete(JOOBI_DS_JOOBI);
		return true;
	}
}