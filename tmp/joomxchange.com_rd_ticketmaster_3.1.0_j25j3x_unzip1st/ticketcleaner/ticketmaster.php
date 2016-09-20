<?php
/**
* @version 2.5.4
* @package Ticketmaster
* @copyright (C) 2010 www.rd-media.org
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
* @Functionality: Using the ticket sale stopper.
*/

## no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );


class plgSystemTicketMaster extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatibility we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	 
	function plgSystemTicketMaster( &$subject, $config ) {
	
		parent::__construct( $subject, $config );
		
	}
	
	function onAfterDispatch() {
		
		## Getting the current date (NOW)
		$now = JFactory::getDate();	
		
		## Connecting the database.
		$db = JFactory::getDBO();
		
		## Setting the query to unpublish
		$query = ' UPDATE #__ticketmaster_tickets'
			   . ' SET published = 0'
			   . ' WHERE use_sale_stop = 1'
			   . ' AND sale_stop < "'.$now.'" ';
		
		## Do the query now	
		$db->setQuery( $query );
		
		## When query goes wrong.. Show message with error.
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		
			
		## path to remover -- If this path exsist we can perform a cleanup :)
		$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ticketmaster'.DS.'classes'.DS.'ticketcleaner.class.php';

		## Include path.
		if (file_exists($path)) {
			require_once($path);
		}else{
			return false;
		}
		
		$cleaner = new remover();  
		$cleaner->cleanup();

		
		return true;			

	}	

}