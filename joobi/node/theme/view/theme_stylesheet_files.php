<?php 


* @license GNU GPLv3 */

class Theme_Theme_stylesheet_files_view extends Output_Listings_class {
function prepareQuery(){
$tmid=WGlobals::getEID();
$themeC=WClass::get('theme.helper');
$objData=$themeC->getFiles($tmid, 'css', true);
if(!empty($objData))$this->addData($objData );
else return false;
return true;
}}