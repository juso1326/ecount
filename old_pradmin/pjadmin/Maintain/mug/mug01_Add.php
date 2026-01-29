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
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	function ChkForm(form){
		if(empty(form.MUG01_NAME.value,'群組名稱')){
		}else if(empty(form.MUG01_OPEN.value,'是否開啟')){
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
                        <form id="form1" name="form1" method="post" action="<?php echo $GetFileCode;?>_AddNew.php">
                        	<table class="table-input">
                            	<tr>
                                	<td><em>*</em>群組名稱</td>
                                	<td><input type="text" id="MUG01_NAME" name="MUG01_NAME" size="30" maxlength="50"></td>
                                </tr>
                            	<tr>
                                	<td><em>*</em>是否開啟</td>
                                	<td>
                                    	<Select id="MUG01_OPEN" name="MUG01_OPEN">
                                            <option value="Y">是</option>
                                            <option value="N">否</option>
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