<?php
//*****************************************************************************************
//		撰寫日期：
//		程式功能：
//		使用參數：
//*****************************************************************************************
	header ('Content-Type: text/html; charset=utf-8');
	session_start();
//函式庫
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config.ini.php");
	LoginChk(GetFileCode(__FILE__),"1");
//資料庫連線
	$NewSql = new mysql();		
//參數
	$GetFileCode = GetFileCode(__FILE__);
	$GetFileType = GetFileType(__FILE__);	
	$GetTitle = GetTitle($NewSql,$GetFileCode);
		
	$PageCon = "";
	$LevelLimit = "";	
//頁碼
	$num_pages = xRequest("page");
	if($num_pages != ""){
		$PageCon .= "&page=$num_pages";
	}	
//搜尋	
	$sCom = xRequest("sCom");
	$sSreach = xRequest("sSreach");
	$sAllPrj = xRequest("sAllPrj");
	$sS = xRequest("sS");
	$DateSt = xRequest("DateSt");
	$DateEd = xRequest("DateEd");
	if($DateSt == '' and $DateEd == ''){
		$DateSt = date("Ymd",strtotime("-1 year"));
		$DateEd = date('Ymd');		
	}
	$mywhere = " where 1 = 1 ";	
	$LevelLimit = LevelLimit($NewSql,"I01.ADD_ID");
	$mywhere .= ($LevelLimit != ''?" and " . $LevelLimit:"");

	$Sql = "
		Select comm01_no,comm01_nicknm
		From com_m01
		where comm01_type3 = 'Y'		
		order by 
			(case when length(left(comm01_nicknm,1)) != character_length(left(comm01_nicknm,1)) Then 0 Else 1 End)
			, CAST(CONVERT(left(comm01_nicknm,1) using big5) AS BINARY)
	";
	$sComm01Run = $NewSql -> db_query($Sql) or die("SQL ERROR 2");

	if($sCom != ""){
		$mywhere .= " and cm1.comm01_no = '$sCom'";
		$PageCon .= "&sCom=$sCom";
	}
	if($sSreach != ""){
		$mywhere .= " and (prj.prjm01_nm like '%$sSreach%' or I01.inm01_content like '%$sSreach%' or inm01_invoiceno like '%$sSreach%' )";
		$PageCon .= "&sSreach=$sSreach";
	}
	if($sS != ""){
		$mywhere .= " and I01.inm01_type = '$sS'";
		$PageCon .= "&sS=$sS";
	}	
	if($DateSt != "" & $DateEd != ""){
		$mywhere .= " and inm01_invoicedate >= '$DateSt'";
		$mywhere .= " and inm01_invoicedate <= '$DateEd'";	
	}else if($DateSt != ""){
		$mywhere .= " and inm01_invoicedate = '$DateSt'";	
	}else if($DateEd != ""){
		$mywhere .= " and inm01_invoicedate = '$DateEd'";
	}
	$PageCon .= "&DateSt=$DateSt";
	$PageCon .= "&DateEd=$DateEd";
	
	if($sAllPrj != ''){
		$PageCon .= "&sAllPrj=$sAllPrj";
	}else{
		$mywhere .= " and prj.t02_no != '4'";
	}
	
