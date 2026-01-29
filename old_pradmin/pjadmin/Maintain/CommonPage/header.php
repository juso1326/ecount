<?php 
	//取得登入者的名稱
	$Sql = " 
		select memm01_nick,memm01_nm
		from mem_m01
		where memm01_no = '" . $_SESSION["MemNO"] . "'
	";
	$headerRun = $NewSql -> db_query($Sql) or die("SQL ERROR 1");
	$Mem = $NewSql -> db_fetch_array($headerRun);
?>
	<div class="header">
    	<div class="header-nav left">
            <div class="brand">
                <a href="/pjadmin/Maintain/frame/frame.php">3Wcreative</a>
            </div>
        </div>
        <div class="header-nav right">
        	<ul>
                <li class="dropdown open">
                	<a href="javascript:void(0)">
                    	<i class="user"></i>
                        <?php echo $Mem["memm01_nick"] . "	[" . $Mem["memm01_nm"] . "]";?>
                        <input class="Logout" type="button" value="Logout" onClick="window.location='/pjadmin/Maintain/login/Logout.php'">
                    </a>
                </li>
                <li></li>                
            </ul>
        </div>
    </div>