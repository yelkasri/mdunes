<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_CoreAllrpt_form extends WForms_default {
function show(){
$extM=WModel::get('apps');
$extM->select('wid', 0, null, 'count');
$extM->whereIn('type',array( 1, 350 ));
$extM->whereE('publish', 1 );
$extM->makeLJ('apps.info','wid');
$extM->where('beta','<=', 1, 1 );
$count=$extM->load('lr');
$this->content=$count;
return true;
}}