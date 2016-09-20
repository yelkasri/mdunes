<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Users_Email_class extends WClasses {
 public function validateEmail($email,$fullCheck=true){
 if(!filter_var($email, FILTER_VALIDATE_EMAIL )){
 return false;
  }
  if($fullCheck){
   list($user, $domain )=explode('@',$email );
if( function_exists('checkdnsrr') && !checkdnsrr($domain.'.','MX')){
return false;
}
 }
 return true;
 }
 public function checkEmailUnique($email){
 if(empty($email)) return false;
 $users=WModel::get('users');
 $users->whereE('email',$email );
 $uid=$users->load('lr','uid');
 if(empty($uid)) return true;
 else return false;
 }
 public function checkUsernameUnique($username){
 if(empty($username)) return false;
 $users=WModel::get('users');
 $users->whereE('username',$username );
 $uid=$users->load('lr','uid');
 if(empty($uid)) return true;
 else return false;
 }
}