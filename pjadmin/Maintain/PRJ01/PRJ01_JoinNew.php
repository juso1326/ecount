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
	$success = true;
	$prjm01_no = xRequest("prjm01_no");
	
//資料庫連線
	$NewSql = new mysql();
	

		$Sql = " 
			Select *
			From prj_t02
		";
		$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
		$i = 0;
		while($Data = $NewSql -> db_field($initRun)){		
			switch ($Data -> name){
				case 'prjt02_no':
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
		Insert($NewSql,"prj_t02",$RowArr,$Arr);
	
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