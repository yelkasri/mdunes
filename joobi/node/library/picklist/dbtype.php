<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Dbtype_picklist extends WPicklist {
function create(){
$this->addElement('framework', ucfirst( JOOBI_FRAMEWORK_TYPE ));
if( function_exists('mysqli_query'))$this->addElement('mysqli','mysqli');
if( function_exists('mysql_query'))$this->addElement('mysql','mysql');
return true;
}
}