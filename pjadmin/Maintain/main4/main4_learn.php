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
	
	foundt03($NewSql,$sY,$sM);	//加入固定
	
	switch ($LevelMem){
		case '1':
		case '2':
			$sendable = true;
			break;
		default:
			$sendable = false;
			break;
	}
	$sY = xRequest("sY");
	$sM = xRequest("sM");
	$sT = xRequest("sT");
	$sMem = xRequest("sMem");
	
	if ($sMem == "" & $sT == "M"){
		$sMem = $_SESSION["MemNO"];
	}
	if ($sY == ""){
		$sY = date("Y");
	}
	if ($sM == ""){
		$sM = date("m");
	}
	//限制最初為 2014.9
	if($sY < 2017 || ($sY == 2017 and $sM < 4)){
		$sY = 2017;
		$sM = 4;
	}
	$myWhere = " where 1 = 1 ";
	$myWhere .= LevelLimit($NewSql,"m01.memm01_no");
	
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
		order by m01.MUG01_NO
	";
	$memRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
	$memCount = $NewSql -> db_num_rows($memRun);
	
	$Data_Ary = Array();
//收入
	$Sql = "
		SELECT payt03_paydate,payt03_no,payt03_memo, payt03_paytotal * 1 as payt03_paytotal, payt03_paytotalAfter * 1 as payt03_paytotalAfter, payt03_remarker, 
		mempay.memm01_nick as pay_nick,
		mempm.memm01_nick as pm_nick,
		memfrom.memm01_nick as from_nick,
		'I' as type
		FROM pay_t03 payt03
		Left join mem_m01 mempay on payt03.`memm01_no_pay` = mempay.memm01_no
		Left join mem_m01 mempm on payt03.`memm01_no_pm` = mempm.memm01_no
		Left join mem_m01 memfrom on payt03.`memm01_no_from` = memfrom.memm01_no
		where `payt03_paydate` >= '" . $sY . $sM . "06'
		and `payt03_paydate` <= '" . date("Ym05",strtotime("+1 months",strtotime($sY . $sM . "05"))) . "'	
		and `memm01_no_pay`= '" . $sMem . "'	
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR 2");
	$initCount = $NewSql -> db_num_rows($initRun);
	while ($Data = $NewSql -> db_fetch_array($initRun)){
		array_push($Data_Ary,$Data);
	}
	
//支出(PM)
	$Sql = "
		SELECT payt03_paydate, payt03_no,payt03_memo, payt03_paytotal * -1 as payt03_paytotal, payt03_paytotalAfter * - 1 as payt03_paytotalAfter,payt03_remarker,
		mempay.memm01_nick as pay_nick,
		mempm.memm01_nick as pm_nick,
		memfrom.memm01_nick as from_nick,
		'P' as type
		FROM pay_t03 payt03
		Left join mem_m01 mempay on payt03.`memm01_no_pay` = mempay.memm01_no
		Left join mem_m01 mempm on payt03.`memm01_no_pm` = mempm.memm01_no
		Left join mem_m01 memfrom on payt03.`memm01_no_from` = memfrom.memm01_no
		where `payt03_paydate` >= '" . $sY . $sM . "06'
		and `payt03_paydate` <= '" . date("Ym05",strtotime("+1 months",strtotime($sY . $sM . "05"))) . "'
		and memm01_no_pm = '" . $sMem . "'
	";
	$payRun = $NewSql -> db_query($Sql) or die("SQL ERROR 3");

	while ($Data = $NewSql -> db_fetch_array($payRun)){
		array_push($Data_Ary,$Data);
	}
	sort($Data_Ary);
	
//總計算
	$Sql = "
		SELECT SUM(payt03_paytotalAfter) as after
		From (
			SELECT 
			case when memm01_no_pay = '" . $sMem . "' Then (payt03_paytotal * -1) else (payt03_paytotal * 1) end payt03_paytotal
			, case when memm01_no_pm = '" . $sMem . "' Then payt03_paytotalAfter * -1 else payt03_paytotalAfter * 1 end payt03_paytotalAfter
			FROM pay_t03 payt03
			Left join mem_m01 mempay on payt03.`memm01_no_pay` = mempay.memm01_no
			Left join mem_m01 mempm on payt03.`memm01_no_pm` = mempm.memm01_no
			Left join mem_m01 memfrom on payt03.`memm01_no_from` = memfrom.memm01_no
			where `payt03_paydate` >= '" . $sY . $sM . "06'
			and `payt03_paydate` <= '" . date("Ym05",strtotime("+1 months",strtotime($sY . $sM . "05"))) . "'
			and (memm01_no_pay= '" . $sMem . "' or memm01_no_pm = '" . $sMem . "')
			and memm01_no_pay != memm01_no_pm
		)as X
	";
	$calRun = $NewSql -> db_query($Sql) or die("SQL ERROR 4");
	$cal = $NewSql -> db_result($calRun);
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
	function SetKey(Key){
		$('#DataKey').val(Key)		
	}
	function Search(sY,sM,sMem,sType){
		switch (sType){
			case 1:
				sY = $(sY).val();
				break;
			case 2:
				sM = $(sM).val();
				break;
			case 3:
				sMem = $(sMem).val();
				break;
		}
		window.location = 'main4_learn.php?sY=' + sY + '&sM=' + sM + '&sMem=' + sMem
	}
