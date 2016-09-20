<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_Level_type extends WTypes {
public $level=array(
''=>'Core', 
'0'=>'Core',
'25'=>'Plus',
'50'=>'PRO',
'75'=>'Gold',
'199'=>'Dev',
'239'=>'Joobi',
'1'=>'Notdefined',
'9999'=>'Notdefined'
);
}