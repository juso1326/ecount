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
//參數
	$GetFileCode = GetFileCode(__FILE__);
	$success = true;
	$prjm01_no = xRequest("prjm01_no");
	$foundt01_no = xRequest("foundt01_no");
	$foundt01_DateSt = xRequest("foundt01_DateSt");
//資料庫連線
	$NewSql = new mysql();
	

		$Sql = " 
			Select *
			From found_t01
		";
		$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
		$i = 0;
		while($Data = $NewSql -> db_field($initRun)){		
			switch ($Data -> name){
				case 'foundt01_no':
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
		if($foundt01_no == ''){
			//新增資料
			Insert($NewSql,"found_t01",$RowArr,$Arr);
			$LastAutoId = LastAutoId($NewSql);
			
			InsertSalary($NewSql,"F",$LastAutoId,xRequest("memm01_no"));			
		}else{
			$where = " where 1 = 1 ";
			$where .= " and foundt01_no = '$foundt01_no'";
			Update($NewSql,"found_t01",$RowArr,$Arr,$where);
			$LastAutoId = $foundt01_no;
			
			//更新
			/*
			$Sql = "
				Delete From pay_t02
				where 1 = 1 
				and payt02_date >= '$foundt01_DateSt'
				and memm01_no = '" . xRequest("memm01_no") . "'
				and foundt01_no = '$foundt01_no'			
			";
			//'" . date("Ym06",strtotime("+1 months",strtotime(date('Ym'). "05"))) . "'
			$NewSql -> db_query($Sql) or die("SQL ERROR 2");
			*/
			$Sql = "
				Select payt02_no, payt02_date
				From pay_t02 
				where 1 = 1 
				and payt02_date >= '$foundt01_DateSt'
				and memm01_no = '" . xRequest("memm01_no") . "'
				and foundt01_no = '$foundt01_no'	
				order by payt02_date			
			";
			$T02Run = $NewSql -> db_query($Sql) or die("SQL ERROR 2");
			$T02Count = $NewSql -> db_num_rows($T02Run);
			while($T02 = $NewSql -> db_fetch_array($T02Run)){
				$sY = substr($T02["payt02_date"],0,4);
				$sM = substr($T02["payt02_date"],4,2);
				$Sql = "
					Select *
					From found_t03
					where memm01_no = '" . xRequest("memm01_no") . "'
					and foundt03_year = '$sY'
					and foundt03_month = '$sM'
				";
				$f03Run = $NewSql -> db_query($Sql) or die("SQL ERROR 3");
				$f03Count = $NewSql -> db_num_rows($f03Run);	
				if($f03Count <= 0){
					$Sql = "
						Delete From pay_t02
						where 1 = 1 
						and payt02_no = '" . $T02["payt02_no"] . "'
					";
					$NewSql -> db_query($Sql) or die("SQL ERROR 4");
				}			
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