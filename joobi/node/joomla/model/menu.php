<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WLoadFile( 'category.model.node' ,JOOBI_DS_NODE );
class Joomla_Menu_model extends Category_Node_model {
	protected $_deleteLeaf = true;	
	function addValidate() {
		if ( !isset( $this->parent_id ) ) $this->parent_id = 1;
		return parent::addValidate();
	}}