<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Add_class extends WClasses {
public function addUser($email,$role=null,$userObj=null,$importObject=null){
$usersEmailC=WClass::get('users.email');
if(!$usersEmailC->validateEmail($email)){
$EMAIL=$email;
$this->userE('1377828382KXRU',array('$EMAIL'=>$EMAIL));
return false;
}
$uid=WUser::get('uid',$email );
if(!empty($uid)){
$this->_insertUpdateImportedUser($uid, $importObject );
return $uid;
}
if(!empty($role)){
$roleHelperC=WRole::get();
$roleCompared=$roleHelperC->compareRole($role, 'registered');
if($roleCompared===true){
$usersCredentialC=WUser::credential();
return $usersCredentialC->ghostAccount($email );
}
}else{
$role=WRole::getRole('visitor');
}
$rolid=WRole::getRole($role );
if(empty($rolid)){
$message=WMessage::get();
$message->adminE('The specified role does not exist.');
return false;
}
$usersM=WModel::get('users');
$usersM->addProperties($userObj );
$usersM->email=$email;
$usersM->rolid=$rolid;
$usersM->returnId();
$status=$usersM->save();
if(!empty($usersM->uid))$this->_insertUpdateImportedUser($usersM->uid, $importObject );
return (!empty($usersM->uid)?$usersM->uid : false);
}
private function _insertUpdateImportedUser($uid,$importObject){
if(empty($importObject->model )) return false;
$data=$importObject->data;
$importModelM=WModel::get($importObject->model, 'object', null, false);
if(empty($importModelM)) return false;
$importModelM->whereE('uid',$uid );
$entryExist=$importModelM->exist();
if($entryExist){
$importModelM->whereE('uid',$uid );
foreach($data as $property=> $value){
$importModelM->setVal($property, $value );
}$importModelM->update();
}else{
$importModelM->setVal('uid',$uid );
foreach($data as $property=> $value){
$importModelM->setVal($property, $value );
}$importModelM->insertIgnore();
}
}
}