<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_toggle_controller extends WController {
function toggle(){
$extensionHelperC=WCache::get();
$extensionHelperC->resetCache('Model_mailing_node');
$status=parent::toggle();
return $status;
}}