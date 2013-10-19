<?php
/**
 * -------------------------------------------
 * 模型类，实现了基本的CURD，数据过滤操作
 * --------------------------------------------
 * @author pmz(mingzepeng@gmail.com)
 * @version 1.2  2012.6.30
 */
class Model extends Core
{
	public $_name = 'Model';
	//数据库操作对象
	public $dbstuff = null;

	//数据库表前缀
	public $tablepre = '';
	//数据库表
	public $table = '';
	//表主键
	public $pk = null;
	//表字段
	public $fields = array();
	
	//表数据信息
	public $data = array();
	
	//构成sql的信息
	public $option = array();
	
	public $backdata = array();
	
	//是否自动检测数据
	public $autocheck = 0;
	//要检测的字段的检测方式
	public $validate = array();
	
	//是否自动调用函数或方法过滤数据
	public $auto_input_filter = 0;
	public $input_filter = array();
	
	public $auto_output_filter = 0;
	public $output_filter = array();
	
	public $debug = 0;
	
	
	/**
	 * 构造函数
	 * 
	 */ 
	public function __construct($table='')
	{
		$this->dbstuff = dbmysql::getInstance(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME,DB_CHARSET,PCONNECT);
		$this->dbstuff->connect();
		if(defined('DB_TABLE_PRE')) $this->tablepre = DB_TABLE_PRE;
		$this->setTable($table);
	}
	
	public function __destruct()
	{
	    if($this->debug==1)
	    {
	    	var_dump($this);
	    }
	    $this->dbstuff = null;
	}
	
	public function __set($var,$value)
	{
		$this->set($var,$value);
	}
	
    public function __get($var)
	{
		return $this->get($var); 
	}
	
	public function setTable($table='')
	{
	    if ($table === '') return false;
	    $this->table = $table;
	    return $this;
	}
	
	public function create($data=array())
	{
		$this->data = empty($data) ? $_POST : $data;
		$this->auto_input_filter && $this->inputFilter();
		$this->autocheck && $this->_validate();	
		return $this;
	}
	
	public function clear()
	{
		$this->backdata[] = array('data'=>$this->data,'option'=>$this->option);
		$this->data   = array();
		$this->option = array();
		return $this;
	}
	
    public function query($sql)
    {
        return $this->dbstuff->query($sql);	
    }
    
	public function find($pk=array())
	{
		if($this->table === '' ) return false;
		$table = $this->tablepre.$this->table;
		$field = (isset($this->option['field']) && !empty($this->option['field'])) ? implode(',',$this->option['field']) : '*';
		$join = '';
		$where = '';
		$condition = '';
		$condition2 = '';
		if (isset($this->option['join']['matchfield']) && isset($this->option['join']['table']))
		{
			if ($field != '*') 
			{
				if (!empty($this->option['join']['field'])) $field.=','.implode($this->option['join']['field']);
			}
			$join = ' '.$this->option['join']['type'].' join '.$this->option['join']['table'].' on '.$this->table.'.'.$this->option['join']['matchfield'][0].'='.$this->option['join']['table'].'.'.$this->option['join']['matchfield'][1].' ';
		}

		if (!empty($this->option['condition']))
		{
		    $condition = '('.$this->option['condition'].')';
		}

		if (isset($this->pk))
		{
			if(count($pk)>0)
			{
				$pk = array_unique($pk);
				foreach ($pk as $key => $value) {
					$pk[$key] = "'".$value."'";
				}
				$pk ='('.implode(',',($pk)).')';
				$condition2 = "({$this->pk} in {$pk})";				
			}
		}

		($condition !== '')  && $where = ' where '.$condition;
		if ($condition2 !== '')
		{
			if ($where !== '')
			    $where .= ' and '.$condition2.' ';
			else 
			    $where = ' where '.$condition2.' ';
		}

		$order = (isset($this->option['order']) && !empty($this->option['order']) ) ? ' order by '.$this->option['order'] : '';
		$limit = (isset($this->option['limit']) && !empty($this->option['limit']) ) ? ' limit '. $this->option['limit'] : '';
		$sql = "select {$field} from {$table} {$join} {$where} {$order} {$limit}";
		$result = $this->dbstuff->fetch($sql);

		$this->clear();
		if ($this->auto_output_filter && $result !== false) 
		{
		    foreach ($result as $key=>$value) $result[$key] = $this->outputFilter($value);
		}
		return $result;
	}
	
