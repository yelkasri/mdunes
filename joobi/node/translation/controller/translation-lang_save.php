<?php 


* @license GNU GPLv3 */

class Translation_lang_save_controller extends WController {
function save(){
$status=parent::save();
$cache=WCache::get();
$cache->resetCache( array('Translation','Views','Language','Menus','Model'));
return $status;
}
}