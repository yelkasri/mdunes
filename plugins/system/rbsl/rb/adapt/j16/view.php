<?php
/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		RB Framework
* @subpackage	Frontend
* @contact 		shyam@readybytes.in
*/
if(defined('_JEXEC')===false) die('Restricted access' );

jimport( 'joomla.application.component.view' );

class Rb_AdaptJ16View extends JViewLegacy
{
	protected	$_name		= null;
	
	function setModel(&$model, $default = false)
	{
		 $this->_model = $model;
		 return $this;
	}
}

