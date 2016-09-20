<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Default_install_preferences {
public $affiliate='';
public $distrib_server5='';
public $distrib_website='';
public $distrib_website_beta='';
public $distrib_website_beta_time=1429215000;
public $distrib_website_dev='';
public $fresh='';
public $installdebug=0;
public $installdetails=0;
public $install_params=0;
public $install_status=0;
public $license='';
public $showjCenter='';
public $distrib_edit=0;
}
class Role_install_preferences {
public $affiliate='allusers';
public $distrib_server5='allusers';
public $distrib_website='allusers';
public $distrib_website_beta='sadmin';
public $distrib_website_beta_time='allusers';
public $distrib_website_dev='sadmin';
public $fresh='allusers';
public $installdebug='admin';
public $installdetails='admin';
public $install_params='manager';
public $install_status='manager';
public $license='allusers';
public $showjCenter='allusers';
public $distrib_edit='sadmin';
}