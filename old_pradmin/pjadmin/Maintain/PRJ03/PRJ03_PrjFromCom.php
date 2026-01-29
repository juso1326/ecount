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
	
	if($DataKey == ""){
		$success = false;	
	}
	
	if($success){
		$Sql = "
			Select prjm01_no, prjm01_nm, m.c02_no, ct02.codet02_nm
			From prj_m01 m
			Left join code_t02 ct02 on m.t02_no  = ct02.codet02_no
			Where comm01_no = '$DataKey'
			/*and m.t02_no in ('1','2','3','4','5')		*/
		";
		$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
		$initCount = $NewSql -> db_num_rows($initRun);
	
	}
//關閉資料庫連線
	$NewSql -> db_close();
	header ('Content-Type: text/html; charset=utf-8');
	echo '<Response>';
	if($success){
		echo '<resu>1</resu>';
		while($init = $NewSql -> db_fetch_array($initRun)){
			echo '<Row>';
			echo '<no>' . $init["prjm01_no"] . '</no>';
			echo '<nm>' . $init["prjm01_nm"] . '</nm>';
			echo '<t02nm>' . $init["codet02_nm"] . '</t02nm>';			
			echo '</Row>';			
		}
	}else{
		echo '<resu>0</resu>';		
	}
	echo '</Response>';	
	exit();
?>