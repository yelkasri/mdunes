<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_savepref_controller extends WController {
function savepref(){
parent::savepref();
$appSaveprefC=WClass::get('apps.savepref');
$appSaveprefC->appsSave($this->generalPreferences );
WPages::redirect('controller=apps');
return true;
}
}