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
	ChkLogin();
//資料庫連線
	$NewSql = new mysql();		
	$editable = false;
//參數
	$LevelMem = LevelMEM($NewSql);
	switch ($LevelMem){
		case '1':
		case '2':
			$editable = true;
			break;
	}
	
	$Sql = " 
		Select dashm_content
		From dash_m
		limit 1
	";
	$DashRun = $NewSql -> db_query($Sql) or die("SQL ERROR1");
	$Dash = $NewSql -> db_result($DashRun);


?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	$(function(){

		$("#TEXT01").change(function(){
			$("#showContent").html($("#TEXT01").val())
		})
		$("#Save").click(function(){
			//window.location = 'Post_Dashbord.php?TEXT01=' + $("#TEXT01").val()
			$.post(
				'Post_Dashbord.php',
				{TEXT01:$("#TEXT01").val()},
				function(xml){
				}
			);
		})
	})
</script>
<style>
	#BoxContent{
		position:absolute;
		margin:20px;
		left:10px;
	}
	#BoxContent h2{
		margin:10px 0 10px 0;
	}
</style>
</head>
<body>
	<div id="BoxContent" align="left">
    	<h2>[ 系統訊息 ]</h2>
		<ul>
        	<?php if($editable){?>
			<li>
                <div align="left">
                	<input id="Save" name="Save" type="button" value="存檔">
                    <input id="Cancel" onClick="javascript:window.location.reload()" type="button" value="取消">
                </div>            
            	<textarea id="TEXT01" name="TEXT01" rows="20" cols="100"><?php echo $Dash ;?></textarea>
            </li>
            <?php }?>
			<li id="showContent"><?php echo $Dash ;?></li>
		</ul>
	</div>
</body>
</html>