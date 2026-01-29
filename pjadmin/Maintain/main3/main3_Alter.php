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
	$LevelMem = LevelMEM($NewSql);
	$GetFileCode = GetFileCode(__FILE__);
	$GetFileType = GetFileType(__FILE__);	
	$GetTitle = GetTitle($NewSql,$GetFileCode);
	$DataKey = xRequest("DataKey");
//default
	switch ($LevelMem){
		case '1':
		case '2':
			$editLevel = true;
			break;
		default:
			$editLevel = false;
			break;
	}
	
		
	$Sql = " Select *
			From mem_m01
			where memm01_no = '$DataKey' 
	";
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
		where MUG01_SHOW = 'Y'
	";
	$mug01Run = $NewSql -> db_query($Sql) or die("SQL ERROR");	
	$mug01Count = $NewSql -> db_num_rows($mug01Run);
	
//設計主管
	$Sql = "
		select memm01_no, memm01_nick
		from mem_m01
		where MUG01_NO in ('2','3')	
		order by MUG01_NO 
	";
	$mem01Run = $NewSql -> db_query($Sql) or die("SQL ERROR 2");

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	$(function() {
		$(".datepicker").datepicker({
			dateFormat: 'yymmdd'
		});
		
		$(".chosen-select").chosen({no_results_text: "Oops, nothing found!", allow_single_deselect:true}); 
		
		ChangeLevel($("#MUG01_NO").val());
	  });
	function ChangeLevel(key){
		if(key == '4'){
			$('#memm01_uplevel_chosen').show();
			$(".chosen-select").chosen({no_results_text: "Oops, nothing found!"}); 	
		}else{
			$('#memm01_uplevel_chosen').hide();		
			$('#memm01_uplevel').removeAttr("selected");
		}	
	}
	
	function ChkForm(form){
		if(empty(form.MUG01_NO.value,'層級')){
		}else if(empty(form.memm01_open.value,'是否在職')){
		}else if(empty(form.memm01_loginid.value,'帳號')){
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
            <li><?php echo $GetTitle . $GetFileType;?></li>
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
                                	<td><em>*</em>層級</td>
                                	<td>
                                    	<select id="MUG01_NO" name="MUG01_NO" class="chosen-select w200" data-placeholder="..." onChange="ChangeLevel(this.value)" <?php echo ($editLevel?"":" disabled")?>>
                                        	<option value=""></option>
                                        <?php 
										while($mug01 = $NewSql -> db_fetch_array($mug01Run)){?>
                                            <option value="<?php echo $mug01["MUG01_NO"];?>"<?php echo ($init["MUG01_NO"] == $mug01["MUG01_NO"]?" selected":"")?>><?php echo $mug01["MUG01_NAME"];?></option>
										<?php }?>                                                                              
                                        </select>
                                    	<select id="memm01_uplevel" name="memm01_uplevel" class="chosen-select w200" data-placeholder="..." <?php echo ($editLevel?"":" disabled")?>>
                                        	<option value="">請選擇</option>
                                        <?php 
										while($mem01 = $NewSql -> db_fetch_array($mem01Run)){?>
                                            <option value="<?php echo $mem01["memm01_no"];?>" <?php echo ($init["memm01_uplevel"] == $mem01["memm01_no"]?" selected":"")?> ><?php echo $mem01["memm01_nick"];?></option>
										<?php }?>                                                                              
                                        </select>                                    
                                        
                                    </td>
                                </tr>
                            	<tr>
                                	<td>編號</td>
                                	<td><input type="text" id="memm01_numid" name="memm01_numid" size="20" maxlength="20" value="<?php echo $init["memm01_numid"];?>" ></td>
                                </tr>
                            	<tr>
                                	<td><em>*</em>姓名</td>
                                	<td>
                                    	<input type="text" id="memm01_nm" name="memm01_nm" size="20" maxlength="20" value="<?php echo $init["memm01_nm"];?>" >
                                        簡稱
                                        <input type="text" id="memm01_nick" name="memm01_nick" size="20" maxlength="20" value="<?php echo $init["memm01_nick"];?>" >
                                    </td>
                                </tr>
                            	<tr>
                                	<td><em>*</em>是否在職</td>
                                	<td>
                                    	<select id="memm01_open" name="memm01_open" class="chosen-select w200" data-placeholder="..." >
                                        	<option value=""></option>
											<option value="Y" <?php echo ($init["memm01_open"] == "Y"?" selected":"")?>>是</option>
											<option value="N" <?php echo ($init["memm01_open"] == "N"?" selected":"")?>>否</option>
                                        </select>
                                    </td>
                                </tr>                                
                            	<tr>
                                	<td><em>*</em>登入帳號</td>
                                	<td><input type="text" id="memm01_loginid" name="memm01_loginid" size="80" maxlength="100" value="<?php echo $init["memm01_loginid"];?>" ></td>
                                </tr>
                             	<tr>
                                	<td><em>*</em>密碼</td>
                                	<td><input type="text" id="memm01_pwd" name="memm01_pwd" size="20" maxlength="20" value="<?php echo $init["memm01_pwd"];?>" ></td>
                                </tr>
                             	<tr>
                                	<td>身分證字號</td>
                                	<td><input type="text" id="memm01_id" name="memm01_id" size="20" maxlength="20" value="<?php echo $init["memm01_id"];?>" ></td>
                                </tr>                                
                             	<tr>
                                	<td>出生年月日</td>
                                	<td><input type="text" class="datepicker" id="memm01_birth" name="memm01_birth" size="8" maxlength="8" value="<?php echo $init["memm01_birth"];?>" ></td>
                                </tr>
                             	<tr>
                                	<td>Email</td>
                                	<td><input type="text" id="memm01_email" name="memm01_email" size="80" maxlength="100" value="<?php echo $init["memm01_email"];?>" ></td>
                                </tr> 
                             	<tr>
                                	<td>備份Email</td>
                                	<td><input type="text" id="memm01_bkemail" name="memm01_bkemail" size="80" maxlength="100" value="<?php echo $init["memm01_bkemail"];?>" ></td>
                                </tr>   
                             	<tr>
                                	<td>電話 市話 手機</td>
                                	<td><input type="text" id="memm01_phone" name="memm01_phone" size="20" maxlength="20" value="<?php echo $init["memm01_phone"];?>" ></td>
                                </tr> 
                             	<tr>
                                	<td>銀行帳戶資訊</td>
                                	<td>
                                    	<input type="text" id="memm01_bank" name="memm01_bank" size="20" maxlength="20" placeholder="銀行" value="<?php echo $init["memm01_bank"];?>" >
                                        分行
                                        <input type="text" id="memm01_bankbrand" name="memm01_bankbrand" size="20" maxlength="20" placeholder="分行" value="<?php echo $init["memm01_bankbrand"];?>" >
                                        帳號
                                        <input type="text"  size="20" id="memm01_bankacc" name="memm01_bankacc" maxlength="20" placeholder="帳號" value="<?php echo $init["memm01_bankacc"];?>" >
                                    </td>
                                </tr> 
                             	<tr>
                                	<td>緊急聯絡人/電話</td>
                                	<td>
                                    	<input type="text" id="memm01_emd" name="memm01_emd" size="10" maxlength="10" placeholder="姓名" value="<?php echo $init["memm01_emd"];?>" >
                                        電話
                                        <input type="text" id="memm01_emdphone" name="memm01_emdphone" size="20" maxlength="20" placeholder="連絡電話" value="<?php echo $init["memm01_emdphone"];?>" >
                                    </td>
                                </tr>                               
								<tr>
                                	<td>到職日</td>
                                	<td>
                                    	<input type="text" class="datepicker" id="ondate" name="ondate" size="10" maxlength="10" placeholder="" autocomplete="off" value="<?php echo $init["ondate"] == '0000-00-00' ? "" : str_replace('-','',$init["ondate"]);?>" >
                                        離職日
                                        <input type="text" class="datepicker" id="offdate" name="offdate" size="20" maxlength="20" placeholder="" autocomplete="off" value="<?php echo $init["offdate"] == '0000-00-00' ? "" : str_replace('-','',$init["offdate"]);?>" >
                                    </td>
                                </tr> 
								<tr>
                                	<td>停權日</td>
                                	<td>
                                    	<input type="text" class="datepicker" id="lockdate" name="lockdate" size="10" maxlength="10" placeholder="" autocomplete="off" value="<?php echo $init["lockdate"] == '0000-00-00' ? "" : str_replace('-','',$init["lockdate"]);?>" >
                                    </td>
                                </tr>   
								</tr> 
								<tr>
                                	<td>最後登入時間</td>
                                	<td>
                                    	<?php echo $init["lastlogin"];?>
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