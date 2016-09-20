<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_Show_class extends WClasses {
function checkInstalled($wid){
static $infoA=array();
if(!empty($infoA[$wid])) return $infoA[$wid];
$extensionM=WModel::get('apps');
$extensionM->select( array('version','lversion'));
$extensionM->select( array('ltype','maintenance','level'), 1 );
$extensionM->makeLJ('apps.userinfos','wid');
$extensionM->whereOn('enabled','=', 1 );
$extensionM->whereE('wid',$wid );
$extensionM->whereE('publish', 1 );
$infoA[$wid]=$extensionM->load('o');
return $infoA[$wid];
}
}