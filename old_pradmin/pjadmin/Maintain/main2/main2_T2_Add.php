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
	$DataKey = xRequest("DataKey");
	$Key = xRequest("Key");
	
	$Sql = "
		select firet02_bank ,firet02_branch , firet02_acc, firet02_accnm
		from fire_t02
		where firet02_no = '$Key'	
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$init = $NewSql -> db_fetch_array($initRun);
	
	$bank = $init["firet02_bank"];
	$branch = $init["firet02_branch"];
	$acc = $init["firet02_acc"];
	$accnm = $init["firet02_accnm"];
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
		if(empty(form.firet02_bank.value,'銀行')){
		}else if(empty(form.firet02_acc.value,'戶名')){
		}else if(empty(form.firet02_accnm.value,'帳號')){
		}else{
			$.post(
				$("#form1").attr("action"),
				$("#form1").serialize(),
				function(xml){
					if($('resu',xml).text() == "1"){
						window.parent.location.reload();
						parent.Shadowbox.close()						
					}
				}
			)
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
                        <form id="form1" name="form1" method="post" action="main2_T2_AddNew.php">
                        <input id="firem01_no" name="firem01_no" type="hidden" value="<?php echo $DataKey;?>">
                        <input id="firet02_no" name="firet02_no" type="hidden" value="<?php echo $Key;?>">
                        	<table class="table-input">
                                <tr>
                                	<td><em>*</em>銀行</td>
                                	<td>
                                    <input type="text" id="firet02_bank" name="firet02_bank" size="20" maxlength="30" value="<?php echo $bank;?>">
                                    分行
                                    <input type="text" id="firet02_branch" name="firet02_branch" size="20" maxlength="30" value="<?php echo $branch;?>">
                                    </td>
                                </tr>
                                <tr>
                                	<td><em>*</em> 戶名</td>
                                	<td><input type="text" id="firet02_acc" name="firet02_acc" size="30" maxlength="30" value="<?php echo $acc;?>"></td>
                                </tr>                                
                                <tr>
                                	<td><em>*</em>帳號</td>
                                	<td><input type="text" id="firet02_accnm" name="firet02_accnm" size="30" maxlength="30" value="<?php echo $accnm;?>"></td>                                    
                                </tr> 
								<tr>
                                	<td></td>
                                	<td>
                                    	<input class="blue" type="button" value="Save" onClick="ChkForm(form1)">
                                    </td>                                    
                                </tr>                                                                                                                                                            
                            </table>
						</form>
                        </div>
                </div>  
</div>
</body>
</html>