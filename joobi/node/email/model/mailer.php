<?php 


* @license GNU GPLv3 */

class Email_Mailer_model extends WModel {
function validate(){
if($this->type < 100){
$this->designation=1;
}elseif($this->type > 100){
$this->designation=2;
}
return true;
}
}