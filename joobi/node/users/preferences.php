<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Default_users_preferences {
public $account_landing='';
public $activationmethod='self';
public $changeusername=0;
public $emailpwd=1;
public $fail_interval=15;
public $fail_max=5;
public $files_method_photos='local';
public $frameworkrole=3;
public $framework_be='joomla';
public $framework_fe='joomla';
public $imgformat='jpg,gif,png';
public $imgmaxsize=800;
public $loginallow=1;
public $loginemail=1;
public $login_landing='';
public $login_page='';
public $login_style=0;
public $logout_landing='';
public $maxph=500;
public $maxpw=500;
public $notifyadmin=0;
public $notifyadminemail='';
public $pwd_strength_admin='strong';
public $pwd_strength_register='normal';
public $registrationallow=1;
public $registrationrole=3;
public $registration_landing='';
public $registration_page='';
public $smallih=50;
public $smalliw=50;
public $useavatar=1;
public $usecaptcha='image';
public $usecurrency=1;
public $usehtmlemail=1;
public $uselanguage=1;
public $usemobile=0;
public $usernamedefault='email';
public $usetimezone=1;
public $watermarkitem=0;
}
class Role_users_preferences {
public $account_landing='sadmin';
public $activationmethod='admin';
public $changeusername='admin';
public $emailpwd='admin';
public $fail_interval='admin';
public $fail_max='admin';
public $files_method_photos='admin';
public $frameworkrole='sadmin';
public $framework_be='sadmin';
public $framework_fe='sadmin';
public $imgformat='sadmin';
public $imgmaxsize='admin';
public $loginallow='sadmin';
public $loginemail='sadmin';
public $login_landing='sadmin';
public $login_page='sadmin';
public $login_style='sadmin';
public $logout_landing='sadmin';
public $maxph='admin';
public $maxpw='admin';
public $notifyadmin='sadmin';
public $notifyadminemail='sadmin';
public $pwd_strength_admin='admin';
public $pwd_strength_register='admin';
public $registrationallow='sadmin';
public $registrationrole='sadmin';
public $registration_landing='admin';
public $registration_page='sadmin';
public $smallih='admin';
public $smalliw='sadmin';
public $useavatar='admin';
public $usecaptcha='admin';
public $usecurrency='admin';
public $usehtmlemail='admin';
public $uselanguage='admin';
public $usemobile='admin';
public $usernamedefault='sadmin';
public $usetimezone='admin';
public $watermarkitem='admin';
}