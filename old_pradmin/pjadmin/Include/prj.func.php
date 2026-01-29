<?php 
/*
	1.1 LevelMEM		使用者層級
	1.2 LevelLimit		使用者層級權限

	CompanyTaxPer	營業稅
	GetTermYM		取得結帳區間
	InAdence		應收扣繳計算
	InComeSatus		應收狀態
	
	3.1 InsertSalary	撥款明細
	4.1 InsertpayStauts 應付明細記錄
	
	5.1	SqlOrder	 Sql 排序參數
	5.2	OrderUrl	 Sql 排序url html	
*/
	function BusinessTax(){
		return .05;
	}
	function PayTax(){
		return .02;
	}
	function ThreeWTax(){
		return .05;		
	}
	function LevelMEM($NewSql){
		$MemNO = $_SESSION["MemNO"];
		//使用者層級Update
		$str = " 
			Select MUG01_NO
			From mem_m01
			Where memm01_no = '$MemNO'		
		";
		$LevelLimitRun = $NewSql -> db_query($str) or die("SQL ERROR P1");
		$MUG01_NO = $NewSql -> db_result($LevelLimitRun);
		return $MUG01_NO;	
	}
	function LevelLimit($NewSql,$SqlKey){
		$mywhere = "";
		$MemNO = $_SESSION["MemNO"];
		//使用者層級
		$str = " 
			Select MUG01_NO
			From mem_m01
			Where memm01_no = '$MemNO'		
		";
		$LevelLimitRun = $NewSql -> db_query($str) or die("SQL ERROR P1");
		$MUG01_NO = $NewSql -> db_result($LevelLimitRun);
		
		switch ($MUG01_NO){
			case '1':
			case '2':
				$mywhere = "";
				break;
			case '3':
				$mywhere = "
				 " . $SqlKey . "	in (
						select memm01_no 
						from mem_m01
						where memm01_no = '$MemNO' or memm01_uplevel = '$MemNO'
					)				
				";
				break;
			case '4':
				$mywhere = "
				 " . $SqlKey . "	= (
						select memm01_no 
						from mem_m01
						where memm01_no = '$MemNO'
					)				
				";
				break;
			default:
				$mywhere = "
				 " . $SqlKey . "	= ''
				";
				break;			
	
		}
		
		return $mywhere;
	}

	function CompanyTaxPer($NewSql){
	//代碼	營業稅
		$Sql = "
			Select C05_per
			From code_c05
			where C05_no = 'sales'
		";
		$C05Run = $NewSql -> db_query($Sql) or die("SQL ERROR 4");	
		$salesTaxPer = $NewSql -> db_result($C05Run);
		
		return $salesTaxPer;		
	}
	function YearMonthPN($Type,$sY,$sM){
		//if($sY < "2014" || ($sY == "2014" & $sM < 9)){
		if(($sY < "2014" || ($sY == "2014" & $sM <= "9")) & $Type == "P"){
			$sY = "2014";
			$sM = "09";
			$Type = "";
			return "&sY=" . $sY . "&sM=" . $sM;		
		}		
		switch ($Type){
			case 'P':
				$sY = date("Y",strtotime("-1 months",strtotime($sY . $sM . "01")));
				$sM = date("m",strtotime("-1 months",strtotime($sY . $sM . "01")));
				break;
			case 'N':
				$sY = date("Y",strtotime("1 months",strtotime($sY . $sM . "01")));
				$sM = date("m",strtotime("1 months",strtotime($sY . $sM . "01")));			
				break;				
		}
		return "&sY=" . $sY . "&sM=" . $sM;		
	}
	function GetTermYM($NewSql,$date){
		$str = "
			Select 
			case
			  when day('$date') > 5 Then date_format('$date','%Y%m')
			  when day('$date') <= 5 Then date_format(DATE_ADD('$date', Interval -1 month),'%Y%m')
			  Else ''
			End 
		";
		$YMRun = $NewSql -> db_query($str) or die("SQL ERROR 1");	
		$YM = $NewSql -> db_result($YMRun);			
		return $YM;
	}
	/* STOP*/
	function InAdence($NewSql,$DataKey){
		$PayAdence = 0;
		$str = "
			Select I01.inm01_no ,inm01_subtotal,inm01_total , inm01_hasinvoice , ifnull(Paytotal,0) as Paytotal, I01.prjm01_no
			From in_m01 I01
			Left join (
				Select sum(paym01_subtotal) as Paytotal,prjm01_no
				From pay_m01
				Group by prjm01_no		
			)as pay on I01.prjm01_no = pay.prjm01_no
			where I01.inm01_no = '$DataKey'
			Order by inm01_invoicedate desc		
		";
		$I01Run = $NewSql -> db_query($str) or die("SQL ERROR 5");	
		$I01 = $NewSql -> db_fetch_array($I01Run);
		
		switch($I01["inm01_hasinvoice"]){
			case 'N':
			/*
				if($I01["prjm01_no"] != ""){//Paytotal
					$PayAdence = ($I01["inm01_subtotal"] - $I01["paym01_subtotal"])*.05 - ($I01["paym01_subtotal"]*.07);
				}else{
					$PayAdence = ($I01["inm01_subtotal"])*.05;									
				}
			*/
				$PayAdence = round(($I01["inm01_subtotal"])*.07);
				break;	
			case 'Y':
			/*
				if($I01["prjm01_no"] != ""){
					$PayAdence = ($I01["inm01_subtotal"])*.12;
				}else{
					$PayAdence = ($I01["inm01_subtotal"] - $I01["paym01_subtotal"])*.12;									
				}
			*/
				$PayAdence = round(($I01["inm01_subtotal"])*.14);
				break;											
			}
		
		//更新
		$Sql = "
			Update in_m01 Set
			inm01_Advance = " . $PayAdence . "
			where inm01_no = '$DataKey'
		";
		$NewSql -> db_query($Sql) or die("SQL ERROR 5 - 1");
	}
	
	function InComeSatus($NewSql,$DataKey){
		$Statu = "0";
		$str = "
			Select inm01_type,inm01_hasinvoice ,inm01_invoicedate ,inm01_invoiceno
			From in_m01
			where inm01_no= '$DataKey'
		";
		
		$DataRun = $NewSql -> db_query($str) or die("SQL ERROR 1");	
		$Data = $NewSql -> db_fetch_array($DataRun);	
		
		switch ($Data["inm01_type"]){
			case '2':
			case '3':
			case '4':
				$Statu = $Data["inm01_type"];
				break;
			default:
				if ($Data["inm01_invoicedate"] != "" and $Data["inm01_invoiceno"] == ""){
					$Statu = "5";
				}	
				if ($Data["inm01_invoicedate"] != "" and $Data["inm01_invoiceno"] != ""){
					$Statu = "1";
				}
				if ($Data["inm01_invoicedate"] == "" and $Data["inm01_invoiceno"] == ""){
					$Statu = "6";
				}						
				break;
		}	
		
		//更新
		$str = " Update in_m01 Set inm01_type = '$Statu' where inm01_no= '$DataKey'";
		$NewSql -> db_query($str) or die("SQL ERROR 2");
	}
