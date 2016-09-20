<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_CoreMemoryusage_form extends WForms_default {
function create(){
$maxMemory=@ini_get('memory_limit');
if(empty($maxMemory))$maxMemory=@get_cfg_var('memory_limit');
$realMemoryMimit=WTools::returnBytes($maxMemory );
if($realMemoryMimit > 1 && $realMemoryMimit < WTools::returnBytes('30M')){
$this->content='<span style="color:red; font-weight:boldl">'.WText::t('1378928005HOHD'). '</span>';
$this->content .='<br/><span style="color:red; font-weight:boldl">'.WText::t('1378928005HOHE'). WTools::returnBytes($maxMemory, true). '</span>';
return true;
}
return false;
}}