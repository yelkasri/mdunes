<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_CoreCurrenttoken_form extends WForms_default {
function create(){
$appsInfoC=WCLass::get('apps.info');
$token=$appsInfoC->getPossibleCode(true, 'token');
if(empty($token) || $token===true) return false;
$this->content=$token;
return true;
}}