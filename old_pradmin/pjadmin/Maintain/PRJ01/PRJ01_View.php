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
	$DataKey = xRequest("DataKey");
	$PrjIncomeTotal = 0;					//專案入帳額
	$PageCon = xRequest("PageCon");
	$payY = '0';
	$payN = '0';
	
//主 專案
	$Sql = " 
		Select pm1.* , cm1.comm01_nm, cm1.comm01_quid, cc02.C02_nm, ct02.codet02_nm
		, case when pm1.memm01_no = '" . $_SESSION["MemNO"] . "' Then '1' when memm01_uplevel = '" . $_SESSION["MemNO"] . "' Then '1' End 'RoleAuth'		
		From prj_m01 pm1
		Left join com_m01 cm1 on pm1.comm01_no = cm1.comm01_no
		Left join mem_m01 mm01 on pm1.memm01_no = mm01.memm01_no		
		Left join code_c02 cc02 on pm1.c02_no = cc02.c02_no		
		Left join code_t02 ct02 on pm1.t02_no = ct02.codet02_no		
		where prjm01_no = '$DataKey' 
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
	$init = $NewSql -> db_fetch_array($initRun);
	$initCount = $NewSql -> db_num_rows($initRun);

//明細 應收
	$Sql = "
		select I01.inm01_no, inm01_content, ADD_DATE, inm01_invoiceno, inm01_invoicedate, inm01_subtotal ,inm01_total ,inm01_Advance
		, inm01_incometotal , inm01_type, inm01_remark
		, I01.inm01_type, codet05_nm
		, incomeDate , int01_incometypeNM
		from in_m01	I01
		Left join code_t05 t05 on I01.inm01_type = t05.codet05_code	
		Left join (
			Select Max(int01_date) as incomeDate ,inm01_no
			, Case 
				when int01_incometype = '1' Then '匯款'
				when int01_incometype = '2' Then '現金'
				when int01_incometype = '3' Then '支票'
			End int01_incometypeNM
			From in_t01
			Group by inm01_no		
		)as De on I01.inm01_no = De.inm01_no			
		where prjm01_no = '$DataKey'
	";
	$inRun = $NewSql -> db_query($Sql) or die("SQL ERROR 2");
	$inCount = $NewSql -> db_num_rows($inRun);	
	
//明細 應付
	$Sql = "
		Select paym01_no, ADD_DATE, paym01_type1, memm01_nick, comm01_nicknm , paym01_prjcontent , paym01_total , paym01_paydate , paym01_haspay, paym01_invoiceno, paym01_invoicedate
		, paym01_paytotal ,paym01_remark
		From pay_m01 pm01
		Left join mem_m01  mm01 on pm01.memm01_no = mm01.memm01_no
		Left join com_m01 cmf01 on pm01.firmm01_no = cmf01.comm01_no 
		Where prjm01_no = '$DataKey'	
	";
	$payRun = $NewSql -> db_query($Sql) or die("SQL ERROR 3");
	$payCount = $NewSql -> db_num_rows($payRun);	
	
//專案明細
	$Sql = "
		select prjt02_no, memm01_nick, C01_nm
		from prj_t02 pt02
		left join mem_m01 m01 on pt02.memm01_no= m01.memm01_no
		left join code_c01 cc01 on pt02.C01_no = cc01.C01_no
		where prjm01_no= '$DataKey'	
	";
	$Pt02Run = $NewSql -> db_query($Sql) or die("SQL ERROR 3");
	$Pt02Count = $NewSql -> db_num_rows($Pt02Run);	
	
//明細
	$Sql = "
		Select codet02_no, codet02_nm
		From code_t02
		order by codet02_no
	";
	$t02Run = $NewSql -> db_query($Sql) or die("SQL ERROR 3");
	
//應收金額
	$Sql = "
		Select sum(inm01_subtotal) as subtotal ,sum(inm01_incometotal) as incomeHasTax,sum(inm01_incometotal - inm01_Advance) as income
		From in_m01
		where prjm01_no = '$DataKey'	
	";
	$incometotal = $NewSql -> db_fetch_array($NewSql -> db_query($Sql));
	$incomeHasTax = $incometotal["incomeHasTax"];
	$income = $incometotal["income"];
	$subtotal = $incometotal["subtotal"];
