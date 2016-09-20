<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_resetlang_controller extends WController {
function resetlang(){
$translationResetC=WClass::get('translation.reset');
$translationResetC->resetAutoColumns();
WPages::redirect('controller=apps&task=preferences');
return true;
}
}