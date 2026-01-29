<?php
//*****************************************************************************************
//		日期: 20141207
//		程式功能： 依客戶取得未結案的專案
//		使用參數：
//*****************************************************************************************
	header ('Content-Type: text/html; charset=utf-8');
	session_start();
//函式庫
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config.ini.php");
	LoginChk(GetFileCode(__FILE__),"1");
//參數
	$GetFileCode = GetFileCode(__FILE__);

//資料庫連線
	$NewSql = new mysql();	
	
	$success = true;
	$DataKey = xRequest("DataKey");
	$type = xRequest("type");
	if($type != "M" & $type != "F" & $type != "P"){
		$success = false;	
	}
	
	if($success & $type != "P"){
		//員工
		$Sql = "
			Select memm01_no, memm01_nick, memm01_uplevel
			From mem_m01 m01
			Left join mug_01 on m01.MUG01_NO = mug_01.MUG01_NO
			where MUG01_SHOW = 'Y'
			order by m01.MUG01_NO
		";
		$memRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
		$memCount = $NewSql -> db_num_rows($memRun);
	
		//廠商
		$Sql = "
			Select cm01.comm01_no,comm01_nicknm, ct02.comt02_branch ,ct02.comt02_acc, comm01_type1
			From com_m01 cm01
			Left join (
				Select *
				From com_t02
				limit 1
			)as ct02 on cm01.comm01_no = ct02.comm01_no			
			where comm01_type2 = 'Y'
			order by comm01_no			
		";
		$fireRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
		$fireCount = $NewSql -> db_num_rows($fireRun);		
	}
//關閉資料庫連線
	$NewSql -> db_close();
	header ('Content-Type: text/html; charset=utf-8');
	echo '<Response>';
	if($success){
		echo '<resu>1</resu>';
		while($mem = $NewSql -> db_fetch_array($memRun)){
			echo '<M>';
			echo '<no>' . $mem["memm01_no"] . '</no>';
			echo '<nm>' . $mem["memm01_nick"] . '</nm>';
			echo '<T1>P</T1>';
			echo '<uplevel>' . $mem["memm01_uplevel"] . '</uplevel>';
			echo '</M>';			
		}
		while($fire = $NewSql -> db_fetch_array($fireRun)){
			echo '<F>';
			echo '<no>' . $fire["comm01_no"] . '</no>';
			echo '<nm>' . $fire["comm01_nicknm"] . '</nm>';		
			echo '<branch>' . $fire["comt02_branch"] . '</branch>';
			echo '<acc>' . $fire["comt02_acc"] . '</acc>';	
			echo '<T1>' . ($fire["comm01_type1"]) . '</T1>';						
			echo '</F>';			
		}		
	}else{
		echo '<resu>0</resu>';		
	}
	echo '</Response>';	
	exit();
?>