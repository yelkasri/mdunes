<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WRender_Saveauto_blueprint extends Theme_Render_class {
public function render($formname){
if( defined('PLIBRARY_NODE_TIMEINTERVAL') && PLIBRARY_NODE_TIMEINTERVAL)$timeInterval=PLIBRARY_NODE_TIMEINTERVAL;
if(isset($timeInterval) && is_numeric($timeInterval))$time=$timeInterval*1000;else return;
$controller=WGlobals::get('controller'); $url=WPage::routeURL('controller='.$controller.'&task=saveauto'); $namekey='HP_'.$formname.'_save';
$script='
(function($j){
$j(document).ready(function(){
window.WApps.helpers.autoSave("'.$formname.'" ,"'. $time.'" ,"'. $url.'" ,"'. $namekey.'" );
return false;
});
})(jQuery);';
WPage::addJSLibrary('jquery');
WPage::addJSFile('main/js/jquery.autosave.js','inc');
WPage::addJSScript($script );
}
}