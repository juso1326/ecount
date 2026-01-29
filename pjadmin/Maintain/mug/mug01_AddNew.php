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
	$DataCount = xRequest("DataCount");
	
//資料庫連線
	$NewSql = new mysql();	
	
	$Sql = " Select MUG01_NO, MUG01_NAME, MUG01_OPEN
			From mug_01
			Order by MUG01_NO ";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$i = 0;
	while($Data = $NewSql -> db_field($initRun)){
		switch ($Data -> name){
			case 'MUG01_NO':
				break;
			default:
				$RowArr[$i] = $Data -> name;
				$Arr[$i]= xRequest($Data -> name);
				break;			
		}
		if($RowArr[$i] != ""){
			$i ++;
		}				
	}

	//新增資料
	Insert($NewSql,"mug_01",$RowArr,$Arr);
	
	header("Location: " . $GetFileCode . ".php");
?>