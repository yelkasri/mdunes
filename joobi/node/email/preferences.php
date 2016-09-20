<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Default_email_preferences {
public $enable=1;
public $adminnotif='';
public $allowbyurl=0;
public $allowbyurlpwd='';
public $charset='utf-8';
public $displayname=1;
public $dkimkeyloc='';
public $dkimpassphrase='';
public $embedimages=0;
public $encoding='8bit';
public $fromemail='from@yoursite.com';
public $fromname='Your website name';
public $hostname='';
public $keep_email=1;
public $mailer='phpmail';
public $mailerdkimyesno=0;
public $multiplepart=1;
public $process_last='';
public $process_next='';
public $queue_max_email=60;
public $replyemail='';
public $replyname='Your website name';
public $senderemail='';
public $sendmailpath='/usr/sbin/sendmail';
public $smsenable=0;
public $smssplit=0;
public $smtp_auth_required=0;
public $smtp_host='localhost';
public $smtp_password='';
public $smtp_port=25;
public $smtp_secure='';
public $smtp_username='';
public $statistics_clean=91;
public $statistics_link=0;
public $statistics_logfail=0;
public $statistics_peruser=0;
public $statistics_read=0;
public $statistics_sent=0;
public $usecms=1;
public $usenotification=1;
public $wordwrap=200;
public $type='html';
}
class Role_email_preferences {
public $enable='sadmin';
public $adminnotif='sadmin';
public $allowbyurl='sadmin';
public $allowbyurlpwd='sadmin';
public $charset='admin';
public $displayname='admin';
public $dkimkeyloc='admin';
public $dkimpassphrase='admin';
public $embedimages='admin';
public $encoding='admin';
public $fromemail='admin';
public $fromname='admin';
public $hostname='admin';
public $keep_email='listmanager';
public $mailer='admin';
public $mailerdkimyesno='admin';
public $multiplepart='admin';
public $process_last='allusers';
public $process_next='allusers';
public $queue_max_email='listmanager';
public $replyemail='admin';
public $replyname='admin';
public $senderemail='admin';
public $sendmailpath='admin';
public $smsenable='sadmin';
public $smssplit='admin';
public $smtp_auth_required='admin';
public $smtp_host='admin';
public $smtp_password='admin';
public $smtp_port='admin';
public $smtp_secure='admin';
public $smtp_username='admin';
public $statistics_clean='admin';
public $statistics_link='admin';
public $statistics_logfail='admin';
public $statistics_peruser='admin';
public $statistics_read='admin';
public $statistics_sent='admin';
public $usecms='admin';
public $usenotification='sadmin';
public $wordwrap='admin';
public $type='admin';
}
