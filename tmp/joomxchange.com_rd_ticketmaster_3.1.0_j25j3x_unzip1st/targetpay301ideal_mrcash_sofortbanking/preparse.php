<?php 

## Check if there is a token?
$token = $_GET["token"];

## No token! Stop script!
if (!$token) { die(); }

## Set URL
$url = false;

## explode params:
$a = explode (",", $token);

## Loop through prams
foreach ($a as $k) {
	
    list ($_var, $_val) = explode("-", $k, 2);
    $url .= "&$_var=$_val";
}

## Read IDeal Params
foreach ($_GET as $k => $v) {
	
    if ($k!="token")  {
	    $url .= "&$k=$v";
    }
	
 }


## Redirect the user naar de goede URL
$url = 'http://'.$_SERVER['SERVER_NAME'].''.$_SERVER['PHP_SELF'].'?'.substr($url,1);
$url = str_replace('preparse', "index", $url);
header ("Location: $url");