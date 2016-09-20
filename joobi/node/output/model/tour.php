<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Tour_model extends WModel {
function validate(){
if(empty($this->alias ))$this->alias=$this->getChild('tourname','name');
$this->core=0;
return true;
}}