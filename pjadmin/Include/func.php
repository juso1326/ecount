<?php
/*
功能列表	
	+單元
		GetFileCode		取得列表單元ID
		GetFileType		取得單元狀態
	+值
		xRequest		取值
*/
/* 功能: 取值 */
	function xRequest($str, $len=''){
		if($len == ''){
				return (@$_REQUEST[$str]);
		}else{
				return iconv_substr((@$_REQUEST[$str]),0,$len,'utf-8');
		}	
	}

/* 取ID */
	function GetFileCode($FilePath){
		$FileName = basename($FilePath,'.php');
		$strpos = strpos($FileName,'_');
		
		if($strpos <= 0){
			return $FileName;
		}else{
			return substr($FileName,0,$strpos);
		}
	}
/* 檔案類別 */
	function GetFileType($FilePath){
		$FileName = basename($FilePath,'.php');
		$strpos = strpos($FileName,'_');

		if($strpos <= 0){
			return "";
		}else{

			switch(substr($FileName,$strpos + 1,strlen($FileName))){
				case 'Add':				
					return " > 新增";
					break;
				case 'Alter':
					return " > 修改";				
					break;
				default:
					return "";
					break;
			}
		}
	}
	
	function DspDate($Date,$br = ''){
		$str = "";
		switch(strlen($Date)){
			case "8":			
				$str = substr($Date,0,4) . $br . substr($Date,4,2) . $br . substr($Date,6,2) ;
				break;
			case "6":
				$str = substr($Date,0,4) . $br . substr($Date,4,2);
				break;
			default:
				$str = $Date;
				break;
		}	
		return $str;
	}
	function DspTime($Time,$br = ''){
		$str = "";
		switch(strlen($Time)){
			case "6":			
				$str = substr($Time,0,2) . $br . substr($Time,2,2) . $br . substr($Time,4,2) ;
				break;
			default:
				$str = $Time;
				break;
		}	
		return $str;
	}
	
	function MoneyFormat($number){
		if(is_numeric($number)){
			return "$".number_format($number, 0, '', ',');
		}else{
			return $number;
		}
	}
	
	/* ------------------ 後台 ------------------ */
	function LoginChk($GetFileCode,$Set){
		if($_SESSION["MemNO"] == ""){
			header('location:/Maintain/Login/Logout.php');
			//$Login = false;
		}else{
			
		}
			/*
		$NewSql = new mysql();
		$Login = false;
		if($_SESSION["MemNO"] == ""){
			$Login = false;
		}else{

			$Sql = " 
				Select m02.GP_ID
				From mu_m02 m02
				Left join mu_m01 m01 on m02.GP_ID = m01.GP_ID
				where AC_ID = '" . $_SESSION["MemNO"] . "'
				and MUM02_OPEN = 'Y'
				and m01.MUM01_OPEN = 'Y'
			";
			$DataRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
			$Result = $NewSql -> db_result($DataRun);
			
			if($Result != ""){
				switch($Set){
					case "1";
						$LimitCulm = "MUM04_SREACH";
						break;
					case "2";
						$LimitCulm = "MUM04_ADD";
						break;
					case "3";
						$LimitCulm = "MUM04_DELETE";
						break;
					case "4";
						$LimitCulm = "MUM04_UPDATE";
						break;
				}
				$Sql = "
					Select count(*)
					From mu_m04
					where GP_ID = '" . $Result . "'
					and MUM03_NO = '" . $GetFileCode . "'
					and " . $LimitCulm . " = 'Y'
				";
				$UnitRun = $NewSql -> db_query($Sql) or die("SQL ERROR 2");
				$Unit = $NewSql -> db_result($UnitRun);
				if($Unit){
					$Login = true;
				}
			}

		}
		
		if(!$Login){
			header('location:/Maintain/Login/Logout.php');
		}
			*/
	}		
?>