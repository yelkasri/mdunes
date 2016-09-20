<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_logs_delete_controller extends WController {
function delete(){
$FILE=WGlobals::get('file');  
$fileClass=WGet::file(); 
if(is_string($FILE) && !empty($FILE)){
if($fileClass->delete( JOOBI_DS_USER.'logs'.DS.$FILE )){
$this->userS('1260434893HJHR',array('$FILE'=>$FILE));
}
}
WPages::redirect('controller=apps-logs&task=listing');
return true;
}
}