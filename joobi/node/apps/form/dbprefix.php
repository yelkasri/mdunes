<?php 


* @license GNU GPLv3 */

class Apps_CoreDbprefix_form extends WForms_default {
function show(){
$this->content=JOOBI_DB_PREFIX;
return true;
}}