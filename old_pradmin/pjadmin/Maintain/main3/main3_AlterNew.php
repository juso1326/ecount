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
//參數
	$LevelMem = LevelMEM($NewSql);
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
	$RowArr = [];
	$Arr = [];
	while($Data = $NewSql -> db_field($initRun)){
		switch ($Data -> name){
			case 'memm01_no':
			case 'lastlogin':
			case 'nowdate':
			case 'memm01_workst':
			case 'memm01_worken':
				break;
			case 'memm01_uplevel':
				switch($LevelMem){
					case '1':
					case '2':
						$RowArr[$i] = $Data -> name;			
						if(xRequest("MUG01_NO") == "4"){		
							$Arr[$i] = xRequest($Data -> name);
						}else{
							$Arr[$i] = "";					
						}			
						break;
					default:
						break;
				}
				break;
			case 'MUG01_NO':
				switch($LevelMem){
					case '1':
					case '2':
						array_push($RowArr,$Data -> name);
						array_push($Arr,xRequest($Data -> name));
						// $RowArr[$i] = $Data -> name;
						// $Arr[$i] = xRequest($Data -> name);		
						break;
					default:
						break;
				}
				break;
			default:
				array_push($RowArr,$Data -> name);
				array_push($Arr,xRequest($Data -> name));
				// $RowArr[$i] = $Data -> name;
				// $Arr[$i] = xRequest($Data -> name);

				break;			
		}
					
	}
	$where = " where 1 = 1 ";
	$where .= " and memm01_no = '$memm01_no'";

	Update($NewSql,"mem_m01",$RowArr,$Arr,$where);
	
	header("Location: " . $GetFileCode . ".php");
?>