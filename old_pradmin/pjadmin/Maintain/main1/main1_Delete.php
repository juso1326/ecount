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
	$DataKey = xRequest("comm01_no");
	$PageCon = xRequest("PageCon");
//資料庫連線
	$NewSql = new mysql();
	if($DataKey != ""){
		
		$SqlWhere = " where 1 = 1 ";	
		$SqlKey = "";	
		$SqlWhere .= " and comm01_no = '" . $DataKey . "'";
		$SqlTb = "com_m01";
		if($SqlTb != "" & $SqlWhere != ""){
			Delete($NewSql,$SqlTb,$SqlWhere);
		}
	}
	
	header("Location: " . $GetFileCode . ".php?" . $PageCon);	
?>