<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Joomla_Node_install extends WInstall {
	public function install(&$object) {
	}
	public function addExtensions() {
				if ( JOOBI_FRAMEWORK_TYPE != 'joomla' ) return true;
				$extension = new stdClass;
		$extension->namekey = 'joomla.quickicons.module';
		$extension->name = 'Joobi - Joobi Quick Icons';
		$extension->folder = 'quickicons';
		$extension->type = 25;
		$extension->publish = 1;
		$extension->certify = 1;
		$extension->destination = 'node|joomla|module';
		$extension->core = 1;
		$extension->params = "position=icon\npublish=1\naccess=1\nclient=1\nordering=1";
		$extension->description = '';
		if ( $this->insertNewExtension( $extension ) ) $this->installExtension( $extension->namekey );
				$extension = new stdClass;
		$extension->namekey = 'joomla.shortmenu.module';
		$extension->name = 'Joobi - Joobi Short Menu';
		$extension->folder = 'shortmenu';
		$extension->type = 25;
		$extension->publish = 1;
		$extension->certify = 1;
		$extension->destination = 'node|joomla|module';
		$extension->core = 1;
		$extension->params = "position=menu\npublish=1\naccess=3\nclient=1\nordering=2";
		$extension->description = '';
		if ( $this->insertNewExtension( $extension ) ) $this->installExtension( $extension->namekey );
	}
}