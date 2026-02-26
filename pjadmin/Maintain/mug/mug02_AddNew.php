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
	LoginChk(GetFileCode(__FILE__),"2");
//參數
	$GetFileCode = GetFileCode(__FILE__);

//資料庫連線
	$NewSql = new mysql();	
	
	$RowArr = array('memm01_numid', 'memm01_nm', 'memm01_nick', 'memm01_loginid', 'memm01_pwd', 'memm01_open', 'MUG01_NO');
	$Arr    = array(
		xRequest('memm01_numid'),
		xRequest('memm01_nm'),
		xRequest('memm01_nick'),
		xRequest('memm01_loginid'),
		xRequest('memm01_pwd'),
		xRequest('memm01_open'),
		xRequest('MUG01_NO'),
	);

	//新增資料
	Insert($NewSql,"mem_m01",$RowArr,$Arr);
	
	header("Location: " . $GetFileCode . ".php");
?>