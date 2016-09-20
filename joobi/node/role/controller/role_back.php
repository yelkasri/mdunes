<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Role_back_controller extends WController {
function back(){
WPages::redirect('controller=role');
return true;
}}