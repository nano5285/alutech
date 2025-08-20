<?php
#*************** USER INFORMATIONS ***********************
if(!isset($_COOKIE["lethe_admin"])){
	define('admin_mode',0);
	define('admin_ID',0);
} else {
	$opLoggedUser = $myconn->query("SELECT * FROM ". db_table_pref ."users WHERE user_hash='". mysql_prep($_COOKIE["lethe_admin"]) ."'") or die(mysqli_error($myconn));
	if(mysqli_num_rows($opLoggedUser)==0){header('Location:lethe.login.php');}else{
		$opLoggedUserRs = mysqli_fetch_assoc($opLoggedUser);
		define('admin_mode',$opLoggedUserRs['admin_mode']); # 1 - Super Admin // 2 - User
		define('admin_ID',$opLoggedUserRs['ID']);
		define('admin_name',$opLoggedUserRs['lethe_user']);
	}

}

if(admin_mode==0){header('Location:lethe.login.php');}
?>