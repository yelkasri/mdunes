<?php 


* @license GNU GPLv3 */

WLoadFile( 'category.model.node' ,JOOBI_DS_NODE );
class Joomla_Menu_model extends Category_Node_model {
	protected $_deleteLeaf = true;	
	function addValidate() {
		if ( !isset( $this->parent_id ) ) $this->parent_id = 1;
		return parent::addValidate();
	}}