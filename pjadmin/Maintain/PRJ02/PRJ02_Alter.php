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
	LoginChk(GetFileCode(__FILE__),"4");
//資料庫連線
	$NewSql = new mysql();	
		
//參數
	$GetFileCode = GetFileCode(__FILE__);
	$GetFileType = GetFileType(__FILE__);	
	$GetTitle = GetTitle($NewSql,$GetFileCode);
	$ChangeData = true;							//修改資料權限
	$DataKey = xRequest("DataKey");
	$Source = xRequest("Source");				//網頁來源	
	$PageCon = xRequest("PageCon");	
	$Sql = "
		Select *
		From pay_m01
		where paym01_no = '$DataKey'
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$init = $NewSql -> db_fetch_array($initRun);
	$initCount = $NewSql -> db_num_rows($initRun);
	
	if($initCount != 1){
		header('location:' . $GetFileCode . '.php');
	}

//	已支出 :未付款可刪除
	$Sql = "
		Select *
		From pay_t02 t02
		Left join pay_m01 m01 on t02.paym01_no = m01.paym01_no
		where t02.paym01_no = '$DataKey'
		and ifnull(t02.payt02_paydate,'') = ''
		and m01.paym01_type1 = 'P'
		/*and m01.paym01_haspay != 'Y'*/
	";
	$PRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$PCount = $NewSql -> db_num_rows($PRun);
//檢查修改權限
	switch (true){
		case $_SESSION["MemNO"] == $init["ADD_ID"]:
			$ChangeData = true;
			break;
		case LevelMEM($NewSql) == '1':
		case LevelMEM($NewSql) == '2':
			$ChangeData = true;
			break;
		default:
			$ChangeData = false;
			break;
	}
//	$ChangeData
//主檔 專案
	$Sql = "
		Select prjm01_no, prjm01_nm, m.c02_no, ct02.codet02_nm, m.memm01_no
		From prj_m01 m
		Left join code_t02 ct02 on m.t02_no  = ct02.codet02_no
		Where comm01_no = '" . $init["comm01_no"] . "'
		/*and m.t02_no in ('1','2','3','5')		*/
	";
	$prjRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$prjCount = $NewSql -> db_num_rows($prjRun);
	
//代碼	專案類型
	$Sql = " 
		Select C02_no, C02_nm
		From code_c02
		order by C02_no
	";
	$C02Run = $NewSql -> db_query($Sql) or die("SQL ERROR 1");

//代碼	發票憑證類別
	$Sql = " 
		Select codet03_no, codet03_nm
		From code_t03
		order by codet03_no
	";
	$t03Run = $NewSql -> db_query($Sql) or die("SQL ERROR 2");	
	
//主		客戶管理
	$Sql = "
		Select cm01.comm01_no,comm01_nicknm
		From com_m01 cm01
		where comm01_type3 = 'Y'
		order by comm01_no
	";
	$comm01Run = $NewSql -> db_query($Sql) or die("SQL ERROR 3");	
	
//代碼	營業稅
	$salesTaxPer = CompanyTaxPer($NewSql);
//員工
	$Sql = "
		Select memm01_no, memm01_nick, memm01_uplevel
		From mem_m01 m01
		Left join mug_01 on m01.MUG01_NO = mug_01.MUG01_NO
		where MUG01_SHOW = 'Y'
		order by m01.MUG01_NO
	";
	$memRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
	$memCount = $NewSql -> db_num_rows($memRun);
	
	//廠商
	$Sql = "
		Select cm01.comm01_no,comm01_nicknm, ct02.comt02_branch ,ct02.comt02_acc, comm01_type1
		From com_m01 cm01
		Left join (
			Select *
			From com_t02
			limit 1
		)as ct02 on cm01.comm01_no = ct02.comm01_no			
		where comm01_type2 = 'Y'
		order by comm01_no	
	";
	$fireRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
	$fireCount = $NewSql -> db_num_rows($fireRun);	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<script language="javascript" src="PRJ02.js?20180530v16"></script>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	function ChkForm(form){
		//if(empty(form.paym01_type1.value,'對象')){}else 
		if(Notnum(form.paym01_subtotal.value,'金額',1)){
		}else{
			form.submit()
		}

	}
	$(function() {
		$(".datepicker").datepicker({
			dateFormat: 'yymmdd'
		});
		
		$(".chosen-select").chosen({
			no_results_text: "Oops, nothing found!",
			search_contains:true
		}); 	
		var type1 = '<?php echo $init["paym01_type1"];?>'
		switch (true){
			case type1 == 'M':
				$("#firmm01_no_chosen").hide();
				$('.custom').hide();
				break;
			case type1 == 'F':
				$("#memm01_no_chosen").hide()
				break;
			case type1 == 'P':
				$("#memm01_no_chosen").hide()
				$("#firmm01_no_chosen").hide()
				break;			
		}
	 });
	function EventClick(event){
		switch (event){
			case 'Del':
				if(confirm("確定刪除此筆資料嗎?")){
					if($("#paym01_no").val() == ""){
						return false;
						break;
					}else{
						$('#form1').prop('action','<?php echo $GetFileCode . '_Delete.php'?>').submit();
					}
				}
				break;
		}
			
	}
