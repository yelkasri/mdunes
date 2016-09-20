<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_CoreOrigin_listing extends WListings_default{
function create(){
if(!$this->getValue('core')){
$namekeyA=explode('.',$this->value );
$FOLDER='<b>'.$namekeyA[0].'</b>';
$this->content=str_replace(array('$FOLDER'), array($FOLDER),WText::t('1329173546QVQL'));
}
return true;
}}