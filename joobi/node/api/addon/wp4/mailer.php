<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
require_once ABSPATH . WPINC.'/class-phpmailer.php';
class Joobi_Mailer extends PHPMailer {
protected $_validMailer=true;
}