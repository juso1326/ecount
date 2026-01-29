<?php
//*****************************************************************************************
//		撰寫日期：
//		程式功能：
//		使用參數：
//*****************************************************************************************
	header ('Content-Type: text/html; charset=utf-8');
//函式庫
	include_once($_SERVER['DOCUMENT_ROOT'] . "/config.ini.php");
//	$_SESSION["MemNO"] = "";
	$ErrorMsg = xRequest("ErrorMsg");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="/pjadmin/Maintain/Css/Login.css" >
<link rel="shortcut icon" href="/pjadmin/favicon.ico" />
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	function ChkForm(form){
		if(empty(form.TEXT01.value,'帳號')){
		}else if(empty(form.TEXT02.value,'密碼')){
		}else{
			form.submit()
		}
	}
	function keyboard(event){
		if(event.keyCode == '13'){
			$('#submitbtn').click();
		}
	}
</script>
</head>

<body>
    <div id="LoginPage">
    	<div class="Loginblock">
        <form id="form1" name="form1" method="post" action="Login_active.php">
        	<div class="icon"></div>
            <h2>Login to your account</h2>
            <ul>
            	<li>
                    <i class="user_b"></i>
                    <input id="TEXT01" name="TEXT01" type="text" placeholder="type username" onKeyDown="keyboard(event);">
                </li>   
            	<li>
                    <i class="pwd_b"></i>
                    <input id="TEXT02" name="TEXT02" type="password" placeholder="type password" onKeyDown="keyboard(event);">
                </li>                
                <li class="login-btn"> 
					<span><?php echo ($ErrorMsg != "")?"訊息:" . $ErrorMsg:"";?><input id="submitbtn" type="button" class="loginbtn" value="Login" onClick="ChkForm(form1)"></span>
                </li>                           
            </ul>
		</form>            
        </div>
    </div>
</body>
</html>