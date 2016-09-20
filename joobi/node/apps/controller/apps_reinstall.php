<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_reinstall_controller extends WController {
function reinstall(){
$wid=WGlobals::getEID();
$helperC=WClass::get('apps.helper');
$appExtensions=$helperC->getAppsDependencies($wid );
$appsM=WModel::get('apps');
$appsM->updatePlus('version', -1 );
$appsM->whereIn('namekey',$appExtensions );
$appsM->update();
$extensionHelperC=WCache::get();
$extensionHelperC->resetCache();
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