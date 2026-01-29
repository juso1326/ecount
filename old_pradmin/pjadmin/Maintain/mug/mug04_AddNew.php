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
	$DataKey = xRequest("DataKey");	
//資料庫連線
	$NewSql = new mysql();	
	
	$where = '';
	$j = 0;

	if ($DataCount > 0){
		while($j < $DataCount){
			$j ++;
			unset($RowArr);
			unset($Arr);			
		if(xRequest("check" . $j) != ''){			
			$Sql = " Select * From mug_04 ";
			$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
			$i = 0;
			while($Data = $NewSql -> db_field($initRun)){					
				switch ($Data -> name){
					case 'MUG01_NO':
						$RowArr[$i] = $Data -> name;	
						$Arr[$i] = $DataKey;					
						break;
					case 'MUG03_NO':
						$RowArr[$i] = $Data -> name;	
						$Arr[$i] = xRequest("check" . $j);					
						break;
					case 'MUG04_OPENADD':
						$RowArr[$i] = $Data -> name;	
						$Arr[$i] = xRequest("ADD" . $j);					
						break;
					case 'MUG04_OPENDEL':
						$RowArr[$i] = $Data -> name;	
						$Arr[$i] = xRequest("DEL" . $j);					
						break;
					case 'MUG04_OPENALT':
						$RowArr[$i] = $Data -> name;	
						$Arr[$i] = xRequest("ALT" . $j);					
						break;
					case 'MUG04_OPENSEL':
						$RowArr[$i] = $Data -> name;	
						$Arr[$i] = xRequest("Search" . $j);					
						break;
					case 'MUG04_NO':
					default:				
						break;																					
				}
				if($RowArr[$i] != ""){
					$i ++;
				}				
			}
			//新增資料
			Insert($NewSql,"mug_04",$RowArr,$Arr);		
			}
		}
	}


	
	header("Location: " . $GetFileCode . ".php?DataKey=" . $DataKey);
?>