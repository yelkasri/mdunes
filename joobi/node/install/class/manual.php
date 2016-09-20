<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Install_Manual_class {
	public function runInstall($namekey,$new=true) {
		WLoadFile( 'install.class.install', JOOBI_DS_NODE );
		$folder = WExtension::get( $namekey, 'folder' );
		$object = new stdClass;
		$object->newInstall = $new;
		WLoadFile( $folder . '.install.install', JOOBI_DS_NODE );
		$className = ucfirst( $folder ) . '_Node_install';
		if ( class_exists( $className ) ) {
			$instance = new $className;
			$instance->install( $object );
		}
		return true;
	}
}
