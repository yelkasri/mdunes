<?php
/************************************************************
 * @version			ticketmaster 2.5.5
 * @package			com_ticketmaster
 * @copyright		Copyright © 2009 - All rights reserved.
 * @license			GNU/GPL
 * @author			Robert Dam
 * @author mail		info@rd-media.org
 * @website			http://www.rd-media.org
 *************************************************************/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

class ticketmasterModelUpcoming extends JmodelLegacy{

	function __construct(){
		parent::__construct();

		$mainframe =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Get the pagination request variables
		$limit        = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart    = JRequest::getInt('limitstart', 0);
		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$array    = JRequest::getVar('cid', array(0), '', 'array');
		$this->id = (int)$array[0]; 
		
		$this->eventid    = JRequest::getInt('id', 0);			
	}
	
	function getPagination() {
		
		if (empty($this->_pagination)) {
		
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
	
		return $this->_pagination;
	}
    
    function getTotal() {
	
        if (empty($this->_total)) {
		
			## Making the query for showing all the clients in list function
			$sql = 'SELECT t.starttime, t.ticketprice, t.ticketdate, t.ticketid, e.ticketcounter,
						CONCAT( e.eventname, " - ", t.ticketname ) AS upcomingeventname, e.eventdescription, t.start_price, t.end_price
					FROM #__ticketmaster_tickets AS t, #__ticketmaster_events AS e
					WHERE  parent =0
					AND t.eventid = e.eventid
					AND t.published = 1
					ORDER BY t.ticketdate ASC'; 
 
            $this->_total = $this->_getListCount($sql, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_total;
    }  

   function getList() {
   
		if (empty($this->_data)) {

		 	$db = JFactory::getDBO();
		
			## Making the query for showing all the clients in list function
			$sql = 'SELECT t.starttime, t.ticketprice, t.ticketdate, t.ticketid, e.ticketcounter, t.eventid,
						CONCAT( e.eventname, " - ", t.ticketname ) AS upcomingeventname, e.eventdescription, t.start_price, t.end_price
					FROM #__ticketmaster_tickets AS t, #__ticketmaster_events AS e
					WHERE  parent =0
					AND t.eventid = e.eventid
					AND t.published = 1
					ORDER BY t.ticketdate ASC'; 
		 
		 	$db->setQuery($sql, $this->getState('limitstart'), $this->getState('limit' ));
		 	$this->data = $db->loadObjectList();
		}
		return $this->data;
	}

   function getSold() {
   
        if (empty($this->_data)) {

			$db = JFactory::getDBO();

			## Making the query for showing all the cars in list function
			$sql = 'SELECT eventid, ticketid, COUNT(orderid) AS soldtickets
							FROM #__ticketmaster_orders
							GROUP BY ticketid';

			$db->setQuery($sql);
			$this->data = $db->loadObjectList();
        }
        return $this->data;
    }
	
   function getAdded() {
   
        if (empty($this->_data)) {

			$db = JFactory::getDBO();

			## Making the query for showing all the cars in list function
			$sql = 'SELECT eventid, ticketid, SUM(totaltickets) AS totals
							FROM #__ticketmaster_tickets
							GROUP BY ticketid';

			$db->setQuery($sql);
			$this->data = $db->loadObjectList();
        }
        return $this->data;
    }	

	
}
?>