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
	$payt02_no = xRequest("payt02_no");

//資料庫連線
	$NewSql = new mysql();	

	$Sql = " Select payt02_no ,payt02_acttotal ,payt02_remain ,payt02_paytotal,payt02_paydate
			From pay_t02
			where payt02_no = '$payt02_no'
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$initCount = $NewSql -> db_num_rows($initRun);
	if($initCount != 1){
		header('location:/pjadmin/Maintain/ERRORPAGE.php?SysFileId=' . $GetFileCode . '&Error=' . '資料錯誤');
		exit();
	}
	$i = 0;
	while($Data = $NewSql -> db_field($initRun)){
		switch ($Data -> name){
			case 'payt02_no':
			case 'paym01_no':
			case 'payt02_date':
				break;
			case 'payt02_remain':
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = (xRequest("payt02_paytotal") - xRequest("payt02_acttotal"));
				break;	
			case 'payt02_acttotal':
				$RowArr[$i] = $Data -> name;
				$Arr[$i] = (xRequest("payt02_acttotal"));
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
	$where = " where 1 = 1 ";
	$where .= " and payt02_no = '$payt02_no'";
	Update($NewSql,"pay_t02",$RowArr,$Arr,$where);
	PayStatus($NewSql,$payt02_no,xRequest('payt02_paydate'));
	//檢查是否足額 不足額 新增一筆下個月
	if ((xRequest("payt02_paytotal") - xRequest("payt02_acttotal")) > 0){
		/*
		$Sql = "
			Insert Into pay_t02(paym01_no,payt02_date,payt02_paydate, payt02_total, payt02_acttotal, payt02_paytype, payt02_remain)
			Select paym01_no,(SELECT date_format(DATE_ADD(payt02_date, INTERVAL 1 month),'%Y%m%d')),''," . (xRequest("payt02_total") - xRequest("payt02_acttotal")) . ",0,'',0
			From pay_t02
			where payt02_no = '$payt02_no'
		";
		*/
		$sYm = (substr(xRequest("payt02_paydate"),0,6));
		$Sql = "
			Insert into pay_t02(payt02_type,paym01_no , firmm01_no,payt02_memo ,payt02_remark ,payt02_paytotal ,payt02_paytotalAfter,payt02_date ,payt02_from ,payt02_Sort)
			Select payt02_type,paym01_no , firmm01_no,payt02_memo 
			,payt02_remark ," . (xRequest("payt02_paytotal") - xRequest("payt02_acttotal")) . "," . (xRequest("payt02_paytotal") - xRequest("payt02_acttotal")) . "
			, " . date('Ym06',strtotime(date($sYm . '06') . " +1 month")) . ",payt02_from ,payt02_Sort
			From pay_t02
			where 1 = 1
			and payt02_no = '$payt02_no'			
		";
		$NewSql -> db_query($Sql) or die("SQL ERROR");
	}
?>