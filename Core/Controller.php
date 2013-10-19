<?php
class Controller extends Core 
{
	
	public $_name = 'Controller';
	
	public static $app = null;
	
	public static function init()
	{
		import('Common','func');
		import('Security','func');
		import('Out');
		
		//输入数据转义设置
		@set_magic_quotes_runtime(0);
		
		//对GPC进行转义
		if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
		{
			$_POST   = escape_string(stripslashes_deep($_POST));
			$_GET    = escape_string(stripslashes_deep($_GET));
			$_COOKIE = escape_string(stripslashes_deep($_COOKIE));
			$_FILES  = escape_string(stripslashes_deep($_FILES));
		}
		else
		{
			$_POST   = escape_string($_POST);
			$_GET    = escape_string($_GET);
			$_COOKIE = escape_string($_COOKIE);
			$_FILES  = escape_string($_FILES);				
		}
	}
	
	public static function Action($app=null,$action=null)
	{
		$app = (is_null($app)) ?  APP.'App' : $app.'App';
	    $action = (is_null($action)) ?  ACTION.'Action' : $action.'Action';
	    $app_path = ROOT.'/'.APP_DIR.'/'.$app.'.php';
		
	    (!is_file($app_path)) && exit('不存在此应用程序');
	    
	    include_once($app_path);
	    
		$app_class = (strrchr($app,'/') !== false) ? substr(strrchr($app,'/'),1) : $app ;
		
	    if (class_exists($app_class)) 
	        self::$app = new $app_class;
	    else 
	        exit('不存在此应用程序相对应的类程序，请先创建');
		
	    if (!method_exists(self::$app,$action))
	        exit('此应用程序的对象不存在此方法，请先创建');

	    self::$app->$action();
	}
	
	public static function run($app=null,$action=null)
	{
		self::init();
		//import('AppInit','inc');

		$module = (defined('MODULE')) ? MODULE : DEFAULT_MODULE;
		
		import($module."Init",'inc');

		App::$view = new View();
		App::$view->setModule($module);
		if (is_dir(ROOT.'/'.THEME_DIR))
		{
			App::$view->template_dir = THEME_DIR;
			App::$view->setConfig('common_dir',THEME_DIR.'/common');
			App::$view->setConfig('common_css_dir',THEME_DIR.'/common/css');
			App::$view->setConfig('common_js_dir', THEME_DIR.'/common/js');
			App::$view->setConfig('common_image_dir', THEME_DIR.'/common/images');
		}	
		self::Action($app,$action);
		M(null,'clear');
	}
}