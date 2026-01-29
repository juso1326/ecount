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
	$PageCon = xRequest("PageCon");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	function ChkForm(form){
		if(empty(form.comm01_nm.value,'名稱')){
		}else if(empty(form.comm01_nicknm.value,'簡稱')){
//		}else if(empty(form.comm01_type1.value,'類型')){			
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
                        <li><input class="gray" type="button" value="Return" onClick="window.location='<?php echo $GetFileCode;?>.php?<?php echo $PageCon;?>'" ></li>
                    </ul>
                </div>
                
                <div class="box-data">
                    	<div class="input-Group">
                        <form id="form1" name="form1" method="post" action="<?php echo $GetFileCode;?>_AddNew.php">
                        <input id="Code_Id" name="Code_Id" type="hidden" value="<?php echo $GetFileCode;?>">
                        <input id="PageCon" name="PageCon" type="hidden" value="<?php echo $PageCon;?>">
                        	<table class="table-input">
                            	<tr>
                                	<td><em>*</em>名稱</td>
                                	<td><input type="text" id="comm01_nm" name="comm01_nm" size="30" maxlength="30"></td>
                                </tr>
                            	<tr>
                                	<td><em>*</em>簡稱</td>
                                	<td><input type="text" id="comm01_nicknm" name="comm01_nicknm" size="20" maxlength="20"></td>
                                </tr>
                            	<tr>
                                	<td><em>*</em>類型</td>
                                	<td>
                                    	<input type="radio" id="comm01_type1" name="comm01_type1" value="C" >公司
                                    	<input type="radio" id="comm01_type1" name="comm01_type1" value="P" >個人
                                    </td>
                                </tr>
                            	<tr>
                                	<td></td>
                                	<td>
                                    	<input type="checkbox" id="comm01_type3" name="comm01_type3" value="Y" >客戶
                                        <input type="checkbox" id="comm01_type2" name="comm01_type2" value="Y" >外製
                                    </td>
                                </tr>                                                               
                            	<tr>
                                	<td>統編</td>
                                	<td><input type="text" id="comm01_quid" name="comm01_quid" size="20" maxlength="20"></td>
                                </tr>
                            	<tr>
                                	<td>地址</td>
                                	<td><input type="text" id="comm01_addr" name="comm01_addr" size="80" maxlength="100"></td>
                                </tr>
                            	<tr>
                                	<td>電話</td>
                                	<td><input type="text" id="comm01_tel" name="comm01_tel" size="20" maxlength="20"></td>
                                </tr>
                            	<tr>
                                	<td>傳真</td>
                                	<td><input type="text" id="comm01_fax" name="comm01_fax" size="20" maxlength="20"></td>
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