<?php 


* @license GNU GPLv3 */

class Apps_resetlang_controller extends WController {
function resetlang(){
$translationResetC=WClass::get('translation.reset');
$translationResetC->resetAutoColumns();
WPages::redirect('controller=apps&task=preferences');
return true;
}
}