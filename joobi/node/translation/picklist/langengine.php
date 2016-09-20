<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_Langengine_picklist extends WPicklist {
function create(){
$engineM=WModel::get('engine.languages');
$engineM->makeLJ('library.languages','ref_lgid','lgid');
$engineM->select('ref_lgid');
$engineM->select('name', 1 );
$engineM->groupBy('ref_lgid');
$engineM->whereE('lgid', 1 );
$engineM->whereE('publish',true);
$engineM->setLimit( 500 );
$availLangs=$engineM->load('ol');
foreach($availLangs as $result){
$this->addElement($result->ref_lgid, $result->name );
}
}
}