</script>
</head>
<body>
<div id="wrapper">

<div class="side-content">
	<ul class="breadcrumb">
    	<li><?php echo $GetTitle;?> > 主管加給</li>
    	<li></li>        
    </ul>
	<div class="box">
        <div class="box-content">
            <div class="box-head">
            	<ul class="btn">
                	<li>
                    	<select id="sT" name="sT" onChange="SearchPage(this)">
                        	<option value="M" >個人入帳紀錄</option>                        
                        	<?php if($sendable){?><option value="T" selected>薪資總表</option><?php }?> 
                        	<?php if($sendable){?><option value="F" >支付廠商記錄</option><?php }?>     
                        	<?php if($sendable){?><option value="L" selected>主管加給</option><?php }?>                      
                        </select>
						<select id="sMem" name="sMem" onChange="Search('<?=$sY;?>','<?=$sM;?>',this,3)">
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
            <?php if($sendable){?>   
			<form id="KeyForm" name="KeyForm" method="post">
            <input id="DataKey" name="DataKey" type="hidden">
        	<table class="table-bordered" id="FTable">
            	<tr>              
                	<td colspan="11" align="left" >
                        <div style="margin-left:20px;">                    
                            <span><a class="prev" href="main4_learn.php?<?php echo YearMonthPN('P',$sY,$sM);?>&sMem=<?php echo $sMem;?>"></a></span>
                            <select id="sY" name="sY" onChange="Search(this,'<?=$sM;?>',<?=$sMem;?>,1)">
                            <?php for($i = date("Y") + 1;$i>=2017;$i--){?>
                                <option value="<?php echo $i;?>"<?php echo ($sY == $i)?" selected":"";?>><?php echo $i;?></option>
                            <?php }?>                           
                            </select>
                            年                    
                            <select id="sM" name="sM" onChange="Search('<?=$sY;?>',this,<?=$sMem;?>,2)">
                            <?php 
								if($sY == '2017'){ $starM = "4";}else { $starM = "1";}
								for($i = $starM;$i<=12;$i++){
							?>
                                <option value="<?php echo str_pad($i,2,'0',STR_PAD_LEFT);?>"<?php echo ($sM == str_pad($i,2,'0',STR_PAD_LEFT))?" selected":"";?>><?php echo str_pad($i,2,'0',STR_PAD_LEFT);?></option>
                            <?php }?>                           
                            </select>
                            月 
                            <span class="next_box"><a class="next" href="main4_learn.php?<?php echo YearMonthPN('N',$sY,$sM);?>&sMem=<?php echo $sMem;?>"></a></span>  
                        </div>                                                
                    </td>
                </tr>
            	<tr class="title">
                    <td class="center" width="30">日期</td>
                    <td class="center">收入/支出</td>
                    <td class="left" style="min-width:500px;">摘要</td>
                    <td class="center">金額</td>
                    <td class="center">稅後</td>
                    <td class="center">來源/對象</td>
                    <td class="center">備註</td>
                </tr>
                <?php 
				foreach ($Data_Ary as $i => $Data){
				?>
            	<tr class="<?php echo ($i%2 == 0)?'odd':'';?>">                                                  
                    <td class="center"><?php echo $Data["payt03_paydate"];?></td>
                    <td class="center">
						<?php echo ($Data["payt03_paytotalAfter"] > 0) ? '<span class="block green whitetxt">收入</span>' : '<span class="block pink whitetxt">支出</span>';?>
                    </td>
                    <td class="left"><?php echo $Data["payt03_memo"];?></td>
                    <td class="center"><?php echo MoneyFormat($Data["payt03_paytotal"]);?></td>
                    <td class="center"><?php echo MoneyFormat($Data["payt03_paytotalAfter"]);?></td>
                    <td class="center"><?php echo ($Data["type"] == 'P' ? $Data["pay_nick"] : $Data["pm_nick"]) . ' / ' . $Data["from_nick"];?></td>
                    <td class="center"><?php echo $Data["payt03_remarker"];?></td>
                </tr>
                <?php }?> 
                <tr>
                	<td colspan="3"></td>
                    <td>總計</td>
                    <td><?=MoneyFormat($cal);?></td>
                    <td colspan="2"></td>
                </tr>
                                              
            </table>
			</form> 
            <?php }?>                   
        </div>
    </div>
</div>

</div>
</body>
</html>