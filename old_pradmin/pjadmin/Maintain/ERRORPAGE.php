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
//資料庫連線
	$NewSql = new mysql();	
//參數
	$SysFileId = xRequest("SysFileId");
	$Error = xRequest("Error");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
</head>
<body>
<div id="wrapper">
<div class="side-content">
	<form id="KeyForm" name="KeyForm" method="post">
    	<input id="DataKey" name="DataKey" type="hidden">
    </form>
	<ul class="breadcrumb">
    	<li></li>
    	<li></li>        
    </ul>
	<div class="box">
        <div class="box-content">
            <div class="box-head">
            	<ul class="btn">
                	<li><input class="gray" type="button" value="Return" onClick="window.location='/Maintain/<?php echo $SysFileId . '/' . $SysFileId?>.php'" ></li>
                </ul>
            </div>        
            <div><?php echo $Error;?></div>
        </div>
    </div>
</div>

</div>
</body>
</html>