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
	$DataKey = xRequest("DataKey");
	$Source = xRequest("Source");				//網頁來源
	$PageCon = xRequest("PageCon");
		
	$Sql = " 
		Select *
		From in_m01
		where inm01_no = '$DataKey' 
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$init = $NewSql -> db_fetch_array($initRun);
	$initCount = $NewSql -> db_num_rows($initRun);
	
	if($initCount != 1){
		header('location:' . $GetFileCode . '.php');
	}
//主檔 專案
	$Sql = "
		Select prjm01_no, prjm01_nm, m.c02_no, ct02.codet02_nm
		From prj_m01 m
		Left join code_t02 ct02 on m.t02_no  = ct02.codet02_no
		Where comm01_no = '" . $init["comm01_no"] . "'
/*		and m.t02_no in ('1','2','3','5')		*/
	";
	$prjRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$prjCount = $NewSql -> db_num_rows($prjRun);
	

//主		客戶管理
	$Sql = "
		Select comm01_no,comm01_nicknm, comm01_quid
		From com_m01
		where comm01_type3 = 'Y'		
		order by comm01_no
	";
	$comm01Run = $NewSql -> db_query($Sql) or die("SQL ERROR 3");
	
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
                        <?php if($init["inm01_type"] != "4"){?><li><input class="blue" type="button" value="Save" onClick="ChkForm(form1)"></li><?php }?>
                        <?php
							$onClick = "";
                        	switch($Source){
								case 'PRJ01':
									$onClick = "parent.window.location.reload();parent.Shadowbox.close();";
									break;
								default:
									$onClick = "window.location='" . $GetFileCode . ".php?" . $PageCon . "'";
									break;								
							}						
						?>
                        <li><input class="gray" type="button" value="Return" onClick="<?php echo $onClick;?>" ></li>
                        <?php //if($init["inm01_type"] == "1" || $init["inm01_type"] == ""){?><li><input class="red" type="button" value="Delete" onClick="InInvalid('<?php echo $init["inm01_no"];?>')" ></li><?php //}?>
                    </ul>
                </div>
                
                <div class="box-data">
                    	<div class="input-Group">
                        <form id="form1" name="form1" method="post" action="<?php echo $GetFileCode;?>_AlterNew.php">
                        	<input id="inm01_no" name="inm01_no" type="hidden" value="<?php echo $init["inm01_no"];?>">
                            <input id="Source" name="Source" type="hidden" value="<?php echo $Source?>">
                            <input id="inm01_type" name="inm01_type" type="hidden" value="<?php echo $init["inm01_type"];?>">
							<input id="PageCon" name="PageCon" type="hidden" value="<?php echo $PageCon;?>">
                        	<table class="table-input">
                            	<tr>
                                	<td><em>*</em>客戶</td>
                                	<td>
										<select id="comm01_no" name="comm01_no" class="chosen-select w300" data-placeholder="..." onChange="prjfromcom(this.value);quidfromcom(this);content();">
											<option value=""> - /</option>
											<?php while($comm01 = $NewSql -> db_fetch_array($comm01Run)){?>
												<option value="<?php echo $comm01["comm01_no"]?>" quid="<?php echo $comm01["comm01_quid"];?>" <?php echo ($init["comm01_no"] == $comm01["comm01_no"])?" selected":"";?>><?php echo $comm01["comm01_nicknm"]?></option>
											<?php }?>
										</select>                                    
                                    </td>
                                </tr> 
                            	<tr>
                                	<td>統編</td>
                                	<td><input id="comm01_quid" name="comm01_quid" size="30" maxlength="30" value="<?php echo $init["comm01_quid"];?>"></td>
                                </tr>
                            	<tr>
                                	<td>專案</td>
                                	<td>
										<select id="prjm01_no" name="prjm01_no" class="chosen-select w300" data-placeholder="..." onChange="content();">
											<option value=""> - /</option>  
											<?php while($prj = $NewSql -> db_fetch_array($prjRun)){?>
												<option value="<?php echo $prj["prjm01_no"]?>"<?php echo ($init["prjm01_no"] == $prj["prjm01_no"])?" selected":"";?>><?php echo $prj["prjm01_nm"]?></option>
											<?php }?>                                                                                                
										</select>                                    
                                    </td>
                                </tr>
                            	<tr>
                                	<td>內容</td>
                                	<td><input id="inm01_content" name="inm01_content" size="60" maxlength="50" value="<?php echo $init["inm01_content"];?>"></td>
                                </tr>
                            	<tr>
                                	<td><em>*</em>金額</td>
                                	<td>
										<input type="text" id="inm01_subtotal" name="inm01_subtotal" size="20" maxlength="20" value="<?php echo $init["inm01_subtotal"];?>" onChange="GetTotal()">
                                                
										<select id="inm01_hastax" name="inm01_hastax" placeholder="營業稅" class="chosen-select w150" data-placeholder="..." onChange="GetTotal()">
											<option value="N" <?php echo ($init["inm01_hastax"] == "N")?" selected":"";?>>無</option>
											<option value="Y" <?php echo ($init["inm01_hastax"] == "Y")?" selected":"";?>>營業稅<?php echo $salesTaxPer?>%</option>                                            
										</select>
                                    </td>
                                </tr>
                            	<tr>
                                	<td>總計</td>
                                	<td>
										<input type="hidden" id="inm01_taxper" name="inm01_taxper" size="3" maxlength="3" value="<?php echo $salesTaxPer?>">
										<input type="hidden" id="inm01_tax" name="inm01_tax" value="<?php echo $init["inm01_tax"];?>">                                    
                                    	<span id="hasTaxTotal" style="text-decoration:underline; color:#FC060A"><?php echo $init["inm01_total"];?></span>
                                    </td>
                                </tr>
                            	<tr>
                                	<td>備註</td>
                                	<td>
                                    	<textarea id="inm01_remark" name="inm01_remark" cols="60" rows="3"><?php echo $init["inm01_remark"];?></textarea>
                                    </td>
                                </tr> 
                                                                                                                               
                            	<tr>
                                	<td>開立資訊</td>
                                	<td>
                                    </td>
                                </tr>
                            	<tr>
                                	<td></td>
                                	<td>
                                        日期 <input id="inm01_invoicedate" name="inm01_invoicedate" size="10" maxlength="8" class="datepicker" readonly value="<?php echo $init["inm01_invoicedate"];?>">
                                        發票號碼 <input id="inm01_invoiceno" name="inm01_invoiceno" size="30" maxlength="20" value="<?php echo $init["inm01_invoiceno"];?>" >
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