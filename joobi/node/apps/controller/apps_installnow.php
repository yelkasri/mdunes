<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_installnow_controller extends WController {
function installnow(){
$libProgreC=WClass::get('library.progress');
$progressO=$libProgreC->get('apps');
$progressO->run();
$ajaxHTML=$progressO->displayAjax();
echo $ajaxHTML;
$progressO->finish();
exit();
return true;
}
}