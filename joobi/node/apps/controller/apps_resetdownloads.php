<?php 


* @license GNU GPLv3 */

class Apps_resetdownloads_controller extends WController {
function resetdownloads(){
$fileS=WGet::folder();
$fileS->delete( JOOBI_DS_USER.'downloads'.DS.'packages');
$this->userS('1429564804LHII');
WPage::redirect('controller=apps&task=preferences');
return true;
}
}