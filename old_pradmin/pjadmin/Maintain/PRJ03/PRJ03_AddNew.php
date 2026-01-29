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
	$Source = xRequest("Source");
	$PageCon = xRequest("PageCon");	
	$prjm01_no = xRequest("prjm01_no");
//資料庫連線
	$NewSql = new mysql();	
//取得專案負責人
	$Sql = "
		Select memm01_no
		From prj_m01
		where prjm01_no = '" . $prjm01_no . "'	
	";
	$memm01_no = $NewSql -> db_result($NewSql -> db_query($Sql));
	
	$Sql = " 
		Select *
		From in_m01
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$i = 0;
	while($Data = $NewSql -> db_field($initRun)){
		switch ($Data -> name){
			case 'inm01_no':
			case 'inm01_Advance': //扣繳
			case 'inm01_type':			
				break;
			case 'inm01_total':
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = xRequest("inm01_tax") + xRequest("inm01_subtotal");
				break;		
			case 'inm01_total':
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = "1";
				break;	
			case 'inm01_hasinvoice':
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = xRequest("inm01_hastax");
				break;
			case 'inm01_incometotal':				
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = "0";
				break;	
/*				
			case 'inm01_Advance': //扣繳
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = round(xRequest("inm01_subtotal")*.12);
				break;	
*/						
			case 'ADD_ID':
				$RowArr[$i] = $Data -> name;
				if($memm01_no != ""){
					$Arr[$i]	 = $memm01_no;
				}else{
					$Arr[$i]	 = $_SESSION["MemNO"];	
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
	Insert($NewSql,"in_m01",$RowArr,$Arr);
	//取KEY	
	$LastAutoId = LastAutoId($NewSql);
//	//更新扣繳
	InAdence($NewSql,$LastAutoId);
//更新狀態
	InComeSatus($NewSql,$LastAutoId);
	//更新專案狀態為已 請款中	
	$prjm01_no = xRequest("prjm01_no");
	
	if ($prjm01_no != ""){
		unset($RowArr);
		unset($Arr);
		$status = "5";
		$Sql = " Select *
				From prj_t01
		";
		$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
		$i = 0;
		while($Data = $NewSql -> db_field($initRun)){
			switch ($Data -> name){
				case 'prjt01_no':
					break;
				case 'prjm01_no':
					$RowArr[$i] = $Data -> name;
					$Arr[$i]	 = $prjm01_no;			
					break;
				case 'prjt01_state';
					$RowArr[$i] = $Data -> name;
					$Arr[$i]	 = $status;
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
		Insert($NewSql,"prj_t01",$RowArr,$Arr);

		//t02_no
		$Sql = " Update prj_m01 set t02_no = '$status' where prjm01_no = '$prjm01_no'";
		$NewSql -> db_query($Sql) or die("SQL ERROR2");

	}
	
	if($Source == ""){
		header("Location: " . $GetFileCode . ".php?" . $PageCon . "");
	}else if($Source == "PRJ01"){
		echo '<script>parent.window.location.reload();parent.Shadowbox.close();</script>';
	}
?>