	public function findone($pk=null)
	{
		if($this->table === '' ) return false;
		$table = $this->tablepre.$this->table;
		$field = (isset($this->option['field']) && !empty($this->option['field'])) ? implode(',',$this->option['field']) : '*' ;
		$join = '';
		$condition  = '';
		$condition2 = '';
		$where = '';
		if (isset($this->option['join']['matchfield']) && isset($this->option['join']['table']))
		{
			if ($field != '*') 
			{
				if (!empty($this->option['join']['field'])) $field.=','.implode($this->option['join']['field']);
			}
			$join = ' '.$this->option['join']['type'].' join '.$this->option['join']['table'].' on '.$this->table.'.'.$this->option['join']['matchfield'][0].'='.$this->option['join']['table'].'.'.$this->option['join']['matchfield'][1].' ';
		}
		$order = (isset($this->option['order']) && !empty($this->option['order']))?  ' order by '.$this->option['order'] : '';
		$limit =  ' limit 1';

		if (isset($this->option['condition']))
		    $condition = ' ('.$this->option['condition'].') ';

		if (isset($this->pk) && isset($pk))
		    $condition2 = ' ('.$this->pk."='".$pk."')";
		    
		($condition !== '') && $where = ' where '.$condition;
		if ($condition2 !== '')
		{
			if ($where !== '')
			    $where .= ' and '.$condition2.' ';
			else 
			    $where  = ' where '.$condition2.' ';
		}
        
		$sql = "select {$field} from {$table} {$join} {$where} {$order} {$limit}";
		$result = $this->dbstuff->fetchone($sql);
		$this->clear();
		if ($this->auto_output_filter)  $result = $this->outputFilter($result);
		return $result;
	}
	
	public function save($pk=null)
	{
		if($this->table === '' ) return false;
		$table = $this->tablepre.$this->table;
		$condition = '';
		$condition2 = '';
		$where = '';
		$kv = '';

		if (isset($this->option['condition']))
		{
			$condition = '('.$this->option['condition'].')';
		}

		if (isset($this->pk) && isset($pk))
		{
		    if(is_array($pk))
			{
				if(count($pk)>0)
				{
					$pk = array_unique($pk);
					foreach ($pk as $key => $value) {
						$pk[$key] = "'".$value."'";
					}
					$pk ='('.implode(',',($pk)).')';
					$condition2 = "({$this->pk} in {$pk})";					
				}
			}
			else
			{
				$condition2 = "({$this->pk} = '{$pk}')";
			}
		}

		($condition !== '') && $where = ' where '.$condition;
		if ($condition2 !== '')
		{
			if ($where !== '')
			    $where .= ' and '.$condition2.' ';
			else 
			    $where  = ' where '.$condition2.' ';
		}

		foreach ($this->data as $key=>$value) $kv .= $key.'='."'".$value."'".',';
		$kv = substr($kv,0,-1);
		$sql ="update {$table} set {$kv} {$where}";
		$result = $this->dbstuff->query($sql);
		$this->clear();
		return $result;
	}
	 
