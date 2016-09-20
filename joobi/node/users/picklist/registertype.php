<?php 


* @license GNU GPLv3 */

class Users_Registertype_picklist extends WPicklist {
function create(){
$itemTypeM=WModel::get('contacts.type');
$itemTypeM->remember('contacts_type_picklist', true);
$itemTypeM->makeLJ('contacts.typetrans','utypid');
$itemTypeM->select('name', 1 );
$itemTypeM->whereLanguage(1);
$itemTypeM->whereE('publish', 1 );
$itemTypeM->orderBy('ordering');
$itemTypeM->orderBy('type');
$itemTypeM->setLimit( 500 );
$productTypesA=$itemTypeM->load('ol',array('utypid','namekey','type','color'));
if(!empty($productTypesA )){
foreach($productTypesA as $productType){
$this->addElement($productType->utypid, $productType->name, array('color'=> $productType->color ));
}
}
return true;
}
}