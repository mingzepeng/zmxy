<?php
/* 一个数据库操作的类
   实现功能: 连接数据库
            数据库查询
			  获取一条数据
			  获取多条数据
			修改数据
			插入数据
*/
class dbmysql
{
    private $link;
    private $db_host;   
    private $db_user;
    private $db_passwd;
    private $db_name;
    private $db_charset;
    private $pconnect;
    public  $result = null;
    static  $Instance = null;
    static  $sqls = array();
    static  $sqllog = array();
  
    private function __construct($host,$user,$password,$dbname,$charset='utf8',$pconnect=0)
    {
        $this->db_host = $host;
      	$this->db_user = $user;
      	$this->db_passwd = $password;
      	$this->db_name = $dbname;
      	$this->db_charset = $charset;
      	$this->pconnect = $pconnect;
    }

    public function __destruct()
    {
        if(self::$Instance !== null)
          mysql_close($this->link);  
    }
  
    static function getInstance($host,$user,$password,$dbname,$charset='utf8',$pconnect=0)
    {
        if(self::$Instance == null) self::$Instance = new dbmysql($host,$user,$password,$dbname,$charset,$pconnect);
        return self::$Instance; 
    }
  
    public function connect()
    {
      	if ($this->pconnect == 1) 
      	   $this->link = mysql_pconnect($this->db_host,$this->db_user,$this->db_passwd);
      	else
      	   $this->link = mysql_connect($this->db_host,$this->db_user,$this->db_passwd,true);
        if (!$this->link) $this->err('无法连接数据库:'.mysql_error());
        if (!mysql_select_db($this->db_name,$this->link)) $this->err('选择数据库出错:'.mysql_error());
    	  mysql_set_charset($this->db_charset,$this->link); 
        //mysql_query("set names '{$this->db_charset}' ",$this->link);
    }
  
    public function err($error)
    {
        exit($error);
    }
  
    public function query($sql)
    {
    	  if (!isset($sql) || empty($sql))
    	  {
    	  	self::$sqllog[] = "sql:empty";
    	  	return false;
    	  } 
    	  self::$sqls[] = $sql;
    	  $result = mysql_query($sql,$this->link);
    	  if(!$result)
    	  {
    	  	self::$sqllog[] = "mysql_query:error.".mysql_error();
    	  	return false;
    	  }
    	  $this->result=$result;
        return $this->result;
    }
  
    public function num_rows($sql)
    {
      	$result = $this->query($sql);
      	if ($result)
      	{
      		$this->result = $result;
      		return mysql_num_rows($this->result);
      	}	
      	else 
      	{
      		return false;
      	}	
    }

    public function fetchone($sql,$type=MYSQL_ASSOC)
    {
      	$result = $this->query($sql);
      	if($result)
      	{
      		$this->result = $result;
      	    return mysql_fetch_array($this->result,$type);
      	}
        else
        {
        	return  false;
        }
    }
    
    public function fetch($sql=null,$type=MYSQL_ASSOC)
    {
      	 $result = $this->query($sql);
      	 if($result)
      	 {
      	 	$this->result = $result;
      	 	$rows=array();
      	    while ($row = mysql_fetch_array($this->result,$type))  array_push($rows,$row); 	    
      	    return $rows;
      	 }
      	 else 
      	 {
      	   return false;
      	 }
    }
    
    public function insert_id() 
    {
      	return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
    }
    
    public function affected_rows()
    {
    	  return mysql_affected_rows($this->link);
    }
    
    public function getSqls()
    {
    	  return self::$sqls;
    }
    
    public function getLastSql()
    {
      	$sqls = self::$sqls;
      	$count = count($sqls);
      	return ($count > 0) ?$sqls[$count-1] : null ;
    }

}