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
	$inm01_no = xRequest("DataKey");
	$no = xRequest("no");
	$success = true;
	
	if($inm01_no == ""){
		$success = false;
	}
	
	if($success){
//資料庫連線
	$NewSql = new mysql();
	//刪除收款	
		$Sql = " Delete from in_t01 where int01_no = '$no'";
		$NewSql -> db_query($Sql) or die("SQL ERROR 1");
		
	//更新已付金額
		//-已收金額
		$Sql = " 
			select ifnull(sum(int01_incometotal),0)
			from in_t01
			where inm01_no= '$inm01_no'		
		";
		$sumRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
		$int01_incometotal = $NewSql -> db_result($sumRun);
				
		if($int01_incometotal == ""){$int01_incometotal = 0;}
		
		if($int01_incometotal == 0){
			//更新應收狀態
			$Sql = "
				Update in_m01 Set
				inm01_type = '1'
				where inm01_no= '$inm01_no'	
			";
			$NewSql -> db_query($Sql) or die("SQL ERROR 2");
		}
		//更新
		$Sql = "
			Update in_m01 Set
			inm01_incometotal = " . $int01_incometotal . "
			where inm01_no= '$inm01_no'
		";
		$NewSql -> db_query($Sql) or die("SQL ERROR 3");
		
		//刪除負責人的收入
		$Sql = "
			Delete From pay_t02
			where int01_no = '$no' 
		";
		$NewSql -> db_query($Sql) or die("SQL ERROR 4");		
		
//關閉資料庫連線
	$NewSql -> db_close();		
	}

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