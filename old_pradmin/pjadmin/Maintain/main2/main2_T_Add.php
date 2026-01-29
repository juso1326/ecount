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
		select firet01_nm ,firet01_tel , firet01_phone, firet01_email
		from fire_t01
		where firet01_no = '$Key'	
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$init = $NewSql -> db_fetch_array($initRun);
	
	$nm = $init["firet01_nm"];
	$tel = $init["firet01_tel"];
	$phone = $init["firet01_phone"];
	$email = $init["firet01_email"];
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
		if(empty(form.firet01_nm.value,'姓名')){
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
                        <form id="form1" name="form1" method="post" action="main2_T_AddNew.php">
                        <input id="firem01_no" name="firem01_no" type="hidden" value="<?php echo $DataKey;?>">
                        <input id="firet01_no" name="firet01_no" type="hidden" value="<?php echo $Key;?>">
                        	<table class="table-input">
                                <tr>
                                	<td><em>*</em> 姓名</td>
                                	<td><input type="text" id="firet01_nm" name="firet01_nm" size="20" maxlength="20" value="<?php echo $nm;?>"></td>
                                </tr>
                                <tr>
                                	<td>公司電話</td>
                                	<td>
                                    <input type="text" id="firet01_tel" name="firet01_tel" size="20" maxlength="20" value="<?php echo $tel;?>">
                                    手機
                                    <input type="text" id="firet01_phone" name="firet01_phone" size="20" maxlength="20" value="<?php echo $phone;?>">
                                    </td>
                                </tr>
                                <tr>
                                	<td>EMAIL</td>
                                	<td><input type="text" id="firet01_email" name="firet01_email" size="60" maxlength="100" value="<?php echo $email;?>"></td>                                    
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