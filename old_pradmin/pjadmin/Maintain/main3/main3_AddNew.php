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

//資料庫連線
	$NewSql = new mysql();	
	$memm01_loginid = xRequest("memm01_loginid");
	//檢查帳號
	$Sql = "
		Select *
		From mem_m01
		where memm01_loginid = '$memm01_loginid'
	";
	$EsRun = $NewSql -> db_query($Sql) or die("SQL ERROR");	
	$EsCount = $NewSql -> db_num_rows($EsRun);
	
	if($EsCount <= 0){
		$Sql = " 
			Select *
			From mem_m01
		";
		$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
		$i = 0;
		while($Data = $NewSql -> db_field($initRun)){
			switch ($Data -> name){
				case 'memm01_no':
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
		Insert($NewSql,"mem_m01",$RowArr,$Arr);
		
		header("Location: " . $GetFileCode . ".php");
	}else{
		echo "<script>alert('你輸入的帳號已存在');window.history.back();</script>";
	}
?>