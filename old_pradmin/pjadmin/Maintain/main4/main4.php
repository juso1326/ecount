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
	ChkLogin();
//資料庫連線
	$NewSql = new mysql();		
//參數
	$LevelMem = LevelMEM($NewSql);
	$GetFileCode = GetFileCode(__FILE__);
	$GetFileType = GetFileType(__FILE__);	
	$GetTitle = GetTitle($NewSql,$GetFileCode);
	$num_pages = xRequest("page");
	$sY = xRequest("sY");
	$sM = xRequest("sM");
	$sT = xRequest("sT");
	if ($sY == ""){
		$sY = date("Y");
	}
	if ($sM == ""){
		$sM = date("m");
	}
	//限制最初為 2014.9
	if($sY < 2014 || ($sY == 2014 and $sM < 9)){
		$sY = 2014;
		$sM = 9;
	}
	foundt03($NewSql,$sY,$sM);	//加入固定
	switch ($LevelMem){
		case '1':
		case '2':		
			$editable = true;
			$prevData = true;
			$sendable = true;
			$VendorPay =  true;
			break;
		case '3':
			$editable = true;
			$prevData = true;
			$sendable = false;
			$VendorPay =  true;
			break;
		default:
			$editable = false;
			$prevData = false;
			$sendable = false;	
			$VendorPay =  false;					
			break;
	}
	if(date('Ym') >= $sY.$sM){
		$prevData = false;
	}	

	
	if($sT == 'F'){
		header('location:main4_fire.php');
		exit();
	}
	if($sT == 'T'){
		header('location:main4_summary.php');
		exit();
	}
	if($sT == 'L'){
		header('location:main4_learn.php');
		exit();
	}	
	
	$sMem = xRequest("sMem");
	if ($sT == ""){
		$sT = "M";
	}	
	if ($sMem == "" & $sT == "M"){
		$sMem = $_SESSION["MemNO"];
	}	
	$mywhere = " where 1 = 1 ";	
	$LevelLimit = LevelLimit($NewSql,"m01.memm01_no");
	$mywhere .= ($LevelLimit != ''?" and " . $LevelLimit:"");

//員工
	$mywhere = " where 1 = 1 ";
	$LevelLimit = LevelLimit($NewSql,"memm01_no");
	$mywhere .= ($LevelLimit != ''?" and " . $LevelLimit:"");	
	$Sql = "
		Select memm01_no, memm01_nick
		From mem_m01 m01
		Left join mug_01 on m01.MUG01_NO = mug_01.MUG01_NO
		$mywhere
		and MUG01_SHOW = 'Y'
		order by m01.MUG01_NO,memm01_nick, memm01_no
	";
	$memRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
	$memCount = $NewSql -> db_num_rows($memRun);
	
//個人
	$mywhere = " where 1 = 1 ";
	$LevelLimit = LevelLimit($NewSql,"pt02.memm01_no");
	$mywhere .= ($LevelLimit != ''?" and " . $LevelLimit:"");
	
	$Sql = "
		Select pt02.* , mm01.memm01_nick ,cm01.comm01_nicknm
		From pay_t02 pt02
		Left join mem_m01 mm01 on pt02.payt02_from = mm01.memm01_no
		Left join com_m01 cm01 on pt02.payt02_from2 = cm01.comm01_no
		$mywhere
		and pt02.memm01_no = '$sMem'
		and payt02_date >= '" . $sY . $sM . "06'
		and payt02_date <= '" . date("Ym05",strtotime("+1 months",strtotime($sY . $sM . "05"))) . "'
		order by payt02_Sort  ,payt02_date	
	";
	$MRun = $NewSql -> db_query($Sql) or die("SQL ERROR 2");
	$MCount = $NewSql -> db_num_rows($MRun);
//2017 新增 教育費
	$Sql = "
		SELECT SUM(payt03_paytotalAfter) as after
		From (
			SELECT 
			case when memm01_no_pay = '$sMem' Then (payt03_paytotal * -1) else (payt03_paytotal * 1) end payt03_paytotal
			, case when memm01_no_pm = '$sMem' Then payt03_paytotalAfter * -1 else payt03_paytotalAfter * 1 end payt03_paytotalAfter
			FROM pay_t03 payt03
			Left join mem_m01 mempay on payt03.`memm01_no_pay` = mempay.memm01_no
			Left join mem_m01 mempm on payt03.`memm01_no_pm` = mempm.memm01_no
			Left join mem_m01 memfrom on payt03.`memm01_no_from` = memfrom.memm01_no
			where `payt03_paydate` >= '" . $sY . $sM . "06'
			and `payt03_paydate` <= '" . date("Ym05",strtotime("+1 months",strtotime($sY . $sM . "05"))) . "'
			and (memm01_no_pay= '$sMem' or memm01_no_pm = '$sMem')
		)as X
	";
	$calRun = $NewSql -> db_query($Sql) or die("SQL ERROR 4");
	$cal = $NewSql -> db_result($calRun);
