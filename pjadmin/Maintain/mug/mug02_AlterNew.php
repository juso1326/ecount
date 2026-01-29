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
	LoginChk(GetFileCode(__FILE__),"4");
//參數
	$GetFileCode = GetFileCode(__FILE__);
	$memm01_no = xRequest("memm01_no");
	
//資料庫連線
	$NewSql = new mysql();	
	
	$Sql = " Select *
			From mem_m01
			where memm01_no = '$memm01_no'
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$initCount = $NewSql -> db_num_rows($initRun);
	if($initCount != 1){
		header('location:/pjadmin/Maintain/ERRORPAGE.php?SysFileId=' . $GetFileCode . '&Error=' . '資料錯誤');
		exit();
	}
	$i = 0;
	while($Data = $NewSql -> db_field($initRun)){
		switch ($Data -> name){
			case 'memm01_no':
				break;
			default:
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = xRequest($Data -> name);
				break;			
		}
		$i ++;						
	}
	$where = " where 1 = 1 ";
	$where .= " and memm01_no = '$memm01_no'";
	Update($NewSql,"mem_m01",$RowArr,$Arr,$where);
	header("Location: " . $GetFileCode . ".php");
?>