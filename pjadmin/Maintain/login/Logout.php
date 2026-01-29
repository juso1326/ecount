<?php
//*****************************************************************************************
//		日期: 20141129
//		程式功能：
//		使用參數：
//*****************************************************************************************
	header ('Content-Type: text/html; charset=utf-8');
	session_start();
//函式庫
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config.ini.php");

//資料庫連線
	if($_SESSION["MemNO"] != ""){
		$_SESSION["MemNO"] = "";
		session_unset();
	}
	header("Location: /pjadmin/Maintain/login/Login.php");
?>