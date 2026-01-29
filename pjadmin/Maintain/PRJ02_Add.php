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
	LoginChk(GetFileCode(__FILE__),"2");
//資料庫連線
	$NewSql = new mysql();	
//參數
	$GetFileCode = GetFileCode(__FILE__);
	$GetFileType = GetFileType(__FILE__);	
	$GetTitle = GetTitle($NewSql,$GetFileCode);
	$Source = xRequest("Source");				//網頁來源
	$comm01_no = xRequest("comm01_no");	
	$prjm01_no = xRequest("prjm01_no");
	$PageCon = xRequest("PageCon");
		
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
		Select cm01.comm01_no,comm01_nicknm, ct02.comt02_branch ,ct02.comt02_acc
		From com_m01 cm01
		Left join (
			Select *
			From com_t02
			limit 1
		)as ct02 on cm01.comm01_no = ct02.comm01_no
		where comm01_type3 = 'Y'
		order by comm01_no
	";
	$comm01Run = $NewSql -> db_query($Sql) or die("SQL ERROR 3");	
	
//主檔 專案
	$Sql = "
		Select prjm01_no, prjm01_nm, m.c02_no, ct02.codet02_nm, m.memm01_no
		From prj_m01 m
		Left join code_t02 ct02 on m.t02_no  = ct02.codet02_no
		Where comm01_no = '" . $comm01_no . "'
	";
	$prjRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$prjCount = $NewSql -> db_num_rows($prjRun);

//專案負責人
	$Sql = "
		Select memm01_no From prj_m01 where prjm01_no = '$prjm01_no'
	";
	$prjmemRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$prjmemNo = $NewSql -> db_result($prjmemRun);