//加扣項
/*
	$mywhere = " where 1 = 1 ";
	$mywhere .= LevelLimit($NewSql,"memm01_no");
	$Sql = "
		Select foundt02_no , foundt02_date, foundt02_content ,foundt02_total ,foundt02_remark
		From found_t02 ft02
		$mywhere
		and memm01_no = '$sMem'
		and foundt02_date like '" . $sY . $sM . "%'				
	";	
	$fRun = $NewSql -> db_query($Sql) or die("SQL ERROR 3");
	$fCount = $NewSql -> db_num_rows($fRun);
	
Select 
case
  when day('20150301') > 5 Then date_format('20150301','%Y%m')
  Else date_format(DATE_ADD('20150301', Interval -1 month),'%Y%m')
End 	
*/		
//撥款
	$Sql = "
		Select foundt03_no,foundt03_date,foundt03_total,foundt03_salary,foundt03_remark
		From found_t03
		where memm01_no = '$sMem'
		and foundt03_year = '$sY'
		and foundt03_month = '$sM'
	";
	$ft03Run = $NewSql -> db_query($Sql) or die("SQL ERROR 3");
	$ft03Count = $NewSql -> db_num_rows($ft03Run);
	$ft03 = $NewSql -> db_fetch_array($ft03Run);
	
	if($ft03["foundt03_no"]	!= ""){
		$editable = false;
	}
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
			modal: true
		});
	});	
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
			case 'Del':
				if($('.checkbox:checked').length <= 0){
					alert("請勾選要刪除的資料")
				}else{
					$('#KeyForm').attr('action','Code_Delete.php')
					$('#KeyForm').submit();					
				}
				break;
			case 'nextP':
				if(confirm("確定移到上個月嗎?")){
					$.post(
						'main4_NextNew.php',
						{DataKey:$("#DataKey").val(),T:'-1'},
						function(xml){
							window.location.reload();
						}
					);
				}
				break;
			case 'nextN':
				if(confirm("確定移到下個月嗎?")){
					$.post(
						'main4_NextNew.php',
						{DataKey:$("#DataKey").val(),T:'1'},
						function(xml){
							window.location.reload();
						}
					);
				}
				break;	
			case 'Delete':
				if(confirm("確定刪除此筆資料嗎?")){
					$.post(
						'main4_Delete.php',
						{DataKey:$("#DataKey").val()},
						function(xml){
							window.location.reload();
						}
					);
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
	function SetType(Key){
		$('#T').val(Key)		
	}
	function Search(){
		var sT = $("#sT").find("option:selected").val()
		var sY = $("#sY").find("option:selected").val()
		var sM = $("#sM").find("option:selected").val()
		var sMem = $("#sMem").find("option:selected").val()
		
		window.location = 'main4.php?sT=' + sT + '&sY=' + sY + '&sM=' + sM + '&sMem=' + sMem
	}
</script>
</head>
<body>
<div id="wrapper">

<div class="side-content">
	<ul class="breadcrumb">
    	<li><?php echo $GetTitle;?> > 成員薪資</li>
    	<li></li>        
    </ul>
	<div class="box">
        <div class="box-content">
            <div class="box-head">
            	<ul class="btn">
                    <?php if($sT == "M"){// & $ft03["foundt03_no"] == ""?>
                    <li><a title="+加/扣項" href="main4_fix.php?DataKey=<?php echo $sMem;?>&sY=<?php echo $sY;?>&sM=<?php echo $sM;?>" rel="shadowbox;width=600;height=500" ><input class="green" type="button" value="+加/扣項" ></a></li>
					<?php }?>
                	<li>
                    	<select id="sT" name="sT" onChange="SearchPage(this)">                        
                        	<option value="M"<?php echo ($sT == "M")?" selected":"";?>>個人入帳紀錄</option>
							<?php if($sendable){?><option value="T"<?php echo ($sT == "T")?" selected":"";?>>薪資總表</option><?php }?>                            
                        	<?php if($VendorPay){?><option value="F"<?php echo ($sT == "F")?" selected":"";?>>支付廠商記錄</option><?php }?>
                        	<?php if($sendable){?><option value="L"<?php echo ($sT == "L")?" selected":"";?>>主管加給</option><?php }?>                                     
                        </select>
                        
						<select id="sMem" name="sMem" onChange="Search()">
							<option value=""<?php echo ($sMem == "")?" selected":"";?>></option>                        
							<?php 
								while($mem = $NewSql -> db_fetch_array($memRun)){
									echo '<option value="' . $mem["memm01_no"] . '"';
									echo ($sMem == $mem["memm01_no"])?" selected":"";
									echo '>' . $mem["memm01_nick"] . '</option>';		
								}														
							?>
						</select>                        
                    </li>                                       
                </ul>
            </div>   
   			<?php if($sT == "M"){
				$MonthTotal = 0;	
			?>
			<form id="KeyForm" name="KeyForm" method="post">
            <input id="DataKey" name="DataKey" type="hidden">
            <input id="T" name="T" type="hidden">
        	<table class="table-bordered" id="MTable">
            	<tr>
                	<td colspan="11" align="left" >
                        <div style="margin-left:20px;">
                            <span><a class="prev" href="main4.php?<?php echo YearMonthPN('P',$sY,$sM);?>&sMem=<?php echo $sMem;?>"></a></span>                    
                            <select id="sY" name="sY" onChange="Search()">
                            <?php 
								for($i = date("Y") + 1;$i>=2014;$i--){
							?>
                                <option value="<?php echo $i;?>"<?php echo ($sY == $i)?" selected":"";?>><?php echo $i;?></option>
                            <?php }?>                           
                            </select>年                    
                            <select id="sM" name="sM" onChange="Search()">
                            <?php 
								if($sY == '2014'){ $starM = "9";}else { $starM = "1";}
								for($i = $starM;$i<=12;$i++){
							?>
                                <option value="<?php echo str_pad($i,2,'0',STR_PAD_LEFT);?>"<?php echo ($sM == str_pad($i,2,'0',STR_PAD_LEFT))?" selected":"";?>><?php echo str_pad($i,2,'0',STR_PAD_LEFT);?></option>
                            <?php }?>                           
                            </select>月    
                            <span class="next_box"><a class="next" href="main4.php?<?php echo YearMonthPN('N',$sY,$sM);?>&sMem=<?php echo $sMem;?>"></a></span>  
                        </div>                                                                           
                    </td>
                </tr>            
            	<tr class="title">
                	<?php if($editable){?><td class="center" width="30">刪除</td><?php }?>
					<!--
                    <?php if($editable & $prevData){?><td class="center" width="30">上個月</td><?php }?>
                    <?php if($editable){?><td class="center" width="30">下個月</td><?php }?>
					-->
                    <td class="center">日期</td>                    
                    <td class="center"></td>
                    <td class="left" style="min-width:500px;">摘要Memo</td>
                    <td class="center">金額</td>
                    <td class="center">稅後</td>
                    <td class="center">退稅</td>
                    <td class="center">來源/對象</td>
                    <td class="center">備註</td>
                </tr>               
                <?php 
				$i = 0;
				while($M = $NewSql -> db_fetch_array($MRun)){
					$i ++;
					$MonthTotal = $MonthTotal + $M["payt02_paytotalAfter"];
					$Monthbefore = $Monthbefore + $M["payt02_paytotal"];
				?>
            	<tr class="<?php echo ($i%2 == 0)?'odd':'';?>"> 
                    <?php if($editable){?>
                        <td class="center" width="30">
                        <?php if($M["payt02_paydate"] == "" & $M["payt02_type"] != "" ){?>                    
                            <div align="center" class="block red" style="width:30px;">
                                <a onClick="SetKey(<?php echo $M["payt02_no"];?>);EventClick('Delete')" >刪除</a>
                            </div>
                        <?php }?>                        
                        </td> 
                    <?php }?>
					<!--
                    <?php if($editable & $prevData){?>
                        <td class="center" width="30">
                        <?php if($M["payt02_paydate"] == "" & $M["payt02_type"] != ""  & $prevData){?>                    
                            <div align="center" class="block blue" style="width:30px;">
                                <a onClick="SetKey(<?php echo $M["payt02_no"];?>);EventClick('nextP')" ><</a>
                            </div>
                        <?php }?>                        
                        </td> 
                    <?php }?>                                   
                    <?php if($editable){?>
                        <td class="center" width="30">
                        <?php if($M["payt02_paydate"] == "" & $M["payt02_type"] != ""){?>
                            <div align="center" class="block blue" style="width:30px;">
                                <a onClick="SetKey(<?php echo $M["payt02_no"];?>);EventClick('nextN')" >></a>
                            </div>
                        <?php }?>                        
                        </td>
                    <?php }?>
					-->
                    <td class="center"><?php echo Dspdate($M["payt02_date"],"/");?></td>                    
                    <td class="center">
                    	<?php 
							if($M["payt02_paytotalAfter"] <= 0){
								echo '<span class="block pink whitetxt">支出</span>';
							}else{
								echo '<span class="block green whitetxt">收入</span>';
							}
						?>
                    </td>
                    <td class="left"><?php echo $M["payt02_memo"];?></td>
					<td class="right <?php if($M["payt02_paytotalAfter"] < 0){ echo 'redfont';}?>"><?php echo ($M["payt02_paytotal"] != 0)?MoneyFormat($M["payt02_paytotal"]):"";?></td>
                    <?php if($M["payt02_drawback"] == 'Y'){?>
                        <td class="right"></td>
                        <td class="right <?php if($M["payt02_paytotalAfter"] < 0){ echo 'redfont';}?>"><?php echo MoneyFormat($M["payt02_paytotalAfter"]);?></td>
                    <?php }else{?>
                        <td class="right <?php if($M["payt02_paytotalAfter"] < 0){ echo 'redfont';}?>"><?php echo MoneyFormat($M["payt02_paytotalAfter"]);?></td>
                        <td class="right"></td>
                    <?php }?>
                    <td class="center"><?php echo ($M["memm01_nick"] != ""?$M["memm01_nick"]:$M["comm01_nicknm"]);?></td>
                    <td class="center"><?php echo $M["payt02_remark"];?></td>     
                </tr>
                <?php }?>   
                <?php if ($cal != 0){
					$MonthTotal += $cal;
                ?>
                           
				<tr>
					<?php if($editable){?><td></td><?php }?>
					<!--
					<?php if($editable & $prevData){?><td></td><?php }?>
					<?php if($editable){?><td></td><?php }?>
					-->
					<td class="center"><?php echo date("Y/m/05",strtotime("+1 months",strtotime($sY . $sM . "05")));?></td> 
                    <td class="center">
                    	<?php 
							if($cal <= 0){
								echo '<span class="block pink whitetxt">支出</span>';
							}else{
								echo '<span class="block bigblue whitetxt">主管加給</span>';
							}
						?>
                    </td>
                    <td>主管訓練職務加給</td>
                    <td></td>
                    <td><?=MoneyFormat($cal);?></td>
					<td colspan="4"></td>
				</tr>
				<?php }?>
                <tr>
					<?php if($editable){?><td colspan="1"></td><?php }?>
					<!--
                    <?php if($editable & $prevData){?><td colspan="1"></td><?php }?>
					<?php if($editable){?><td></td><?php }?>
						-->
					<td colspan="2"></td>                    
                    <td class="right">總計</td>
                    <td class="right"><?php echo MoneyFormat($Monthbefore);?></td>
                    <td colspan="2" class="right <?php if($MonthTotal < 0){ echo 'redfont';}?>"><?php echo MoneyFormat($MonthTotal);?></td>
                	<td colspan="4"></td>                    
                </tr> 
                <?php if($ft03["foundt03_no"] != ""){?>
                <tr>
                	<!--<?php if($editable){?><td colspan="2" class="center" width="30"></td><?php }?>-->
                    <td><?php echo Dspdate($ft03["foundt03_date"],'-');?></td> 
                    <td></td>
                    <td class="left">薪資撥款</td>
                    <td></td>
                    <td class="right <?php if($ft03["foundt03_salary"] < 0){ echo 'redfont';}?>"><?php echo MoneyFormat($ft03["foundt03_salary"]);?></td>
                    <td colspan="2" class="right <?php if($ft03["foundt03_total"] - $ft03["foundt03_salary"] < 0){ echo 'redfont';}?>">餘 <?php echo MoneyFormat($ft03["foundt03_total"] - $ft03["foundt03_salary"]);?></td>
                	<td colspan="4"><?php echo $ft03["foundt03_remark"];?></td>                    
                </tr>                
                <?php }else if($MonthTotal > 0 and $sendable){?>
                <tr>
                	<td colspan="<?php echo ($editable)?"10":"9"?>"><a title="薪資撥款" href="main4_Clear.php?sMem=<?php echo $sMem;?>&sY=<?php echo $sY;?>&sM=<?php echo $sM;?>&T=<?php echo $MonthTotal;?>" rel="shadowbox;width=600;height=400" ><input type="button" value="撥款" ></li></a></td>                  
                </tr>
                <?php }?>
            </table>
			</form> 
            <?php }?>                     
        </div>
    </div>
</div>

</div>
</body>
</html>