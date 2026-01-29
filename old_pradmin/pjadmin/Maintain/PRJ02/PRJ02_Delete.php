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
	$DataCount = xRequest("DataCount");
	$paym01_no = xRequest("paym01_no");
	$Source = xRequest("Source");	
	$PageCon = xRequest("PageCon");
	
//資料庫連線
	$NewSql = new mysql();
	if($paym01_no != ""){
		
		//刪除主檔
		$SqlWhere = " where 1 = 1 and paym01_no = '$paym01_no'";
		$SqlTb = "pay_m01";
		
		//刪除付款		
		if($SqlTb != "" & $SqlWhere != ""){
			Delete($NewSql,$SqlTb,$SqlWhere);
		}
		
		//２０１７新增教育費
		//刪除主檔
		$SqlWhere = " where 1 = 1 and paym01_no = '$paym01_no'";
		$SqlTb = "pay_t03";
		
		//刪除教育		
		if($SqlTb != "" & $SqlWhere != ""){
			Delete($NewSql,$SqlTb,$SqlWhere);
		}
		
		//刪除薪資
		//刪除主檔
		$SqlWhere = " where 1 = 1 and paym01_no = '$paym01_no'";
		$SqlTb = "pay_t02";
		
		//刪除付款		
		if($SqlTb != "" & $SqlWhere != ""){
			Delete($NewSql,$SqlTb,$SqlWhere);
		}
	}
	if($Source == ""){
		header("Location: " . $GetFileCode . ".php?" . $PageCon);
	}else if($Source == "PRJ01"){
		echo '<script>parent.window.location.reload();parent.Shadowbox.close();</script>';
	}	
?>