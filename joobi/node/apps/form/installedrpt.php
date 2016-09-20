<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_CoreInstalledrpt_form extends WForms_default {
function show(){
$extM=WModel::get('apps');
$extM->makeLJ('apps.info','wid','wid');
$extM->select('wid', 0, null, 'count', 0);
$extM->whereIn('type',array(1, 350), 0);
$extM->whereE('publish', 1, 0);
$extM->where('userversion','!=',' ', 1);
$count=$extM->load('lr');
$this->content=$count;
return true;
}}