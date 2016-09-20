<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_Savepref_class extends WClasses {
function appsSave(&$preferencesA){
$cache=WCache::get();
$cache->resetCache();
if(isset($preferencesA['install.node']['distrib_website'])) trim($preferencesA['install.node']['distrib_website'] );
if(isset($preferencesA['install.node']['distrib_website_beta'])){
trim($preferencesA['install.node']['distrib_website_beta'] );
$installPref=WPref::get('install.node');
$installPref->updatePref('distrib_website_beta_time', time());
}if(isset($preferencesA['install.node']['distrib_website_dev'])) trim($preferencesA['install.node']['distrib_website_dev'] );
if(isset($preferencesA['library.node']['documentation_site'])) trim($preferencesA['library.node']['documentation_site'] );
if(isset($preferencesA['apps.node']['home_site'])) trim($preferencesA['apps.node']['home_site'] );
if(isset($preferencesA['users.node']['framework_be'])){
$userManagement=$preferencesA['users.node']['framework_be'];
if(!empty($userManagement)){
$usersAddon=WAddon::get('users.'.$userManagement );
if( is_object($usersAddon)){
$usersAddon->checkPlugin();
}}}
if(isset($preferencesA['users.node']['framework_fe'])){
$userManagement=$preferencesA['users.node']['framework_fe'];
if(!empty($userManagement)){
$usersAddon=WAddon::get('users.'.$userManagement );
if( is_object($usersAddon)){
$usersAddon->checkPlugin();
}}}
if(isset($preferencesA['apps.node']['distribserver'])){
if( 99==$preferencesA['apps.node']['distribserver']){
$appsPref=WPref::get('apps.node');
$appsPref->updatePref('distribservertime', time());
}}
if(isset($preferencesA['library.node']['cron'])){
if($preferencesA['library.node']['cron']==10){
$schedulerCronC=WClass::get('scheduler.cron');
$result=$schedulerCronC->checkCron();
if(!defined('PAPPS_NODE_HOME_SITE')) WPref::get('apps');
$HOMESITE=PAPPS_NODE_HOME_SITE;
if($result){
$this->userS('1298294128HPXJ',array('$HOMESITE'=>$HOMESITE));
}else{
$this->userE('1298294128HPXK',array('$HOMESITE'=>$HOMESITE));
}
}
if($preferencesA['library.node']['cron']==5 )  $enabled=true;
else  $enabled=false;
WApplication::enable('scheduler_system_plugin',$enabled, 'plugin');
$ENABLED=($enabled?'enabled' : 'disabled');
$this->userN('1303354916DPMU',array('$ENABLED'=>$ENABLED));
}
if(isset($preferencesA['library.node']['useminify']) && empty($preferencesA['library.node']['useminify'])){
$mainMinifyC=WClass::get('main.minify');
if(!empty($mainMinifyC)){
if(!$mainMinifyC->getMinifyThemes()){
$this->userE('1413490911HRES');
$preferencesA['library.node']['useminify']=true;
$prefM=WPref::get('library.node');
$prefM->updatePref('useminify', 1 );
$extensionHelperC=WCache::get();
$extensionHelperC->resetCache('Preference');
return false;
}}
}
return true;
}
}