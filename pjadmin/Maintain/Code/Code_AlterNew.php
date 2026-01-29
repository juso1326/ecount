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
	$Code_Id = xRequest("Code_Id");
	
//資料庫連線
	$NewSql = new mysql();	

	$SqlWhere = " where 1 = 1 ";	
	$SqlKey = "";
	switch ($Code_Id){
		case 'CodeC01':
			$SqlKey = xRequest("C01_no");
			$SqlWhere .= " and C01_no = '$SqlKey'";
			$Sql = "
				Select '',C01_nm,ALTER_ID,ALTER_DATE,ALTER_TIME
				From code_c01
				$SqlWhere
			";
			$SqlTb = "code_c01";
			break;
		case 'CodeC02':
			$SqlKey = xRequest("C02_no");
			$SqlWhere .= " and C02_no = '$SqlKey'";
			$Sql = "
				Select C02_nm,ALTER_ID,ALTER_DATE,ALTER_TIME
				From code_c02
				$SqlWhere
			";
			$SqlTb = "code_c02";
			break;
		case 'CodeC03':
			$SqlKey = xRequest("C03_no");
			$SqlWhere .= " and C03_no = '$SqlKey'";
			$Sql = "
				Select '',C03_nm,ALTER_ID,ALTER_DATE,ALTER_TIME
				From code_c03
				$SqlWhere
			";
			$SqlTb = "code_c03";
			break;				
		case 'CodeC04':
			$SqlKey = xRequest("C04_no");
			$SqlWhere .= " and C04_no = '$SqlKey'";
			$Sql = "
				Select C04_nm,ALTER_ID,ALTER_DATE,ALTER_TIME
				From code_c04
				$SqlWhere
			";
			$SqlTb = "code_c04";
			break;					
	}
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$initCount = $NewSql -> db_num_rows($initRun);
	if($initCount != 1){
		header('location:/pjadmin/Maintain/ERRORPAGE.php?SysFileId=' . $GetFileCode . '&Error=' . '資料錯誤');
		exit();
	}
	$i = 0;
	while($Data = $NewSql -> db_field($initRun)){
		switch ($Data -> name){
			case 'ALTER_ID':
			case 'ALTER_DATE':
			case 'ALTER_TIME':
				break;
			default:
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = xRequest($Data -> name);
				break;			
		}
		if($RowArr[$i] != ""){
			$i ++;
		}					
	}
	Update($NewSql,$SqlTb,$RowArr,$Arr,$SqlWhere);
	header("Location: " . $Code_Id . ".php");
?>