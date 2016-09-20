<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Api_Wp4_Controller_class extends WClasses {
public function wpRun($identifer){
$content=JoobiWP::slugToApp($identifer );
echo $content;
}
}