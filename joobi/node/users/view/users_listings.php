<?php 


* @license GNU GPLv3 */

class Users_Users_listings_view extends Output_Listings_class {
function prepareView(){
$mainExit=WExtension::exist('main.node');
if(!$mainExit )  $this->removeElements( array('users_listings_users_ip','users_listings_users_login_report'));
return true;
}}