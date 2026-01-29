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
	$success = true;
	$DataKey = xRequest("DataKey");
	if($DataKey == ""){
		$success = false;
	}

//資料庫連線
	$NewSql = new mysql();
	if($success){
	
		$SqlWhere = " where 1 = 1 ";	
		$SqlKey = "";	
		$SqlWhere .= " and comt01_no = " . $DataKey . "";
		$SqlTb = "com_t01";
		
		if($SqlTb != "" & $SqlWhere != ""){
			Delete($NewSql,$SqlTb,$SqlWhere);

		}
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