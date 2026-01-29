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
	LoginChk(GetFileCode(__FILE__),"4");
//資料庫連線
	$NewSql = new mysql();	
		
//參數
	$GetFileCode = GetFileCode(__FILE__);
	$GetFileType = GetFileType(__FILE__);	
	$GetTitle = GetTitle($NewSql,$GetFileCode);
	$DataKey = xRequest("DataKey");
	$PageCon = xRequest("PageCon");
	
	$Sql = " Select *
			From prj_m01
			where prjm01_no = '$DataKey' 
	";
	$initRun = $NewSql -> db_query($Sql) or die("SQL ERROR");
	$init = $NewSql -> db_fetch_array($initRun);
	$initCount = $NewSql -> db_num_rows($initRun);
	
	if($initCount != 1){
		header('location:' . $GetFileCode . '.php');
	}
		
//代碼	專案類型
	$Sql = " 
		Select c02_no, C02_nm
		From code_c02
		order by c02_no
	";
	$C02Run = $NewSql -> db_query($Sql) or die("SQL ERROR");
	
//主		客戶管理
	$Sql = "
		Select comm01_no,comm01_nicknm
		From com_m01
		where comm01_type3 = 'Y'		
		order by comm01_no
	";
	$comm01Run = $NewSql -> db_query($Sql) or die("SQL ERROR");	
	
// 專案是否有應收
	$Sql = "
		Select 
		Case when (Select count(*) From pay_m01 where prjm01_no = '$DataKey') > 0 Then '1'
		when (Select count(*) From in_m01 where prjm01_no = '$DataKey') > 0 Then '1'
		Else 0 End 'Count'
	";
	$paym01Run = $NewSql -> db_query($Sql) or die("SQL ERROR");	
	$paym01Count = $NewSql -> db_result($paym01Run);
	
//成員 
	$Sql = "
		Select memm01_no, memm01_numid, memm01_nm, memm01_nick
		From mem_m01 m01
		Left join mug_01 on m01.MUG01_NO = mug_01.MUG01_NO
		where MUG01_SHOW = 'Y'
		order by m01.MUG01_NO
	";
	$memRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
	$memCount = $NewSql -> db_num_rows($memRun);	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
<script>
	function ChkForm(form){
		if(empty(form.prjm01_startDate.value,'開案日期')){
		}else if(empty(form.prjm01_nm.value,'專案名稱')){
		}else if(empty(form.comm01_no.value,'客戶')){			
		}else if(Notnum(form.prjm01_totalmoney.value,'金額')){
		}else if(empty(form.memm01_no.value,'專案負責人')){
		}else{
			form.submit()
		}

	}
	$(function() {
		$(".datepicker").datepicker({
			dateFormat: 'yymmdd'
		});
		
		$(".chosen-select").chosen({
			no_results_text: "Oops, nothing found!",
			search_contains:true			
		})	
	  });
	/* 作廢*/
	function InInvalid(DataKey){
		if(confirm("確定要刪除?")){
			window.location='PRJ01_Delete.php?DataKey=' + DataKey + '&PageCon=<?php echo urlencode($PageCon)?>'
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
                        <?php if($paym01Count <= 0){?>
						<li><input class="red" type="button" value="Delete" onClick="InInvalid('<?php echo $init["prjm01_no"];?>')" ></li></li>
                        <?php }?>
                    </ul>
                </div>
                
                <div class="box-data">
                    	<div class="input-Group">
                        <form id="form1" name="form1" method="post" action="<?php echo $GetFileCode;?>_AlterNew.php">
                        	<input id="prjm01_no" name="prjm01_no" type="hidden" value="<?php echo $init["prjm01_no"];?>">
                            <input id="PageCon" name="PageCon" type="hidden" value="<?php echo ($PageCon);?>">
                        	<table class="table-input">                           
                            	<tr>
                                	<td><em>*</em>開案日期</td>
                                	<td><input type="text" id="prjm01_startDate" name="prjm01_startDate" size="8" maxlength="8" class="datepicker" value="<?php echo DspDate($init["prjm01_startDate"]);?>"></td>
                                </tr>                            
                            	<tr>
                                	<td><em>*</em>專案名稱</td>
                                	<td><input type="text" id="prjm01_nm" name="prjm01_nm" size="60" maxlength="50" value="<?php echo $init["prjm01_nm"];?>"></td>
                                </tr>                            
                            	<tr>
                                	<td>專案類型</td>
                                	<td>
                                    	<select id="c02_no" name="c02_no" class="chosen-select w200" data-placeholder="..." >
                                        	<option value=""></option>
                                            <?php while($C02 = $NewSql -> db_fetch_array($C02Run)){?>
                                            <option value="<?php echo $C02["c02_no"]?>"<?php echo ($init["c02_no"] == $C02["c02_no"]?" selected":"")?>><?php echo $C02["C02_nm"]?></option>
                                            <?php }?>
                                        </select>
                                    </td>
                                
                                </tr>
                            	<tr>
                                	<td><em>*</em>客戶</td>
                                	<td>
                                    	<select id="comm01_no" name="comm01_no" class="chosen-select w300" data-placeholder="..." >
                                        	<option value=""></option>
                                            <?php while($comm01 = $NewSql -> db_fetch_array($comm01Run)){?>
                                            <option value="<?php echo $comm01["comm01_no"]?>"<?php echo ($init["comm01_no"] == $comm01["comm01_no"]?" selected":"")?>><?php echo $comm01["comm01_nicknm"]?></option>
                                            <?php }?>
                                        </select>
                                    </td>                                
                                </tr>
                            	<tr>
                                	<td>報價單號</td>
                                	<td><input type="text" id="prjm01_Quoid" name="prjm01_Quoid" size="50" maxlength="30" value="<?php echo $init["prjm01_Quoid"]?>"></td>                               
                                </tr>
                            	<tr>
                                	<td>總額</td>
                                	<td><input type="text" id="prjm01_totalmoney" name="prjm01_totalmoney" size="20" maxlength="20" value="<?php echo $init["prjm01_totalmoney"];?>"><small>請填寫含稅總額</small></td>
                                </tr>
                                <tr>
                                	<td>專案負責人</td>
                                    <td>
                                    	<Select id="memm01_no" name="memm01_no">
                                        	<option value=""></option>
                                            <?php while($mem = $NewSql -> db_fetch_array($memRun)){?>
                                        	<option value="<?php echo $mem["memm01_no"];?>"<?php echo ($mem["memm01_no"] == $init["memm01_no"])?" selected":"";?>><?php echo $mem["memm01_nick"];?></option>
                                            <?php }?>
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