/*
	代碼: 3.1
	撰寫日期:2014.12.28
	撰寫功能:InsertSalary	撥款明細
	參數:
		$Type(類別) P :應付 I:應收 F:扣項
		$TypeKey(類別KEY)
*/
	function InsertSalary($NewSql,$Type,$TypeKey,$MemNo,$sYM=''){
		switch ($Type){
			case 'P':
				$str = "
					Select pm01.paym01_no , pm01.paym01_type1 , pm01.memm01_no , prj.memm01_no as prjmemm01_no, pm01.firmm01_no
					, pm01.paym01_paydate , pm01.paym01_total , cm01.comm01_nicknm , prj.prjm01_nm , pm01.paym01_prjcontent
					, pm01.paym01_remark
					, paym01_paytotal
					, ADD_ID
					, cf01.comm01_type1
					, pm01.paym01_hastax
					, pm01.paym01_paylearn
					From pay_m01 pm01
					Left join com_m01 cm01 on pm01.comm01_no = cm01.comm01_no
					Left join com_m01 cf01 on pm01.firmm01_no = cf01 .comm01_no and cf01.comm01_type2 = 'Y'
					Left join prj_m01 prj on pm01.prjm01_no = prj.prjm01_no
					where 1 = 1 
					/*pm01.paym01_type1 = 'M'	*/
					and pm01.paym01_no = '$TypeKey'			
				";
				$SqlRun = $NewSql -> db_query($str) or die("SQL ERROR 3.1.1");	
				$Rs = $NewSql -> db_fetch_array($SqlRun);
				
				$Memo = $Rs["comm01_nicknm"];
				
				//公司資料
				if($Rs["prjm01_nm"] != ''){
					$Memo .= " : " . $Rs["prjm01_nm"];
				}
				if($Rs["paym01_prjcontent"] != ''){
					$Memo .= " : " . $Rs["paym01_prjcontent"];
				}
				if($Rs["paym01_type1"] == "M" || $Rs["paym01_type1"] == "F"){				
				//稅後金額 
					$paytotal = $Rs["paym01_total"];
					if($Rs["comm01_type1"] == "P"){
						$paytotalAfter = $Rs["paym01_paytotal"];
						$paytotal = $Rs["paym01_paytotal"];
					}else{
						$paytotalAfter = $Rs["paym01_paytotal"];
					}
				//處理對象 - 成員、廠商
					$InsertMem = "";
					//帳務對象
					if($Rs["prjmemm01_no"] != ""){
						$InsertMem = $Rs["prjmemm01_no"];
					}else if($Rs["ADD_ID"] != ""){
						$InsertMem = $Rs["ADD_ID"];
					}
					
					$str = "
						Insert into pay_t02(						
							payt02_type ,paym01_no ,memm01_no , firmm01_no,payt02_memo 
							,payt02_remark ,payt02_paytotal ,payt02_paytotalAfter ,payt02_from  
							,payt02_date ,payt02_Sort
							,ADD_ID ,ADD_DATE ,ADD_TIME
						)
						Select '$Type'," . $Rs["paym01_no"] . " , '" . $Rs["memm01_no"] . "' , '" . $Rs["firmm01_no"] . "', '" . $Memo . "' 
						, '" . $Rs["paym01_remark"] . "'," . $paytotal . " ," . $paytotalAfter . " , '" . $InsertMem . "'
						, '" . $Rs["paym01_paydate"] . "',1
						, '" . $_SESSION["MemNO"] . "','" . date('Ymd') . "','" . date('His') . "'
					";
					$NewSql -> db_query($str) or die("SQL ERROR 3.1.2");
					
					if ($Rs["paym01_paylearn"] > 0){
						//２０１７新增教育費用 - 設計師給主管
						$str = "
							Insert into pay_t03 (`paym01_no`, `payt03_paytotal`, `payt03_paytotalAfter`, `memm01_no_from`, `memm01_no_pm`, `memm01_no_pay`, `payt03_remarker`,payt03_paydate,payt03_memo)
							SELECT paym01_no
							, paym01_paylearn * 2
							, ROUND(paym01_paylearn * 2 * (1-paym01_discountTax),0) as 'A'
							, pay.`memm01_no`
							, prj.memm01_no
							, m.memm01_uplevel
							, pay.paym01_remark
							, '" . $Rs["paym01_paydate"] . "'
							, '" . $Memo . "'
							FROM pay_m01 pay
							Left join mem_m01 m on pay.memm01_no = m.memm01_no
							Left join prj_m01 prj on prj.prjm01_no = pay.prjm01_no
							where paym01_paylearn > 0
							and paym01_no = '" . $Rs["paym01_no"] . "'
						";
						print_r($str);
						$NewSql -> db_query($str) or die("SQL ERROR 3.1.8");
					
					}
					//, (case pay.paym01_hastax WHEN 'Y' Then ROUND(paym01_paylearn * 0.91,0) Else ROUND(paym01_paylearn * 0.93,0) End) 'A'
					
					//２０１７新增教育費用 - PM給設計師的主管
					/*
					$str = "
						Insert into pay_t03 (`paym01_no`, `payt03_paytotal`, `payt03_paytotalAfter`, `memm01_no_from`, `memm01_no_pm`, `memm01_no_pay`, `payt03_remarker`)
						SELECT paym01_no
						, paym01_paylearn
						, (case pay.paym01_hastax WHEN 'Y' Then ROUND(paym01_paylearn * 0.91,0) Else ROUND(paym01_paylearn * 0.93,0) End) 'A'
						, prj.memm01_no
						, m.memm01_uplevel
						, ''
						, pay.paym01_remark
						FROM pay_m01 pay
						Left join mem_m01 m on pay.memm01_no = m.memm01_no
						Left join prj_m01 prj on prj.prjm01_no = pay.prjm01_no
						where paym01_paylearn > 0
						and paym01_no = '" . $Rs["paym01_no"] . "'
					";	
					$NewSql -> db_query($str) or die("SQL ERROR 3.1.9");
					*/
					$InsertMem = "";
					if($Rs["memm01_no"] != $Rs["prjmemm01_no"] & $Rs["prjmemm01_no"] != ""){
						$InsertMem = $Rs["prjmemm01_no"];
					}else if($Rs["memm01_no"] != $Rs["ADD_ID"]){
						$InsertMem = $Rs["ADD_ID"];
					}
					if($InsertMem != ""){
						$str = "
							Insert into pay_t02(
								payt02_type ,paym01_no ,memm01_no ,payt02_memo 
								,payt02_remark ,payt02_paytotal ,payt02_paytotalAfter ,payt02_from ,payt02_from2  
								,payt02_date ,payt02_Sort		
								,ADD_ID ,ADD_DATE ,ADD_TIME									
							)
							Select '$Type'," . $Rs["paym01_no"] . " , " . $InsertMem . " , '" . $Memo . "' 
							, '" . $Rs["paym01_remark"] . "'," . $Rs["paym01_total"]*-1 . " ," . $paytotalAfter*-1 . ", '" . $Rs["memm01_no"] . "', '" . $Rs["firmm01_no"] . "'
							, '" . $Rs["paym01_paydate"] . "',1 
							, '" . $_SESSION["MemNO"] . "','" . date('Ymd') . "','" . date('His') . "'
						";
						$NewSql -> db_query($str) or die("SQL ERROR 3.1.3");					
					}	
				}else if($Rs["paym01_type1"] == "P"){
				//and $Rs["paym01_hastax"] == "Y"
				//已支出類別且含稅
				
				//稅後金額 
					$total = $Rs["paym01_total"];
					$paytotalAfter = $Rs["paym01_total"] - $Rs["paym01_paytotal"];
					$paytotal = $Rs["paym01_paytotal"];
					$InsertMem = "";
				//公司資料
				/*
					if($Rs["prjm01_nm"] != ''){
						$Memo .= " : " . $Rs["prjm01_nm"];
					}
					if($Rs["paym01_prjcontent"] != ''){
						$Memo .= " : " . $Rs["paym01_prjcontent"];
					}	
				*/				
					//帳務對象
					/*
					if($Rs["prjmemm01_no"] != ""){
						$InsertMem = $Rs["prjmemm01_no"];
					}else if($Rs["ADD_ID"] != ""){
						$InsertMem = $Rs["ADD_ID"];
					}
					*/
					$str = "
						Insert into pay_t02(						
							payt02_type ,paym01_no ,memm01_no , firmm01_no,payt02_memo 
							,payt02_remark ,payt02_paytotal ,payt02_paytotalAfter ,payt02_from  
							,payt02_date ,payt02_Sort, payt02_drawback
							,ADD_ID ,ADD_DATE ,ADD_TIME
						)
						Select '$Type'," . $Rs["paym01_no"] . " , '" . $Rs["ADD_ID"] . "' , '', '" . $Memo . "' 
						, '" . $Rs["paym01_remark"] . "'," . $total . " ," . $paytotalAfter . " , '" . $InsertMem . "'
						, '" . $Rs["paym01_paydate"] . "',1, 'Y'
						, '" . $Rs["ADD_ID"] . "','" . date('Ymd') . "','" . date('His') . "'
					";	
					$NewSql -> db_query($str) or die("SQL ERROR 3.1.4");					 
				}
				break;										
			case 'I':
			//取出資訊
				$str = "
					Select int01_no ,T.inm01_no , M.ADD_ID ,int01_date ,int01_incometotal
					,cm.comm01_nicknm ,prj.prjm01_nm, M.inm01_content
					,T.int01_remark
					From in_t01 T
					Left join in_m01 M on T.inm01_no = M.inm01_no
					Left join com_m01 cm on M.comm01_no = cm.comm01_no
					Left join prj_m01 prj on M.prjm01_no = prj.prjm01_no
					where int01_no = '$TypeKey'				
				";
				$SqlRun = $NewSql -> db_query($str) or die("SQL ERROR 3.1.4");	
				$Rs = $NewSql -> db_fetch_array($SqlRun);
				
				//更新扣繳
				//InAdence($NewSql,$Rs["inm01_no"]);
				
				//明細金額:檢查是否需要扣掉扣繳
				$str = "
					SELECT 
						Case 
							when inm01_incometotal = " . $Rs["int01_incometotal"] . " Then 'Y'
							Else 'N'
						End Income
						,inm01_Advance
					FROM in_m01
					WHERE inm01_no =  '" . $Rs["inm01_no"] . "'				
				";
				$IncomeRun = $NewSql -> db_query($str) or die("SQL ERROR 3.1.4.1");
				$Income = $NewSql -> db_fetch_array($IncomeRun);
				if($Income["Income"] == "Y"){
					$paytotalAfter = $Rs["int01_incometotal"] - $Income["inm01_Advance"];
				}else{
					$paytotalAfter = $Rs["int01_incometotal"];
				}
				
				$Memo = $Rs["comm01_nicknm"];
				if($Rs["prjm01_nm"] != ''){
					$Memo .= ":" . $Rs["prjm01_nm"];
				}
				if($Rs["inm01_content"] != ''){
					$Memo .= ":" . $Rs["inm01_content"];
				}
			//新增資料							
				$str = "
					Insert into pay_t02(
						payt02_type ,int01_no ,memm01_no ,payt02_memo 
						,payt02_remark ,payt02_paytotal ,payt02_paytotalAfter
						,payt02_date ,payt02_Sort
						,ADD_ID ,ADD_DATE ,ADD_TIME							
					)
					Select '$Type'," . $Rs["int01_no"] . " , " . $Rs["ADD_ID"] . " , '" . $Memo . "' 
					, '" . $Rs["int01_remark"] . "'," . $Rs["int01_incometotal"] . " ," . $paytotalAfter . " 
					, '" . $Rs["int01_date"] . "',1 
					, '" . $_SESSION["MemNO"] . "','" . date('Ymd') . "','" . date('His') . "'
				";			
				$NewSql -> db_query($str) or die("SQL ERROR 3.1.5");
				break;
			case 'F':
			//取出資訊
				$str = "
					Select foundt01_no ,memm01_no ,foundt01_type ,foundt01_content ,foundt01_total,foundt01_total,foundt01_remark ,foundt01_DateSt ,foundt01_DateEd, foundt01_who
					From  found_t01
					where foundt01_no = '$TypeKey'				
				";
				$SqlRun = $NewSql -> db_query($str) or die("SQL ERROR 3.1.6");	
				$Rs = $NewSql -> db_fetch_array($SqlRun);
				
				switch ($Rs["foundt01_type"]){
					case '1':
						$Memo = "應加";
						$total = $Rs["foundt01_total"];
						break;
					case '2':
						$Memo = "應扣";	
						$total = $Rs["foundt01_total"]*-1;				
						break;
				}
				$Memo .= ":" . $Rs["foundt01_content"];
					
			//新增資料
				$D = $Rs["foundt01_DateSt"]; 
				if ($sYM != ""){$D = $sYM . "06";}				
				$str = "
					Insert into pay_t02(
						payt02_type ,foundt01_no ,memm01_no ,payt02_memo 
						,payt02_remark ,payt02_paytotal ,payt02_paytotalAfter 
						,payt02_date ,payt02_Sort
						,ADD_ID ,ADD_DATE ,ADD_TIME		
					)
					Select 
					'$Type'," . $Rs["foundt01_no"] . " , " . $Rs["memm01_no"] . " , '" . $Memo . "' 
					, '" . $Rs["foundt01_remark"] . "',0," . $total . " 
					, '" . $D . "',1
					, '" . $_SESSION["MemNO"] . "','" . date('Ymd') . "','" . date('His') . "'
				";	
				$NewSql -> db_query($str) or die("SQL ERROR 3.1.7");
					
				if( $Rs["foundt01_who"] != "" ){
					$str = "
						Insert into pay_t02(
							payt02_type ,foundt01_no ,memm01_no ,payt02_memo 
							,payt02_remark ,payt02_paytotal ,payt02_paytotalAfter 
							,payt02_date ,payt02_Sort
							,ADD_ID ,ADD_DATE ,ADD_TIME		
						)
						Select 
						'$Type'," . $Rs["foundt01_no"] . " , " . $Rs["foundt01_who"] . " , '" . $Memo . "' 
						, '" . $Rs["foundt01_remark"] . "',0," . $total * -1 . " 
						, '" . $D . "',1
						, '" . $_SESSION["MemNO"] . "','" . date('Ymd') . "','" . date('His') . "'
					";	
					$NewSql -> db_query($str) or die("SQL ERROR 3.1.7");

				}
				break;
		}
		
	}
