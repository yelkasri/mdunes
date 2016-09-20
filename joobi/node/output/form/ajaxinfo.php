<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_Coreajaxinfo extends WForms_default {
function create(){
$libProgreC=WClass::get('library.progress');
$progressO=$libProgreC->get( WExtension::get($this->wid, 'folder'));
if(empty($progressO)){
return false;
}
WPage::addJSFile('js/ajax.js','api');
$text='<span class="label label-warning"><i class="fa fa-spinner fa-spin"></i>'.WText::t('1427486118NNQO'). '</span>';
WPage::addJS( "jCore.msg['prgr']='$text'" );
$text='<span class="label label-danger">'.WText::t('1427652827KXNP'). '</span>';
WPage::addJS( "jCore.msg['cal']='$text'" );
$maxTime=$progressO->maxExecutionTime();
if($maxTime > 10 ) WPage::addJS( "jCore.msg['maxTime']='$maxTime'" );
if( WPref::load('PLIBRARY_NODE_ENABLESOUND')){
$browser=WPage::browser('namekey');
$extension=($browser=='safari' || $browser=='msie')?'mp3' : 'ogg';
$URLBeep=WPref::load('PLIBRARY_NODE_CDNSERVER'). '/joobi/user/media/sounds/finish'.'.'. $extension;
$extraSound='<audio autoplay="true" src="'.$URLBeep.'" preload="auto" autobuffer></audio>';
WPage::addJS( "jCore.msg['sound']='$extraSound'" );
}
$text=WText::t('1427652827KXNQ');
WPage::addJS( "jCore.msg['exhaust']='$text'" );
$text=WText::t('1427652827KXNR');
WPage::addJS( "jCore.msg['stop']='$text'" );
$text=WText::t('1427652827KXNS');
WPage::addJS( "jCore.msg['chkit']='$text'" );
$text=WText::t('1427652827KXNT');
WPage::addJS( "jCore.msg['chkscs']='$text'" );
$text=WText::t('1427652827KXNU');
WPage::addJS( "jCore.msg['chkerr']='$text'" );
$url=$progressO->redirectURL();
if(!empty($url)){
WPage::addJS( "jCore.msg['url']='$url'" );
}
$html='<div id="WAjaxFrame" class="clearfix" style="margin-left:38px;">';
$html .=$progressO->display();
$html .='</div>';
$firstMessage=$progressO->firstMessage();
if(!empty($firstMessage )){
WPage::addJS( "jCore.msg['frstmsg']='$firstMessage'" );
}
$this->content=$html;
return true;
}
function show(){
return $this->create();
}
}
