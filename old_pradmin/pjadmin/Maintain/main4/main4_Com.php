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
	$totalpay = xRequest("T");
	$payt02_no = xRequest("payt02_no");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	$(function(){
		$(".datepicker").datepicker({
			dateFormat: 'yymmdd'
		});
	})
	function ChkForm(form){
		if(empty(form.foundt03_date.value,'給付日')){ 
		}else if(Notnum(form.foundt03_salary.value,'實收',1)){
		}else{
			form1.submit()
			/*
			$.post(
				"main4_ComNew.php",
				$("#form1").serialize(),
				function(xml){
					window.parent.location.reload();
					parent.Shadowbox.close()
				}
			)
			*/
		}

	}
	function ExRemaint(){
		$("#remain").val($("#foundt03_total").val() - $("#foundt03_salary").val())
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
                        <form id="form1" name="form1" method="post" action="main4_ComNew.php">
                        	<input id="payt02_no" name="payt02_no" type="hidden" value="<?php echo $payt02_no;?>">
                            <input id="foundt03_year" name="foundt03_year" type="hidden" value="<?php echo $sY;?>">
                            <input id="foundt03_month" name="foundt03_month" type="hidden" value="<?php echo $sM;?>">
                        	<table class="table-input">                                
                                <tr>
                                	<td></td>
                                	<td>
                                    	<table>
                                        	<tr>
                                            	<td><em>*</em>給付日</td>
                                            	<td><input id="foundt03_date" name="foundt03_date" class="datepicker" type="text"></td>                                                
                                            </tr>
                                        	<tr>
                                            	<td>應付</td>
                                            	<td>$<?php echo $totalpay;?>
                                                <input id="foundt03_total" name="foundt03_total" type="hidden" value="<?php echo $totalpay;?>">
                                                </td>                                                
                                            </tr>   
                                        	<tr>
                                            	<td><em>*</em>實收發放</td>
                                                <td>$<input id="foundt03_salary" name="foundt03_salary" type="text" size="10" max="8" onChange="ExRemaint()"></td>                                                
                                            </tr>
                                        	<tr>
                                            	<td>餘額</td>
                                            	<td><input id="remain" name="remain" type="text" size="10" max="8" disabled 
                                                value="<?php echo $totalpay;?>"></td>
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