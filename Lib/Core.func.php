<?php
if (!defined('IN')) die('access deined');
function newClass($class)
{
    static $classes = array();
    if (array_key_exists($class,$classes)) return $classes[$class];
	
    $class_path = ROOT.'/Lib/'.$class.'.class.php';
    if (!is_file($class_path)) return false;
	
	include_once $class_path;
	
    $pos = strrpos($class,'/');
    if ($pos !== false)
        $instance = substr($class,$pos+1);
    else 
        $instance = $class; 
	if(!class_exists($instance)) return false;
	
    $classes[$class] = new $instance;
    return $classes[$class];
}

// static class
function import($class,$type='class')
{
	static $classes = array();
	$class = $class.'.'.$type.'.php';
	if (in_array($class,$classes)) return true;
	
    $class_path = ROOT.'/Lib/'.$class;
    if (!is_file($class_path)) return false;
	
    include_once($class_path);
    $classes[] = $class;
	return true;
}

function M($model=null,$type=null)
{
	static $models = array();
	if($type === 'clear')
	{
		if(isset($model)) 
		{
			if(isset($models[$model])) unset($models[$model]);
		}
		else
		{
			$models = array();
		}
		return;
	}
	if (!isset($model))
	{
		if(!isset($models[0])) $models[0] = new Model();
		return $models[0];
	}
	else 
	{
		if(!isset($models[$model]))
		{
			$modelfile = ROOT.'/'.MODEL_DIR.'/'.$model.'Model.php';
			if(is_file($modelfile))
			{
				include_once($modelfile);
				$modelclass = $model.'Model';
				$models[$model] = new $modelclass;
			}
			else
			{
				$models[$model] = new Model($model);
			}
		}
		return isset($models[$model]) ? $models[$model] : null ;
	}
}

function U($app='',$action='',$param = array(),$enter='index')
{
	$url = '';
	$params = '';
	($app === '')    && $app = 'index';
    ($action === '') && $action = 'index';
	$app = 'app='.$app;
	$action = 'action='.$action;
	if (is_array($param))
	{
		foreach ($param as $key=>$value)  $params.= '&'.$key.'='.$value;
	}
	$enter .= '.php?';
	$url = $enter.$app.'&'.$action.$params;
	return $url;
}

function url($app,$action,$param = array(),$enter='index')
{
    echo U($app,$action,$param,$enter);   
}