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
	$GetTitle = GetTitle($NewSql,$GetFileCode);
	$num_pages = xRequest("page");
	
	$Culm = xRequest("OD");
	$ad = xRequest("Des");
	$OderBy = SqlOrder("prjm01_startDate",$Culm,$ad);
		
	$PageCon = "";	
	$LevelLimit = "";

//頁碼
	$num_pages = xRequest("page");
	if($num_pages != ""){
		$PageCon .= "&page=$num_pages";
	}	
	
	$DateSt = xRequest("DateSt");
	$DateEd = xRequest("DateEd");
	$sct02 = xRequest("sct02");
	$sCom = xRequest("sCom");
	$sMem = xRequest("sMem");
	$sSreach = xRequest("sSreach");
	if($DateSt == '' and $DateEd == ''){
		$DateSt = date("Ymd",strtotime("-1 year"));
		$DateEd = date('Ymd');		
	}	
//搜尋
	$mywhere = " where 1 = 1 ";	
	$LevelLimit = LevelLimit($NewSql,"pm1.memm01_no");
	//$mywhere .= ($LevelLimit != ''?" and " . $LevelLimit:"");
	$Sql = " 
		Select codet02_no,codet02_nm
		From code_t02
		Order by codet02_no 
	";
	$ct02Run = $NewSql -> db_query($Sql) or die("SQL ERROR1");
	
	$Sql = "
		Select comm01_no,comm01_nicknm
		From com_m01
		where comm01_type3 = 'Y'	
		order by 
			(case when length(left(comm01_nicknm,1)) != character_length(left(comm01_nicknm,1)) Then 0 Else 1 End)
			, CAST(CONVERT(left(comm01_nicknm,1) using big5) AS BINARY)
	";
	$comm01Run = $NewSql -> db_query($Sql) or die("SQL ERROR 2");

	if($sct02 != ""){
		$mywhere .= " and pm1.t02_no = '$sct02'";
		$PageCon .= "&sct02=$sct02";		
	}else{
		$mywhere .= " and pm1.t02_no != '4'";
	}
	if($sCom != ""){
		$mywhere .= " and cm1.comm01_no = '$sCom'";
		$PageCon .= "&sCom=$sCom";		
	}
	if($sMem != ""){
		$mywhere .= " and (GPnick like '%$sMem%' or memm01_nick like '%$sMem%') ";
		$PageCon .= "&sMem=$sMem";	
	}	
	if($sSreach != ""){
		$mywhere .= " and (pm1.prjm01_nm like '%$sSreach%')";
		$PageCon .= "&sSreach=$sSreach";		
	}
	if($DateSt != "" & $DateEd != ""){
		$mywhere .= " and prjm01_startDate >= '$DateSt'";
		$mywhere .= " and prjm01_startDate <= '$DateEd'";	
	}else if($DateSt != ""){
		$mywhere .= " and prjm01_startDate = '$DateSt'";	
	}else if($DateEd != ""){
		$mywhere .= " and prjm01_startDate = '$DateEd'";
	}
	$PageCon .= "&DateSt=$DateSt";
	$PageCon .= "&DateEd=$DateEd";
//排序	
	$Culm = xRequest("OD");
	$ad = xRequest("Des");
	if($Culm != ""){
		$PageCon .= "&OD=$Culm";
	}
	if($ad != ""){
		$PageCon .= "&Des=$ad";
	}		
	
//成員
	$Sql = getMember();
	$memRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
	$memCount = $NewSql -> db_num_rows($memRun);
