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

//資料庫連線
	$NewSql = new mysql();	
	$sY = xRequest("foundt03_year");
	$sM = xRequest("foundt03_month");
	$memm01_no = xRequest("memm01_no");

	$Sql = " Select *
			From found_t03
			 ";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$i = 0;
	while($Data = $NewSql -> db_field($initRun)){
		switch ($Data -> name){
			case 'foundt03_no':
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
	Insert($NewSql,"found_t03",$RowArr,$Arr);

	//更新各狀態
	$Sql = "
		select pt02.*
		From pay_t02 pt02
		where 1 = 1 
		and pt02.memm01_no = '$memm01_no'
		and payt02_date >= '" . $sY . $sM . "06'
		and payt02_date <= '" . date("Ym05",strtotime("+1 months",strtotime($sY . $sM . "05"))) . "'
	";
	$SqlRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
		
	while($Rs = $NewSql -> db_fetch_array($SqlRun)){
		if($Rs["payt02_type"] == "P" & $Rs["payt02_paytotalAfter"] > 0){
			PayStatus($NewSql,$Rs["payt02_no"],xRequest("foundt03_date"));
		}
		
		$Sql = "
			Update pay_t02 Set	
			payt02_paydate = '" . xRequest("foundt03_date") . "'
			where  1 = 1 
			and memm01_no = '$memm01_no'
			and payt02_date >= '" . $sY . $sM . "06'
			and payt02_date <= '" . date("Ym05",strtotime("+1 months",strtotime($sY . $sM . "05"))) . "'
		";
		$NewSql -> db_query($Sql) or die("SQL ERROR 3");
	}
	//exit();
	if ((xRequest("foundt03_total") - xRequest("foundt03_salary")) > 0 || (xRequest("foundt03_total") - xRequest("foundt03_salary")) < 0){
			unset($RowArr);
			unset($Arr);
			$Sql = " Select *
					From pay_t02
			";
			$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
			$i = 0;
			while($Data = $NewSql -> db_field($initRun)){
				switch ($Data -> name){
					case 'payt02_no':
						break;
					case 'payt02_type':	
					case 'paym01_no':
					case 'int01_no':
					case 'foundt01_no':	
					case 'payt02_paytotal':									
						break;
					case 'memm01_no':
						$RowArr[$i] = $Data -> name;
						$Arr[$i]	 = xRequest("memm01_no");
						break;					
					case 'payt02_memo';
						$RowArr[$i] = $Data -> name;
						$Arr[$i]	 = "上期餘款";
						break;
					case 'payt02_paytotalAfter':
						$RowArr[$i] = $Data -> name;
						$Arr[$i]	 = (xRequest("foundt03_total") - xRequest("foundt03_salary"));
						break;
					case 'payt02_date':
						$RowArr[$i] = $Data -> name;
						$Arr[$i] = date('Ym06',strtotime(date($sY . $sM . '05') . " +1 month"));
						break;
					case 'payt02_Sort':
						$RowArr[$i] = $Data -> name;
						$Arr[$i] = "0";
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
			Insert($NewSql,"pay_t02",$RowArr,$Arr);
			
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