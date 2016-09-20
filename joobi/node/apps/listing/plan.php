<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_CorePlan_listing extends WListings_default{
function create(){
if($this->getValue('publish')){
$namekey=$this->getValue('namekey');
if( JOOBI_MAIN_APP.'.application'==$namekey ) return false;
$club=WGlobals::get('sweetClub', false);
if($club){
$subStype=WGlobals::get('sweetClubType');
if(!empty($subStype)){
$hT=WType::get('apps.clubsubtype');
$nH=$hT->getName($subStype);
if(empty($nH))$nH='Joobi Care';
$colorT=WType::get('apps.clubcolor');
$color=$colorT->getName($subStype );
$this->content .='<span class="label label-'.$color.'"><big>'.$nH.'</big></span>';
}
}else{
if($this->getValue('enabled','apps.userinfos')){
$subStype=$this->getValue('subtype','apps.userinfos');
if(!empty($subStype)){
$hT=WType::get('apps.clubsubtype');
$nH=$hT->getName($subStype);
if(empty($nH))$nH='Joobi Care';
$colorT=WType::get('apps.clubcolor');
$color=$colorT->getName($subStype);
$this->content .='<span class="label label-'.$color.'"><big>'.$nH.'</big></span>';
}else{
$this->content .='<span class="label label-info">'.WText::t('1357059110IPWM'). '</span>';
}}else{
$this->content .='<span class="label label-danger">'.WText::t('1206961944PEUR'). '</span>';
}
}
$this->content .='<br/>';
$maintenance=0;
if($club){
$maintenance=WGlobals::get('sweetClubTime');
}else{
$maintenance=$this->getValue('maintenance');
if(empty($maintenance)){
$maintenance=$this->getValue('expire');
if($maintenance > ( time() + 63072000 )){
$maintenance -=283824000;}}
if(empty($maintenance))$maintenance=WGlobals::get('sweetClubTimeFree') + 86400*29;}
if(!is_numeric($maintenance))$maintenance=strtotime($maintenance );
if($namekey !=JOOBI_MAIN_APP.'.application' && $namekey !='jst'.'ore.application' && $namekey !='jca'.'talog.application'){
if($maintenance > 2506000){
$maintenance +=86400;
if($maintenance < time()){
$color='red';
$text=WText::t('1235461988ERWP'). ': ';
}else{
$color='green';
$text='';
}
$this->content .='<small><span style="color:'.$color.';">'.$text . WApplication::date( WTools::dateFormat('date'), $maintenance ). '</span></small>';
}
}
}else{
return false;
}
return true;
}}