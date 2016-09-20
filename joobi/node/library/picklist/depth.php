<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Depth_picklist extends WPicklist {
function create(){
$sid=$this->sid;
$depth=WGlobals::get('catdepth'.$sid, 0, 'session');
if(!$depth){
$myM=WModel::get($sid );
$myM->select('depth' ,0, null, 'max');
$depth=$myM->load('lr') +1;
WGlobals::set('catdepth'.$sid, $depth, 'session');
}
$this->addElement( 0,  WText::t('1215098992JNKQ'));
for($index=1; $index < $depth; $index++){
$this->addElement($index,  WText::t('1206732389NXTA'). ' '.$index  );
}
}}