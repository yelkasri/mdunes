<?php

## no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
## Include the syndicate functions only once
require_once( dirname(__FILE__).DS.'helper.php' );
 
$list = mod_ticketmasterupcomingHelper::getList( $params );

$db    		= JFactory::getDBO();
$mainframe  = JFactory::getApplication();

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$ul_sfx = htmlspecialchars($params->get('ul_sfx'));
$title = $params->get('title');
$date_position = $params->get('date_position');
$date_format =  htmlspecialchars($params->get('date_format'));


require( JModuleHelper::getLayoutPath( 'mod_ticketmasterupcoming' ) );
?>