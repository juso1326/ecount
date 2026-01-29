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
	$LevelMem = LevelMEM($NewSql);
	$GetFileCode = GetFileCode(__FILE__);
	$GetFileType = GetFileType(__FILE__);	
	$GetTitle = GetTitle($NewSql,$GetFileCode);
	$DataKey = xRequest("DataKey");
	$sY = xRequest("sY");
	$sM = xRequest("sM");
	$sKey = xRequest("sKey");
	
	if($sY.$sM == date('Ym')){
		$Ymd = date('Ymd');
	}else{
		$Ymd = $sY.$sM."06";
	}
	$Sql = "
		Select C03_no, C03_nm
		From code_c03	
	";
	$C03Run = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
	$C03Count = $NewSql -> db_num_rows($C03Run);	
	
	$Sql = "
		Select foundt01_no ,foundt01_type ,foundt01_content ,foundt01_total ,foundt01_remark ,foundt01_DateSt ,foundt01_DateEd,foundt01_who
		, foundt01_times
		From found_t01 f
		where memm01_no = '$DataKey'
		and foundt01_times = '2'	
		order by foundt01_DateEd desc
	";
	$FRun = $NewSql -> db_query($Sql) or die("SQL ERROR 2");
	
	if($sKey != ''){
		$Sql = "
			Select foundt01_no ,foundt01_type ,foundt01_content ,foundt01_total ,foundt01_remark ,foundt01_DateSt ,foundt01_DateEd, foundt01_who
			, foundt01_times
			From found_t01 f
			where memm01_no = '$DataKey'
			and foundt01_no = '$sKey'	
		";
		$foundRun = $NewSql -> db_query($Sql) or die("SQL ERROR 3");
		$found = $NewSql -> db_fetch_array($foundRun);
		$foundt01_type = $found["foundt01_type"];
		$foundt01_content = $found["foundt01_content"];
		$foundt01_total = $found["foundt01_total"];
		$foundt01_remark = $found["foundt01_remark"];
		//$foundt01_DateSt = $found["foundt01_DateSt"];
		$Ymd = $found["foundt01_DateSt"];
		$foundt01_DateEd = $found["foundt01_DateEd"];
		$foundt01_times = $found["foundt01_times"];
		$foundt01_who = $found["foundt01_who"];
	}

	//員工
	$mywhere = " where 1 = 1 ";
	// $LevelLimit = LevelLimit($NewSql,"memm01_no");
	// $mywhere .= ($LevelLimit != ''?" and " . $LevelLimit:"");	
	$Sql = "
		Select memm01_no, memm01_nick
		From mem_m01 m01
		Left join mug_01 on m01.MUG01_NO = mug_01.MUG01_NO
		$mywhere
		and MUG01_SHOW = 'Y'
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
	$(document).ready(function(){		
		<?php if($found["foundt01_DateSt"] != $Ymd){?>
		//檢查預設
		var val = $("#foundt01_DateSt").val()
		var mem = $("#memm01_no").val()
		if(val != ""){
			$.post(
				'Post_main4.php',
				{date:val,mem:mem},
				function(xml){
					if($('resu',xml).text() != '1'){
						$("#foundt01_DateSt").val('')
					}
				}
			);
		}
		<?php }?>
	})
	$(function(){
		$(".datepicker").datepicker({
			dateFormat: 'yymmdd',
			minDate: '20150101'
		});		
		
		$("#C03_no").change(function(){
			$("#foundt01_content").val($(this).find("option:selected").text())
		})
	})
	function ChkForm(form){
		if(empty(form.foundt01_content.value,'內容')){
		}else if(empty(form.foundt01_total.value,'金額')){			
		}else{
			/*
			$("#form1").attr('action','main4_fixNew.php')
			$("#form1").submit()
			*/
			$.post(
				"main4_fixNew.php",
				$("#form1").serialize(),
				function(xml){
					if($('resu',xml).text() == "1"){
						window.parent.location.reload();
						parent.Shadowbox.close()						
					}
				}
			)
		}

	}
	
	function EventClick(E,Key){
		if(E == 'Alter'){
			window.location.href='main4_fix.php?DataKey=<?php echo $DataKey;?>&sY=<?php echo $sY?>&sM=<?php echo $sM?>&sKey=' + Key
		}
	}
	//檢查給付日期是否已經結帳
	function ChkPayDate(){
		var val = $("#foundt01_DateSt").val()
		var defaultDate = $("#defaultDate").val()
		var mem = $("#memm01_no").val()
		if(val != "" & defaultDate != val){
			$.post(
				'Post_main4.php',
				{date:val,mem:mem},
				function(xml){
					if($('resu',xml).text() == '1'){
					}else{
						$("#foundt01_DateSt").val('')
						if($('rtMeg',xml).text() != ''){
							alert($('rtMeg',xml).text())
						}else{
							alert("請選擇正確資料");
						}
					}
				}
			);
		}
	}	
</script>
<style>
.table-input .minTab{
	padding:5px;
	border:1px dashed #A5A5A5;
}
.table-input .minTab td{
	text-align:center;
	border:1px dashed #A5A5A5;	
}
.table-input .minTab td.date{ width:50px;}
.table-input .minTab td.money{ width:50px;}
.table-input .minTab td.method{ width:50px;}
.table-input .minTab td.per{ width:50px;}
.table-input .minTab td.close{ width:50px;}
</style>
</head>

