<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Library_Sql_type extends WTypes {
public $type=array(
10=> 'mysql',
15=> 'mysqli',
20=> 'posgres',
30=> 'oracle'
  );
 }
