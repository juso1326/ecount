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
	$PageCon = xRequest("PageCon");
	$DeleteAble = true;
	
	$Sql = " 
		Select *
		From  com_m01
		where comm01_no = '$DataKey' 			
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
	$init = $NewSql -> db_fetch_array($initRun);
	$initCount = $NewSql -> db_num_rows($initRun);
	if($initCount != 1){
		header('location:' . $GetFileCode . '.php');
	}
	
//聯絡人
	$Sql = "
		Select comt01_no ,comt01_nm ,comt01_tel	,comt01_phone , comt01_email
		From com_t01
		Where comm01_no = '$DataKey'	
	";
	$T01Run = $NewSql -> db_query($Sql) or die("SQL ERROR 2");

//銀行	
	$Sql = "
		Select comt02_no ,comt02_bank ,comt02_branch , comt02_acc, comt02_accnm,comt02_bankId
		From com_t02
		Where comm01_no = '$DataKey'	
	";
	$T02Run = $NewSql -> db_query($Sql) or die("SQL ERROR 3");
	
//檢查專案是否已經使用
	$Sql = "
		Select count(*)
		From prj_m01
		where comm01_no = '$DataKey'
	";
	
	$UseRun = $NewSql -> db_query($Sql) or die("SQL ERROR 4");
	$Use = $NewSql -> db_result($UseRun);	
	
	if($Use > 0 ){ $DeleteAble = false;}
	
	$Sql = "
		Select count(*)
		From pay_m01
		where  comm01_no = '$DataKey' or firmm01_no = '$DataKey'	
	";
	$UseRun = $NewSql -> db_query($Sql) or die("SQL ERROR 4");
	$Use = $NewSql -> db_result($UseRun);	
	
	if($Use > 0 ){ $DeleteAble = false;}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	$(document).ready(function(){
		Shadowbox.init({
			overlayOpacity: 0.4,
			modal: true
		});
	});	
	function ChkForm(form){
		if(empty(form.comm01_nm.value,'名稱')){
		}else if(empty(form.comm01_nicknm.value,'簡稱')){	
//		}else if(empty(form.comm01_type1.value,'類型')){					
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
	function EventClick(event){
		switch (event){
			case 'Del':
				if(confirm("確定刪除此筆資料嗎?")){
					if($("#comm01_no").val() == ""){
						return false;
						break;
					}else{
						$('#form1').prop('action','<?php echo $GetFileCode . '_Delete.php'?>').submit();
					}
				}
				break;
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
                        <li><input class="gray" type="button" value="Return" onClick="window.location='<?php echo $GetFileCode;?>.php?<?php echo $PageCon;?>'" ></li>
                        <?php if($DeleteAble){?><li><input class="red" type="button" value="Delete" onClick="EventClick('Del')"></li><?php }?>
                    </ul>
                </div>
                
                <div class="box-data">
                    	<div class="input-Group">
                        <form id="form1" name="form1" method="post" action="<?php echo $GetFileCode;?>_AlterNew.php">
                        	<input id="Code_Id" name="Code_Id" type="hidden" value="<?php echo $GetFileCode;?>">
                        	<input id="comm01_no" name="comm01_no" type="hidden" value="<?php echo $init["comm01_no"];?>">
                            <input id="PageCon" name="PageCon" type="hidden" value="<?php echo $PageCon;?>">
                        	<table class="table-input">

                            	<tr>
                                	<td><em>*</em>名稱</td>
                                	<td><input type="text" id="comm01_nm" name="comm01_nm" size="30" maxlength="30" value="<?php echo $init["comm01_nm"]?>"></td>
                                </tr>
                            	<tr>
                                	<td><em>*</em>簡稱</td>
                                	<td><input type="text" id="comm01_nicknm" name="comm01_nicknm" size="20" maxlength="20" value="<?php echo $init["comm01_nicknm"]?>"></td>
                                </tr>
                            	<tr>
                                	<td><em>*</em>類型</td>
                                	<td>
                                    	<input type="radio" id="comm01_type1" name="comm01_type1" value="C" <?php echo ($init["comm01_type1"]== "C"?'checked="checked"':'')?>>公司
                                    	<input type="radio" id="comm01_type1" name="comm01_type1" value="P" <?php echo ($init["comm01_type1"]== "P"?'checked="checked"':'')?>>個人
                                    </td>
                                </tr>
                            	<tr>
                                	<td></td>
                                	<td>
                                    	<input type="checkbox" id="comm01_type3" name="comm01_type3" value="Y" <?php echo ($init["comm01_type3"]== "Y"?'checked="checked"':'')?>>客戶
                                        <input type="checkbox" id="comm01_type2" name="comm01_type2" value="Y" <?php echo ($init["comm01_type2"]== "Y"?'checked="checked"':'')?>>外製
                                        </td>
                                </tr>                                                               
                            	<tr>
                                	<td>統編</td>
                                	<td><input type="text" id="comm01_quid" name="comm01_quid" size="20" maxlength="20" value="<?php echo $init["comm01_quid"]?>"></td>
                                </tr>
                            	<tr>
                                	<td>地址</td>
                                	<td><input type="text" id="comm01_addr" name="comm01_addr" size="80" maxlength="100" value="<?php echo $init["comm01_addr"]?>"></td>
                                </tr>
                            	<tr>
                                	<td>電話</td>
                                	<td><input type="text" id="comm01_tel" name="comm01_tel" size="20" maxlength="20" value="<?php echo $init["comm01_tel"]?>"></td>
                                </tr>
                            	<tr>
                                	<td>傳真</td>
                                	<td><input type="text" id="comm01_fax" name="comm01_fax" size="20" maxlength="20" value="<?php echo $init["comm01_fax"]?>"></td>
                                </tr>
                                <tr>
                                	<td><a title="新增聯繫人" id="AddContact" href="main1_T_Add.php?DataKey=<?php echo $init["comm01_no"];?>" rel="shadowbox;width=600;height=200" ><i class="add"></i></a></td>
                                	<td>聯繫人</td>                                    
                                </tr>
                                <?php while($T01 = $NewSql -> db_fetch_array($T01Run)){?>
                                	<tr>
                                    	<td>
                                            <a title="聯繫人" href="main1_T_Add.php?DataKey=<?php echo $init["comm01_no"];?>&Key=<?php echo $T01["comt01_no"];?>" rel="shadowbox;width=600;height=200" ><i class="pen"></i></a><br>
                                            <a class="remove" onClick="RemoveT01('<?php echo $T01["comt01_no"];?>','main1_T_Delete.php')"></a>
                                        </td>
                                    	<td style="line-height:25px;">
                                            姓名：<?php echo $T01["comt01_nm"];?>
                                            <br>公司電話：<?php echo $T01["comt01_tel"];?>
                                            手機：<?php echo $T01["comt01_phone"];?>
                                            <br>E-Mail：<?php echo $T01["comt01_email"];?>
                                        </td>                                        
                                    </tr>
                                <?php }?>
                                
                                <tr>
                                	<td><a title="新增帳戶資訊" id="AddContact" href="main1_T2_Add.php?DataKey=<?php echo $init["comm01_no"];?>" rel="shadowbox;width=600;height=200" ><i class="add"></i></a></td>
                                	<td>帳戶資訊</td>                                    
                                </tr> 
                                <?php while($T2 = $NewSql -> db_fetch_array($T02Run)){?>
                                	<tr>
                                    	<td>
                                            <a title="聯繫人" href="main1_T2_Add.php?DataKey=<?php echo $init["comm01_no"];?>&Key=<?php echo $T2["comt02_no"];?>" rel="shadowbox;width=600;height=200" ><i class="pen"></i></a><br>
                                            <a class="remove" onClick="RemoveT01('<?php echo $T2["comt02_no"];?>','main1_T2_Delete.php')"></a>
                                        </td>
                                    	<td style="line-height:25px;">
                                            <span class="bold">銀行：</span><?php echo $T2["comt02_bank"] . '	' . $T2["comt02_bankId"];?>
                                            分行：<?php echo $T2["comt02_branch"];?>	
                                            <br>帳號：<?php 	preg_match_all('/\d+/',$T2["comt02_acc"],$acc);$acc = join('',$acc[0]);echo $acc;?>
                                            <br>戶名：<?php echo $T2["comt02_accnm"];?>
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