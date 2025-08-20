<?php 
include_once(dirname(dirname(dirname(__FILE__))).'/inc/inc_connector.php'); 
include_once(LETHEPATH.'/inc/inc_functions.php');
$errText = null;
if(isset($_GET['logout']) && $_GET['logout']=='yes'){
	if(isset($_COOKIE['lethe_admin'])){
		
		setcookie("lethe_admin", "", time()-3600,'/');
	}
	header('Location: index.php');
	die('error!');
	}
if(isset($_POST['pass_rec']) && $_POST['pass_rec']=='PSRC'){

	if(demo_mode){echo(lethe_demo_mode_active);}else{
		include_once('classes/class.lethe.php');
		include_once('lethe.config.php');
		
		if(!isset($_POST['lethe_mail']) || !mailVal($_POST['lethe_mail'])){
			die('<div class="alert alert-danger">'. lethe_invalid_e_mail_address .'</div>');
		}
		
		$opUs = $myconn->prepare("SELECT ID,user_mail,lethe_user FROM ". db_table_pref ."users WHERE user_mail=?") or die(mysqli_error($myconn));
		$opUs->bind_param('s',$_POST['lethe_mail']);
		$opUs->execute();
		$opUs->store_result();
		if($opUs->num_rows==0){
			echo('<div class="alert alert-danger">'. lethe_record_not_found .'</div>');
		}else{
			$opUs->bind_result($u_ID,$u_user_mail,$u_lethe_user);
			$opUs->fetch();
			$lethe = new lethe;
			if($lethe->pass_recovery($u_ID,$u_user_mail,$u_lethe_user)){
				echo('<div class="alert alert-success">'. lethe_a_new_password_has_been_sent_to_your_e_mail_address .'</div>');
			}else{
				echo('<div class="alert alert-danger">'. $lethe->errPrint .'</div>');
			}
			
		} $opUs->close();
	}
	die();
	}
if(isset($_POST['login'])){
	
	if(!isset($_POST['lethe_user']) || empty($_POST['lethe_user'])){
		$errText .= '* '. lethe_please_enter_a_username .'<br>';
		}
	if(!isset($_POST['lethe_pass']) || empty($_POST['lethe_pass'])){
		$errText .= '* '. lethe_please_enter_a_password .'<br>';
		}
		
	if($errText==''){
			$opUser = $myconn->query("SELECT * FROM ". db_table_pref ."users WHERE lethe_user='". mysql_prep($_POST['lethe_user']) ."'") or die(mysqli_error($myconn));
			
				if(mysqli_num_rows($opUser)==0){
					$errText = '<div class="alert alert-danger">'. lethe_incorrect_login_informations .'</div>';
				}else{
					$opUserRs = mysqli_fetch_assoc($opUser);
					if($opUserRs['user_pass']!=encr($_POST['lethe_pass'])){
						$errText = '<div class="alert alert-danger">'. lethe_incorrect_login_informations .'</div>';
						}else{
							# ** User Informations Define to Cookie
							# If you wanna use Session Change These Cookie scripts to Session
							$sessionTime = 0;
							if(isset($_POST['remember_me']) && !empty($_POST['remember_me'])){
								$sessionTime=time() + (10 * 365 * 24 * 60 * 60);
							}else{
								$sessionTime=time()+(11800);
							}
							//setcookie("lethe_admin", $opUserRs["user_hash"], $sessionTime,lethe_admin_path);
							setcookie("lethe_admin", $opUserRs["user_hash"], $sessionTime,'/');
							header('Location: index.php');
							}
					}
		
		}else{
			$errText = '<div class="alert alert-danger">'. $errText .'</div>';
			}
	
	}
?>
<!doctype html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
<title><?php echo(lethe_newsletter_site_name);?></title>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">

<link rel="stylesheet" href="bootstrap/dist/css/normalize.css">
<link rel="stylesheet" href="bootstrap/dist/css/bootstrap.min.css">
<link href="css/lethe.style.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="Scripts/jquery-1.8.1.min.js"></script>
<style>
	.panel{width:350px; margin:50px auto;}
	#carousel-login{width:550px; margin:0 auto;}
</style>
</head>

<body>

<div id="page-wrapper">
<div id="main-layout">
<div id="logo-area"><img src="images/lethe/lethe.png" alt="Lethe Newsletter & Mailling System"></div>

<div id="carousel-login" class="carousel slide" data-ride="carousel" data-interval="0">

  <!-- Wrapper for slides -->
  <div class="carousel-inner">
    <div class="item active">

		<!-- Content Start -->
		<div class="panel panel-default">
		  <div class="panel-body">
		  
			<h1>Login</h1>
			<?php echo($errText);?>
		<form role="form" action="" method="post">
		  <div class="form-group">
			<label for="InputEmail1"><?php echo(lethe_username);?></label>
			<input type="text" value="<?php if(demo_mode){echo('lethe');}?>" class="form-control" name="lethe_user" id="InputEmail1" placeholder="<?php echo(lethe_username);?>">
		  </div>
		  <div class="form-group">
			<label for="InputPassword1"><?php echo(lethe_password);?></label>
			<input type="password" value="<?php if(demo_mode){echo('lethe');}?>" name="lethe_pass" class="form-control" id="InputPassword1" placeholder="<?php echo(lethe_password);?>">
		  </div>
		  <div class="form-group">
				<span style="font-size:11px;"><a tabindex="-1" href="javascript:;" class="forgot_pass"><?php echo(lethe_forgot_my_password);?></a></span>
				<span class="pull-right"><input type="checkbox" name="remember_me" id="remember_me"> <label for="remember_me"><?php echo(lethe_remember_me);?></label></span>
		  </div>
		  <button type="submit" name="login" value="login" class="btn btn-info pull-right"><?php echo(lethe_login);?></button>
		</form>

		  
		  </div><!-- Panel Body End -->
		</div>
		<!-- Content End -->

    </div>

    <div class="item">


		<!-- Content Start -->
		<div class="panel panel-default">
		  <div class="panel-body">
		  
			<h2><?php echo(lethe_password_recovery);?></h2>
			<div id="results"></div>
		<form role="form" action="" method="post" name="passRecForm" id="passRecForm">
		  <div class="form-group">
			<label for="InputEmail2"><?php echo(lethe_e_mail_address);?></label>
			<input type="email" class="form-control" name="lethe_mail" id="InputEmail2" placeholder="<?php echo(lethe_e_mail_address);?>">
		  </div>
		  <button type="submit" name="send_pass" value="send_pass" class="btn btn-info pull-right"><?php echo(lethe_send);?></button>
		  <input type="hidden" name="pass_rec" value="PSRC">
		</form>

		  
		  </div><!-- Panel Body End -->
		</div>
		<!-- Content End -->
		<a href="javascript:;" class="back_login carousel-control"><span class="glyphicon glyphicon-chevron-left"></span></a>

	
    </div>
	
  </div>

</div>


</div>
</div>

<script>
$('.forgot_pass').click(function () {
  $('#carousel-login').carousel('next');
});
$('.back_login').click(function () {
  $('#carousel-login').carousel('prev');
});
$(function(){$("#passRecForm").submit(function(e){e.preventDefault();dataString=$("#passRecForm").serialize();$.ajax({type:"POST",url:"<?php echo($_SERVER['SCRIPT_NAME']);?>",data:dataString,dataType:"html",success:function(e){$("#results").html("<div>"+e+"</div>")},error:function(e){$("#results").html("<div>Error</div>")}})})});

</script>
<!-- Page End -->
<script src="bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
<?php 
$myconn->close();
?>