//排序	
	$Culm = xRequest("OD");
	$ad = xRequest("Des");
	if($Culm != ""){
		$PageCon .= "&OD=$Culm";
	}
	if($ad != ""){
		$PageCon .= "&Des=$ad";
	}		
	$OderBy = SqlOrder("inm01_invoicedate",$Culm,$ad);
	$Sql = " 
		Select I01.inm01_no, mem_m01.memm01_nick, I01.ADD_DATE, inm01_invoicedate , comm01_nicknm, I01.comm01_quid, inm01_invoiceno ,inm01_subtotal ,inm01_tax ,inm01_total 
		, inm01_type, I01.inm01_type, codet05_nm, inm01_incometotal, inm01_hasinvoice , incomeDate, Paytotal
		, I01.prjm01_no
		, I01.inm01_Advance
		, prj.prjm01_nm, I01.inm01_content
		, prj.prjm01_Quoid
		From in_m01 I01
		Left join prj_m01 prj on I01.prjm01_no = prj.prjm01_no
		Left join com_m01 cm1 on I01.comm01_no = cm1.comm01_no
		Left join code_t05 t05 on I01.inm01_type = t05.codet05_code
		Left join mem_m01 on I01.ADD_ID = mem_m01.memm01_no
		Left join (
			Select Max(int01_date) as incomeDate,inm01_no
			From in_t01
			Group by inm01_no		
		)as De on I01.inm01_no = De.inm01_no
		Left join (
			Select sum(paym01_subtotal) as Paytotal,prjm01_no
			From pay_m01
			Group by prjm01_no		
		)as pay on I01.prjm01_no = pay.prjm01_no
		$mywhere
		$OderBy,I01.inm01_no
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$initCount = $NewSql -> db_num_rows($initRun);
	

	$aPageCount = "15";
	Page($NewSql,$initCount,$Sql,$aPageCount);	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	$(document).ready(function(){
		Shadowbox.init({
			overlayOpacity: 0.4,
			modal: true,
			onClose:function(){location.reload();}
		});
		
		//$('#sCom').chosen({ allow_single_deselect: true });
	});
	$(function(){
		$(".chosen-select").chosen({
			no_results_text: "Oops, nothing found!",
			search_contains:true,	
			allow_single_deselect: true
		});
		$(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,			
			dateFormat: 'yymmdd'
		});
		
		$("#sb-nav-close").click(function(){
			//alert('werf')
	  })	
	})
	function EventClick(event){
		switch (event){
			case 'Add':
				$('#KeyForm').attr('action','<?php echo $GetFileCode . '_Add.php'?>')
				$('#KeyForm').submit();
				break;
			case 'Alter':
				$('#KeyForm').attr('action','<?php echo $GetFileCode . '_Alter.php'?>')
				$('#KeyForm').submit();
				break;
			case 'View':
				$('#KeyForm').attr('action','<?php echo $GetFileCode . '_View.php'?>')
				$('#KeyForm').submit();
				break;			
			case 'Del':
				if($('.checkbox:checked').length <= 0){
					alert("請勾選要刪除的資料")
				}else{
					$('#KeyForm').attr('action','Code_Delete.php')
					$('#KeyForm').submit();					
				}
				break;
			case 'Setting':
				$('#KeyForm').attr('action','mug04.php')
				$('#KeyForm').submit();
				break;			
				break;
			default:
				break;
		}
	}
	function SetKey(Key){
		$('#DataKey').val(Key)		
	}
	function Sreach(){
		$("#SreachForm").submit()
	}	
</script>
</head>
<body>
<div id="wrapper">

