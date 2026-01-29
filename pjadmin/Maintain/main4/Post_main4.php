<?php
//*****************************************************************************************
//		日期: 20150325
//		程式功能： 檢查成員是否當日期是否結帳
//		使用參數：
//*****************************************************************************************
	header ('Content-Type: text/html; charset=utf-8');
	session_start();
//函式庫
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config.ini.php");
//參數
	$GetFileCode = GetFileCode(__FILE__);

//資料庫連線
	$NewSql = new mysql();	
	
	$success = true;
	$date = xRequest("date");
	$mem = xRequest("mem");
	$mem_arr = explode("，",$mem);
	if($date == ""){
		$success = false;
	}
	if($success){
		$YM = GetTermYM($NewSql,$date);
		$Y = substr($YM,0,4);
		$M = substr($YM,4,2);
		//檢查當月是否已經付過薪水
		$rtMeg = "";
		for($i = 0;$i <= count($mem_arr);$i ++){
			if($mem_arr[$i] != ""){
				$Sql = "
					Select count(*)
					From found_t03
					where foundt03_year = '$Y'
					and foundt03_month = '$M'
					and memm01_no = '" . $mem_arr[$i] . "'
				";
				//echo '<br>Sql =  ' . $Sql;
				$foun3Run = $NewSql -> db_query($Sql) or die("SQL ERROR");
				$foun3 = $NewSql -> db_result($foun3Run);
				if($foun3 > 0){
					$success = false;
					$rtMeg .= "日期不允許輸入(包含已結帳日期內)";
				}
			}
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
		echo '<rtMeg>' . $rtMeg . '</rtMeg>';	
	}
	echo '</Response>';	
	exit();
?>