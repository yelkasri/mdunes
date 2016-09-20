<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Apps_Install_class extends WClasses {
 public function createDashboardMenu($icon,$count){
 if(empty($icon)) return true;
 $yid=WView::get('joomla_quickicons','yid');
 $extensionName=strtolower($icon);
 $libraryViewMenuM=WModel::get('library.viewmenus');
 $libraryViewMenuM->whereE('namekey','joomla_quickicons_' .$extensionName );
 $libraryViewMenuM->whereE('publish', 1 );
 $mid=$libraryViewMenuM->load('lr','mid');
 if(empty($mid)){
  $libraryViewMenuM->setVal('yid',$yid );
 $libraryViewMenuM->setVal('namekey','joomla_quickicons_' .$extensionName );
 $libraryViewMenuM->setVal('type', 102 );
 $libraryViewMenuM->setVal('icon', strtolower($icon));
 $libraryViewMenuM->setVal('ordering',$count );
 $libraryViewMenuM->setVal('publish', 1 );
 $libraryViewMenuM->setVal('params','filef=quickicons');
 $libraryViewMenuM->setVal('core', 0 );
 $libraryViewMenuM->returnId();
 $libraryViewMenuM->insertIgnore();
 $mid=$libraryViewMenuM->mid;
 }
 if(empty($mid)) return false;
  $libraryViewMenuTransM=WModel::get('library.viewmenustrans');
 $libraryViewMenuTransM->mid=$mid;
 $libraryViewMenuTransM->name=$icon;
 $libraryViewMenuTransM->save();
 }
}
