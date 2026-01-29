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
	$num_pages = xRequest("page");
		
	$PageCon = "";
	$LevelLimit = "";
//頁碼
	$num_pages = xRequest("page");
	if($num_pages != ""){
		$PageCon .= "&page=$num_pages";
	}	
			
	$DateSt = xRequest("DateSt");
	$DateEd = xRequest("DateEd");	
	$stype1 = xRequest("stype1");
	$sCom = xRequest("sCom");
	$shaspay = xRequest("shaspay");
	$sSreach = xRequest("sSreach");	
	$sAllPrj = xRequest("sAllPrj");
	if($DateSt == '' and $DateEd == ''){
		$DateSt = date("Ymd",strtotime("-1 year"));
		$DateEd = date('Ymd');		
	}	
	
//搜尋
	$mywhere = " where 1 = 1 ";
	$LevelLimit = LevelLimit($NewSql,"pm01.ADD_ID");
	if($LevelLimit != ''){
		$mywhere .= ($LevelLimit != ''?" and ( " . $LevelLimit:"");
		$LevelLimit = LevelLimit($NewSql,"pm01.memm01_no");
		$mywhere .= ($LevelLimit != ''?" or (paym01_type1 = 'M' and " . $LevelLimit . ")":"");
		$mywhere .= " )";
	}
	$Sql = "
		Select comm01_no,comm01_nicknm
		From com_m01
		where comm01_type3 = 'Y'		
		order by 
			(case when length(left(comm01_nicknm,1)) != character_length(left(comm01_nicknm,1)) Then 0 Else 1 End)
			, CAST(CONVERT(left(comm01_nicknm,1) using big5) AS BINARY)
	";
	$comm01Run = $NewSql -> db_query($Sql) or die("SQL ERROR 2");
 

	if($stype1 != ""){
		$mywhere .= " and paym01_type1 = '$stype1'";
		$PageCon .= "&stype1=$stype1";		
	}
	if($sCom != ""){
		$mywhere .= " and pm01.comm01_no = '$sCom'";
		$PageCon .= "&sCom=$sCom";
	}
	if($shaspay != ""){
		$mywhere .= " and ifnull(paym01_haspay,'N') = '$shaspay'";
		$PageCon .= "&shaspay=$shaspay";
	}	
	if($sSreach != ""){
		$mywhere .= " and (pj01.prjm01_nm like '%$sSreach%')";
		$PageCon .= "&sSreach=$sSreach";
	}
	if($DateSt != "" & $DateEd != ""){
		$mywhere .= " and ADD_DATE >= '$DateSt'";
		$mywhere .= " and ADD_DATE <= '$DateEd'";	
	}else if($DateSt != ""){
		$mywhere .= " and ADD_DATE = '$DateSt'";	
	}else if($DateEd != ""){
		$mywhere .= " and ADD_DATE = '$DateEd'";
	}
	$PageCon .= "&DateSt=$DateSt";
	$PageCon .= "&DateEd=$DateEd";
	
	
	if($sAllPrj != ''){
		$PageCon .= "&sAllPrj=$sAllPrj";
	}else{
		$mywhere .= " and pj01.t02_no != '4'";
	}
