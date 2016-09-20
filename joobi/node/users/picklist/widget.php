<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Widget_picklist extends WPicklist {
function create(){
$this->addElement('name', WText::t('1206732392OZVB'));
$this->addElement('firstname', WText::t('1206732412DABT'));
$this->addElement('lastname', WText::t('1206732412DABX'));
$this->addElement('username', WText::t('1206732411EGRV'));
$this->addElement('email', WText::t('1206961899DDKP'));
return true;
}}