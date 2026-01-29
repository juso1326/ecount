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
	$prjt02_no = xRequest("DataKey");
	$success = true;
	
	if($prjt02_no == ""){
		$success = false;
	}
	
	if($success){
//資料庫連線
	$NewSql = new mysql();
		
		$Sql = " Delete from prj_t02 where prjt02_no = '$prjt02_no'";
		$NewSql -> db_query($Sql) or die("SQL ERROR 1");
	
	}

//關閉資料庫連線
	$NewSql -> db_close();
	header ('Content-Type: text/html; charset=utf-8');
	echo '<Response>';
	if($success){
		echo '<resu>1</resu>';
	}else{
		echo '<resu>0</resu>';		
	}
	echo '</Response>';	
	exit();
?>