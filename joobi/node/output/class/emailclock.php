<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Emailclock_class {
function cloakmail($idLabel,$eid,$email,$truncate=0,$decodeOnly=false){
if(empty($email)) return '';
$cloakmail=WGlobals::get('cloakmail', null, 'global');
if(empty($cloakmail)){
WPage::addJSFile('main/js/cloakemail.js','inc');
WGlobals::set('cloakmail', true, 'global');
}
if($decodeOnly){
WPage::addJSScript('joobenEmail('.$eid.',\''.$idLabel.'\','.$truncate.');');
return '';
}
$value=explode('@',$email );
$val3='';
$val4='';
if(isset($value[1])){
$val2=explode('.',$value[1],2);
$val3=$val2[0];
$val4=isset($val2[1])?$val2[1]:'undefined';
}
$cloak='joobRecEmail(\''.$eid.'\',\''.$value[0].'\',\''.$val3.'\',\''.$val4.'\');' .
'joobenEmail('.$eid.',\''.$idLabel.'\','.$truncate.');';
WPage::addJSScript($cloak);
return '<span id="'.$idLabel.'"></span><noscript>'.WText::t('1206732368LJDG'). '</noscript>';
}
}