/*
	代碼: 4.1
	撰寫日期:2014.12.28
	撰寫功能:InsertpayStauts 應付明細記錄
	參數:
		$prjm01_no(KEY)
		$status(狀態)
*/
	function InsertpayStauts($NewSql,$prjm01_no,$status){
			unset($RowArr);
			unset($Arr);
			$Sql = " 
				Select *
				From prj_t01
			";
			$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
			$i = 0;
			while($Data = $NewSql -> db_field($initRun)){
				switch ($Data -> name){
					case 'prjt01_no':
						break;
					case 'prjm01_no':
						$RowArr[$i] = $Data -> name;
						$Arr[$i]	 = $prjm01_no;			
						break;
					case 'prjt01_state';
						$RowArr[$i] = $Data -> name;
						$Arr[$i]	 = $status;
						break;
					case 'ALTER_ID':
						$RowArr[$i] = $Data -> name;
						$Arr[$i]	 = $_SESSION["MemNO"];
						break;
					case 'ALTER_DATE':
						$RowArr[$i] = $Data -> name;
						$Arr[$i]	 = DspDate(date('Ymd'));
						break;
					case 'ALTER_TIME':					
						$RowArr[$i] = $Data -> name;
						$Arr[$i] = DspTime(date('His'));				
						break;											
					default:
						$RowArr[$i] = $Data -> name;
						$Arr[$i]	 = xRequest($Data -> name);
						break;			
				}
				if($RowArr[$i] != ""){
					$i ++;
				}				
			}
		
			//新增資料
			Insert($NewSql,"prj_t01",$RowArr,$Arr);		
	}
	
	function foundt03($NewSql,$sY,$sM){
		$str = "
			Select foundt01_no ,memm01_no ,foundt01_type ,foundt01_content ,foundt01_total ,foundt01_remark
			From found_t01
			where foundt01_times= '2'
			and foundt01_DateSt <= '" . $sY.$sM . "06' and (foundt01_DateEd = '' or foundt01_DateEd >= '" . $sY.$sM . "05')
		";
		$fRun = $NewSql -> db_query($str) or die("SQL ERROR 2");
		
		while($f = $NewSql -> db_fetch_array($fRun)){
			$str = "
				Select *
				From pay_t02
				where payt02_date >= '" . $sY.$sM . "06'
				and payt02_date <= '" . date("Ym05",strtotime("+1 months",strtotime($sY . $sM . "05"))) . "'
				and foundt01_no = '" . $f["foundt01_no"] . "'
				and memm01_no = '" . $f["memm01_no"] . "'			
			";
			$ExistRun = $NewSql -> db_query($str) or die("SQL ERROR 2");
			$Exist = $NewSql -> db_num_rows($ExistRun);	
			
			//檢查是否付款
			$str = "
				Select *
				From found_t03
				where memm01_no = '" . $f["memm01_no"] . "'
				and foundt03_year = '$sY'
				and foundt03_month = '$sM'
			";
			$Exist2Run = $NewSql -> db_query($str) or die("SQL ERROR 2");
			$Exist2 = $NewSql -> db_num_rows($Exist2Run);	
			
			//檢查是否有新增
			$str = "
				Select foundt04_no ,foundt04_payYM ,foundt01_no 
				From found_t04
				where foundt01_no = '" . $f["foundt01_no"] . "'
				and foundt04_payYM >= '" . $sY.$sM . "06'
				and foundt04_payYM <= '" . date("Ym05",strtotime("+1 months",strtotime($sY . $sM . "05"))) . "'				
			";		
			$Exist3Run = $NewSql -> db_query($str) or die("SQL ERROR 3");
			$Exist3 = $NewSql -> db_num_rows($Exist3Run);
			
			if ($Exist2 == ""){$Exist2 = 0;}	
			if ($Exist3 == ""){$Exist3 = 0;}	
			if($Exist <= 0 and $Exist3 <= 0){
				InsertSalary($NewSql,"F",$f["foundt01_no"],$f["memm01_no"],$sY.$sM);
			}
		}
	}

