<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WButton_CoreHelp extends WButtons_default {
function create(){
if( WRoles::isNotAdmin('manager') && ! PLIBRARY_NODE_HELPFE){
return false;
}
$linkNamekey=WView::get($this->buttonO->yid, 'namekey');
$startNamekey=WExtension::get($this->viewInfoO->wid, 'folder');
$defaultPage=$startNamekey.'doc_glossary';
if(empty($linkNamekey))$linkNamekey=$defaultPage;
else $linkNamekey=$startNamekey.'_'.$linkNamekey;
$langauge=WLanguage::get( WUser::get('lgid'), 'code');
$langID=substr($langauge, 0, 2 );
$link=rtrim( PLIBRARY_NODE_DOCUMENTATION_SITE, '/'). '/index.php?option=com_jlinks&controller=redirect&linkid='.$linkNamekey.'&alt='.$defaultPage.'&lang='.$langID;
$js='function showWizard(){var box=jQuery(\'#helpArea\');var tagN=box.get(0).nodeName;if(box.find(\'iframe\').length){}else{box.html(\'<iframe width="100%" height="800px" scrolling=auto src="'.$link.'"></iframe>\');}if(box.is(\':visible\')){box.hide();}else{box.show();}}';
WPage::addJSScript($js, 'jquery', false);
$this->buttonO->buttonJS='showWizard();return false;';
$objButtonO=WPage::newBluePrint('button');
$objButtonO->text=$this->buttonO->name;
$objButtonO->type='infoLink';
$objButtonO->link=$link;
$objButtonO->id='mid_'.$this->buttonO->mid;
$objButtonO->linkOnClick=$this->buttonO->buttonJS;
$objButtonO->icon=$this->buttonO->faicon;
$objButtonO->color=$this->buttonO->color;
$this->content=WPage::renderBluePrint('button',$objButtonO );
return true;
}
}