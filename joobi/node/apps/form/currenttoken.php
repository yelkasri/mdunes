<?php 


* @license GNU GPLv3 */

class Apps_CoreCurrenttoken_form extends WForms_default {
function create(){
$appsInfoC=WCLass::get('apps.info');
$token=$appsInfoC->getPossibleCode(true, 'token');
if(empty($token) || $token===true) return false;
$this->content=$token;
return true;
}}