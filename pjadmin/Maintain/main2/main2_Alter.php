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
	
	$Sql = " 
		Select *
		From  fire_m01
		where firmm01_no = '$DataKey' 			
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$init = $NewSql -> db_fetch_array($initRun);
	$initCount = $NewSql -> db_num_rows($initRun);
	if($initCount != 1){
		header('location:' . $GetFileCode . '.php');
	}
	
	
//聯絡人
	$Sql = "
		Select firet01_no ,firet01_nm ,firet01_tel	,firet01_phone , firet01_email
		From fire_t01
		Where firem01_no = '$DataKey'	
	";
	$T01Run = $NewSql -> db_query($Sql) or die("SQL ERROR 2");

//銀行	
	$Sql = "
		Select firet02_no ,firet02_bank ,firet02_branch , firet02_acc, firet02_accnm
		From fire_t02
		Where firem01_no = '$DataKey'	
	";
	$T02Run = $NewSql -> db_query($Sql) or die("SQL ERROR 3");	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	function ChkForm(form){
		if(empty(form.firmm01_nm.value,'名稱')){
		}else if(empty(form.firmm01_nicknm.value,'簡稱')){			
		}else{
			form.submit()
		}
	}
	function RemoveT01(DataKey,url){
		$.post(
			url,
			{DataKey:DataKey},
			function (xml){
				if($('resu',xml).text() == "1"){
					window.location.reload();
				}
			}
		);
	}
	$(document).ready(function(){
		Shadowbox.init({
			overlayOpacity: 0.4,
			modal: true
		});
	});		
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
                        	<input id="Code_Id" name="Code_Id" type="hidden" value="<?php echo $GetFileCode;?>">
                        	<input id="firmm01_no" name="firmm01_no" type="hidden" value="<?php echo $init["firmm01_no"];?>">
                        	<table class="table-input">
                             	<tr>
                                	<td><em>*</em>名稱</td>
                                	<td><input type="text" id="firmm01_nm" name="firmm01_nm" size="30" maxlength="30" value="<?php echo $init["firmm01_nm"]?>"></td>
                                </tr>
                            	<tr>
                                	<td><em>*</em>簡稱</td>
                                	<td><input type="text" id="firmm01_nicknm" name="firmm01_nicknm" size="20" maxlength="20" value="<?php echo $init["firmm01_nicknm"]?>"></td>
                                </tr>                                
                            	<tr>
                                	<td>類型</td>
                                	<td>
                                    	<input type="checkbox" id="firmm01_type1" name="firmm01_type1" value="C" <?php echo ($init["firmm01_type1"]== "C"?'checked="checked"':'')?>>公司
                                    	<input type="checkbox" id="firmm01_type1" name="firmm01_type1" value="P" <?php echo ($init["firmm01_type1"]== "P"?'checked="checked"':'')?>>個人
                                    </td>
                                </tr>
                            	<tr>
                                	<td>外製</td>
                                	<td><input type="checkbox" id="firmm01_type2" name="firmm01_type2" value="Y" <?php echo ($init["firmm01_type2"]== "Y"?'checked="checked"':'')?>></td>
                                </tr>
                            	<tr>
                                	<td>統編</td>
                                	<td><input type="text" id="firmm01_id" name="firmm01_id" size="20" maxlength="20" value="<?php echo $init["firmm01_id"]?>"></td>
                                </tr>
                            	<tr>
                                	<td>地址</td>
                                	<td><input type="text" id="firmm01_addr" name="firmm01_addr" size="80" maxlength="100" value="<?php echo $init["firmm01_addr"]?>"></td>
                                </tr>
                            	<tr>
                                	<td>電話</td>
                                	<td><input type="text" id="firmm01_tel" name="firmm01_tel" size="20" maxlength="20" value="<?php echo $init["firmm01_tel"]?>"></td>
                                </tr>
                            	<tr>
                                	<td>傳真</td>
                                	<td><input type="text" id="firmm01_fax" name="firmm01_fax" size="20" maxlength="20" value="<?php echo $init["firmm01_fax"]?>"></td>
                                </tr> 
                                <tr>
                                	<td></td>
                                    <td class="hr"></td>
                                </tr>                              
                                <tr>
                                	<td><a title="新增聯繫人" id="AddContact" href="main2_T_Add.php?DataKey=<?php echo $init["firmm01_no"];?>" rel="shadowbox;width=600;height=200" ><i class="add"></i></a></td>
                                	<td>聯繫人</td>                                    
                                </tr>
                                <?php while($T01 = $NewSql -> db_fetch_array($T01Run)){?>
                                	<tr>
                                    	<td>
                                            <a title="聯繫人" href="main2_T_Add.php?DataKey=<?php echo $init["firmm01_no"];?>&Key=<?php echo $T01["firet01_no"];?>" rel="shadowbox;width=600;height=200" ><i class="pen"></i></a><br>
                                            <a class="remove" onClick="RemoveT01('<?php echo $T01["firet01_no"];?>','main2_T_Delete.php')"></a>
                                        </td>
                                    	<td style="line-height:25px;">
                                            姓名：<?php echo $T01["firet01_nm"];?>
                                            <br>公司電話：<?php echo $T01["firet01_tel"];?>
                                            手機：<?php echo $T01["firet01_phone"];?>
                                            <br>E-Mail：<?php echo $T01["firet01_email"];?>
                                        </td>                                        
                                    </tr>
                                <?php }?>
                                
                                <tr>
                                	<td><a title="新增帳戶資訊" id="AddContact" href="main2_T2_Add.php?DataKey=<?php echo $init["firmm01_no"];?>" rel="shadowbox;width=600;height=200" ><i class="add"></i></a></td>
                                	<td>帳戶資訊</td>                                    
                                </tr> 
                                <?php while($T2 = $NewSql -> db_fetch_array($T02Run)){?>
                                	<tr>
                                    	<td>
                                            <a title="聯繫人" href="main2_T2_Add.php?DataKey=<?php echo $init["firmm01_no"];?>&Key=<?php echo $T2["firet02_no"];?>" rel="shadowbox;width=600;height=200" ><i class="pen"></i></a><br>
                                            <a class="remove" onClick="RemoveT01('<?php echo $T2["firet02_no"];?>','main2_T2_Delete.php')"></a>
                                        </td>
                                    	<td style="line-height:25px;">
                                            銀行：<?php echo $T2["firet02_bank"];?>
                                            <br>分行：<?php echo $T2["firet02_branch"];?>
                                            戶名：<?php echo $T2["firet02_acc"];?>
                                            <br>帳號：<?php echo $T2["firet02_accnm"];?>
                                        </td>                                        
                                    </tr>
                                <?php }?>  
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