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
//資料庫連線
	$NewSql = new mysql();	
	
	if($DataKey != ""){
		//更新狀態
		$Sql = "
			Delete From prj_m01
			where prjm01_no  = '$DataKey'
		";
		$NewSql -> db_query($Sql) or die("SQL ERROR 1");
		
		$Sql = "
			Delete From prj_t01
			where prjm01_no  = '$DataKey'
		";
		$NewSql -> db_query($Sql) or die("SQL ERROR 2");
		
	}
	header("Location: " . $GetFileCode . ".php?" . $PageCon);
?>