<?php 
/*
功能列表	
	ChkLogin	檢查是否為登入狀態
	Insert		新增
	Update		更新
	Delete		刪除
	LastAutoId	取最後一個更新值
	
	ChkLogin
	GetTitle
*/
function Page($NewSql,$DataCount,$Sql,$aPageCount){
	global $PageCount;
	global $PageCurrent;
	global $Pagefir;
	global $PageSen;
	global $num_pages;
	global $initRun;
	(($num_pages <= 0)?$num_pages = 1:"");
	if($aPageCount == 0 || $DataCount == 0){
		$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR F1");
		return $initRun;
	}else{
		$PageCount = ceil($DataCount/$aPageCount);
		
		$Pagefir = ($num_pages - 1)*$aPageCount;
		$PageSen = $Pagefir + $aPageCount;
		$Sql .= " Limit " . $Pagefir . "," . $aPageCount;
		echo '<!--Data ' . $Sql . '-->';
		$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR F2");
		return $initRun;
		//$initCount = $NewSql -> db_num_rows($initRun);
	}
}
/*	功能 :SQL - 新增	*/
function Insert($NewSql,$Table,$RowArr,$ValArr){
	$z = 0;
	$str = '';
	$key = '';
	$value = '';
	while(count($RowArr) > $z){
		if (count($RowArr) > ($z + 1)){
			$key .= $RowArr[$z] . ',';	
			$value .= "'" . $ValArr[$z] . "',";
		}else{
			$key .= $RowArr[$z];
			$value .= "'" . $ValArr[$z] . "'";
		}
		$z ++;		
	}
	$str = " Insert Into ". $Table;
	$str .= "(" . $key . ")values(" . $value . ")";
	$NewSql -> db_query($str) or die("SQL ERROR F1");
}

function Update($NewSql,$Table,$RowArr,$ValArr,$where){
	$i = 0;
	$str = '';
	$kV = '';
	while(count($RowArr) > $i){			
		if ($RowArr[$i] != ""){
			if (count($RowArr) > ($i + 1)){
				$kV .= $RowArr[$i] . " = '" . $ValArr[$i] . "',";
			}else{
				$kV .= $RowArr[$i] . " = '" . $ValArr[$i] . "'";
			}			
		}	
		$i ++;				
	}
	$str = " Update " . $Table . " Set ";
	$str .= $kV;
	$str .= $where;
	// print_r($str);
	// exit();
	$NewSql -> db_query($str) or die("SQL ERROR F2");
}

function Delete($NewSql,$Table,$where){
	$str = " Delete From " . $Table;
	$str .= $where;
	$NewSql -> db_query($str) or die("SQL ERROR F3");	
}

function LastAutoId($NewSql){
	$str = " select LAST_INSERT_ID() ";
	$LastAutoIdRun = $NewSql -> db_query($str) or die("SQL ERROR F5");
	$LastAutoId = $NewSql -> db_result($LastAutoIdRun);
	
	return $LastAutoId;	
}
function ChkLogin(){
	if($_SESSION["MemNO"] == ""){
		header('location:/Maintain/Login/Logout.php');
	}
}

function GetTitle($NewSql,$Key){
	$str = "
		Select MUG03_NM
		From mug_03
		where MUG03_CODE = '$Key'	
	";	
	$GetTitleRun = $NewSql -> db_query($str) or die("SQL ERROR F6");
	$MUG03_NM = $NewSql -> db_result($GetTitleRun);
	return "專案帳戶管理 > " . $MUG03_NM;
}

function getMember(){
	//非停權員工
	$Sql = "
		Select memm01_no, memm01_nick
		From mem_m01 m01
		Left join mug_01 on m01.MUG01_NO = mug_01.MUG01_NO
		where 1 = 1 
		and MUG01_SHOW = 'Y'
		and (lockdate = '' or lockdate = '0000-00-00' or lockdate > '".date('Y-m-d')."' or lockdate is null) 
		order by m01.MUG01_NO,memm01_nick,memm01_no
	";
	
	return $Sql;
}

?>