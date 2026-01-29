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
	$prjm01_no = xRequest("prjm01_no");
	$success = true;
	
	if($prjm01_no == ''){
		$success = false;
	}
	if($success){
	//資料庫連線
		$NewSql = new mysql();	
		
		$Sql = " Select prjm01_no, prjm01_DateSt, prjm01_DateEd, t02_no ,prjm01_script
				From prj_m01
				where prjm01_no = '$prjm01_no'
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
				case 'prjm01_no':
				case 'memm01_no':			
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
		$where .= " and prjm01_no = '$prjm01_no'";
		Update($NewSql,"prj_m01",$RowArr,$Arr,$where);
	}
	sleep(2);
//	header("Location: " . $GetFileCode . ".php");
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