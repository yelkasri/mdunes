<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_Storage4listing_picklist extends WPicklist {
function create() {
	$this->addElement( '0', WText::t('1352329012LFYC') );
	$this->addElement( '1', WText::t('1349726930JFTH') );
	$this->addElement( '3', 'Amazon S3' );
	$this->addElement( '5', 'DropBox' );
	return true;
}}