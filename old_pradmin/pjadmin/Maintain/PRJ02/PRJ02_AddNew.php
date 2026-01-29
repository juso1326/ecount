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
	$Source = xRequest("Source");				//網頁來源
	$PageCon = xRequest("PageCon");	
	
	$Sql = " 
		Select *
		From pay_m01
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$i = 0;
	while($Data = $NewSql -> db_field($initRun)){
		switch ($Data -> name){
			case 'paym01_no':
				break;
			case 'paym01_type1':
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = xRequest("paym01_type1");
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
			case 'paym01_total':
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = xRequest("paym01_tax") + xRequest("paym01_subtotal");
				break;		
			case 'paym01_haspay':
				if(xRequest("paym01_type1") == "P"){
					$RowArr[$i] = $Data -> name;
					$Arr[$i] = "Y";
				}
				break;
			case 'paym01_type2':
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = "N";
				break;	
			case 'ADD_ID':
				$RowArr[$i] = $Data -> name;
				if(xRequest("ADD_ID") != "" & xRequest("prjm01_no") != ""){
					$Arr[$i] = xRequest("ADD_ID");
				}else{
					$Arr[$i] = $_SESSION["MemNO"];
				}
				break;
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
				$Arr[$i] = xRequest($Data -> name);
				break;
		}
		if($RowArr[$i] != ""){
			$i ++;
		}				
	}

	//新增資料
	Insert($NewSql,"pay_m01",$RowArr,$Arr);
	
	if(xRequest("paym01_paydate") != ""){
		$LastAutoId = LastAutoId($NewSql);
		InsertSalary($NewSql,"P",$LastAutoId,"");
	}	
	
	if($Source == ""){
		header("Location: " . $GetFileCode . ".php?" . $PageCon);
	}else if($Source == "PRJ01"){
		echo '<script>parent.window.location.reload();parent.Shadowbox.close();</script>';
	}
?>