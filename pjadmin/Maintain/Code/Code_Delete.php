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
	$DataCount = xRequest("DataCount");
	$Code_Id = xRequest("Code_Id");

//資料庫連線
	$NewSql = new mysql();
	if($Code_Id != ""){
		$where = '';
		$i = 0;
		if ($DataCount > 0){
			while($i < $DataCount){
				$i ++;
				if(xRequest("check" . $i)<> ''){
					if ($where != ""){
						$where .= ",";		
					}
					$where .= "'" . xRequest("check" . $i) . "'";
				}
			}
		}
		
		$SqlWhere = " where 1 = 1 ";	
		$SqlKey = "";	
		switch ($Code_Id){
			case 'CodeC01':
				$SqlWhere .= " and C01_no in (" . $where . ")";
				$SqlTb = "code_c01";
				break;
			case 'CodeC02':
				$SqlWhere .= " and C02_no in (" . $where . ")";
				$SqlTb = "code_c02";
				break;	
			case 'CodeC03':
				$SqlWhere .= " and C03_no in (" . $where . ")";
				$SqlTb = "code_c03";
				break;				
			case 'CodeC04':
				$SqlWhere .= " and C04_no in (" . $where . ")";
				$SqlTb = "code_c04";
				break;								
		}	
		if($SqlTb != "" & $where != ""){
			Delete($NewSql,$SqlTb,$SqlWhere);
		}

		header("Location: " . $Code_Id . ".php");
	}
?>