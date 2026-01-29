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
//參數
	$GetFileCode = GetFileCode(__FILE__);	
	$DataKey = xRequest("DataKey");
//資料庫連線
	$NewSql = new mysql();	
	
	$Sql = "
		Select *
		From mug_03
		where MUG03_NO not in (
			Select MUG03_NO
			From mug_04
			where MUG01_NO = '$DataKey'
		)
		and MUG03_TYPE = 'F'
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL Error 1");
	$initCount = $NewSql -> db_num_rows($initRun);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	$(function(){
		$('input[type="checkbox"]').attr('checked',true)
	})
	function EventClick(event){
		switch (event){
			case 'Add':
				$('#KeyForm').attr('action','<?php echo $GetFileCode . '_AddNew.php'?>')
				$('#KeyForm').submit();
				break;
			default:
				break;
		}
	}
	function SetKey(Key){
		$('#DataKey').val(Key)
	}
</script>
<style>
.prev{
	padding:2px 6px;
	background-color:#fff;
	border:1px solid #ddd;
}
</style>
</head>
<body>
<div id="wrapper">
<div class="side-content">
	<ul class="breadcrumb">
    	<li>群組管理</li>
    	<li></li>        
    </ul>
	<div class="box">
        <div class="box-content">
            <div class="box-head">
            	<ul class="btn">
                	<li><input class="green" type="button" value="Add" onClick="EventClick('Add')"></li>
					<li><input class="gray" type="button" value="Return" onClick="window.location='mug04.php?DataKey=<?php echo $DataKey;?>'"></li>                    
                </ul>
            </div>   
			<form id="KeyForm" name="KeyForm" method="post">
            <input id="DataKey" name="DataKey" type="hidden" value="<?php echo $DataKey;?>">
            <input id="DataCount" name="DataCount" type="hidden" value="<?php echo $initCount;?>">
        	<table class="table-bordered">
            	<tr class="title">
                	<td class="center" width="30"></td>
                    <td class="center" width="30"></td>                   
                	<td class="center" width="150">程式名稱</td>
                	<td class="left" width="50">程式路徑</td>
                    <td class="left" width="50">查詢</td>
                    <td class="left" width="50">新增</td>
                    <td class="left" width="50">刪除</td>
                    <td class="left" width="50">修改</td>
                </tr>
                <?php 
				$i = 0;
				while($Data = $NewSql -> db_fetch_array($initRun)){
					$i ++;
				?>
            	<tr class="<?php echo ($i%2 == 0)?'odd':'';?>">
                	<td class="center" width="30"><?php echo $i;?></td>
                    <td class="center" width="30"><input id="check<?php echo $i;?>" name="check<?php echo $i;?>" class="checkbox" type="checkbox" value="<?php echo $Data["MUG03_NO"];?>"></td>                    
                	<td><?php echo $Data["MUG03_NM"];?></td>
                	<td><?php echo $Data["MUG03_PATH"];?></td>
                    <td class="left" width="50"><input id="Search<?php echo $i;?>" name="Search<?php echo $i;?>" class="checkbox" type="checkbox" value="Y"></td>
                    <td class="left" width="50"><input id="ADD<?php echo $i;?>" name="ADD<?php echo $i;?>" class="checkbox" type="checkbox" value="Y"></td>
                    <td class="left" width="50"><input id="DEL<?php echo $i;?>" name="DEL<?php echo $i;?>" class="checkbox" type="checkbox" value="Y"></td>
                    <td class="left" width="50"><input id="ALT<?php echo $i;?>" name="ALT<?php echo $i;?>" class="checkbox" type="checkbox" value="Y"></td>                    
                </tr>
                <?php }?>                               
            </table>
			</form>            
        </div>
    </div>
</div>

</div>
</body>
</html>