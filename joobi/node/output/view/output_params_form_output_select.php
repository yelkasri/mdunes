<?php 


* @license GNU GPLv3 */

class Output_Output_params_form_output_select_view extends Output_Forms_class {
function prepareView(){
WGlobals::set('Main_Output_params_form_selectype',$this->getValue('selectype'), 'global');
return true;
}}