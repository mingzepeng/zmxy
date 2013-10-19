<?php
class indexApp extends App
{
	public $_name = 'index';

	public function __construct()
	{
		parent::__construct();
		if(self::$view)
		{
			$module_dir = self::$view->template_dir.'/'.self::$view->module;
			$this->setConfig('css_dir',$module_dir.'/css');
			$this->setConfig('js_dir',$module_dir.'/js');
			$this->setConfig('image_dir',$module_dir.'/images');
		}
	}

	public function indexAction()
	{
		$m1 = M('candidate');
		$m2 = M('votes');
		$candidates = $m1->where("display_order is not null")->order('display_order')->find();
		$datas = $m2->dbstuff->fetch('select count(ip) as count,candidate_id from jh_votes  GROUP BY candidate_id');
		$results = array();
		if(!empty($datas)) foreach ($datas as $data) $results[$data['candidate_id']] = $data['count'];
		$sum = array_sum($results);
		foreach ($candidates as &$c) 
		{
			$id = $c['id'];
			$c['count'] = 0;
			if(isset($results[$id])){
				$c['count'] = (int)$results[$id];
			}

		}
		$this->assign('sum',$sum);
		$this->assign('candidates',$candidates);
		$this->display('index');
	}

	public function voteAction()
	{
		if( isset($_POST['candidates']))
		{
			$candidates = $_POST['candidates'];
			$count = count($candidates);
			if($count < 8 || $count > 10) 
			{
				Out::ajaxError('评选的候选人不得少于8人、不得多于10人');
			}
			else if(empty($_SESSION) || !isset($_POST['code']) || $_SESSION['code'] !== $_POST['code'])
			{
				Out::ajaxError('验证码不正确，请重新填写');
			}
			else
			{
				$m = M('votes');
				$time = time();
				$table = DB_TABLE_PRE . 'votes';
				$ip = getIP();
				$result = $m->where("ip = '$ip'")->order('add_time desc')->findone();

				if(!empty($result) && $result['add_time'] + 24*60*60 > $time) 
					Out::ajaxError('一个ip24小时内只能投一次票！');

				$sql = "insert into {$table} values";// ('','')"
				$values = array();
				foreach ($candidates as $c) 
				{
					$values[] = "('{$ip}','{$c}','$time')";
				}
				$sql .= implode($values, ',');
				$result = $m->query($sql);
				if($result !== false) 
					Out::ajaxSuccess('投票成功，感谢您的投票,谢谢！');
				else
					Out::ajaxError('投票失败，请重试！');
			}
		}
		else
		{
			Out::ajaxError('评选的候选人不得少于8人、不得多于10人');
		}
		
	}
}