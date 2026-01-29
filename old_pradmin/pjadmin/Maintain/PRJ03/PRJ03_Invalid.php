<?php
//*****************************************************************************************
//		日期: 20141120
//		程式功能：
//		使用參數：
//*****************************************************************************************
	header ('Content-Type: text/html; charset=utf-8');
	session_start();
//函式庫
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config.ini.php");
	LoginChk(GetFileCode(__FILE__),"3");
//參數
	$GetFileCode = GetFileCode(__FILE__);
	$DataKey = xRequest("DataKey");
	$PageCon = xRequest("PageCon");	
	$Source = xRequest("Source");
		
//資料庫連線
	$NewSql = new mysql();	
	
	if($DataKey != ""){
		//更新狀態
		/*
		$Sql = " 
			Update in_m01 Set
			inm01_type = '4'
			where inm01_no = '$DataKey'
		";
		*/
		$Sql = "
			Delete From in_m01
			where inm01_no = '$DataKey'
		";
		$NewSql -> db_query($Sql) or die("SQL ERROR 1");
		
		$Sql = "
			Delete From in_t01
			where inm01_no = '$DataKey'
		";
		$NewSql -> db_query($Sql) or die("SQL ERROR 2");	
			
		//清除薪資
		$Sql = "
			Delete From pay_t02
			where int01_no in (Select int01_no  From in_t01 where inm01_no = '$DataKey')
			and payt02_type = 'I' 
			and payt02_paydate = '' 
		";
		$NewSql -> db_query($Sql) or die("SQL ERROR 3");		
	}

	if($Source == ""){
		header("Location: " . $GetFileCode . ".php?" . $PageCon . "");
	}else{
		echo '<script>parent.window.location.reload();parent.Shadowbox.close();</script>';
	}
?>