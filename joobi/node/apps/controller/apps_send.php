<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_send_controller extends WController {
function send(){
$trk=WGlobals::get( JOOBI_VAR_DATA );
$mess=WMessage::get();
$extension=$trk['x']['extension'];
if(!isset($extension)){
return $mess->historyE('1213020853MLHQ');
}
if(isset($trk['x']['reason'])){
$typeInquiry=$trk['x']['reason'];
switch($typeInquiry){
case 10:
case 20:
case 100:
case 110:
break;
default:
return $mess->historyE('1206732398LZJD');
break;
}
$extExp=explode('_',$extension);
$extNamekey=$extExp[0];
$token=isset($extExp[1])?$extExp[1] : '';
$link='https://joobi.co/index.php?option=com_jlinks&controller=redirect';
$link .='&link=newticket'; 
$link .='&project='.$extNamekey;
$link .='&type='.$typeInquiry;
$link .='&token='.$token;
$link .='&web='.rtrim( JOOBI_SITE, "/" );  
WPages::redirect($link, false, false);
}else{
return $mess->historyE('1206732398LZJD');
}
}
}