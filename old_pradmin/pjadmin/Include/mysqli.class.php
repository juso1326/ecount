<?php
	header ('Content-Type: text/html; charset=utf-8');
	class mysql{
		var $num;
		var $link = null;
		var $host = Host;
		var $user_name = UserName;
		var $database = DBName;
		var $passwd = PassWord;
		
		function mysql(){
			$this->link = new mysqli(Host, UserName,PassWord, DBName);
			$this->link->query("SET NAMES 'utf8'");
			return $this->link;
		}
		
		function db_query($str){
			//$this->link = new mysqli(Host, UserName,PassWord, DBName);
			return $this->link->query($str);
		}
		
		function db_num_rows($result){
			return $result->num_rows;
		}
		function db_fetch_array($result) {
			return $result->fetch_array();
		}	
		function db_result($result, $row = 0){
			if ($result && mysqli_num_rows($result) > $row) {
				$array = mysqli_fetch_array($result, MYSQLI_NUM);
				return $array[0];
			}
			return FALSE;
		}			
		function db_close() {
			$this->link->close();
		}
		
		function db_field($str){
			return mysqli_fetch_field($str);
		}
	
	}
    function my_session_start()
    {
        if (isset($_COOKIE['PHPSESSID'])) {
            $sessid = $_COOKIE['PHPSESSID'];
        } else if (isset($_GET['PHPSESSID'])) {
            $sessid = $_GET['PHPSESSID'];
        } else {
            session_start();
            return false;
        }
        
        if (!preg_match('/^[a-z0-9]{32}$/', $sessid)) {
            return false;
        }
        session_start();
        
        return true;
    }	
