<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Editor_Framework_addon extends Editor_Get_class {
	var $cms = true;
	public $showButtons = false;
	var $changeVar = true;
	function display() {
		$addonFramekework = WAddon::get( 'api.' . JOOBI_FRAMEWORK . '.editor' );
		$addonFramekework->addProperties( $this );
		return $addonFramekework->display();
	}
	function getEditorName() {
		$addonFramekework = WAddon::get( 'api.' . JOOBI_FRAMEWORK . '.editor' );
		return $addonFramekework->getEditorName();
	}
}