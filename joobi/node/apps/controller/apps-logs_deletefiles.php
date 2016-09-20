<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_logs_deletefiles_controller extends WController {
function deletefiles(){
$folder=WGet::folder();
$folder->delete( JOOBI_DS_USER.'logs');
$folder->create( JOOBI_DS_USER.'logs');
$mess=WMessage::get();
$mess->userS('1342037586EZIY');
return true;
}}