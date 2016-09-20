<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Output_Advsearch_class {
private $_elementsA=array();
function createAdvanceSearch($searchFilterA,$elementsA,$buttonsHTML=''){
if(empty($searchFilterA)) return '';
$HTML='';
foreach($searchFilterA as $oneFilter){
$HTML .=$this->_createOneAdvanceSearch($oneFilter );
}
if(!empty($buttonsHTML)){
$HTML .='<div class="advSearchButton">';
$HTML .=$buttonsHTML;
$HTML .='</div>';
}
return $HTML;
}
private function _createOneAdvanceSearch($oneFilter){
$columnInstance=Output_Doc_Document::loadListingElement($oneFilter );
if(empty($columnInstance)) return '';
$status=$columnInstance->advanceSearch();
if(empty($status)) return '';
$HTML='<div class="filterName">'.$oneFilter->name.'</div>';
$HTML .=$columnInstance->content;
$HTML='<div class="panel panel-default advSearchFilter">
<div class="panel-body">'.$HTML.'</div>
</div>';
return $HTML;
}
}
