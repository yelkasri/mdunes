<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
/*
 * This code was borrowed from SimplePie and
 * then adapted to our needs and thus
 * released under the LGPL. See the link below
 * for more information about this license
 * http://www.gnu.org/licenses/lgpl.html
 */
/**
 * Inc_Lib_Encoding_include is to convert strings between charset
 * it is used only when the website is not in utf8 ( eg: Joomla 1.0.x )
 */
class Inc_Lib_Encoding_include {
/**
	 * change the encoding of a string
	 *
	 * @param string $data the string which will be converted
	 * @param string $input the encoding of the string before
	 * @param string $output the encoding of the string you want after
	 * @return string the converted string
	 */
	public static function changeEncoding( $data, $input, $output ) {
		$compatInput = Inc_Lib_Encoding_include::compatibleEncoding($input,'iconv');
		$compatOutput = Inc_Lib_Encoding_include::compatibleEncoding($output,'iconv');
		if ( $compatInput==$compatOutput ) {
			return $data;
		}//endif
		if (function_exists('iconv') && $compatInput && $compatOutput){
			//even tough the //IGNORE should make the iconv function skip illegal character conversion and continue,
			//on some configurations the iconv stops...this little code detect if such an error occurred and in that
			//case discard the result of the iconv and tries another method of conversion
			set_error_handler('Inc_Lib_Encoding_include_error_handler');
			$output = iconv($compatInput, $compatOutput."//IGNORE", $data);
			restore_error_handler();
			if(!Inc_Lib_Encoding_include_error_handler('result')){
				return $output;
			}//endif
		}//endif
		$compatInput = Inc_Lib_Encoding_include::compatibleEncoding($input,'mbstring');
		$compatOutput = Inc_Lib_Encoding_include::compatibleEncoding($output,'mbstring');
		if (function_exists('mb_convert_encoding') && $compatInput && $compatOutput){
			return mb_convert_encoding($data, $compatOutput, $compatInput);
		}
		$compatInput = Inc_Lib_Encoding_include::compatibleEncoding($input,'utf_encode');
		$compatOutput = Inc_Lib_Encoding_include::compatibleEncoding($output,'utf_encode');
		if ($compatInput == 'ISO-8859-1' && $compatOutput == 'UTF-8'){
			return utf8_encode($data);
		} elseif ($compatInput == 'UTF-8' && $compatOutput == 'ISO-8859-1') {
			return utf8_decode($data);
		}//endif
		return $data;
	}//endfct
/**
 * look if a converting method is compatible with an encoding
 *
 * @param string $encoding the encoding name
 * @param string $type the converting method name
 * @return false : not compatible / string the encoding name to give to the function
 */
	public static function compatibleEncoding( $encoding, $type ) {
		static $compatiblity = array();
		$key = $encoding.$type;
		if(!isset($compatiblity[$key])){
			$compatiblity[$key] = Inc_Lib_Encoding_include::encoding($encoding,$type);
		}//endif
		return $compatiblity[$key];
	}//endfct
/**
 * format an encoding name so that we can look it up in our compatibility table
 *
 * @param string $encoding an encoding name
 * @return string the encoding name formatted
 */
	public static function format( $encoding ) {
		static $encodings = array();
		if(!isset($encodings[$encoding])){
			$encodings[$encoding] = str_replace('-','',strtolower($encoding));
		}//endif
		return $encodings[$encoding];
	}//endfct
/**
 * look if a converting method is compatible with an encoding (use the method "compatibleEncoding" which has a small caching system to speed up the lookup)
 *
 * @param string $encoding the encoding name
 * @param string $type the converting method name
 * @return false : not compatible / string the encoding name to give to the function
 */
	public static function encoding( $encoding, $type ) {
		$encodings = array(
			'iconv' => array(
					'armscii'=>'ARMSCII-8',
					'armscii8'=>'ARMSCII-8',
					'ascii'=>'US-ASCII',
					'usascii'=>'US-ASCII',
					'big5'=>'BIG5',
					'950'=>'BIG5',
					'big5hkscs'=>'BIG5-HKSCS',
					'euccn'=>'EUC-CN',
					'eucjisx0213'=>'EUC-JISX0213',
					'eucjp'=>'EUC-JP',
					'eucjpwin'=>'EUCJP-win',
					'euckr'=>'EUC-KR',
					'euctw'=>'EUC-TW',
					'gb18030'=>'GB18030',
					'gb180302000'=>'GB18030',
					'gbk'=>'GBK',
					'georgianacademy'=>'Georgian-Academy',
					'georgianps'=>'Georgian-PS',
					'hz'=>'HZ',
					'iso2022cn'=>'ISO-2022-CN',
					'iso2022cnext'=>'ISO-2022-CN',
					'iso2022jp'=>'ISO-2022-JP',
					'iso2022jp1'=>'ISO-2022-JP-1',
					'iso2022jp2'=>'ISO-2022-JP-2',
					'iso2022jp3'=>'ISO-2022-JP-3',
					'iso2022kr'=>'ISO-2022-KR',
					'iso88591'=>'ISO-8859-1',
					'iso88592'=>'ISO-8859-2',
					'iso88593'=>'ISO-8859-3',
					'iso88594'=>'ISO-8859-4',
					'iso88595'=>'ISO-8859-5',
					'iso88596'=>'ISO-8859-6',
					'iso88597'=>'ISO-8859-7',
					'iso88598'=>'ISO-8859-8',
					'iso88599'=>'ISO-8859-9',
					'iso885910'=>'ISO-8859-10',
					'iso885913'=>'ISO-8859-13',
					'iso885914'=>'ISO-8859-14',
					'iso885915'=>'ISO-8859-15',
					'iso885916'=>'ISO-8859-16',
					'johab'=>'JOHAB',
					'koi8r'=>'KOI8-R',
					'koi8t'=>'KOI8-T',
					'koi8u'=>'KOI8-U',
					'koi8ru'=>'KOI8-RU',
					'macintosh'=>'Macintosh',
					'macarabic'=>'MacArabic',
					'maccentraleurope'=>'MacCentralEurope',
					'maccroatian'=>'MacCroatian',
					'maccyrillic'=>'MacCyrillic',
					'macgreek'=>'MacGreek',
					'machebrew'=>'MacHebrew',
					'maciceland'=>'MacIceland',
					'macroman'=>'MacRoman',
					'macromania'=>'MacRomania',
					'macthai'=>'MacThai',
					'macturkish'=>'MacTurkish',
					'macukraine'=>'MacUkraine',
					'mulelao1'=>'MuleLao-1',
					'shift_jis'=>'Shift_JIS',
					'sjis'=>'Shift_JIS',
					'932'=>'Shift_JIS',
					'shiftjisx0213'=>'Shift_JISX0213',
					'sjiswin'=>'SJIS-win',
					'shift_jiswin'=>'SJIS-win',
					'tcvn'=>'TCVN',
					'tds565'=>'TDS565',
					'tis620'=>'TIS-620',
					'ucs2'=>'UCS-2',
					'utf16'=>'UCS-2',
					'ucs2be'=>'UCS-2BE',
					'utf16be'=>'UCS-2BE',
					'ucs2le'=>'UCS-2LE',
					'utf16le'=>'UCS-2LE',
					'ucs2internal'=>'UCS-2-INTERNAL',
					'ucs4'=>'UCS-4',
					'utf32'=>'UCS-4',
					'ucs4be'=>'UCS-4BE',
					'utf32be'=>'UCS-4BE',
					'ucs4le'=>'UCS-4LE',
					'utf32le'=>'UCS-4LE',
					'ucs4internal'=>'UCS-4-INTERNAL',
					'ucs16'=>'UCS-16',
					'ucs16be'=>'UCS-16BE',
					'ucs16le'=>'UCS-16LE',
					'ucs32'=>'UCS-32',
					'ucs32be'=>'UCS-32BE',
					'ucs32le'=>'UCS-32LE',
					'utf7'=>'UTF-7',
					'viscii'=>'VISCII',
					'cp1250'=>'Windows-1250',
					'windows1250'=>'Windows-1250',
					'win1250'=>'Windows-1250',
					'1250'=>'Windows-1250',
					'cp1251'=>'Windows-1251',
					'windows1251'=>'Windows-1251',
					'win1251'=>'Windows-1251',
					'1251'=>'Windows-1251',
					'cp1252'=>'Windows-1252',
					'windows1252'=>'Windows-1252',
					'1252'=>'Windows-1252',
					'cp1253'=>'Windows-1253',
					'windows1253'=>'Windows-1253',
					'1253'=>'Windows-1253',
					'cp1254'=>'Windows-1254',
					'windows1254'=>'Windows-1254',
					'1254'=>'Windows-1254',
					'cp1255'=>'Windows-1255',
					'windows1255'=>'Windows-1255',
					'1255'=>'Windows-1255',
					'cp1256'=>'Windows-1256',
					'windows1256'=>'Windows-1256',
					'1256'=>'Windows-1256',
					'cp1257'=>'Windows-1257',
					'windows1257'=>'Windows-1257',
					'1257'=>'Windows-1257',
					'cp1258'=>'Windows-1258',
					'windows1258'=>'Windows-1258',
					'1258'=>'Windows-1258',
					'utf'=>'UTF-8',
					'utf8'=>'UTF-8'
			),
			'mbstring' => array(
					'7bit'=>'7bit',
					'8bit'=>'8bit',
					'ascii'=>'US-ASCII',
					'usascii'=>'US-ASCII',
					'base64'=>'BASE64',
					'big5'=>'BIG5',
					'950'=>'BIG5',
					'big5hkscs'=>'BIG5-HKSCS',
					'byte2be'=>'byte2be',
					'byte2le'=>'byte2le',
					'byte4be'=>'byte4be',
					'byte4le'=>'byte4le',
					'euccn'=>'EUC-CN',
					'eucjp'=>'EUC-JP',
					'eucjpwin'=>'EUCJP-win',
					'euckr'=>'EUC-KR',
					'euctw'=>'EUC-TW',
					'936'=>'GB2312',
					'gb2312'=>'GB2312',
					'htmlentities'=>'HTML-ENTITIES',
					'hz'=>'HZ',
					'iso2022jp'=>'ISO-2022-JP',
					'iso2022kr'=>'ISO-2022-KR',
					'iso88591'=>'ISO-8859-1',
					'iso88592'=>'ISO-8859-2',
					'iso88593'=>'ISO-8859-3',
					'iso88594'=>'ISO-8859-4',
					'iso88595'=>'ISO-8859-5',
					'iso88596'=>'ISO-8859-6',
					'iso88597'=>'ISO-8859-7',
					'iso88598'=>'ISO-8859-8',
					'iso88599'=>'ISO-8859-9',
					'iso885910'=>'ISO-8859-10',
					'iso885913'=>'ISO-8859-13',
					'iso885914'=>'ISO-8859-14',
					'iso885915'=>'ISO-8859-15',
					'jis'=>'JIS',
					'koi8r'=>'KOI8-R',
					'shift_jis'=>'Shift_JIS',
					'sjis'=>'Shift_JIS',
					'932'=>'Shift_JIS',
					'sjiswin'=>'SJIS-win',
					'shift_jiswin'=>'SJIS-win',
					'tis620'=>'TIS-620',
					'ucs2'=>'UCS-2',
					'utf16'=>'UCS-2',
					'ucs2be'=>'UCS-2BE',
					'utf16be'=>'UCS-2BE',
					'ucs2le'=>'UCS-2LE',
					'utf16le'=>'UCS-2LE',
					'ucs4'=>'UCS-4',
					'utf32'=>'UCS-4',
					'ucs4be'=>'UCS-4BE',
					'utf32be'=>'UCS-4BE',
					'ucs4le'=>'UCS-4LE',
					'utf32le'=>'UCS-4LE',
					'ucs16'=>'UCS-16',
					'ucs16be'=>'UCS-16BE',
					'ucs16le'=>'UCS-16LE',
					'ucs32'=>'UCS-32',
					'ucs32be'=>'UCS-32BE',
					'ucs32le'=>'UCS-32LE',
					'utf7'=>'UTF-7',
					'utf7imap'=>'UTF7-IMAP',
					'cp1251'=>'Windows-1251',
					'windows1251'=>'Windows-1251',
					'win1251'=>'Windows-1251',
					'1251'=>'Windows-1251',
					'cp1252'=>'Windows-1252',
					'windows1252'=>'Windows-1252',
					'1252'=>'Windows-1252',
					'utf'=>'UTF-8',
					'utf8'=>'UTF-8'
			),
			'utf_encode'=>array(
					'iso88591'=>'ISO-8859-1',
					'utf'=>'UTF-8',
					'utf8'=>'UTF-8'
			)
		);
		$compat = false;
		$encoding = Inc_Lib_Encoding_include::format($encoding);
		if(isset($encodings[$type][$encoding])){
			$compat = $encodings[$type][$encoding];
		}//endif
		return $compat;
	}//endfct
}//endclass
/**
 * log the errors happening in the iconv function in order to know if we can use its result or not
 * @param mixed $errno if the value is the string "result", it return the value of the static var and reinit it
 * @param string $errstr (php internal fills it)
 * @return boolean true: an error occured, false, no error occured
 */
 function Inc_Lib_Encoding_include_error_handler( $errno, $errstr='' ) {
		static $error = false;
		if(is_string($errno) && $errno=='result'){
		  //return the value of the static flag and re init it
		  $tmp = $error;
		  $error = false;
		  return $tmp;
		}
		//set the satic flag
		$error = true;
		//no need to display the notice
		return true;
 }//endfct
