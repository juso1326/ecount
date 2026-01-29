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
//參數
	$GetFileCode = GetFileCode(__FILE__);
	$GetTitle = GetTitle($NewSql,$GetFileCode);
	$num_pages = xRequest("page");
	
	$Sql = " 
		Select C04_no,C04_nm
		From code_c04
		Order by C04_no 
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$initCount = $NewSql -> db_num_rows($initRun);
	
	$aPageCount = "20";
	Page($NewSql,$initCount,$Sql,$aPageCount);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	function EventClick(event){
		switch (event){
			case 'Add':
				$('#KeyForm').attr('action','<?php echo $GetFileCode . '_Add.php'?>')
				$('#KeyForm').submit();
				break;
			case 'Alter':
				$('#KeyForm').attr('action','<?php echo $GetFileCode . '_Alter.php'?>')
				$('#KeyForm').submit();
				break;
			case 'Del':
				if($('.checkbox:checked').length <= 0){
					alert("請勾選要刪除的資料")
				}else{
					$('#KeyForm').attr('action','Code_Delete.php')
					$('#KeyForm').submit();					
				}
				break;
			case 'Setting':
				$('#KeyForm').attr('action','mug04.php')
				$('#KeyForm').submit();
				break;			
				break;
			default:
				break;
		}
	}
	function SetKey(Key){
		$('#DataKey').val(Key)		
	}
</script>
</head>
<body>
<div id="wrapper">

<div class="side-content">
	<ul class="breadcrumb">
    	<li><?php echo $GetTitle;?></li>
    	<li></li>        
    </ul>
	<div class="box">
        <div class="box-content">
            <div class="box-head">
            	<ul class="btn">
                	<li><input class="green" type="button" value="Add" onClick="EventClick('Add')"></li>
                	<li><input class="red" type="button" value="Delete" onClick="EventClick('Del')"></li>
                    <li style="padding:0 10px;">
                    	第
						<select onChange="javascript:window.location='CodeC04.php?page=' + this.value">
                        <?php for($i = 1;$i <= $PageCount;$i++){?>
                        	<option value="<?php echo $i;?>"<?php echo ($num_pages == $i?" selected":"")?>><?php echo $i;?></option>
						<?php }?>
                        </select>
                        <?php echo "/	" . $PageCount;?>
                        頁
                    </li>                    
                </ul>
                <!--
            	<ul class="Src">
                	<li>搜尋</li>
                	<li>搜尋</li>
                	<li>搜尋</li>                                        
                </ul>
                -->
            </div>   
			<form id="KeyForm" name="KeyForm" method="post">
            <input id="DataKey" name="DataKey" type="hidden">
            <input id="DataCount" name="DataCount" type="hidden" value="<?php echo $initCount;?>">
            <input id="Code_Id" name="Code_Id" type="hidden" value="<?php echo $GetFileCode?>">
        	<table class="table-bordered">
            	<tr class="title">
                	<td class="center" width="30"></td>
                    <td class="center" width="30"></td>
                    <td class="center" width="30">編輯</td>
                    <td class="center">給付名稱</td>
                </tr>
                <?php 
				$i = 0;
				while($Data = $NewSql -> db_fetch_array($initRun)){
					$i ++;
				?>
            	<tr class="<?php echo ($i%2 == 0)?'odd':'';?>">
                	<td class="center" width="30"><?php echo $i;?></td>
                    <td class="center" width="30"><input id="check<?php echo $i;?>" name="check<?php echo $i;?>" class="checkbox" type="checkbox" value="<?php echo $Data["C04_no"];?>"></td>
                    <td class="center" width="30">
                    	<div align="center" class="block blue">
                        	<a class="edit" onClick="SetKey(<?php echo $Data[0];?>);EventClick('Alter')"></a>
                        </div>
                    </td>                    
                	<td><?php echo $Data["C04_nm"];?></td>
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