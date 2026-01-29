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
//資料庫連線
	$NewSql = new mysql();	
	
	$Sql = " 
		Select *
		From in_t01
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$i = 0;
	while($Data = $NewSql -> db_field($initRun)){
		switch ($Data -> name){
			case 'int01_no':
				break;	
			case 'int01_incometotalAfter':
				$RowArr[$i] = $Data -> name;
				$Arr[$i]	 = "";
				break;	
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
				$Arr[$i] = xRequest($Data -> name);
				break;
		}
		if($RowArr[$i] != ""){
			$i ++;
		}				
	}

	//新增資料
	Insert($NewSql,"in_t01",$RowArr,$Arr);
	$LastAutoId = LastAutoId($NewSql);
		
	//更新已付金額
		//-已收金額
		$Sql = " 
			select sum(int01_incometotal)
			from in_t01
			where inm01_no= '$inm01_no'		
		";
		$sumRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
		$int01_incometotal = $NewSql -> db_result($sumRun);
		
		//更新
		$Sql = "
			Update in_m01 Set
			inm01_incometotal = " . $int01_incometotal . "
			,inm01_type = '2'
			where inm01_no= '$inm01_no'
		";
		$NewSql -> db_query($Sql) or die("SQL ERROR 2");

	//更新專案狀態為已 請款中	
		$prjm01_no = xRequest("prjm01_no");
		
		if ($prjm01_no != ""){
			//更新付款狀態			
			$status = "6";
			InsertpayStauts($NewSql,$prjm01_no,$status);
						
			//更新主檔狀態
			$Sql = " Update prj_m01 set t02_no = '$status' where prjm01_no = '$prjm01_no'";
			$NewSql -> db_query($Sql) or die("SQL ERROR2");
		}	
		
	//建立薪資
		InsertSalary($NewSql,"I",$LastAutoId,"");			
//關閉資料庫連線
	$NewSql -> db_close();
	header ('Content-Type: text/html; charset=utf-8');
	echo '<Response>';
	if($success){
		echo '<resu>1</resu>';
	}else{
		echo '<resu>0</resu>';		
	}
	echo '</Response>';	
	exit();
?>