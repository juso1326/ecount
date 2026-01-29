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
//參數
	$GetFileCode = GetFileCode(__FILE__);
	$TEXT01 = xRequest("TEXT01");
	$TEXT02 = xRequest("TEXT02");
//資料庫連線
	$NewSql = new mysql();
	
	$Sql = " 
		select memm01_no/*,MUG03_NO,memm01_loginid,memm01_pwd*/
		from mem_m01
		where memm01_open = 'Y'
		and memm01_loginid = '$TEXT01'
		and memm01_pwd = '$TEXT02'
		and (lockdate = '' or lockdate = '0000-00-00' or lockdate > '".date('Y-m-d')."' or lockdate is null) 
	";
// print_r($Sql);
// exit();
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$DataKey = $NewSql -> db_result($initRun);
	$initCount = $NewSql -> db_num_rows($initRun);

	$canLogin = true;
	if($initCount != 1){
		$canLogin = false;
	}
	if($canLogin){
		$_SESSION["MemNO"] = $DataKey;

		//記錄登入時間

		$Sql = "
			Update mem_m01 Set
			lastlogin = nowdate,
			nowdate = '".date('Y-m-d H:i:s')."'
			where memm01_no = '$DataKey'
		";
		$NewSql -> db_query($Sql) or die("SQL ERROR login -2");
	}

	if(!$canLogin){
		header('location:/pjadmin/Maintain/login/Login.php?ErrorMsg=無法登入');
		exit();
	}

	header("Location: /pjadmin/Maintain/frame/frame.php");



?>