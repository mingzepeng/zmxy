<?php
if (!defined('IN')) die('access deined');
function authcode($string, $operation = 'DECODE', $key = 'jhuser', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}



/**
 *   安全函数，用于对字符串或者数组中特殊字符的转�?
 *   paremeters：string || array
 *   returns:string ||array
 */
function addslashes_deep($filter) 
{
 	if(!isset($filter) || empty($filter))
 	{
 		return $filter;
 	}
 	else
 	{
 		return is_array($filter) ? array_map("addslashes_deep",$filter) : addslashes($filter);
 	}
}



 function stripslashes_deep($filter)
 {
 	if(!isset($filter) || empty($filter))
 	{
 		return $filter;
 	}
 	else
 	{
 		return is_array($filter) ? array_map("stripslashes_deep",$filter) : stripslashes($filter);
 	}
 }


/**
 *   安全函数，用于对字符串或者数组中html字符以及特殊字符的转�?
 *   paremeters：string �? array
 *   returns:string ，array
 */
 function char_cv($filter)
 {
    if(!isset($filter) || empty($filter))
    {
 		return $filter;
 	}
 	else
 	{
 		return is_array($filter) ? array_map("char_cv",$filter) : htmlspecialchars(addslashes($filter));
 	}
}

 /**
 *   安全函数，用于对字符串或者数组中中文字符的编码或解码
 *   paremeters：string �? array
 *   returns:string ，array
 */
 function base64($filter,$opi='ENCODE'){
    if(!isset($filter) || empty($filter)){
 		return $filter;
 	}
 	elseif($opi == 'ENCODE')
 	{

 		if(is_array($filter))
 		{
 		  foreach ($filter as $key => $value) $filter[$key] = base64_encode($value);
 		  return $filter;
 		}
 		else
 		{
 		  return base64_encode($value);
 		}
 	}
 	elseif($opi=='DECODE') 
 	{

 		if(is_array($filter))
 		{
 		  foreach ($filter as $key => $value) $filter[$key] =  base64_decode($value);
 		  return $filter;
 		}
 		else
 		{
 		  return base64_decode($value);
 		}
 	}
 }

 /**
 *   安全函数，用于对字url进行编码和解码
 *   paremeters：string
 *   returns:string
 */
 function urlcode($url,$opi='ENCODE')
 {
    if(!isset($url) || empty($url))
    {
 		return $url;
 	}
 	else
 	{
 		if($opi == 'ENCODE')
 		  return rawurlencode($url);
 		elseif($opi == 'DECODE')
 		  return rawurldecode($url);
 	}
 }
 
 function escape_string($data = array())
 {
  	if(!isset($data) || $data ==='')
 	{
 		return $data;
 	}
 	else
 	{
 		return is_array($data) ? array_map("escape_string",$data) : mysql_escape_string($data);
 	}
 }