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
	$DataCount = xRequest("DataCount");
//資料庫連線
	$NewSql = new mysql();
	
	$where = '';
	$i = 0;
	if ($DataCount > 0){
		while($i < $DataCount){
			$i ++;
			if(xRequest("check" . $i)<> ''){
				if ($where != ""){
					$where .= ",";		
				}
				$where .= "'" . xRequest("check" . $i) . "'";
			}
		}
	}
	$where = " where 1 = 1 and MUG01_NO in (" . $where . ")";
	
	Delete($NewSql,"mug_01",$where);

	header("Location: mug.php");
?>