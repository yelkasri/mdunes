<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WLoadFile('users.class.parent', JOOBI_DS_NODE );
class Users_Mobilev1_addon extends Users_Parent_class {
public function getUser($userId,$loadFrom=''){if(empty($userId)) return null;
$colID=(empty($loadFrom))?'id' : $loadFrom;
$key=trim($userId.'-'.$colID );
$key=(string)$key;
if(isset(self::$myMemberUID[$key])) return self::$myMemberUID[$key];if(isset(self::$myMemberID[$key])) return self::$myMemberID[$key];
if(isset(self::$myMemberEmail[$key])) return self::$myMemberEmail[$key];
if(isset(self::$myMemberUsername[$key])) return self::$myMemberUsername[$key];
$userM=WModel::get('users','object', null, false);
if(empty($userM)){
WMessage::log($userId, 'install-missing-users-model');
WMessage::log( debugB( 8246711 ), 'install-missing-users-model');
WMessage::log('install-missing-users-model','install');
return false;
}
$userM->rememberQuery(false);
$userM->select('*', 0 );
$userM->select('ip', 0, 'previousip','ip');
if( is_numeric($userId)){
$userM->whereE($colID, $userId );
}else{
$userM->whereE('email',$userId );
$userM->operator('OR');
$userM->whereE('username',$userId );
}
$userM->select('id', 0, 'id');
$theMember=$userM->load('o');
if(empty($theMember)){
$theMember=false;
self::$myMemberID[$userId.'-'.$colID]=$theMember;
}else{
self::$myMemberID[$theMember->id.'-id']=$theMember;
self::$myMemberUID[$theMember->uid.'-uid']=$theMember;
self::$myMemberEmail[$theMember->email.'-uid']=$theMember;
self::$myMemberUsername[$theMember->username.'-uid']=$theMember;
}
return $theMember;
}
}