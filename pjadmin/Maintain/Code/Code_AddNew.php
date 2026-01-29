<?php
//*****************************************************************************************
//		日期: 20141129
//		程式功能：  /共用/ 代碼新增
//		使用參數：
//*****************************************************************************************
	header ('Content-Type: text/html; charset=utf-8');
	session_start();
//函式庫
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config.ini.php");
	ChkLogin();	
//參數
	$GetFileCode = GetFileCode(__FILE__);
	$Code_Id = xRequest("Code_Id");
//資料庫連線
	$NewSql = new mysql();	
	
	switch ($Code_Id){
		case 'CodeC01':
			$Sql = "
				Select C01_nm,ADD_ID,ADD_DATE,ADD_TIME,ALTER_ID,ALTER_DATE,ALTER_TIME
				From code_c01
			";
			$SqlTb = "code_c01";
			break;
		case 'CodeC02':
			$Sql = "
				Select C02_nm,ADD_ID,ADD_DATE,ADD_TIME,ALTER_ID,ALTER_DATE,ALTER_TIME
				From code_c02
			";
			$SqlTb = "code_c02";
			break;
		case 'CodeC03':
			$Sql = "
				Select C03_nm,ADD_ID,ADD_DATE,ADD_TIME,ALTER_ID,ALTER_DATE,ALTER_TIME
				From code_c03
			";
			$SqlTb = "code_c03";
			break;				
		case 'CodeC04':
			$Sql = "
				Select C04_nm,ADD_ID,ADD_DATE,ADD_TIME,ALTER_ID,ALTER_DATE,ALTER_TIME
				From code_c04
			";
			$SqlTb = "code_c04";
			break;					
	}
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$i = 0;
	while($Data = $NewSql -> db_field($initRun)){
		switch ($Data -> name){
			case 'ADD_ID':
			case 'ALTER_ID':
				$RowArr[$i] = $Data -> name;
				$Arr[$i]	 = $_SESSION["MemNO"];
				break;
			case 'ADD_DATE':
			case 'ALTER_DATE':
				$RowArr[$i] = $Data -> name;
				$Arr[$i]	 = DspDate(date('Ymd'));
				break;
			case 'ADD_TIME':
			case 'ALTER_TIME':					
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = DspTime(date('His'));				
				break;
			default:
				$RowArr[$i] = $Data -> name;
				$Arr[$i]	 = xRequest($Data -> name);
				break;			
		}	
		if($RowArr[$i] != ""){
			$i ++;
		}				
	}

	//新增資料
	Insert($NewSql,$SqlTb,$RowArr,$Arr);
	
	header("Location: " . $Code_Id . ".php");
?>