	public function delete($pk=null)
	{
		if($this->table === '' ) return false;
		$table = $this->tablepre.$this->table;
		$condition = '';
		$condition2 = '';
		$where = '';
		if (isset($this->option['condition']))
		{
			$condition = '('.$this->option['condition'].')';
		}

		if (isset($this->pk) && isset($pk))
		{
		    if(is_array($pk))
			{
				if(count($pk)>0)
				{
					$pk = array_unique($pk);
					foreach ($pk as $key => $value) {
						$pk[$key] = "'".$value."'";
					}
					$pk ='('.implode(',',($pk)).')';
					$condition2 = "({$this->pk} in {$pk})";					
				}

			}
			else
			{
				$condition2 = "({$this->pk} = '{$pk}')";
			}
		}

		($condition !== '') && $where = ' where '.$condition;
		if ($condition2 !== '')
		{
			if ($where !== '')
			    $where .= ' and '.$condition2.' ';
			else 
			    $where  = ' where '.$condition2.' ';
		}	

		$sql = "delete from {$table} {$where}";
		$result = $this->dbstuff->query($sql);
		$this->clear();
		return $result;
	}
	
	public function add()
	{
		if($this->table === '' ) return false;
		$table = $this->tablepre.$this->table;
		if(!isset($this->table) || empty($this->table)) return false;
		$fields = implode(',',array_keys($this->data));
		$values = "'".implode("','",array_values($this->data))."'";
		$sql = "insert into {$table} ($fields) values($values)";
		var_dump($sql);
		$result = $this->dbstuff->query($sql);
		$this->clear();
		return $result;
	}

	public function OutInvalid($info)
	{
		Out::ajaxError($info);
	}
	
	public function _validate($data = array(),$callback='OutInvalid',$errorback='error')
	{
		if(empty($data)) $data = $this->data;
	    if(empty($this->validate)) return true;
	    $rules = $this->validate;
	    foreach ($data as $field => $value){
	        foreach ($rules as $rule){
	            if($field !== $rule[0]) continue;
	            switch ($rule[1]){
	            	case 'require':
	            		if(!isset($value) || $value === '') $this->$callback($rule[3]);
	            		break;
	            	case 'function':
	            		$fun = $rule[2];
	            		if(function_exists($fun)){
	            			if(!$fun($value)) $this->$callback($rule[3]);
	            		}else{
	            			$this->$errorback("function:$fun unavailable");
	            		}
	            	    break;
	            	case 'callback':
	            		$method = $rule[2];
	            		if(method_exists($this,$method)){
	            			if(!$this->$method($value)) $this->$callback($rule[3]);
	            		}else{
	            			$this->$errorback('object:'.__CLASS__." mehtod:$method unavailable");
	            		}
	            		break;
	            	case 'regex':
	            		if(!preg_match($rule[2],$value)) $this->$callback($rule[3]);
	            		break;
	            	case 'equal':
	            		if($value != $rule[2]) $this->$callback($rule[3]);
	            		break;
	            	case 'int':
	            		if(isset($rule[2]) && $rule[2] != '')
	            			$reg = '/^\d{'.$rule[2].'}$/i';
	            		else
	            			$reg = '/^\d+$/i';
	            		if(!preg_match($reg,$value))  $this->$callback($rule[3]);
	            		break;
					case 'char':
	            		if(isset($rule[2]) && $rule[2] != '')
	            			$reg = '/^\w{'.$rule[2].'}$/i';
	            		else
	            			$reg = '/^\w+$/i';

	            		if(!preg_match($reg,$value))  $this->$callback($rule[3]);						
						break;
					case 'length':
						if(isset($rule[2]) && $rule[2] != '')
							$reg = '/^.{'.$rule[2].'}$/i';
						else 
							$reg = '/^.+$/i';
						if(!preg_match($reg,$value))  $this->$callback($rule[3]);
						break;
	            	case 'unique':
	            		if ($this->table === '') $this->$errorback('data table unavailable');
	            		$table = $this->tablepre.$this->table;
						$sql = "select {$field} from {$table} where {$field}='{$value}' ";
	            		$result = $this->dbstuff->num_rows($sql);
	            		if($result > 0) $this->$callback($rule[3]);
	            		break;
					case 'enum':
						if(!in_array($value, $rule[2])) $this->$callback($rule[3]);
						break;
	            }
	    	}
	    }
	}
	
