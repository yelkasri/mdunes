<?php 

## Generate Captcha image link
	define( '_JEXEC', 1 );
	define( 'JPATH_BASE', dirname(dirname(dirname(dirname(dirname(__FILE__))))));
	define( 'DS', DIRECTORY_SEPARATOR );
	
	require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
	require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
	
	## Instantiate the application.
	$app = JFactory::getApplication('site');
	
	## Starting a session.
	$session =& JFactory::getSession();

	## Setting the font.
    $font       = dirname(__FILE__)."/TravelingTypewriter.ttf";
    $width      = 90;
    $height     = 35;
    $characters = 5;

    ## Start output buffering
    if (ob_get_length() === false) {
       ob_start();
    }

	## List all possible characters, similar looking characters and vowels have been removed
	$possible   = '23456789bcdfghjkmnpqrstvwxyz';
	$code       = '';
	$i          = 0;
	
	while ($i < $characters) {
		$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
		$i++;
	}
	
    ## Font size will be 50% of the image height
    $font_size          = $height * 0.5;
    $image              = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');

    ## Set the colors
    $background_color   = imagecolorallocate($image, 255, 255, 255);
    $text_color         = imagecolorallocate($image, 20, 40, 100);
    $noise_color        = imagecolorallocate($image, 255, 255, 255);

    ## Generate random dots in background
    for( $i=0; $i<($width*$height)/3; $i++ ) {
        imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
    }

    ## generate random lines in background
    for( $i=0; $i<($width*$height)/150; $i++ ) {
        imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
    }

    ## Create textbox and add text
    $textbox = imagettfbbox($font_size, 0, $font, $code)  or die('Error in imagettfbbox function');
    $x = ($width - $textbox[4])/2;
    $y = ($height - $textbox[5])/2;
    imagettftext($image, $font_size, 0, $x, $y, $text_color, $font , $code) or die('Error in imagettftext function');

	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

    ## output captcha image to browser
    header('Content-Type: image/jpeg');
    imagejpeg($image);
    imagedestroy($image);

    ## Set session variable for newly created code
    $session->set('security_code', md5($code));

    ob_end_flush();
    exit();
