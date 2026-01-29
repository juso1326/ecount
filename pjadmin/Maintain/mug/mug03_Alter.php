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
	$DataKey = xRequest("DataKey");
//資料庫連線
	$NewSql = new mysql();	
	
	$Sql = " Select *
			From mug_03
			where MUG03_NO = '$DataKey' ";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
	$init = $NewSql -> db_fetch_array($initRun);
	$initCount = $NewSql -> db_num_rows($initRun);
	
	$Sql = "
		Select MUG03_NO,MUG03_NM
		From mug_03
		where MUG03_PATH = ''
	";
	$LevelRun = $NewSql -> db_query($Sql) or die("SQL ERROR 2");
		
	if($initCount != 1){
		header('location:' . $GetFileCode . '.php');
	}
//	$NewSql -> db_close();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	function ChkForm(form){
		if(empty(form.MUG03_CODE.value,'程式代碼')){
		}else if(empty(form.MUG03_NM.value,'程式名稱')){
		//}else if(empty(form.MUG03_PATH.value,'程式路徑')){			
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
            <li>使用者管理 - 修改</li>
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
                        	<input id="MUG03_NO" name="MUG03_NO" type="hidden" value="<?php echo $init["MUG03_NO"];?>">
                        	<table class="table-input">
                            	<tr>
                                	<td><em>*</em>程式代碼</td>
                                	<td><input type="text" id="MUG03_CODE" name="MUG03_CODE" size="20" maxlength="30" value="<?php echo $init["MUG03_CODE"];?>"></td>
                                </tr>
                            	<tr>
                                	<td>檔案類別</td>
                                	<td>
                                    	<Select id="MUG03_TYPE" name="MUG03_TYPE">
                                            <option value="D"<?php echo ($init["MUG03_TYPE"] == "D")?'selected':'';?> >D資料夾</option>
                                            <option value="F"<?php echo ($init["MUG03_TYPE"] == "F")?'selected':'';?> >F檔案</option>
                                        </Select>
                                    </td>
                                </tr> 
								<tr>
                                	<td>檔案階層</td>
                                	<td>
                                    	<Select id="MUG03_LEVEL" name="MUG03_LEVEL">
                                        	<option value=""></option>
											<?php while($Level = $NewSql -> db_fetch_array($LevelRun)){?>
                                            <option value="<?php echo $Level["MUG03_NO"]?>"<?php echo ($Level["MUG03_NO"] == $init["MUG03_LEVEL"])?'selected':'';?> ><?php echo $Level["MUG03_NM"]?></option>
                                            <?php }?>
                                        </Select>
                                    </td>
                                </tr>                                                               
                                
                            	<tr>
                                	<td><em>*</em>程式名稱</td>
                                	<td><input type="text" id="MUG03_NM" name="MUG03_NM" size="20" maxlength="30" value="<?php echo $init["MUG03_NM"];?>"></td>
                                </tr>
                            	<tr>
                                	<td><em>*</em>程式路徑</td>
                                	<td><input type="text" id="MUG03_PATH" name="MUG03_PATH" size="80" maxlength="100"  value="<?php echo $init["MUG03_PATH"];?>"></td>
                                </tr>
                            	<tr>
                                	<td>程式圖示</td>
                                	<td><input type="text" id="MUG03_IMG" name="MUG03_IMG" size="20" maxlength="20" value="<?php echo $init["MUG03_IMG"];?>"</td>
                                </tr>  
                            	<tr>
                                	<td>排序</td>
                                	<td><input type="text" id="MUG03_SORT" name="MUG03_SORT" size="5" maxlength="5" value="<?php echo $init["MUG03_SORT"];?>" ></td>
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