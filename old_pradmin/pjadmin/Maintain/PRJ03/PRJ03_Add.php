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
	
//主		客戶管理
	$Sql = "
		Select comm01_no,comm01_nicknm, comm01_quid
		From com_m01
		where comm01_type3 = 'Y'		
		order by comm01_no
	";
	$comm01Run = $NewSql -> db_query($Sql) or die("SQL ERROR 3");
	
//主檔 專案
	$Sql = "
		Select prjm01_no, prjm01_nm, m.c02_no, ct02.codet02_nm
		From prj_m01 m
		Left join code_t02 ct02 on m.t02_no  = ct02.codet02_no
		Where comm01_no = '" . $comm01_no . "'
	";
	$prjRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$prjCount = $NewSql -> db_num_rows($prjRun);
	
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
<script language="javascript" src="PRJ03.js"></script>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	$(window).load(function(){
		$("#comm01_quid").val($("#comm01_no").find('option:selected').attr('quid'))
	})
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
                        <input id="ADD_ID" name="ADD_ID" type="hidden" value="<?php echo "";?>">
                        	<table class="table-input">
                            	<tr>
                                	<td><em>*</em>客戶</td>
                                	<td>
										<select id="comm01_no" name="comm01_no" class="chosen-select w300" data-placeholder="..." onChange="prjfromcom(this.value);quidfromcom(this);content();">
											<option value=""> ---</option>
											<?php while($comm01 = $NewSql -> db_fetch_array($comm01Run)){?>
												<option value="<?php echo $comm01["comm01_no"]?>" quid="<?php echo $comm01["comm01_quid"];?>" <?php echo ($comm01_no == $comm01["comm01_no"])?" selected":"";?>><?php echo $comm01["comm01_nicknm"]?></option>
											<?php }?>
										</select>                                    
                                    </td>
                                </tr> 
                            	<tr>
                                	<td>統編</td>
                                	<td><input id="comm01_quid" name="comm01_quid" size="30" maxlength="30"></td>
                                </tr>
                            	<tr>
                                	<td>專案</td>
                                	<td>
										<select id="prjm01_no" name="prjm01_no" class="chosen-select w300" data-placeholder="..." onChange="content();">
											<option value=""> --- </option>   
											<?php while($prj = $NewSql -> db_fetch_array($prjRun)){?>
												<option value="<?php echo $prj["prjm01_no"]?>" <?php echo ($prjm01_no == $prj["prjm01_no"])?" selected":"";?>><?php echo $prj["prjm01_nm"]?></option>
											<?php }?>                                                                                               
										</select>                                    
                                    </td>
                                </tr>
                            	<tr>
                                	<td>內容</td>
                                	<td><input id="inm01_content" name="inm01_content" size="60" maxlength="50"></td>
                                </tr>
                            	<tr>
                                	<td><em>*</em>金額</td>
                                	<td>
										<input type="text" id="inm01_subtotal" name="inm01_subtotal" size="20" maxlength="20" value="0" onChange="GetTotal()">
                                                
										<select id="inm01_hastax" name="inm01_hastax" placeholder="營業稅" onChange="GetTotal()">
											<option value="N">無</option>
											<option value="Y">營業稅<?php echo $salesTaxPer?>%</option>                                            
										</select>                                 
                                    </td>
                                </tr>
                            	<tr>
                                	<td>總計</td>
                                	<td>
										<input type="hidden" id="inm01_taxper" name="inm01_taxper" size="3" maxlength="3" value="<?php echo $salesTaxPer?>">
										<input type="hidden" id="inm01_tax" name="inm01_tax" value="0">                                    
                                    	<span id="hasTaxTotal" style="text-decoration:underline; color:#FC060A">0</span>
                                    </td>
                                </tr>
                            	<tr>
                                	<td>備註</td>
                                	<td>
                                    	<textarea id="inm01_remark" name="inm01_remark" cols="60" rows="3"></textarea>
                                    </td>
                                </tr>                                                                                                
                            	<tr>
                                	<td>開立資訊</td>
                                	<td>
                                    日期 <input id="inm01_invoicedate" name="inm01_invoicedate" size="10" maxlength="8" class="datepicker" readonly>
                                    發票號碼 <input id="inm01_invoiceno" name="inm01_invoiceno" size="30" maxlength="20" >
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