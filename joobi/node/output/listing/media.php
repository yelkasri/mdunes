<?php 


* @license GNU GPLv3 */

class WListing_Coremedia extends WListings_default{
function create(){
if(empty($this->value)) return '';
$filesMediaC=WClass::get('files.media');
$this->content=$filesMediaC->renderHTML($this->value, $this->element );
return true;
}
public function advanceSearch(){
return false;
}
public function searchQuery(&$model,$element,$searchedTerms=null,$operator=null){
}
}