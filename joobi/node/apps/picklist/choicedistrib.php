<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_Choicedistrib_picklist extends WPicklist {
function create(){
$this->addElement( 11, WText::t('1206732410ICCJ'));
$this->addElement( 1, WText::t('1357387105DGOO'));
$this->addElement( 99, WText::t('1231158811CQTU'));
$this->addElement( 54, WText::t('1357387105DGOP'));
return true;
}
}