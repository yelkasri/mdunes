<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Translation_Share_class extends WClasses {
function shareTranslation($content,$metadata){
$langM=WTable::get('joobi','languages','lgid');
$langM->whereE('name',$metadata['Language'] );
$code=$langM->load('o',array('code','lgid'));
$wid=WGet::exntension( strtolower($metadata['Application']), 'wid');
$extLangM=WModel::get('apps.languages');
$extLangM->whereE('lgid',$code->lgid );
$extLangM->whereE('wid',$wid );
$extLangM->whereE('translation', true);
$needed=$extLangM->exist();
if($needed){
$data=new stdClass;
$data->url=JOOBI_SITE;
$data->metadata=$metadata;
$data->content=$content;
if(!defined('PINSTALL_NODE_DISTRIB_WEBSITE')) WPref::get('install.node');
$netcom=WNetcom::get();
$result=$netcom->send( PINSTALL_NODE_DISTRIB_WEBSITE, 'repository','languagefile',$data );
if($result){
$this->userS('1206732404ORZG');
}else{
$EMAIL='translation@joobi.co';
$this->userE('1213285236EJWF');
}$this->userS('1213020853MLHS');
}else{
$EMAIL='translation@joobi.co';
$this->userN('1213285236EJWG');
$this->userN('1213020853MLHS');
}
return true;
}
}
