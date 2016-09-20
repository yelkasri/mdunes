<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Editor_Editors_picklist extends WPicklist {
	function create() {
		$class = WClass::get('editor.get');
		$editors = $class->getAllEditorsName();
		foreach( $editors as $editor ) {
			$this->addElement( 'a' , '--'. $editor->name );
			foreach( $editor->editors as $idEditor => $nameEditor ) {
				$this->addElement( $idEditor , $nameEditor );
			}
		}
	}}