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
	
	if($sT == 'F'){
		header('location:main4_fire.php');
		exit();
	}	
	if($sT == 'M'){
		header('location:main4.php');
		exit();
	}
	
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
	if($sY < 2014 || ($sY == 2014 and $sM < 9)){
		$sY = 2014;
		$sM = 9;
	}
	$myWhere = " where 1 = 1 ";
	$myWhere .= LevelLimit($NewSql,"m01.memm01_no");

	$Sql = "
		Select sum(payt02_paytotalAfter) + ifnull(payt03_paytotalAfter_x,0) + ifnull(payt03_paytotalAfter_y,0) as totalpay,pt01.memm01_no,ft03.foundt03_no
		,m01.memm01_nick ,m01.memm01_bank ,m01.memm01_bankacc ,m01.memm01_bankbrand, m01.memm01_id
		,ft03.foundt03_salary,ft03.foundt03_remark
		From pay_t02 pt01
		Left join mem_m01 m01 on pt01.memm01_no = m01.memm01_no
		Left join found_t03 ft03 on pt01.memm01_no = ft03.memm01_no and foundt03_year = '$sY' and foundt03_month = '$sM'
		Left join (
			SELECT 
            	sum(payt03_paytotalAfter) as payt03_paytotalAfter_x, memm01_no_pay as memm01_no_x
			FROM pay_t03 payt03
            where 1 = 1 
            and `payt03_paydate` >= '" . $sY . $sM . "06' and `payt03_paydate` <= '" . date("Ym05",strtotime("+1 months",strtotime($sY . $sM . "05"))) . "'
            Group by memm01_no_x
		) as x on x.memm01_no_x = m01.memm01_no
		Left join (
			SELECT 
            	sum(payt03_paytotalAfter * -1) as payt03_paytotalAfter_y, memm01_no_pm as memm01_no_y
			FROM pay_t03 payt03
            where 1 = 1 
            and `payt03_paydate` >= '" . $sY . $sM . "06' and `payt03_paydate` <= '" . date("Ym05",strtotime("+1 months",strtotime($sY . $sM . "05"))) . "'
            Group by memm01_no_y
		) as y on y.memm01_no_y = m01.memm01_no
		where 1 = 1 
		and payt02_date >= '" . $sY . $sM . "06'
		and payt02_date <= '" . date("Ym05",strtotime("+1 months",strtotime($sY . $sM . "05"))) . "'		
		and pt01.memm01_no != ''
		Group by memm01_no
		order by m01.MUG01_NO		
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$initCount = $NewSql -> db_num_rows($initRun);

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
			case 'Setting':
				$('#KeyForm').attr('action','mug04.php')
				$('#KeyForm').submit();		
				break;
			default:
				break;
		}
	}
	function SetKey(Key){
		$('#DataKey').val(Key)		
	}
	function Sreach(){
		var sT = $("#sT").find("option:selected").val()
		var sY = $("#sY").find("option:selected").val()
		var sM = $("#sM").find("option:selected").val()
		var sMem = $("#sMem").find("option:selected").val()
		
		window.location = 'main4_summary.php?sT=' + sT + '&sY=' + sY + '&sM=' + sM + '&sMem=' + sMem
	}
</script>
</head>
<body>
<div id="wrapper">

<div class="side-content">
	<ul class="breadcrumb">
    	<li><?php echo $GetTitle;?> > 薪資總表</li>
    	<li></li>        
    </ul>
	<div class="box">
        <div class="box-content">
            <div class="box-head">
            	<ul class="btn">
                    <?php if($sT == "M"){?><li><a title="+加/扣項" href="main4_fix.php?DataKey=<?php echo $sMem;?>" rel="shadowbox;width=600;height=400" ><input class="green" type="button" value="+加/扣項" ></a></li><?php }?>
                	<li>
                    	<select id="sT" name="sT" onChange="SearchPage(this)">
                        	<option value="M" >個人入帳紀錄</option>                        
                        	<?php if($sendable){?><option value="T" selected>薪資總表</option><?php }?> 
                        	<?php if($sendable){?><option value="F" >支付廠商記錄</option><?php }?>     
                        	<?php if($sendable){?><option value="L" >主管加給</option><?php }?>                           
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
                            <span><a class="prev" href="main4_summary.php?<?php echo YearMonthPN('P',$sY,$sM);?>&sMem=<?php echo $sMem;?>"></a></span>
                            <select id="sY" name="sY" onChange="Sreach()">
                            <?php for($i = date("Y") + 1;$i>=2014;$i--){?>
                                <option value="<?php echo $i;?>"<?php echo ($sY == $i)?" selected":"";?>><?php echo $i;?></option>
                            <?php }?>                           
                            </select>
                            年                    
                            <select id="sM" name="sM" onChange="Sreach()">
                            <?php 
								if($sY == '2014'){ $starM = "9";}else { $starM = "1";}
								for($i = $starM;$i<=12;$i++){
							?>
                                <option value="<?php echo str_pad($i,2,'0',STR_PAD_LEFT);?>"<?php echo ($sM == str_pad($i,2,'0',STR_PAD_LEFT))?" selected":"";?>><?php echo str_pad($i,2,'0',STR_PAD_LEFT);?></option>
                            <?php }?>                           
                            </select>
                            月 
                            <span class="next_box"><a class="next" href="main4_summary.php?<?php echo YearMonthPN('N',$sY,$sM);?>"></a></span>  
                        </div>                                                
                    </td>
                </tr>
            	<tr class="title">
                    <td class="center" width="30">給付</td>
                    <td class="center">姓名</td>
                    <td class="center">銀行</td>
                    <td class="center">分行</td>
                    <td class="center">帳號</td>
                    <td class="center">統編/身分字號</td>
                    <td class="center">金額</td>
                    <td class="center">撥款金額</td>
                    <td class="center">發放日</td>
                    <td class="center">備註</td>
                </tr>
                <?php 
				$i = 0;
				while($Data = $NewSql -> db_fetch_array($initRun)){
					$i ++;
				?>
            	<tr class="<?php echo ($i%2 == 0)?'odd':'';?>"> 
                    <td class="center" width="30">
                    <?php if($Data["foundt03_no"] == ""){?>
                    	<div align="center" class="block blue">
                        	<a href="main4_Clear.php?sMem=<?php echo $Data["memm01_no"];?>&sY=<?php echo $sY;?>&sM=<?php echo $sM;?>&T=<?php echo $Data["totalpay"];?>" rel="shadowbox;width=600;height=300" class="edit" title="撥款" ></a>
                        </div>
					<?php }else { echo '已付';}?>                        
                    </td>                                                  
                    <td class="center"><?php echo $Data["memm01_nick"];?></td>
                    <td class="center"><?php echo $Data["memm01_bank"];?></td>
                    <td class="center"><?php echo $Data["memm01_bankbrand"];?></td>
                    <td class="center"><?php echo $Data["memm01_bankacc"];?></td>
                    <td class="center"><?php echo $Data["memm01_id"];?></td>
                    <td class="right"><?php echo MoneyFormat($Data["totalpay"]);?></td>
                    <td class="right"><?php echo MoneyFormat($Data["foundt03_salary"]);?></td>
                    <td class="center"><?php echo ($Data["payt02_paydate"] == "")?Dspdate(date('Ym05',strtotime(date($sY . $sM . '05') . " +1 month")),"/"):Dspdate($Data["payt02_paydate"],"/");?></td>
                    <td class="center"><?php echo $Data["foundt03_remark"];?></td>
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