<?php 


* @license GNU GPLv3 */

class Email_Queue_scheduler extends Scheduler_Parent_class {
function process(){
$emailQueueC=WClass::get('email.queue');
$emailQueueC->processQueue(false);
return true;
}
}