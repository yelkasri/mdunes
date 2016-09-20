<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_canceledit_controller extends WController {
function canceledit(){
$eid=WGlobals::getEID(false);
WPages::redirect('controller=theme&task=show&eid='.$eid );
return true;
}}