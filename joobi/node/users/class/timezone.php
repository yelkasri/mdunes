<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Users_Timezone_class extends WClasses {
 function getTimeZoneKey($timeZoneString) 
 {
  $dateTimeZoneUTC=new DateTimeZone("Europe/London");
  $dateTimeZoneUser=new DateTimeZone($timeZoneString);
 $dateTimeUTC=new DateTime("now", $dateTimeZoneUTC);
$dateTimeUser=new DateTime("now",$dateTimeZoneUser);
$offset=$dateTimeZoneUser->getOffset($dateTimeUTC) - $dateTimeZoneUTC->getOffset($dateTimeUTC);
$time=gmdate('U');
 $trasitions=$dateTimeZoneUser->getTransitions($time);
 if(!empty($trasitions))
 {
 foreach($trasitions as $transition)
 {
 if(!$transition['isdst'])
 {
 $offset=$transition['offset'];
 break;
 }
 }
 }
$key=$offset/60;
 return $key;
 } 
 }