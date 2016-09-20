<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_advsearch_controller extends WController {
function advsearch(){
$viewID=WGlobals::get('viewID');
$currentValue=WGlobals::getUserState( "wiev-$viewID-adv_srch" , 'viewIDadv','','string');
$newValue=(empty($currentValue)?'1' : '0');
WGlobals::set('viewIDadv',$newValue );
$currentOrder=WGlobals::getUserState( "wiev-$viewID-adv_srch" , 'viewIDadv','','string');
WPages::redirect('previous');
return true;
}
}