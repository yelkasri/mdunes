<?php
 
## no direct access
defined('_JEXEC') or die('Restricted access');

class mod_ticketmasterupcomingHelper
{
 
    function getList( $params )
    {
		
		$db    		= JFactory::getDBO();
		$mainframe  = JFactory::getApplication();
		
		## Making the query for showing all the clients in list function
		$sql = 'SELECT 
				t.ticketprice, t.ticketdate, t.ticketid, CONCAT( e.eventname, " - ", t.ticketname ) AS upcomingeventname, t.ticketname, e.eventname, v.venue
				FROM #__ticketmaster_tickets AS t, #__ticketmaster_events AS e, #__ticketmaster_venues AS v
				WHERE t.parent = 0
				AND t.eventid = e.eventid
				AND t.published = 1
				AND t.venue = v.id
				ORDER BY t.ticketdate ASC'; 
	 
		$db->setQuery($sql, 0, $params->get('list_limit'));
		$data = $db->loadObjectList();
		
        return $data;
    }

}
?>