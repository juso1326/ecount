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
//參數
//	$GetFileCode = GetFileCode(__FILE__);

//資料庫連線
	$NewSql = new mysql();	
	$TEXT01 = xRequest("TEXT01");


	$Sql = " Update dash_m Set dashm_content = '" . $TEXT01 . "' where dashm_no = '1' ";
	$NewSql -> db_query($Sql) or die("SQL ERROR 1");

//	header("Location:Dashbord.php");
?>