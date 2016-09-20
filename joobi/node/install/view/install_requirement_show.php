<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Install_Install_requirement_show_view extends Output_Forms_class {
		function prepareQuery() {
			$installRequirementsC = WClass::get( 'install.requirements' );
			$reuiqrementsA = $installRequirementsC->displayRequirements();
			$objElement = new stdClass;
			foreach( $reuiqrementsA as $paneInfoO ) {
				if ( empty($paneInfoO->paneA) ) continue;
				foreach( $paneInfoO->paneA as $onePaneO ) {
					$prop = substr( $onePaneO->map, 2, -1 );
					$value = $onePaneO->value;
					$objElement->$prop = $value;
				}
			}
			$this->addData( $objElement );
			return true;
		}
	function prepareView() {
		$installRequirementsC = WClass::get( 'install.requirements' );
		$reuiqrementsA = $installRequirementsC->displayRequirements();
		$samplePane = $this->elements[0];
		$sampleElement = $this->elements[1];
		$newElementsA = array();
		$count = 1;
		$parent = 0;
		foreach( $reuiqrementsA as $paneInfoO ) {
						$Pane = null;
			$Pane = clone($samplePane);
			$Pane->name = $paneInfoO->name;
			$Pane->fid = $count;
			$parent = $count;
			$newElementsA[] = $Pane;
			if ( !empty($paneInfoO->paneA) ) {
				foreach( $paneInfoO->paneA as $element ) {
					$count++;
					$pref = null;
					$pref = clone($sampleElement);
					foreach( $element as $p => $v ) $pref->$p = $v;
					$pref->fid = $count;
					$pref->parent = $parent;
					$newElementsA[] = $pref;
				}
			}
			$count++;
		}
		$this->elements = $newElementsA;
		return true;
	}
}