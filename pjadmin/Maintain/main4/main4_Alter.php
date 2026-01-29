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
//資料庫連線
	$NewSql = new mysql();	
		
//參數
	$GetFileCode = GetFileCode(__FILE__);
	$GetFileType = GetFileType(__FILE__);	
	$GetTitle = GetTitle($NewSql,$GetFileCode);
	$DataKey = xRequest("DataKey");
	
//撥款
	$Sql = "
		Select payt02_no ,payt02_paydate, payt02_paytotal,  payt02_acttotal, payt02_paytype ,payt02_remain
		From pay_t02 
		where payt02_no = '$DataKey'	
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$init = $NewSql -> db_fetch_array($initRun);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<script language="javascript" src="PRJ03.js"></script>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	$(function(){
		$(".datepicker").datepicker({
			dateFormat: 'yymmdd'
		});
	})
	function ChkForm(form){
		if(empty(form.payt02_paydate.value,'給付日')){ 
		}else if(Notnum(form.payt02_acttotal.value,'實收',1)){
		}else{
			$.post(
				"main4_AlterNew.php",
				$("#form1").serialize(),
				function(xml){
					window.parent.location.reload();
					parent.Shadowbox.close()
				}
			)
		}

	}
	function ExRemaint(){
		$("#payt02_remain").val($("#payt02_paytotal").val() - $("#payt02_acttotal").val())
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
                        <form id="form1" name="form1" method="post" action="main4_AlterNew.php">
                        	<input id="payt02_no" name="payt02_no" type="hidden" value="<?php echo $DataKey;?>">
                        	<table class="table-input">                                
                                <tr>
                                	<td></td>
                                	<td>
                                    	<table>
                                        	<tr>
                                            	<td><em>*</em>給付日</td>
                                            	<td><input id="payt02_paydate" name="payt02_paydate" class="datepicker" type="text"></td>                                                
                                            </tr>
                                        	<tr>
                                            	<td>應付</td>
                                            	<td>$<?php echo $init["payt02_paytotal"];?>
                                                <input id="payt02_paytotal" name="payt02_paytotal" type="hidden" value="<?php echo $init["payt02_paytotal"];?>">
                                                </td>                                                
                                            </tr>   
                                        	<tr>
                                            	<td><em>*</em>實收發放</td>
                                            	<td>$<input id="payt02_acttotal" name="payt02_acttotal" type="text" size="10" max="8" onChange="ExRemaint()">
                                                方式
                                            	<select id="payt02_paytype" name="payt02_paytype">
													<option value=""></option>
                                                    <option value="1">轉帳匯款</option>
                                                    <option value="2">現金</option>
                                                    <option value="3">支票</option>
                                                </select>
                                                </td>                                                
                                            </tr>
                                        	<tr>
                                            	<td>餘額</td>
                                            	<td><input id="payt02_remain" name="payt02_remain" type="text" size="10" max="8" disabled 
                                                value="<?php echo $init["payt02_paytotal"];?>"></td>
                                            </tr>
                                            <tr>
                                            	<td></td>
                                                <td>
													<input class="blue" type="button" value="給付" onClick="ChkForm(form1)">								
                                                </td>
                                            </tr>
                                        </table>
                                    </td>                                    
                                </tr>                                                          
                            </table>
						</form>
                        </div>
                </div>  
</div>
</body>
</html>