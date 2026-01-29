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
	
	$Sql = " 
		Select inm01_no, mem_m01.memm01_nm, m01.comm01_nicknm , inm01_content,inm01_total ,inm01_incometotal
		, I01.prjm01_no, I01.inm01_type
		, prj.prjm01_nm
		, I01.inm01_hastax
		, I01.ADD_ID
		From in_m01 I01
		Left join prj_m01 prj on I01.prjm01_no = prj.prjm01_no
		Left join com_m01 m01 on I01.comm01_no = m01.comm01_no
		Left join code_t05 t05 on I01.inm01_type = t05.codet05_code
		Left join mem_m01 on I01.ADD_ID = mem_m01.memm01_no
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
		and m.t02_no in ('1','2','3','5')		
	";
	$prjRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$prjCount = $NewSql -> db_num_rows($prjRun);
	
//明細	付款
	$Sql = "
		select int01_no ,int01_date ,int01_incometotal, int01_incometype,int01_remark
		from in_t01
		where inm01_no = '$DataKey'	
	";
	$int01Run = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$int01Count = $NewSql -> db_num_rows($int01Run);
		
//主		客戶管理
	$Sql = "
		Select comm01_no,comm01_nicknm, comm01_quid
		From com_m01
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
<script>
	function ChkForm(form){
		if(empty(form.int01_date.value,'給付日')){ 
		}else if(Notnum(form.int01_incometotal.value,'實收',1)){
		}else{
			$.post(
				"PRJ03_ViewNew.php",
				$("#form1").serialize(),
				function(xml){
					window.location.reload();
				}
			)
		}

	}
	function IncomeRemove(val){
		if(confirm("確定要刪除入帳紀錄嗎?")){
			$.post(
				'PRJ03_ViewDel.php',
				{DataKey:$("#inm01_no").val(),no:val},
				function(xml){
					window.location.reload();
				}
			);
		}
	}
	//檢查給付日期是否已經結帳
	function ChkPayDate(){
		var val = $("#int01_date").val()
		var mem = $("#ADD_ID").val()
		if(val != ""){
			//window.location = 'Post_PRJ03.php?date=' + val + '&mem=' + mem
			$.post(
				'Post_PRJ03.php',
				{date:val,mem:mem},
				function(xml){
					if($('resu',xml).text() == '1'){
					}else{
						$("#int01_date").val('')
						if($('rtMeg',xml).text() != ''){
							alert($('rtMeg',xml).text())
						}else{
							alert("請選擇正確資料");
						}
					}
				}
			);
		}
	}	
</script>
<style>
.table-input .minTab{
	padding:5px;
	border:1px dashed #A5A5A5;
}
.table-input .minTab td{
	text-align:center;
	border:1px dashed #A5A5A5;	
}
.table-input .minTab td.date{ width:50px;}
.table-input .minTab td.money{ width:50px;}
.table-input .minTab td.method{ width:50px;}
.table-input .minTab td.per{ width:50px;}
.table-input .minTab td.close{ width:50px;}
</style>
</head>

<body>
<div id="wrapper">
	<div class="box-data">
             	<div class="input-Group">
                        <form id="form1" name="form1" method="post" action="PRJ03_ViewNew.php">
                        	<input id="inm01_no" name="inm01_no" type="hidden" value="<?php echo $init["inm01_no"];?>">
                            <input id="prjm01_no" name="prjm01_no" type="hidden" value="<?php echo $init["prjm01_no"];?>">
                            <input id="ADD_ID" name="ADD_ID" type="hidden" value="<?php echo $init["ADD_ID"];?>" />
                        	<table class="table-input">
                            	<tr>
                                	<td>應收資訊</td>                                
                                	<td>
                                        <ul class="discript" style=" color:#FB1417;">
                                            <li>
                                                客戶 : <?php echo $init["comm01_nicknm"];?>
                                            </li>
                                            <li>
                                                專案名稱 : <?php echo $init["prjm01_nm"];?>
                                            </li>                                            
                                            <li>
                                                內容 : <?php echo $init["inm01_content"];?>
                                            </li>
                                            <li>
                                                總金額 : <?php echo MoneyFormat($init["inm01_total"]);?>
                                            </li>                                            
                                        </ul>                                                                        
                                    </td>
                                </tr>
                                <tr>
                                	<td>入帳資訊</td>
                                    <td>
										<table class="minTab">
                                        	<tr>
                                                <td class="date">日期</td>
                                                <td class="money">金額</td>
                                                <td class="method">方式</td>
                                                <td class="per">比例</td>
                                                <td class="per">備註</td>
                                                <td class="close"></td>
											</tr>
                                            <?php while($int01 = $NewSql -> db_fetch_array($int01Run)){?>
                                        	<tr>
                                                <td width="100"><?php echo DspDate($int01["int01_date"],"/")?></td>
                                                <td width="100"><?php echo '$' . $int01["int01_incometotal"]?></td>
                                                <td>
												<?php 
													switch($int01["int01_incometype"]){
														case "1":
															echo "匯款";
															break;
														case "2":
															echo "現金";
															break;
														case "3":
															echo "支票";
															break;																														
													};
												?>
                                                </td>
                                                <td><?php echo round($int01["int01_incometotal"]/$init["inm01_total"]*100,2) . "%";?></td>                                                <td><?php echo $int01["int01_remark"];?></td>
                                                <td><a class="remove" onClick="IncomeRemove('<?php echo $int01["int01_no"];?>')"></a></td>
											</tr>
                                            <?php }?>                                           
                                        </table>
                                    </td>
                                </tr>
                                <?php if($init["inm01_total"] - $init["inm01_incometotal"] > 0 and $init["inm01_type"] != "4"){?>
                                <tr>
                                	<td>新增</td>
                                	<td></td>                                    
                                </tr>                                
                                <tr>
                                	<td></td>
                                	<td>
                                    	<table>
                                        	<tr>
                                            	<td><em>*</em>給付日</td>
                                            	<td><input id="int01_date" name="int01_date" class="datepicker" onChange="ChkPayDate()" type="text"></td>                                                
                                            </tr>
                                        	<tr>
                                            	<td>應付</td>
                                            	<td>
                                                <input id="int01_remain" name="int01_remain" type="hidden" value="<?php echo ($init["inm01_total"] - $init["inm01_incometotal"]);?>">
                                                <span><?php echo MoneyFormat($init["inm01_total"] - $init["inm01_incometotal"]);?></span>
                                                </td>                                                
                                            </tr>   
                                        	<tr>
                                            	<td><em>*</em>實收</td>
                                            	<td>$<input id="int01_incometotal" name="int01_incometotal" type="text">
                                            	<select id="int01_incometype" name="int01_incometype">
													<option value=""></option>
                                                    <option value="1">轉帳匯款</option>
                                                    <option value="2">現金</option>
                                                    <option value="3">支票</option>
                                                </select>
                                                </td>                                                
                                            </tr>
                                        	<tr>
                                            	<td>備註</td>
                                            	<td><textarea id="int01_remark" name="int01_remark"></textarea></td>
                                            </tr>
                                            <tr>
                                            	<td></td>
                                                <td>
													<input class="blue" type="button" value="Save" onClick="ChkForm(form1)">								
                                                </td>
                                            </tr>
                                        </table>
                                    </td>                                    
                                </tr> 
                                <?php }?>                                                                                              
                            </table>
						</form>
                        </div>
                </div>  
</div>
</body>
</html>