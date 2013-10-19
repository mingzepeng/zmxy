<?php
class Core
{
	public $_name = 'Core';
	
	public $log = array();

	public $logFile = ''; 
	
	public $debug = 0;
	
	public function log($msg='')
	{
        $this->log[] = $msg;
	}
	
	public function error($msg)
	{
		exit($msg);
	}
}