//總額
	$Sql = "
		Select sum(prjm01_totalmoney) as totalmoney
		, sum(Sumpaytotal + LearTotal) as Sumpaytotal
		, sum(ifnull(SumincomdeTotal,prjm01_totalmoney)) as SumincomdeTotal
		, sum(ifnull(inAdvance,0)) as inAdvance 
		From prj_m01 pm1 
		Left join com_m01 cm1 on pm1.comm01_no = cm1.comm01_no	
		Left join mem_m01 mm01 on pm1.memm01_no = mm01.memm01_no	
		Left join ( 
		  Select sum(paym01_paytotal) as Sumpaytotal,prjm01_no 
		  From pay_m01 Group by prjm01_no
		)as PT on pm1.prjm01_no = PT.prjm01_no 
		Left join ( 
		  Select sum(inm01_incometotal-inm01_Advance) as SumincomdeTotal , sum(inm01_Advance) as inAdvance ,prjm01_no 
		  From in_m01 
		  Group by prjm01_no 
		)as Inm on pm1.prjm01_no = Inm.prjm01_no 
		Left join (
			Select prjm01_no ,GROUP_CONCAT(pt02.memm01_no ) as GPno,GROUP_CONCAT(m.memm01_nick) as GPnick
			From prj_t02 pt02
			Left join mem_m01 m on pt02.memm01_no = m.memm01_no 
			group by prjm01_no
		)as J on pm1.prjm01_no  = J.prjm01_no 	
		Left join (
            SELECT prjm01_no as Learn_prj,sum(payt03_paytotal) as LearTotal
            FROM pay_t03 t03
            Left join pay_m01 m01 on t03.paym01_no = m01.paym01_no
            where prjm01_no is not null
            GROUP by prjm01_no
        )as Learn on pm1.prjm01_no = Learn.Learn_prj
		$mywhere
	";
	$culateRun = $NewSql -> db_query($Sql) or die("SQL ERROR");	
	$culate = $NewSql -> db_fetch_array($culateRun);	
//主檔	
	$Sql = " 
		Select pm1.prjm01_no, prjm01_startDate, cm1.comm01_nicknm, pm1.prjm01_nm, cc02.C02_nm, mm01.memm01_nick, prjm01_totalmoney , ct02.codet02_nm
		, (Sumpaytotal + ifnull(LearTotal,0)) as Sumpaytotal
		, ifnull(SumincomdeTotal,prjm01_totalmoney) as SumincomdeTotal
		, ifnull(inAdvance,0) as inAdvance
		, GPno, GPnick
		, case when pm1.memm01_no = '" . $_SESSION["MemNO"] . "' Then '1' when memm01_uplevel = '" . $_SESSION["MemNO"] . "' Then '1' End 'RoleAuth'
		From prj_m01 pm1 
		Left join com_m01 cm1 on pm1.comm01_no = cm1.comm01_no
		Left join code_c02 cc02 on pm1.c02_no = cc02.c02_no
		Left join mem_m01 mm01 on pm1.memm01_no = mm01.memm01_no
		Left join code_t02 ct02 on pm1.t02_no = ct02.codet02_no
		Left join (
			Select sum(paym01_paytotal) as Sumpaytotal,prjm01_no
			From pay_m01
			Group by prjm01_no			
		)as PT on pm1.prjm01_no = PT.prjm01_no
		Left join (
			Select sum(inm01_incometotal-inm01_Advance) as SumincomdeTotal , sum(inm01_Advance) as inAdvance ,prjm01_no
			From in_m01
			Group by prjm01_no
		)as Inm on pm1.prjm01_no = Inm.prjm01_no
		Left join (
			Select prjm01_no ,GROUP_CONCAT(pt02.memm01_no ) as GPno,GROUP_CONCAT(m.memm01_nick) as GPnick
			From prj_t02 pt02
			Left join mem_m01 m on pt02.memm01_no = m.memm01_no 
			group by prjm01_no
		)as J on pm1.prjm01_no  = J.prjm01_no 
		Left join (
            SELECT prjm01_no as Learn_prj,sum(payt03_paytotal) as LearTotal
            FROM pay_t03 t03
            Left join pay_m01 m01 on t03.paym01_no = m01.paym01_no
            where prjm01_no is not null
            GROUP by prjm01_no
        )as Learn on pm1.prjm01_no = Learn.Learn_prj
		$mywhere
		$OderBy,pm1.prjm01_no
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$initCount = $NewSql -> db_num_rows($initRun);

