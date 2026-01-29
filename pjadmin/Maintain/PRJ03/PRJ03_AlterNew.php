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
	$inm01_no = xRequest("inm01_no");
	$Source = xRequest("Source");
	$PageCon = xRequest("PageCon");
		
//資料庫連線
	$NewSql = new mysql();	
	
	$Sql = " Select *
			From in_m01
			where inm01_no = '$inm01_no'
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
			case 'inm01_no':
			case 'inm01_incometotal':
			case 'inm01_type':
			case 'ADD_ID':
			case 'ADD_DATE':
			case 'ADD_TIME':
			case 'inm01_Advance': //扣繳						
				break;		
			case 'inm01_total':
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = xRequest("inm01_tax") + xRequest("inm01_subtotal");
				break;	
			case 'inm01_hasinvoice':
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = xRequest("inm01_hastax");
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
	$where .= " and inm01_no = '$inm01_no'";
	Update($NewSql,"in_m01",$RowArr,$Arr,$where);
//更新狀態
	InComeSatus($NewSql,$inm01_no);
	if(xRequest("inm01_type") != "2"){
	//更新扣繳
		InAdence($NewSql,$inm01_no);
	}
	
	if($Source == ""){
		header("Location: " . $GetFileCode . ".php?" . $PageCon . "");
	}else if($Source == "PRJ01"){
		echo '<script>parent.window.location.reload();parent.Shadowbox.close();</script>';
	}
?>