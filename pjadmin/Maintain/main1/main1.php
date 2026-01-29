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
	$GetFileCode = GetFileCode(__FILE__);
	$GetTitle = GetTitle($NewSql,$GetFileCode);
	$num_pages = xRequest("page");
	$myWhere = " where 1 = 1 ";
	$PageCon = "";										//搜尋條件
	
//搜尋
	$sType1 = xRequest("sType1");
	$sType2 = xRequest("sType2");
	$sType3 = xRequest("sType3");
	$sType3 = xRequest("sType3");
	$sSreach = xRequest("sSreach");

	if($sType1 != ""){
		$myWhere .= " and comm01_type1 = '$sType1'";
		$PageCon .= "&sType1=$sType1";
	}
	if($sType2 != ""){
		$myWhere .= " and comm01_type2 = '$sType2'";
		$PageCon .= "&sType2=$sType2";
	}
	if($sType3 != ""){
		$myWhere .= " and comm01_type3 = '$sType3'";
		$PageCon .= "&sType3=$sType3";
	}	
	if($sSreach != ""){
		$myWhere .= " and (comm01_nm like '%$sSreach%' or comm01_nicknm like '%$sSreach%')";
		$PageCon .= "&sSreach=$sSreach";
	}			
	
	$Sql = " 
		Select comm01_no,comm01_nm,comm01_nicknm, comm01_type1, comm01_type2, comm01_type3
		, comm01_quid, comm01_tel, comm01_addr
		From com_m01 
		$myWhere
		Order by comm01_no desc
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
					$('#KeyForm').attr('action','<?php echo $GetFileCode . '_Delete.php'?>')
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
	function Sreach(){
		$("#SreachForm").submit()
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
                    <li style="padding:0 10px;">
                    	第
						<select onChange="javascript:window.location='/pjadmin/Maintain/main1/main1.php?page=' + this.value">
                        <?php for($i = 1;$i <= $PageCount;$i++){?>
                        	<option value="<?php echo $i;?>"<?php echo ($num_pages == $i?" selected":"")?>><?php echo $i;?></option>
						<?php }?>
                        </select>
                        <?php echo "/	" . $PageCount;?>
                        頁,每頁<?php echo $aPageCount;?>筆,共<?php echo $initCount;?>筆
                    </li>
                    <li>
                    	<input type="button" class="gary" onClick="Sreach()" value="查詢">
                    </li>  
                    <li>
                        <form id="SreachForm" name="SreachForm" action="<?php echo $GetFileCode . '.php'?>">
                            類型
                            <Select id="sType1" name="sType1" class="chosen-select" data-placeholder="選擇">
                                <option value=""></option>
                                <option value="C" <?php if($sType1 == "C"){ echo " selected";}?>>公司</option>
                                <option value="P" <?php if($sType1 == "P"){ echo " selected";}?>>個人</option>
                            </Select>
                            <input id="sType3" name="sType3" type="checkbox" value="Y" <?php if($sType3 == "Y"){ echo " checked";}?>>
                            客戶
                            <input id="sType2" name="sType2" type="checkbox" value="Y" <?php if($sType2 == "Y"){ echo " checked";}?>>
                            外製
                            搜尋
                            <input id="sSreach" name="sSreach" type="text" value="<?php echo $sSreach;?>" placeholder="名稱">
                        </form>
                    </li>                  
                </ul>
            </div>   
			<form id="KeyForm" name="KeyForm" method="post">
            <input id="DataKey" name="DataKey" type="hidden">
            <input id="DataCount" name="DataCount" type="hidden" value="<?php echo $initCount;?>">
            <input id="Code_Id" name="Code_Id" type="hidden" value="<?php echo $GetFileCode?>">
            <input id="PageCon" name="PageCon" type="hidden" value="<?php echo $PageCon;?>">
        	<table class="table-bordered">
            	<tr class="title">
                    <!--<td class="center" width="30"></td>-->
                    <td class="center" width="30">編輯</td>
                    <td class="center" width="30">類型</td>
                    <td class="center" width="30">客戶</td>                    
                    <td class="center" width="30">外製</td>                    
                    <td class="left">名稱</td>
                    <td class="left">簡稱</td>
					<td class="left">統編</td>
                    <td class="left">電話</td>                    
                    <td class="left">地址</td>                                        
                </tr>
                <?php 
				$i = 0;
				while($Data = $NewSql -> db_fetch_array($initRun)){
					$i ++;
				?>
            	<tr class="<?php echo ($i%2 == 0)?'odd':'';?>">
                    <!--<td class="center" width="30"><input id="check<?php echo $i;?>" name="check<?php echo $i;?>" class="checkbox" type="checkbox" value="<?php echo $Data["comm01_no"];?>"></td>-->
                    <td class="center" width="30">
                    	<div align="center" class="block blue">
                        	<a class="edit" onClick="SetKey(<?php echo $Data["comm01_no"];?>);EventClick('Alter')"></a>
                        </div>
                    </td>
                    <td class="center"><?php echo ($Data["comm01_type1"] == "C"?"公司":"個人");?></td>
                    <td class="center" width="30"><?php echo ($Data["comm01_type3"] == "Y"?"是":"");?></td>                      
                    <td class="center" width="30"><?php echo ($Data["comm01_type2"] == "Y"?"是":"");?></td>                    
                    <td class="left" width="30"><?php echo $Data["comm01_nm"];?></td>                                                      
                    <td class="left"><?php echo $Data["comm01_nicknm"];?></td>
					<td class="left"><?php echo $Data["comm01_quid"];?></td>
                    <td class="left"><?php echo $Data["comm01_tel"];?></td>                    
                    <td class="left"><?php echo $Data["comm01_addr"];?></td>                    
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