<?php
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.
* @license GNU GPLv3 */

//defined('_JEXEC') or die;
define('JOOBI_SECURE',true);
$p=isset($_REQUEST['netcom']) ? $_REQUEST['netcom']:'';
if(!empty($p)) {
if($p == 1) $p='netcom';
define('JOOBI_FRAMEWORK_CONFIG_FILE','configuration.php');
define('JOOBI_FRAMEWORK_OVERRIDE','joomla30');
define('JOOBI_FRAMEWORK_CONFIG','joomla');
define('JOOBI_FRAMEWORK',$p);
}
require( dirname(__FILE__). DIRECTORY_SEPARATOR  . 'entry.php' );