<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_sendtest_controller extends WController {
function sendtest(){
$this->skipMessage(true);
$this->savepref();
WPages::redirect('controller=apps&task=sendtestmessage');
}}