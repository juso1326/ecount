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
	$MUG01_NO = xRequest("MUG01_NO");
	
//資料庫連線
	$NewSql = new mysql();	
	
	$Sql = " Select MUG01_NO, MUG01_NAME, MUG01_OPEN
			From mug_01
			where MUG01_NO = '$MUG01_NO'
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
			case 'MUG01_NO':
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
	$where = " where 1 = 1 ";
	$where .= " and MUG01_NO = '$MUG01_NO'";
	Update($NewSql,"mug_01",$RowArr,$Arr,$where);
	header("Location: " . $GetFileCode . ".php");
?>