<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Wp4_Role_addon {
public function getRoleName($roleID){
WText::load('api.node');
switch($roleID){
case 'register':
return WText::t('1424213023BXKK');
break;
case 'sadmin':
return WText::t('1206961998KTYJ');
break;
case 'author':
return WText::t('1206732400OWZO');
break;
case 'editor':
return WText::t('1211280059QYRJ');
break;
default:
return $roleID;
break;
}
}
public function getColumnName(){
return 'namekey';
}
public function getRoles(){
$resultA=array();
$resultA['register']='subcriber';
$resultA['sadmin']='administrator';
$resultA['author']='author';
$resultA['editor']='editor';
return $resultA;
}
public function insertRole($uid,$equivalentRole){
}
public function deleteRole($uid,$equivalentRole){
}
}