/*
	代碼: 5.1
	撰寫日期:2015.1.11
	撰寫功能:SqlOrder Sql 排序參數
	參數:
		$Default 預設欄位
		$Culm 排序欄位
*/	
	function SqlOrder($Default,$Culm,$ad){
		$Order = "";
		if($Default != ''){
			$Order = " order by $Default desc ";
		}
		if($Culm == ''){
			return $Order;
		}else{
			$Order = " order by $Culm $ad ";
			return $Order;
		}
	}
/*
	代碼: 5.2
	撰寫日期:2015.1.11
	撰寫功能:SqlOrder Sql 排序url html
	參數:
		$UrlPageCon Url + 參數
		$nowCulm 排序欄位
		$Culm 原始排序欄位
		$ad 排序方使
*/	
	function OrderUrl($UrlPageCon,$nowCulm,$Culm,$ad){
		$NewUrl = "";
		$OD = $UrlPageCon;
		if($nowCulm == $Culm){
			$OD .= "&OD=" . $nowCulm ;
			if($ad == "desc"){
				$OD .= "&Des=asc";
				$NewUrl = '<div style="float:left;"><a class="down-b" href="' . $OD . '"></a></div>';
			}else{
				$OD .= "&Des=desc";	
				$NewUrl = '<div style="float:left;"><a class="up-b" href="' . $OD . '"></a></div>';			
			}		
		}else{
			$OD = $UrlPageCon . "&OD=" . $nowCulm . "&Des=desc";
			$NewUrl = '<div style="float:left;"><a class="up-b" href="' . $OD . '"></a></div>';
		}
		return $NewUrl;
	}
	
	function PayStatus($NewSql,$payT02,$payDate){
		$str = "
			Select paym01_no
			From pay_t02
			where 1 = 1
			and paym01_no != ''
			and payt02_no = '" . $payT02 . "'
		";
		$paym01Run = $NewSql -> db_query($str) or die("SQL ERROR 1");
		$paym01 = $NewSql -> db_result($paym01Run);		
		
		if($paym01 != 0){
			$str = "
				Update pay_m01 Set
				paym01_paydate = '" . $payDate . "'
				, paym01_haspay = 'Y'
				where paym01_no = '" . $paym01 . "'
			";
			$NewSql -> db_query($str) or die("SQL ERROR 2");
		}
	}
?>