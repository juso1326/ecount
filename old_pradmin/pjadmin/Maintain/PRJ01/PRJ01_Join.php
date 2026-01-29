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
//員工
	$Sql = "
		Select memm01_no, memm01_nick
		From mem_m01 m01
		Left join mug_01 on m01.MUG01_NO = mug_01.MUG01_NO
		where MUG01_SHOW = 'Y'
		order by m01.MUG01_NO
	";
	$memRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
	$memCount = $NewSql -> db_num_rows($memRun);
	
//任務
	$Sql = "
		Select C01_no, C01_nm
		From code_c01	
	";
	$C01Run = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
	$C01Count = $NewSql -> db_num_rows($C01Run);	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	function ChkForm(form){
		if(empty(form.memm01_no.value,'成員')){
		}else if(empty(form.C01_no.value,'專案任務')){			
		}else{
			$.post(
				"PRJ01_JoinNew.php",
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
                        <form id="form1" name="form1" method="post">
                        <input id="prjm01_no" name="prjm01_no" type="hidden" value="<?php echo $DataKey;?>">
                        <input id="prjt02_type" name="prjt02_type" type="hidden" value="M">
                        
                        	<table class="table-input">
                                <tr>
                                	<td><em>*</em> 成員</td>
                                	<td>
										<select id="memm01_no" name="memm01_no" class="chosen-select w200" data-placeholder="...">
											<option value=""></option>
											<?php 
												while($mem = $NewSql -> db_fetch_array($memRun)){
													echo '<option value="' . $mem["memm01_no"] . '">' . $mem["memm01_nick"] . '</option>';		
												}														
											?>
										</select>                                     
                                    </td>
                                </tr>
                                <tr>
                                	<td><em>*</em> 專案任務</td>
                                	<td>
										<select id="C01_no" name="C01_no" class="chosen-select w200" data-placeholder="...">
											<option value=""></option>
											<?php 
												while($C01 = $NewSql -> db_fetch_array($C01Run)){
													echo '<option value="' . $C01["C01_no"] . '">' . $C01["C01_nm"] . '</option>';		
												}														
											?>
										</select>                                     
                                    </td>
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