	public function inputFilter($errorback='error')
	{
	    if (empty($this->input_filter)) return ;
	    $this->data = $this->_filter($this->data,$this->input_filter,$errorback);
	}
	
	public function outputFilter($data = array(),$errorback='error')
	{
	    if (empty($this->output_filter)) return $data;
	    return $this->_filter($data,$this->output_filter,$errorback);
	}
	
	public function _filter($data = array(),$rules,$errorback='error')
	{
		
		foreach ($data as $field=>$value)
		{
			foreach ($rules as $rule) 
			{
			    if($field !== $rule[0]) continue;
			    switch ($rule[1])
			    {
			    	case "function":
			    		$fun = $rule[2];
			    		if(!function_exists($fun)) 
			    		    $this->$errorback("function:$fun no exist");
			    		else 
			    		    $data[$field] = $fun($value);
			    		break;
			    	case "callback":
			    		$method = $rule[2];
			    		if(!method_exists($this,$method)) 
			    		    $this->$errorback('object:'.__CLASS__." mehtod:$method no exist");
			    		else 
			    		    $data[$field] = $this->$method($value);
			    		break;
			    }
			}
		}
		return $data;
	}
	
	public function set($var,$value=null)
	{
		if ($var == null) return false;
		if (is_array($var))
		{
			foreach ($var as $key=>$value1) $this->data[$key] = $value1;
		}
		else 
		{
			$this->data[$var] = $value;
		}
		return $this;
	}
	
	public function get($var)
	{
		if(isset( $this->data[$var])) 
		    return  $this->data[$var];
		else
		    return null;
	}
	
	public function field($fields=null)
	{
		if (is_string($fields) && $fields !== '') $fields = explode(',',$fields);
		if (is_array($fields) && !empty($fields) ) 
		{
			if (isset($this->table)) 
			    foreach ($fields as $key=>$field) $fields[$key] = $this->tablepre.$this->table.'.'.$field;
			$this->option['field'] = $fields;
		}
		return $this;
	}
	
	public function select($fields)
	{
		return $this->field($fields);
	}
	
	public function where($condition)
	{
		if(is_string($condition) && !empty($condition)) 
		    $this->option['condition'] = $condition;
		elseif (is_array($condition) && !empty($condition))
		{
			$temp = array();
			foreach ($condition as $key=>$value) $temp[] = $key."='".$value."'";
			$this->option['condition'] = implode(' and ',$temp);
		}
		return $this;
	}
	
	public function order($order=array())
	{
		if(is_array($order) && !empty($order))
		    $this->option['order'] = implode(',',$order);
		elseif (is_string($order) && !empty($order))
		    $this->option['order'] = $order;
		return $this;
	}
	
	public function limit($limit)
	{
		if(is_string($limit) && !empty($limit)) $this->option['limit'] = $limit;
		return $this;
	}
	
	public function join($jontable,$matchfield,$joinfields,$type='inner')
	{
		$this->option['join']['table'] = $this->tablepre.$jontable;
		if(is_array($matchfield)) 
		{
			$this->option['join']['matchfield'] = array_values($matchfield);
		}	    
		else
		{
			$matchfield = explode(',',$matchfield);
			if (!isset($matchfield[1])) $matchfield[1] = $matchfield[0];
			$this->option['join']['matchfield'] = $matchfield;
		}
			
		if(isset($joinfields) && !empty($joinfields))
		{
		    if(!is_array($joinfields)) $joinfields = explode(',',$joinfields);
		    foreach ($joinfields as $key=>$value) $joinfields[$key] = $jontable.'.'.$value;
		    $this->option['join']['field'] = $joinfields;			
		}
		$this->option['join']['type'] = 'inner';
		return $this;
	}
	
	public function insert_id()
	{
		return $this->dbstuff->insert_id();
	}
	
	public function num_rows($sql)
	{
		return $this->dbstuff->num_rows($sql);
	}
	
	public function affected_rows()
	{
		return $this->dbstuff->affected_rows();
	}
}