<?php 
include_once(dirname(dirname(dirname(__FILE__))).'/inc/inc_connector.php'); 
include_once(LETHEPATH.'/inc/inc_functions.php');
include_once('lethe_auth.php'); # User Authorization, if you have already user system change it with your own file.
include_once('classes/class.lethe.php');
include_once('lethe.config.php');

/* MiniPAN */
define('minipan_on',(file_exists(dirname(__FILE__).'/miniPan/'))?1:0);

if(!isset($_GET["pos"])){$pos=0;}else{$pos=intval($_GET["pos"]);}
if(!isset($_GET["ppos"])){$ppos=0;}else{$ppos=intval($_GET["ppos"]);}
if(!isset($_GET["ID"])){$ID=0;}else{$ID=intval($_GET["ID"]);}
if(!isset($_GET["ajax"])){$ajax=0;}else{$ajax=intval($_GET["ajax"]);}
if(!isset($_GET["ajax_part"])){$ajax_part=0;}else{$ajax_part=intval($_GET["ajax_part"]);}

# ** XMLHTTP Area ***
if($ajax==1){
	
	# Username Checker
	if($ajax_part==1){
		if(demo_mode){echo(lethe_demo_mode_active);}else{
			if(!isset($_GET["letData"])){$letData=0;}else{$letData=trim($_GET["letData"]);}
			# Check Validation
			if(!nicknameVal(mysql_prep($letData))) { // for english chars + numbers only + 15 Char
				die('<span class="glyphicon glyphicon-remove errorRed"></span>');
			}
			# Check on DB
			if(chkData("SELECT ID,lethe_user FROM ". db_table_pref ."users WHERE lethe_user='". mysql_prep($letData) ."'")){
					echo('<span class="glyphicon glyphicon-ok errorGreen"></span>');
				}else{
					echo('<span class="glyphicon glyphicon-remove errorRed"></span>');
					}
		}}
	# User E-Mail Checker
	if($ajax_part==2){
		if(demo_mode){echo(lethe_demo_mode_active);}else{
			if(!isset($_GET["letData"])){$letData=0;}else{$letData=trim($_GET["letData"]);}
			# Check Validation
			if(!mailVal(mysql_prep($letData))) { // for english chars + numbers only + 15 Char
				die('<span class="glyphicon glyphicon-remove errorRed"></span>');
			}
			# Check on DB
			if(chkData("SELECT ID,user_mail FROM ". db_table_pref ."users WHERE user_mail='". mysql_prep($letData) ."'")){
					echo('<span class="glyphicon glyphicon-ok errorGreen"></span>');
				}else{
					echo('<span class="glyphicon glyphicon-remove errorRed"></span>');
					}
		}}
	# Template Preview
	if($ajax_part==3){

				$opTempList = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_templates WHERE ID=". $ID ."") or die(mysqli_error());
                if(mysqli_num_rows($opTempList)==0){echo('<div class="alert alert-info">'. lethe_record_not_found .'</div>');}else{
                    $opTempListRs = mysqli_fetch_assoc($opTempList);
					echo($opTempListRs['details']);
					}
				$opTempList->free();

		}
	# Submission Account Details
	if($ajax_part==4){

				$opAccDetail = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_accounts WHERE ID=". $ID ."") or die(mysqli_error());
                if(mysqli_num_rows($opAccDetail)==0){echo('<div class="alert alert-info">'. lethe_record_not_found .'</div>');}else{
                    $opAccDetailRs = mysqli_fetch_assoc($opAccDetail);
					if($opAccDetailRs['email_type']==0){$mailContType = 'HTML';}else{$mailContType = 'TEXT';}
					echo('<strong>'. lethe_account_title .':</strong> '. $opAccDetailRs['account_title'] .'<br>
						  <strong>'. lethe_from_title .':</strong> '. $opAccDetailRs['sender_title'] .'<br>
						  <strong>'. lethe_from_e_mail .':</strong> '. $opAccDetailRs['sender_mail'] .'<br>
						  <strong>'. lethe_test_e_mail .':</strong> '. $opAccDetailRs['test_mail'] .'<br>
						  <strong>'. lethe_daily_send_limit .':</strong> '. $opAccDetailRs['send_mail_limit'] .' / '. $opAccDetailRs['dailySent'] .'<br>
						  <strong>'. lethe_mail_limit_per_connection .':</strong> '. $opAccDetailRs['mail_limit_per_con'] .'<br>
						  <strong>'. lethe_sending_duration .':</strong> '. $opAccDetailRs['send_mail_duration'] .' ' . lethe_sec .'<br>
						  <strong>'. lethe_e_mail_type .':</strong> '. $mailContType .'<br>
						  <strong>'. lethe_sending_method .':</strong> '. $lethe_mail_method[$opAccDetailRs['send_type']] .'<br>
					');
					}
				$opAccDetail->free();

		}
	# Test Mail
	if($ajax_part==5){
		if(demo_mode){echo('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
				$opAccDetail = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_accounts WHERE ID=". $ID ."") or die(mysqli_error());
                if(mysqli_num_rows($opAccDetail)==0){echo('<p class="text-danger">'. lethe_record_not_found .'</p>');die();}
                    $opAccDetailRs = mysqli_fetch_assoc($opAccDetail);
						
						$errText = '';
						
						if(!isset($_POST['subject']) || empty($_POST['subject'])){$errText.=lethe_please_enter_subject.'<br>';}
						if(!isset($_POST['details']) || empty($_POST['details'])){$errText.=lethe_please_enter_details.'<br>';}
						
						if($errText==''){
							
						$subDetailArr = array(0=>array(
												'subscriber_name'=>$opAccDetailRs['sender_title'],
												'subscriber_mail'=>$opAccDetailRs['test_mail'],
												'subscriber_phone'=>'Subscriber Phone',
												'subscriber_company'=>'Subscriber Company',
												)
											);
						
						$lethe = new lethe;
						$lethe->subAccID = $ID;
						$lethe->newsBody = $_POST['details'];
						$lethe->newsSubject = $_POST['subject'];
						$lethe->newsAttach = @$_POST['attach_file'];
						$lethe->subscriberDetails = $subDetailArr;
						$lethe->newsletterUnsubscribe = 'UNSUBSCRIBE URL';
						$lethe->newsletterLink = 'NEWSLETTER URL';
						$lethe->subscribers = array($opAccDetailRs['test_mail']=>$opAccDetailRs['sender_title']);
						$lethe->sendPriority = 3; # Normal for tests
						$lethe->testMode = 1;
						$lethe->send_newsletter();
						echo($lethe->errPrint);
						
						}else{
							echo($errText);
							}
				$opAccDetail->free();
						
		}}
		
	# Subscribe Form Actions
	if($ajax_part==6){
			if(!isset($_GET['subf_act'])){$subf=1;}else{$subf=intval($_GET['subf_act']);}
			
			if($subf!=0){
				$opForm = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_forms WHERE ID=". $ID ."") or die(mysqli_error());
				if(mysqli_num_rows($opForm)==0){echo('Error Occured!');die();}
				
				$opFormRs = mysqli_fetch_assoc($opForm);
				
				}
			
			# Preview
				if($subf==1){
					echo($opFormRs['form_contents']);
					}
			# Codes
				elseif($subf==2){
					echo('<label for="formKey">'.lethe_form_key.'</label> <input type="text" class="form-control" id="formKey" value="'. $opFormRs['form_code'] .'"><br><label for="codes">'.lethe_embed_code.'</label> <textarea id="codes" class="form-control" onClick="this.select();">'. $opFormRs['form_contents'] .'</textarea>');
					}
			# Delete
				elseif($subf==3){
					if(demo_mode){echo('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
						$myconn->query("DELETE FROM ". db_table_pref ."newsletter_forms WHERE ID=". $ID ."") or die(mysqli_error());
						echo('<div class="alert alert-danger"><strong>'. $opFormRs['form_name'] .'</strong> '. lethe_d_eleted_successfully .'</div>');
					}
					}
		}
		
	if($ajax_part==7){ # Connection tester
			error_reporting(0);
			$smtp_status = '<span class="text-danger">SMTP Error<span> - ';
			$pop3_status = '<span class="text-danger">POP3 Error<span> - ';
			$imap_status = '<span class="text-danger">IMAP Error<span>';
			if(@$_POST['ssl_tls']!=0){
				$chk_ssl = true;
				if(@$_POST['ssl_tls']==1){$chk_ssl_str = "/ssl";}
				else if(@$_POST['ssl_tls']==2){$chk_ssl_str = "/tls";}
				}
			else{
				$chk_ssl = false;
				$chk_ssl_str = "";
				}
			$checkconnsmtp = @fsockopen(@$_POST['smtp_host'], @$_POST['smtp_port'], $errno, $errstr, 5);
			$checkconnpop3 = @imap_open("{". @$_POST['pop3_host'] .":". @$_POST['pop3_port'] ."/pop3$chk_ssl_str}INBOX",@$_POST['pop3_user'],@$_POST['pop_pass']);
			$checkconnimap = @imap_open("{". @$_POST['imap_host'] .":". @$_POST['imap_port'] ."/imap$chk_ssl_str}INBOX",@$_POST['imap_user'],@$_POST['imap_pass']);
			if($checkconnsmtp){
				$smtp_status = '<span class="text-success">SMTP OK!<span> - ';
				}
			if($checkconnpop3){
				$pop3_status = '<span class="text-success">POP3 OK!<span> - ';
				}
			if($checkconnimap){
				$imap_status = '<span class="text-success">IMAP OK!<span> - ';
				}
			echo($smtp_status.$pop3_status.$imap_status);
		}

$myconn->close();
die();
}
# ** XMLHTTP End ****

# ** Add New User
if(isset($_POST['addUser'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = true; # Only Super Admin
		$lethe->add_user();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Edit User
if(isset($_POST['editUser'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->content_ID = $ID;
		$lethe->admin_area = true; # Only Super Admin
		$lethe->edit_user();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Edit Profile
if(isset($_POST['editProfiles'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->content_ID = admin_ID;
		$lethe->admin_area = false;
		$lethe->edit_profile();
		$errText = $lethe->errPrint;
	}
	}

# ** Add New Template
if(isset($_POST['addTemplate'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = set_template_permission; # Mark it true, if you wanna only admins can create templates.
		$lethe->add_template();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Edit Template
if(isset($_POST['editTemplate'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->content_ID = $ID;
		$lethe->admin_area = set_template_permission; # Mark it true, if you wanna only admins can edit templates.
		$lethe->edit_template();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Update Settings
if(isset($_POST['updateSettings'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = true; # Only Super Admin
		$lethe->edit_settings();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Add New Account
if(isset($_POST['addAccount'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = true; # Only Super Admin
		$lethe->add_account();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Edit Account
if(isset($_POST['editAccount'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->content_ID = $ID;
		$lethe->admin_area = true; # Only Super Admin
		$lethe->edit_account();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Update Short Codes
if(isset($_POST['editShortCodes'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = true; # Only Super Admin
		$lethe->short_codes();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Update Bounce Codes
if(isset($_POST['editBounceCodes'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = true; # Only Super Admin
		$lethe->bounce_codes();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Add Subscriber Groups
if(isset($_POST['addSubscriberGrp'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = set_subgrp_permission; # Mark it true, if you wanna only admins can edit groups.
		$lethe->edit_subscriber_group();
		$errText = $lethe->errPrint;	
	}
	}
	
# ** Add Subscribe Form 1
if(isset($_POST['addSubscribeForm1'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = true; # Mark it true, if you wanna only admins can add subscribe form.
		$lethe->add_subscribe_form('create_form');
		$errText = $lethe->errPrint;
	}
	}
	
# ** Add Subscribe Form 2
if(isset($_POST['addSubscribeForm2'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = true; # Mark it true, if you wanna only admins can add subscribe form.
		$lethe->add_subscribe_form('create_link');
		$errText = $lethe->errPrint;
	}
	}
	
# ** Add Subscribe Form 3
if(isset($_POST['addSubscribeForm3'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = true; # Mark it true, if you wanna only admins can add subscribe form.
		$lethe->add_subscribe_form('custom_forms');
		$errText = $lethe->errPrint;
	}
	}

# ** Add Subscriber
if(isset($_POST['addSubscriber'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = set_subscr_permission; # Mark it true, if you wanna only admins can add subscriber.
		$lethe->add_subscriber();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Edit Subscriber
if(isset($_POST['editSubscriber'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = set_subscr_permission; # Mark it true, if you wanna only admins can edit subsriber.
		$lethe->content_ID = intval($ID);
		$lethe->edit_subscriber();
		$errText = $lethe->errPrint;
	}
	}
	
/* Update Subscriber List */
if(isset($_POST['editSubscriberList'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		if(!isset($_POST['letheSubscr'])){$_POST['letheSubscr']=array();}else{$selSubs = $_POST['letheSubscr'];}
		
		foreach($selSubs as $k=>$v){
		
			$subIDs = intval($v);
			$myconn->query("DELETE FROM ". db_table_pref ."newsletter_tasks WHERE SID=". $subIDs ."");
			$myconn->query("DELETE FROM ". db_table_pref ."newsletter_subscribers WHERE ID=". $subIDs ."");
		
		}
		
	}
}
	
# ** Export Subscribers
if(isset($_POST['exportSubscribers'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = set_exmp_imp_permission; # Mark it true, if you wanna only admins can add subsriber.
		$lethe->export_subscribers();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Import Custom
if(isset($_POST['importSubCustom'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = set_exmp_imp_permission; # Mark it true, if you wanna only admins can add subsriber.
		$lethe->import_file_type = array("txt","csv"); # Allowed File Types
		$lethe->import_custom();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Import Wordpress
if(isset($_POST['importWordpress'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = set_exmp_imp_permission; # Mark it true, if you wanna only admins can add subsriber.
		$lethe->import_wordpress();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Import Custom CMS
if(isset($_POST['importCustomCMS'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = set_exmp_imp_permission; # Mark it true, if you wanna only admins can add subsriber.
		$lethe->import_custom_cms();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Add Newsletter
if(isset($_POST['addNewsletter'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = set_newsletter_permission; # Mark it true, if you wanna only admins can add new newsletter.
		$lethe->add_newsletter();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Edit Newsletter
if(isset($_POST['editNewsletter'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->content_ID = $ID;
		$lethe->admin_area = set_newsletter_permission; # Mark it true, if you wanna only admins can edit new newsletter.
		$lethe->edit_newsletter();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Add Autoresponder
if(isset($_POST['addAutoresponder'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = set_autoresponder_permission; # Mark it true, if you wanna only admins can add new autoresponder.
		$lethe->add_autoresponder();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Edit Autoresponder
if(isset($_POST['editAutoresponder'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->content_ID = $ID;
		$lethe->admin_area = set_autoresponder_permission; # Mark it true, if you wanna only admins can edit autoresponder.
		$lethe->edit_autoresponder();
		$errText = $lethe->errPrint;
	}
	}
	
# ** Update Blacklist
if(isset($_POST['updateBlacklist'])){
	if(demo_mode){$errText=('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_demo_mode_active .'</div>');}else{
		$lethe = new lethe;
		$lethe->admin_mode = admin_mode;
		$lethe->admin_ID = admin_ID;
		$lethe->admin_area = true; # Mark it true, if you wanna only admins can edit blacklist.
		$lethe->edit_blacklist();
		$errText = $lethe->errPrint;
	}
	}
	
?>
<!doctype html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
<title>LETHE Newsletter & Mailing System</title>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">

<link rel="stylesheet" href="bootstrap/dist/css/normalize.css">
<link rel="stylesheet" href="bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="bootstrap/dist/css/switch.css">
<link href="css/lethe.style.css" rel="stylesheet" type="text/css">
<link href="css/slicknav.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="Scripts/jquery-1.8.1.min.js"></script>
 <script src="Scripts/1.10.4-jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="Scripts/jquery.fancybox.pack.js?v=2.1.5"></script>
<script type="text/javascript" src="Scripts/jquery.slicknav.min.js"></script>

<!-- tinyMCE -->
<script>
var customMCEchar = '<?php echo($cnsLang);?>';
var customMCEHeight = '';
var customMCEWidth = '';
<?php if(minipan_on==1){echo('var customButPAN = true;');}else{echo('var customButPAN = false;');}?>
var customButCMS = true;
var customButLETHE = true;
</script>
<script src="Scripts/tinymce/tinymce.min.js"></script>
<script src="Scripts/tinymce/tinymce_custom.js"></script>
<!-- tinyMCE -->

<!-- Jqplot -->
<script type="text/javascript" src="Scripts/Chart.min.js"></script>
<!-- Jqplot -->
</head>

<body>

<div id="page-wrapper">
<div id="main-layout">
<div id="logo-area"><img src="images/lethe/lethe.png" alt="Lethe Newsletter & Mailling System"></div>
<!-- Navigation Start -->
<div id="top-navigation-bar" class="hidden-xs">
<nav class="navbar navbar-inverse" role="navigation">

    <div class="collapse navbar-collapse" id="bs-art-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>" title="<?php echo(lethe_dashboard)?>"><span class="glyphicon glyphicon-home"></span></a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-tasks"></span> <?php echo(lethe_newsletter);?> <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=1&amp;ppos=0"><span class="glyphicon glyphicon-envelope"></span> <?php echo(lethe_newsletters);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=1&amp;ppos=1"><span class="glyphicon glyphicon-plus"></span> <?php echo(lethe_create_newsletter);?></a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-repeat"></span> <?php echo(lethe_autoresponders);?> <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=6&amp;ppos=0"><span class="glyphicon glyphicon-share"></span> <?php echo(lethe_autoresponders);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=6&amp;ppos=1"><span class="glyphicon glyphicon-plus"></span> <?php echo(lethe_create_autoresponder);?></a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <?php echo(lethe_subscribers);?> <b class="caret"></b></a>
          <ul class="dropdown-menu">
	        <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=2&amp;ppos=0"><span class="glyphicon glyphicon-list"></span> <?php echo(lethe_subscriber_list);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=2&amp;ppos=1"><span class="glyphicon glyphicon-cloud"></span> <?php echo(lethe_subscriber_groups);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=2&amp;ppos=2"><span class="glyphicon glyphicon-plus"></span> <?php echo(lethe_add_subscriber);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=2&amp;ppos=5"><span class="glyphicon glyphicon-credit-card"></span> <?php echo(lethe_subscribe_forms);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=2&amp;ppos=6"><span class="glyphicon glyphicon-ban-circle"></span> <?php echo(lethe_blacklist);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=2&amp;ppos=4"><span class="glyphicon glyphicon-cloud-download"></span> <?php echo(lethe_export.' / '.lethe_import);?></a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-picture"></span> <?php echo(lethe_templates);?> <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=3&amp;ppos=0"><span class="glyphicon glyphicon-list"></span> <?php echo(lethe_template_list);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=3&amp;ppos=1"><span class="glyphicon glyphicon-plus"></span> <?php echo(lethe_create_template);?></a></li>
          </ul>
        </li>
        <?php if(admin_mode==1){?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon glyphicon-cog"></span> <?php echo(lethe_settings);?> <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=4&amp;ppos=0"><span class="glyphicon glyphicon glyphicon-cog"></span> <?php echo(lethe_general_settings);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=4&amp;ppos=1"><span class="glyphicon glyphicon-asterisk"></span> <?php echo(lethe_submission_accounts);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=4&amp;ppos=4"><span class="glyphicon glyphicon-eye-open"></span> <?php echo(lethe_users);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=4&amp;ppos=7"><span class="glyphicon glyphicon-font"></span> <?php echo(lethe_short_codes);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=4&amp;ppos=8"><span class="glyphicon glyphicon-screenshot"></span> <?php echo(lethe_bounce_catcher);?></a></li>
          </ul>
        </li>
        <?php }?>
        <?php if($pos==2 && $ppos==0 || $pos==1 && $ppos==0 || $pos==6 && $ppos==0){?><li><a href="javascript:void(0);" id="search-opener"><span class="glyphicon glyphicon-search"></span> <?php echo(lethe_search);?></a></li><?php }?>
      </ul>
        <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon glyphicon-globe"></span> <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <?php foreach($_SLNG_CNF as $v){
					echo('<li><a href="lngChange.php?l='. $v['lkey'] .'"><span class="pull-left flag flag-'. $v['lkey'] .'"></span> '. $v['lang'] .'</a></li>');
			}?>
          </ul>
        </li>
        <?php if(admin_mode!=1){?><li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=5"><span class="glyphicon glyphicon-tower"></span></a></li><?php }?>
        <li><a href="lethe.login.php?logout=yes"><span class="glyphicon glyphicon-off"></span></a></li>
        </ul>
    
    </div>
</nav>
</div>
<div id="top-navigation-bar2" class="visible-xs">
      <ul id="top-navigation-bar-small">
        <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>" title="<?php echo(lethe_dashboard)?>"><span class="glyphicon glyphicon-home"></span> <?php echo(lethe_dashboard);?></a></li>
        <li>
          <a href="#"><span class="glyphicon glyphicon-tasks"></span> <?php echo(lethe_newsletter);?></a>
          <ul>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=1&amp;ppos=0"><span class="glyphicon glyphicon-envelope"></span> <?php echo(lethe_newsletters);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=1&amp;ppos=1"><span class="glyphicon glyphicon-plus"></span> <?php echo(lethe_create_newsletter);?></a></li>
          </ul>
        </li>
        <li>
          <a href="#"><span class="glyphicon glyphicon-repeat"></span> <?php echo(lethe_autoresponders);?></a>
          <ul>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=6&amp;ppos=0"><span class="glyphicon glyphicon-share"></span> <?php echo(lethe_autoresponders);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=6&amp;ppos=1"><span class="glyphicon glyphicon-plus"></span> <?php echo(lethe_create_autoresponder);?></a></li>
          </ul>
        </li>
        <li>
          <a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo(lethe_subscribers);?></a>
          <ul>
	        <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=2&amp;ppos=0"><span class="glyphicon glyphicon-list"></span> <?php echo(lethe_subscriber_list);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=2&amp;ppos=1"><span class="glyphicon glyphicon-cloud"></span> <?php echo(lethe_subscriber_groups);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=2&amp;ppos=2"><span class="glyphicon glyphicon-plus"></span> <?php echo(lethe_add_subscriber);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=2&amp;ppos=5"><span class="glyphicon glyphicon-credit-card"></span> <?php echo(lethe_subscribe_forms);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=2&amp;ppos=6"><span class="glyphicon glyphicon-ban-circle"></span> <?php echo(lethe_blacklist);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=2&amp;ppos=4"><span class="glyphicon glyphicon-cloud-download"></span> <?php echo(lethe_export.' / '.lethe_import);?></a></li>
          </ul>
        </li>
        <li>
          <a href="#"><span class="glyphicon glyphicon-picture"></span> <?php echo(lethe_templates);?></a>
          <ul>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=3&amp;ppos=0"><span class="glyphicon glyphicon-list"></span> <?php echo(lethe_template_list);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=3&amp;ppos=1"><span class="glyphicon glyphicon-plus"></span> <?php echo(lethe_create_template);?></a></li>
          </ul>
        </li>
        <?php if(admin_mode==1){?>
        <li>
          <a href="#"><span class="glyphicon glyphicon glyphicon-cog"></span> <?php echo(lethe_settings);?></a>
          <ul>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=4&amp;ppos=0"><span class="glyphicon glyphicon glyphicon-cog"></span> <?php echo(lethe_general_settings);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=4&amp;ppos=1"><span class="glyphicon glyphicon-asterisk"></span> <?php echo(lethe_submission_accounts);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=4&amp;ppos=4"><span class="glyphicon glyphicon-eye-open"></span> <?php echo(lethe_users);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=4&amp;ppos=7"><span class="glyphicon glyphicon-font"></span> <?php echo(lethe_short_codes);?></a></li>
            <li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=4&amp;ppos=8"><span class="glyphicon glyphicon-screenshot"></span> <?php echo(lethe_bounce_catcher);?></a></li>
          </ul>
        </li>
        <?php }?>
        <?php if($pos==2 && $ppos==0 || $pos==1 && $ppos==0 || $pos==6 && $ppos==0){?><li><a href="javascript:void(0);" id="search-opener"><span class="glyphicon glyphicon-search"></span> <?php echo(lethe_search);?></a></li><?php }?>
        <ul>
        <li>
          <a href="#"><span class="glyphicon glyphicon glyphicon-globe"></span> <?php echo(lethe_language);?></a>
          <ul>
            <?php foreach($langList as $v){
					echo('<li><a href="lngChange.php?l='. $v['lkey'] .'"><span class="pull-left flag flag-'. $v['lkey'] .'"></span> '. $v['lang'] .'</a></li>');
			}?>
          </ul>
        </li>
        <?php if(admin_mode!=1){?><li><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=5"><span class="glyphicon glyphicon-tower"></span></a></li><?php }?>
        <li><a href="lethe.login.php?logout=yes"><span class="glyphicon glyphicon-off"></span> <?php echo(lethe_logout);?></a></li>
        </ul>
      </ul>
</div>
<!-- Navigation End -->

<!-- Content Start -->
<div class="panel panel-default">
  <div class="panel-body">
    
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php if($pos==0){ # Dashboard

/* Stat Gatherer */

$opStat1 = $myconn->query("SELECT 
											NEWS.ID AS NEWS_ID,
											NEWST.ID AS NEWST_ID,
											
											(SELECT COUNT(ID) FROM ". db_table_pref ."newsletter_tasks) AS totalQuoue,
											(SELECT SUM(view_hit) FROM ". db_table_pref ."newsletters) AS statOpens,
											(SELECT SUM(click_hit) FROM ". db_table_pref ."newsletters) AS statClicks,
											(SELECT SUM(bounces) FROM ". db_table_pref ."newsletters) AS statBounces,
											(SELECT COUNT(unsubscribed) FROM ". db_table_pref ."newsletter_tasks WHERE unsubscribed=1) AS statUnsubscribes,
											(SELECT COUNT(ID) FROM ". db_table_pref ."newsletters) AS totalCamp,
											(SELECT COUNT(ID) FROM ". db_table_pref ."newsletters WHERE position=0) AS totalCampPend,
											(SELECT COUNT(ID) FROM ". db_table_pref ."newsletters WHERE position=1) AS totalCampLoad,
											(SELECT COUNT(ID) FROM ". db_table_pref ."newsletters WHERE position=2) AS totalProc,
											(SELECT COUNT(ID) FROM ". db_table_pref ."newsletters WHERE position=3) AS totalStop,
											(SELECT COUNT(ID) FROM ". db_table_pref ."newsletters WHERE position=4) AS totalComp
							FROM
											". db_table_pref ."newsletters AS NEWS,
											". db_table_pref ."newsletter_tasks AS NEWST
							") or die(mysqli_error());
$opStat1Num = $opStat1->fetch_array();

$totalQuoue = $opStat1Num['totalQuoue']; // Total Tasks
$statOpens = $opStat1Num['statOpens']; // Total Opened Mails
$statClicks = $opStat1Num['statClicks']; // Total Click Clicks
$statBounces = $opStat1Num['statBounces']; // Total Bounces
$statUnsubscribes = $opStat1Num['statUnsubscribes']; // Total Unsubscribes

// Campaign Pos
$totalCamp = $opStat1Num['totalCamp'];
$totalCampPend = $opStat1Num['totalCampPend'];
$totalCampLoad = $opStat1Num['totalCampLoad'];
$totalProc = $opStat1Num['totalProc'];
$totalStop = $opStat1Num['totalStop'];
$totalComp = $opStat1Num['totalComp'];

$opPerc = percentage($statOpens,$totalQuoue, 0);
$clPerc = percentage($statClicks, $totalQuoue, 1);
$boPerc = percentage($statBounces, $totalQuoue, 1);
$unPerc = percentage($statUnsubscribes, $totalQuoue, 1);
$opStat1->free();

// Subscribers
$lastFive = "";
$getMySubs = $myconn->prepare("SELECT 	
										(SELECT count(ID) FROM ". db_table_pref ."newsletter_subscribers WHERE (activated=1) AND (DATE_FORMAT(add_date,'%m-%Y')=?)) AS SubVerif,
										(SELECT count(ID) FROM ". db_table_pref ."newsletter_subscribers WHERE (active=1) AND (DATE_FORMAT(add_date,'%m-%Y')=?)) AS SubActv,
										(SELECT count(ID) FROM ". db_table_pref ."newsletter_blacklist WHERE (DATE_FORMAT(add_date,'%m-%Y')=?)) AS SubBlk
								 FROM
										". db_table_pref ."newsletter_subscribers AS SWSB
								 WHERE
										DATE_FORMAT(SWSB.add_date,'%m-%Y')=?
								 ") or die(mysqli_error());
$sVer = '';
$sAct = '';
$sBlk = '';
for ($i = 4; $i > -1; $i--) {
	$myDate = date('m-Y', strtotime("-$i month"));
	$myMonth = date('n', strtotime("-$i month"));
	$lastFive .= '"'.$lethe_monthList['short'][$myMonth].'",';
	$getMySubs->bind_param('ssss',$myDate,$myDate,$myDate,$myDate);
	$getMySubs->execute();
	$SubVerif=null;$SubActv=null;$SubBlk=null;
	$getMySubs->bind_result($SubVerif,$SubActv,$SubBlk);
	$getMySubs->fetch();
	
	$sVer .= intval($SubVerif).',';
	$sAct .= intval($SubActv).',';
	$sBlk .= intval($SubBlk).',';
	
	}
$getMySubs->close();
$lastFive = substr($lastFive,0,-1);
$statVerfied = substr($sVer,0,-1);
$statActive = substr($sAct,0,-1);
$statBlist = substr($sBlk,0,-1);
?>
  <tr>
    <td>
        <h3 class="panel-header"><?php echo(lethe_hello . ' ' . admin_name);?></h3>
		
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="60%" valign="top">
				<div class="canvas-container">
					<canvas id="chart0" width="120" height="150"></canvas>
				</div>
				<div class="canvas-container">
					<canvas id="chart1" width="120" height="150"></canvas>
				</div>
				<div class="canvas-container">
					<canvas id="chart2" width="120" height="150"></canvas>
				</div>
				<div class="canvas-container">
					<canvas id="chart3" width="120" height="150"></canvas>
				</div>
	</td>
    <td valign="top">
		<div id="dash-box">
		<div class="dash-boxes label label-default"><span class="small"><?php echo(lethe_total_campaign);?></span><br><h4><?php echo($totalCamp);?></h4></div>
		<div class="dash-boxes label label-warning"><span class="small"><?php echo(lethe_pending);?></span><br><h4><?php echo($totalCampPend);?></h4></div>
		<div class="dash-boxes label label-info"><span class="small"><?php echo(lethe_loaded);?></span><br><h4><?php echo($totalCampLoad);?></h4></div>
		<div class="dash-boxes label label-primary"><span class="small"><?php echo(lethe_process);?></span><br><h4><?php echo($totalProc);?></h4></div>
		<div class="dash-boxes label label-danger"><span class="small"><?php echo(lethe_stopped);?></span><br><h4><?php echo($totalStop);?></h4></div>
		<div class="dash-boxes label label-success"><span class="small"><?php echo(lethe_completed);?></span><br><h4><?php echo($totalComp);?></h4></div>
		</div>
	</td>
  </tr>
  <tr>
    <td valign="top">
		<h4 class="panel-header"><?php echo(lethe_recent_campaigns);?></h4>
		<div id="countdown" class="well">
		<div style="200px;display:inline-block;">
			<h3><?php echo(lethe_next_campaign_start);?></h3>
		</div>
		<div style="250px;display:inline-block;padding-right:10px;" class="pull-right" id="countArea"></div>
		</div>
		
		<?php 
		$opNews = $myconn->query("SELECT ID,subject,launch_date,position,data_mode FROM ". db_table_pref ."newsletters WHERE (position=1 OR position=2) AND (data_mode=0) AND (launch_date>NOW()) ORDER BY launch_date ASC LIMIT 0,15") or die(mysql_error());
		?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover">
		<?php 
		$recentNews = null;
		while($opNewsRs = $opNews->fetch_assoc()){
		if(is_null($recentNews)){$recentNews=$opNewsRs['launch_date'];}
		?>
		  <tr>
			<td><a href="<?php echo($_SERVER['SCRIPT_NAME'])?>?pos=1&ppos=2&ID=<?php echo($opNewsRs['ID']);?>"><?php echo($opNewsRs['subject']);?></a></td>
			<td><span class="small pull-right"><?php echo(setMyDate($opNewsRs['launch_date'],6));?></span></td>
		  </tr>
		<?php }?>
		</table>
		<script src="Scripts/countdown.js"></script>
		<script>var myCountdown1 = new Countdown({target:'countArea', height:30, width:260, time: '<?php echo(strtotime($recentNews)-time())?>', labelText : {<?php echo('second:"'. mb_strtoupper(lethe_seconds,'UTF-8') .'",minute:"'. mb_strtoupper(lethe_minutes,'UTF-8') .'",hour:"'. mb_strtoupper(lethe_hours,'UTF-8') .'",day:"'. mb_strtoupper(lethe_day,'UTF-8') .'",month:"'. mb_strtoupper(lethe_month,'UTF-8') .'",year:"'. mb_strtoupper(lethe_year,'UTF-8') .'"');?>}, labels : {font   : "Tahoma",color  : "#000000",weight : "normal"}  });</script> 		
	</td>
    <td valign="top">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td>
				<h4 class="panel-header"><?php echo(lethe_subscribers);?></h4>
				
				<canvas id="subChart" width="400" height="150"></canvas>
				<div id="dash-sub-stat">
					<ul>
						<li><span class="label label-success"></span> <?php echo(lethe_verified);?></li>
						<li><span class="label label-info"></span> <?php echo(lethe_active);?></li>
						<li><span class="label label-default"></span> <?php echo(lethe_blacklist);?></li>
					</ul><div class="clearfix"></div>
				</div>
				
			</td>
		  </tr>
		  <tr>
			<td>
				<h4 class="panel-header"><?php echo(lethe_system_info);?></h4>
				<div class="well">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td height="35" width="45%"><strong><?php echo(lethe_default_timezone);?></strong></td>
						<td width="5%"><strong>:</strong></td>
						<td width="50%"><small><?php echo(set_def_timezone.'<br>'.date('Y-m-d H:i:s A'));?></small></td>
					  </tr>
					  <tr>
						<td height="35"><strong><?php echo(lethe_site_url);?></strong></td>
						<td><strong>:</strong></td>
						<td><input type="text" onclick="this.select();" value="<?php echo(set_site_url);?>" class="form-control input-sm" readonly></td>
					  </tr>
					  <tr>
						<td height="35"><strong><?php echo(lethe_rss_url);?></strong></td>
						<td><strong>:</strong></td>
						<td><input type="text" onclick="this.select();" value="<?php echo(set_rss_url);?>" class="form-control input-sm" readonly></td>
					  </tr>
					  <tr>
						<td height="35"><strong><?php echo(lethe_bounce_rules);?></strong></td>
						<td><strong>:</strong></td>
						<td><?php echo(cntData("SELECT ID FROM ". db_table_pref ."newsletter_bounce_catcher WHERE active=1"));?></td>
					  </tr>
					  <tr>
						<td height="35">&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					  </tr>
					</table>
				</div>

			</td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
		  </tr>
		</table>

	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>

<script type="text/javascript">
// Campaigns

$(document).ready(function(){

var data = [
[{value: 100-<?php echo($opPerc);?>, color:"#637b85",textPerc:"<?php echo($opPerc);?>%",chartLab:"<?php echo(lethe_opens);?>"},{value: <?php echo($opPerc);?>, color:"rgb(92,184,92)",textPerc:"50"}],
[{value: 100-<?php echo($clPerc);?>, color:"#637b85",textPerc:"<?php echo($clPerc);?>%",chartLab:"<?php echo(lethe_clicks);?>"},{value: <?php echo($clPerc);?>, color:"rgb(91,192,222)",textPerc:"50"}],
[{value: 100-<?php echo($boPerc);?>, color:"#637b85",textPerc:"<?php echo($boPerc);?>%",chartLab:"<?php echo(lethe_bounces);?>"},{value: <?php echo($boPerc);?>, color:"rgb(240,173,78)",textPerc:"50"}],
[{value: 100-<?php echo($unPerc);?>, color:"#637b85",textPerc:"<?php echo($unPerc);?>%",chartLab:"<?php echo(lethe_unsubscribes);?>"},{value: <?php echo($unPerc);?>, color:"rgb(217,83,79)",textPerc:"50"}]
];

			function _init(){
				for(c=0;c<=3;c++){
				  var canv = $("#chart"+c).get(0);
				  var ctx = canv.getContext("2d");
				  
				  ctx.font = '14px Arial';
				  ctx.textAlign = 'center';
				  ctx.fillStyle = '#555';
				  ctx.fillText(data[c][0].textPerc, canv.width/2, canv.height/1.9);
				  ctx.fillText(data[c][0].chartLab, canv.width/2.5, 10);
				}
			}
			
for(c=0;c<=3;c++){
		new Chart($("#chart"+c).get(0).getContext("2d")).Doughnut(data[c],{onAnimationComplete : function(){
			
				_init();
			
		}});
}

});


// Subscribers
var data2 = {
	labels : [<?php echo($lastFive);?>],
	datasets : [
		{ /* Verified */
			fillColor : "rgba(92,184,92,0.5)",
			strokeColor : "rgba(92,184,92,1)",
			data : [<?php echo($statVerfied);?>]
		},
		{ /* Active */			
			fillColor : "rgba(151,187,205,0.5)",
			strokeColor : "rgba(151,187,205,1)",
			data : [<?php echo($statActive);?>]
		},
		{ /* Blacklist */
			fillColor : "rgba(220,220,220,0.5)",
			strokeColor : "rgba(220,220,220,1)",
			data : [<?php echo($sBlk);?>]
		}
	]
}
var ctx2 = $("#subChart").get(0).getContext("2d");
new Chart($("#subChart").get(0).getContext("2d")).Bar(data2);

</script>
        
    </td>
  </tr>
<?php }elseif($pos==1){ # Newsletter?>
  <tr>
    <td>
    
    <!-- Newsletter Start -->
<!-- Date Picker -->
<link rel="stylesheet" type="text/css" href="css/jquery.datepick.css">  
<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.23.css">
<script type="text/javascript" src="Scripts/datepicker/jquery.datepick.js"></script>
<script type="text/javascript" src="Scripts/datepicker/jquery.datepick-en.js"></script>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <?php if($ppos==0){ # Newsletter List?>
          <tr>
            <td>
            <h1 class="panel-header"><?php echo(lethe_newsletters);?></h1>
<!-- Search Panel -->
<?php 
# *** Search Queries Start **
$onSrc=0;
$src = "";
$pgQuery = "";

	# Keywords
	if(isset($_GET['src_v']) && !empty($_GET['src_v'])){
			$src_v = mysql_prep(trim($_GET['src_v']));
			$src .= " AND (UPPER(subject) LIKE '%". strtoupper($src_v) ."%')";
					  $pgQuery .= "&amp;src_v=" . $src_v;
			$onSrc=1;
		}
				
	# Launch Date
	if(isset($_GET['src_ld_fr']) && isset($_GET['src_ld_to'])){
		if(!empty($_GET['src_ld_fr']) && !empty($_GET['src_ld_to'])){
			$dt_f = date('Y-m-d',strtotime($_GET['src_ld_fr']));
			$dt_t = date('Y-m-d',strtotime($_GET['src_ld_to']));
			$src .= " AND (launch_date BETWEEN '". $dt_f ."' AND '". $dt_t ."')";
					  $pgQuery .= "&amp;src_ld_fr=". mysql_prep($_GET['src_ld_fr']) ."&amp;src_ld_to=". mysql_prep($_GET['src_ld_to']) ."";
			$onSrc=1;
		}
		}
		
	# Status
	if(isset($_GET['src_status']) && is_numeric($_GET['src_status'])){
			if($_GET['src_status']!=-1){
				$src .= " AND (position=". intval($_GET['src_status']) .")";
			}
			$pgQuery .= "&amp;src_status=" . $_GET['src_status'];
			$onSrc=1;
		}

# *** Search Queries End **

# ******** Order Queries **********************
$dtOrder = "";
$qrOrder = " ORDER BY subject ASC"; # Default Ordering
$qrOrd = mysql_prep(@$_GET['qrOrd']); # Order Area like title, date
$qrOrdPos = intval(@$_GET['qrOrdPos']); # Order Pos as 1 - ASC, 2 - DESC
	
	# Order By Title
	if($qrOrd=='byTitle'){
		if($qrOrdPos==0){
			$qrOrder = " ORDER BY subject ASC";
			$dtOrder = '&amp;qrOrd=byTitle&amp;qrOrdPos=0';
			}
		elseif($qrOrdPos==1){
			$qrOrder = " ORDER BY subject DESC";
			$dtOrder = '&amp;qrOrd=byTitle&amp;qrOrdPos=1';
			}
		}
					
	# Order By Date
	if($qrOrd=='byDate'){
		if($qrOrdPos==0){
			$qrOrder = " ORDER BY launch_date ASC";
			$dtOrder = '&amp;qrOrd=byDate&amp;qrOrdPos=0';
			}
		elseif($qrOrdPos==1){
			$qrOrder = " ORDER BY launch_date DESC";
			$dtOrder = '&amp;qrOrd=byDate&amp;qrOrdPos=1';
			}
		}
	
	
# ******** Order Queries **********************
?>
<div id="search_panel">
	<div class="well">
		<form action="" method="get" name="search_form">
        <input type="hidden" name="pos" value="<?php echo($pos);?>">
        <input type="hidden" name="ppos" value="<?php echo($ppos);?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="40" width="15%"><strong><?php echo(lethe_search);?></strong></td>
    <td width="2%"><strong>:</strong></td>
    <td><div class="input-group"><input type="text" value="<?php echo(mysql_prep(@$_GET['src_v']));?>" name="src_v" class="form-control autoWidth" size="35"><span class="input-group-btn autoWidth"><button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> <?php echo(lethe_find);?></button></span></div></td>
  </tr>
  <tr>
    <td height="40"><strong><?php echo(lethe_launch_date);?></strong></td>
    <td><strong>:</strong></td>
    <td><?php echo(lethe_from);?>: <input type="text" value="<?php echo(mysql_prep(@$_GET['src_ld_fr']));?>" name="src_ld_fr" id="src_ld_fr" class="form-control autoWidth inlineBlock" placeholder="dd-mm-yyyy"> <?php echo(lethe_to);?>: <input placeholder="dd-mm-yyyy" type="text" value="<?php echo(mysql_prep(@$_GET['src_ld_to']));?>" name="src_ld_to" id="src_ld_to" class="form-control autoWidth inlineBlock"><script>$(document).ready(function(){$('#src_ld_fr').datepick({dateFormat: 'dd-mm-yyyy'});$('#src_ld_to').datepick({dateFormat: 'dd-mm-yyyy'});});</script></td>
  </tr>
  <tr>
    <td height="40"><strong><?php echo(lethe_status);?></strong></td>
    <td><strong>:</strong></td>
    <td><select name="src_status" id="src_status" class="form-control autoWidth">
    	<?php 
				echo('<option value="-1">'. lethe_all .'</option>');
			for($i=0;$i<count($lethe_status_mode);$i++){
				echo('<option value="'. $i .'"'. formSelector(@$_GET['src_status'],$i,0) .'>'. $lethe_status_mode[$i] .'</option>');
			}?>
    </select></td>
  </tr>
</table>

        </form>
    </div>
</div>
<?php if($onSrc==1){echo('<script>$("#search_panel").toggle("slideDown");</script>');} # Auto open search box if search variables defined?>
<!-- Search Panel -->
            <!-- Newsletter List Start -->
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th width="20%"><?php echo(lethe_subject);?> <a href="?<?php echo('pos='. $pos .'&amp;ppos='. $ppos .'');?>&amp;qrOrd=byTitle&amp;qrOrdPos=0<?php echo($pgQuery);?>"><span class="glyphicon glyphicon-chevron-down"></span></a><a href="?<?php echo('pos='. $pos .'&amp;ppos='. $ppos .'');?>&amp;qrOrd=byTitle&amp;qrOrdPos=1<?php echo($pgQuery);?>"><span class="glyphicon glyphicon-chevron-up"></span></a></th>
                    <th width="15%"><?php echo(lethe_launch_date);?> <a href="?<?php echo('pos='. $pos .'&amp;ppos='. $ppos .'');?>&amp;qrOrd=byDate&amp;qrOrdPos=0<?php echo($pgQuery);?>"><span class="glyphicon glyphicon-chevron-down"></span></a><a href="?<?php echo('pos='. $pos .'&amp;ppos='. $ppos .'');?>&amp;qrOrd=byDate&amp;qrOrdPos=1<?php echo($pgQuery);?>"><span class="glyphicon glyphicon-chevron-up"></span></a></th>
                    <th width="2%" align="center"><?php echo(lethe_status);?></th>
                    <th width="4%" align="center"><?php echo(lethe_total);?></th>
                    <th width="4%" align="center"><?php echo(lethe_sent);?></th>
                    <th width="4%" align="center"><?php echo(lethe_bounces);?></th>
                    <th width="4%" align="center"><?php echo(lethe_unsubscribers);?></th>
                    <th width="4%" align="center"><?php echo(lethe_views);?></th>
					<th width="4%" align="center"><?php echo(lethe_clicks);?></th>
                    <th width="10%"><?php echo(lethe_progress);?></th>
                  </tr>
                </thead>
                <tbody>
                <?php 
$limit = 25;
$pgGo = @$_GET["pgGo"];
if(empty($pgGo) or !is_numeric($pgGo)) {$pgGo = 1;}

 $count		 = mysqli_num_rows($myconn->query("SELECT * FROM ". db_table_pref ."newsletters WHERE data_mode=0 ". $src .""));
 $total_page	 = ceil($count / $limit);
 $dtStart	 = ($pgGo-1)*$limit;
 
				
				$opNews = $myconn->query("SELECT * FROM ". db_table_pref ."newsletters WHERE data_mode=0  ". $src ." ". $qrOrder ." LIMIT $dtStart,$limit") or die(mysqli_error());
				
				// mysqli disabled here
				//$cntNewsletterStat = $myconn->prepare("SELECT NWS.NID AS NWSID,
				//												(SELECT COUNT(ID) FROM ". db_table_pref ."newsletter_tasks WHERE NID=NWS.NID) AS total_entry,
				//												(SELECT COUNT(ID) FROM ". db_table_pref ."newsletter_tasks WHERE NID=NWS.NID AND sent=1) AS sent_entry,
				//												(SELECT COUNT(ID) FROM ". db_table_pref ."newsletter_tasks WHERE NID=NWS.NID AND sent=0 AND unsubscribed=0) AS unsent_entry,
				//												(SELECT COUNT(ID) FROM ". db_table_pref ."newsletter_tasks WHERE NID=NWS.NID AND unsubscribed=1) AS unsubscribed_entry
				//									FROM
				//											". db_table_pref ."newsletter_tasks AS NWS
				//									WHERE
				//											NWS.NID=?
				//									");
				
				while($opNewsRs = $opNews->fetch_assoc()){
				//$cntNewsletterStat->bind_param('i',$opNewsRs['ID']);
				//$cntNewsletterStat->execute();
				//$NWSID=null;$total_entry=0;$sent_entry=0;$unsent_entry=0;$unsubscribed_entry=0;
				//$cntNewsletterStat->bind_result($NWSID,$total_entry,$sent_entry,$unsent_entry,$unsubscribed_entry);
				//$cntNewsletterStat->fetch();
				$cntNewsletter = cntdata("SELECT ID FROM ". db_table_pref ."newsletter_tasks WHERE NID=". $opNewsRs['ID'] ."");
				$cntSent = cntData("SELECT ID,sent FROM ". db_table_pref ."newsletter_tasks WHERE sent=1 AND NID=". $opNewsRs['ID'] ."");
				$cntUSent = cntData("SELECT ID,sent,unsubscribed FROM ". db_table_pref ."newsletter_tasks WHERE sent=0 AND unsubscribed=0 AND NID=". $opNewsRs['ID'] ."");
				$cntBounces = $opNewsRs['bounces'];
				$cnsUnsubscribers = cntData("SELECT ID,unsubscribed FROM ". db_table_pref ."newsletter_tasks WHERE unsubscribed=1 AND NID=". $opNewsRs['ID'] ."");
				?>
                  <tr>
                    <td><a href="<?php echo($_SERVER['SCRIPT_NAME']);?>?pos=1&amp;ppos=2&amp;ID=<?php echo($opNewsRs['ID']);?>"><?php echo($opNewsRs['subject']);?></a></td>
                    <td><?php echo(date('d.m.Y h:i A',strtotime($opNewsRs['launch_date'])));?></td>
                    <td align="center"><?php echo(getMyActPos($opNewsRs['position']));?></td>
                    <td align="center"><?php echo($cntNewsletter);?></td>
                    <td align="center"><?php echo('<span class="text-success" data-toggle="tooltip" data-placement="top" title="'. lethe_sent .'">'.$cntSent.'</span><br><span class="text-warning" data-toggle="tooltip" data-placement="top" title="'. lethe_unsent .'">'.$cntUSent.'</span>');?></td>
                    <td align="center"><?php echo($cntBounces);?></td>
                    <td align="center"><?php echo($cnsUnsubscribers);?></td>
                    <td align="center"><?php echo($opNewsRs['view_hit']);?></td>
					<td align="center"><?php echo($opNewsRs['click_hit']);?></td>
                    <td>
						<?php 
                        $totalLoaded = $cntNewsletter;
                        $loadedQueue = $cntUSent;
                        $res = @( ( $totalLoaded - $loadedQueue ) / $totalLoaded ) * 100;
                        $res = round($res,1);
                        ?>      
                        <div class="progress active<?php if($res!=100 && $opNewsRs['position']==2){echo(' progress-striped');}?>" style="background-color:#999;">
                          <div class="progress-bar<?php echo(getMyActProg($opNewsRs['position']));?>" role="progressbar" aria-valuenow="<?php echo($res);?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo($res);?>%;">
                            <?php echo($res);?>%
                          </div>
                        </div>
                    </td>
                  </tr>
               <?php 
			   }
			   //$cntNewsletterStat->close();
			   ?>
				<?php if($total_page>1){?>
                  <tr class="non-striped">
                    <td colspan="10">&nbsp;</td>
                  </tr>
                  <tr class="non-striped">
                    <td colspan="10"><?php $pgVar='?pos='. $pos .'&ppos='. $ppos .''.$pgQuery.$dtOrder;include("inc/inc_pagination.php");?></td>
                  </tr>
                <?php }?>
                </tbody>
                </table>
				<?php $opNews->free();?>
            <!-- Newsletter List End -->
                        
            </td>
          </tr>
        <?php }elseif($ppos==1){ # Newsletter Create?>
          <tr>
            <td>
            
            	<h1 class="panel-header"><?php echo(lethe_add_newsletter);?></h1>
                <?php echo($errText);?>
            	<!-- Add Newsletter Start -->
                <form role="form" name="add_newsletterForm" id="add_newsletterForm" action="" method="post" enctype="multipart/form-data">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="23%" height="40"><strong><?php echo(lethe_submission_account);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="76%" height="40"><select name="sub_account" id="sub_account" class="form-control autoWidth inlineBlock" required>
                    <option value="0"><?php echo(lethe_choose);?></option>
                    <?php 
					if(admin_mode==2){$permSel = " AND permission=2";}else{$permSel = "";}
					$opAccs = $myconn->query("SELECT ID,account_title,sender_mail,active,permission FROM ". db_table_pref ."newsletter_accounts WHERE active=1 ". $permSel ."") or die(mysqli_error());
					while($opAccsRs = $opAccs->fetch_assoc()){
						echo('<option value="'. $opAccsRs['ID'] .'">'. $opAccsRs['account_title'] .' ('. $opAccsRs['sender_mail'] .')</option>');
						}
					$opAccs->free();
					?>
                    </select> <button type="button" class="btn btn-warning btn-sm" id="account-info" disabled><span class="glyphicon glyphicon glyphicon-info-sign"></span> <?php echo(lethe_info);?></button>
                    <div id="sub-acc-info" class="alert alert-info" style="display:none;"></div>
                    </td>
                  </tr>
                  <tr>
                    <td width="23%" height="40"><strong><?php echo(lethe_subject);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="76%" height="40"><input value="<?php echo(mysql_prep(@$_POST['subject']));?>" name="subject" type="text" id="subject" class="form-control autoWidth" size="75" required></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_group);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><select name="sub_group[]" id="sub_group" class="form-control autoWidth" size="10" style="width:500px;" multiple required>
                    <?php $opGroup = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE active=1 ORDER BY group_name ASC") or die(mysqli_error());
						while($opGroupRs = $opGroup->fetch_assoc()){
							echo('<option value="'. $opGroupRs['ID'] .'">('. cntData("SELECT ID FROM ". db_table_pref ."newsletter_subscribers WHERE GID=". $opGroupRs['ID'] ."") .') '. $opGroupRs['group_name'] .'</option>');
							}
						$opGroup->free();
					?>
                    </select></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_launch_date);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo(mysql_prep(@$_POST['launch_date']));?>" name="launch_date" type="text" id="launch_date" class="form-control autoWidth inlineBlock" size="15" placeholder="<?php echo('dd-mm-yyyy');?>" required>
                    <select name="launch_date_h" class="form-control autoWidth inlineBlock">
                    	<?php
                        	for($i=0;$i<=23;$i++){
								echo('<option value="'. addZero($i) .'"'. formSelector(@$_POST['launch_date_h'],addZero($i),0) .'>'. addZero($i) .'</option>');
								}
						?>
                    </select> : 
                    <select name="launch_date_m" class="form-control autoWidth inlineBlock">
                    	<?php
                        	for($i=0;$i<=59;$i++){
								echo('<option value="'. addZero($i) .'"'. formSelector(@$_POST['launch_date_m'],addZero($i),0) .'>'. addZero($i) .'</option>');
								}
						?>
                    </select>
                    <script>$(document).ready(function(){$('#launch_date').datepick({dateFormat: 'dd-mm-yyyy'});});</script>
                    </td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_high_importance);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-importance" data-off="danger" data-on-label="<?php echo(lethe_yes);?>" data-off-label="<?php echo(lethe_no);?>"><input name="importance" type="checkbox" id="importance" value="YES"></div></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_template);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40">
                    	<select name="temp_list" id="temp_list" class="form-control autoWidth">
                        	<option value="null"><?php echo(lethe_choose);?></option>
                            <?php $opTemps = $myconn->query("SELECT ID,title FROM ". db_table_pref ."newsletter_templates ORDER BY title ASC") or die(mysqli_error());
							while($opTempsRs = $opTemps->fetch_assoc()){
								echo('<option value="'. $opTempsRs['ID'] .'">'. $opTempsRs['title'] .'</option>');
								}
							$opTemps->free();
							?>
                        </select>
                    </td>
                  </tr>
                      <tr>
                        <td width="22%" height="35"><strong><?php echo(lethe_short_codes);?></strong></td>
                        <td width="1%" height="35"><strong>:</strong></td>
                        <td width="77%" height="35">
                        	<div class="well" id="short_code_list">
                            	<?php
                                	for($i=0;$i<count($lethe_short_codes);$i++){ # List of static codes
										echo('<div class="label label-danger" data-lethe-codes="{'. $lethe_short_codes[$i] .'}" data-lethe-code-field="details">{'. $lethe_short_codes[$i] .'}</div> ');
										}
									$opCodes = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_codes ORDER BY lethe_code ASC") or die(mysqli_error());
									while($opCodesRs = $opCodes->fetch_assoc()){
										echo('<div class="label label-info" data-lethe-codes="{'. $opCodesRs['lethe_code'] .'}" data-lethe-code-field="details">{'. $opCodesRs['lethe_code'] .'}</div> ');
										}
									$opCodes->free();
								?>
                            </div>
                        </td>
                      </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_details);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><textarea name="details" id="details" class="mceEditor"></textarea></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_file);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="input-group">
              <input name="attach_file" class="form-control" type="text" id="attach_file" size="40" placeholder="http://">
			  <?php 
			  if(minipan_on==1){
				echo('
              <span class="input-group-btn">
                <button data-pan-model="fancybox" data-pan-field="attach_file" data-pan-link="default" data-pan-platform="normal" class="btn btn-default minipan" type="button">miniPan</button>
              </span>
				');
			  }
			  ?>
              </div></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_web_option);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-onweb" data-off="danger" data-on-label="<?php echo(lethe_on);?>" data-off-label="<?php echo(lethe_off);?>"><input name="onweb" type="checkbox" id="onweb" value="YES" checked></div></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><button type="button" name="prev" id="prev" class="fancybox2 btn btn-warning"><?php echo(lethe_preview);?></button> <button type="submit" name="addNewsletter" value="addNewsletter" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> <?php echo(lethe_add_newsletter);?></button> <button type="button" id="test-sender" data-loading-text="<?php echo(lethe_sending);?>..." class="btn btn-success"><span class="glyphicon glyphicon-send"></span> <?php echo(lethe_send_test);?></button><div id="test-result" class="inlineBlock pull-right"></div></td>
                  </tr>
                </table>
                </form>
                <script>
					// Template Loader
                	$("#temp_list").change(function(){
						if($(this).val()!="null"){
							
							var editor = tinymce.get('details');
							var content = editor.getContent();

								$.ajax({
								  url: '<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=3&ID=')?>'+$(this).val(),
								  cache: false,
								  success: function(html){
									editor.setContent(html);
								  }
								});
							}
						});
						
					// Submission Account Info Loader
                	$("#sub_account").change(function(){
						if($(this).val()!="0"){
							$("#account-info").prop('disabled',false);
							getAjax('#sub-acc-info','<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=4&ID=')?>'+$(this).val(),'<?php echo(lethe_loading);?>');					
							}else{
								$("#account-info").prop('disabled',true);
								$("#sub-acc-info").hide();
								}
						});
					
					$("#account-info").click(function() {
					$("#sub-acc-info").toggle( "fast", function() {});
					});
					
					// Send Test
					$("#test-sender").click(function() {
						var btn = $(this);
						btn.button('loading');
						tinyMCE.triggerSave();
						var serializedData = $("#add_newsletterForm").serialize();
						
						$.ajax({
								url: '<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=5&ID=')?>'+$("#sub_account").val(),
								type: 'POST',
								data: serializedData ,
								success: function (response) {
									btn.button('reset');
									$('#test-result').html(response);
								},
								error: function () {

								}
							}); 
						});
						
                </script>
                <!-- Add Newsletter End -->
            
            </td>
          </tr>
        <?php }elseif($ppos==2){ # Newsletter Edit?>
          <tr>
            <td>
            	<!-- Edit Newsletter Start -->
                <h1 class="panel-header"><?php echo(lethe_edit_newsletter);?></h1>
                <?php echo($errText);?>
                <?php $opNews = $myconn->query("SELECT * FROM ". db_table_pref ."newsletters WHERE ID=". $ID ." AND data_mode=0") or die(mysqli_error());
				if(mysqli_num_rows($opNews)==0){echo('<div class="alert alert-info">' .lethe_record_not_found.'</div>');}else{
					$opNewsRs = $opNews->fetch_assoc();
				?>
                <form role="form" name="edit_newsletterForm" id="edit_newsletterForm" action="" method="post" enctype="multipart/form-data">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="23%" height="40"><strong><?php echo(lethe_submission_account);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="76%" height="40"><select name="sub_account" id="sub_account" class="form-control autoWidth inlineBlock" required>
                    <option value="0"><?php echo(lethe_choose);?></option>
                    <?php 
					if(admin_mode==2){$permSel = " AND permission=2";}else{$permSel = "";}
					$opAccs = $myconn->query("SELECT ID,account_title,sender_mail,active,permission FROM ". db_table_pref ."newsletter_accounts WHERE active=1 ". $permSel ."") or die(mysqli_error());
					while($opAccsRs = $opAccs->fetch_assoc()){
						echo('<option value="'. $opAccsRs['ID'] .'"'. formSelector($opNewsRs['SUID'],$opAccsRs['ID'],0) .'>'. $opAccsRs['account_title'] .' ('. $opAccsRs['sender_mail'] .')</option>');
						}
					$opAccs->free();
					?>
                    </select> <button type="button" class="btn btn-warning btn-sm" id="account-info" disabled><span class="glyphicon glyphicon glyphicon-info-sign"></span> <?php echo(lethe_info);?></button>
                    <div id="sub-acc-info" class="alert alert-info" style="display:none;"></div>
                    </td>
                  </tr>
                  <tr>
                    <td width="23%" height="40"><strong><?php echo(lethe_subject);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="76%" height="40"><input value="<?php echo($opNewsRs['subject']);?>" name="subject" type="text" id="subject" class="form-control autoWidth" size="75" required></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_process);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40">
                    	<select name="newsletter_proc" id="newsletter_proc" class="form-control autoWidth">
                            <?php for($i=0;$i<count($lethe_status_mode);$i++){
								echo('<option value="'. $i .'"'. formSelector($i,$opNewsRs['position'],0) .'>'. $lethe_status_mode[$i] .'</option>');
								}
							?>
                        </select>
                    </td>
                  </tr>
                  <tr>
                    <td height="40" valign="top"><strong><?php echo(lethe_subscriber_groups);?></strong></td>
                    <td height="40" valign="top"><strong>:</strong></td>
                    <td height="40">
						<p><?php echo('<span class="label label-success">' . cntData("SELECT NID FROM ". db_table_pref ."newsletter_tasks WHERE NID=". $opNewsRs['ID'] ."") . ' '. lethe_subscriber_loaded .'</span> ');?></p>
                        
                        <!-- Groups -->
                        <table width="600" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="45%">
                            	<?php
								$group_list =  $opNewsRs['groups'];
								$unselected = " AND (ID<>". str_replace(',',' AND ID<>',$group_list) .")";
								$selected = " AND (ID=". str_replace(',',' OR ID=',$group_list) .")";;
								?>
                            	<h4 class="text-danger"><?php echo(lethe_groups);?></h4>
                                <select onBlur="this.select()" name="sub_group2[]" id="sub_group2" class="form-control autoWidth" size="10" style="width:100%;" multiple>
                                <?php $opGroup = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE active=1 ". $unselected ." ORDER BY group_name ASC") or die(mysqli_error());
                                    while($opGroupRs = $opGroup->fetch_assoc()){
                                        echo('<option value="'. $opGroupRs['ID'] .'" selected>('. cntData("SELECT ID FROM ". db_table_pref ."newsletter_subscribers WHERE GID=". $opGroupRs['ID'] ."") .') '. $opGroupRs['group_name'] .'</option>');
                                        }
									$opGroup->free();
                                ?>
                                </select>
                            </td>
                            <td width="10%" align="center">
                            	<button type="button" onClick="listbox_moveacross('sub_group2', 'sub_group');" name="selectMov1" class="btn btn-default"><span class="glyphicon glyphicon-chevron-right"></span></button>
                                <button type="button" onClick="listbox_moveacross('sub_group', 'sub_group2');" name="selectMov2" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span></button>
                            </td>
                            <td width="45%">
                            	<h4 class="text-success"><?php echo(lethe_s_elected_groups);?></h4>
                                <select onBlur="this.select()" name="sub_group[]" id="sub_group" class="form-control autoWidth" size="10" style="width:100%;" multiple>
                                <?php $opGroup = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE active=1 ". $selected ." ORDER BY group_name ASC") or die(mysqli_error());
                                    while($opGroupRs = $opGroup->fetch_assoc()){
                                        echo('<option value="'. $opGroupRs['ID'] .'" selected>('. cntData("SELECT ID FROM ". db_table_pref ."newsletter_subscribers WHERE GID=". $opGroupRs['ID'] ."") .') '. $opGroupRs['group_name'] .'</option>');
                                        }
									$opGroup->free();
                                ?>
                                </select>
                            </td>
                          </tr>
                        </table>
                        <!-- Groups -->
                        
                    </td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_launch_date);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo(date('d-m-Y',strtotime($opNewsRs['launch_date'])));?>" name="launch_date" type="text" id="launch_date" class="form-control autoWidth inlineBlock" size="15" placeholder="<?php echo('dd-mm-yyyy');?>" required>
                    <select name="launch_date_h" class="form-control autoWidth inlineBlock">
                    	<?php
                        	for($i=0;$i<=23;$i++){
								echo('<option value="'. addZero($i) .'"'. formSelector(date('H',strtotime($opNewsRs['launch_date'])),addZero($i),0) .'>'. addZero($i) .'</option>');
								}
						?>
                    </select> : 
                    <select name="launch_date_m" class="form-control autoWidth inlineBlock">
                    	<?php
                        	for($i=0;$i<=59;$i++){
								echo('<option value="'. addZero($i) .'"'. formSelector(date('i',strtotime($opNewsRs['launch_date'])),addZero($i),0) .'>'. addZero($i) .'</option>');
								}
						?>
                    </select>
                    <script>$(document).ready(function(){$('#launch_date').datepick({dateFormat: 'dd-mm-yyyy'});});</script>
                    </td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_high_importance);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-importance" data-off="danger" data-on-label="<?php echo(lethe_yes);?>" data-off-label="<?php echo(lethe_no);?>"><input name="importance" type="checkbox" id="importance" value="YES"<?php echo(formSelector($opNewsRs['priotity'],1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_template);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40">
                    	<select name="temp_list" id="temp_list" class="form-control autoWidth">
                        	<option value="null"><?php echo(lethe_choose);?></option>
                            <?php $opTemps = $myconn->query("SELECT ID,title FROM ". db_table_pref ."newsletter_templates ORDER BY title ASC") or die(mysqli_error());
							while($opTempsRs = $opTemps->fetch_assoc()){
								echo('<option value="'. $opTempsRs['ID'] .'">'. $opTempsRs['title'] .'</option>');
								}
							$opTemps->free();
							?>
                        </select>
                    </td>
                  </tr>
                      <tr>
                        <td width="22%" height="35"><strong><?php echo(lethe_short_codes);?></strong></td>
                        <td width="1%" height="35"><strong>:</strong></td>
                        <td width="77%" height="35">
                        	<div class="well" id="short_code_list">
                            	<?php
                                	for($i=0;$i<count($lethe_short_codes);$i++){ # List of static codes
										echo('<div class="label label-danger" data-lethe-codes="{'. $lethe_short_codes[$i] .'}" data-lethe-code-field="details">{'. $lethe_short_codes[$i] .'}</div> ');
										}
									$opCodes = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_codes ORDER BY lethe_code ASC") or die(mysqli_error());
									while($opCodesRs = $opCodes->fetch_assoc()){
										echo('<div class="label label-info" data-lethe-codes="{'. $opCodesRs['lethe_code'] .'}" data-lethe-code-field="details">{'. $opCodesRs['lethe_code'] .'}</div> ');
										}
									$opCodes->free();
								?>
                            </div>
                        </td>
                      </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_details);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><textarea name="details" id="details" class="mceEditor"><?php echo($opNewsRs['details']);?></textarea></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_file);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="input-group">
              <input name="attach_file" value="<?php echo($opNewsRs['file_url']);?>" class="form-control" type="text" id="attach_file" size="40" placeholder="http://">
			  <?php 
			  if(minipan_on==1){
				echo('
              <span class="input-group-btn">
                <button data-pan-model="fancybox" data-pan-field="attach_file" data-pan-link="default" data-pan-platform="normal" class="btn btn-default minipan" type="button">miniPan</button>
              </span>
				');
			  }
			  ?>
              </div></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_web_option);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-onweb" data-off="danger" data-on-label="<?php echo(lethe_on);?>" data-off-label="<?php echo(lethe_off);?>"><input name="onweb" type="checkbox" id="onweb" value="YES"<?php echo(formSelector($opNewsRs['web_view'],1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_reset);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input type="checkbox" value="YES" name="res" id="resNewsletter" onclick="if(this.checked==true){return confirm('<?php echo(lethe_all_tasks_will_marked_unsent);?>');}"> <label for="resNewsletter"><?php echo(lethe_yes);?></label></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_d_elete);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input type="checkbox" value="YES" name="del" id="delNewsletter" onclick="if(this.checked==true){return confirm('<?php echo(lethe_are_you_sure_to_d_elete);?>');}"> <label for="delNewsletter"><?php echo(lethe_yes);?></label></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><button type="button" name="prev" id="prev" class="fancybox2 btn btn-warning"><?php echo(lethe_preview);?></button> <button type="submit" name="editNewsletter" value="editNewsletter" class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> <?php echo(lethe_edit_newsletter);?></button> <button type="button" id="test-sender" data-loading-text="<?php echo(lethe_sending);?>..." class="btn btn-success"><span class="glyphicon glyphicon-send"></span> <?php echo(lethe_send_test);?></button><div id="test-result" class="inlineBlock pull-right"></div></td>
                  </tr>
                </table>
                </form>
                <script>
					// Template Loader
                	$("#temp_list").change(function(){
						if($(this).val()!="null"){
							
							var editor = tinymce.get('details');
							var content = editor.getContent();

								$.ajax({
								  url: '<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=3&ID=')?>'+$(this).val(),
								  cache: false,
								  success: function(html){
									editor.setContent(html);
								  }
								});
							}
						});
											
					// Submission Account Info Loader
                	$("#sub_account").change(function(){
						if($(this).val()!="0"){
							$("#account-info").prop('disabled',false);
							getAjax('#sub-acc-info','<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=4&ID=')?>'+$(this).val(),'<?php echo(lethe_loading);?>');					
							}else{
								$("#account-info").prop('disabled',true);
								$("#sub-acc-info").hide();
								}
						});
					
					$("#account-info").click(function() {
					$("#sub-acc-info").toggle( "fast", function() {});
					});
					
					// Send Test
					$("#test-sender").click(function() {
						var btn = $(this);
						btn.button('loading');
						tinyMCE.triggerSave();
						var serializedData = $("#edit_newsletterForm").serialize();
						
						$.ajax({
								url: '<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=5&ID=')?>'+$("#sub_account").val(),
								type: 'POST',
								data: serializedData ,
								success: function (response) {
									btn.button('reset');
									$('#test-result').html(response);
								},
								error: function () {

								}
							}); 
						});
						
						// Account Loader
						$(document).ready(function(){
							 if($("#sub_account").val()!=0){
								 $("#account-info").prop('disabled',false);
								 getAjax('#sub-acc-info','<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=4&ID='.$opNewsRs['SUID'])?>','<?php echo(lethe_loading);?>');
								 }
						});
						
						// Group Selector
						$(document).ready(function(){
							$('#edit_newsletterForm').submit(function() {
								listbox_selectall('sub_group', true);
								listbox_selectall('sub_group2', true);
								return true; // return false to cancel form action
							});
						});
                </script>
                <?php $opNews->free();}?>
                <!-- Edit Newsletter End -->
            </td>
          </tr>
        <?php }?>
        </table>
        
    <!-- Newsletter End -->
    
    </td>
  </tr>
<?php }elseif($pos==2){ # Subscribers?>
  <tr>
    <td>

    <!-- Subscribers Start -->
    
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <?php if($ppos==0){ # Subscribers List?>
          <tr>
            <td>
            <!-- Subscriber List Start -->
<h1 class="panel-header"><?php echo(lethe_subscribers);?></h1>

<!-- Search Panel -->
<?php 
# *** Search Queries Start **
$onSrc=0;
$src = "";
$pgQuery = "";

	# Keywords
	if(isset($_GET['src_v']) && !empty($_GET['src_v'])){
			$src_v = mysql_prep(trim($_GET['src_v']));
			$src .= " AND (UPPER(sub_name) LIKE '%". strtoupper($src_v) ."%' 
					  OR UPPER(sub_mail) LIKE '%". strtoupper($src_v) ."%'
					  OR UPPER(sub_phone) LIKE '%". strtoupper($src_v) ."%' 
					  OR UPPER(sub_company) LIKE '%". strtoupper($src_v) ."%')";
					  $pgQuery .= "&amp;src_v=" . $src_v;
			$onSrc=1;
		}
		
	# Group
	if(isset($_GET['src_grp']) && intval($_GET['src_grp'])!=0){
			$src_grp = mysql_prep(intval($_GET['src_grp']));
			$src .= " AND (GID=". $src_grp .")";
					  $pgQuery .= "&amp;src_grp=" . $src_grp;
			$onSrc=1;
		}
		
	# Activation
	if(isset($_GET['src_actn']) && intval($_GET['src_actn'])!=0){
			$src_actn = mysql_prep(intval($_GET['src_actn']));
			if($src_actn==1){$src .= " AND (activated=1)";}else{$src .= " AND (activated=0)";}
			$pgQuery .= "&amp;src_actn=" . $src_actn;
			$onSrc=1;
		}
		
	# Active
	if(isset($_GET['src_actv']) && intval($_GET['src_actv'])!=0){
			$src_actv = mysql_prep(intval($_GET['src_actv']));
			if($src_actv==1){$src .= " AND (active=1)";}else{$src .= " AND (active=0)";}
			$pgQuery .= "&amp;src_actv=" . $src_actv;
			$onSrc=1;
		}
		
	# First Char
	if(isset($_GET['src_c']) && !empty($_GET['src_c'])){
			$src_c = mysql_prep(trim($_GET['src_c']));
			if($src_c!='0-9'){
				$src .= " AND (UPPER(LEFT(sub_name,1))='". $src_c ."')";
			}else{
				$src .= " AND (LEFT(sub_name,1) REGEXP '^[0-9]')";
				}
			$pgQuery = "&amp;src_c=" . $src_c;
		}

# *** Search Queries End **

# ******** Order Queries **********************
$dtOrder = "";
$qrOrder = " ORDER BY sub_name ASC"; # Default Ordering
$qrOrd = mysql_prep(@$_GET['qrOrd']); # Order Area like title, date
$qrOrdPos = intval(@$_GET['qrOrdPos']); # Order Pos as 1 - ASC, 2 - DESC
	
	# Order By Title
	if($qrOrd=='byTitle'){
		if($qrOrdPos==0){
			$qrOrder = " ORDER BY sub_name ASC";
			$dtOrder = '&amp;qrOrd=byTitle&amp;qrOrdPos=0';
			}
		elseif($qrOrdPos==1){
			$qrOrder = " ORDER BY sub_name DESC";
			$dtOrder = '&amp;qrOrd=byTitle&amp;qrOrdPos=1';
			}
		}
					
	# Order By Date
	if($qrOrd=='byDate'){
		if($qrOrdPos==0){
			$qrOrder = " ORDER BY add_date ASC";
			$dtOrder = '&amp;qrOrd=byDate&amp;qrOrdPos=0';
			}
		elseif($qrOrdPos==1){
			$qrOrder = " ORDER BY add_date DESC";
			$dtOrder = '&amp;qrOrd=byDate&amp;qrOrdPos=1';
			}
		}
	
	# Order By Active
	if($qrOrd=='byActive'){
		if($qrOrdPos==0){
			$qrOrder = " ORDER BY active ASC";
			$dtOrder = '&amp;qrOrd=byActive&amp;qrOrdPos=0';
			}
		elseif($qrOrdPos==1){
			$qrOrder = " ORDER BY active DESC";
			$dtOrder = '&amp;qrOrd=byActive&amp;qrOrdPos=1';
			}
		}
	
	
# ******** Order Queries **********************
?>
<div id="search_panel">
	<div class="well">
		<form action="" method="get" name="search_form">
        <input type="hidden" name="pos" value="<?php echo($pos);?>">
        <input type="hidden" name="ppos" value="<?php echo($ppos);?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="40" width="15%"><strong><?php echo(lethe_search);?></strong></td>
    <td width="2%"><strong>:</strong></td>
    <td><div class="input-group"><input type="text" value="<?php echo(mysql_prep(@$_GET['src_v']));?>" name="src_v" class="form-control autoWidth" size="35"><span class="input-group-btn autoWidth"><button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> <?php echo(lethe_find);?></button></span></div></td>
  </tr>
  <tr>
    <td height="40"><strong><?php echo(lethe_group);?></strong></td>
    <td><strong>:</strong></td>
    <td><select name="src_grp" id="src_grp" class="form-control autoWidth">
                    <option value="0">-- <?php echo(lethe_all);?></option>
                    <?php $opGroup = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups ORDER BY group_name ASC") or die(mysqli_error());
						while($opGroupRs = $opGroup->fetch_assoc()){
							echo('<option value="'. $opGroupRs['ID'] .'"'. formSelector(intval(@$_POST['sub_group']),$opGroupRs['ID'],0) .'>'. $opGroupRs['group_name'] .'</option>');
							}
						$opGroup->free();
					?>
                    </select></td>
  </tr>
  <tr>
    <td height="40"><strong><?php echo(lethe_activation);?></strong></td>
    <td><strong>:</strong></td>
    <td><select name="src_actn" id="src_actn" class="form-control autoWidth">
    	<option value="0">-- <?php echo(lethe_all);?></option>
        <option value="1"><?php echo(lethe_activated);?></option>
        <option value="2"><?php echo(lethe_non_activated);?></option>
    </select></td>
  </tr>
  <tr>
    <td height="40"><strong><?php echo(lethe_active);?></strong></td>
    <td><strong>:</strong></td>
    <td><select name="src_actv" id="src_actv" class="form-control autoWidth">
    	<option value="0">-- <?php echo(lethe_all);?></option>
        <option value="1"><?php echo(lethe_active);?></option>
        <option value="2"><?php echo(lethe_inactive);?></option>
    </select></td>
  </tr>
  <tr>
    <td height="40"><strong><?php echo(lethe_by_first_character);?></strong></td>
    <td><strong>:</strong></td>
    <td><?php
$chrs = '<div id="search-chrs">';
for( $i = 65; $i < 91; $i++){
        $chrs .= '<a href="?pos='. $pos .'&amp;ppos='. $ppos .'&amp;src_c='. chr($i) .'"'. formSelector(chr($i),mysql_prep(@$_GET['src_c']),3) .'>'. chr($i) .'</a> ';
}
$chrs .= '<a href="?pos='. $pos .'&amp;ppos='. $ppos .'&amp;src_c=0-9"'. formSelector(mysql_prep(@$_GET['src_c']),'0-9',3) .'>0-9</a> ';
$chrs .= '</div>';
echo $chrs;
?></td>
  </tr>
</table>

        </form>
    </div>
</div>
<?php if($onSrc==1){echo('<script>$("#search_panel").toggle("slideDown");</script>');} # Auto open search box if search variables defined?>
<!-- Search Panel -->
<form name="letheSubscribers" action="" method="POST">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped">
<thead>
  <tr>
	<th><input type="checkbox" value="" id="checkedAll"></th>
    <th width="20%"><?php echo(lethe_name);?> <a href="?<?php echo('pos='. $pos .'&amp;ppos='. $ppos .'');?>&amp;qrOrd=byTitle&amp;qrOrdPos=0<?php echo($pgQuery);?>"><span class="glyphicon glyphicon-chevron-down"></span></a><a href="?<?php echo('pos='. $pos .'&amp;ppos='. $ppos .'');?>&amp;qrOrd=byTitle&amp;qrOrdPos=1<?php echo($pgQuery);?>"><span class="glyphicon glyphicon-chevron-up"></span></a></th>
    <th width="20%"><?php echo(lethe_e_mail);?></th>
    <th width="20%"><?php echo(lethe_group);?></th>
    <th width="5%"><?php echo(lethe_activation);?></th>
    <th width="10%"><?php echo(lethe_active);?> <a href="?<?php echo('pos='. $pos .'&amp;ppos='. $ppos .'');?>&amp;qrOrd=byActive&amp;qrOrdPos=0<?php echo($pgQuery);?>"><span class="glyphicon glyphicon-chevron-down"></span></a><a href="?<?php echo('pos='. $pos .'&amp;ppos='. $ppos .'');?>&amp;qrOrd=byActive&amp;qrOrdPos=1<?php echo($pgQuery);?>"><span class="glyphicon glyphicon-chevron-up"></span></a></th>
    <th width="20%"><?php echo(lethe_date);?> <a href="?<?php echo('pos='. $pos .'&amp;ppos='. $ppos .'');?>&amp;qrOrd=byDate&amp;qrOrdPos=0<?php echo($pgQuery);?>"><span class="glyphicon glyphicon-chevron-down"></span></a><a href="?<?php echo('pos='. $pos .'&amp;ppos='. $ppos .'');?>&amp;qrOrd=byDate&amp;qrOrdPos=1<?php echo($pgQuery);?>"><span class="glyphicon glyphicon-chevron-up"></span></a></th>
  </tr>
</thead>
<tbody>
<?php 
$limit = 25;
$pgGo = @$_GET["pgGo"];
if(empty($pgGo) or !is_numeric($pgGo)) {$pgGo = 1;}

 $count		 = mysqli_num_rows($myconn->query("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE ID>0 ". $src .""));
 $total_page	 = ceil($count / $limit);
 $dtStart	 = ($pgGo-1)*$limit;
 
$opSubscibers = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE ID>0 ". $src ." ". $qrOrder ." LIMIT $dtStart,$limit") or die(mysqli_error());
if(mysqli_num_rows($opSubscibers)==0){echo('<tr class="non-striped"><td colspan="7"><div class="alert alert-info">'. lethe_record_not_found .'</div></td></tr>');}
while($opSubscibersRs = $opSubscibers->fetch_assoc()){?>
  <tr class="tbl-cl-hvr">
	<td class="tbl-brd-rgt"><input class="checkSingle" type="checkbox" name="letheSubscr[]" value="<?php echo($opSubscibersRs['ID']);?>"></td>
    <td class="tbl-brd-rgt"><a href="?pos=2&amp;ppos=3&amp;ID=<?php echo($opSubscibersRs['ID']);?>" title="<?php echo($opSubscibersRs['sub_name']);?>"><?php if(empty($opSubscibersRs['sub_name'])){echo('{'.lethe_no_name.'}');}else{echo($opSubscibersRs['sub_name']);}?></a></td>
    <td class="tbl-brd-rgt"><?php echo($opSubscibersRs['sub_mail']);?></td>
    <td class="tbl-brd-rgt"><small><?php echo(getSubscriber($opSubscibersRs['GID'],0));?></small></td>
    <td class="tbl-brd-rgt" align="center"><?php if($opSubscibersRs['activated']==0){echo('<span class="glyphicon glyphicon-unchecked errorRed"></span>');}else{echo('<span class="glyphicon glyphicon-check errorGreen"></span>');}?></td>
    <td class="tbl-brd-rgt" align="center"><?php if($opSubscibersRs['active']==0){echo('<span class="glyphicon glyphicon-unchecked errorRed"></span>');}else{echo('<span class="glyphicon glyphicon-check errorGreen"></span>');}?></td>
    <td><small><?php echo(setMyDate($opSubscibersRs['add_date'],6));?></small></td>
  </tr>
<?php }?>

<?php if($total_page>1){?>
  <tr class="non-striped">
    <td colspan="7">&nbsp;</td>
  </tr>
  <tr class="non-striped">
    <td colspan="7"><?php $pgVar='?pos='. $pos .'&ppos='. $ppos .''.$pgQuery.$dtOrder;include("inc/inc_pagination.php");?></td>
  </tr>
<?php }
$opSubscibers->free();
?>
</tbody>
</table>
<hr>
<button type="submit" name="editSubscriberList" class="btn btn-primary"><?php echo(lethe_u_pdate);?></button>
</form>
<script type="text/javascript">
	$(document).ready(function(){
	/* Checkbox Selector */
	  $("#checkedAll").change(function(){
		if(this.checked){
		  $(".checkSingle").each(function(){
			this.checked=true;
		  })              
		}else{
		  $(".checkSingle").each(function(){
			this.checked=false;
		  })              
		}
	  });

	  $(".checkSingle").click(function () {
		if ($(this).is(":checked")){
		  var isAllChecked = 0;
		  $(".checkSingle").each(function(){
			if(!this.checked)
			   isAllChecked = 1;
		  })              
		  if(isAllChecked == 0){ $("#checkedAll").prop("checked", true); }     
		}
		else {
		  $("#checkedAll").prop("checked", false);
		}
	  });
	});
</script>

            <!-- Subscriber List End -->
            </td>
          </tr>
        <?php }elseif($ppos==1){ # Subscribers Groups?>
          <tr>
            <td>
            
            	<h1 class="panel-header"><?php echo(lethe_subscriber_groups);?></h1>
                <?php echo($errText);?>
            	<!-- Subscriber Groups Start -->
                <form role="form" name="subscrForm" action="" method="post">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped">
  <thead>
  <tr>
  	<th width="20">#</th>
    <th width="20"><?php echo(lethe_d_elete);?></th>
    <th><?php echo(lethe_subscribers);?></th>
    <th><?php echo(lethe_group);?></th>
    <th><?php echo(lethe_active);?></th>
  </tr>
  </thead>
  <tbody>
  <?php 
  $i = 1;
  $groups = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups ORDER BY group_name ASC") or die(mysqli_error());
  while($groupsRs = $groups->fetch_assoc()){
	  if(!empty($groupsRs['icon'])){$icon_th='<div class="col-xs-5 col-md-2"><a href="javascript:void(0);" class="thumbnail"><img src="'. $groupsRs['icon'] .'" alt=""></a></div>';}else{$icon_th='';}
	  ?>
  <tr>
  	<td><?php echo($i);?></td>
    <td><input onclick="if(this.checked==true){return confirm('<?php echo(lethe_all_sub_entries_will_be_removed.'!\n'.lethe_are_you_sure_to_d_elete);?>');}" type="checkbox" name="delCat<?php echo($i);?>" value="YES"><input type="hidden" name="ID<?php echo($i);?>" value="<?php echo($groupsRs['ID']);?>"></td>
    <td><span class="label label-info font12px"><?php echo(cntData("SELECT ID FROM ". db_table_pref ."newsletter_subscribers WHERE GID=". $groupsRs['ID'] .""));?></span></td>
    <td><input type="text" name="subscrCat<?php echo($i);?>" value="<?php echo($groupsRs['group_name']);?>" class="form-control"></td>
    <td><div class="make-switch switch-mini" data-on="success" id="switch-active<?php echo($i);?>" data-off="danger" data-on-label="<?php echo(lethe_yes);?>" data-off-label="<?php echo(lethe_no);?>"><input type="checkbox" name="active<?php echo($i);?>" value="YES"<?php echo(formSelector($groupsRs['active'],1,1));?>></div></td>
  </tr>
  <?php $i++;}
  $groups->free();
  ?>
  <tr>
  	<td></td>
    <td></td>
    <td><input type="hidden" name="total_rec" value="<?php echo(intval($i-1));?>"></td>
    <td><input type="text" name="subscrCat_new" placeholder="<?php echo(lethe_new_group_name);?>" value="" class="form-control"></td>
    <td><div class="make-switch switch-mini" data-on="success" id="switch-active-new" data-off="danger" data-on-label="<?php echo(lethe_yes);?>" data-off-label="<?php echo(lethe_no);?>"><input type="checkbox" name="active_new" value="YES"></div></td>
  </tr>
  <tr class="non-striped">
    <td colspan="6">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="6"><button name="addSubscriberGrp" value="addSubscriberGrp" class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-refresh"></span> <?php echo(lethe_u_pdate_group);?></button></td>
  </tr>
  </tbody>
</table>
                </form>
                <!-- Subscriber Groups End -->
            
            </td>
          </tr>
        <?php }elseif($ppos==2){ # Add Subscriber?>
          <tr>
            <td>
<!-- Date Picker -->
<link rel="stylesheet" type="text/css" href="css/jquery.datepick.css">  
<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.23.css">
<script type="text/javascript" src="Scripts/datepicker/jquery.datepick.js"></script>
<script type="text/javascript" src="Scripts/datepicker/jquery.datepick-en.js"></script>
            	<h1 class="panel-header"><?php echo(lethe_add_subscriber);?></h1>
                <?php echo($errText);?>
            	<!-- Add Subscriber Start -->
                <form role="form" name="add_subscrForm" action="" method="post">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="23%" height="40"><strong><?php echo(lethe_name);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="76%" height="40"><input name="sub_name" type="text" id="sub_name" class="form-control autoWidth" size="35"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_e_mail);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input name="sub_mail" type="text" id="sub_mail" class="form-control autoWidth" size="35"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_group);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><select name="sub_group" id="sub_group" class="form-control autoWidth">
                    <option value="0">-- <?php echo(lethe_choose);?></option>
                    <?php $opGroup = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE active=1 ORDER BY group_name ASC") or die(mysqli_error());
						while($opGroupRs = $opGroup->fetch_assoc()){
							echo('<option value="'. $opGroupRs['ID'] .'"'. formSelector(intval(@$_POST['sub_group']),$opGroupRs['ID'],0) .'>'. $opGroupRs['group_name'] .'</option>');
							}
						$opGroup->free();
					?>
                    </select></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_company);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input name="sub_comp" type="text" id="sub_comp" class="form-control autoWidth" size="35" placeholder="<?php echo(lethe_optional);?>"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_phone);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input name="sub_phone" type="text" id="sub_phone" class="form-control autoWidth" size="35" placeholder="<?php echo(lethe_optional);?>"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_date);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input name="sub_date" type="text" id="sub_date" class="form-control autoWidth" size="35" placeholder="<?php echo(lethe_optional);?>"><script>$(document).ready(function(){$('#sub_date').datepick({dateFormat: 'dd/mm/yyyy'});});</script></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><button type="submit" name="addSubscriber" value="addSubscriber" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> <?php echo(lethe_add_subscriber);?></button></td>
                  </tr>
                </table>
                </form>
                <!-- Add Subscriber End -->
            
            </td>
          </tr>
        <?php }elseif($ppos==3){ # Edit Subscriber?>
          <tr>
            <td>
<!-- Date Picker -->
<link rel="stylesheet" type="text/css" href="css/jquery.datepick.css">  
<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.23.css">
<script type="text/javascript" src="Scripts/datepicker/jquery.datepick.js"></script>
<script type="text/javascript" src="Scripts/datepicker/jquery.datepick-en.js"></script>
            	<h1 class="panel-header"><?php echo(lethe_edit_subscriber);?></h1>
                <?php 
				$opSub = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE ID=". $ID ."") or die(mysqli_error());
				if(mysqli_num_rows($opSub)==0){echo('<div class="alert alert-info">'. lethe_record_not_found .'</div>');}else{
				echo($errText);
				$opSubRs = $opSub->fetch_assoc();
				?>
            	<!-- Edit Subscriber Start -->
                <form role="form" name="edit_subscrForm" action="" method="post">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="23%" height="40"><strong><?php echo(lethe_name);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="76%" height="40"><input value="<?php echo($opSubRs['sub_name']);?>" name="sub_name" type="text" id="sub_name" class="form-control autoWidth" size="35"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_e_mail);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo($opSubRs['sub_mail']);?>" name="sub_mail" type="email" id="sub_mail" class="form-control autoWidth" size="35"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_group);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><select name="sub_group" id="sub_group" class="form-control autoWidth">
                    <option value="0">-- <?php echo(lethe_choose);?></option>
                    <?php $opGroup = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups ORDER BY group_name ASC") or die(mysqli_error());
						while($opGroupRs = $opGroup->fetch_assoc()){
							echo('<option value="'. $opGroupRs['ID'] .'"'. formSelector(intval($opSubRs['GID']),$opGroupRs['ID'],0) .'>'. $opGroupRs['group_name'] .'</option>');
							}
						$opGroup->free();
					?>
                    </select></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_company);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo($opSubRs['sub_company']);?>" name="sub_comp" type="text" id="sub_comp" class="form-control autoWidth" size="35" placeholder="<?php echo(lethe_optional);?>"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_phone);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo($opSubRs['sub_phone']);?>" name="sub_phone" type="text" id="sub_phone" class="form-control autoWidth" size="35" placeholder="<?php echo(lethe_optional);?>"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_date);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo(date('d/m/Y',strtotime($opSubRs['sub_date'])));?>" name="sub_date" type="text" id="sub_date" class="form-control autoWidth" size="35" placeholder="<?php echo(lethe_optional);?>"><script>$(document).ready(function(){$('#sub_date').datepick({dateFormat: 'dd/mm/yyyy'});});</script></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_active);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-active" data-off="danger" data-on-label="<?php echo(lethe_on);?>" data-off-label="<?php echo(lethe_off);?>"><input name="active" type="checkbox" id="active" value="YES"<?php echo(formSelector($opSubRs['active'],1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_activation);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-activation" data-off="danger" data-on-label="<?php echo(lethe_on);?>" data-off-label="<?php echo(lethe_off);?>"><input name="activation" type="checkbox" id="activation" value="YES"<?php echo(formSelector($opSubRs['activated'],1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td height="40">&nbsp;</td>
                    <td height="40">&nbsp;</td>
                    <td height="40"><input onclick="if(this.checked==true){return confirm('<?php echo(lethe_all_sub_entries_will_be_removed.'!\n'.lethe_are_you_sure_to_d_elete);?>');}" type="checkbox" name="delSub" id="delSub" value="YES"> <label for="delSub"><?php echo(lethe_d_elete);?></label></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><button type="submit" name="editSubscriber" value="editSubscriber" class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> <?php echo(lethe_edit_subscriber);?></button></td>
                  </tr>
                </table>
                </form>
                <!-- Edit Subscriber End -->
                <?php }?>
            
            </td>
          </tr>
        <?php }elseif($ppos==4){ # Import / Export?>
          <tr>
            <td>
            
            	<h1 class="panel-header"><?php echo(lethe_export.' / '.lethe_import);?></h1>
                <!-- Export / Import Start -->
				<?php echo($errText);?>
                    <ul class="nav nav-tabs">
                      <li class="active"><a href="#export" data-toggle="tab"><?php echo(lethe_export);?></a></li>
                      <li><a href="#import-custom" data-toggle="tab"><?php echo(lethe_import_custom);?></a></li>
                      <li><a href="#import-wordpress" data-toggle="tab"><?php echo(lethe_import_from_wordpress);?></a></li>
                      <li><a href="#import-cms" data-toggle="tab"><?php echo(lethe_import_from_custom_cms);?></a></li>
                    </ul>
                    
                    <div class="tab-content tab-pane-default">
                      <div class="tab-pane fade in active" id="export">
                      	<!-- Export -->
                        <form role="form" name="exportMails" method="post" action="">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_group);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><select name="sub_group[]" id="sub_group" class="form-control autoWidth" size="10" style="width:500px;" multiple>
                    <?php $opGroup = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE active=1 ORDER BY group_name ASC") or die(mysqli_error());
						while($opGroupRs = $opGroup->fetch_assoc()){
							echo('<option value="'. $opGroupRs['ID'] .'">('. cntData("SELECT ID FROM ". db_table_pref ."newsletter_subscribers WHERE GID=". $opGroupRs['ID'] ."") .') '. $opGroupRs['group_name'] .'</option>');
							}
						$opGroup->free();
					?>
                    </select></td>
                              </tr>
                              <tr>
                                <td height="40"><strong><?php echo(lethe_export_model);?></strong></td>
                                <td><strong>:</strong></td>
                                <td><select name="exp_model" id="exp_model" class="form-control autoWidth">
                    <option value="1">&quot;<?php echo(lethe_name);?>&quot; &lt;mail@address&gt;,</option>
                    <option value="2">&quot;<?php echo(lethe_name);?>&quot; &lt;mail@address&gt;;</option>
                    <option value="3">&lt;mail@address&gt;,</option>
                    <option value="4">&lt;mail@address&gt;;</option>
                    <option value="5">mail@address,</option>
                    <option value="6">mail@address;</option>
                    </select></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><button type="submit" name="exportSubscribers" value="exportSubscribers" class="btn btn-primary"><span class="glyphicon glyphicon-cloud-download"></span> <?php echo(lethe_export);?></button></td>
                              </tr>
                            </table>
                        </form>
                        <!-- Export -->
                      </div>
                      <div class="tab-pane fade" id="import-custom">
                      
                      	<!-- Import Custom -->
                        <form role="form" name="importMailsCustom" method="post" action="" enctype="multipart/form-data">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_group);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><select name="sub_group" id="sub_group" class="form-control autoWidth">
                    <?php $opGroup = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE active=1 ORDER BY group_name ASC") or die(mysqli_error());
						while($opGroupRs = $opGroup->fetch_assoc()){
							echo('<option value="'. $opGroupRs['ID'] .'">('. cntData("SELECT ID FROM ". db_table_pref ."newsletter_subscribers WHERE GID=". $opGroupRs['ID'] ."") .') '. $opGroupRs['group_name'] .'</option>');
							}
						$opGroup->free();
					?>
                    </select></td>
                              </tr>
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_file);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><input name="importFile" type="file" class="filestyle" data-classButton="btn btn-warning" data-classIcon="glyphicon glyphicon-plus"></td>
                              </tr>
                              <tr>
                                <td height="40"><strong><?php echo(lethe_import_model);?></strong></td>
                                <td><strong>:</strong></td>
                                <td><select name="imp_model" id="imp_model" class="form-control autoWidth">
                    <option value="1">&quot;<?php echo(lethe_name);?>&quot; &lt;mail@address&gt;,</option>
                    <option value="2">&quot;<?php echo(lethe_name);?>&quot; &lt;mail@address&gt;;</option>
                    <option value="3">&lt;mail@address&gt;,</option>
                    <option value="4">&lt;mail@address&gt;;</option>
                    <option value="5">mail@address,</option>
                    <option value="6">mail@address;</option>
					<option value="7">mail@address{line_break}</option>
                    </select></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><button type="submit" name="importSubCustom" value="importSubCustom" class="btn btn-primary"><span class="glyphicon glyphicon-cloud-upload"></span> <?php echo(lethe_import);?></button></td>
                              </tr>
                            </table>
                        </form>
                        <!-- Import Custom -->
                      
                      </div>
                      <div class="tab-pane fade" id="import-wordpress">
                      
                      	<!-- Import Wordpress -->
                        <form role="form" name="importMailsWordpress" method="post" action="">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_group);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><select name="sub_group" id="sub_group" class="form-control autoWidth">
                    <?php $opGroup = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE active=1 ORDER BY group_name ASC") or die(mysqli_error());
						while($opGroupRs = $opGroup->fetch_assoc()){
							echo('<option value="'. $opGroupRs['ID'] .'">('. cntData("SELECT ID FROM ". db_table_pref ."newsletter_subscribers WHERE GID=". $opGroupRs['ID'] ."") .') '. $opGroupRs['group_name'] .'</option>');
							}
						$opGroup->free();
					?>
                    </select></td>
                              </tr>
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_database_host);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><input type="text" name="db_host" value="" class="form-control autoWidth"></td>
                              </tr>
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_database_name);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><input type="text" name="db_name" value="" class="form-control autoWidth"></td>
                              </tr>
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_database_username);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><input type="text" name="db_user" value="" class="form-control autoWidth"></td>
                              </tr>
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_database_password);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><input type="password" name="db_pass" value="" class="form-control autoWidth"></td>
                              </tr>
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_table_prefix);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><input type="text" name="db_table_pref" value="" class="form-control autoWidth" placeholder="wp_"></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><button type="submit" name="importWordpress" value="importWordpress" class="btn btn-primary"><span class="glyphicon glyphicon-cloud-upload"></span> <?php echo(lethe_import);?></button></td>
                              </tr>
                            </table>
                        </form>
                        <!-- Import Wordpress -->
                      
                      </div>
                      <div class="tab-pane fade" id="import-cms">
                      
                      	<!-- Import Custom CMS -->
                        <form role="form" name="importMailsCustomCMS" method="post" action="">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_group);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><select name="sub_group" id="sub_group" class="form-control autoWidth">
                    <?php $opGroup = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE active=1 ORDER BY group_name ASC") or die(mysqli_error());
						while($opGroupRs = $opGroup->fetch_assoc()){
							echo('<option value="'. $opGroupRs['ID'] .'">('. cntData("SELECT ID FROM ". db_table_pref ."newsletter_subscribers WHERE GID=". $opGroupRs['ID'] ."") .') '. $opGroupRs['group_name'] .'</option>');
							}
						$opGroup->free();
					?>
                    </select></td>
                              </tr>
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_database_host);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><input type="text" name="db_host" value="" class="form-control autoWidth"></td>
                              </tr>
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_database_name);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><input type="text" name="db_name" value="" class="form-control autoWidth"></td>
                              </tr>
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_database_username);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><input type="text" name="db_user" value="" class="form-control autoWidth"></td>
                              </tr>
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_database_password);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><input type="password" name="db_pass" value="" class="form-control autoWidth"></td>
                              </tr>
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_table_name);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><input type="text" name="db_table_name" value="" class="form-control autoWidth"></td>
                              </tr>
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_name_field);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><input type="text" name="db_name_field" value="" class="form-control autoWidth"></td>
                              </tr>
                              <tr>
                                <td height="40" width="20%"><strong><?php echo(lethe_mail_field);?></strong></td>
                                <td width="3%"><strong>:</strong></td>
                                <td><input type="text" name="db_mail_field" value="" class="form-control autoWidth"></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><button type="submit" name="importCustomCMS" value="importCustomCMS" class="btn btn-primary"><span class="glyphicon glyphicon-cloud-upload"></span> <?php echo(lethe_import);?></button></td>
                              </tr>
                            </table>
                        </form>
                        <!-- Import Custom CMS -->
                      
                      </div>
                    </div>
                <!-- Export / Import End -->
            
            </td>
          </tr>
        <?php }elseif($ppos==5){ # Subscribe Forms
		
		$uniq_code1 = 'lethe_'.base_convert(uniqid('lethe',true), 10, 36);
		$uniq_code2 = 'lethe_'.base_convert(uniqid('lethe',true), 10, 36);
		$uniq_code3 = 'lethe_'.base_convert(uniqid('lethe',true), 10, 36);
		?>
          <tr>
            <td>
            <h1 class="panel-header"><?php echo(lethe_subscribe_forms);?></h1>
            <input type="hidden" name="sub_forms_url" id="sub_forms_url" value="<?php echo(set_site_url);?>">
            <?php echo($errText);?>
            <!-- Subscribe Forms Start -->
            <ul class="nav nav-tabs" id="subscr_form_modes">
              <li class="active" data-tab-id="subscribe_forms"><a href="#subscribe_forms" data-toggle="tab"><?php echo(lethe_subscribe_forms);?></a></li>
              <li class="" data-tab-id="create_form"><a href="#create_form" data-toggle="tab"><?php echo(lethe_create_form);?></a></li>
              <li class="" data-tab-id="create_link"><a href="#create_link" data-toggle="tab"><?php echo(lethe_create_link);?></a></li>
              <li class="" data-tab-id="custom_forms"><a href="#custom_forms" data-toggle="tab"><?php echo(lethe_custom_forms);?></a></li>
            </ul>
            
            <div class="tab-content tab-pane-default" id="subscr_forms">
              <div class="tab-pane active" id="subscribe_forms">
              
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-hover table-striped">
<thead>
  <tr>
    <th width="42%"><?php echo(lethe_title);?></th>
    <th width="14%" style="text-align:center" align="center"><?php echo(lethe_type);?></th>
    <th width="14%" style="text-align:center" align="center"><?php echo(lethe_remove_after);?></th>
    <th width="18%" style="text-align:center" align="center"><?php echo(lethe_add_date);?></th>
    <th width="12%" style="text-align:center" align="center"><?php echo(lethe_action);?></th>
  </tr>
</thead>
<tbody>
<?php $opForms = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_forms") or die(mysqli_error());
if(mysqli_num_rows($opForms)==0){
	echo('<tr><td colspan="5"><div class="alert alert-info">'. lethe_record_not_found .'</div></td></tr>');
	}else{
		while($opFormsRs = $opForms->fetch_assoc()){
?>
  <tr>
    <td><?php echo($opFormsRs['form_name']);?></td>
    <td align="center"><?php echo($lethe_form_models[$opFormsRs['form_type']]);?></td>
    <td align="center"><?php if($opFormsRs['remove_after']==0){echo('<span class="glyphicon glyphicon-asterisk"></span>');}else{echo('<span class="glyphicon glyphicon-asterisk" style="color:red;"></span>');}?></td>
    <td align="center"><?php echo(setMyDate($opFormsRs['add_date'],6));?></td>
    <td align="center">
    
        <div class="btn-group btn-group-xs" style="text-align:left">
          <button type="button" class="btn btn-danger"><?php echo(lethe_action);?></button>
          <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
          </button>
          <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:void(0);" onClick="javascript:getAjax('.subFormAct .modal-body','<?php echo($_SERVER['SCRIPT_NAME']);?>?ajax=1&ajax_part=6&subf_act=2&ID=<?php echo($opFormsRs['ID']);?>','<?php echo(lethe_loading);?>');" data-toggle="modal" data-target=".subFormAct"><?php echo(lethe_get_codes);?></a></li>
            <li><a href="javascript:void(0);" onClick="javascript:getAjax('.subFormAct .modal-body','<?php echo($_SERVER['SCRIPT_NAME']);?>?ajax=1&ajax_part=6&subf_act=1&ID=<?php echo($opFormsRs['ID']);?>','<?php echo(lethe_loading);?>');" data-toggle="modal" data-target=".subFormAct"><?php echo(lethe_preview);?></a></li>
            <li class="divider"></li>
            <li><a href="javascript:void(0);" onClick="javascript:getAjax('.subFormAct .modal-body','<?php echo($_SERVER['SCRIPT_NAME']);?>?ajax=1&ajax_part=6&subf_act=3&ID=<?php echo($opFormsRs['ID']);?>','<?php echo(lethe_loading);?>');" data-toggle="modal" data-target=".subFormAct"><?php echo(lethe_remove);?></a></li>
          </ul>
        </div>
    
    </td>
  </tr>
<?php }}
$opForms->free();
?>
</tbody>
</table>
<!-- Form Actions -->
<div class="modal fade subFormAct" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"><?php echo(lethe_subscribe_forms);?></h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>     
<!-- Form Actions -->
              
              </div>
              
              <div class="tab-pane" id="create_form">
              
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" width="60%">
    
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><h3><?php echo(lethe_design);?></h3></td>
          </tr>
          <tr>
            <td>
<?php 
require_once(LETHEPATH.'/recaptchalib.php');
$publickey = "6LdAPPASAAAAAEBocHar3-whAUirBxpxH6kmpMia";
$captchaCode = '<div class="recapt_area"><script type="text/javascript">
var RecaptchaOptions = {
theme : "custom",
custom_theme_widget: "recaptcha_widget"
};
</script><div id="recaptcha_widget" style="display:none">
					<div id="recaptcha_image"></div>
					<div class="recaptcha_only_if_incorrect_sol" style="color:red">'. lethe_incorrect_verification_code .'</div>
					<input type="text" id="recaptcha_response_field" name="recaptcha_response_field">
					<div><a href="javascript:Recaptcha.reload()">'. lethe_new_code .'</a></div>
				</div>
				<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=6LdAPPASAAAAAEBocHar3-whAUirBxpxH6kmpMia"></script>
				<noscript>
					<iframe src="http://www.google.com/recaptcha/api/noscript?k=6LdAPPASAAAAAEBocHar3-whAUirBxpxH6kmpMia" height="200" width="200" frameborder="0"></iframe>
				<br><textarea name="recaptcha_challenge_field" rows="3" cols="20"></textarea>
					<input type="hidden" name="recaptcha_response_field" value="manual_challenge">
				</noscript></div>';
$date_models = '<select id="lethe_date_model" class="form-control input-sm autoWidth inlineBlock">';
foreach($lethe_date_field_models as $key=>$value){
	$date_models.='<option value="'. $key .'">'. $value .'</option>';
}
$date_models.='</select>';
?>
<div id="form-field-list">
    <ul id="sortable1" class="connectedSortable">
        <li class="alert alert-info" data-form-field="lethe_name" data-form-req="" data-form-var="<?php echo(htmlentities('<input class="form-control" name="lethe_name" type="text" value="" placeholder="'. lethe_name .'">', ENT_QUOTES, "UTF-8"));?>"><?php echo(lethe_name);?><span class="pull-right"><input type="text" name="lethe_name_pattern" id="lethe_name_pattern" class="form-control autoWidth input-sm inlineBlock" value="" placeholder="<?php echo(lethe_pattern);?>"> <input type="checkbox" value="YES" name="name_required" class="form_req_area"> <?php echo(lethe_required);?></span></li>
        <li class="alert alert-info" data-form-field="lethe_company" data-form-req="" data-form-var="<?php echo(htmlentities('<input class="form-control" name="lethe_company" type="text" value="" placeholder="'. lethe_company .'">', ENT_QUOTES, "UTF-8"));?>"><?php echo(lethe_company);?><span class="pull-right"><input type="text" name="lethe_company_pattern" id="lethe_company_pattern" class="form-control autoWidth input-sm inlineBlock" value="" placeholder="<?php echo(lethe_pattern);?>"> <input type="checkbox" value="YES" name="company_required" class="form_req_area"> <?php echo(lethe_required);?></span></li>
        <li class="alert alert-info" data-form-field="lethe_phone" data-form-req="" data-form-var="<?php echo(htmlentities('<input class="form-control" name="lethe_phone" type="tel" value="" placeholder="'. lethe_phone .'">', ENT_QUOTES, "UTF-8"));?>"><?php echo(lethe_phone);?><span class="pull-right"><input type="text" name="lethe_phone_pattern" id="lethe_phone_pattern" class="form-control autoWidth input-sm inlineBlock" value="" placeholder="<?php echo(lethe_pattern);?>"> <input type="checkbox" value="YES" name="phone_required" class="form_req_area"> <?php echo(lethe_required);?></span></li>
        <li class="alert alert-info" data-form-field="lethe_pos1" data-form-req="" data-form-var="<?php echo(htmlentities('<input type="radio" name="lethe_pos" id="lethe_pos1" value="0" checked> <label for="lethe_pos1">'. lethe_add .'</label> <input type="radio" name="lethe_pos" id="lethe_pos2" value="1"> <label for="lethe_pos2">'. lethe_remove .'</label>', ENT_QUOTES, "UTF-8"));?>"><?php echo(lethe_add_remove);?></li>
		<li class="alert alert-info" data-form-field="lethe_date" data-form-req="" data-form-var="<?php echo(htmlentities('(dateScript)<input class="form-control" id="lethe_date" name="lethe_date" type="date" value="" placeholder="'. lethe_date .'">', ENT_QUOTES, "UTF-8"));?>"><?php echo(lethe_date);?><span class="pull-right"><input type="text" name="lethe_date_pattern" id="lethe_date_pattern" class="form-control autoWidth input-sm inlineBlock" value="" placeholder="<?php echo(lethe_pattern);?>"> <?php echo($date_models);?> <input type="checkbox" value="YES" name="date_required" class="form_req_area"> <?php echo(lethe_required);?></span></li>
        <li class="alert alert-info" data-form-field="captcha" data-form-req=" required" data-form-var="<?php echo(htmlentities($captchaCode, ENT_QUOTES, "UTF-8"));?>"><?php echo(lethe_captcha);?></li>
		<li id="lethe_listbox_field" class="alert alert-info" data-form-field="lethe_listbox" data-form-req="" data-form-var=""><?php echo(lethe_listbox);?><span class="pull-right"> <button type="button" data-target="#listBoxItems" data-toggle="modal" class="btn btn-success btn-sm" id="listbox_adder"><span class="glyphicon glyphicon-plus"></span></button> <input type="checkbox" value="YES" name="listbox_required" class="form_req_area"> <?php echo(lethe_required);?></span></li>
    </ul>
    <ul id="sortable2" class="connectedSortable">
    	<li id="email-form" class="alert alert-info" data-form-field="lethe_email" data-form-req=" required" data-form-var="<?php echo(htmlentities('<input class="form-control" name="lethe_email" type="email" value="" placeholder="'. lethe_e_mail .'">', ENT_QUOTES, "UTF-8"));?>"><?php echo(lethe_e_mail);?><span class="pull-right"><input type="checkbox" value="YES" name="mail_required" class="form_req_area" disabled checked> <?php echo(lethe_required);?></span></li>
    </ul>
</div>
            
                <div class="clearfix"></div>
            </td>
          </tr>
          <tr>
            <td height="40">
            	<select class="form-control autoWidth" name="form_group" id="form_group">
                	<option value="0"><?php echo(lethe_ungrouped);?></option>
                    <?php $opGroups = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE active=1 ORDER BY group_name ASC") or die(mysqli_error());
					while($opGroupsRs = $opGroups->fetch_assoc()){
						echo('<option value="'. $opGroupsRs['ID'] .'">'. $opGroupsRs['group_name'] .'</option>');	
					}
					$opGroups->free();
					?>
                </select>
            </td>
          </tr>
          <tr>
            <td height="40">
            	<input type="radio" name="form_model" id="form_model1" value="0" checked> <label for="form_model1"><?php echo(lethe_normal);?></label> 
                <input type="radio" name="form_model" id="form_model2" value="1"> <label for="form_model2"><?php echo(lethe_inline);?></label> 
                <input type="radio" name="form_model" id="form_model4" value="3"> <label for="form_model4"><?php echo(lethe_table);?></label>
            </td>
          </tr>
          <tr>
            <td height="40">
            	<input type="checkbox" name="form_alert_mode" id="form_alert_mode" value="YES" checked> <label for="form_alert_mode"><?php echo(lethe_s_how_results_on_alert_popup);?></label>
            </td>
          </tr>
          <tr>
            <td><button class="btn btn-primary" type="button" data-submit-val="<?php echo(lethe_add);?>" data-submit-action="lethe.subscriber.php?pos=0" data-submit-form-code="#sub_forms_1" data-submit-prev-code="#sub_forms_prev_1" id="form_code_gen1"><?php echo(lethe_generate_code);?></button></td>
          </tr>
        </table>

    </td>
    <td valign="top" width="50%">
    
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><h3><?php echo(lethe_preview);?></h3></td>
          </tr>
          <tr>
            <td><div id="sub_forms_prev_1" class="form_prevs"></div></td>
          </tr>
          <tr>
            <td><h3><?php echo(lethe_embed_code);?></h3></td>
          </tr>
          <tr>
            <td>
            <div id="sub_form_res1"></div>
            <form name="sub_formz1" id="sub_formz1" action="" method="post" enctype="multipart/form-data">
            	<input class="form-control" type="hidden" name="sub_form_code1" id="sub_form_code1" value="<?php echo($uniq_code1);?>"><br>
                <input class="form-control" type="hidden" name="sub_form_opt1" id="sub_form_opt1" value="">
                <input class="form-control" type="hidden" name="sub_form_typ1" id="sub_form_typ1" value="0">
            	<p><textarea class="form-control" rows="12" id="sub_forms_1" name="sub_forms_1" required></textarea></p>
                <p><input type="text" value="" name="form_title1" id="form_title1" class="form-control" placeholder="<?php echo(lethe_title);?>"></p>
				<p><input type="text" value="" name="form_succ_text" id="form_succ_text" class="form-control" placeholder="<?php echo(lethe_success_text);?>"></p>
				<p><input type="text" value="" name="form_succ_link_title" id="form_succ_link_title" class="form-control" placeholder="<?php echo(lethe_success_url_title);?>"></p>
				<p><input type="url" value="" name="form_succ_link" id="form_succ_link" class="form-control" placeholder="<?php echo(lethe_success_url);?>"></p>
				<p><input type="number" value="" name="form_succ_redir_time" id="form_succ_redir_time" class="form-control" placeholder="<?php echo(lethe_after_this_number_of_seconds);?>" required>
				<span class="help-block">0 - <?php echo(lethe_will_dont_redirect_page_after_subs_cription);?>.</span>
				</p>
                <p><button name="addSubscribeForm1" id="addSubscribeForm1" type="submit" class="btn btn-primary pull-right"><?php echo(lethe_save);?></button></p>
            </form>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
    
    </td>
  </tr>
</table>
<script>
$(document).ready(function(){
	/* Add Item */
	$("#addBoxItem").click(function(){
		
		if($('#boxKey').val()=='' || $('#boxVal').val()==''){alert('<?php echo(lethe_please_enter_a_value);?>');return false;}
		$('#tempbox').append('<option value="'+ $('#boxKey').val() +'">'+ $('#boxVal').val() +'</option>');
		$('#boxKey').val('');
		$('#boxVal').val('');

	});
	/* Save List */
	$("#saveBox").click(function(){
			
		var dataCode = '<select class="form-control" name="lethe_listbox" id="lethe_listbox">';
			$('#tempbox > option').each(function() {
				dataCode+='<option value="'+ $(this).text() +'">'+ $(this).val() +'</option>';
			});
		dataCode+='</select>';
		$("#lethe_listbox_field").attr('data-form-var',htmlEncode(dataCode));
	});
});
</script>
<div class="modal fade" id="listBoxItems" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?php echo(lethe_listbox);?></h4>
      </div>
      <div class="modal-body">
		<div id="boxResult">
		
		</div>
		<div class="designbox">
			<select id="tempbox" name="tempbox" class="form-control" multiple>
			
			</select>
      </div>
	  <div class="controlbox" style="margin-top:15px;">
	  <button onclick="javascript:listbox_move('tempbox', 'up');" type="button" class="btn btn-primary"><span class="glyphicon glyphicon-chevron-up"></span></button>
	  <button onclick="javascript:listbox_move('tempbox', 'down');" type="button" class="btn btn-primary"><span class="glyphicon glyphicon-chevron-down"></span></button>
	  <button onclick="javascript:listbox_remove('tempbox');" type="button" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></button>
	  </div>
	  <div class="row" style="margin-top:15px;">
		<div class="col-md-4"><input type="text" value="" placeholder="<?php echo(lethe_key)?>" id="boxKey" class="form-control" required></div><div class="col-md-4"><input type="text" value="" placeholder="<?php echo(lethe_value)?>" id="boxVal" class="form-control" required></div><div class="col-md-4"><button type="button" id="addBoxItem" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span></button></div>
	  </div>
	  
	  </div>
      <div class="modal-footer">
        <button type="button" id="saveBox" class="btn btn-primary"><?php echo(lethe_save)?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


              
              </div>
              
              <div class="tab-pane" id="create_link">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="50%" valign="top">
    
                <form role="form" name="add_subscrLink" id="add_subscrLink" action="" method="post">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="23%" height="40"><strong><?php echo(lethe_name);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="76%" height="40"><input name="sub_name" type="text" id="sub_name_cl" data-field-name="lethe_name" class="form-control autoWidth" size="35"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_e_mail);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input name="sub_mail" type="text" id="sub_mail_cl" data-field-name="lethe_email" class="form-control autoWidth" size="35"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_group);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><select name="sub_group" id="sub_group_cl" data-field-name="lethe_subscribe_group" class="form-control autoWidth">
                    <option value="0">-- <?php echo(lethe_choose);?></option>
                    <?php $opGroup = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE active=1 ORDER BY group_name ASC") or die(mysqli_error());
						while($opGroupRs = $opGroup->fetch_assoc()){
							echo('<option value="'. $opGroupRs['ID'] .'"'. formSelector(intval(@$_POST['sub_group']),$opGroupRs['ID'],0) .'>'. $opGroupRs['group_name'] .'</option>');
							}
						$opGroup->free();
					?>
                    </select></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_company);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input name="sub_comp" data-field-name="lethe_company" type="text" id="sub_comp_cl" class="form-control autoWidth" size="35" placeholder="<?php echo(lethe_optional);?>"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_phone);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input name="sub_phone" data-field-name="lethe_phone" type="text" id="sub_phone_cl" class="form-control autoWidth" size="35" placeholder="<?php echo(lethe_optional);?>"></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><button type="button" name="addSubscriberLink" id="addSubscriberLink" value="addSubscriberLink" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> <?php echo(lethe_create_link);?></button></td>
                  </tr>
                </table>
                </form>
    
    </td>
    <td width="50%" valign="top">
    
<table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><h3><?php echo(lethe_subscribe_link);?></h3></td>
          </tr>
          <tr>
            <td>
            <div id="sub_form_res2"></div>
            <form name="sub_formz2" id="sub_formz2" action="" method="post">
            	<input class="form-control" type="hidden" name="sub_form_code1" id="sub_form_code2" value="<?php echo($uniq_code2);?>"><br>
                <input class="form-control" type="hidden" name="sub_form_opt1" id="sub_form_opt2" value="[lethe_email@required:yes]">
                <input class="form-control" type="hidden" name="sub_form_typ1" id="sub_form_typ2" value="1">
                    <p>
                        <div class="input-group">
                          <span class="input-group-addon">HTML</span>
                          <input type="text" class="form-control" id="subscr_links1" placeholder="HTML">
                        </div>
                    </p>
                    <p>
                        <div class="input-group">
                          <span class="input-group-addon">TEXT</span>
                          <input type="text" class="form-control" name="sub_forms_1" id="subscr_links2" placeholder="TEXT">
                        </div>
                    </p>
                <p><input type="text" value="" name="form_title1" id="form_title2" class="form-control" placeholder="<?php echo(lethe_title);?>"></p>
				<p><input type="text" value="" name="form_succ_text" id="form_succ_text2" class="form-control" placeholder="<?php echo(lethe_success_text);?>"></p>
				<p><input type="text" value="" name="form_succ_link_title" id="form_succ_link_title2" class="form-control" placeholder="<?php echo(lethe_success_url_title);?>"></p>
				<p><input type="url" value="" name="form_succ_link" id="form_succ_link2" class="form-control" placeholder="<?php echo(lethe_success_url);?>"></p>
				<p><input type="number" value="" name="form_succ_redir_time" id="form_succ_redir_time2" class="form-control" placeholder="<?php echo(lethe_after_this_number_of_seconds);?>" required>
				<span class="help-block">0 - <?php echo(lethe_will_dont_redirect_page_after_subs_cription);?>.</span>
				</p>
                <p><input type="checkbox" name="remove_after" id="remove_after" value="YES"> <label for="remove_after"><?php echo(lethe_d_elete_form_after_registration);?></label></p>
                <p><button name="addSubscribeForm2" id="addSubscribeForm2" type="submit" class="btn btn-primary pull-right"><?php echo(lethe_save);?></button></p>
            </form>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
    
    </td>
  </tr>
</table>


              </div>
              
              <div class="tab-pane" id="custom_forms">

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><h3><?php echo(lethe_group);?></h3></td>
          </tr>
          <tr>
            <td>
            
					<select name="sub_group" id="sub_group_cc" class="form-control autoWidth">
                    <option value="0">-- <?php echo(lethe_choose);?></option>
                    <option value="0"><?php echo(lethe_ungrouped);?></option>
                    <?php $opGroup = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE active=1 ORDER BY group_name ASC") or die(mysqli_error());
						while($opGroupRs = $opGroup->fetch_assoc()){
							echo('<option value="'. $opGroupRs['ID'] .'">'. $opGroupRs['group_name'] .'</option>');
							}
						$opGroup->free();
					?>
                    </select>
            
            </td>
          </tr>
          <tr>
            <td><h3><?php echo(lethe_preview);?></h3></td>
          </tr>
          <tr>
            <td>
                <div id="sub_forms_prev_3" class="form_prevs alert alert-warning" style="min-width:400px;"></div>
            </td>
          </tr>
          <tr>
            <td><h3><?php echo(lethe_embed_code);?></h3></td>
          </tr>
          <tr>
            <td>
            <div id="sub_form_res3"></div>
            <form name="sub_formz3" id="sub_formz3" action="" method="post">
            	<input class="form-control" type="hidden" name="sub_form_code1" id="sub_form_code3" value="<?php echo($uniq_code3);?>"><br>
                <input class="form-control" type="hidden" name="sub_form_opt1" id="sub_form_opt3" value="[lethe_email@required:yes]">
                <input class="form-control" type="hidden" name="sub_form_typ1" id="sub_form_typ3" value="2">
            	<p><textarea class="form-control" rows="12" id="sub_forms_3" name="sub_forms_1" required></textarea></p>
                <p><input type="text" value="" name="form_title1" id="form_title1" class="form-control" placeholder="<?php echo(lethe_title);?>"></p>
				<p><input type="text" value="" name="form_succ_text" id="form_succ_text2" class="form-control" placeholder="<?php echo(lethe_success_text);?>"></p>
				<p><input type="text" value="" name="form_succ_link_title" id="form_succ_link_title2" class="form-control" placeholder="<?php echo(lethe_success_url_title);?>"></p>
				<p><input type="url" value="" name="form_succ_link" id="form_succ_link2" class="form-control" placeholder="<?php echo(lethe_success_url);?>"></p>
				<p><input type="number" value="" name="form_succ_redir_time" id="form_succ_redir_time2" class="form-control" placeholder="<?php echo(lethe_after_this_number_of_seconds);?>" required>
				<span class="help-block">0 - <?php echo(lethe_will_dont_redirect_page_after_subs_cription);?>.</span>
				</p>
                <p><input name="form_chk" type="checkbox" id="form_chk" value="YES"> <label for="form_chk"><?php echo(lethe_default_checked);?></label></p>
                <p class="pull-right"><button name="prevCustForms" id="prevCustForms" type="button" class="btn btn-warning"><span class="glyphicon glyphicon-eye-open"></span> <?php echo(lethe_preview);?></button> <button name="addSubscribeForm3" id="addSubscribeForm3" type="submit" class="btn btn-primary"><?php echo(lethe_save);?></button></p>
            </form>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>

              </div>
              
            </div>
            
            <!-- Subscribe Forms End -->
            
            </td>
          </tr>
        <?php }elseif($ppos==6){ # Blacklist ?>
        <tr>
        	<td>
            
            <!-- Blacklist Start -->
            <h1 class="panel-header"><?php echo(lethe_blacklist);?></h1>
            <form role="form" name="editBlacklist" id="editBlacklist" method="post">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-hover table-striped">
            <thead>
              <tr>
                <th width="2%"></th>
                <th width="20%"><?php echo(lethe_e_mail);?></th>
                <th width="5%"><?php echo(lethe_ip_address);?></th>
                <th width="15%"><?php echo(lethe_reason);?></th>
                <th width="15%"><?php echo(lethe_date);?></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td></td>
                <td><input type="email" name="bl_mail" value="" class="form-control" placeholder="<?php echo(lethe_add_new_email);?>"></td>
                <td><input type="text" name="bl_ip" pattern="^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$" value=""  class="form-control" placeholder="<?php echo(lethe_ip_address);?>"></td>
                <td><input type="text" name="bl_reason" value=""  class="form-control" placeholder="<?php echo(lethe_reason);?>"></td>
                <td>-</td>
              </tr>
            <?php $opBList = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_blacklist ORDER BY email ASC") or die(mysqli_error());
			if(mysqli_num_rows($opBList)==0){echo('');}else{
				while($opBListRs = $opBList->fetch_assoc()){?>
              <tr>
                <td><input type="checkbox" name="del[]" value="<?php echo($opBListRs['ID']);?>"></td>
                <td><?php echo($opBListRs['email']);?></td>
                <td><?php echo($opBListRs['ip_addr']);?></td>
                <td><?php echo($opBListRs['reason']);?></td>
                <td><?php echo(setMydate($opBListRs['add_date'],6));?></td>
              </tr>
              <?php }?>
              <tr>
                <td colspan="5" class="non-striped">&nbsp;</td>
              </tr>
           <?php }
		   $opBList->free();
		   ?>
              <tr>
                <td colspan="5" class="non-striped"><button type="submit" name="updateBlacklist" id="updateBlacklist" class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> <?php echo(lethe_u_pdate);?></button></td>
              </tr>
            </tbody>
            </table>
			</form>
            <!-- Blacklist End -->
            
            </td>
        </tr>
        <?php }?>
        </table>
        
    <!-- Subscribers End -->
    
    </td>
  </tr>
<?php }elseif($pos==3){ # Templates?>
  <tr>
    <td>
    
    <!-- Templates Start -->
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <?php if($ppos==0){ # Template List?>
          <tr>
            <td>
            	<h1 class="panel-header"><?php echo(lethe_templates);?></h1>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover">
                  <thead>
                  <tr>
                    <th width="20%"><?php echo(lethe_preview);?></th>
                    <th width="50%"><?php echo(lethe_template);?></th>
                    <th width="30%"><?php echo(lethe_date);?></th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php 
$limit = 15;
$pgGo = @$_GET["pgGo"];
if(empty($pgGo) or !is_numeric($pgGo)) {$pgGo = 1;}

 $count		 = mysqli_num_rows($myconn->query("SELECT ID FROM ". db_table_pref ."newsletter_templates"));
 $total_page	 = ceil($count / $limit);
 $dtStart	 = ($pgGo-1)*$limit;
 
$opTemp = $myconn->query("SELECT ID,preview,add_date,title FROM ". db_table_pref ."newsletter_templates ORDER BY title ASC LIMIT $dtStart,$limit") or die(mysqli_error());

				  if($count==0){echo('<tr><td colspan="3"><div class="alert alert-danger">'. lethe_record_not_found .'</div></td></tr>');}else{
				  while($opTempRs = $opTemp->fetch_assoc()){
				  ?>
                  <tr>
                    <td><div class="thumbnail"><a href="?pos=3&ppos=2&ID=<?php echo($opTempRs['ID']);?>"><img src="<?php echo($opTempRs['preview']);?>" alt="" width="150"></a></div></td>
                    <td valign="middle"><a href="?pos=3&ppos=2&ID=<?php echo($opTempRs['ID']);?>"><?php echo($opTempRs['title']);?></a><br><br><a href="?ajax=1&ajax_part=3&ID=<?php echo($opTempRs['ID']);?>" data-fancybox-type="ajax" data-fancybox-width="1000" data-fancybox-height="800" class="btn btn-warning btn-sm fancybox" role="button"><span class="glyphicon glyphicon-eye-open"></span> <?php echo(lethe_preview);?></a></td>
                    <td valign="middle"><?php echo(setMyDate($opTempRs['add_date'],6));?></td>
                  </tr>
                  <?php }}?>
					<?php if($total_page>1){?>
					  <tr class="non-striped">
						<td colspan="3">&nbsp;</td>
					  </tr>
					  <tr class="non-striped">
						<td colspan="3"><?php $pgVar='?pos='. $pos .'&ppos='. $ppos;include("inc/inc_pagination.php");?></td>
					  </tr>
					<?php }
					$opTemp->free();
					?>
                  </tbody>
                </table>
            
            </td>
          </tr>
        <?php }elseif($ppos==1){ # Add Template?>
          <tr>
            <td>
            	<h1 class="panel-header"><?php echo(lethe_create_template);?></h1>
                <?php echo($errText);?>
            	<!-- Add Template Start -->
                <form id="addTemp" role="form" action="" method="post">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="22%" height="35"><strong><?php echo(lethe_template_name);?></strong></td>
                        <td width="1%" height="35"><strong>:</strong></td>
                        <td width="77%" height="35"><input value="<?php echo(mysql_prep(@$_POST['title']));?>" class="form-control input-sm" name="title" type="text" id="title" size="50" maxlength="255" required></td>
                      </tr>
                      <tr>
                        <td width="22%" height="35"><strong><?php echo(lethe_template_image);?></strong></td>
                        <td width="1%" height="35"><strong>:</strong></td>
                        <td width="77%" height="35">
                        	<div class="input-group">
	                        	<input placeholder="http://" value="<?php echo(mysql_prep(@$_POST['prev_img']));?>" class="form-control input-sm" name="prev_img" type="text" id="prev_img" size="50" maxlength="255" required>
			  <?php 
			  if(minipan_on==1){
				echo('
              <span class="input-group-btn">
                <button data-pan-model="fancybox" data-pan-field="prev_img" data-pan-link="default" data-pan-platform="normal" class="btn btn-default btn-sm minipan" type="button"><span class="glyphicon glyphicon-cloud-upload"></span></button>
              </span>
				');
			  }
			  ?>
                            </div>
                        </td>
                      </tr>
                      <tr>
                        <td width="22%" height="35"><strong><?php echo(lethe_short_codes);?></strong></td>
                        <td width="1%" height="35"><strong>:</strong></td>
                        <td width="77%" height="35">
                        	<div class="well" id="short_code_list">
                            	<?php
                                	for($i=0;$i<count($lethe_short_codes);$i++){ # List of static codes
										echo('<div class="label label-danger" data-lethe-codes="{'. $lethe_short_codes[$i] .'}" data-lethe-code-field="details">{'. $lethe_short_codes[$i] .'}</div> ');
										}
									$opCodes = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_codes ORDER BY lethe_code ASC") or die(mysqli_error());
									while($opCodesRs = $opCodes->fetch_assoc()){
										echo('<div class="label label-info" data-lethe-codes="{'. $opCodesRs['lethe_code'] .'}" data-lethe-code-field="details">{'. $opCodesRs['lethe_code'] .'}</div> ');
										}
									$opCodes->free();
								?>
                            </div>
                        </td>
                      </tr>
                      <tr>
                        <td height="35"><strong><?php echo(lethe_template_content);?></strong></td>
                        <td height="35"><strong>:</strong></td>
                        <td height="35"><textarea name="details" id="details" class="mceEditor"><?php echo(@$_POST['details']);?></textarea></td>
                      </tr>
                      <tr>
                        <td height="35"><strong><?php echo(lethe_use_for_verification);?></strong></td>
                        <td height="35"><strong>:</strong></td>
                        <td height="35"><div class="make-switch" data-on="success" id="switch-verify" data-off="danger" data-on-label="<?php echo(lethe_yes);?>" data-off-label="<?php echo(lethe_no);?>"><input name="temp_verify" type="checkbox" id="temp_verify" value="YES"></div></td>
                      </tr>
                      <tr>
                        <td height="35">&nbsp;</td>
                        <td height="35">&nbsp;</td>
                        <td height="35">&nbsp;</td>
                      </tr>
                      <tr>
                        <td height="35">&nbsp;</td>
                        <td height="35">&nbsp;</td>
                        <td height="35"><button type="button" name="prev" id="prev" class="fancybox2 btn btn-warning"><?php echo(lethe_preview);?></button> <button type="submit" name="addTemplate" id="addTemplate" value="addTemplate" class="btn btn-primary"><?php echo(lethe_save);?></button></td>
                      </tr>
                    </table>
                </form>
                <!-- Add Template End -->            
            </td>
          </tr>
        <?php }elseif($ppos==2){ # Edit Template?>
          <tr>
            <td>
            	<h1 class="panel-header"><?php echo(lethe_edit_template);?></h1>
				<?php $opTempList = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_templates WHERE ID=". $ID ."") or die(mysqli_error());
                if(mysqli_num_rows($opTempList)==0){echo('<div class="alert alert-info">'. lethe_record_not_found .'</div>');}else{
                    $opTempListRs = $opTempList->fetch_assoc();
                ?>
                <?php echo($errText);?>
            	<!-- Edit Template Start -->
                <form id="editTemp" role="form" action="" method="post">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="22%" height="35"><strong><?php echo(lethe_template_name);?></strong></td>
                        <td width="1%" height="35"><strong>:</strong></td>
                        <td width="77%" height="35"><input value="<?php echo($opTempListRs['title']);?>" class="form-control input-sm" name="title" type="text" id="title" size="50" maxlength="255"></td>
                      </tr>
                      <tr>
                        <td width="22%" height="35"><strong><?php echo(lethe_template_image);?></strong></td>
                        <td width="1%" height="35"><strong>:</strong></td>
                        <td width="77%" height="35">
                        	<div class="input-group">
	                        	<input placeholder="http://" value="<?php echo($opTempListRs['preview']);?>" class="form-control input-sm" name="prev_img" type="text" id="prev_img" size="50" maxlength="255">
			  <?php 
			  if(minipan_on==1){
				echo('
              <span class="input-group-btn">
                <button data-pan-model="fancybox" data-pan-field="prev_img" data-pan-link="default" data-pan-platform="normal" class="btn btn-default btn-sm minipan" type="button"><span class="glyphicon glyphicon-cloud-upload"></span></button>
              </span>
				');
			  }
			  ?>
                            </div>
                        </td>
                      </tr>
                      <tr>
                        <td width="22%" height="35"><strong><?php echo(lethe_short_codes);?></strong></td>
                        <td width="1%" height="35"><strong>:</strong></td>
                        <td width="77%" height="35">
                        	<div class="well" id="short_code_list">
                            	<?php
                                	for($i=0;$i<count($lethe_short_codes);$i++){ # List of static codes
										echo('<div class="label label-danger" data-lethe-codes="{'. $lethe_short_codes[$i] .'}" data-lethe-code-field="details">{'. $lethe_short_codes[$i] .'}</div> ');
										}
									$opCodes = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_codes ORDER BY lethe_code ASC") or die(mysqli_error());
									while($opCodesRs = $opCodes->fetch_assoc()){
										echo('<div class="label label-info" data-lethe-codes="{'. $opCodesRs['lethe_code'] .'}" data-lethe-code-field="details">{'. $opCodesRs['lethe_code'] .'}</div> ');
										}
									$opCodes->free();
								?>
                            </div>
                        </td>
                      </tr>
                      <tr>
                        <td height="35"><strong><?php echo(lethe_template_content);?></strong></td>
                        <td height="35"><strong>:</strong></td>
                        <td height="35"><textarea name="details" id="details" class="mceEditor"><?php echo($opTempListRs['details']);?></textarea></td>
                      </tr>
                      <tr>
                        <td height="40"><strong><?php echo(lethe_use_for_verification);?></strong></td>
                        <td height="40"><strong>:</strong></td>
                        <td height="40"><div class="make-switch" data-on="success" id="switch-verify" data-off="danger" data-on-label="<?php echo(lethe_yes);?>" data-off-label="<?php echo(lethe_no);?>"><input name="temp_verify" type="checkbox" id="temp_verify" value="YES"<?php echo(formSelector($opTempListRs['verification'],1,1));?>></div></td>
                      </tr>
                      <tr>
                        <td height="40"><strong><?php echo(lethe_d_elete);?></strong></td>
                        <td height="40"><strong>:</strong></td>
                        <td height="40"><input type="checkbox" value="YES" name="del" id="delTemp" onclick="if(this.checked==true){return confirm('<?php echo(lethe_are_you_sure_to_d_elete);?>');}"> <label for="delTemp"><?php echo(lethe_yes);?></label></td>
                      </tr>
                      <tr>
                        <td height="35">&nbsp;</td>
                        <td height="35">&nbsp;</td>
                        <td height="35">&nbsp;</td>
                      </tr>
                      <tr>
                        <td height="35">&nbsp;</td>
                        <td height="35">&nbsp;</td>
                        <td height="35"><button type="button" name="prev" id="prev" class="fancybox2 btn btn-warning"><?php echo(lethe_preview);?></button> <button type="submit" name="editTemplate" id="editTemplate" value="editTemplate" class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> <?php echo(lethe_save);?></button></td>
                      </tr>
                    </table>
                </form>
                <?php }
				$opTempList->free();
				?>
                <!-- Edit Template End -->  
            </td>
          </tr>
        <?php }?>
        </table>
        
    <!-- Templates End -->
    
    </td>
  </tr>
<?php }elseif($pos==4){ # Settings
if(admin_mode==1){ # Mode Check Start?>
  <tr>
    <td>
    
    <!-- Settings Start -->
    
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <?php if($ppos==0){ # General?>
          <tr>
            <td>
            
            <h1 class="panel-header"><?php echo(lethe_general_settings);?></h1>
            <!-- General Settings Start -->
            <?php echo($errText);?>
            <form role="form" name="editSettings" action="" method="post">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="25%" height="40"><strong><?php echo(lethe_site_url);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><input type="url" value="<?php echo(set_site_url);?>" name="site_url" class="form-control autoWidth" size="40" required></td>
                  </tr>
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_only_verified_mails);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><div class="make-switch" data-on="success" id="switch-verified" data-off="info" data-on-label="<?php echo(lethe_on);?>" data-off-label="<?php echo(lethe_off);?>"><input name="verified" type="checkbox" id="verified" value="YES"<?php echo(formSelector(set_only_verified,1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_only_active_mails);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><div class="make-switch" data-on="success" id="switch-active" data-off="info" data-on-label="<?php echo(lethe_on);?>" data-off-label="<?php echo(lethe_off);?>"><input name="active" type="checkbox" id="active" value="YES"<?php echo(formSelector(set_only_active,1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_random_loader);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><div class="make-switch" data-on="success" id="switch-random-load" data-off="info" data-on-label="<?php echo(lethe_on);?>" data-off-label="<?php echo(lethe_off);?>"><input name="random_load" type="checkbox" id="random_load" value="YES"<?php echo(formSelector(set_random_load,1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_send_verification);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><div class="make-switch" data-on="success" id="switch-send_verification" data-off="info" data-on-label="<?php echo(lethe_on);?>" data-off-label="<?php echo(lethe_off);?>"><input name="send_verification" type="checkbox" id="send_verification" value="YES"<?php echo(formSelector(set_send_verification,1,1));?>></div></td>
                  </tr>
                  
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_template_permission);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><div class="make-switch" data-on="success" id="switch-template_permission" data-off="info" data-on-label="<?php echo(lethe_admin);?>" data-off-label="<?php echo(lethe_all);?>"><input name="template_permission" type="checkbox" id="template_permission" value="YES"<?php echo(formSelector(set_template_permission,1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_subscriber_group_permission);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><div class="make-switch" data-on="success" id="switch-subgrp_permission" data-off="info" data-on-label="<?php echo(lethe_admin);?>" data-off-label="<?php echo(lethe_all);?>"><input name="subgrp_permission" type="checkbox" id="subgrp_permission" value="YES"<?php echo(formSelector(set_subgrp_permission,1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_subscriber_permission);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><div class="make-switch" data-on="success" id="switch-subscr_permission" data-off="info" data-on-label="<?php echo(lethe_admin);?>" data-off-label="<?php echo(lethe_all);?>"><input name="subscr_permission" type="checkbox" id="subscr_permission" value="YES"<?php echo(formSelector(set_subscr_permission,1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_export_import_permission);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><div class="make-switch" data-on="success" id="switch-exmp_imp_permission" data-off="info" data-on-label="<?php echo(lethe_admin);?>" data-off-label="<?php echo(lethe_all);?>"><input name="exmp_imp_permission" type="checkbox" id="exmp_imp_permission" value="YES"<?php echo(formSelector(set_exmp_imp_permission,1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_newsletter_permission);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><div class="make-switch" data-on="success" id="switch-newsletter_permission" data-off="info" data-on-label="<?php echo(lethe_admin);?>" data-off-label="<?php echo(lethe_all);?>"><input name="newsletter_permission" type="checkbox" id="newsletter_permission" value="YES"<?php echo(formSelector(set_newsletter_permission,1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_autoresponder_permission);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><div class="make-switch" data-on="success" id="switch-autoresponder_permission" data-off="info" data-on-label="<?php echo(lethe_admin);?>" data-off-label="<?php echo(lethe_all);?>"><input name="autoresponder_permission" type="checkbox" id="autoresponder_permission" value="YES"<?php echo(formSelector(set_autoresponder_permission,1,1));?>></div></td>
                  </tr>
                  
                  
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_default_timezone);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40">
                    	<select class="form-control autoWidth" name="set_def_timezone" id="set_def_timezone">
                        	<?php 
							$getZoneList = timezone_list();
							foreach($getZoneList as $v) { 
							?>
                        		<option value="<?php echo($v['timezone']);?>"<?php echo(formSelector($v['timezone'],set_def_timezone,0));?>><?php echo($v['loc_gmt']);?></option>
                            <?php }?>
                        </select>
                    </td>
                  </tr>
				  
                  <tr>
                    <td width="25%" height="40"><strong><?php echo(lethe_rss_url);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><input type="url" value="<?php echo(set_rss_url);?>" name="rss_url" class="form-control autoWidth" size="40">
						<span class="help-block" style="font-size:11px;">RSS: <?php echo(relDocs(LETHEPATH));?>/lethe.newsletter.php?pos=4</span>
					</td>
                  </tr>
				  
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_after_user_d_eleted);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40">
                    	<select class="form-control autoWidth" name="after_user_delete" id="after_user_delete">
                        	<option value="0"<?php echo(formSelector(set_after_user_delete,0,0));?>><?php echo(lethe_remove_all_entries);?></option>
                            <option value="1"<?php echo(formSelector(set_after_user_delete,1,0));?>><?php echo(lethe_remove_user_all_entries_link_to_super_admin);?></option>
                        </select>
                    </td>
                  </tr>
                  
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_after_unsubscribe);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40">
                    	<select class="form-control autoWidth" name="set_after_unsubscribe" id="set_after_unsubscribe">
                        	<option value="0"<?php echo(formSelector(set_after_unsubscribe,0,0));?>><?php echo(lethe_mark_it_inactive);?></option>
                            <option value="1"<?php echo(formSelector(set_after_unsubscribe,1,0));?>><?php echo(lethe_remove_from_list);?></option>
                            <option value="3"<?php echo(formSelector(set_after_unsubscribe,3,0));?>><?php echo(lethe_remove_from_database);?></option>
                            <option value="2"<?php echo(formSelector(set_after_unsubscribe,2,0));?>><?php echo(lethe_move_to_unsubscriber_list);?></option>
                        </select>
                    </td>
                  </tr>
                  
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_unique_code);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><div class="input-group"><input size="35" type="text" name="unique_code" id="unique_code" class="form-control autoWidth" value="<?php echo(set_unique_code);?>"><span class="input-group-btn autoWidth"><button type="button" name="gen_unique" id="gen_unique" class="btn btn-warning autoWidth"><span class="glyphicon glyphicon-refresh"></span></button></span></div></td>
                  </tr>
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_cron_path_for_bounces);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><input type="text" value="<?php echo(relDocs(LETHEPATH) . '/' . lethe_admin_path .'/lethe.bouncer.php');?>" onClick="this.select();" class="form-control"></td>
                  </tr>
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_cron_path_for_autoresponder);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><input type="text" value="<?php echo(relDocs(LETHEPATH) . '/' . lethe_admin_path .'/lethe.autoresponder.php');?>" onClick="this.select();" class="form-control"></td>
                  </tr>
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_cron_path);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><input type="text" value="<?php echo(relDocs(LETHEPATH) . '/' . lethe_admin_path .'/lethe.tasks.php');?>" onClick="this.select();" class="form-control">
					<span class="help-block" style="font-size:11px;">eg: */10 * * * * curl --request GET '<?php echo(relDocs(LETHEPATH) . '/' . lethe_admin_path .'/lethe.tasks.php');?>'</span>
					</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><button type="submit" name="updateSettings" value="updateSettings" class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> <?php echo(lethe_u_pdate)?></button></td>
                  </tr>
                </table>
            </form>
            <script>
            $('#gen_unique').click(function() {
                $('#unique_code').val(Math.random().toString(36).substr(2));
            });
            </script>
            <!-- General Settings End -->
            
            </td>
          </tr>
        <?php }elseif($ppos==1){ # Account List?>
          <tr>
            <td>
            
            <!-- Account List Start -->
            <h1 class="panel-header"><?php echo(lethe_submission_accounts);?></h1>
            <p><button onclick="location.href='?pos=4&ppos=2';" type="button" class="btn btn-success"><span class="glyphicon glyphicon-user"></span> <?php echo(lethe_add_account);?></button></p>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover">
              <thead>
              <tr>
                <th width="20%"><?php echo(lethe_account);?></th>
                <th width="20%"><?php echo(lethe_sender);?></th>
                <th width="5%"><?php echo(lethe_permission);?></th>
                <th width="5%"><?php echo(lethe_type);?></th>
                <th width="5%"><?php echo(lethe_secure);?></th>
                <th width="5%"><?php echo(lethe_active);?></th>
                <th width="10%"><?php echo(lethe_date);?></th>
              </tr>
              </thead>
              <tbody>
				<?php $opAccList = $myconn->query("SELECT ID,account_title,sender_mail,permission,send_type,ssl_tls,active,add_date FROM ". db_table_pref ."newsletter_accounts ORDER BY account_title ASC") or die(mysqli_error());
                while($opAccListRs = $opAccList->fetch_assoc()){?>
              <tr>
                <td><a href="?pos=4&ppos=3&ID=<?php echo($opAccListRs['ID']);?>"><?php echo($opAccListRs['account_title']);?></a></td>
                <td><?php echo($opAccListRs['sender_mail']);?></td>
                <td><?php if($opAccListRs['permission']==1){echo(lethe_admin);}else{echo(lethe_public);}?></td>
                <td><?php echo($lethe_mail_method[$opAccListRs['send_type']]);?></td>
                <td><?php echo($lethe_mail_secure[$opAccListRs['ssl_tls']]);?></td>
                <td><?php if($opAccListRs['active']==1){echo('<span class="glyphicon glyphicon-ok errorGreen"></span>');}else{echo('<span class="glyphicon glyphicon-remove errorRed"></span>');}?></td>
                <td><small><?php echo(setMyDate($opAccListRs['add_date'],3));?></small></td>
              </tr>
              	<?php }
				$opAccList->free();
				?>
              </tbody>
            </table>

            <!-- Account List End -->
            
            </td>
          </tr>
        <?php }elseif($ppos==2){ # Add Account?>
          <tr>
            <td>
            <!-- Add Account Start -->
            <h1 class="panel-header"><?php echo(lethe_add_account);?></h1>
            <?php echo($errText);?>
            	<form name="addAcc" id="addAcc" role="form" action="" method="post">
                <ul class="nav nav-tabs">
                	<li class="active"><a href="#sub-settings" data-toggle="tab"><?php echo(lethe_sending_settings);?></a></li>
                    <li><a href="#conn-settings" data-toggle="tab"><?php echo(lethe_connection_settings);?></a></li>
                    <li><a href="#data-save" data-toggle="tab"><?php echo(lethe_save);?></a></li>
                </ul>
                <div class="tab-content">
                <div class="tab-pane tab-pane-default active" id="sub-settings">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="31%" height="40"><label for="account_title"><?php echo(lethe_account_title);?></label></td>
                    <td width="2%" height="40"><strong>:</strong></td>
                    <td width="67%" height="40"><input value="<?php echo(@mysql_prep($_POST['account_title']));?>" name="account_title" type="text" class="form-control autoWidth input-sm" id="account_title" size="40" maxlength="255"></td>
                  </tr>
                  <tr>
                    <td width="31%" height="40"><label for="sender_title"><?php echo(lethe_from_title);?></label></td>
                    <td width="2%" height="40"><strong>:</strong></td>
                    <td width="67%" height="40"><input value="<?php echo(@mysql_prep($_POST['sender_title']));?>" name="sender_title" type="text" class="form-control autoWidth input-sm" id="sender_title" size="40" maxlength="255"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="sender_mail"><?php echo(lethe_from_e_mail);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo(@mysql_prep($_POST['sender_mail']));?>" name="sender_mail" onBlur="if(document.getElementById('reply_mail').value==''){document.getElementById('reply_mail').value=this.value;}" type="email" class="form-control autoWidth input-sm" id="sender_mail" size="40" maxlength="100"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="reply_mail"><?php echo(lethe_reply_e_mail);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo(@mysql_prep($_POST['reply_mail']));?>" name="reply_mail" type="email" class="form-control autoWidth input-sm" id="reply_mail" size="40" maxlength="100"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="test_mail"><?php echo(lethe_test_e_mail);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo(@mysql_prep($_POST['test_mail']));?>" name="test_mail" type="email" class="form-control autoWidth input-sm" id="test_mail" size="40" maxlength="100"></td>
                  </tr>
                  <tr>
                    <td height="40"><label><?php echo(lethe_permission);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-permission" data-off="info" data-on-label="<?php echo(lethe_admin);?>" data-off-label="<?php echo(lethe_public);?>"><input name="permission" type="checkbox" id="permission" value="YES"<?php echo(formSelector(@mysql_prep($_POST['permission']),1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td height="40"><label><?php echo(lethe_debug_mode);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-debug_mode" data-off="danger" data-on-label="<?php echo(lethe_on);?>" data-off-label="<?php echo(lethe_off);?>"><input name="debug_mode" type="checkbox" id="debug_mode" value="YES"<?php echo(formSelector(@mysql_prep($_POST['debug_mode']),1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="send_mail_limit"><?php echo(lethe_daily_send_limit);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="input-group"><input value="<?php echo(@mysql_prep($_POST['send_mail_limit']));?>" onkeydown="validateNumber(event);" name="send_mail_limit" type="number" id="send_mail_limit" class="form-control autoWidth input-sm" size="6" maxlength="5"><span class="input-group-addon autoWidth">~</span></div></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="mail_limit_per_con"><?php echo(lethe_mail_limit_per_connection);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo(@mysql_prep($_POST['mail_limit_per_con']));?>" onkeydown="validateNumber(event);" name="mail_limit_per_con" type="number" id="mail_limit_per_con" class="form-control autoWidth input-sm" size="6" maxlength="5"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="send_mail_duration"><?php echo(lethe_sending_duration.' ('. lethe_sec .')');?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo(@mysql_prep($_POST['send_mail_duration']));?>" onkeydown="validateNumber(event);" name="send_mail_duration" type="number" id="send_mail_duration" class="form-control autoWidth input-sm" size="6" maxlength="5"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="email_type"><?php echo(lethe_e_mail_type);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><select name="email_type" id="email_type" class="form-control autoWidth input-sm">
                    <option value="0"<?php echo(formSelector(@mysql_prep($_POST['email_type']),0,0));?>>HTML</option>
                    <option value="1"<?php echo(formSelector(@mysql_prep($_POST['email_type']),1,0));?>>Text</option>
                    </select></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="send_type"><?php echo(lethe_sending_method);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><select name="send_type" id="send_type" class="form-control autoWidth input-sm">
                    <?php foreach($lethe_mail_method as $key=>$value){
						echo('<option value="'. $key .'"'. formSelector(@$_POST['send_type'],$key,0) .'>'. $value .'</option>');	
					}
						?>
                    </select></td>
                  </tr>
                  <tr>
                    <td height="40"><label><?php echo(lethe_active);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-active" data-off="danger" data-on-label="<?php echo(lethe_yes);?>" data-off-label="<?php echo(lethe_no);?>"><input name="active" type="checkbox" id="active" value="YES" checked></div></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                </table>
                </div>
                <div class="tab-pane tab-pane-default" id="conn-settings">
                <div class="alert alert-info"><?php echo(lethe_pop3_fields_required_for_bounce_mails);?></div>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="31%" height="40"><label for="smtp_host"><?php echo(lethe_smtp_host);?></label></td>
                    <td width="2%" height="40"><strong>:</strong></td>
                    <td width="67%" height="40"><input onBlur="document.getElementById('pop3_host').value=this.value;document.getElementById('imap_host').value=this.value;" value="<?php echo(@mysql_prep($_POST['smtp_host']));?>" name="smtp_host" type="text" class="form-control autoWidth input-sm" id="smtp_host" size="40" maxlength="255"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="smtp_port"><?php echo(lethe_smtp_port);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input placeholder="587" value="<?php echo(@mysql_prep($_POST['smtp_port']));?>" onkeydown="validateNumber(event);" name="smtp_port" type="number" class="form-control autoWidth input-sm" id="smtp_port" size="5" maxlength="10"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="smtp_user"><?php echo(lethe_smtp_username);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo(@mysql_prep($_POST['smtp_user']));?>" onBlur="document.getElementById('pop3_user').value=this.value;document.getElementById('imap_user').value=this.value;" name="smtp_user" type="text" class="form-control autoWidth input-sm" id="smtp_user" size="40" maxlength="100"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="smtp_pass"><?php echo(lethe_smtp_password);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input name="smtp_pass" type="password" class="form-control autoWidth input-sm" id="smtp_pass" size="40" maxlength="100" autocomplete="off"></td>
                  </tr>
                  <tr>
                    <td height="40"><label><?php echo(lethe_smtp_auth);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-smtp_auth" data-off="danger" data-on-label="<?php echo(lethe_on);?>" data-off-label="<?php echo(lethe_off);?>"><input name="smtp_auth" type="checkbox" id="smtp_auth" value="YES"<?php echo(formSelector(@mysql_prep($_POST['smtp_auth']),1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td width="31%" height="40"><label for="pop3_host"><?php echo(lethe_pop3_host);?></label></td>
                    <td width="2%" height="40"><strong>:</strong></td>
                    <td width="67%" height="40"><input value="<?php echo(@mysql_prep($_POST['pop3_host']));?>" name="pop3_host" type="text" class="form-control autoWidth input-sm" id="pop3_host" size="40" maxlength="255"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="pop3_port"><?php echo(lethe_pop3_port);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input placeholder="110" value="<?php echo(@mysql_prep($_POST['pop3_port']));?>" onkeydown="validateNumber(event);" name="pop3_port" type="number" class="form-control autoWidth input-sm" id="pop3_port" size="5" maxlength="10"></td>
                  </tr>
                  <tr>
                    <td width="31%" height="40"><label for="pop3_user"><?php echo(lethe_pop3_username);?></label></td>
                    <td width="2%" height="40"><strong>:</strong></td>
                    <td width="67%" height="40"><input value="<?php echo(@mysql_prep($_POST['pop3_user']));?>" name="pop3_user" type="text" class="form-control autoWidth input-sm" id="pop3_user" size="40" maxlength="255"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="pop3_pass"><?php echo(lethe_pop3_pass);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo(@mysql_prep($_POST['pop3_pass']));?>" name="pop3_pass" type="password" class="form-control autoWidth input-sm" id="pop3_pass" size="40"></td>
                  </tr>
                  <tr>
                    <td width="31%" height="40"><label for="imap_host"><?php echo(lethe_imap_host);?></label></td>
                    <td width="2%" height="40"><strong>:</strong></td>
                    <td width="67%" height="40"><input value="<?php echo(@mysql_prep($_POST['imap_host']));?>" name="imap_host" type="text" class="form-control autoWidth input-sm" id="imap_host" size="40" maxlength="255"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="imap_port"><?php echo(lethe_imap_port);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input placeholder="143" value="<?php echo(@mysql_prep($_POST['imap_port']));?>" onkeydown="validateNumber(event);" name="imap_port" type="number" class="form-control autoWidth input-sm" id="imap_port" size="5" maxlength="10"></td>
                  </tr>
                  <tr>
                    <td width="31%" height="40"><label for="imap_user"><?php echo(lethe_imap_username);?></label></td>
                    <td width="2%" height="40"><strong>:</strong></td>
                    <td width="67%" height="40"><input value="<?php echo(@mysql_prep($_POST['imap_user']));?>" name="imap_user" type="text" class="form-control autoWidth input-sm" id="imap_user" size="40" maxlength="255"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="imap_pass"><?php echo(lethe_imap_pass);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo(@mysql_prep($_POST['imap_pass']));?>" name="imap_pass" type="password" class="form-control autoWidth input-sm" id="imap_pass" size="40"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="ssl_tls"><?php echo(lethe_ssl_tls);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><select name="ssl_tls" id="ssl_tls" class="form-control autoWidth input-sm">
                    <?php foreach($lethe_mail_secure as $key=>$value){
						echo('<option value="'. $key .'"'. formSelector(@$_POST['ssl_tls'],$key,0) .'>'. $value .'</option>');	
					}
						?>
                    </select></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="bounce_acc"><?php echo(lethe_bounce_account);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><select name="bounce_acc" id="bounce_acc" class="form-control autoWidth input-sm">
                    <option value="0"<?php echo(formSelector(@$_POST['bounce_acc'],0,0));?>>POP3</option>
					<option value="1"<?php echo(formSelector(@$_POST['bounce_acc'],1,0));?>>IMAP</option>
                    </select></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                </table>
                <script>
                $('#test_conn').click(function (e) {
						var btn = $(this);
						btn.button('loading');
						var serializedData = $("#addAcc").serialize();
						
						$.ajax({
								url: '/<?php echo(lethe_admin_path);?>/index.php?ajax=1&ajax_part=7&ID=',
								type: 'POST',
								data: serializedData ,
								success: function (response) {
									btn.button('reset');
									$('#test-result').html(response);
								},
								error: function () {

								}
							}); 
						});
                </script>
                </div>
                <div class="tab-pane tab-pane-default" id="data-save">
                <button type="submit" name="addAccount" value="addAccount" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> <?php echo(lethe_add_account)?></button>
                </div>
                </div>
                </form>
			<!-- Add Account End -->            
            </td>
          </tr>
        <?php }elseif($ppos==3){ # Edit Account?>
          <tr>
            <td>
            
            <!-- Edit Account Start -->
            <h1 class="panel-header"><?php echo(lethe_edit_account);?></h1>
            <?php $opAccList = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_accounts WHERE ID=". $ID ."") or die(mysqli_error());
			if(mysqli_num_rows($opAccList)==0){echo('<div class="alert alert-info">'. lethe_record_not_found .'</div>');}else{
				$opAccListRs = $opAccList->fetch_assoc();
			?>
            <?php echo($errText);?>
            	<form name="editAcc" id="editAcc" role="form" action="" method="post">
                <ul class="nav nav-tabs">
                	<li class="active"><a href="#sub-settings" data-toggle="tab"><?php echo(lethe_sending_settings);?></a></li>
                    <li><a href="#conn-settings" data-toggle="tab"><?php echo(lethe_connection_settings);?></a></li>
                    <li><a href="#data-save" data-toggle="tab"><?php echo(lethe_save);?></a></li>
                </ul>
                <div class="tab-content">
                <div class="tab-pane tab-pane-default active" id="sub-settings">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="31%" height="40"><label for="account_title"><?php echo(lethe_account_title);?></label></td>
                    <td width="2%" height="40"><strong>:</strong></td>
                    <td width="67%" height="40"><input value="<?php echo($opAccListRs['account_title']);?>" name="account_title" type="text" class="form-control autoWidth input-sm" id="account_title" size="40" maxlength="255"></td>
                  </tr>
                  <tr>
                    <td width="31%" height="40"><label for="sender_title"><?php echo(lethe_from_title);?></label></td>
                    <td width="2%" height="40"><strong>:</strong></td>
                    <td width="67%" height="40"><input value="<?php echo($opAccListRs['sender_title']);?>" name="sender_title" type="text" class="form-control autoWidth input-sm" id="sender_title" size="40" maxlength="255"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="sender_mail"><?php echo(lethe_from_e_mail);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo($opAccListRs['sender_mail']);?>" name="sender_mail" type="email" class="form-control autoWidth input-sm" id="sender_mail" size="40" maxlength="100"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="reply_mail"><?php echo(lethe_reply_e_mail);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo($opAccListRs['reply_mail']);?>" name="reply_mail" type="email" class="form-control autoWidth input-sm" id="reply_mail" size="40" maxlength="100"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="test_mail"><?php echo(lethe_test_e_mail);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo($opAccListRs['test_mail']);?>" name="test_mail" type="email" class="form-control autoWidth input-sm" id="test_mail" size="40" maxlength="100"></td>
                  </tr>
                  <tr>
                    <td height="40"><label><?php echo(lethe_permission);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-permission" data-off="info" data-on-label="<?php echo(lethe_admin);?>" data-off-label="<?php echo(lethe_public);?>"><input name="permission" type="checkbox" id="permission" value="YES"<?php echo(formSelector($opAccListRs['permission'],1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td height="40"><label><?php echo(lethe_debug_mode);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-debug_mode" data-off="danger" data-on-label="<?php echo(lethe_on);?>" data-off-label="<?php echo(lethe_off);?>"><input name="debug_mode" type="checkbox" id="debug_mode" value="YES"<?php echo(formSelector(@mysql_prep($opAccListRs['debug_mode']),1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="send_mail_limit"><?php echo(lethe_daily_send_limit);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="input-group"><input value="<?php echo($opAccListRs['send_mail_limit']);?>" onkeydown="validateNumber(event);" name="send_mail_limit" type="number" id="send_mail_limit" class="form-control autoWidth input-sm" size="6" maxlength="5"><span class="input-group-addon autoWidth"><?php echo($opAccListRs['dailySent']);?></span></div></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="mail_limit_per_con"><?php echo(lethe_mail_limit_per_connection);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo($opAccListRs['mail_limit_per_con']);?>" onkeydown="validateNumber(event);" name="mail_limit_per_con" type="number" id="mail_limit_per_con" class="form-control autoWidth input-sm" size="6" maxlength="5"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="send_mail_duration"><?php echo(lethe_sending_duration.' ('. lethe_sec .')');?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo($opAccListRs['send_mail_duration']);?>" onkeydown="validateNumber(event);" name="send_mail_duration" type="number" id="send_mail_duration" class="form-control autoWidth input-sm" size="6" maxlength="5"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="email_type"><?php echo(lethe_e_mail_type);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><select name="email_type" id="email_type" class="form-control autoWidth input-sm">
                    <option value="0"<?php echo(formSelector($opAccListRs['email_type'],0,0));?>>HTML</option>
                    <option value="1"<?php echo(formSelector($opAccListRs['email_type'],1,0));?>>Text</option>
                    </select></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="send_type"><?php echo(lethe_sending_method);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><select name="send_type" id="send_type" class="form-control autoWidth input-sm">
                    <?php foreach($lethe_mail_method as $key=>$value){
						echo('<option value="'. $key .'"'. formSelector($opAccListRs['send_type'],$key,0) .'>'. $value .'</option>');	
					}
						?>
                    </select></td>
                  </tr>
                  <tr>
                    <td height="40"><label><?php echo(lethe_active);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-active" data-off="danger" data-on-label="<?php echo(lethe_yes);?>" data-off-label="<?php echo(lethe_no);?>"><input name="active" type="checkbox" id="active" value="YES"<?php echo(formSelector($opAccListRs['active'],1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_d_elete);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input type="checkbox" value="YES" name="del" id="delAcc" onclick="if(this.checked==true){return confirm('<?php echo(lethe_are_you_sure_to_d_elete);?>');}"> <label for="delAcc"><?php echo(lethe_yes);?></label></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                </table>
                </div>
                <div class="tab-pane tab-pane-default" id="conn-settings">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="31%" height="40"><label for="smtp_host"><?php echo(lethe_smtp_host);?></label></td>
                    <td width="2%" height="40"><strong>:</strong></td>
                    <td width="67%" height="40"><input onBlur="if(document.getElementById('pop3_host').value==''){document.getElementById('pop3_host').value=this.value;}" value="<?php echo($opAccListRs['smtp_host']);?>" name="smtp_host" type="text" class="form-control autoWidth input-sm" id="smtp_host" size="40" maxlength="255"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="smtp_port"><?php echo(lethe_smtp_port);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo($opAccListRs['smtp_port']);?>" onkeydown="validateNumber(event);" name="smtp_port" type="number" class="form-control autoWidth input-sm" id="smtp_port" size="5" maxlength="10"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="smtp_user"><?php echo(lethe_smtp_username);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo($opAccListRs['smtp_user']);?>" name="smtp_user" type="text" class="form-control autoWidth input-sm" id="smtp_user" size="40" maxlength="100"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="smtp_pass"><?php echo(lethe_smtp_password);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input name="smtp_pass" type="password" class="form-control autoWidth input-sm" id="smtp_pass" size="40" maxlength="100" autocomplete="off"></td>
                  </tr>
                  <tr>
                    <td height="40"><label><?php echo(lethe_smtp_auth);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-smtp_auth" data-off="danger" data-on-label="<?php echo(lethe_on);?>" data-off-label="<?php echo(lethe_off);?>"><input name="smtp_auth" type="checkbox" id="smtp_auth" value="YES"<?php echo(formSelector(@mysql_prep($opAccListRs['smtp_auth']),1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td width="31%" height="40"><label for="pop3_host"><?php echo(lethe_pop3_host);?></label></td>
                    <td width="2%" height="40"><strong>:</strong></td>
                    <td width="67%" height="40"><input value="<?php echo($opAccListRs['pop3_host']);?>" name="pop3_host" type="text" class="form-control autoWidth input-sm" id="pop3_host" size="40" maxlength="255"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="pop3_port"><?php echo(lethe_pop3_port);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input placeholder="110" value="<?php echo($opAccListRs['pop3_port']);?>" onkeydown="validateNumber(event);" name="pop3_port" type="number" class="form-control autoWidth input-sm" id="pop3_port" size="5" maxlength="10"></td>
                  </tr>
                  <tr>
                    <td width="31%" height="40"><label for="pop3_user"><?php echo(lethe_pop3_username);?></label></td>
                    <td width="2%" height="40"><strong>:</strong></td>
                    <td width="67%" height="40"><input value="<?php echo($opAccListRs['pop3_user']);?>" name="pop3_user" type="text" class="form-control autoWidth input-sm" id="pop3_user" size="40" maxlength="255"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="pop3_pass"><?php echo(lethe_pop3_pass);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="" name="pop3_pass" type="password" class="form-control autoWidth input-sm" id="pop3_pass" size="40"></td>
                  </tr>
                  <tr>
                    <td width="31%" height="40"><label for="imap_host"><?php echo(lethe_imap_host);?></label></td>
                    <td width="2%" height="40"><strong>:</strong></td>
                    <td width="67%" height="40"><input value="<?php echo($opAccListRs['imap_host']);?>" name="imap_host" type="text" class="form-control autoWidth input-sm" id="imap_host" size="40" maxlength="255"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="imap_port"><?php echo(lethe_imap_port);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input placeholder="143" value="<?php echo($opAccListRs['imap_port']);?>" onkeydown="validateNumber(event);" name="imap_port" type="number" class="form-control autoWidth input-sm" id="imap_port" size="5" maxlength="10"></td>
                  </tr>
                  <tr>
                    <td width="31%" height="40"><label for="imap_user"><?php echo(lethe_imap_username);?></label></td>
                    <td width="2%" height="40"><strong>:</strong></td>
                    <td width="67%" height="40"><input value="<?php echo($opAccListRs['imap_user']);?>" name="imap_user" type="text" class="form-control autoWidth input-sm" id="imap_user" size="40" maxlength="255"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="imap_pass"><?php echo(lethe_imap_pass);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="" name="imap_pass" type="password" class="form-control autoWidth input-sm" id="imap_pass" size="40"></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="ssl_tls"><?php echo(lethe_ssl_tls);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><select name="ssl_tls" id="ssl_tls" class="form-control autoWidth input-sm">
                    <?php foreach($lethe_mail_secure as $key=>$value){
						echo('<option value="'. $key .'"'. formSelector($opAccListRs['ssl_tls'],$key,0) .'>'. $value .'</option>');	
					}
						?>
                    </select></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="ssl_tls"><?php echo(lethe_bounce_account);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><select name="bounce_acc" id="bounce_acc" class="form-control autoWidth input-sm">
                    <option value="0"<?php echo(formSelector(@$opAccListRs['bounce_acc'],0,0));?>>POP3</option>
					<option value="1"<?php echo(formSelector(@$opAccListRs['bounce_acc'],1,0));?>>IMAP</option>
                    </select></td>
                  </tr>
				  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                </table>
                </div>
                <div class="tab-pane tab-pane-default" id="data-save">
                <button type="submit" name="editAccount" value="editAccount" class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> <?php echo(lethe_u_pdate)?></button>
                </div>
                </div>
                </form>
                <?php }
				$opAccList->free();
				?>
			<!-- Edit Account End -->
            
            
            </td>
          </tr>
        <?php }elseif($ppos==4){ # User List?>
          <tr>
            <td>
            	<h1 class="panel-header"><?php echo(lethe_users);?></h1>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><button onclick="location.href='?pos=4&ppos=5';" type="button" class="btn btn-success"><span class="glyphicon glyphicon-user"></span> <?php echo(lethe_add_user);?></button></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>
                    
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover">
                        <thead>
                          <tr>
                            <th width="30%"><?php echo(lethe_username);?></th>
                            <th width="30%"><?php echo(lethe_e_mail);?></th>
                            <th width="20%"><?php echo(lethe_admin_mode);?></th>
                            <th width="30%"><?php echo(lethe_date);?></th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php $opUserList = $myconn->query("SELECT * FROM ". db_table_pref ."users ORDER BY lethe_user ASC") or die(mysqli_error());
						while($opUserListRs = $opUserList->fetch_assoc()){?>
                          <tr>
                            <td><a href="?pos=4&ppos=6&ID=<?php echo($opUserListRs['ID']);?>"><?php echo($opUserListRs['lethe_user']);?></a></td>
                            <td><?php echo($opUserListRs['user_mail']);?></td>
                            <td><?php if($opUserListRs['admin_mode']==1){echo(lethe_super_admin);}else{echo(lethe_user);}?></td>
                            <td><?php echo(setMyDate($opUserListRs['add_date'],6));?></td>
                          </tr>
                        <?php }
						$opUserList->free();
						?>
                        </tbody>
                        </table>
                    
                    </td>
                  </tr>
                </table>

            </td>
          </tr>
        <?php }elseif($ppos==5){ # Add User?>
          <tr>
            <td>
            <h1 class="panel-header"><?php echo(lethe_add_user);?></h1>
            <!-- Add User Start -->
            <?php echo($errText);?>
            <form role="form" name="addNewUser" action="" method="post">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_username);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><input class="form-control autoWidth inlineBlock" name="username" type="text" id="username" maxlength="50" onBlur="getAjax('#user-check','<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=1&letData=')?>'+this.value,'<span class=glyphicon glyphicon-refresh></span>');"> <span id="user-check"></span></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_password);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input class="form-control autoWidth" name="pass" type="password" id="pass" maxlength="50"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_re_type);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input class="form-control autoWidth" name="pass2" type="password" id="pass2" maxlength="50"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_e_mail);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input class="form-control autoWidth inlineBlock" name="email" type="mail" id="email" maxlength="100" onBlur="getAjax('#mail-check','<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=2&letData=')?>'+this.value,'<span class=glyphicon glyphicon-refresh></span>');"> <span id="mail-check"></span></td>
                  </tr>
                  <tr>
                    <td height="40"><label for="send_mail_limit"><?php echo(lethe_daily_send_limit);?></label></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input placeholder="100" value="" onkeydown="validateNumber(event);" name="send_mail_limit" type="number" id="send_mail_limit" class="form-control autoWidth input-sm" size="6" maxlength="5"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_admin_mode);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><select class="form-control autoWidth" name="amode" id="amode">
                    <option value="2"><?php echo(lethe_user);?></option>
                    <option value="1"><?php echo(lethe_super_admin);?></option>
                    </select></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><button type="submit" name="addUser" value="addUser" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> <?php echo(lethe_add_user)?></button></td>
                  </tr>
                </table>
            </form>
            <!-- Add User End -->
            
            </td>
          </tr>
        <?php }elseif($ppos==6){ # Edit User?>
          <tr>
            <td>
            <h1 class="panel-header"><?php echo(lethe_edit_user);?></h1>
            <!-- Edit User Start -->
            <?php $opUserList = $myconn->query("SELECT * FROM ". db_table_pref ."users WHERE ID=". $ID ."") or die(mysqli_error());
			if(mysqli_num_rows($opUserList)==0){echo('<div class="alert alert-info">'. lethe_record_not_found .'</div>');}else{
				$opUserListRs = $opUserList->fetch_assoc();
			?>
            <?php echo($errText);?>
            <form role="form" name="editUsers" action="" method="post">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_username);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><input value="<?php echo($opUserListRs['lethe_user']);?>" class="form-control autoWidth inlineBlock" name="username" type="text" id="username" maxlength="50"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_password);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input class="form-control autoWidth" name="pass" type="password" id="pass" maxlength="50" autocomplete="off"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_re_type);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input class="form-control autoWidth" name="pass2" type="password" id="pass2" maxlength="50" autocomplete="off"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_e_mail);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo($opUserListRs['user_mail']);?>" class="form-control autoWidth inlineBlock" name="email" type="mail" id="email" maxlength="100"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_daily_send_limit);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="input-group"><input value="<?php echo($opUserListRs['sendLimit']);?>" class="form-control autoWidth inlineBlock" onkeydown="validateNumber(event);" name="send_mail_limit" type="number" id="send_mail_limit" maxlength="7"><span class="input-group-addon autoWidth"><?php echo($opUserListRs['dailySent']);?></span></div></td>
                  </tr>
                  <?php if($opUserListRs['primary_user']!=1){?>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_admin_mode);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><select class="form-control autoWidth" name="amode" id="amode">
                    <option value="2"<?php echo(formSelector($opUserListRs['admin_mode'],2,0));?>><?php echo(lethe_user);?></option>
                    <option value="1"<?php echo(formSelector($opUserListRs['admin_mode'],1,0));?>><?php echo(lethe_super_admin);?></option>
                    </select></td>
                  </tr>
                  <?php }?>
                  <?php if($opUserListRs['primary_user']==0){?>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_d_elete);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input type="checkbox" value="YES" name="del" id="delUser" onclick="if(this.checked==true){return confirm('<?php echo(lethe_are_you_sure_to_d_elete);?>');}"> <label for="delUser"><?php echo(lethe_yes);?></label></td>
                  </tr>
                  <?php }?>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><button type="submit" name="editUser" value="editUser" class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> <?php echo(lethe_u_pdate)?></button></td>
                  </tr>
                </table>
            </form>
            <?php }
			$opUserList->free();
			?>
            <!-- Edit User End -->
            
        <?php }elseif($ppos==7){ # Short Codes?>
          <tr>
            <td>
            <h1 class="panel-header"><?php echo(lethe_short_codes);?></h1>
            <!-- Short Codes Start -->
                <form role="form" name="shortCodes" method="post" action="">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped">
                <thead>
                  <tr>
                    <th width="5%"><?php echo(lethe_d_elete);?></th>
                    <th width="45%"><?php echo(lethe_code);?></th>
                    <th width="50%"><?php echo(lethe_value);?></th>
                  </tr>
                </thead>
                <tbody>
                <?php for($i=0;$i<count($lethe_short_codes);$i++){?>
                  <tr>
                    <td><input type="checkbox" disabled></td>
                    <td><?php echo('{'.$lethe_short_codes[$i].'}');?></td>
                    <td>&nbsp;</td>
                  </tr>
                <?php }
				$opCodes = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_codes ORDER BY lethe_code ASC") or die(mysqli_error());
				while($opCodesRs = $opCodes->fetch_assoc()){
				?>
                  <tr>
                    <td><input type="checkbox" name="delCode[]" value="<?php echo($opCodesRs['ID']);?>"></td>
                    <td><strong>{<?php echo($opCodesRs['lethe_code']);?>}</strong></td>
                    <td><?php echo($opCodesRs['lethe_code_val']);?></td>
                  </tr>
                <?php }
				$opCodes->free();
				?>
                  <tr>
                    <td>&nbsp;</td>
                    <td><input class="form-control autoWidth" type="text" value="" name="new_code" placeholder="<?php echo(lethe_new_code);?>"></td>
                    <td><input class="form-control autoWidth" type="text" value="" name="new_code_val" placeholder="<?php echo(lethe_new_code_value);?>"></td>
                  </tr>
                  <tr class="non-striped">
                    <td colspan="3"><button name="editShortCodes" value="editShortCodes" class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> <?php echo(lethe_u_pdate);?></button></td>
                  </tr>
                </tbody>
                </table>
                </form>
            <!-- Short Codes End -->
            
            </td>
          </tr>
          
        <?php }elseif($ppos==8){ # Bounce Catcher?>
          <tr>
            <td>
		    <?php if(beta_mode){echo('<span style="font-size:11px;" class="label label-default">BETA</span>');}?>
            <h1 class="panel-header"><?php echo(lethe_bounce_catcher);?></h1>
            <!-- Bounce Catcher Start -->
                <form role="form" name="bounceCodes" method="post" action="">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped">
                <thead>
                  <tr>
                    <th width="5%"><?php echo(lethe_d_elete);?></th>
                    <th width="45%"><?php echo(lethe_code);?></th>
                    <th width="45%"><?php echo(lethe_bounce_rule);?></th>
                    <th width="50%"><?php echo(lethe_active);?></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>&nbsp;</td>
                    <td><input class="form-control" type="text" value="" name="new_code" placeholder="<?php echo(lethe_new_code);?>"></td>
                    <td>
                    	<select class="form-control autoWidth" name="set_after_bounce" id="set_after_bounce">
                        	<option value="0"><?php echo(lethe_mark_it_inactive);?></option>
                            <option value="1"><?php echo(lethe_remove_from_list);?></option>
                            <option value="2"><?php echo(lethe_remove_and_add_to_blacklist);?></option>
                        </select>
                    </td>
                    <td><div class="make-switch" data-on="success" id="switch-new-code-act" data-off="danger" data-on-label="<?php echo(lethe_yes);?>" data-off-label="<?php echo(lethe_no);?>"><input name="new_code_act" type="checkbox" id="new_code_act" value="YES" checked></div></td>
                  </tr>
                <?php 
$limit = 25;
$pgGo = @$_GET["pgGo"];
if(empty($pgGo) or !is_numeric($pgGo)) {$pgGo = 1;}

 $count		 = mysqli_num_rows($myconn->query("SELECT ID FROM ". db_table_pref ."newsletter_bounce_catcher"));
 $total_page	 = ceil($count / $limit);
 $dtStart	 = ($pgGo-1)*$limit;
				
				$opCodes = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_bounce_catcher ORDER BY bounce_code ASC LIMIT $dtStart,$limit") or die(mysqli_error());
				while($opCodesRs = $opCodes->fetch_assoc()){
				
				?>
                  <tr>
                    <td><input type="checkbox" name="delCode[]" value="<?php echo($opCodesRs['ID']);?>"></td>
                    <td><strong><?php echo($opCodesRs['bounce_code']);?></strong></td>
                    <td>
                    	<select class="form-control input-sm autoWidth" name="set_after_bounce[]" id="set_after_bounce<?php echo($opCodesRs['ID']);?>">
                        	<option value="0"<?php echo(formSelector($opCodesRs['bounce_rule'],0,0));?>><?php echo(lethe_mark_it_inactive);?></option>
                            <option value="1"<?php echo(formSelector($opCodesRs['bounce_rule'],1,0));?>><?php echo(lethe_remove_from_list);?></option>
                            <option value="2"<?php echo(formSelector($opCodesRs['bounce_rule'],2,0));?>><?php echo(lethe_remove_and_add_to_blacklist);?></option>
                        </select>
                    </td>
                    <td><div class="make-switch switch-mini" data-on="success" id="switch-new-code-act-<?php echo($opCodesRs['ID']);?>" data-off="danger" data-on-label="<?php echo(lethe_yes);?>" data-off-label="<?php echo(lethe_no);?>"><input name="new_code_act" type="checkbox" id="new_code_act" value="YES"<?php echo(formSelector($opCodesRs['active'],1,1));?>></div></td>
                  </tr>
                <?php }
				$opCodes->free();
				?>
				<?php if($total_page>1){?>
                  <tr class="non-striped">
                    <td colspan="4">&nbsp;</td>
                  </tr>
                  <tr class="non-striped">
                    <td colspan="4"><?php $pgVar='?pos='. $pos .'&ppos='. $ppos;include("inc/inc_pagination.php");?></td>
                  </tr>
                <?php }?>
                  <tr class="non-striped">
                    <td colspan="4"><button name="editBounceCodes" value="editBounceCodes" class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> <?php echo(lethe_u_pdate);?></button></td>
                  </tr>
                </tbody>
                </table>
                </form>
            <!-- Bounce Catcher End -->
            
            </td>
          </tr>
		<?php }?>
        </table>
        
    <!-- Settings End -->
    
    </td>
  </tr>
<?php } else {echo('<tr><td><div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span> '. lethe_you_dont_have_permission_to_access_view_this_page .'</div></td></tr>');}# Mode Check End?>

<?php }elseif($pos==5){ # Update Profile?>
<tr>
	<td>
    
            <h1 class="panel-header"><?php echo(lethe_edit_profile);?></h1>
            <!-- Edit User Start -->
            <?php $opUserList = $myconn->query("SELECT * FROM ". db_table_pref ."users WHERE ID=". admin_ID ." AND admin_mode=2") or die(mysqli_error());
			if(mysqli_num_rows($opUserList)==0){echo('<div class="alert alert-info">'. lethe_record_not_found .'</div>');}else{
				$opUserListRs = $opUserList->fetch_assoc();
			?>
            <?php echo($errText);?>
            <form role="form" name="editProfile" action="" method="post">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="18%" height="40"><strong><?php echo(lethe_username);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="81%" height="40"><?php echo($opUserListRs['lethe_user']);?></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_password);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input class="form-control autoWidth" name="pass" type="password" id="pass" maxlength="50" autocomplete="off"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_re_type);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input class="form-control autoWidth" name="pass2" type="password" id="pass2" maxlength="50" autocomplete="off"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_e_mail);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input value="<?php echo($opUserListRs['user_mail']);?>" class="form-control autoWidth inlineBlock" name="email" type="mail" id="email" maxlength="100"></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><button type="submit" name="editProfiles" value="editUser" class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> <?php echo(lethe_u_pdate)?></button></td>
                  </tr>
                </table>
            </form>
            <?php }
			$opUserList->free();
			?>
            <!-- Edit User End -->
    
    </td>
</tr> 

<?php }elseif($pos==6){ # Auto Responders?> 
  <tr>
    <td>
 <?php if(beta_mode){echo('<span style="font-size:11px;" class="label label-default">BETA</span>');}?>
    <!-- Autoresponder Start -->

<!-- Date Picker -->
<link rel="stylesheet" type="text/css" href="css/jquery.datepick.css"> 
<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.23.css"> 
<script type="text/javascript" src="Scripts/datepicker/jquery.datepick.js"></script>
<script type="text/javascript" src="Scripts/datepicker/jquery.datepick-en.js"></script>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <?php if($ppos==0){ # Autoresponder List?>
          <tr>
            <td>
            
            <h1 class="panel-header"><?php echo(lethe_autoresponders);?></h1>
<!-- Search Panel -->
<?php 
# *** Search Queries Start **
$onSrc=0;
$src = "";
$pgQuery = "";

	# Keywords
	if(isset($_GET['src_v']) && !empty($_GET['src_v'])){
			$src_v = mysql_prep(trim($_GET['src_v']));
			$src .= " AND (UPPER(subject) LIKE '%". strtoupper($src_v) ."%')";
					  $pgQuery .= "&amp;src_v=" . $src_v;
			$onSrc=1;
		}
				
	# Launch Date
	if(isset($_GET['src_ld_fr']) && isset($_GET['src_ld_to'])){
		if(!empty($_GET['src_ld_fr']) && !empty($_GET['src_ld_to'])){
			$dt_f = date('Y-m-d',strtotime($_GET['src_ld_fr']));
			$dt_t = date('Y-m-d',strtotime($_GET['src_ld_to']));
			$src .= " AND (launch_date BETWEEN '". $dt_f ."' AND '". $dt_t ."')";
					  $pgQuery .= "&amp;src_ld_fr=". mysql_prep($_GET['src_ld_fr']) ."&amp;src_ld_to=". mysql_prep($_GET['src_ld_to']) ."";
			$onSrc=1;
		}
		}
		
	# Status
	if(isset($_GET['src_status']) && is_numeric($_GET['src_status'])){
			if($_GET['src_status']!=-1){
				$src .= " AND (position=". intval($_GET['src_status']) .")";
			}
			$pgQuery .= "&amp;src_status=" . $_GET['src_status'];
			$onSrc=1;
		}

# *** Search Queries End **

# ******** Order Queries **********************
$dtOrder = "";
$qrOrder = " ORDER BY subject ASC"; # Default Ordering
$qrOrd = mysql_prep(@$_GET['qrOrd']); # Order Area like title, date
$qrOrdPos = intval(@$_GET['qrOrdPos']); # Order Pos as 1 - ASC, 2 - DESC
	
	# Order By Title
	if($qrOrd=='byTitle'){
		if($qrOrdPos==0){
			$qrOrder = " ORDER BY subject ASC";
			$dtOrder = '&amp;qrOrd=byTitle&amp;qrOrdPos=0';
			}
		elseif($qrOrdPos==1){
			$qrOrder = " ORDER BY subject DESC";
			$dtOrder = '&amp;qrOrd=byTitle&amp;qrOrdPos=1';
			}
		}
					
	# Order By Date
	if($qrOrd=='byDate'){
		if($qrOrdPos==0){
			$qrOrder = " ORDER BY launch_date ASC";
			$dtOrder = '&amp;qrOrd=byDate&amp;qrOrdPos=0';
			}
		elseif($qrOrdPos==1){
			$qrOrder = " ORDER BY launch_date DESC";
			$dtOrder = '&amp;qrOrd=byDate&amp;qrOrdPos=1';
			}
		}
	
	
# ******** Order Queries **********************
?>
<div id="search_panel">
	<div class="well">
		<form action="" method="get" name="search_form">
        <input type="hidden" name="pos" value="<?php echo($pos);?>">
        <input type="hidden" name="ppos" value="<?php echo($ppos);?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="40" width="15%"><strong><?php echo(lethe_search);?></strong></td>
    <td width="2%"><strong>:</strong></td>
    <td><div class="input-group"><input type="text" value="<?php echo(mysql_prep(@$_GET['src_v']));?>" name="src_v" class="form-control autoWidth" size="35"><span class="input-group-btn autoWidth"><button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> <?php echo(lethe_find);?></button></span></div></td>
  </tr>
  <tr>
    <td height="40"><strong><?php echo(lethe_launch_date);?></strong></td>
    <td><strong>:</strong></td>
    <td><?php echo(lethe_from);?>: <input type="text" value="<?php echo(mysql_prep(@$_GET['src_ld_fr']));?>" name="src_ld_fr" id="src_ld_fr" class="form-control autoWidth inlineBlock" placeholder="dd-mm-yyyy"> <?php echo(lethe_to);?>: <input placeholder="dd-mm-yyyy" type="text" value="<?php echo(mysql_prep(@$_GET['src_ld_to']));?>" name="src_ld_to" id="src_ld_to" class="form-control autoWidth inlineBlock"><script>$(document).ready(function(){$('#src_ld_fr').datepick({dateFormat: 'dd-mm-yyyy'});$('#src_ld_to').datepick({dateFormat: 'dd-mm-yyyy'});});</script></td>
  </tr>
  <tr>
    <td height="40"><strong><?php echo(lethe_status);?></strong></td>
    <td><strong>:</strong></td>
    <td><select name="src_status" id="src_status" class="form-control autoWidth">
    	<?php 
				echo('<option value="-1">'. lethe_all .'</option>');
			for($i=0;$i<count($lethe_status_mode);$i++){
				echo('<option value="'. $i .'"'. formSelector(@$_GET['src_status'],$i,0) .'>'. $lethe_status_mode[$i] .'</option>');
			}?>
    </select></td>
  </tr>
</table>

        </form>
    </div>
</div>
<?php if($onSrc==1){echo('<script>$("#search_panel").toggle("slideDown");</script>');} # Auto open search box if search variables defined?>
<!-- Search Panel -->
            <!-- Autoresponder List Start -->
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th width="20%"><?php echo(lethe_subject);?> <a href="?<?php echo('pos='. $pos .'&amp;ppos='. $ppos .'');?>&amp;qrOrd=byTitle&amp;qrOrdPos=0<?php echo($pgQuery);?>"><span class="glyphicon glyphicon-chevron-down"></span></a><a href="?<?php echo('pos='. $pos .'&amp;ppos='. $ppos .'');?>&amp;qrOrd=byTitle&amp;qrOrdPos=1<?php echo($pgQuery);?>"><span class="glyphicon glyphicon-chevron-up"></span></a></th>
                    <th width="4%" style="text-align:center;"><?php echo(lethe_action);?></th>
                    <th width="4%" style="text-align:center;"><?php echo(lethe_bounces);?></th>
					<th width="4%" style="text-align:center;"><?php echo(lethe_unsubscribers);?></th>
                    <th width="4%" style="text-align:center;"><?php echo(lethe_views);?></th>
					<th width="4%" style="text-align:center;"><?php echo(lethe_clicks);?></th>
                  </tr>
                </thead>
                <tbody>
                <?php 
$limit = 10;
$pgGo = @$_GET["pgGo"];
if(empty($pgGo) or !is_numeric($pgGo)) {$pgGo = 1;}

 $count		 = mysqli_num_rows($myconn->query("SELECT * FROM ". db_table_pref ."newsletters WHERE data_mode=1 ". $src .""));
 $total_page	 = ceil($count / $limit);
 $dtStart	 = ($pgGo-1)*$limit;
				
				$opNews = $myconn->query("SELECT * FROM ". db_table_pref ."newsletters WHERE data_mode=1  ". $src ." ". $qrOrder ." LIMIT $dtStart,$limit") or die(mysqli_error());
				
				//$cntNewsletterStat = $myconn->prepare("SELECT NWS.NID AS NWSID,
				//												(SELECT COUNT(ID) FROM ". db_table_pref ."newsletter_tasks WHERE NID=NWS.NID) AS total_entry,
				//												(SELECT COUNT(ID) FROM ". db_table_pref ."newsletter_tasks WHERE NID=NWS.NID AND sent=1) AS sent_entry,
				//												(SELECT COUNT(ID) FROM ". db_table_pref ."newsletter_tasks WHERE NID=NWS.NID AND sent=0 AND unsubscribed=0) AS unsent_entry,
				//												(SELECT COUNT(ID) FROM ". db_table_pref ."newsletter_tasks WHERE NID=NWS.NID AND unsubscribed=1) AS unsubscribed_entry
				//									FROM
				//											". db_table_pref ."newsletter_tasks AS NWS
				//									WHERE
				//											NWS.NID=?
				//									");				
				
				while($opNewsRs = $opNews->fetch_assoc()){
					$cntBounces = $opNewsRs['bounces'];
				?>
                  <tr>
                    <td>
                    	<p><a href="<?php echo($_SERVER['SCRIPT_NAME'].'?pos='. $pos .'&amp;ppos=2');?>&amp;ID=<?php echo($opNewsRs['ID']);?>"><?php echo($opNewsRs['subject']);?></a></p>
                        <?php if($opNewsRs['ar_mode']!=2){
						if($opNewsRs['ar_mode']==3){$act_time=lethe_before;}else{$act_time=lethe_after;}
						?>
                            <p><?php echo('<strong>'.lethe_time.':</strong> '.$act_time.' '.$opNewsRs['ar_mode_time'].' '.$lethe_ar_dates[$opNewsRs['ar_mode_date']]);?></p>
                        <?php 
						$cnsUnsubscribers = cntData("SELECT ID,unsubscribed FROM ". db_table_pref ."newsletter_tasks WHERE unsubscribed=1 AND NID=". $opNewsRs['ID'] ."");
						}else{ # Specific Dates
									//$cntNewsletterStat->bind_param('i',$opNewsRs['ID']);
									//$cntNewsletterStat->execute();
									//$NWSID=null;$total_entry=0;$sent_entry=0;$unsent_entry=0;$unsubscribed_entry=0;
									//$NWSID=null;$total_entry=null;$sent_entry=null;$unsent_entry=null;$unsubscribed_entry=null;
									//$cntNewsletterStat->bind_result($NWSID,$total_entry,$sent_entry,$unsent_entry,$unsubscribed_entry);
									//$cntNewsletterStat->fetch();
									$cntNewsletter = cntdata("SELECT ID FROM ". db_table_pref ."newsletter_tasks WHERE NID=". $opNewsRs['ID'] ."");
									$cntSent = cntData("SELECT ID,sent FROM ". db_table_pref ."newsletter_tasks WHERE sent=1 AND NID=". $opNewsRs['ID'] ."");
									$cntUSent = cntData("SELECT ID,sent,unsubscribed FROM ". db_table_pref ."newsletter_tasks WHERE sent=0 AND unsubscribed=0 AND NID=". $opNewsRs['ID'] ."");
									$cnsUnsubscribers = cntData("SELECT ID,unsubscribed FROM ". db_table_pref ."newsletter_tasks WHERE unsubscribed=1 AND NID=". $opNewsRs['ID'] ."");
									$cntBounces = $opNewsRs['bounces'];
						?>
                            <p><strong><?php echo(lethe_start);?>:</strong> <?php echo(date('d.m.Y h:i A',strtotime($opNewsRs['launch_date'])));?></p>
                            <p><strong><?php echo(lethe_finish);?>:</strong> <?php echo(date('d.m.Y h:i A',strtotime($opNewsRs['finish_date'])));?></p>
							<p><strong><?php echo(lethe_campaign_end);?>:</strong><?php if($opNewsRs['end_camp']==1){echo(' <span class="glyphicon glyphicon-check text-success"></span>');}else{echo(' <span class="glyphicon glyphicon-remove text-danger"></span>');}?></p>
                            <p style="font-size:14px;"><strong><?php echo(lethe_total);?>:</strong> <span class="label label-danger"><?php echo($cntNewsletter);?></span> <strong><?php echo(lethe_daily_sent);?>:</strong> <?php echo('<span class="label label-success" data-toggle="tooltip" data-placement="top" title="'. lethe_sent .'">'.$cntSent.'</span><span class="label label-default" data-toggle="tooltip" data-placement="top" title="'. lethe_unsent .'">'.$cntUSent.'</span>');?> <strong><?php echo(lethe_position);?>:</strong> <?php echo(getMyActPos($opNewsRs['position']));?></p>
                            <p><strong><?php echo(lethe_weeks);?>:</strong> <?php for($i=0;$i<count($lethe_weekList['short']);$i++){if($opNewsRs['ar_week_'.$i]==1){echo('<span class="label label-success">' . $lethe_weekList['short'][$i].'</span> ');}else{echo('<span class="label label-default">' . $lethe_weekList['short'][$i].'</span> ');}}?></p>
						<?php 
                        $totalLoaded = $cntNewsletter;
                        $loadedQueue = $cntUSent;
                        $res = @( ( $totalLoaded - $loadedQueue ) / $totalLoaded ) * 100;
                        $res = round($res,1);
                        ?>  
						<div style="width:200px;">
                        <div class="progress active<?php if($res!=100 && $opNewsRs['position']==2){echo(' progress-striped');}?>" style="background-color:#999;">
                          <div class="progress-bar<?php echo(getMyActProg($opNewsRs['position']));?>" role="progressbar" aria-valuenow="<?php echo($res);?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo($res);?>%;">
                            <?php echo($res);?>%
                          </div>
                        </div>
						</div>
                        	
                        <?php }?>
                    </td>
                    <td align="center"><?php echo($lethe_ar_models[$opNewsRs['ar_mode']]);?></td>
                    <td align="center"><?php echo($opNewsRs['bounces']);?></td>
					<td align="center"><?php echo($cnsUnsubscribers);?></td>
                    <td align="center"><?php echo($opNewsRs['view_hit']);?></td>
					<td align="center"><?php echo($opNewsRs['click_hit']);?></td>
                  </tr>
               <?php }
			   $opNews->free();
			   ?>
				<?php if($total_page>1){?>
                  <tr class="non-striped">
                    <td colspan="6">&nbsp;</td>
                  </tr>
                  <tr class="non-striped">
                    <td colspan="6"><?php $pgVar='?pos='. $pos .'&ppos='. $ppos .''.$pgQuery.$dtOrder;include("inc/inc_pagination.php");?></td>
                  </tr>
                <?php }?>
                </tbody>
                </table>
            <!-- Autoresponder List End -->
                        
            </td>
          </tr>
        <?php }elseif($ppos==1){ # Autoresponder Create?>
          <tr>
            <td>
            
            	<h1 class="panel-header"><?php echo(lethe_create_autoresponder);?></h1>
                <?php echo($errText);?>
            	<!-- Add Autoresponder Start -->
                <form role="form" name="add_autoresponderForm" id="add_autoresponderForm" action="" method="post" enctype="multipart/form-data">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="23%" height="40"><strong><?php echo(lethe_submission_account);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="76%" height="40"><select name="sub_account" id="sub_account" class="form-control autoWidth inlineBlock" required>
                    <option value="0"><?php echo(lethe_choose);?></option>
                    <?php 
					if(admin_mode==2){$permSel = " AND permission=2";}else{$permSel = "";}
					$opAccs = $myconn->query("SELECT ID,account_title,sender_mail,active,permission FROM ". db_table_pref ."newsletter_accounts WHERE active=1 ". $permSel ."") or die(mysqli_error());
					while($opAccsRs = $opAccs->fetch_assoc()){
						echo('<option value="'. $opAccsRs['ID'] .'">'. $opAccsRs['account_title'] .' ('. $opAccsRs['sender_mail'] .')</option>');
						}
					$opAccs->free();
					?>
                    </select> <button type="button" class="btn btn-warning btn-sm" id="account-info" disabled><span class="glyphicon glyphicon glyphicon-info-sign"></span> <?php echo(lethe_info);?></button>
                    <div id="sub-acc-info" class="alert alert-info" style="display:none;"></div>
                    </td>
                  </tr>
                  <tr>
                    <td width="23%" height="40"><strong><?php echo(lethe_subject);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="76%" height="40"><input value="<?php echo(mysql_prep(@$_POST['subject']));?>" name="subject" type="text" id="subject" class="form-control autoWidth" size="75" required></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_group);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><select name="sub_group[]" id="sub_group" class="form-control autoWidth" size="10" style="width:500px;" multiple required>
                    <?php $opGroup = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE active=1 ORDER BY group_name ASC") or die(mysqli_error());
						while($opGroupRs = $opGroup->fetch_assoc()){
							echo('<option value="'. $opGroupRs['ID'] .'">('. cntData("SELECT ID FROM ". db_table_pref ."newsletter_subscribers WHERE GID=". $opGroupRs['ID'] ."") .') '. $opGroupRs['group_name'] .'</option>');
							}
						$opGroup->free();
					?>
                    </select></td>
                  </tr>
                  <tr>
                    <td height="40" valign="top"><strong><?php echo(lethe_action);?></strong></td>
                    <td height="40" valign="top"><strong>:</strong></td>
                    <td height="40">
                    <div id="ar-actions" style="margin:10px;">
                    <select name="ar_action" id="ar_action" class="form-control autoWidth" required>
                    	<?php foreach($lethe_ar_models as $key=>$value){echo('<option value="'. $key .'">'. $value .'</option>');}?>
                    </select><br>
					
						<!-- After Subscribe -->
                    	<div id="ar-actions-0" class="alert alert-warning">
                        	<input type="text" size="5" value="1" name="ar_0_time" id="ar_0_time" class="form-control autoWidth inlineBlock"> 
                            <select name="ar_0_date" id="ar_0_date" class="form-control autoWidth inlineBlock">
                            	<?php for($i=1;$i<count($lethe_ar_dates);$i++){echo('<option value="'. $i .'">'. $lethe_ar_dates[$i] .'</option>');}?>
                            </select> <span><?php echo(lethe_later);?></span>
                        </div>
						
						<!-- After Unsubscribe -->
                        <div id="ar-actions-1" class="alert alert-warning" style="display:none;">
                        	<input type="text" size="5" value="1" name="ar_1_time" id="ar_1_time" class="form-control autoWidth inlineBlock"> 
                            <select name="ar_1_date" id="ar_1_date" class="form-control autoWidth inlineBlock">
                            <?php for($i=1;$i<count($lethe_ar_dates);$i++){echo('<option value="'. $i .'">'. $lethe_ar_dates[$i] .'</option>');}?>
                            </select> <span><?php echo(lethe_later);?></span>
                        </div>
						
						<!-- Specific Dates -->
                        <div id="ar-actions-2" class="alert alert-warning" style="display:none">
                        
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td height="45"><strong><?php echo(lethe_start);?></strong></td>
                            <td><strong>:</strong></td>
                            <td>
                            
                            <input value="<?php echo(mysql_prep(@$_POST['launch_date']));?>" name="launch_date" type="text" id="launch_date" class="form-control autoWidth inlineBlock" size="15" placeholder="<?php echo('dd-mm-yyyy');?>">
                            <select name="launch_date_h" class="form-control autoWidth inlineBlock">
                                <?php
                                    for($i=0;$i<=23;$i++){
                                        echo('<option value="'. addZero($i) .'"'. formSelector(@$_POST['launch_date_h'],addZero($i),0) .'>'. addZero($i) .'</option>');
                                        }
                                ?>
                            </select> : 
                            <select name="launch_date_m" class="form-control autoWidth inlineBlock">
                                <?php
                                    for($i=0;$i<=59;$i++){
                                        echo('<option value="'. addZero($i) .'"'. formSelector(@$_POST['launch_date_m'],addZero($i),0) .'>'. addZero($i) .'</option>');
                                        }
                                ?>
                            </select>
                            <script>$(document).ready(function(){$('#launch_date').datepick({dateFormat: 'dd-mm-yyyy'});});</script>
                            
                            </td>
                          </tr>
                          <tr>
                            <td height="45"><strong><?php echo(lethe_end);?></strong></td>
                            <td><strong>:</strong></td>
                            <td>
                            
                            <input value="<?php echo(mysql_prep(@$_POST['finish_date']));?>" name="finish_date" type="text" id="finish_date" class="form-control autoWidth inlineBlock" size="15" placeholder="<?php echo('dd-mm-yyyy');?>">
                            <select name="finish_date_h" class="form-control autoWidth inlineBlock">
                                <?php
                                    for($i=0;$i<=23;$i++){
                                        echo('<option value="'. addZero($i) .'"'. formSelector(@$_POST['finish_date_h'],addZero($i),0) .'>'. addZero($i) .'</option>');
                                        }
                                ?>
                            </select> : 
                            <select name="finish_date_m" class="form-control autoWidth inlineBlock">
                                <?php
                                    for($i=0;$i<=59;$i++){
                                        echo('<option value="'. addZero($i) .'"'. formSelector(@$_POST['finish_date_m'],addZero($i),0) .'>'. addZero($i) .'</option>');
                                        }
                                ?>
                            </select> <input type="checkbox" name="end_campaign" id="end_campaign" value="YES"<?php echo(formSelector(@$_POST['end_campaign'],'YES',1));?>> <?php echo(lethe_end_campaign);?>
                            <script>$(document).ready(function(){$('#finish_date').datepick({dateFormat: 'dd-mm-yyyy'});});</script>
                            
                            </td>
                          </tr>
                          <tr>
                            <td height="45"><strong><?php echo(lethe_next_launch_date);?></strong></td>
                            <td><strong>:</strong></td>
                            <td>
                        	<input type="text" size="5" value="1" name="ar_2_time" id="ar_2_time" class="form-control autoWidth inlineBlock"> 
                            <select name="ar_2_date" id="ar_2_date" class="form-control autoWidth inlineBlock">
                            <?php for($i=1;$i<count($lethe_ar_dates);$i++){echo('<option value="'. $i .'">'. $lethe_ar_dates[$i] .'</option>');}?>
                            </select> <span><?php echo(lethe_later);?></span>
                            </td>
                          </tr>
                          <tr>
                            <td height="45"><strong><?php echo(lethe_weekdays);?></strong></td>
                            <td><strong>:</strong></td>
                            <td>
                            <?php for($i=0;$i<=6;$i++){
								echo('<input type="checkbox" name="ar_weeks_'. $i .'" id="ar_weeks_'. $i .'" value="YES"'. formSelector(@$_POST['ar_weeks_'. $i ],'YES',1) .'> <label for="ar_weeks_'. $i .'">' . $lethe_weekList['short'][$i].'</label> ');
								}?>
                            </td>
                          </tr>
                        </table>

                            
                        </div>
						
						<!-- Special Day -->
                        <div id="ar-actions-3" class="alert alert-warning" style="display:none;">
							<p><?php echo(lethe_action_uses_subscriber_date_field);?></p>
							<span><?php echo(lethe_before);?></span> <input type="text" size="5" value="1" name="ar_3_time" id="ar_3_time" class="form-control autoWidth inlineBlock"> 
                            <select name="ar_3_date" id="ar_3_date" class="form-control autoWidth inlineBlock">
                            <?php for($i=1;$i<count($lethe_ar_dates);$i++){echo('<option value="'. $i .'">'. $lethe_ar_dates[$i] .'</option>');}?>
                            </select>
                        </div>
						
                    </div>
                    <script>
                	$("#ar_action").change(function(){
						if($(this).val()!="null"){
							for(i=0;i<4;i++){
								$('#ar-actions-'+i).hide();
								}
							$('#ar-actions-'+$(this).val()).show();
							}
						});
                    </script>
                    </td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_high_importance);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-importance" data-off="danger" data-on-label="<?php echo(lethe_yes);?>" data-off-label="<?php echo(lethe_no);?>"><input name="importance" type="checkbox" id="importance" value="YES"></div></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_template);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40">
                    	<select name="temp_list" id="temp_list" class="form-control autoWidth">
                        	<option value="null"><?php echo(lethe_choose);?></option>
                            <?php $opTemps = $myconn->query("SELECT ID,title FROM ". db_table_pref ."newsletter_templates ORDER BY title ASC") or die(mysqli_error());
							while($opTempsRs = $opTemps->fetch_assoc()){
								echo('<option value="'. $opTempsRs['ID'] .'">'. $opTempsRs['title'] .'</option>');
								}
							$opTemps->free();
							?>
                        </select>
                    </td>
                  </tr>
                      <tr>
                        <td width="22%" height="35"><strong><?php echo(lethe_short_codes);?></strong></td>
                        <td width="1%" height="35"><strong>:</strong></td>
                        <td width="77%" height="35">
                        	<div class="well" id="short_code_list">
                            	<?php
                                	for($i=0;$i<count($lethe_short_codes);$i++){ # List of static codes
										echo('<div class="label label-danger" data-lethe-codes="{'. $lethe_short_codes[$i] .'}" data-lethe-code-field="details">{'. $lethe_short_codes[$i] .'}</div> ');
										}
									$opCodes = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_codes ORDER BY lethe_code ASC") or die(mysqli_error());
									while($opCodesRs = $opCodes->fetch_assoc()){
										echo('<div class="label label-info" data-lethe-codes="{'. $opCodesRs['lethe_code'] .'}" data-lethe-code-field="details">{'. $opCodesRs['lethe_code'] .'}</div> ');
										}
									$opCodes->free();
								?>
                            </div>
                        </td>
                      </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_details);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><textarea name="details" id="details" class="mceEditor"></textarea></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_file);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="input-group">
              <input name="attach_file" class="form-control" type="text" id="attach_file" size="40">
			  <?php 
			  if(minipan_on==1){
				echo('
              <span class="input-group-btn">
                <button data-pan-model="fancybox" data-pan-field="attach_file" data-pan-link="default" data-pan-platform="normal" class="btn btn-default minipan" type="button">miniPan</button>
              </span>
				');
			  }
			  ?>
              </div></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_web_option);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-onweb" data-off="danger" data-on-label="<?php echo(lethe_on);?>" data-off-label="<?php echo(lethe_off);?>"><input name="onweb" type="checkbox" id="onweb" value="YES" checked></div></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><button type="button" name="prev" id="prev" class="fancybox2 btn btn-warning"><?php echo(lethe_preview);?></button> <button type="submit" name="addAutoresponder" value="addAutoresponder" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> <?php echo(lethe_add_newsletter);?></button> <button type="button" id="test-sender" data-loading-text="<?php echo(lethe_sending);?>..." class="btn btn-success"><span class="glyphicon glyphicon-send"></span> <?php echo(lethe_send_test);?></button><div id="test-result" class="inlineBlock pull-right"></div></td>
                  </tr>
                </table>
                </form>
                <script>
					// Template Loader
                	$("#temp_list").change(function(){
						if($(this).val()!="null"){
							
							var editor = tinymce.get('details');
							var content = editor.getContent();

								$.ajax({
								  url: '<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=3&ID=')?>'+$(this).val(),
								  cache: false,
								  success: function(html){
									editor.setContent(html);
								  }
								});
							}
						});
						
					// Submission Account Info Loader
                	$("#sub_account").change(function(){
						if($(this).val()!="0"){
							$("#account-info").prop('disabled',false);
							getAjax('#sub-acc-info','<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=4&ID=')?>'+$(this).val(),'<?php echo(lethe_loading);?>');					
							}else{
								$("#account-info").prop('disabled',true);
								$("#sub-acc-info").hide();
								}
						});
					
					$("#account-info").click(function() {
					$("#sub-acc-info").toggle( "fast", function() {});
					});
					
					// Send Test
					$("#test-sender").click(function() {
						var btn = $(this);
						btn.button('loading');
						tinyMCE.triggerSave();
						var serializedData = $("#add_autoresponderForm").serialize();
						
						$.ajax({
								url: '<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=5&ID=')?>'+$("#sub_account").val(),
								type: 'POST',
								data: serializedData ,
								success: function (response) {
									btn.button('reset');
									$('#test-result').html(response);
								},
								error: function () {

								}
							}); 
						});
                </script>
                <!-- Add Autoresponder End -->
            
            </td>
          </tr>
        <?php }elseif($ppos==2){ # Autoresponder Edit?>
          <tr>
            <td>
            	<!-- Edit Autoresponder Start -->
                <h1 class="panel-header"><?php echo(lethe_edit_autoresponder);?></h1>
                <?php echo($errText);?>
                <?php $opNews = $myconn->query("SELECT * FROM ". db_table_pref ."newsletters WHERE ID=". $ID ." AND data_mode=1") or die(mysqli_error());
				if(mysqli_num_rows($opNews)==0){echo('<div class="alert alert-info">' .lethe_record_not_found.'</div>');}else{
					$opNewsRs = $opNews->fetch_assoc();
				?>
                <form role="form" name="edit_autoresponderForm" id="edit_autoresponderForm" action="" method="post" enctype="multipart/form-data">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="23%" height="40"><strong><?php echo(lethe_submission_account);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="76%" height="40"><select name="sub_account" id="sub_account" class="form-control autoWidth inlineBlock">
                    <option value="0"><?php echo(lethe_choose);?></option>
                    <?php 
					if(admin_mode==2){$permSel = " AND permission=2";}else{$permSel = "";}
					$opAccs = $myconn->query("SELECT ID,account_title,sender_mail,active,permission FROM ". db_table_pref ."newsletter_accounts WHERE active=1 ". $permSel ."") or die(mysqli_error());
					while($opAccsRs = $opAccs->fetch_assoc()){
						echo('<option value="'. $opAccsRs['ID'] .'"'. formSelector($opNewsRs['SUID'],$opAccsRs['ID'],0) .'>'. $opAccsRs['account_title'] .' ('. $opAccsRs['sender_mail'] .')</option>');
						}
					$opAccs->free();
					?>
                    </select> <button type="button" class="btn btn-warning btn-sm" id="account-info" disabled><span class="glyphicon glyphicon glyphicon-info-sign"></span> <?php echo(lethe_info);?></button>
                    <div id="sub-acc-info" class="alert alert-info" style="display:none;"></div>
                    </td>
                  </tr>
                  <tr>
                    <td width="23%" height="40"><strong><?php echo(lethe_subject);?></strong></td>
                    <td width="1%" height="40"><strong>:</strong></td>
                    <td width="76%" height="40"><input value="<?php echo($opNewsRs['subject']);?>" name="subject" type="text" id="subject" class="form-control autoWidth" size="75"></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_process);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40">
                    	<select name="newsletter_proc" id="newsletter_proc" class="form-control autoWidth">
                            <?php for($i=0;$i<count($lethe_status_mode);$i++){
								echo('<option value="'. $i .'"'. formSelector($i,$opNewsRs['position'],0) .'>'. $lethe_status_mode[$i] .'</option>');
								}
							?>
                        </select>
                    </td>
                  </tr>
                  <tr>
                    <td height="40" valign="top"><strong><?php echo(lethe_subscriber_groups);?></strong></td>
                    <td height="40" valign="top"><strong>:</strong></td>
                    <td height="40">                        
                        <!-- Groups -->
                        <table width="600" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="45%">
                            	<?php
								$group_list =  $opNewsRs['groups'];
								$unselected = " AND (ID<>". str_replace(',',' AND ID<>',$group_list) .")";
								$selected = " AND (ID=". str_replace(',',' OR ID=',$group_list) .")";;
								?>
                            	<h4 class="text-danger"><?php echo(lethe_groups);?></h4>
                                <select onBlur="this.select()" name="sub_group2[]" id="sub_group2" class="form-control autoWidth" size="10" style="width:100%;" multiple>
                                <?php $opGroup = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE active=1 ". $unselected ." ORDER BY group_name ASC") or die(mysqli_error());
                                    while($opGroupRs = $opGroup->fetch_assoc()){
                                        echo('<option value="'. $opGroupRs['ID'] .'" selected>('. cntData("SELECT ID FROM ". db_table_pref ."newsletter_subscribers WHERE GID=". $opGroupRs['ID'] ."") .') '. $opGroupRs['group_name'] .'</option>');
                                        }
									$opGroup->free();
                                ?>
                                </select>
                            </td>
                            <td width="10%" align="center">
                            	<button type="button" onClick="listbox_moveacross('sub_group2', 'sub_group');" name="selectMov1" class="btn btn-default"><span class="glyphicon glyphicon-chevron-right"></span></button>
                                <button type="button" onClick="listbox_moveacross('sub_group', 'sub_group2');" name="selectMov2" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span></button>
                            </td>
                            <td width="45%">
                            	<h4 class="text-success"><?php echo(lethe_s_elected_groups);?></h4>
                                <select onBlur="this.select()" name="sub_group[]" id="sub_group" class="form-control autoWidth" size="10" style="width:100%;" multiple>
                                <?php $opGroup = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE active=1 ". $selected ." ORDER BY group_name ASC") or die(mysqli_error());
                                    while($opGroupRs = $opGroup->fetch_assoc()){
                                        echo('<option value="'. $opGroupRs['ID'] .'" selected>('. cntData("SELECT ID FROM ". db_table_pref ."newsletter_subscribers WHERE GID=". $opGroupRs['ID'] ."") .') '. $opGroupRs['group_name'] .'</option>');
                                        }
									$opGroup->free();
                                ?>
                                </select>
                            </td>
                          </tr>
                        </table>
                        <!-- Groups -->
                        
                    </td>
                  </tr>
                  <tr>
                    <td height="40" valign="top"><strong><?php echo(lethe_action);?></strong></td>
                    <td height="40" valign="top"><strong>:</strong></td>
                    <td height="40">
                    <div id="ar-actions" style="margin:10px;">
                    <select name="ar_action" id="ar_action" class="form-control autoWidth">
                    	<?php foreach($lethe_ar_models as $key=>$value){echo('<option value="'. $key .'"'. formSelector($opNewsRs['ar_mode'],$key,0) .'>'. $value .'</option>');}?>
                    </select><br>
                    	<div id="ar-actions-0" class="alert alert-warning" style="display:none;">
                        	<input type="text" size="5" value="<?php echo($opNewsRs['ar_mode_time']);?>" name="ar_0_time" id="ar_0_time" class="form-control autoWidth inlineBlock"> 
                            <select name="ar_0_date" id="ar_0_date" class="form-control autoWidth inlineBlock">
                            	<?php for($i=1;$i<count($lethe_ar_dates);$i++){echo('<option value="'. $i .'"'. formSelector($opNewsRs['ar_mode_date'],$i,0) .'>'. $lethe_ar_dates[$i] .'</option>');}?>
                            </select> <span><?php echo(lethe_later);?></span>
                        </div>
                        <div id="ar-actions-1" class="alert alert-warning" style="display:none;">
                        	<input type="text" size="5" value="<?php echo($opNewsRs['ar_mode_time']);?>" name="ar_1_time" id="ar_1_time" class="form-control autoWidth inlineBlock"> 
                            <select name="ar_1_date" id="ar_1_date" class="form-control autoWidth inlineBlock">
                            <?php for($i=1;$i<count($lethe_ar_dates);$i++){echo('<option value="'. $i .'"'. formSelector($opNewsRs['ar_mode_date'],$i,0) .'>'. $lethe_ar_dates[$i] .'</option>');}?>
                            </select> <span><?php echo(lethe_later);?></span>
                        </div>
                        <div id="ar-actions-2" class="alert alert-warning" style="display:none">
                        
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td height="45"><strong><?php echo(lethe_start);?></strong></td>
                            <td><strong>:</strong></td>
                            <td>
                            
                            <input value="<?php echo(date('d-m-Y',strtotime($opNewsRs['launch_date'])));?>" name="launch_date" type="text" id="launch_date" class="form-control autoWidth inlineBlock" size="15" placeholder="<?php echo('dd-mm-yyyy');?>">
                            <select name="launch_date_h" class="form-control autoWidth inlineBlock">
                                <?php
                                    for($i=0;$i<=23;$i++){
                                        echo('<option value="'. addZero($i) .'"'. formSelector(date('H',strtotime($opNewsRs['launch_date'])),addZero($i),0) .'>'. addZero($i) .'</option>');
                                        }
                                ?>
                            </select> : 
                            <select name="launch_date_m" class="form-control autoWidth inlineBlock">
                                <?php
                                    for($i=0;$i<=59;$i++){
                                        echo('<option value="'. addZero($i) .'"'. formSelector(date('i',strtotime($opNewsRs['launch_date'])),addZero($i),0) .'>'. addZero($i) .'</option>');
                                        }
                                ?>
                            </select>
                            <script>$(document).ready(function(){$('#launch_date').datepick({dateFormat: 'dd-mm-yyyy'});});</script>
                            
                            </td>
                          </tr>
                          <tr>
                            <td height="45"><strong><?php echo(lethe_end);?></strong></td>
                            <td><strong>:</strong></td>
                            <td>
                            
                            <input value="<?php echo(date('d-m-Y',strtotime($opNewsRs['finish_date'])));?>" name="finish_date" type="text" id="finish_date" class="form-control autoWidth inlineBlock" size="15" placeholder="<?php echo('dd-mm-yyyy');?>">
                            <select name="finish_date_h" class="form-control autoWidth inlineBlock">
                                <?php
                                    for($i=0;$i<=23;$i++){
                                        echo('<option value="'. addZero($i) .'"'. formSelector(date('H',strtotime($opNewsRs['finish_date'])),addZero($i),0) .'>'. addZero($i) .'</option>');
                                        }
                                ?>
                            </select> : 
                            <select name="finish_date_m" class="form-control autoWidth inlineBlock">
                                <?php
                                    for($i=0;$i<=59;$i++){
                                        echo('<option value="'. addZero($i) .'"'. formSelector(date('i',strtotime($opNewsRs['finish_date'])),addZero($i),0) .'>'. addZero($i) .'</option>');
                                        }
                                ?>
                            </select> <input type="checkbox" name="end_campaign" id="end_campaign" value="YES"<?php echo(formSelector($opNewsRs['end_camp'],1,1));?>> <?php echo(lethe_end_campaign);?>
                            <script>$(document).ready(function(){$('#finish_date').datepick({dateFormat: 'dd-mm-yyyy'});});</script>
                            
                            </td>
                          </tr>
                          <tr>
                            <td height="45"><strong><?php echo(lethe_next_launch_date);?></strong></td>
                            <td><strong>:</strong></td>
                            <td>
                        	<input type="text" size="5" value="<?php echo($opNewsRs['ar_mode_time']);?>" name="ar_2_time" id="ar_2_time" class="form-control autoWidth inlineBlock"> 
                            <select name="ar_2_date" id="ar_2_date" class="form-control autoWidth inlineBlock">
                            <?php for($i=1;$i<count($lethe_ar_dates);$i++){echo('<option value="'. $i .'"'. formSelector($opNewsRs['ar_mode_date'],$i,0) .'>'. $lethe_ar_dates[$i] .'</option>');}?>
                            </select> <span><?php echo(lethe_later);?></span>
                            </td>
                          </tr>
                          <tr>
                            <td height="45"><strong><?php echo(lethe_weekdays);?></strong></td>
                            <td><strong>:</strong></td>
                            <td>
                            <?php for($i=0;$i<=6;$i++){
								echo('<input type="checkbox" name="ar_weeks_'. $i .'" id="ar_weeks_'. $i .'" value="YES"'. formSelector($opNewsRs['ar_week_'.$i],1,1) .'> <label for="ar_weeks_'. $i .'">' . $lethe_weekList['short'][$i].'</label> ');
								}?>
                            </td>
                          </tr>
                        </table>

                            
                        </div>
						
						<!-- Special Day -->
                        <div id="ar-actions-3" class="alert alert-warning" style="display:none;">
							<p><?php echo(lethe_action_uses_subscriber_date_field);?></p>
							<span><?php echo(lethe_before);?></span> <input type="text" size="5" value="<?php echo($opNewsRs['ar_mode_time']);?>" name="ar_3_time" id="ar_3_time" class="form-control autoWidth inlineBlock"> 
                            <select name="ar_3_date" id="ar_3_date" class="form-control autoWidth inlineBlock">
                            <?php for($i=1;$i<count($lethe_ar_dates);$i++){echo('<option value="'. $i .'"'. formSelector($opNewsRs['ar_mode_date'],$i,0) .'>'. $lethe_ar_dates[$i] .'</option>');}?>
                            </select>
                        </div>
						
                    </div>
                    <script>
                	$("#ar_action").change(function(){
						if($(this).val()!="null"){
							for(i=0;i<4;i++){
								$('#ar-actions-'+i).hide();
								}
							$('#ar-actions-'+$(this).val()).show();
							}
						});
					// Load Mode Settings
					$(document).ready(function(){
						$('#ar-actions-<?php echo($opNewsRs['ar_mode']);?>').show();
						});
                    </script>
                    </td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_high_importance);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-importance" data-off="danger" data-on-label="<?php echo(lethe_yes);?>" data-off-label="<?php echo(lethe_no);?>"><input name="importance" type="checkbox" id="importance" value="YES"<?php echo(formSelector($opNewsRs['priotity'],1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_template);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40">
                    	<select name="temp_list" id="temp_list" class="form-control autoWidth">
                        	<option value="null"><?php echo(lethe_choose);?></option>
                            <?php $opTemps = $myconn->query("SELECT ID,title FROM ". db_table_pref ."newsletter_templates ORDER BY title ASC") or die(mysqli_error());
							while($opTempsRs = $opTemps->fetch_assoc()){
								echo('<option value="'. $opTempsRs['ID'] .'">'. $opTempsRs['title'] .'</option>');
								}
							$opTemps->free();
							?>
                        </select>
                    </td>
                  </tr>
                      <tr>
                        <td width="22%" height="35"><strong><?php echo(lethe_short_codes);?></strong></td>
                        <td width="1%" height="35"><strong>:</strong></td>
                        <td width="77%" height="35">
                        	<div class="well" id="short_code_list">
                            	<?php
                                	for($i=0;$i<count($lethe_short_codes);$i++){ # List of static codes
										echo('<div class="label label-danger" data-lethe-codes="{'. $lethe_short_codes[$i] .'}" data-lethe-code-field="details">{'. $lethe_short_codes[$i] .'}</div> ');
										}
									$opCodes = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_codes ORDER BY lethe_code ASC") or die(mysqli_error());
									while($opCodesRs = $opCodes->fetch_assoc()){
										echo('<div class="label label-info" data-lethe-codes="{'. $opCodesRs['lethe_code'] .'}" data-lethe-code-field="details">{'. $opCodesRs['lethe_code'] .'}</div> ');
										}
									$opCodes->free();
								?>
                            </div>
                        </td>
                      </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_details);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><textarea name="details" id="details" class="mceEditor"><?php echo($opNewsRs['details']);?></textarea></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_file);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="input-group">
              <input placeholder="http://" name="attach_file" value="<?php echo($opNewsRs['file_url']);?>" class="form-control" type="text" id="attach_file" size="40">
			  <?php 
			  if(minipan_on==1){
				echo('
              <span class="input-group-btn">
                <button data-pan-model="fancybox" data-pan-field="attach_file" data-pan-link="default" data-pan-platform="normal" class="btn btn-default minipan" type="button">miniPan</button>
              </span>
				');
			  }
			  ?>
              </div></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_web_option);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><div class="make-switch" data-on="success" id="switch-onweb" data-off="danger" data-on-label="<?php echo(lethe_on);?>" data-off-label="<?php echo(lethe_off);?>"><input name="onweb" type="checkbox" id="onweb" value="YES"<?php echo(formSelector($opNewsRs['web_view'],1,1));?>></div></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_reset);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input type="checkbox" value="YES" name="res" id="resNewsletter" onclick="if(this.checked==true){return confirm('<?php echo(lethe_all_tasks_will_marked_unsent);?>');}"> <label for="resNewsletter"><?php echo(lethe_yes);?></label></td>
                  </tr>
                  <tr>
                    <td height="40"><strong><?php echo(lethe_d_elete);?></strong></td>
                    <td height="40"><strong>:</strong></td>
                    <td height="40"><input type="checkbox" value="YES" name="del" id="delNewsletter" onclick="if(this.checked==true){return confirm('<?php echo(lethe_are_you_sure_to_d_elete);?>');}"> <label for="delNewsletter"><?php echo(lethe_yes);?></label></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><button type="button" name="prev" id="prev" class="fancybox2 btn btn-warning"><?php echo(lethe_preview);?></button> <button type="submit" name="editAutoresponder" value="editAutoresponder" class="btn btn-primary"><span class="glyphicon glyphicon-refresh"></span> <?php echo(lethe_edit_newsletter);?></button> <button type="button" id="test-sender" data-loading-text="<?php echo(lethe_sending);?>..." class="btn btn-success"><span class="glyphicon glyphicon-send"></span> <?php echo(lethe_send_test);?></button><div id="test-result" class="inlineBlock pull-right"></div></td>
                  </tr>
                </table>
                </form>
                <script>
					// Template Loader
                	$("#temp_list").change(function(){
						if($(this).val()!="null"){
							
							var editor = tinymce.get('details');
							var content = editor.getContent();

								$.ajax({
								  url: '<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=3&ID=')?>'+$(this).val(),
								  cache: false,
								  success: function(html){
									editor.setContent(html);
								  }
								});
							}
						});
											
					// Submission Account Info Loader
                	$("#sub_account").change(function(){
						if($(this).val()!="0"){
							$("#account-info").prop('disabled',false);
							getAjax('#sub-acc-info','<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=4&ID=')?>'+$(this).val(),'<?php echo(lethe_loading);?>');					
							}else{
								$("#account-info").prop('disabled',true);
								$("#sub-acc-info").hide();
								}
						});
					
					$("#account-info").click(function() {
					$("#sub-acc-info").toggle( "fast", function() {});
					});
					
					// Send Test
					$("#test-sender").click(function() {
						var btn = $(this);
						btn.button('loading');
						tinyMCE.triggerSave();
						var serializedData = $("#edit_autoresponderForm").serialize();
						
						$.ajax({
								url: '<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=5&ID=')?>'+$("#sub_account").val(),
								type: 'POST',
								data: serializedData ,
								success: function (response) {
									btn.button('reset');
									$('#test-result').html(response);
								},
								error: function () {

								}
							}); 
						});
						
						// Account Loader
						$(document).ready(function(){
							 if($("#sub_account").val()!=0){
								 $("#account-info").prop('disabled',false);
								 getAjax('#sub-acc-info','<?php echo($_SERVER['SCRIPT_NAME'].'?ajax=1&ajax_part=4&ID='.$opNewsRs['SUID'])?>','<?php echo(lethe_loading);?>');
								 }
						});
						
						// Group Selector
						$(document).ready(function(){
							$('#edit_autoresponderForm').submit(function() {
								listbox_selectall('sub_group', true);
								listbox_selectall('sub_group2', true);
								return true; // return false to cancel form action
							});
						});
                </script>
                <?php $opNews->free();}?>
                <!-- Edit Autoresponder End -->
            </td>
          </tr>
        <?php }?>
        </table>
        
    <!-- Autoresponder End -->
    
    </td>
  </tr>
<?php }else{?>
  <tr>
    <td>Error</td>
  </tr>
<?php }?>
</table>
  
  
  </div><!-- Panel Body End -->
</div>
<!-- Content End -->
<?php echo(set_lethe_powered);?>
</div>
</div>

<!-- Page End -->
<script src="bootstrap/dist/js/bootstrap.min.js"></script>
<script src="bootstrap/dist/js/switch.js"></script>
<script src="bootstrap/dist/js/bootstrap-filestyle.min.js"></script>
<script type="text/javascript" src="Scripts/lethe.js"></script>
<script type="text/javascript" src="Scripts/pan.min.js"></script>
</body>
</html>
<?php
$myconn->close();
?>