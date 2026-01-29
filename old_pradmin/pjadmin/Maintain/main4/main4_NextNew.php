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
	$DataKey = xRequest("DataKey");
	$T = xRequest("T");
//資料庫連線
	$NewSql = new mysql();	

	if ($DataKey != ""){
		//取出資料
		$Sql = "
			Select payt02_no ,payt02_type ,paym01_no ,int01_no , foundt01_no
			,date_format(DATE_ADD(payt02_date, INTERVAL " . $T .  " month),'%Y%m%d') as payDate
			From pay_t02
			where payt02_no = '$DataKey'		
		";
		$SqlRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
		$Rs = $NewSql -> db_fetch_array($SqlRun);		
		
		switch($Rs["payt02_type"]){
			case 'P':
				//更新應付 給付日期
				$Sql = "
					Update pay_m01 Set
					paym01_paydate = '" . $Rs["payDate"] . "'
					where paym01_no = '" . $Rs["paym01_no"] . "'
				";
				$NewSql -> db_query($Sql) or die("SQL ERROR 2");
				
				$Sql = "			
					Update pay_t02 Set
					payt02_date = '" . $Rs["payDate"] . "'
					where payt02_type = 'P'
					and paym01_no = '" . $Rs["paym01_no"] . "'
					and ifnull(payt02_paydate,'') = ''
				";
				$NewSql -> db_query($Sql) or die("SQL ERROR 3");				
				break;
			default:
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