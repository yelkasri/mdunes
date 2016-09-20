<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_CoreDbprefix_form extends WForms_default {
function show(){
$this->content=JOOBI_DB_PREFIX;
return true;
}}