//權限
	$LevelMEM = LevelMEM($NewSql);
	switch ($LevelMEM){
		case '2':
			$ShowMoney = true;
			$AuthAlter = true;
			break;
		case '3':
			$ShowMoney = true;
			$AuthAlter = false;
			break;
		case '1':
		case '4':
			$ShowMoney = false;
			$AuthAlter = false;
			break;
	}
	
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
	$(function(){
		$(".chosen-select").chosen({
			no_results_text: "Oops, nothing found!",
			search_contains:true,
			allow_single_deselect:true			
		});	
		$(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,			
			dateFormat: 'yymmdd'
		});		
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
						<select onChange="javascript:window.location='/pjadmin/Maintain/PRJ01/PRJ01.php?<?php echo $PageCon;?>&page=' + this.value">
                        <?php for($i = 1;$i <= $PageCount;$i++){?>
                        	<option value="<?php echo $i;?>"<?php echo ($num_pages == $i?" selected":"")?>><?php echo $i;?></option>
						<?php }?>
                        </select>
                        <?php echo "/	" . $PageCount;?>
                        頁,每頁<?php echo $aPageCount;?>筆,共<?php echo $initCount;?>筆
                    </li>   
					<li>
						<input type="button" class="red" onClick="window.location='PRJ01.php'" value="全部">
                    </li>
                    <li>
                    	<input type="button" class="gary" onClick="Sreach()" value="查詢">
                    </li>                 
                    <li>
                        <form id="SreachForm" name="SreachForm" action="<?php echo $GetFileCode . '.php'?>">
                        <input type="hidden" id="OD" name="OD" value="<?php echo $Culm;?>">
                        <input type="hidden" id="Des" name="Des" value="<?php echo $ad;?>">                        
                            
                            <Select id="sct02" name="sct02" class="chosen-select" data-placeholder="類型">
                                <option value=""></option>
                                <?php while($ct02 = $NewSql -> db_fetch_array($ct02Run)){?>
                                <option value="<?php echo $ct02["codet02_no"]?>" <?php if($sct02 == $ct02["codet02_no"]){ echo " selected";}?>><?php echo $ct02["codet02_nm"]?></option>
                                <?php }?>
                            </Select>
                            
                            <Select id="sCom" name="sCom" class="chosen-select w150" data-placeholder="客戶">
                                <option value=""></option>
                                <?php while($comm01 = $NewSql -> db_fetch_array($comm01Run)){?>
                                <option value="<?php echo $comm01["comm01_no"]?>" <?php if($sCom == $comm01["comm01_no"]){ echo " selected";}?>><?php echo $comm01["comm01_nicknm"]?></option>
                                <?php }?>
                            </Select>
                            
                            <Select id="sMem" name="sMem" class="chosen-select w100" data-placeholder="成員">
                                <option value=""></option>
                                <?php while($mem = $NewSql -> db_fetch_array($memRun)){?>
                                <option value="<?php echo $mem["memm01_nick"]?>" <?php if($sMem == $mem["memm01_nick"]){ echo " selected";}?>><?php echo $mem["memm01_nick"]?></option>
                                <?php }?>
                            </Select>
                            
                            <input id="sSreach" name="sSreach" type="text" value="<?php echo $sSreach;?>" placeholder="搜尋專案名稱">
                            日期
                            <input id="DateSt" name="DateSt" type="text" maxlength="8" size="8" class="datepicker" value="<?php echo $DateSt?>"> ~ <input id="DateEd" name="DateEd" type="text" maxlength="8" size="8" class="datepicker"  value="<?php echo $DateEd;?>">
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
            <input id="PageCon" name="PageCon" type="hidden" value="<?php echo $PageCon;?>">
            <input id="DataCount" name="DataCount" type="hidden" value="<?php echo $initCount;?>">
            <input id="Code_Id" name="Code_Id" type="hidden" value="<?php echo $GetFileCode?>">
        	<table class="table-bordered">
            	<tr class="title">
                	<td class="center" width="30">序號</td>
                    <!--<td class="center" width="30"></td>-->
                    <td class="center" width="30">編輯</td>
                    <td class="center">開案日<?php echo OrderUrl($GetFileCode . ".php?" . $PageCon,"prjm01_startDate",$Culm,$ad);?></td>
                    <td class="center">客戶<?php echo OrderUrl($GetFileCode . ".php?" . $PageCon,"comm01_nicknm",$Culm,$ad);?></td>
                    <td class="left">專案名</td>
                    <td class="center">類型</td>
                    <td class="center">專案負責</td>
                    <td class="center">成員</td>
                    <td class="center">總額<?php if($ShowMoney){?><br><div style="font-weight:100;" align="right"><?php echo MoneyFormat($culate["totalmoney"]);?></div><?php }?></td>
                    <td class="center">扣繳<?php if($ShowMoney){?><br><div style="font-weight:100;" align="right"><?php echo MoneyFormat($culate["inAdvance"]);?></div><?php }?></td>
                    <td class="center">專案支出<?php if($ShowMoney){?><br><div style="font-weight:100;" align="right"><?php echo MoneyFormat($culate["Sumpaytotal"]);?></div><?php }?></td> 
                    <td class="center">累計<?php if($ShowMoney){?><br><div style="font-weight:100;" align="right"><?php echo MoneyFormat($culate["SumincomdeTotal"] - $culate["Sumpaytotal"]);?></div><?php }?></td>                    
                    <td class="center">狀態</td>
                </tr>
                <?php 
				$i = 0;
				while($Data = $NewSql -> db_fetch_array($initRun)){
					$i ++;
				?>
            	<tr class="<?php echo ($i%2 == 0)?'odd':'';?>">
                	<td class="center" width="30"><?php echo $i + (($num_pages-1)*$aPageCount);?></td>
                    <!--<td class="center" width="30"><input id="check<?php echo $i;?>" name="check<?php echo $i;?>" class="checkbox" type="checkbox" value="<?php echo $Data["comm01_no"];?>"></td>-->
                    <td class="center" width="30">
                    <?php if($AuthAlter || $Data["RoleAuth"] == "1"){?>
                    	<div align="center" class="block blue">
                        	<a class="edit" onClick="SetKey(<?php echo $Data["prjm01_no"];?>);EventClick('Alter')">編輯</a>
                        </div>
					<?php }?>
                    </td> 
                    <td class="center"><?php echo Dspdate($Data["prjm01_startDate"],'/');?></td>
                    <td class="center"><?php echo $Data["comm01_nicknm"];?></td>
                    <td class="left"><?php echo $Data["prjm01_nm"];?></td>
                    <td class="center"><?php echo $Data["C02_nm"];?></td>
                    <td class="center"><?php echo $Data["memm01_nick"];?></td>
                    <td class="center"><?php echo $Data["GPnick"];?></td>                    
                    <td class="right">
					<?php 
						if($ShowMoney || $Data["RoleAuth"] == "1"){
							echo MoneyFormat($Data["prjm01_totalmoney"]);
						}
					?>
                    </td>
                    <td class="right">
					<?php 
						if($ShowMoney || $Data["RoleAuth"] == "1"){
							echo MoneyFormat($Data["inAdvance"]);
						}
					?>
                    </td> 
                                       
                    <td class="right">
					<?php 
						if($ShowMoney || $Data["RoleAuth"] == "1"){
							echo MoneyFormat($Data["Sumpaytotal"]);
						}
					?>
                    </td> 
                    <td class="right <?php if($Data["SumincomdeTotal"] - $Data["Sumpaytotal"] < 0){echo 'redfont';}?>">
					<?php 
						if($ShowMoney || $Data["RoleAuth"] == "1"){
							echo MoneyFormat($Data["SumincomdeTotal"] - $Data["Sumpaytotal"]);
						}
					?>
                    </td>
                    <td class="center"><?php echo $Data["codet02_nm"];?></td>
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