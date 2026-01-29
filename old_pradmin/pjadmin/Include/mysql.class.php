<?php
	header ('Content-Type: text/html; charset=utf-8');
class mysql{
	var $num;
	var $link;
	var $host = Host;
	var $user_name = UserName;
	var $database = DBName;
	var $passwd = PassWord;
//	var $debug = false;
	function mysql(){
//		set_time_limit('0');
//		$this -> link = mysql_connect($this -> host, $this -> user_name, $this -> passwd) or die('Connect Error');
//		mysql_select_db($this -> database,$this -> link) or die ('select DB Error');
//		mysql_query('SET NAMES UTF8');

//		$this -> link = mysqli_connect($this -> Host,$this -> UserName,$this -> PassWord,$this -> DBName) or die('Connect Error');
//		$link = mysqli_connect($this -> Host,$this -> UserName,$this -> PassWord,$this -> DBName) or die('Connect Error');
		//mysqli_select_db($link,$this -> DBName) or die ('select DB Error');
		return new mysqli(Host, UserName,PassWord, DBName);
	}
	function mysqli(){
//		set_time_limit('0');
//		$this -> link = mysql_connect($this -> host, $this -> user_name, $this -> passwd) or die('Connect Error');
//		mysql_select_db($this -> database,$this -> link) or die ('select DB Error');
//		mysql_query('SET NAMES UTF8');

//		mysql_select_db($this -> database,$this -> link) or die ('select DB Error');
//		mysql_query('SET NAMES UTF8');
//		return new mysqli($this -> host, $this -> user_name, $this -> passwd, $this -> DBName);
//		return new mysqli(Host, UserName,PassWord, DBName);
		return new mysqli(Host, UserName,PassWord, DBName);
	}	
	function db_query($str){
	  return @mysqli_query(mysql(),$str);
	 //	return query($str);
	}
	function db_field($str){
		return @mysql_fetch_field($str);
	}
	function db_num_rows($res){
		return @mysql_num_rows($res);
	}	
	function db_fetch_array($res) {
		return @mysql_fetch_array($res);
	}
	function db_fetch_row($result){
		return @mysql_fetch_row($result);
	}
	function db_result($result){
		return @mysql_result($result, 0);
	}
	function db_error ($res) {
//		mysql service �M��insert_id()���	  
	$mysql = "select @@ERROR as code";
	$result = @mssql_query($mysql, $con);
	$row = @mssql_fetch_array($result);
	$code = $row["code"]; // error code
	$mysql = "select cast (description as varchar(255)) as errtxt from master.dbo.sysmessages where error = $code and msglangid = 1031"; // german
	$result = @mssql_query($mysql, $con);
	$row = @mssql_fetch_array($result);
	if ($row)
		$text = $row["errtxt"]; // error text (with placeholders)
	else
		$text = "onknown error";
		@mssql_free_result($result);
	return "[$code] $text";
	}
	function db_close() {
		@mysql_close();
	}	
}