<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Queue_scheduler extends Scheduler_Parent_class {
function process(){
$emailQueueC=WClass::get('email.queue');
$emailQueueC->processQueue(false);
return true;
}
}