//應付 :已付 未付 $paytotal["paytotal"]$paytotal["advance"]
	$Sql = "
		Select sum(paym01_paytotal) as paytotal , sum(paym01_advance) as advance ,paym01_haspay 
		From pay_m01 
		where prjm01_no = '$DataKey' 
		Group by paym01_haspay
	";
	$paytotalRun = $NewSql -> db_query($Sql);
	
//權限
	$LevelMEM = LevelMEM($NewSql);
	switch ($LevelMEM){
		case '1':
		case '2':
			$ShowMoney = true;
			$AuthAlter = true;
			break;
		case '3':
			$ShowMoney = true;
			$AuthAlter = false;
			break;
		case '4':
			$ShowMoney = false;
			$AuthAlter = false;
			break;
	}
	
	while($paytotal = $NewSql -> db_fetch_array($paytotalRun)){
		switch($paytotal["paym01_haspay"]){
			case 'Y':
				$payY = $paytotal["paytotal"];
				break;
			case 'N':
				$payN = $paytotal["paytotal"];
				break;
		}	
	}
	
	if($incomeHasTax == ''){ $incomeHasTax = '0';}
	if($income == ''){ $income = '0';}	
	if($payY == ''){ $payY = '0';}
	if($payN == ''){ $payN = '0';}	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>

	$(function() {
		$(".datepicker").datepicker({
			dateFormat: 'yymmdd'
		});
		
		$(".chosen-select").chosen({
			no_results_text: "Oops, nothing found!",
			search_contains:true
		}); 	
		
		$("#memm01_no_chosen").hide()
		$("#firmm01_no_chosen").hide()		
	 });
	$(document).ready(function(){
		Shadowbox.init({
			overlayOpacity: 0.4,
			modal: true
		});
	});	
	function IncomeRemove(val){
		if(confirm("確定要刪嗎?")){
			$.post(
				'PRJ01_JoinDelete.php',
				{DataKey:val},
				function(xml){
					window.location.reload();
				}
			);
		}
	}
	function ChkForm(form){
	<?php if($AuthAlter || $init["RoleAuth"] == "1"){?>
		$("#SaveMsg").show().text('資料存儲中...')
		$.post(
			"PRJ01_ViewNew.php",
			$("#form1").serialize(),
			function(xml){
				if($('resu',xml).text() == '1'){
					$("#SaveMsg").show().text("資料已儲存")
					$("#SaveMsg").delay(3000).fadeOut()
				}else{
					$("#SaveMsg").show().text("資料失敗")
				}
			}
		);
	<?php }?>
	}		
</script>
<style>

.blockTop{
	width:100%;
}
.blockTop ul .left{
	left:0px;
	text-align:left;
}
.blockTop ul .left ul li span{
	font-weight:bold;
	padding-right:10px;
}
.blockTop ul .right{
	left:310px;
	text-align:left;
	width:300px;	
}

.blockmiddel{
	margin:5px;
}
.blockbottom{
	padding:5px;
	text-align:left;
}
.blockmiddel .title,
.blockbottom .title{
	text-align:left;
	height:30px;
	padding-top:10px;
	font-size:18px;
}
.blockbottom .discript{
	padding:5px;
}
.tablelist{
	margin-left:5px;
	border-color:#eee;
	border-collapse:separatel;
	border-left:0;
	border-spacing:2px;
	border-color:gray;
	border:1px solid #D3D3D3;
	font:16px/20px "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", "DejaVu Sans", Verdana, sans-serif;/*monaco,fantasy;*/
}


.discript span{
	margin-right:10px;
}

.minTab{
	padding:5px;
	border:1px dashed #A5A5A5;
}
.minTab td{
	text-align:center;
	border:1px dashed #A5A5A5;	
}
.minTab td{ width:80px;}
.minTab td.nm{ width:150px;}

.discript ul{
	width:900px;
}
.discript ul li{
	float:left;
	min-width:150px;
}
.discript ul li span{
	padding-left:5px;
	font-weight:bold;
}
.discript ul li.last{
	float:none;
}
</style>
</head>

