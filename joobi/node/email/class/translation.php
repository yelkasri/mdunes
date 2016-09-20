<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Translation_class extends WClasses {
public function secureTranslation($emailO,$sid,$eid){
$uid=WUser::get('uid');
if(empty($uid)) return false;
if( WRole::hasRole('manager')) return true;
return false;
}
}