//代碼	營業稅
	$Sql = "
		Select C05_per
		From code_c05
		where C05_no = 'sales'
	";
	$C05Run = $NewSql -> db_query($Sql) or die("SQL ERROR 4");	
	$salesTaxPer = $NewSql -> db_result($C05Run);		
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
		
		$("#memm01_no_chosen").hide()
		$("#firmm01_no_chosen").hide()		
	 });

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
									$onClick = "parent.Shadowbox.close();";
									break;
								default:
									$onClick = "window.location='" . $GetFileCode . ".php?" . $PageCon . "'";
									break;								
							}						
						?>                    
                        <li><input class="blue" type="button" value="Save" onClick="ChkForm(form1)"></li>
                        <li><input class="gray" type="button" value="Return" onClick="<?php echo $onClick;?>" ></li>
                    </ul>
                </div>
                
                <div class="box-data">
                    	<div class="input-Group">
                        <form id="form1" name="form1" method="post" action="<?php echo $GetFileCode;?>_AddNew.php">
                        <input id="Code_Id" name="Code_Id" type="hidden" value="<?php echo $GetFileCode;?>">
                        <input id="Source" name="Source" type="hidden" value="<?php echo $Source?>">
                        <input id="PageCon" name="PageCon" type="hidden" value="<?php echo $PageCon?>">
                        <input id="ADD_ID" name="ADD_ID" type="hidden" value="<?php echo ($prjmemNo != '' ? $prjmemNo :$_SESSION["MemNO"]);?>">
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
                                                        <option value="<?php echo $comm01["comm01_no"]?>" branch="<?php echo $comm01["comt02_branch"]?>" acc="<?php echo $comm01["comt02_acc"]?>" <?php echo ($comm01_no == $comm01["comm01_no"])?" selected":"";?>><?php echo $comm01["comm01_nicknm"]?></option>
                                                        <?php }?>
                                                    </select>
                                            </li>
                                        	<li>
                                                專案
                                                    <select id="prjm01_no" name="prjm01_no" class="chosen-select w300" data-placeholder="..." onChange="t02fromprj(this);content();">
                                                        <option value=""></option> 
														<?php while($prj = $NewSql -> db_fetch_array($prjRun)){?>
                                                            <option value="<?php echo $prj["prjm01_no"]?>" PM="" mem="<?php echo $prj["memm01_no"]?>" <?php echo ($prjm01_no == $prj["prjm01_no"])?" selected":"";?>><?php echo $prj["prjm01_nm"]?></option>
                                                        <?php }?>                                                                                                             
                                                    </select>
                                                    <span id="prj-t02" style="font-weight:bold; color:#F8060A;"></span>
                                            </li> 
                                        	<li>
                                                內容
                                                	<input id="paym01_prjcontent" name="paym01_prjcontent" type="text" maxlength="50">
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
													<select id="paym01_type1" name="paym01_type1" class="chosen-select w200" data-placeholder="..." onChange="pfrompay(this);">
                                                        <option value=""></option>
                                                        <option value="M">成員</option>
                                                        <option value="F">外製</option>
                                                        <option value="P">已支出</option>                                                        
                                                    </select>  
													<select id="memm01_no" name="memm01_no" class="chosen-select w200" data-placeholder="..." onChange="GetTotal();">
                                                        <option value=""></option>
                                                        
                                                    </select> 
													<select id="firmm01_no" name="firmm01_no" class="chosen-select w200" data-placeholder="..."  onChange="firmChange(this);GetTotal();">
                                                        <option value=""></option>                                                        
                                                    </select>                                                                                                                                                      
                                            </li>
                                            <li class="custom">
                                            	發票憑證
                                                	<select id="t03_no" name="t03_no" class="chosen-select w200" data-placeholder="...">
                                                        <option value=""></option>
                                                        <?php while($t03 = $NewSql -> db_fetch_array($t03Run)){?>
                                                        <option value="<?php echo $t03["codet03_no"]?>"><?php echo $t03["codet03_nm"]?></option>
                                                        <?php }?>
                                                    </select>
													<input type="text" id="paym01_invoiceno" name="paym01_invoiceno" size="20" maxlength="20" placeholder="發票號">
                                                    <input type="text" id="paym01_invoicedate" name="paym01_invoicedate" size="8" maxlength="8" class="datepicker" placeholder="發票日期">
                                            </li>
                                            <li class="custom">
                                            	給付方式
                                                	<select id="paym01_paytype" name="paym01_paytype" class="chosen-select w200" data-placeholder="..." >
                                                        <option value=""></option>
                                                        <option value="1">轉帳匯款</option>
                                                        <option value="2">現金</option>
                                                        <option value="3">支票</option>
                                                        <option value="4">信用卡</option>                                                        
                                                    </select>
                                                    <!--
                                                    <br>
                                                    銀行分行 <input type="text" id="paym01_paybrunch" name="paym01_paybrunch" size="10" maxlength="10" placeholder="分行"><br>
                                                    銀行帳號 <input type="text" id="paym01_payacc" name="paym01_payacc" size="20" maxlength="20" placeholder="帳號">
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
                                                <input type="text" id="paym01_subtotal" name="paym01_subtotal" size="20" maxlength="20" value="0" onChange="GetTotal()">
                                                <input type="hidden" id="paym01_paylearn" name="paym01_paylearn" />
                                                
                                                <select class="custom" id="paym01_hastax" name="paym01_hastax" placeholder="營業稅" onChange="GetTotal()">
                                                    <option value="N">無</option>
                                                    <option value="Y">營業稅<?php echo $salesTaxPer?>%</option>                                            
                                                </select>
                                                <input type="hidden" id="paym01_taxper" name="paym01_taxper" size="3" maxlength="3" value="<?php echo $salesTaxPer?>">
                                                <input type="hidden" id="paym01_tax" name="paym01_tax" value="0">
                                                合計
                                                <span id="hasTaxTotal" style="text-decoration:underline; color:#FC060A">0</span>                                                
                                            </li>
                                            <li>
                                            	實付<span id="paym01_paytotalTEXT" style="text-decoration:underline; color:#FC060A; padding:10px;">0</span>
                                                <input type="hidden" id="paym01_paytotal" name="paym01_paytotal" >
                                                稅額
                                                <Select id="paym01_discountTax" name="paym01_discountTax" onChange="discountTax();">
												</Select>
												<br/>
												<span id="marker" style="font-size:10px;display:none;">＊若費用項目為交通、交際，境外軟體圖庫等，不論憑證是否為發票，均視為費用支出不扣抵稅金，請選擇7％。</span>                                             
                                            </li>
                                            <li>
                                            給付日
                                            	<input type="text" id="paym01_paydate" name="paym01_paydate" size="8" maxlength="8" class="datepicker" onChange="ChkPayDate()">
                                            </li>
											<li>
                                            備註
                                            	<textarea id="paym01_remark" name="paym01_remark" rows="2" cols="80"></textarea>
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