</script>
<style>
.td-box{	
	border:1px dashed #D2D2D2;	
}
td ul {
	padding:5px;
}
td ul li{
	line-height:40px;
	vertical-align:middle;
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
                        <?php
							$onClick = "";
                        	switch($Source){
								case 'PRJ01':
									$onClick = "parent.window.location.reload();parent.Shadowbox.close()";
									break;
								default:
									$onClick = "window.location='" . $GetFileCode . ".php?" . $PageCon . "'";
									break;								
							}
						
						?>                    
                        <?php if($ChangeData){?><li><input class="blue" type="button" value="Save" onClick="ChkForm(form1)"></li><?php }?>
                        <li><input class="gray" type="button" value="Return" onClick="<?php echo $onClick;?>" ></li>
                        <?php if($PCount <= 1 or $init['paym01_haspay'] != 'Y'){?><li><input class="red" type="button" value="Delete" onClick="EventClick('Del')"></li><?php }?>
                    </ul>
                </div>
                
                <div class="box-data">
                    	<div class="input-Group">
                        <form id="form1" name="form1" method="post" action="<?php echo $GetFileCode;?>_AlterNew.php">
                        	<input id="paym01_no" name="paym01_no" type="hidden" value="<?php echo $init["paym01_no"];?>">
                            <input id="paym01_haspay" name="paym01_haspay" type="hidden" value="<?php echo $init["paym01_haspay"]?>">
                            <input id="Source" name="Source" type="hidden" value="<?php echo $Source?>">
							<input id="PageCon" name="PageCon" type="hidden" value="<?php echo $PageCon?>">
                            <input id="ADD_ID" name="ADD_ID" type="hidden" value="<?php echo $init["ADD_ID"]?>">
                        	<table class="table-input">                           
                            	<tr>
                                	<td></td>
                                	<td>專案</td>
                                </tr> 
                            	<tr>
                                	<td></td>
                                	<td class="td-box">
                                    	<ul>
                                        	<li>
                                                客戶
                                                    <select id="comm01_no" name="comm01_no" class="chosen-select w300" data-placeholder="..." onChange="prjfromcom(this);content();">
                                                        <option value=""></option>
                                                        <?php while($comm01 = $NewSql -> db_fetch_array($comm01Run)){?>
                                                        <option value="<?php echo $comm01["comm01_no"]?>"<?php echo ($init["comm01_no"] == $comm01["comm01_no"])?" selected":"";?>><?php echo $comm01["comm01_nicknm"]?></option>
                                                        <?php }?>
                                                    </select>
                                            </li>
                                        	<li>
                                                專案
                                                    <select id="prjm01_no" name="prjm01_no" class="chosen-select w300" data-placeholder="..." onChange="t02fromprj(this);content();">
                                                        <option value=""></option> 
														<?php while($prj = $NewSql -> db_fetch_array($prjRun)){?>
                                                        	<option value="<?php echo $prj["prjm01_no"]?>" mem="<?php echo $prj["memm01_no"]?>" <?php echo ($init["prjm01_no"] == $prj["prjm01_no"])?" selected":"";?>><?php echo $prj["prjm01_nm"]?></option>
                                                        <?php }?>                                               
                                                    </select>
                                                    <span id="prj-t02" style="font-weight:bold; color:#F8060A;"></span>
                                            </li> 
                                        	<li>
                                                內容
                                                	<input id="paym01_prjcontent" name="paym01_prjcontent" type="text" maxlength="50" value="<?php echo $init["paym01_prjcontent"];?>">
                                            </li>                                                                                                                                 
										</ul>
                                    </td>
                                </tr>
                            	<tr>
                                	<td></td>
                                	<td>給付對象</td>
                                </tr>                                                                                            
                            	<tr>
                                	<td></td>
                                	<td class="td-box">
										<ul>
                                        	<li>
                                            <?php //若為成員不需要填給付&發票?>
                                            	<em>*</em> 對象
													<select id="paym01_type1" name="paym01_type1" class="chosen-select w200" data-placeholder="..." onChange="pfrompay(this)">
                                                        <option value=""></option>
                                                        <option value="M"<?php echo ($init["paym01_type1"] == "M")?" selected":"";?>>成員</option>
                                                        <option value="F"<?php echo ($init["paym01_type1"] == "F")?" selected":"";?>>外製</option>
                                                        <option value="P"<?php echo ($init["paym01_type1"] == "P")?" selected":"";?>>已支出</option>                                                        
                                                    </select>  
													<select id="memm01_no" name="memm01_no" class="chosen-select w200" data-placeholder="..." onChange="GetTotal();">
                                                        <option value="">...</option>
                                                        <?php 
															while($mem = $NewSql -> db_fetch_array($memRun)){
																echo '<option value="' . $mem["memm01_no"] . '" T1="P" ';
																echo ($init["memm01_no"] == $mem["memm01_no"]& $init["paym01_type1"] == "M")?" selected":"";
																echo ' level = "' . $mem["memm01_uplevel"] . '"';
																echo '>' . $mem["memm01_nick"] . '</option>';		
															}														
														?>
                                                    </select> 
													<select id="firmm01_no" name="firmm01_no" class="chosen-select w200" data-placeholder="..." onChange="firmChange(this)" >
                                                    	<option value="">...</option>
                                                        <?php 
															while($fire = $NewSql -> db_fetch_array($fireRun)){
																echo '<option value="' . $fire["comm01_no"] . '"' . ' acc="' . $fire["comt02_acc"] . '"' . ' branch="' . $fire["comt02_branch"] . '"';
																echo ' T1="' . $fire["comm01_type1"] . '" ';
																echo ($init["firmm01_no"] == $fire["comm01_no"] & $init["paym01_type1"] == "F")?" selected":"";
																echo '>' . $fire["comm01_nicknm"] . '</option>';		
															}														
														?>                                                      
                                                    </select>                                                                                                                                                      
                                            </li>
                                            <li class="custom">
                                            	發票憑證
                                                	<select id="t03_no" name="t03_no" class="chosen-select w200" data-placeholder="...">
                                                        <option value=""></option>
                                                        <?php while($t03 = $NewSql -> db_fetch_array($t03Run)){?>
                                                        <option value="<?php echo $t03["codet03_no"]?>"<?php echo ($init["t03_no"] == $t03["codet03_no"])?" selected":"";?>><?php echo $t03["codet03_nm"]?></option>
                                                        <?php }?>
                                                    </select>
													<input type="text" id="paym01_invoiceno" name="paym01_invoiceno" size="20" maxlength="20" placeholder="發票號" value="<?php echo $init["paym01_invoiceno"];?>">
                                                    <input type="text" id="paym01_invoicedate" name="paym01_invoicedate" size="8" maxlength="8" class="datepicker" placeholder="發票日期" value="<?php echo $init["paym01_invoicedate"];?>">
                                            </li>
                                            <li class="custom">
                                            	給付方式
                                                	<select id="c" name="paym01_paytype" class="chosen-select w200" data-placeholder="..." >
                                                        <option value=""></option>
                                                        <option value="1"<?php echo ($init["paym01_paytype"] == "1")?" selected":"";?>>轉帳匯款</option>
                                                        <option value="2"<?php echo ($init["paym01_paytype"] == "2")?" selected":"";?>>現金</option>
                                                        <option value="3"<?php echo ($init["paym01_paytype"] == "3")?" selected":"";?>>支票</option>
                                                        <option value="4"<?php echo ($init["paym01_paytype"] == "4")?" selected":"";?>>信用卡</option>
                                                    </select>
                                                    <!--
                                                    <br>
                                                    銀行分行 <input type="text" id="paym01_paybrunch" name="paym01_paybrunch" size="10" maxlength="10" placeholder="分行" value="<?php echo $init["paym01_paybrunch"];?>"><br>
                                                    銀行帳號 <input type="text" id="paym01_payacc" name="paym01_payacc" size="20" maxlength="20" placeholder="帳號" value="<?php echo $init["paym01_payacc"];?>">
                                                    -->
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            	<tr>
                                	<td></td>
                                	<td>
                                    	<ul>
                                        	<li>
                                            	<em>*</em>金額
                                                <input type="text" id="paym01_subtotal" name="paym01_subtotal" size="20" maxlength="20" onChange="GetTotal()" value="<?php echo $init["paym01_subtotal"];?>">
                                                <input type="hidden" id="paym01_paylearn" name="paym01_paylearn" value="<?php echo $init["paym01_paylearn"];?>"/>
                                                
                                                <select id="paym01_hastax" name="paym01_hastax" placeholder="營業稅" onChange="GetTotal()">
                                                    <option value="N"<?php echo ($init["paym01_hastax"] == "N")?" selected":"";?>>無</option>
                                                    <option value="Y"<?php echo ($init["paym01_hastax"] == "Y")?" selected":"";?>>營業稅<?php echo $salesTaxPer?>%</option>                                            
                                                </select>
                                                <input type="hidden" id="paym01_taxper" name="paym01_taxper" size="3" maxlength="3" value="<?php echo $salesTaxPer?>">
                                                <input type="hidden" id="paym01_tax" name="paym01_tax" value="<?php echo $init["paym01_tax"];?>">
                                                合計
                                                <span id="hasTaxTotal" style="text-decoration:underline; color:#FC060A"><?php echo $init["paym01_total"] - $init["paym01_paylearn"];?></span>                                                
                                            </li>
                                            <li>
                                            	實付<span id="paym01_paytotalTEXT" style="text-decoration:underline; color:#FC060A; padding:10px;"><?php echo $init["paym01_paytotal"];?></span>
                                                <input type="hidden" id="paym01_paytotal" name="paym01_paytotal" value="<?php echo $init["paym01_paytotal"];?>">
                                                稅額 
                                                <Select id="paym01_discountTax" name="paym01_discountTax" onChange="discountTax();" value="<?=$init["paym01_discountTax"];?>">
	                                                <option value=".14"<?php echo ($init["paym01_discountTax"] == '.14'?" selected":"");?>>14%</option>
                                                	<option value=".12"<?php echo ($init["paym01_discountTax"] == '.12'?" selected":"");?>>12%</option>
                                                	<option value=".09"<?php echo ($init["paym01_discountTax"] == '.09'?" selected":"");?>>9%</option>
                                                	<option value=".07"<?php echo ($init["paym01_discountTax"] == '.07'?" selected":"");?>>7%</option>
                                                	<option value=".05"<?php echo ($init["paym01_discountTax"] == '.05'?" selected":"");?>>5%</option>
                                                	<option value="0"<?php echo ($init["paym01_discountTax"] == '0'?" selected":"");?>>0%</option>
                                                </Select>
												<br/>
												<span id="marker" style="font-size:10px;<?php echo ($init["paym01_type1"] == "M")?" display:none;":"";?>">＊若費用項目為交通、交際，境外軟體圖庫等，不論憑證是否為發票，均視為費用支出不扣抵稅金，請選擇7％。</span>
                                            </li>                                            
                                            <li>
                                            給付日
                                            	<input type="text" id="paym01_paydate" name="paym01_paydate" size="8" maxlength="10" class="datepicker" value="<?php echo $init["paym01_paydate"];?>" onChange="ChkPayDate()">
                                                <!--<input type="checkbox" id="paym01_advance" name="paym01_advance" value="Y" <?php //echo ($init["paym01_advance"] == "Y"?" checked":"");?>>預付-->
                                            </li>
											<li>
                                            備註
                                            	<textarea id="paym01_remark" name="paym01_remark" rows="2" cols="80"><?php echo $init["paym01_remark"];?></textarea>
                                            </li>                                            
                                        </ul>                                    	
                                    </td>
                                </tr>
                            </table>
						</form>
                        </div>
                </div>
			</div>
		</div>
    </div>    
</div>
</body>
</html>