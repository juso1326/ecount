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
	$paym01_no = xRequest("paym01_no");
	$Source = xRequest("Source");
	$PageCon = xRequest("PageCon");	
//資料庫連線
	$NewSql = new mysql();	
	
	$Sql = "
		Select *
		From pay_m01
		where paym01_no = '$paym01_no'
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
//	$init = $NewSql -> db_fetch_array($initRun);	
	$initCount = $NewSql -> db_num_rows($initRun);
	if($initCount != 1){
		header('location:/pjadmin/Maintain/ERRORPAGE.php?SysFileId=' . $GetFileCode . '&Error=' . '資料錯誤');
		exit();
	}
	$i = 0;
	while($Data = $NewSql -> db_field($initRun)){
		switch ($Data -> name){
			case 'paym01_no':	
			case 'paym01_type2':
			case 'ADD_ID':
			case 'ADD_DATE':
			case 'ADD_TIME':					
				break;
			case 'paym01_haspay':
				if(xRequest("paym01_type1") == "P"){
					$RowArr[$i] = $Data -> name;
					$Arr[$i] = "Y";
				}
				break;
			case 'ADD_ID':
				$RowArr[$i] = $Data -> name;
				if(xRequest("ADD_ID") != "" & xRequest("prjm01_no") != ""){
					$Arr[$i] = xRequest("ADD_ID");
				}else{
					$Arr[$i] = $_SESSION["MemNO"];
				}
				break;				
			case 'paym01_type1':
				$RowArr[$i] = $Data -> name;
				$Arr[$i]	 = xRequest("paym01_type1");
				break;
			case 'paym01_total':
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = xRequest("paym01_tax") + xRequest("paym01_subtotal");
				break;
			case 'memm01_no':
				$RowArr[$i] = $Data -> name;
				if(xRequest("paym01_type1") == 'M'){
					$Arr[$i] = xRequest($Data -> name);
				}else{
					$Arr[$i] = "";
				}
				break;
			case 'firmm01_no':
				$RowArr[$i] = $Data -> name;
				if(xRequest("paym01_type1") == 'F'){
					$Arr[$i] = xRequest($Data -> name);
				}else{
					$Arr[$i] = "";
				}	
				break;
			case 'ALTER_ID':
				$RowArr[$i] = $Data -> name;
				$Arr[$i]	 = $_SESSION["MemNO"];
				break;
			case 'ALTER_DATE':
				$RowArr[$i] = $Data -> name;
				$Arr[$i]	 = DspDate(date('Ymd'));
				break;
			case 'ALTER_TIME':					
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = DspTime(date('His'));				
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
	$where .= " and paym01_no = '$paym01_no'";
	Update($NewSql,"pay_m01",$RowArr,$Arr,$where);
	
	if(xRequest("paym01_paydate") != "" & xRequest("paym01_haspay") != "Y"){
		
		$Sql = " Delete From pay_t02 where paym01_no = '$paym01_no';";
		$NewSql -> db_query($Sql) or die("SQL ERROR 1.1.1");		
		
		$Sql = " Delete From pay_t03 where paym01_no = '$paym01_no'";
		$NewSql -> db_query($Sql) or die("SQL ERROR 1.1.2");
		
		InsertSalary($NewSql,"P",$paym01_no,"");
	}else if(xRequest("paym01_paydate") != "" & xRequest("paym01_haspay") == "Y" &  xRequest("paym01_type1") == 'P'){
		
		$Sql = " Delete From pay_t02 where paym01_no = '$paym01_no';";
		$NewSql -> db_query($Sql) or die("SQL ERROR 1.1");		
		
		$Sql = " Delete From pay_t03 where paym01_no = '$paym01_no';";
		$NewSql -> db_query($Sql) or die("SQL ERROR 1.1");
		
		InsertSalary($NewSql,"P",$paym01_no,"");
	}else if(xRequest("paym01_paydate") == "" ){
		$Sql = " Delete From pay_t02 where paym01_no = '$paym01_no';";
		$NewSql -> db_query($Sql) or die("SQL ERROR 1.1");		
		
		$Sql = " Delete From pay_t03 where paym01_no = '$paym01_no';";
		$NewSql -> db_query($Sql) or die("SQL ERROR 1.1");
	}

	if($Source == ""){
		header("Location: " . $GetFileCode . ".php?" . $PageCon);
	}else if($Source == "PRJ01"){
		echo '<script>parent.window.location.reload();parent.Shadowbox.close();</script>';
	}
?>