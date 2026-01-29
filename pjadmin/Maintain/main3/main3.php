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
	LoginChk(GetFileCode(__FILE__),"1");
//資料庫連線
	$NewSql = new mysql();		
//參數
	$LevelLimit = "";
	$GetFileCode = GetFileCode(__FILE__);
	$GetFileType = GetFileType(__FILE__);	
	$GetTitle = GetTitle($NewSql,$GetFileCode);
	$num_pages = xRequest("page");
	
	$mywhere = " where 1 = 1 ";
	$LevelLimit = LevelLimit($NewSql,"m01.memm01_no");
	$mywhere .= ($LevelLimit != ''?" and " . $LevelLimit:"");
	
	$Sql = " 
		Select m01.memm01_no, m01.memm01_nm, memm01_nick, MUG01_NAME, ifnull(memm01_Levnm,'') as memm01_Levnm
		From mem_m01 m01
		Left join mug_01 mu01 on mu01.MUG01_NO = m01.MUG01_NO
		Left join (
			select memm01_no, case when memm01_nick <> '' then memm01_nick else memm01_nm end memm01_Levnm
			from mem_m01
		)as Levm01 on m01.memm01_uplevel= Levm01 .memm01_no
		$mywhere
		and mu01.MUG01_SHOW = 'Y'
		order by m01.MUG01_NO
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$initCount = $NewSql -> db_num_rows($initRun);
	
	$aPageCount = "15";
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
						<select onChange="javascript:window.location='/pjadmin/Maintain/main3/main3.php?page=' + this.value">
                        <?php for($i = 1;$i <= $PageCount;$i++){?>
                        	<option value="<?php echo $i;?>"<?php echo ($num_pages == $i?" selected":"")?>><?php echo $i;?></option>
						<?php }?>
                        </select>
                        <?php echo "/	" . $PageCount;?>
                        頁,每頁<?php echo $aPageCount;?>筆,共<?php echo $initCount;?>筆
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
                    <td class="center">姓名</td>
                    <td class="center">簡稱</td>
                    <td class="center">層級</td>
                </tr>
                <?php 
				$i = 0;
				while($Data = $NewSql -> db_fetch_array($initRun)){
					$i ++;
				?>
            	<tr class="<?php echo ($i%2 == 0)?'odd':'';?>">
                	<td class="center" width="30"><?php echo $i + (($num_pages-1)*$aPageCount);?></td>
                    <td class="center" width="30"><input id="check<?php echo $i;?>" name="check<?php echo $i;?>" class="checkbox" type="checkbox" value="<?php echo $Data["comm01_no"];?>"></td>
                    <td class="center" width="30">
                    	<div align="center" class="block blue">
                        	<a class="edit" onClick="SetKey(<?php echo $Data["memm01_no"];?>);EventClick('Alter')"></a>
                        </div>
                    </td>                    
                    <td class="center"><?php echo $Data["memm01_nm"];?></td>
                    <td class="center"><?php echo $Data["memm01_nick"];?></td>
                    <td class="center"><?php echo ($Data["memm01_Levnm"] != "")?" -" . $Data["memm01_Levnm"]:$Data["MUG01_NAME"];?></td>
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