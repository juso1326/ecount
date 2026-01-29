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
	$success = true;
	$comm01_no = xRequest("comm01_no");
	$comt01_no = xRequest("comt01_no");
	
//資料庫連線
	$NewSql = new mysql();
	if($comm01_no == ""){
		$success = false;
	}
	
	if($success){
	//判斷新增||修改	
	switch(true){
		case ($comt01_no != ""):
			$Sql = " Select *
					From com_t01
					where comt01_no = '$comt01_no'
			";
			$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
			$i = 0;
			while($Data = $NewSql -> db_field($initRun)){		
				switch ($Data -> name){
					case 'comt01_no':
					case 'comm01_no':
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
			$where .= " and comt01_no = '$comt01_no'";
			Update($NewSql,"com_t01",$RowArr,$Arr,$where);
				
			break;
		default:
			$Sql = " Select *
					From com_t01
			";
			$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
			$i = 0;
			while($Data = $NewSql -> db_field($initRun)){		
				switch ($Data -> name){
					case 'comt01_no':
						break;
					case 'comm01_no':
						$RowArr[$i] = $Data -> name;
						$Arr[$i] = $comm01_no;
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
			Insert($NewSql,"com_t01",$RowArr,$Arr);		
			break;
	}	
	}
	
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