<body>
<div id="wrapper">
	<div class="box-data">
             	<div class="input-Group">
                        <form id="form1" name="form1" method="post" action="main1_T_AddNew.php">
                        <input id="memm01_no" name="memm01_no" type="hidden" value="<?php echo $DataKey;?>">
                        <input id="foundt01_no" name="foundt01_no" type="hidden" value="<?php echo $sKey;?>">
                        <input id="defaultDate" name="" type="hidden" value="<?php echo $found["foundt01_DateSt"];?>">
                        	<table class="table-input">
                                <tr>
                                	<td>日期</td>
                                	<td><input id="foundt01_DateSt" name="foundt01_DateSt" type="text" class="datepicker" value="<?php echo $Ymd;?>" onChange="ChkPayDate();"></td>
                                </tr>
                                <tr>
                                	<td><em>*</em> 加/扣</td>
                                	<td>
										<select id="foundt01_type" name="foundt01_type">
											<option value="1"<?php echo ($foundt01_type == '1'?" selected":"")?>>應加</option>
											<option value="2"<?php echo ($foundt01_type == '2'?" selected":"")?>>應扣</option>											
										</select> 
										<select id="C03_no" name="C03_no" >
											<option value=""></option>
											<?php 
												while($C03 = $NewSql -> db_fetch_array($C03Run)){
													echo '<option value="' . $C03["C03_no"] . '">' . $C03["C03_nm"] . '</option>';		
												}														
											?>
										</select>                                                                              
                                    </td>
                                </tr>
                                <tr>
                                	<td></td>
                                	<td><input id="foundt01_content" name="foundt01_content" type="text" size="30" maxlength="50" value="<?php echo $foundt01_content;?>"></td>
                                </tr>
                                <tr>
                                	<td><em>*</em></td>
                                	<td><input id="foundt01_times" name="foundt01_times" type="radio" value="1"<?php echo ($foundt01_times == '1'?" checked":"");?> >單次
                                    <input id="foundt01_times" name="foundt01_times" type="radio" value="2"<?php echo ($foundt01_times == '2'?" checked":"");?>>固定
                                    </td>
                                </tr>  
								<tr>
                                	<td>支付給</td>
                                	<td>
									<select id="foundt01_who" name="foundt01_who" >
										<option value=""<?php echo ($sMem == "")?" selected":"";?>></option>                        
										<?php 
											while($mem = $NewSql -> db_fetch_array($memRun)){
												echo '<option value="' . $mem["memm01_no"] . '"';
												echo ($sMem == $mem["memm01_no"])?" selected":"";
												echo '>' . $mem["memm01_nick"] . '</option>';		
											}														
										?>
									</select>  
									</td>
                                </tr>                               
                                <tr>
                                	<td>截止日</td>
                                	<td><input id="foundt01_DateEd" name="foundt01_DateEd" type="text" class="datepicker" value="<?php echo $foundt01_DateEd;?>" autocomplete="off"></td>
                                </tr>                                 
                                <tr>
                                	<td><em>*</em>金額</td>
                                	<td><input id="foundt01_total" name="foundt01_total" type="text" size="10" maxlength="8" value="<?php echo $foundt01_total;?>"></td>
                                </tr>  
								<tr>
                                	<td>備註</td>
                                	<td><input id="foundt01_remark" name="foundt01_remark" type="text" size="30" maxlength="100" value="<?php echo $foundt01_remark;?>"></td>
                                </tr>                                                                                              
								<tr>
                                	<td></td>
                                	<td>
                                    	<input class="blue" type="button" value="Save" onClick="ChkForm(form1)">
                                    </td>                                    

                                </tr>                                                                                                                                                            
                            </table>
						</form>
                        <div style="margin-left:50px; margin-top:10px;">
                        <h3 style="padding:2px;">固定加扣項</h3>
                        <table class="tablelist">
                        	<tr class="title">
                            	<td>編輯</td>
                            	<td>類別</td>
                            	<td>內容</td>
                            	<td>金額</td>
                            	<td>起</td>
                            	<td>迄</td>                                
                            </tr>
                        <?php while($F = $NewSql -> db_fetch_array($FRun)){?>
                        	<tr>
                                <td class="center" width="30">
									<?php if($F['foundt01_DateEd'] > date('Ymd') ){?>
                                    <div align="center" class="block blue">
                                        <a class="edit" onClick="EventClick('Alter','<?php echo $F["foundt01_no"];?>')"></a>
                                    </div>
									<?php }?>
                                </td>
                            	<td><?php echo ($F["foundt01_type"] == '1'?"加項":"扣項")?></td>
                            	<td><?php echo $F["foundt01_content"];?></td>
                            	<td class="right"><?php echo MoneyFormat($F["foundt01_total"]);?></td>
                            	<td><?php echo Dspdate($F["foundt01_DateSt"],"/");?></td>
                            	<td><?php echo Dspdate($F["foundt01_DateEd"],"/");?></td>
                            </tr>
						<?php }?>
                        </table>
                        </div>
                        </div>
                </div>  
</div>
</body>
</html>