<body>
<div id="wrapper">

    <div class="side-content">
        <ul class="breadcrumb">
            <li><?php echo $GetTitle . $GetFileType;?></li>
            <li></li>        
        </ul>
        
        <div class="box">
            <div class="box-content">
                <div class="box-head">
                    <ul class="btn">
                        <!--<li><input class="blue" type="button" value="Save" onClick="ChkForm(form1)"></li>-->
                        <li><input class="gray" type="button" value="Return" onClick="window.location='<?php echo $GetFileCode;?>.php?<?php echo $PageCon;?>'" ></li>
                        <li id="SaveMsg" style="color:red; font-size:12px;"></li>
                    </ul>
                </div>
                
                <div class="box-data">
                    	<div class="input-Group">
                        <form id="form1" name="form1" method="post" action="<?php echo $GetFileCode;?>_AddNew.php">
                        <input id="Code_Id" name="Code_Id" type="hidden" value="<?php echo $GetFileCode;?>">
                        <input id="prjm01_no" name="prjm01_no" type="hidden" value="<?php echo $DataKey;?>">
                        	<div class="blockTop">
                            	<ul>
                                    <li class="left" style="width:700px;">
                                        <ul>
                                            <li><span>客戶</span><?php echo $init["comm01_nm"];?> <?php if ($init["comm01_quid"] != "")echo " (統編:" . $init["comm01_quid"] . ")";?></li>
                                            <li><span>狀態</span>
                                            <select id="t02_no" name="t02_no" onChange="ChkForm(form1)">
                                            <?php while($t02 = $NewSql -> db_fetch_array($t02Run)){?>
                                            	<option value="<?php echo $t02["codet02_no"]?>"<?php if($init["t02_no"] == $t02["codet02_no"]){ echo ' Selected';}?>><?php echo $t02["codet02_nm"]?></option>
											<?php }?>
                                            </select>
                                            </li>
                                            <li><span>專案類型</span><?php echo $init["C02_nm"];?></li>
                                            <li><span>專案名稱</span><?php echo $init["prjm01_nm"];?></li>
                                            <li><span>執行日</span><input id="prjm01_DateSt" name="prjm01_DateSt" type="text" class="datepicker" size="10" value="<?php echo $init["prjm01_DateSt"];?>" onChange="ChkForm(form1)"> - <input id="prjm01_DateEd" name="prjm01_DateEd" type="text" class="datepicker" size="10" value="<?php echo $init["prjm01_DateEd"];?>" onChange="ChkForm(form1)"></li>
                                            <li><span>專案內容</span>
                                            </li>
                                            <li>
                                            	<textarea id="prjm01_script" name="prjm01_script" rows="10" cols="60" onChange="ChkForm(form1)"><?php echo $init["prjm01_script"];?></textarea>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="right" style=" width:400px;">
									<?php if($AuthAlter || $init["RoleAuth"] == "1"){?>
                                        <a title="新增成員" id="AddContact" href="PRJ01_Join.php?DataKey=<?php echo $DataKey;?>" rel="shadowbox;width=600;height=200" ><i class="add"></i></a>
									<?php }?>成員
                                        <table class="minTab">
                                        <?php while($Pt02 = $NewSql -> db_fetch_array($Pt02Run)){?>
                                            <tr>
                                                <td><?php echo $Pt02["memm01_nick"]?></td>
                                                <td class="nm"><?php echo $Pt02["C01_nm"]?></td>
                                                <?php if($AuthAlter || $init["RoleAuth"] == "1"){?><td><a class="remove" onClick="IncomeRemove('<?php echo $Pt02["prjt02_no"];?>')"></a></td><?php }?>                
                                            </tr>
										<?php }?>
                                        </table>
                                    </li>
                                </ul>
                            </div>
                            <?php if($ShowMoney || $init["RoleAuth"] == "1"){?>
                            <div class="blockmiddel">
                            	<div class="title">[ 應收-請款 Receviables ]
                                <?php if($AuthAlter || $init["RoleAuth"] == "1"){?>
                                <a class="add"  href="/pjadmin/Maintain/PRJ03/PRJ03_Add.php?&Source=PRJ01&comm01_no=<?php echo $init["comm01_no"];?>&prjm01_no=<?php echo $init["prjm01_no"];?>" rel="shadowbox;width=700;height=700" ></a>
                                <?php }?>
                                </div>
                                <div class="content">
                                    <table class="tablelist">
                                        <tr class="title">
                                            <td class="center" width="30">編輯</td>
                                            <td class="center">開立日</td>
                                            <td class="left">內容</td>
                                            <td class="center">發票號碼</td>
                                            <td class="center">未稅</td>
                                            <td class="center">應收金額</td>
                                            <td class="center">入帳日</td>
                                            <td class="center">實收</td>
                                            <td class="center">扣繳</td>
                                            <td class="center">合計</td>
                                            <td class="center">狀態</td>                                            
                                            <td class="center">備註</td>                                        
                                        </tr>
                                        <?php 
										$i = 0;
										while($in = $NewSql -> db_fetch_array($inRun)){
											$i++;
											$PrjIncomeTotal = $PrjIncomeTotal + $in["inm01_incometotal"];
										?>
                                        <tr class="<?php echo ($i%2 == 0)?'odd':'';?>">
                                            <td class="center" width="30">
                                            <?php if($AuthAlter || $init["RoleAuth"] == "1"){?>
                                                <div align="center" class="block blue">
                                                    <a class="edit"  href="/pjadmin/Maintain/PRJ03/PRJ03_Alter.php?DataKey=<?php echo $in["inm01_no"];?>&Source=PRJ01" rel="shadowbox;width=700;height=700" ></a>
                                                </div>
											<?php }?>
                                            </td>                                      
                                            <td class="center"><?php echo DspDate($in["inm01_invoicedate"],"/");?></td>
                                            <td class="left"><?php echo $in["inm01_content"];?></td>
                                            <td class="center"><?php echo $in["inm01_invoiceno"];?></td>
                                            <td class="right"><?php echo MoneyFormat($in["inm01_subtotal"]);?></td>
                                            <td class="right"><?php echo MoneyFormat($in["inm01_total"]);?></td>
                                            <td class="center"><?php echo DspDate($in["incomeDate"],"/");?></td>
                                            <td class="right"><?php echo MoneyFormat($in["inm01_incometotal"]);?></td>
                                            <td class="right"><?php echo MoneyFormat($in["inm01_Advance"]);?></td>
                                            <td class="right"><?php echo MoneyFormat($in["inm01_incometotal"] - $in["inm01_Advance"]);?></td>
                                            <td class="center">
											<?php
                                                switch ($in["inm01_type"]){
                                                    case '1':
                                                        echo '<span class="block red whitetxt">' . $in["codet05_nm"] . '</span>';
                                                        break;
                                                    case '2':
                                                        echo '<span class="block green whitetxt">' . $in["codet05_nm"] . '</span>';								
                                                        break;								
                                                    case '3':
                                                        echo '<span class="block gary whitetxt">' . $in["codet05_nm"] . '</span>';								
                                                        break;	
                                                    case '5':
                                                        echo '<span class="block blue whitetxt">' . $in["codet05_nm"] . '</span>';								
                                                        break;	
                                                    case '6':
                                                        echo '<span class="block gary whitetxt">' . $in["codet05_nm"] . '</span>';								
                                                        break;																																				
                                                }
                                            ?>                                            
                                            </td>                                             
                                            <td class="center"><?php echo $in["inm01_remark"];?></td>                                    
                                        </tr>                                        
                                        <?php }?>
                                    </table>
                                </div>                                                               
                            </div>
                            <?php }?>
                            <?php if($ShowMoney || $init["RoleAuth"] == "1"){?>
                            <div class="blockbottom">
                            	<div class="title">[ 應付-專案支出 Payments ]
                                <?php if($AuthAlter || $init["RoleAuth"] == "1"){?>
                                <a class="add"  href="/pjadmin/Maintain/PRJ02/PRJ02_Add.php?&Source=PRJ01&comm01_no=<?php echo $init["comm01_no"];?>&prjm01_no=<?php echo $init["prjm01_no"];?>" rel="shadowbox;width=700;height=700" ></a>
                                <?php }?>
                                </div>
                                <div class="discript">
									<ul>
                                    	<li>入帳 <span><?php echo MoneyFormat($incomeHasTax);?></span></li>
										<li>可支付 <span><?php echo MoneyFormat($income);?></span></li>
										<li>已付 <span><?php echo MoneyFormat($payY);?></span></li>
										<li>待付 <span><?php echo MoneyFormat($payN);?></span></li>
										<li class="last">餘額 <span><?php echo MoneyFormat(($income - $payY - $payN));?></span></li>                                        
                                    </ul>
                                </div>
                                <div class="content">
                                    <table class="tablelist">
                                        <tr class="title">
                                            <td class="center" width="30">編輯</td>
                                            <td class="center">日期</td>
                                            <td class="center">類型</td>
                                            <td class="center">對象</td>
                                            <td class="center">內容</td>
                                            <td class="center">預算</td>
                                            <td class="center">比例</td>
                                            <td class="center">付款日</td>
                                            <td class="center">發票日</td>
                                            <td class="center">發票號碼</td> 
                                            <td class="center">扣抵</td>
                                            <td class="center">實付</td>
                                            <td class="center">狀態</td>
                                            <td class="center">備註</td>
                                        </tr>
                                        <?php while($pay = $NewSql -> db_fetch_array($payRun)){?>
                                        <tr>
                                            <td class="center" width="30">
                                            <?php if($AuthAlter || $init["RoleAuth"] == "1"){?>
                                                <div align="center" class="block blue">
                                                    <a class="edit"  href="/pjadmin/Maintain/PRJ02/PRJ02_Alter.php?DataKey=<?php echo $pay["paym01_no"];?>&Source=PRJ01" rel="shadowbox;width=700;height=700" ></a>
                                                </div>
											<?php }?>
                                            </td>                                                                              
                                            <td class="center"><?php echo Dspdate($pay["ADD_DATE"],"/");?></td>
                                            <td class="center">
												<?php //echo ($pay["paym01_type1"] == "M"?"成員":"外製");
													switch ($pay["paym01_type1"]){
														case 'M':
															echo "成員";
															break;
														case 'F':
															echo "外製";
															break;
														case 'P':
															echo "已支出";
															break;
													}
												?>
                                            </td>
                                            <td class="center"><?php echo ($pay["paym01_type1"] == "M"?$pay["memm01_nick"]:$pay["comm01_nicknm"]);?></td>
                                            <td class="center"><?php echo $pay["paym01_prjcontent"];?></td>
                                            <td class="right"><?php echo MoneyFormat($pay["paym01_total"]);?></td>
                                            <td class="center"><?php echo ($subtotal > 0)?round($pay["paym01_total"]/$subtotal,2)*100 . "%":"";?></td>
                                            <td class="center"><?php echo Dspdate($pay["paym01_paydate"],"/");?></td>
                                            <td class="center"><?php echo Dspdate($pay["paym01_invoicedate"],"/");?></td>
                                            <td class="center"><?php echo $pay["paym01_invoiceno"];?></td>
                                            <td class="right"><?php echo MoneyFormat($pay["paym01_total"] - $pay["paym01_paytotal"]);?></td>
                                            <td class="right"><?php echo MoneyFormat($pay["paym01_paytotal"]);?></td>                                            
                                            <td class="center">
											<?php 
												if($pay["paym01_haspay"] == "Y"){
													echo '<div align="center" class="block blue" style="width:30px;"><a>已付</a></div>';
												}else{		
													echo '<div align="center" class="block red" style="width:30px;"><a>未付</a></div>';	
												}											
                                            ?>                                            
                                            </td>
                                            <td class="center"><?php echo $pay["paym01_remark"];?></td>                                                                                        
										</tr>                                         
                                        <?php }?>
                                    </table>
                                </div>
                            </div> 
                            <?php }?>                           
						</form>
                        </div>
                </div>
			</div>
		</div>
    </div>    
</div>
</body>
</html>