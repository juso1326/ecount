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
//參數
	$GetFileCode = GetFileCode(__FILE__);
	$GetFileType = GetFileType(__FILE__);

	$DataKey = xRequest("DataKey");
//資料庫連線
	$NewSql = new mysql();	
	
	$Sql = " Select *
			From mem_m01
			where memm01_no = '$DataKey' ";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$init = $NewSql -> db_fetch_array($initRun);
	$initCount = $NewSql -> db_num_rows($initRun);
	
	if($initCount != 1){
		header('location:' . $GetFileCode . '.php');
	}
		
//群組
	$Sql = " 
		Select MUG01_NO, MUG01_NAME
		From mug_01	
	";
	$mug01Run = $NewSql -> db_query($Sql) or die("SQL ERROR");	
	$mug01Count = $NewSql -> db_num_rows($mug01Run);	

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	function ChkForm(form){
		//if(empty(form.memm01_numid.value,'員工編號')){
		if(empty(form.memm01_nm.value,'姓名')){
		}else if(empty(form.memm01_nick.value,'暱稱')){
		}else if(empty(form.memm01_loginid.value,'登入帳號')){
		}else if(empty(form.memm01_pwd.value,'密碼')){									
		}else{
			form.submit()
		}
	}
</script>
</head>

<body>
<div id="wrapper">

    <div class="side-content">
        <ul class="breadcrumb">
            <li>帳號管理<?php echo $GetFileType;?></li>
            <li></li>        
        </ul>
        
        <div class="box">
            <div class="box-content">
                <div class="box-head">
                    <ul class="btn">
                        <li><input class="blue" type="button" value="Save" onClick="ChkForm(form1)"></li>
                        <li><input class="gray" type="button" value="Return" onClick="window.location='<?php echo $GetFileCode;?>.php'" ></li>
                    </ul>
                </div>
                
                <div class="box-data">
                    	<div class="input-Group">
                        <form id="form1" name="form1" method="post" action="<?php echo $GetFileCode;?>_AlterNew.php">
                        	<input id="memm01_no" name="memm01_no" type="hidden" value="<?php echo $init["memm01_no"];?>">                        
                        	<table class="table-input">
                            	<tr>
                                	<td><em>*</em>員工編號</td>
                                	<td><input type="text" id="memm01_numid" name="memm01_numid" size="30" maxlength="20" value="<?php echo $init["memm01_numid"];?>"></td>
                                </tr>                            
                            	<tr>
                                	<td><em>*</em>姓名</td>
                                	<td><input type="text" id="memm01_nm" name="memm01_nm" size="30" maxlength="20" value="<?php echo $init["memm01_nm"];?>"></td>
                                </tr>
                            	<tr>
                                	<td><em>*</em>暱稱</td>
                                	<td><input type="text" id="memm01_nick" name="memm01_nick" size="30" maxlength="20" value="<?php echo $init["memm01_nick"];?>"></td>
                                </tr>
                            	<tr>
                                	<td><em>*</em>登入帳號</td>
                                	<td><input type="text" id="memm01_loginid" name="memm01_loginid" size="80" maxlength="100" value="<?php echo $init["memm01_loginid"];?>"></td>
                                </tr>
                            	<tr>
                                	<td><em>*</em>密碼</td>
                                	<td><input type="text" id="memm01_pwd" name="memm01_pwd" size="30" maxlength="20" value="<?php echo $init["memm01_pwd"];?>"/></td>
                                </tr>                              
                            	<tr>
                                	<td><em>*</em>是否在職</td>
                                	<td>
                                    	<Select id="memm01_open" name="memm01_open">
                                            <option value="Y"<?php echo ($init["memm01_open"] == "Y")?'selected':'';?> >是</option>
                                            <option value="N"<?php echo ($init["memm01_open"] == "N")?'selected':'';?> >否</option>
                                        </Select>
                                    </td>
                                </tr>
                            	<tr>
                                	<td><em>*</em>群組</td>
                                	<td>
                                    	<Select id="MUG01_NO" name="MUG01_NO">
                                        	<option value=""></option>
                                        <?php
										 while($mug01 = $NewSql -> db_fetch_array($mug01Run)){
										?>
                                            <option value="<?php echo $mug01["MUG01_NO"];?>"<?php echo ($init["MUG01_NO"] == $mug01["MUG01_NO"])?' selected':'';?> ><?php echo $mug01["MUG01_NAME"];?></option>
										<?php }?>
                                        </Select>
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