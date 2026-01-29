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
//參數
	$GetFileCode = GetFileCode(__FILE__);
//資料庫連線
	$NewSql = new mysql();
	$MemNo = $_SESSION["MemNO"];
	if($_SESSION["MemNO"] == ""){
		header('location:/pjadmin/Maintain/login/Logout.php');
	}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<link rel="shortcut icon" href="/pjadmin/favicon.ico" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>
	var BodyHH = $(window).height();
	var BodyWW = $(window).width();
	
	$(document).ready(function(){
		//header
		var header_h = $("#header").height()
		$("#iframe-menu").css('height',BodyHH - header_h - 40)
		$("#iframe-content").css('height',BodyHH - header_h - 40)
		$("#iframe-content").css('width',BodyWW - 150)
	})
	
	$(window).resize(function(){
		BodyHH = $(window).height();
		BodyWW = $(window).width();	
		var header_h = $("#header").height()
		$("#iframe-menu").css('height',BodyHH - header_h - 40)
		$("#iframe-content").css('height',BodyHH - header_h- 40)
		$("#iframe-content").css('width',BodyWW - 150)
	})
</script>
<style>
body{
	width:100%;
	height:100%;
	overflow:hidden;
}
#iframe-content{
	overflow-y:auto;
	bottom:20px;
}
</style>
</head>

<body style="overflow:hidden;">
<div id="wrapper">
<!--======================= header =======================-->
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/header.php")?>
<!--======================= header =======================-->
    <div id="wrapperbox">
    	<ul>
        	<li id="iframe-left">
                <!--======================= sider bar left =======================-->
                <iframe id="iframe-menu" src="/pjadmin/Maintain/CommonPage/Menu.php" ></iframe>
                <!--======================= sider bar left =======================-->            
            </li>
        	<li id="iframe-right">
				<iframe id="iframe-content" src="/pjadmin/Maintain/frame/Dashbord.php"></iframe>            
            </li>            
        </ul>
        <div style="clear:both"></div>
    </div>
</div>
</body>
</html>