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

	$sY = xRequest("sY");
	$sM = xRequest("sM");
	$sT = xRequest("sT");
	$sMem = xRequest("sMem");
	if($sT == 'M'){
		header('location:main4.php');
		exit();
	}
	if($sT == 'T'){
		header('location:main4_summary.php');
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
//	$myWhere .= LevelLimit($NewSql,"m01.memm01_no");
	
//廠商	
/*
	$Sql = "
		Select pt02.* , mm01.memm01_nick
		From pay_t02 pt02
		Left join mem_m01 mm01 on pt02.payt02_from = mm01.memm01_no
		$mywhere
		and pt02.memm01_no = '$sMem'
		and payt02_date like '" . $sY . $sM . "%'
		order by payt02_Sort 	
	";
*/
	$Sql = "
		SELECT pt02.* 
		, cm01.comm01_nicknm , cm01.comm01_nm, cm01.comm01_quid
		, ct02.comt02_branch,ct02.comt02_bank ,ct02.comt02_acc
		, ct02.comt02_bankId
		FROM pay_t02 pt02
		LEFT JOIN com_m01 cm01 ON pt02.firmm01_no = cm01.comm01_no
		LEFT JOIN (
			SELECT * 
			FROM com_t02
			Group by comm01_no
		) AS ct02 ON cm01.comm01_no = ct02.comm01_no
		WHERE payt02_type = 'P'
		and firmm01_no !=  ''
		and payt02_date >= '" . $sY . $sM . "06'
		and payt02_date <= '" . date("Ym05",strtotime("+1 months",strtotime($sY . $sM . "05"))) . "'	
		order by (case when length(left(comm01_nicknm,1)) != character_length(left(comm01_nicknm,1)) Then 0 Else 1 End)
		, CAST(CONVERT(left(comm01_nicknm,1) using big5) AS BINARY)
		,(case when payt02_date != '' Then payt02_date Else payt02_paydate End)
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$initCount = $NewSql -> db_num_rows($initRun);

//員工
	$mywhere = " where 1 = 1 ";
	//$mywhere .= LevelLimit($NewSql,"memm01_no");
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
	
	/*
	$aPageCount = "20";
	Page($NewSql,$initCount,$Sql,$aPageCount);
	*/
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
					/*
					$('#KeyForm').attr('action','Code_Delete.php')
					$('#KeyForm').submit();	
					*/				
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
		
		window.location = 'main4_fire.php?sT=' + sT + '&sY=' + sY + '&sM=' + sM + '&sMem=' + sMem
	}
</script>
</head>
<body>
<div id="wrapper">

<div class="side-content">
	<ul class="breadcrumb">
    	<li><?php echo $GetTitle;?> > 支付廠商記錄</li>
    	<li></li>        
    </ul>
	<div class="box">
        <div class="box-content">
            <div class="box-head">
            	<ul class="btn">
                    <?php if($sT == "M"){?><li><a title="+加/扣項" href="main4_fix.php?DataKey=<?php echo $sMem;?>" rel="shadowbox;width=600;height=400" ><input class="green" type="button" value="+加/扣項" ></a></li><?php }?>
                	<li>
                    	<select id="sT" name="sT" onChange="SearchPage(this)">
                        	<option value="M">個人入帳紀錄</option>
                        	<?php if($sendable){?><option value="T" >薪資總表</option><?php }?> 
                        	<?php if($VendorPay){?><option value="F" selected>支付廠商記錄</option><?php }?>     
                        	<?php if($sendable){?><option value="L" >主管加給</option><?php }?>                           
                        </select>
                    </li>                                       
                </ul>
            </div>
            <?php if($LevelMem == "1" || $LevelMem == "2" || $LevelMem == "3"){?>   
			<form id="KeyForm" name="KeyForm" method="post">
            <input id="DataKey" name="DataKey" type="hidden">
        	<table class="table-bordered" id="FTable">
            	<tr>              
                	<td colspan="11" align="left" >
                        <div style="margin-left:20px;">                    
                            <span><a class="prev" href="main4_fire.php?<?php echo YearMonthPN('P',$sY,$sM);?>&sMem=<?php echo $sMem;?>"></a></span>
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
                            <span class="next_box"><a class="next" href="main4_fire.php?<?php echo YearMonthPN('N',$sY,$sM);?>"></a></span>  
                        </div>                                                
                    </td>
                </tr>
            	<tr class="title">
                    <td class="center" width="30">給付</td>
					<!--
                    <td class="center" width="30">上個月</td>
                    <td class="center" width="30">下個月</td>
					-->
                    <td class="left" style="min-width:500px;">摘要Memo</td>
                    <td class="center">姓名</td>
                    <td class="center">銀行</td>
                    <td class="center">分行</td>
                    <td class="center">帳號</td>
                    <td class="center">統編/身分字號</td>
                    <td class="center">金額</td>
                    <td class="center">發放日</td>
                </tr>
                <?php 
				$i = 0;
				while($Data = $NewSql -> db_fetch_array($initRun)){
					$i ++;
				?>
            	<tr class="<?php echo ($i%2 == 0)?'odd':'';?>"> 
                    <td class="center" width="30">
					<?php 
					if($Data["payt02_paydate"] == ""){
						if($LevelMem == "1" || $LevelMem == "2" ){
					?>
                    	<div align="center" class="block blue">
                        	<a href="main4_Alter.php?DataKey=<?php echo $Data["payt02_no"];?>" rel="shadowbox;width=600;height=300" class="edit" title="撥款" ></a>
                        </div>
					<?php 
						}
					}else { 
						echo '已付';
					}
					?>                        
                    </td> 
					<!--
                    <td class="center" width="30">
                    <?php if($Data["payt02_paydate"] == ""){?>                    
                    	<div align="center" class="block blue" style="width:14px; height:14px;">
                        	<a onClick="SetKey(<?php echo $Data["payt02_no"];?>);EventClick('nextP')" ><</a>
                        </div>
					<?php }?>                        
                    </td> 
                    <td class="center" width="30">
                    <?php if($Data["payt02_paydate"] == ""){?>                    
                    	<div align="center" class="block blue" style="width:14px; height:14px;">
                        	<a onClick="SetKey(<?php echo $Data["payt02_no"];?>);EventClick('nextN')" >></a>
                        </div>
					<?php }?>                        
                    </td> 
					--> 
                    <td class="left"><?php echo $Data["payt02_memo"];?></td>                                                    
                    <td class="left"><?php echo $Data["comm01_nm"];?></td>
                    <td class="left"><?php echo '('.$Data["comt02_bankId"] . ')' . $Data["comt02_bank"];?></td>
                    <td class="center"><?php echo $Data["comt02_branch"];?></td>
                    <td class="left">
	                    <?php 
		                    preg_match_all('/\d+/',$Data["comt02_acc"],$acc);
		                    $acc = join('',$acc[0]);
		                    echo $acc;
	                    ?>
                    </td>
                    <td class="center"><?php echo $Data["comm01_quid"];?></td>
                    <td class="right"><?php echo MoneyFormat($Data["payt02_paytotal"]);?></td>
                    <td class="center"><?php echo ($Data["payt02_paydate"] == "")?Dspdate($Data["payt02_date"],"/"):Dspdate($Data["payt02_paydate"],"/");?></td>
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