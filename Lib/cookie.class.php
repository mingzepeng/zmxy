<?php
if (!defined('IN')) die('access deined');
class cookie
{
	public static $cookiepre = '';
	public static $cookiedomain = '';
	public static $cookiepath = '';
	
	public static function set($var,$value,$life=0,$prefix=1)
	{	
	    if ($prefix)  $var = self::$cookiepre.'_'.$var;
	    $life = $life > 0 ? START_TIME + $life : 0;
	    setcookie($var, $value,$life ,self::$cookiepath ,self::$cookiedomain);
	}
	
	public static function get($var,$prefix=1)
	{
		if ($prefix)  $var = self::$cookiepre.'_'.$var;
		return isset($_COOKIE[$var]) ? $_COOKIE[$var] : null ;
	}
	
	public static function clear($var,$prefix=1)
	{
		if ($prefix)  $var = self::$cookiepre.'_'.$var;
		if (!isset($_COOKIE[$var])) return;
		setcookie($var,'',-86400,self::$cookiepath ,self::$cookiedomain);
	}
}