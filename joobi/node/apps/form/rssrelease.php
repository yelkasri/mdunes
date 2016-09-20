<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_CoreRssrelease_form extends WForms_default {
function create(){
$xml='https://joobi.co/r.php?l=rssreleasenote';
$xml=base64_encode($xml );
switch( JOOBI_FRAMEWORK_TYPE){
case 'joomla':$url='index.php?option=com_japps&controller=apps-rss&url='.$xml.'&tmpl=component';
break;
default:
$url='index.php?page=japps&controller=apps-rss&url='.$xml.'&noheader=1';
break;
}
$text=WText::t('1432148472FAJB');
$this->content='<div id="rssreleasenote"><h3><i class="fa fa-spinner fa-spin"></i>'.WText::t('1442253312FRLP'). '</h3></div> ';
$js='jQuery("#rssreleasenote").load(\''.$url.'\');';
WPage::addJSScript($js );
return true;
}
function show(){
return $this->create();
}
}