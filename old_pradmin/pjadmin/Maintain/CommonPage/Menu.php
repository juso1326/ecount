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
	$Mem_No = $_SESSION["MemNO"];
	//取出帳號是否有開啟
	$Sql = "
		Select mug01.MUG01_NO
		From mem_m01 m01
		Left join mug_01 mug01 on m01.MUG01_NO = mug01.MUG01_NO
		where m01.memm01_no = '$Mem_No'
		and mug01.MUG01_OPEN = 'Y'
		and m01.memm01_open = 'Y'	
	";
	$MemRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
	$Mem = $NewSql -> db_fetch_array($MemRun);
	$MemCount = $NewSql -> db_num_rows($MemRun);
	if($MemCount <= 0){
		exit();
	}
	
	$Sql = " 
	/*
		Select m03.*
		From mug_03 m03
		Left join mug_04 m04 on m03.MUG03_NO = m04.MUG03_NO
		where (m04.MUG01_NO = '2' or m03.MUG03_TYPE = 'D')
		and (
		(m03.MUG03_TYPE = 'D' and (Select count(*) From mug_03 where mug_03.MUG03_LEVEL = m03.MUG03_NO ) > 0)
		or (MUG03_LEVEL = '')
		)	
		order by m03.MUG03_TYPE
	*/
		Select mug_03.*
		From mug_03
		where 1 = 1 
		and (MUG03_TYPE = 'D' or 
			MUG03_No in (
				Select MUG03_NO
				From mug_04
				where MUG01_NO = '" . $Mem["MUG01_NO"] . "'
				and MUG04_OPENSEL = 'Y'
			)
		)
		and MUG03_LEVEL = ''
		order by MUG03_SORT 
	
	";
	$MenuRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
	
//	$NewSql -> db_close();	

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php include_once(root_path . "/pjadmin/Maintain/CommonPage/Meta.php")?>
<title><?php echo MaintainMetaTitle;?></title>
</head>

<body>
<!--======================= sider bar left =======================-->
    <div class="sidebar-left">
        <div class="sidebar-nav">
            <ul class="nav">
                <li class="active">
                    <a href="javascript:void(0)" onClick="javascript:parent.MenuSet(this,'/pjadmin/Maintain/frame/Dashbord.php')" >
                        <i class="Dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <?php while($Menu = $NewSql -> db_fetch_array($MenuRun)){?>
                <li>
                	<?php if ($Menu["MUG03_TYPE"] == "D"){?>
                    <!--
                    <a class="folder-btn" href="javascript:void(0)">
                        <i class="<?php echo $Menu["MUG03_IMG"]?>"></i><span><?php echo $Menu["MUG03_NM"]?></span>
                    </a>
                    -->
                        <?php 
							
								$Sql = " 
									Select MUG03_NO, MUG03_CODE, MUG03_NM, MUG03_PATH 
									From mug_03 m03 
									where MUG03_LEVEL = '" . $Menu["MUG03_NO"] . "' 
									and	m03.MUG03_No in (
											Select MUG03_NO
											From mug_04
											where MUG01_NO = '" . $Mem["MUG01_NO"] . "'
											and MUG04_OPENSEL = 'Y'
										)									
									order by MUG03_SORT 
								";
								$FileRun = $NewSql -> db_query($Sql) or die("SQL ERROR 3");
								$FileCount = $NewSql -> db_num_rows($FileRun);										
								if($FileCount > 0){		
						?>
                                <a class="folder-btn" href="javascript:void(0)">
                                    <i class="<?php echo $Menu["MUG03_IMG"]?>"></i><span><?php echo $Menu["MUG03_NM"]?></span>
                                </a>                        
                        <?php												
									echo "<ul>";
									while($File = $NewSql -> db_fetch_array($FileRun)){
										echo '<li>';
										echo '	<a href="javascript:void(0)" onClick="javascript:parent.MenuSet(this,' . '\'' . $File["MUG03_PATH"] . '\'' . ')"> ';
										//echo '	<a href="javascript:void(0)" onClick="window.parent.location=' .  $File["MUG03_PATH"] . '"> ';
										echo '	<span style="padding-left:20px;">' . $File["MUG03_NM"] . '</span>';
										echo '	</a>';
										echo '</li>';
									}
									echo "</ul>";									
								}								
						?>
                        	
					<?php }else{
							echo '<li>';
							echo '	<a href="javascript:void(0)" onClick="javascript:parent.MenuSet(this,' . '\'' . $Menu["MUG03_PATH"] . '\'' . ')"> ';
							echo '	<span style="padding-left:20px;">' . $Menu["MUG03_NM"] . '</span>';
							echo '	</a>';
							echo '</li>';
					}
					?>                    
				</li>                
                <?php }?>
                <!--
                <li>
                    <a class="folder-btn" href="javascript:void(0)">
                        <i class="folder-on"></i><span>權限管理資料夾</span>
                    </a>
                    <ul>
                        <li>
                            <a href="/Maintain/mug/mug2.php">
                                <span style="padding-left:20px;">功能管理</span>
                            </a>
                        </li>                    
                        <li>
                            <a href="/Maintain/mug/mug.php">
                                <span style="padding-left:20px;">群組管理</span>
                            </a>
                        </li>
                        <li>
                            <a href="/Maintain/mug/mug1.php">
                                <span style="padding-left:20px;">使用者管理</span>
                            </a>
                        </li>                        
                    </ul>
                </li>
				-->            
                                
                <!--
                <li>
                    <a>
                        <i></i>
                        <span>LogOut</span>
                    </a>
                </li>                     
                -->
            </ul>
        </div>
    </div>
<!--======================= sider bar left =======================-->
</body>
</html>
