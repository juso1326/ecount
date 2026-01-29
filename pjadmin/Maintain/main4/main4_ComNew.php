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
	$payt02_no = xRequest("payt02_no");

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
	/*
	$Sql = "
		Update pay_t02 Set
		payt02_acttotal = '" . xRequest("foundt03_salary") . "'
		,payt02_remain = '" . (xRequest("foundt03_total") - xRequest("foundt03_salary")) . "'
		,payt02_paytotal = '" . xRequest("payt02_paydate") . "'
		where 1 = 1
		and payt02_no = '$payt02_no'
	";
	$NewSql -> db_query($Sql) or die("SQL ERROR");
	*/
	if ((xRequest("foundt03_total") - xRequest("foundt03_salary")) > 0){
			$Sql = "
				Insert ino pay_t02(
					payt02_type,paym01_no , firmm01_no,payt02_memo ,
					payt02_remark ,payt02_paytotal ,payt02_paytotalAfter 
					,payt02_date ,payt02_from ,payt02_Sort
				)
				Select payt02_type,paym01_no , firmm01_no,payt02_memo 
				,payt02_remark ," . (xRequest("foundt03_total") - xRequest("foundt03_salary")) . "," . (xRequest("foundt03_total") - xRequest("foundt03_salary")) . "
				, date('Ym01',strtotime(date(" . $sY . $sM . '01' . ") +1 month)),payt02_from ,payt02_Sort
				From pay_t02
				where 1 = 1
				and payt02_no = '$payt02_no'			
			";
			$NewSql -> db_query($Sql) or die("SQL ERROR");
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