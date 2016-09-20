<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
abstract class WUsers extends WUser {
}
abstract class WRoles extends WRole {
public static function isAdmin($role=''){
$mySpace=WGlobals::getSession('page','space', null );
$isAdmin=( in_array($mySpace, array('vendors','sitevendors'))?false : IS_ADMIN );
if(!$isAdmin ) return false;
elseif(empty($role)) return true;
else {
$roleC=WRole::get();
return $roleC->hasRole($role );
}
}
public static function isNotAdmin($role=''){
$mySpace=WGlobals::getSession('page','space', null );
$isAdmin=( in_array($mySpace, array('vendors','sitevendors'))?false : IS_ADMIN );
if(!$isAdmin ) return true;
elseif(empty($role)) return false;else {
$roleC=WRole::get();
return ( ! $roleC->hasRole($role ));}
}
}
abstract class WPages extends WPage {
}