<div class="side-content">
	<ul class="breadcrumb">
    	<li><?php echo $GetTitle;?></li>
    	<li></li>        
    </ul>
	<div class="box">
        <div class="box-content">
            <div class="box-head">
            	<ul class="btn">
                	<li><input class="green" type="button" value="Add" onClick="EventClick('Add')"></li>
                	<!--<li><input class="red" type="button" value="Delete" onClick="EventClick('Del')"></li>-->
                    <li style="padding:0 10px;">
                    	第
						<select onChange="javascript:window.location='/pjadmin/Maintain/PRJ03/PRJ03.php?<?php echo $PageCon;?>&page=' + this.value">
                        <?php for($i = 1;$i <= $PageCount;$i++){?>
                        	<option value="<?php echo $i;?>"<?php echo ($num_pages == $i?" selected":"")?>><?php echo $i;?></option>
						<?php }?>
                        </select>
                        <?php echo "/	" . $PageCount;?>
                        頁,每頁<?php echo $aPageCount;?>筆,共<?php echo $initCount;?>筆
                    </li>
					<li>
						<input type="button" class="red" onClick="window.location='PRJ03.php'" value="全部">
                    </li>
                    <li>
                    	<input type="button" class="gary" onClick="Sreach()" value="查詢">
                    </li>                     
                    <li>
                        <form id="SreachForm" name="SreachForm" action="<?php echo $GetFileCode . '.php'?>">
                        <input type="hidden" id="OD" name="OD" value="<?php echo $Culm;?>">
                        <input type="hidden" id="Des" name="Des" value="<?php echo $ad;?>">                        
                            
                            <Select id="sCom" name="sCom" data-placeholder="客戶" class="chosen-select w150">
                                <option value=""></option>
                                <?php while($sComm01 = $NewSql -> db_fetch_array($sComm01Run)){?>
                                <option value="<?php echo $sComm01["comm01_no"]?>" <?php if($sCom == $sComm01["comm01_no"]){ echo " selected";}?>><?php echo $sComm01["comm01_nicknm"]?></option>
                                <?php }?>
                            </Select>
                            <Select id="sS" name="sS" data-placeholder="狀態" class="chosen-select w100">
                            	<option value=""></option>
                            	<option value="5" <?php if($sS == "5"){ echo " selected";}?>>待發票</option>
                            	<option value="1" <?php if($sS == "1"){ echo " selected";}?>>請款中</option>
                            	<option value="2" <?php if($sS == "2"){ echo " selected";}?>>已入帳</option>
                            </Select>
                            <input id="sSreach" name="sSreach" type="text" value="<?php echo $sSreach;?>" placeholder="搜尋專案名稱">
                            日期
                            <input id="DateSt" name="DateSt" type="text" size="10" maxlength="8" class="datepicker" value="<?php echo $DateSt?>"> ~ <input id="DateEd" name="DateEd" type="text" size="10" maxlength="8" class="datepicker"  value="<?php echo $DateEd;?>">
                            <input id="sAllPrj" name="sAllPrj" type="checkbox" value="Y"<?php echo ($sAllPrj == 'Y'?" checked":"");?>>所有專案
                        </form>
                    </li>                   
                </ul>
                <!--
            	<ul class="Src">
                	<li>搜尋</li>
                	<li>搜尋</li>
                	<li>搜尋</li>                                        
                </ul>
                -->
            </div>   
			<form id="KeyForm" name="KeyForm" method="post">
            <input id="DataKey" name="DataKey" type="hidden">
            <input id="DataCount" name="DataCount" type="hidden" value="<?php echo $initCount;?>">
            <input id="Code_Id" name="Code_Id" type="hidden" value="<?php echo $GetFileCode?>">
			<input id="PageCon" name="PageCon" type="hidden" value="<?php echo $PageCon;?>">
        	<table class="table-bordered">
            	<tr class="title">
                	<td class="center" width="30">序號</td>
                    <!--<td class="center" width="30"></td>-->
                    <td class="center" width="30">編輯</td>
                    <td class="center" width="30">入帳</td>                    
                    <td class="center">負責人<?php echo OrderUrl($GetFileCode . ".php?" . $PageCon,"I01.ADD_ID ",$Culm,$ad);?></td>
                    <td class="center">開立日<?php echo OrderUrl($GetFileCode . ".php?" . $PageCon,"inm01_invoicedate",$Culm,$ad);?></td>
                    <td class="center">客戶<?php echo OrderUrl($GetFileCode . ".php?" . $PageCon,"I01.comm01_no",$Culm,$ad);?></td>
                    <td class="left">專案/內容<?php echo OrderUrl($GetFileCode . ".php?" . $PageCon,"prjm01_nm",$Culm,$ad);?></td>
                    <td class="center">統編<?php echo OrderUrl($GetFileCode . ".php?" . $PageCon,"comm01_quid",$Culm,$ad);?></td>
                    <td class="center">報價單號</td>
                    <td class="center">發票號碼<?php echo OrderUrl($GetFileCode . ".php?" . $PageCon,"inm01_invoiceno",$Culm,$ad);?></td>
                    <td class="center">未稅額</td>
                    <td class="center">稅</td>
                    <td class="center">應收</td>
                    <td class="center">入帳日</td>
                    <td class="center">實收</td>
                    <td class="center">扣繳</td>
                    <!--
                    <td class="center">專案支出</td> 
                    <td class="center">累計</td>
                    -->
                    <td class="center">狀態</td>                                       
                </tr>
                <?php 
				$i = 0;
				while($Data = $NewSql -> db_fetch_array($initRun)){
					$i ++;
				?>
            	<tr class="<?php echo ($i%2 == 0)?'odd':'';?>">
                	<td class="center" width="30"><?php echo $i + (($num_pages-1)*$aPageCount);?></td>
                    <!--<td class="center" width="30"><input id="check<?php echo $i;?>" name="check<?php echo $i;?>" class="checkbox" type="checkbox" value="<?php echo $Data["inm01_no"];?>"></td>-->
                    <td class="center" width="30">
                    	<div align="center" class="block blue">
                        	<a class="edit" onClick="SetKey(<?php echo $Data["inm01_no"];?>);EventClick('Alter')" ></a>
                        </div>
                    </td> 
                    <td class="center" width="30">
                    	<div align="center" class="block blue">
                        	<a class="edit" rel="shadowbox;width=630;height=550" href="PRJ03_View.php?DataKey=<?php echo $Data["inm01_no"];?>" title="入帳內容"></a>
                        </div>
                    </td>                                        
                    <td class="center"><?php echo $Data["memm01_nick"];?></td>
                    <td class="center"><?php echo Dspdate($Data["inm01_invoicedate"],"/");?></td>
                    <td class="center"><?php echo $Data["comm01_nicknm"];?></td>
                    <?php 
						$ConentBox = "";
						if($Data["prjm01_nm"] != "" & $Data["inm01_content"] != ""){
							$ConentBox  = $Data["prjm01_nm"] . ' : ' . $Data["inm01_content"];
						}else{
							$ConentBox  = $Data["prjm01_nm"] . $Data["inm01_content"];
						}
					?>                    
                    <td class="left"><?php echo $ConentBox;?></td>                   
                    <td class="center"><?php echo $Data["comm01_quid"];?></td>
                    <td class="center"><?php echo $Data["prjm01_Quoid"];?></td>
                    <td class="center"><?php echo $Data["inm01_invoiceno"];?></td>
                    <td class="right"><?php echo MoneyFormat($Data["inm01_subtotal"]);?></td>
                    <td class="right"><?php echo MoneyFormat($Data["inm01_tax"]);?></td>
                    <td class="right"><?php echo MoneyFormat($Data["inm01_total"]);?></td>
                    <td class="center"><?php echo Dspdate($Data["incomeDate"],"/");?></td>
                    <td class="right"><?php echo MoneyFormat($Data["inm01_incometotal"]);?></td>
                    <!--
                    <td class="center">
                    <?php 
						echo $Data["inm01_Advance"];
					/*
						switch($Data["inm01_hasinvoice"]){
							case 'Y':
								if($Data["prjm01_no"] != ""){
									echo ($Data["inm01_subtotal"])*.12;
								}else{
									echo ($Data["inm01_subtotal"] - $Data["paym01_subtotal"])*.12;									
								}
								break;
							case 'N':
								if($Data["prjm01_no"] != ""){//Paytotal
									echo ($Data["inm01_subtotal"] - $Data["paym01_subtotal"])*.05 - ($Data["paym01_subtotal"]*.07);
								}else{
									echo ($Data["inm01_subtotal"])*.05;									
								}
								break;								
						}
					*/
					?>
                    </td>
                    <td class="center"><?php echo $Data["Paytotal"];?></td>
                    --> 
                    <td class="right">
                    <?php 
						echo MoneyFormat($Data["inm01_Advance"]);
					/*
						switch($Data["inm01_hasinvoice"]){
							case 'Y':
								echo $Data["inm01_incometotal"] - $Data["Paytotal"] - $Data["inm01_Advance"];
								break;
							case 'N':
								echo $Data["inm01_incometotal"] - $Data["Paytotal"] - $Data["inm01_Advance"];
								break;								
						}
					*/
					?>                    
                    </td>
                    <td class="center">
                    	<?php 
							switch ($Data["inm01_type"]){
								case '1':
									echo '<span class="block red whitetxt">' . $Data["codet05_nm"] . '</span>';
									break;
								case '2':
									echo '<span class="block green whitetxt">' . $Data["codet05_nm"] . '</span>';								
									break;								
								case '3':
									echo '<span class="block gary whitetxt">' . $Data["codet05_nm"] . '</span>';								
									break;		
								case '4':
									echo '<span class="block gary whitetxt">' . $Data["codet05_nm"] . '</span>';								
									break;
								case '5':
									echo '<span class="block gary whitetxt">' . $Data["codet05_nm"] . '</span>';								
									break;
								case '6':
									echo '<span class="block gary whitetxt">' . $Data["codet05_nm"] . '</span>';								
									break;	
								default:
									echo $Data["inm01_type"];														
							}
						?>
                    </td> 
                </tr>
                <?php }?>                               
            </table>
			</form>            
        </div>
    </div>
</div>

</div>
</body>
</html>