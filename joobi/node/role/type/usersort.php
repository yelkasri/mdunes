<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Role_Usersort_type extends WPicklist {
function create(){
$sql=WModel::get('role'); 
$parent=array();
$parent['pkey']='rolid';
$parent['parent']='parent';
$parent['name']='name';
$sql->makeLJ('roletrans','rolid');
$sql->whereLanguage(1);
$sql->select('name', 1);
$sql->orderBy('lft','ASC');
$sql->select('rolid');  
$sql->select('parent');
if($this->onlyOneValue()){
if(!empty($this->defaultValue)){
$sql->whereE('rolid',$this->defaultValue );
$result=$sql->load('o');
if(!isset($result->rolid)){
$message=WMessage::get();
$message->codeE('Could not find the role id "'.$this->defaultValue .'"');
$this->addElement($this->defaultValue, $this->defaultValue );
return true;
}
$this->addElement($result->rolid, $result->name);
}
return true;
}
$sql->where('type','!=','2');
$myitems=$sql->load('ol');
$childOrderParent=array();
$list=WOrderingTools::getOrderedList($parent, $myitems, 1, false, $childOrderParent );
foreach($list as $itemList){
$this->addElement($itemList->rolid, $itemList->name);
}
}
}