//排序
	$Culm = xRequest("OD");
	$ad = xRequest("Des");
	$OderBy = SqlOrder("ADD_DATE",$Culm,$ad);
	if($Culm != ""){
		$PageCon .= "&OD=$Culm";
	}
	if($ad != ""){
		$PageCon .= "&Des=$ad";
	}	
	
	$Sql = " 
		Select paym01_no , addm01.memm01_nick , cm01f.comm01_nicknm,ADD_DATE ,paym01_type1 
		,pj01.prjm01_nm 
		,paym01_prjcontent ,paym01_total , paym01_paytotal, paym01_paydate 
		,paym01_haspay ,paym01_invoicedate ,paym01_invoiceno
		, case 
			when paym01_type1 = 'F' Then cm01f.comm01_nicknm
			when paym01_type1 = 'M' Then form01.memm01_nick
		End forrole
		From pay_m01 pm01
		Left join mem_m01 addm01 on pm01.ADD_ID = addm01.memm01_no
		Left join com_m01 cm01f on pm01.firmm01_no = cm01f.comm01_no
		Left join mem_m01 form01 on pm01.memm01_no = form01.memm01_no
		Left join prj_m01 pj01 on pm01.prjm01_no = pj01.prjm01_no
		$mywhere
		and paym01_type1 != 'P'
		$OderBy,paym01_no
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
					$('#KeyForm').attr('action','<?php echo $GetFileCode . '_Delete.php'?>')
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
<style>
/*
.active-result{
	float:none;
	width:90px;
}
*/
</style>
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
                    <li style="padding:0 10px;">
                    	第
						<select onChange="javascript:window.location='/pjadmin/Maintain/PRJ02/PRJ02.php?<?php echo $PageCon;?>&page=' + this.value">
                        <?php for($i = 1;$i <= $PageCount;$i++){?>
                        	<option value="<?php echo $i;?>"<?php echo ($num_pages == $i?" selected":"")?>><?php echo $i;?></option>
						<?php }?>
                        </select>
                        <?php echo "/	" . $PageCount;?>
                        頁,每頁<?php echo $aPageCount;?>筆,共<?php echo $initCount;?>筆
                    </li> 
					<li>
						<input type="button" class="red" onClick="window.location='PRJ02.php'" value="全部">
                    </li>
                    <li>
                    	<input type="button" class="gary" onClick="Sreach()" value="查詢">
                    </li>                    
                    <li>
                        <form id="SreachForm" name="SreachForm" action="<?php echo $GetFileCode . '.php'?>">
                        <input type="hidden" id="OD" name="OD" value="<?php echo $Culm;?>">
                        <input type="hidden" id="Des" name="Des" value="<?php echo $ad;?>">
                            
                            <Select id="stype1" name="stype1" data-placeholder="類型" class="chosen-select ">
                                <option value=""></option>
                                <option value="M" <?php echo ($stype1 == "M")?" selected":"";?>>Team</option>
                                <option value="F" <?php echo ($stype1 == "F")?" selected":"";?>>外製</option>                                                                
                            </Select>
                            
                            <Select id="sCom" name="sCom" data-placeholder="客戶" class="chosen-select w150" >
                                <option value=""></option>
                                <?php while($comm01 = $NewSql -> db_fetch_array($comm01Run)){?>
                                <option value="<?php echo $comm01["comm01_no"]?>" <?php if($sCom == $comm01["comm01_no"]){ echo " selected";}?>><?php echo $comm01["comm01_nicknm"]?></option>
                                <?php }?>
                            </Select>
                            
                            <select id="shaspay" name="shaspay" data-placeholder="狀態" class="chosen-select ">
                                <option value=""></option>
                                <option value="N" <?php echo ($shaspay == "N")?" selected":"";?>>未付</option>
                                <option value="Y" <?php echo ($shaspay == "Y")?" selected":"";?>>已付</option>                            
                            </select>                            
                            
                            <input id="sSreach" name="sSreach" type="text" value="<?php echo $sSreach;?>" placeholder="搜尋專案名稱">
							日期
                            <input id="DateSt" name="DateSt" type="text" size="10" maxlength="8" class="datepicker" value="<?php echo $DateSt?>"> ~ <input id="DateEd" name="DateEd" type="text" size="10" maxlength="8" class="datepicker"  value="<?php echo $DateEd;?>">
                            <input id="sAllPrj" name="sAllPrj" type="checkbox" value="Y"<?php echo ($sAllPrj == 'Y'?" checked":"");?>>所有專案
                        </form>
                    </li>                    
                  
                </ul>
            </div>   
			<form id="KeyForm" name="KeyForm" method="post">
            <input id="DataKey" name="DataKey" type="hidden">
            <input id="DataCount" name="DataCount" type="hidden" value="<?php echo $initCount;?>" >
            <input id="Code_Id" name="Code_Id" type="hidden" value="<?php echo $GetFileCode?>" >
            <input id="PageCon" name="PageCon" type="hidden" value="<?php echo $PageCon?>" >
        	<table class="table-bordered">
            	<tr class="title">
                	<td class="center" width="30">序號</td>
                    <!--<td class="center" width="30"></td>-->
                    <td class="center" width="30">編輯</td>
                    <td class="center">負責人<?php echo OrderUrl($GetFileCode . ".php?" . $PageCon,"ADD_ID",$Culm,$ad);?></td>
                    <td class="center">日期<?php echo OrderUrl($GetFileCode . ".php?" . $PageCon,"ADD_DATE",$Culm,$ad);?></td>
                    <td class="center">類型</td>
                    <td class="center">對象<?php echo OrderUrl($GetFileCode . ".php?" . $PageCon,"forrole",$Culm,$ad);?></td>
                    <td class="left">專案/內容<?php echo OrderUrl($GetFileCode . ".php?" . $PageCon,"pm01.prjm01_no",$Culm,$ad);?></td>
                    <td class="center">金額</td>
                    <td class="center">扣抵</td>
                    <td class="center">實付</td>
                    <td class="center">付款日<?php echo OrderUrl($GetFileCode . ".php?" . $PageCon,"paym01_paydate",$Culm,$ad);?></td>
                    <td class="center">狀態</td>
                    <td class="center">發票日</td>
                    <td class="center">發票號</td>                                        
                </tr>
                <?php 
				$i = 0;
				while($Data = $NewSql -> db_fetch_array($initRun)){
					$i ++;
				?>
            	<tr class="<?php echo ($i%2 == 0)?'odd':'';?>">
                	<td class="center" width="30"><?php echo $i + (($num_pages-1)*$aPageCount);?></td>
                    <!--<td class="center" width="30"><input id="check<?php echo $i;?>" name="check<?php echo $i;?>" class="checkbox" type="checkbox" value="<?php echo $Data["paym01_no"];?>" <?php echo ($Data["paym01_haspay"] == "Y"?" disabled":"");?>></td>-->
                    <td class="center" width="30">
                    	<div align="center" class="block blue">
                        	<a class="edit" onClick="SetKey(<?php echo $Data["paym01_no"];?>);EventClick('Alter')">編輯</a>
                        </div>
                    </td>                                         
                    <td class="center"><?php echo $Data["memm01_nick"];?></td>
                    <td class="center"><?php echo Dspdate($Data["ADD_DATE"],"/");?></td>
                    <td class="center"><?php echo ($Data["paym01_type1"] == "M")?"Team":"外製";?></td>
                    <td class="center"><?php echo $Data["forrole"];?></td>
                    <?php 
						$ConentBox = "";
						if($Data["prjm01_nm"] != "" & $Data["paym01_prjcontent"] != ""){
							$ConentBox  = $Data["prjm01_nm"] . ' : ' . $Data["paym01_prjcontent"];
						}else{
							$ConentBox  = $Data["prjm01_nm"] . $Data["paym01_prjcontent"];
						}
					?>
                    <td class="left"><?php echo $ConentBox;?></td>
                    <td class="right"><?php echo MoneyFormat($Data["paym01_total"]);?></td>
                    <td class="right"><?php echo MoneyFormat($Data["paym01_total"] - $Data["paym01_paytotal"]);?></td>
                    <td class="right"><?php echo MoneyFormat($Data["paym01_paytotal"]);?></td>                    
                    <td class="center"><?php echo Dspdate($Data["paym01_paydate"],"/");?></td>
                    <td class="center">
                    <?php 
						if($Data["paym01_haspay"] == "Y"){
							echo '<div align="center" class="block blue" style="width:30px;"><a>已付</a></div>';
						}else{		
							echo '<div align="center" class="block red" style="width:30px;"><a>未付</a></div>';	
						}
					?>
                    </td>
                    <td class="center"><?php echo Dspdate($Data["paym01_invoicedate"],"/");?></td>
                    <td class="center"><?php echo $Data["paym01_invoiceno"];?></td>
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