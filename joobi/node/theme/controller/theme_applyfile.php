<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_applyfile_controller extends WController {
function applyfile(){
$themeC=WClass::get('theme.helper');
$themeC->overwriteThemeFile(true);
return true;
}
}