<?php
if (!defined('IN')) die('access deined');
class Template
{
	public static $config = array();
	
	public static function import($files)
	{	
		$dir = self::$config['common_dir'];
		if(!is_array($files)) $files = explode(',',$files);
		foreach ($files as $file)
		{
			$index = strrpos($file,'.');
			$type  = substr($file,$index+1);
			if ($type === 'css')
				echo '<link href="'.$dir.'/'.$file.'" rel="stylesheet" type="text/css" />',"\r\n";
			elseif ($type === 'js')
			    echo '<script type="text/javascript" src="'.$dir.'/'.$file.'"></script>',"\r\n";
		}
	}
	
	public static function importCommonJS($js)
	{
		if(!is_array($js)) $js = explode(',',$js);
		foreach ($js as $j) {
			$j .= '.js';
			echo '<script type="text/javascript" src="'.self::$config['common_js_dir'].'/'.$j.'"></script>'."\r\n";
		}
	}
	
	public static function importCommonCSS($css)
	{
		if(!is_array($css)) $css = explode(',',$css);
		foreach ($css as $cs) {
			$cs .= '.css';
			echo '<link href="'.self::$config['common_css_dir'].'/'.$cs.'" rel="stylesheet" type="text/css" />'."\r\n";
		}
	}
	
	public static function importCommonIMG($img)
	{
		echo self::$config['common_image_dir'].'/'.$img;
	}
	

	public static function importJS($js)
	{
		if(!is_array($js)) $js = explode(',',$js);
		foreach ($js as $j) {
			$j .= '.js';
			echo '<script type="text/javascript" src="'.self::$config['js_dir'].'/'.$j.'"></script>'."\r\n";			
		}
	}
	
	public static function importCSS($css)
	{
		if(!is_array($css)) $css = explode(',',$css);
		foreach ($css as $cs) {
			$cs .= '.css';
			echo '<link href="'.self::$config['css_dir'].'/'.$cs.'" rel="stylesheet" type="text/css" />'."\r\n";
		}
	}

	public static function importIMG($img)
	{
		echo self::$config['image_dir'].'/'.$img;
	}
}