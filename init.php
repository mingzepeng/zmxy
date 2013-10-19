<?php
error_reporting(E_ALL);

define('IN',true);

session_start(); 

include('Config/config.php');

//设置区域时间
date_default_timezone_set(DEFAULT_TIME_ZONE); 

define('START_TIME',time());

//设置根目录
define('ROOT',dirname(__FILE__));

//设置日期
define('DATE',date('Y-m-d',START_TIME));

define('TIME',date('H:i:s',START_TIME));

define('DATETIME',DATE.' '.TIME);

//加载必备文件
include('Core/Core.php');
include('Core/Db/dbmysql.class.php');
include('Core/Model.php');
include('Core/View.php');
include('Core/App.php');
include('Core/Controller.php');
include('Lib/Core.func.php');
