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
	$GetFileCode = GetFileCode(__FILE__);
	$success = true;
	$DataKey = xRequest("DataKey");
	
//資料庫連線
	$NewSql = new mysql();
	
	if($DataKey != ""){
		
		//檢查是否為加扣項
		$Sql = "
			Select foundt01_no, payt02_date
			From pay_t02
			where foundt01_no != ''
			and payt02_no = '$DataKey'
		";
		$foundt01Run = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
		$foundt01 = $NewSql -> db_fetch_array($foundt01Run);		
		
		if($foundt01 != ''){
			$Sql = "
				Insert into found_t04(foundt04_payYM ,foundt01_no)
				Select '" . $foundt01["payt02_date"] . "','" . $foundt01["foundt01_no"] . "'
			";
			$NewSql -> db_query($Sql) or die("SQL ERROR 1");
		}	
		//刪除
		$Sql = " 
			Delete From pay_t02 
			where payt02_no = '$DataKey'
		";
		$NewSql -> db_query($Sql) or die("SQL ERROR");		
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