<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WLoadFile('users.addon.joomla.joomla');
class Users_Jomsocial_addon extends Users_Joomla_addon {
public function goRegister($itemId=null){
WPages::redirect('index.php?option=com_community&view=register',$itemId, false);
}
public function addUserRedirect(){
$option=WApplication::getApp();
WGlobals::setSession('Joobi_Users_ComeBack','JoomlaUsers',$option );
WPages::redirect('index.php?option=com_users&task=add');
}
public function showUserProfile($eid,$onlyLink=false){
$cid=WUser::get('id',$eid, 'uid');
$link='index.php?option=com_community&view=users&layout=profile&id='.$cid;
if($onlyLink ) return $link;
WPages::redirect($link );
}
public function goProfile($uid=null){
$uid=WGlobals::getEID();
$id=WUser::get('id',$uid);
WPages::redirect('index.php?option=com_community&view=profile&userid='.$id );
}
public function editUserRedirect($eid,$onlyLink=false){
$option=WApplication::getApp();
WGlobals::setSession('Joobi_Users_ComeBack','JoomlaUsers',$option );
$cid=WUser::get('id',$eid, 'uid');
$link='index.php?option=com_community&view=users&layout=edit&id='.$cid;
if($onlyLink ) return $link;
if(empty($cid))$this->addUserRedirect();
WPages::redirect($link );
}
public function getAvatar($uid){
$id=WUser::get('id',$uid);
if(empty($id)) return '';
$jspath=JPATH_ROOT.DS.'components'.DS.'com_community';
include_once($jspath.DS.'libraries'.DS.'core.php');
$user=CFactory::getUser($id);
$avatarUrl=$user->getThumbAvatar();
return $avatarUrl;
}
}