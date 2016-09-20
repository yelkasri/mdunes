<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_Coremultisites extends WForms_default {
	function create() {
		if ( file_exists( JPATH_ADMINISTRATOR. DS . 'components' . DS . 'com_multisites' . DS . 'helpers' . DS . 'utils.php' ) ) {
			include_once( JPATH_ADMINISTRATOR. DS . 'components' . DS . 'com_multisites' . DS . 'helpers' . DS . 'utils.php');
			if ( class_exists( 'MultisitesHelperUtils') && method_exists( 'MultisitesHelperUtils', 'getComboSiteIDs') ) {
				$comboSiteIDs = MultisitesHelperUtils::getComboSiteIDs( $this->value, JOOBI_VAR_DATA . '[' . $this->element->sid . '][' . $this->element->map . ']', WText::t('1425439010GLOC') );
				if ( empty($comboSiteIDs) ) return false;
				$this->content = $comboSiteIDs;
				return true;
			}
		} else {
			return false;
		}
		return false;
	}
	function show() {
		return false;
	}
}
