<?php
# +------------------------------------------------------------------------+
# | Artlantis CMS Solutions                                                |
# +------------------------------------------------------------------------+
# | Lethe Newsletter & Mailing System                                      |
# | Copyright (c) Artlantis Design Studio 2014. All rights reserved.       |
# | Version       1.1                                                      |
# | Last modified 11.03.14                                                 |
# | Email         developer@artlantis.net                                  |
# | Web           http://www.artlantis.net                                 |
# +------------------------------------------------------------------------+

class lethe{

	public $admin_mode = 0;
	public $admin_ID = 0;
	public $admin_area = true;
	public $content_ID = 0;
	public $errPrint = '';
	public $sendActivation = false;
	public $import_file_type = array("txt","csv");
	public $import_file_size = 2097152; # Default 2 MB
	
	public $subAccID = 0;
	public $newsBody = '';
	public $newsSubject = '';
	public $newsAttach = '';
	public $subscriberName = '';
	public $subscriberMail = '';
	public $subscriberDetails = array();
	public $newsletterUnsubscribe = '';
	public $newsletterVerification = '';
	public $newsletterLink = '';
	public $subscribers = array();
	public $sendPriority = 3;
	public $testMode = 0;
	public $sendError = 0;

	# **** Add New User 
	function add_user(){
	
		global $myconn;
		$errorz = '';
		$qr1 = 'user_hash';
		$qr2 = "'". encr(uniqid('lethe', true)) ."'";
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
	
		# ** Username
		if(!isset($_POST['username']) || empty($_POST['username'])){$errorz .= '* '. lethe_please_enter_a_username .'<br>';}else{
			if(!nicknameVal(mysql_prep($_POST['username']))){$errorz .= '* '. lethe_invalid_username .'<br>';}else{
				if(!chkData("SELECT ID,lethe_user FROM ". db_table_pref ."users WHERE lethe_user='". mysql_prep($_POST['username']) ."'")){
					$errorz .= '* '. lethe_username_already_exists .'<br>';}else{
						$qr1 .= ',lethe_user';
						$qr2 .= ",'". mysql_prep($_POST['username']) ."'";
					}
			}
		}
		
		# ** Password
		if(!isset($_POST['pass']) || empty($_POST['pass']) || !isset($_POST['pass2']) || empty($_POST['pass2'])){$errorz .= '* '. lethe_please_enter_password .'<br>';}else{
			if($_POST['pass'] != $_POST['pass2']){$errorz .= '* '. lethe_incorrect_passwords .'<br>';}else{
				if(strlen($_POST['pass'])<5){$errorz .= '* '. lethe_password_is_too_short .'<br>';}else{
					$qr1 .= ',user_pass';
					$qr2 .= ",'". encr($_POST['pass']) ."'";
				}
			}
		}
		
		# ** E-Mail
		if(!isset($_POST['email']) || empty($_POST['email'])){$errorz .= '* '. lethe_please_enter_e_mail_address .'<br>';}else{
			if(!mailVal(mysql_prep($_POST['email']))){$errorz .= '* '. lethe_invalid_e_mail_address .'<br>';}else{
				if(!chkData("SELECT ID,user_mail FROM ". db_table_pref ."users WHERE user_mail='". mysql_prep($_POST['email']) ."'")){
					$errorz .= '* '. lethe_e_mail_address_already_exists .'<br>';}else{
						$qr1 .= ',user_mail';
						$qr2 .= ",'". mysql_prep($_POST['email']) ."'";
					}
			}
		}
		
		# ** Admin Mode
		if(!isset($_POST['amode']) || intval($_POST['amode'])==0){$errorz .= '* '. lethe_please_choose_a_admin_mode .'<br>';}else{
			$qr1 .= ',admin_mode';
			$qr2 .= "," . intval($_POST['amode']);
		}
		
		# ** Daily Limit
		if(!isset($_POST['send_mail_limit']) || empty($_POST['send_mail_limit'])){$errorz .= '* '. lethe_please_enter_a_daily_send_limit .'<br>';}else{
			$qr1 .= ',sendLimit';
			$qr2 .= "," . intval($_POST['send_mail_limit']);
		}
		
		# General
			$qr1 .= ',resetDate,edit_date';
			$qr2 .= ",DATE_ADD(NOW(), INTERVAL 1 DAY),NOW()";
		
		if($errorz==''){
			$myconn->query("INSERT INTO ". db_table_pref ."users (". $qr1 .") VALUES (". $qr2 .")") or die(mysqli_error());
			$this->errPrint = '<div class="alert alert-success">'. lethe_recorded_successfully .'</div>';
		}else{
			$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
	
	} # Add User End **

	# **** Edit User 
	function edit_user(){
	
		global $myconn;
		$errorz = '';
		$qr1 = "edit_date='". date('Y-m-d H:i:s') ."'";
		$qr2 = '';
		$primaryStatus = 0;
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
		
		# ** Delete User
		if(isset($_POST['del'])){
			if($this->admin_area){ # Only Super Admin Can Delete Users // Primary User Cannot be Deleted
				if($this->admin_mode==1){
					$myconn->query("DELETE FROM ". db_table_pref ."users WHERE ID=". $this->content_ID ." AND primary_user<>1") or die(mysqli_error());
					
					# Content Manager
					if(set_after_user_delete==0){ # Remove All User Entries
						$opUserRec = $myconn->query("SELECT ID, UID FROM ". db_table_pref ."newsletters WHERE UID=". $this->content_ID ."") or die(mysqli_error());
						while($opUserRecRs = $opUserRec->fetch_assoc()){
							$myconn->query("DELETE FROM ". db_table_pref ."newsletter_tasks WHERE NID=". $opUserRecRs['ID'] ."") or die(mysqli_error());
						}
						$myconn->query("DELETE FROM ". db_table_pref ."newsletters WHERE UID=". $this->content_ID ."") or die(mysqli_error());
						$opUserRec->free();
					}
					else if(set_after_user_delete==1){ # Link to Super Admin
						$getPrimary = getUser(0,0);
						$myconn->query("UPDATE ". db_table_pref ."newsletters SET UID=". $getPrimary ." WHERE UID=". $this->content_ID ."") or die(mysqli_error());
					}
					
				}
			}
		}
		
		# ** Check User Exists
		$opUserDetails = $myconn->query("SELECT ID,primary_user FROM ". db_table_pref ."users WHERE ID=". $this->content_ID ."") or die(mysqli_error());
		if(mysqli_num_rows($opUserDetails)==0){
			return false; # Exit function
		}else{
			$opUserDetailsRs = $opUserDetails->fetch_assoc();
			$primaryStatus = $opUserDetailsRs['primary_user'];
		}
		
		# ** Username
		if(!isset($_POST['username']) || empty($_POST['username'])){$errorz .= '* '. lethe_please_enter_a_username .'<br>';}else{
			if(!nicknameVal(mysql_prep($_POST['username']))){$errorz .= '* '. lethe_invalid_username .'<br>';}else{
				if(!chkData("SELECT ID,lethe_user FROM ". db_table_pref ."users WHERE lethe_user='". mysql_prep($_POST['username']) ."' AND ID<>". $this->content_ID ."")){
					$errorz .= '* '. lethe_username_already_exists .'<br>';}else{
						$qr1 .= ",lethe_user='". mysql_prep($_POST['username']) ."'";
					}
			}
		}
		
		# ** Password
		if(!isset($_POST['pass'])){$_POST['pass']=null;}
		if(!isset($_POST['pass2'])){$_POST['pass2']=null;}
		if(!empty($_POST['pass'])){
			if($_POST['pass'] != $_POST['pass2']){$errorz .= '* '. lethe_incorrect_passwords .'<br>';}else{
				if(strlen($_POST['pass'])<5){$errorz .= '* '. lethe_password_is_too_short .'<br>';}else{
					$qr1 .= ",user_pass='". encr($_POST['pass']) ."'";
				}
			}
		}
		
		# ** E-Mail
		if(!isset($_POST['email']) || empty($_POST['email'])){$errorz .= '* '. lethe_please_enter_e_mail_address .'<br>';}else{
			if(!mailVal(mysql_prep($_POST['email']))){$errorz .= '* '. lethe_invalid_e_mail_address .'<br>';}else{
				if(!chkData("SELECT ID,user_mail FROM ". db_table_pref ."users WHERE user_mail='". mysql_prep($_POST['email']) ."' AND ID<>". $this->content_ID ."")){
					$errorz .= '* '. lethe_e_mail_address_already_exists .'<br>';}else{
						$qr1 .= ",user_mail='". mysql_prep($_POST['email']) ."'";
					}
			}
		}
		
		# ** Daily Limit
		if(!isset($_POST['send_mail_limit']) || empty($_POST['send_mail_limit'])){$errorz .= '* '. lethe_please_enter_a_daily_send_limit .'<br>';}else{
			$qr1 .= ',sendLimit=' . intval($_POST['send_mail_limit']);
		}
		
			if($this->admin_area){ # Only Super Admin Can Change Mode Status
				if($this->admin_mode==1){
					if($primaryStatus==0){ # Primary User Mode Cannot be Changed
						# ** Admin Mode
						if(!isset($_POST['amode']) || intval($_POST['amode'])==0){$errorz .= '* '. lethe_please_choose_a_admin_mode .'<br>';}else{
							$qr1 .= ',admin_mode=' . intval($_POST['amode']);
						}
					}
				}
			}
		
		
		if($errorz==''){
			$myconn->query("UPDATE ". db_table_pref ."users SET ". $qr1 ." WHERE ID=". $this->content_ID ."") or die(mysqli_error());
			$this->errPrint = '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_u_pdated_successfully .'</div>';
		}else{
			$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
		
		$opUserDetails->free();
	
	} # Edit User End **	

	# **** Edit Profile 
	function edit_profile(){
	
		global $myconn;
		$errorz = '';
		$qr1 = "edit_date='". date('Y-m-d H:i:s') ."'";
						
		# ** Password
		if(!isset($_POST['pass'])){$_POST['pass']=null;}
		if(!isset($_POST['pass2'])){$_POST['pass2']=null;}
		if(!empty($_POST['pass'])){
			if($_POST['pass'] != $_POST['pass2']){$errorz .= '* '. lethe_incorrect_passwords .'<br>';}else{
				if(strlen($_POST['pass'])<5){$errorz .= '* '. lethe_password_is_too_short .'<br>';}else{
					$qr1 .= ",user_pass='". encr($_POST['pass']) ."'";
				}
			}
		}
		
		# ** E-Mail
		if(!isset($_POST['email']) || empty($_POST['email'])){$errorz .= '* '. lethe_please_enter_e_mail_address .'<br>';}else{
			if(!mailVal(mysql_prep($_POST['email']))){$errorz .= '* '. lethe_invalid_e_mail_address .'<br>';}else{
				if(!chkData("SELECT ID,user_mail FROM ". db_table_pref ."users WHERE user_mail='". mysql_prep($_POST['email']) ."' AND ID<>". $this->content_ID ."")){
					$errorz .= '* '. lethe_e_mail_address_already_exists .'<br>';}else{
						$qr1 .= ",user_mail='". mysql_prep($_POST['email']) ."'";
					}
			}
		}	
		
		if($errorz==''){
			$myconn->query("UPDATE ". db_table_pref ."users SET ". $qr1 ." WHERE ID=". $this->content_ID ."") or die(mysqli_error());
			$this->errPrint = '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_u_pdated_successfully .'</div>';
		}else{
			$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
	
	} # Edit Profile End **	

	# **** Add Template 
	function add_template(){
	
		global $myconn;
		$errorz = '';
		$qr1 = '';
		$qr2 = '';
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
		
		# Template Title
		if(!isset($_POST['title']) || empty($_POST['title'])){$errorz.='* '. lethe_please_enter_a_template_name .'<br>';}else{
			$qr1 .= 'title'; 
			$qr2 .= "'". mysql_prep($_POST['title']) ."'";
			}
			
		# Template Image
		if(empty($_POST['prev_img'])){$errorz.='* '. lethe_please_choose_a_template_preview_image .'<br>';}else{
			$qr1 .= ',preview'; 
			$qr2 .= ",'". mysql_prep($_POST['prev_img']) ."'";
			}
			
		# Template Details
		if(!isset($_POST['details']) || empty($_POST['details']) || strlen($_POST['details'])<80){$errorz.='* '. lethe_please_enter_template_contents .'<br>';}else{
			$qr1 .= ',details'; 
			$qr2 .= ",'". mysql_prep3($_POST['details']) ."'";
			}
			
		if($errorz==''){
				
				# ** Make for Verification
				if(isset($_POST['temp_verify']) && $_POST['temp_verify']=='YES'){
					# Turn Off Old Template
					$myconn->query("UPDATE ". db_table_pref ."newsletter_templates SET verification=0 WHERE ID>0") or die(mysqli_error());
					$qr1 .= ',verification'; 
					$qr2 .= ",1";
				}
				
				$myconn->query("INSERT INTO ". db_table_pref ."newsletter_templates (". $qr1 .") VALUES (". $qr2 .")") or die(mysqli_error());
				
				$this->errPrint = '<div class="alert alert-success">'. lethe_recorded_successfully .'</div>';
				unset($_POST);
				$_POST = array();
			}else{
				
				$this->errPrint = '<div class="alert alert-danger">'. $errorz .'</div>';
				
				}
	
	} # Add Template End **

	# **** Edit Template 
	function edit_template(){
	
		global $myconn;
		$errorz = '';
		$qr1 = '';
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
		
		# ** Delete Proccess
		if(isset($_POST['del'])){
			$myconn->query("DELETE FROM ". db_table_pref ."newsletter_templates WHERE ID=". $this->content_ID ."") or die(mysqli_error());
			$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_d_eleted_successfully .'</div>';;
			return false;
		}
		
		# Template Title
		if(!isset($_POST['title']) || empty($_POST['title'])){$errorz.='* '. lethe_please_enter_a_template_name .'<br>';}else{
			$qr1 .= "title='". mysql_prep($_POST['title']) ."'"; 
			}
			
		# Template Image
		if(empty($_POST['prev_img'])){$errorz.='* '. lethe_please_choose_a_template_preview_image .'<br>';}else{
			$qr1 .= ",preview='". mysql_prep($_POST['prev_img']) ."'"; 
			}
			
		# Template Details
		if(!isset($_POST['details']) || empty($_POST['details']) || strlen($_POST['details'])<80){$errorz.='* '. lethe_please_enter_template_contents .'<br>';}else{
			$qr1 .= ",details='". mysql_prep3($_POST['details']) ."'"; 
			}
			
		if($errorz==''){
		
				# ** Make for Verification
				if(isset($_POST['temp_verify']) && $_POST['temp_verify']=='YES'){
					# Turn Off Old Template
					$myconn->query("UPDATE ". db_table_pref ."newsletter_templates SET verification=0 WHERE ID>0") or die(mysqli_error());
					$qr1 .= ',verification=1'; 
				} else{
					$qr1 .= ',verification=0';
				}
		
				$myconn->query("UPDATE ". db_table_pref ."newsletter_templates SET ". $qr1 ." WHERE ID=". $this->content_ID ."") or die(mysqli_error());
				$this->errPrint = '<div class="alert alert-success">'. lethe_u_pdated_successfully .'</div>';
			}else{
				
				$this->errPrint = '<div class="alert alert-danger">'. $errorz .'</div>';
				
				}
	
	} # Edit Template End **
	
	# **** Edit Settings 
	function edit_settings(){
	
		global $myconn;
		$errorz = '';
		
		$confList = '';
		$confList.= "<?php\n";
		$confList .= "# +------------------------------------------------------------------------+
# | Artlantis CMS Solutions                                                |
# +------------------------------------------------------------------------+
# | Lethe Newsletter & Mailing System                                      |
# | Copyright (c) Artlantis Design Studio 2014. All rights reserved.       |
# | Version       1.1.5                                                    |
# | Last modified ".date('d-m-Y H.i.s')."                                      |
# | Email         developer@artlantis.net                                  |
# | Web           http://www.artlantis.net                                 |
# +------------------------------------------------------------------------+";
		$confList .= "\n\n";
		$confList .= "# General Settings\n";
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
		
		# Site URL
		if(!isset($_POST['site_url']) || !urlVal($_POST['site_url'])){
			$errorz .= '* '. lethe_please_enter_a_site_url .'<br>';
		}else{
			$confList .= "define('set_site_url','". mysql_prep($_POST['site_url']) ."'); # Site URL\n";
			}
			
		# RSS URL
		if(!isset($_POST['rss_url']) || !urlVal($_POST['rss_url'])){
			$confList .= "define('set_rss_url','". relDocs(LETHEPATH) ."/lethe.newsletter.php?pos=4'); # RSS URL\n";
		}else{
			$confList .= "define('set_rss_url','". mysql_prep($_POST['rss_url']) ."'); # RSS URL\n";
			}
				
		# Only Verified
		if(!isset($_POST['verified']) || empty($_POST['verified'])){
			$confList .= "define('set_only_verified',0); # Select Only Verified Mails\n";
		}else{
			$confList .= "define('set_only_verified',1); # Select Only Verified Mails\n";
			}
			
		# Only Active
		if(!isset($_POST['active']) || empty($_POST['active'])){
			$confList .= "define('set_only_active',0); # Select Only Active Mails\n";
		}else{
			$confList .= "define('set_only_active',1); # Select Only Active Mails\n";
			}
			
		# Select Random Mails
		if(!isset($_POST['random_load']) || empty($_POST['random_load'])){
			$confList .= "define('set_random_load',0); # Select Random Mails\n";
		}else{
			$confList .= "define('set_random_load',1); # Select Random Mails\n";
			}
			
		# Send Verification
		if(!isset($_POST['send_verification']) || empty($_POST['send_verification'])){
			$confList .= "define('set_send_verification',0); # Send Verification Mail\n";
		}else{
			$confList .= "define('set_send_verification',1); # Send Verification Mail\n";
			}
			
		# Unique Code
		if(!isset($_POST['unique_code']) || empty($_POST['unique_code'])){
			$errorz .= '* '. lethe_please_enter_unique_code .'<br>';
		}else{
			$confList .= "define('set_unique_code','". mysql_prep($_POST['unique_code']) ."'); # Unique Code\n";
			}
			
		# Permissions
		if(!isset($_POST['template_permission']) && empty($_POST['template_permission'])){ # Template Permission
			$confList .= "define('set_template_permission',0); # Template Permission\n";
		}else{
			$confList .= "define('set_template_permission',1); # Template Permission\n";
		}
		
		if(!isset($_POST['subgrp_permission']) && empty($_POST['subgrp_permission'])){ # Subscribe Group Permission
			$confList .= "define('set_subgrp_permission',0); # Subscribe Group Permission\n";
		}else{
			$confList .= "define('set_subgrp_permission',1); # Subscribe Group Permission\n";
		}
		
		if(!isset($_POST['subscr_permission']) && empty($_POST['subscr_permission'])){ # Subscriber Permission
			$confList .= "define('set_subscr_permission',0); # Subscriber Permission\n";
		}else{
			$confList .= "define('set_subscr_permission',1); # Subscriber Permission\n";
		}
		
		if(!isset($_POST['exmp_imp_permission']) && empty($_POST['exmp_imp_permission'])){ # Import / Export Permission
			$confList .= "define('set_exmp_imp_permission',0); # Import / Export Permission\n";
		}else{
			$confList .= "define('set_exmp_imp_permission',1); # Import / Export Permission\n";
		}
		
		if(!isset($_POST['newsletter_permission']) && empty($_POST['newsletter_permission'])){ # Newsletter Permission
			$confList .= "define('set_newsletter_permission',0); # Newsletter Permission\n";
		}else{
			$confList .= "define('set_newsletter_permission',1); # Newsletter Permission\n";
		}
		
		if(!isset($_POST['autoresponder_permission']) && empty($_POST['autoresponder_permission'])){ # Autoresponder Permission
			$confList .= "define('set_autoresponder_permission',0); # Autoresponder Permission\n";
		}else{
			$confList .= "define('set_autoresponder_permission',1); # Autoresponder Permission\n";
		}
		
		if(!isset($_POST['after_user_delete']) || !is_numeric($_POST['after_user_delete'])){ # After User Delete
			$confList .= "define('set_after_user_delete',0); # After User Delete\n";
		}else{
			$confList .= "define('set_after_user_delete',". intval($_POST['after_user_delete']) ."); # After User Delete\n";
		}
		
		# Timezones
		if(!isset($_POST['set_def_timezone']) || empty($_POST['set_def_timezone'])){ # Default Timezone
			if(date_default_timezone_get()){
				$confList .= "define('set_def_timezone','". date_default_timezone_get() ."'); # Default Timezone\n";
			}else{
				$confList .= "define('set_def_timezone','Pacific/Midway'); # Default Timezone\n";
			}
		}else{
			$confList .= "define('set_def_timezone','". mysql_prep($_POST['set_def_timezone']) ."'); # Default Timezone\n";
		}
		
		# After Unsubscribe
		if(!isset($_POST['set_after_unsubscribe']) || !is_numeric($_POST['set_after_unsubscribe'])){
			$confList .= "define('set_after_unsubscribe',0); # After Unsubscribe\n";
		}else{
			$confList .= "define('set_after_unsubscribe',". intval($_POST['set_after_unsubscribe']) ."); # After Unsubscribe\n";
		}
		
		$confList .= "/* ************************************* */\n";
		$confList .= "date_default_timezone_set(set_def_timezone);\n";
		$confList .= "define('set_lethe_powered','<div id=\"lethe-powered\">". lethe_newsletter_site_name ."<br>Powered by <a href=\"http://www.artlantis.net\" target=\"_blank\">Artlantis Design Studio</a></div>'); # Powered\n";
		$confList .= "/* ************************************* */\n";
			
			
		$confList.= "?>\n";
			
		if($errorz==''){
				
			# Process
				$pathw = dirname(dirname(__FILE__));
				$filez="lethe.config.php";
				if (!file_exists ($pathw . '/' . $filez) ) {
				@touch ($pathw . '/' . $filez);
				}
				$conc=@fopen ($pathw . '/' . $filez,'w');
				if (!$conc) {
				$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_setting_file_cannot_opened .'</div>';
				return false;
				}
				
				#************* Writing *****
				if (fputs ($conc,$confList) ){
					$this->errPrint = '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_u_pdated_successfully .'</div>';
					header('Location: ?pos=4&ppos=0');
				}else {
					$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_setting_file_cannot_opened .'</div>';
				}
				fclose($conc);
				#************* Writing End **
				
			}else{
				
				$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
				
				}
	
	} # Edit Settings End **
	
	# **** Add New Account 
	function add_account(){
	
		global $myconn;
		$errorz = '';
		$qr1 = "";
		$qr2 = "";
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
	
		# ** Account Title
		if(!isset($_POST['account_title']) || empty($_POST['account_title'])){$errorz .= '* '. lethe_please_enter_a_account_title .'<br>';}else{
			$qr1 .= "account_title";
			$qr2 .= "'". mysql_prep($_POST['account_title']) ."'";
		}
		
		# ** Sender Title
		if(!isset($_POST['sender_title']) || empty($_POST['sender_title'])){$errorz .= '* '. lethe_please_enter_a_sender_title .'<br>';}else{
			$qr1 .= ",sender_title";
			$qr2 .= ",'". mysql_prep($_POST['sender_title']) ."'";
		}
		
		# ** Sender Mail
		if(!isset($_POST['sender_mail']) || empty($_POST['sender_mail'])){$errorz .= '* '. lethe_please_enter_a_sender_mail .'<br>';}else{
			if(!mailVal(mysql_prep($_POST['sender_mail']))){$errorz .= '* '. lethe_invalid_e_mail_address .'<br>';}else{
				$qr1 .= ",sender_mail";
				$qr2 .= ",'". mysql_prep($_POST['sender_mail']) ."'";
			}
		}
		
		# ** Reply Mail
		if(!isset($_POST['reply_mail']) || empty($_POST['reply_mail'])){$errorz .= '* '. lethe_please_enter_a_reply_mail .'<br>';}else{
			if(!mailVal(mysql_prep($_POST['reply_mail']))){$errorz .= '* '. lethe_invalid_e_mail_address .'<br>';}else{
				$qr1 .= ",reply_mail";
				$qr2 .= ",'". mysql_prep($_POST['reply_mail']) ."'";
			}
		}
		
		# ** Test Mail
		if(!isset($_POST['test_mail']) || empty($_POST['test_mail'])){$errorz .= '* '. lethe_please_enter_a_test_mail .'<br>';}else{
			if(!mailVal(mysql_prep($_POST['test_mail']))){$errorz .= '* '. lethe_invalid_e_mail_address .'<br>';}else{
				$qr1 .= ",test_mail";
				$qr2 .= ",'". mysql_prep($_POST['test_mail']) ."'";
			}
		}
		
		# ** Permission
		if(!isset($_POST['permission']) || empty($_POST['permission'])){
				$qr1 .= ",permission";
				$qr2 .= ",2";
		}else{
				$qr1 .= ",permission";
				$qr2 .= ",1";
		}
		
		# ** Debug Mode
		if(!isset($_POST['debug_mode']) || empty($_POST['debug_mode'])){
				$qr1 .= ",debug_mode";
				$qr2 .= ",0";
		}else{
				$qr1 .= ",debug_mode";
				$qr2 .= ",1";
		}
		
		# ** Send Mail Limit
		if(!isset($_POST['send_mail_limit']) || intval($_POST['send_mail_limit'])==0){$errorz .= '* '. lethe_please_enter_send_mail_limit .'<br>';}else{
				$qr1 .= ",send_mail_limit";
				$qr2 .= ",". intval($_POST['send_mail_limit']) ."";
		}
		
		# ** Mail Limit Per Connection
		if(!isset($_POST['mail_limit_per_con']) || intval($_POST['mail_limit_per_con'])==0){$errorz .= '* '. lethe_please_enter_mail_limit_per_con .'<br>';}else{
				$qr1 .= ",mail_limit_per_con";
				$qr2 .= ",". intval($_POST['mail_limit_per_con']) ."";
		}
		
		# ** Mail Send Duration
		if(!isset($_POST['send_mail_duration']) || intval($_POST['send_mail_duration'])==0){$errorz .= '* '. lethe_please_enter_mail_send_duration .'<br>';}else{
				$qr1 .= ",send_mail_duration";
				$qr2 .= ",". intval($_POST['send_mail_duration']) ."";
		}
		
		# ** Mail Type
		if(!isset($_POST['email_type'])){$errorz .= '* '. lethe_please_choose_a_mail_type .'<br>';}else{
				$qr1 .= ",email_type";
				$qr2 .= ",". intval($_POST['email_type']) ."";
		}
		
		# ** Mail Send Type
		if(!isset($_POST['send_type'])){$errorz .= '* '. lethe_please_choose_a_mail_send_type .'<br>';}else{
				$qr1 .= ",send_type";
				$qr2 .= ",". intval($_POST['send_type']) ."";
		}
		
		# ** Active
		if(!isset($_POST['active']) || empty($_POST['active'])){
				$qr1 .= ",active";
				$qr2 .= ",0";
		}else{
				$qr1 .= ",active";
				$qr2 .= ",1";
		}
		
		
		# ** SMTP Host
		if(!isset($_POST['smtp_host']) || empty($_POST['smtp_host'])){$errorz .= '* '. lethe_please_enter_a_smtp_host .'<br>';}else{
				$qr1 .= ",smtp_host";
				$qr2 .= ",'". mysql_prep($_POST['smtp_host']) ."'";
		}
		
		# ** SMTP Port
		if(!isset($_POST['smtp_port']) || intval($_POST['smtp_port'])==0){$errorz .= '* '. lethe_please_enter_a_smtp_port .'<br>';}else{
				$qr1 .= ",smtp_port";
				$qr2 .= ",". intval($_POST['smtp_port']) ."";
		}
		
		# ** SMTP User
		if(!isset($_POST['smtp_user']) || empty($_POST['smtp_user'])){$errorz .= '* '. lethe_please_enter_a_smtp_username .'<br>';}else{
				$qr1 .= ",smtp_user";
				$qr2 .= ",'". mysql_prep($_POST['smtp_user']) ."'";
		}
		
		# ** SMTP Password
		if(!isset($_POST['smtp_pass']) || empty($_POST['smtp_pass'])){$errorz .= '* '. lethe_please_enter_a_smtp_password .'<br>';}else{
				$qr1 .= ",smtp_pass";
				$qr2 .= ",'". mysql_prep($_POST['smtp_pass']) ."'";
		}
		
		# ** SMTP Auth
		if(!isset($_POST['smtp_auth']) || empty($_POST['smtp_auth'])){
				$qr1 .= ",smtp_auth";
				$qr2 .= ",0";
		}else{
				$qr1 .= ",smtp_auth";
				$qr2 .= ",1";
		}
		
		# ** POP3 Host
		if(isset($_POST['pop3_host'])){
				$qr1 .= ",pop3_host";
				$qr2 .= ",'". mysql_prep($_POST['pop3_host']) ."'";
		}
		
		# ** POP3 Port
		if(isset($_POST['pop3_port']) && is_numeric($_POST['pop3_port'])){
				$qr1 .= ",pop3_port";
				$qr2 .= ",". intval($_POST['pop3_port']) ."";
		}
		
		# ** POP3 User
		if(isset($_POST['pop3_user']) && !empty($_POST['pop3_user'])){
			$qr1 .= ",pop3_user";
			$qr2 .= ",'". mysql_prep($_POST['pop3_user']) ."'";
		} else{
			$qr1 .= ",pop3_user";
			$qr2 .= ",'". mysql_prep(@$_POST['smtp_user']) ."'";
		}
		
		# ** POP3 Pass
		if(isset($_POST['pop3_pass']) && !empty($_POST['pop3_pass'])){
				$qr1 .= ",pop3_pass";
				$qr2 .= ",'". mysql_prep($_POST['pop3_pass']) ."'";
		}
		
		# ** IMAP Host
		if(isset($_POST['imap_host'])){
				$qr1 .= ",imap_host";
				$qr2 .= ",'". mysql_prep($_POST['imap_host']) ."'";
		}
		
		# ** IMAP Port
		if(isset($_POST['imap_port']) && is_numeric($_POST['imap_port'])){
				$qr1 .= ",imap_port";
				$qr2 .= ",". intval($_POST['imap_port']) ."";
		}
		
		# ** IMAP Pass
		if(isset($_POST['imap_pass']) && !empty($_POST['imap_pass'])){
				$qr1 .= ",imap_pass";
				$qr2 .= ",'". mysql_prep($_POST['imap_pass']) ."'";
		}
		
		# ** IMAP User
		if(isset($_POST['imap_user']) && !empty($_POST['imap_user'])){
			$qr1 .= ",imap_user";
			$qr2 .= ",'". mysql_prep($_POST['imap_user']) ."'";
		} else{
			$qr1 .= ",imap_user";
			$qr2 .= ",'". mysql_prep(@$_POST['smtp_user']) ."'";
		}
		
		# ** SSL / TLS Mode
		if(!isset($_POST['ssl_tls'])){$errorz .= '* '. lethe_please_choose_a_secure_option .'<br>';}else{
				$qr1 .= ",ssl_tls";
				$qr2 .= ",". intval($_POST['ssl_tls']) ."";
		}
		
		# ** Bounce Account
		if(isset($_POST['bounce_acc']) && is_numeric($_POST['bounce_acc'])){
				$qr1 .= ",bounce_acc";
				$qr2 .= ",". intval($_POST['bounce_acc']) ."";
		}
		
		# General
				$qr1 .= ",dailySent,resetDaily";
				$qr2 .= ",0,DATE_ADD(NOW() , INTERVAL 1 DAY)";
		
		
		if($errorz==''){
			$myconn->query("INSERT INTO ". db_table_pref ."newsletter_accounts (". $qr1 .") VALUES (". $qr2 .")") or die(mysqli_error());
			$this->errPrint = '<div class="alert alert-success">'. lethe_recorded_successfully .'</div>';
		}else{
			$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
	
	} # Add Account End **
	
	# **** Edit Account 
	function edit_account(){
	
		global $myconn;
		$errorz = '';
		$qr1 = "";
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
		
		# ** Delete Proccess
		if(isset($_POST['del'])){
			# Check Primary Account
			if(chkData("SELECT ID,primary_account FROM ". db_table_pref ."newsletter_accounts WHERE ID=". $this->content_ID ." AND primary_account=1")){
				if(chkData("SELECT ID,SUID FROM ". db_table_pref ."newsletters WHERE SUID=". $this->content_ID ."")){
				$myconn->query("DELETE FROM ". db_table_pref ."newsletter_accounts WHERE ID=". $this->content_ID ."") or die(mysqli_error());
				$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_d_eleted_successfully .'</div>';
				$_GET['ppos'] = 1;
				return false;
				}else{
				$errorz .= '* '. lethe_there_founded_an_newsletter_in_this_account .'. '. lethe_account_cannot_be_d_eleted .'!<br>';
				}
			}else{
				$errorz .= '* '. lethe_this_is_primary_account .'. '. lethe_account_cannot_be_d_eleted .'!<br>';
			}
		}
	
		# ** Account Title
		if(!isset($_POST['account_title']) || empty($_POST['account_title'])){$errorz .= '* '. lethe_please_enter_a_account_title .'<br>';}else{
			$qr1 .= "account_title='". mysql_prep($_POST['account_title']) ."'";
		}
		
		# ** Sender Title
		if(!isset($_POST['sender_title']) || empty($_POST['sender_title'])){$errorz .= '* '. lethe_please_enter_a_sender_title .'<br>';}else{
			$qr1 .= ",sender_title='". mysql_prep($_POST['sender_title']) ."'";
		}
		
		# ** Sender Mail
		if(!isset($_POST['sender_mail']) || empty($_POST['sender_mail'])){$errorz .= '* '. lethe_please_enter_a_sender_mail .'<br>';}else{
			if(!mailVal(mysql_prep($_POST['sender_mail']))){$errorz .= '* '. lethe_invalid_e_mail_address .'<br>';}else{
				$qr1 .= ",sender_mail='". mysql_prep($_POST['sender_mail']) ."'";
			}
		}
		
		# ** Reply Mail
		if(!isset($_POST['reply_mail']) || empty($_POST['reply_mail'])){$errorz .= '* '. lethe_please_enter_a_reply_mail .'<br>';}else{
			if(!mailVal(mysql_prep($_POST['reply_mail']))){$errorz .= '* '. lethe_invalid_e_mail_address .'<br>';}else{
				$qr1 .= ",reply_mail='". mysql_prep($_POST['reply_mail']) ."'";
			}
		}
		
		# ** Test Mail
		if(!isset($_POST['test_mail']) || empty($_POST['test_mail'])){$errorz .= '* '. lethe_please_enter_a_test_mail .'<br>';}else{
			if(!mailVal(mysql_prep($_POST['test_mail']))){$errorz .= '* '. lethe_invalid_e_mail_address .'<br>';}else{
				$qr1 .= ",test_mail='". mysql_prep($_POST['test_mail']) ."'";
			}
		}
		
		# ** Permission
		if(!isset($_POST['permission']) || empty($_POST['permission'])){
				$qr1 .= ",permission=2";
		}else{
				$qr1 .= ",permission=1";
		}
		
		# ** Debug Mode
		if(!isset($_POST['debug_mode']) || empty($_POST['debug_mode'])){
				$qr1 .= ",debug_mode=0";
		}else{
				$qr1 .= ",debug_mode=1";
		}		
		
		# ** Send Mail Limit
		if(!isset($_POST['send_mail_limit']) || intval($_POST['send_mail_limit'])==0){$errorz .= '* '. lethe_please_enter_send_mail_limit .'<br>';}else{
				$qr1 .= ",send_mail_limit=".intval($_POST['send_mail_limit']);
		}
		
		# ** Mail Limit Per Connection
		if(!isset($_POST['mail_limit_per_con']) || intval($_POST['mail_limit_per_con'])==0){$errorz .= '* '. lethe_please_enter_mail_limit_per_con .'<br>';}else{
				$qr1 .= ",mail_limit_per_con=".intval($_POST['mail_limit_per_con']);
		}
		
		# ** Mail Send Duration
		if(!isset($_POST['send_mail_duration']) || intval($_POST['send_mail_duration'])==0){$errorz .= '* '. lethe_please_enter_mail_send_duration .'<br>';}else{
				$qr1 .= ",send_mail_duration=".intval($_POST['send_mail_duration']);
		}
		
		# ** Mail Type
		if(!isset($_POST['email_type'])){$errorz .= '* '. lethe_please_choose_a_mail_type .'<br>';}else{
				$qr1 .= ",email_type=".intval($_POST['email_type']);
		}
		
		# ** Mail Send Type
		if(!isset($_POST['send_type'])){$errorz .= '* '. lethe_please_choose_a_mail_send_type .'<br>';}else{
				$qr1 .= ",send_type=".intval($_POST['send_type']);
		}
		
		# ** Active
		if(!isset($_POST['active']) || empty($_POST['active'])){
				$qr1 .= ",active=0";
		}else{
				$qr1 .= ",active=1";
		}
		
		# ** SMTP Host
		if(!isset($_POST['smtp_host']) || empty($_POST['smtp_host'])){$errorz .= '* '. lethe_please_enter_a_smtp_host .'<br>';}else{
				$qr1 .= ",smtp_host='". mysql_prep($_POST['smtp_host']) ."'";
		}
		
		# ** SMTP Port
		if(!isset($_POST['smtp_port']) || intval($_POST['smtp_port'])==0){$errorz .= '* '. lethe_please_enter_a_smtp_port .'<br>';}else{
				$qr1 .= ",smtp_port=". intval($_POST['smtp_port']) ."";
		}
		
		# ** SMTP User
		if(!isset($_POST['smtp_user']) || empty($_POST['smtp_user'])){$errorz .= '* '. lethe_please_enter_a_smtp_username .'<br>';}else{
				$qr1 .= ",smtp_user='". mysql_prep($_POST['smtp_user']) ."'";
		}
		
		# ** SMTP Password
		if(isset($_POST['smtp_pass']) && !empty($_POST['smtp_pass'])){
				$qr1 .= ",smtp_pass='". mysql_prep($_POST['smtp_pass']) ."'";
		}
		
		# ** SMTP Auth
		if(!isset($_POST['smtp_auth']) || empty($_POST['smtp_auth'])){
				$qr1 .= ",smtp_auth=0";
		}else{
				$qr1 .= ",smtp_auth=1";
		}	
		
		# ** POP3 Host
		if(isset($_POST['pop3_host'])){
				$qr1 .= ",pop3_host='". mysql_prep($_POST['pop3_host']) ."'";
		}
		
		# ** POP3 Port
		if(isset($_POST['pop3_port']) && is_numeric($_POST['smtp_port'])){
				$qr1 .= ",pop3_port=". intval($_POST['pop3_port']) ."";
		}
		
		# ** POP3 User
		if(isset($_POST['pop3_user']) && !empty($_POST['pop3_user'])){
			$qr1 .= ",pop3_user='". mysql_prep($_POST['pop3_user']) ."'";
		} else{
			$qr1 .= ",pop3_user='". mysql_prep($_POST['smtp_user']) ."'";
		}
		
		# ** POP3 Pass
		if(isset($_POST['pop3_pass']) && !empty($_POST['pop3_pass'])){
				$qr1 .= ",pop3_pass='". mysql_prep($_POST['pop3_pass']) ."'";
		}
		
		# ** IMAP Host
		if(isset($_POST['imap_host'])){
				$qr1 .= ",imap_host='". mysql_prep($_POST['imap_host']) ."'";
		}
		
		# ** IMAP Port
		if(isset($_POST['imap_port']) && is_numeric($_POST['imap_port'])){
				$qr1 .= ",imap_port=". intval($_POST['imap_port']) ."";
		}
		
		# ** IMAP User
		if(isset($_POST['imap_user']) && !empty($_POST['imap_user'])){
			$qr1 .= ",imap_user='". mysql_prep($_POST['imap_user']) ."'";
		} else{
			$qr1 .= ",imap_user='". mysql_prep($_POST['smtp_user']) ."'";
		}
		
		# ** IMAP Pass
		if(isset($_POST['imap_pass']) && !empty($_POST['imap_pass'])){
				$qr1 .= ",imap_pass='". mysql_prep($_POST['imap_pass']) ."'";
		}
		
		# ** SSL / TLS Mode
		if(!isset($_POST['ssl_tls'])){$errorz .= '* '. lethe_please_choose_a_secure_option .'<br>';}else{
				$qr1 .= ",ssl_tls=".intval($_POST['ssl_tls']);
		}
		
		# ** Bounce Account
		if(isset($_POST['bounce_acc']) && is_numeric($_POST['bounce_acc'])){
				$qr1 .= ",bounce_acc=".intval($_POST['bounce_acc']);
		}
		
		
		if($errorz==''){
			$myconn->query("UPDATE ". db_table_pref ."newsletter_accounts SET ". $qr1 ." WHERE ID=". $this->content_ID ."") or die(mysqli_error());
			$this->errPrint = '<div class="alert alert-success">'. lethe_u_pdated_successfully .'</div>';
		}else{
			$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
	
	} # Edit Account End **
	
	# **** Short Codes
	function short_codes(){
	
		global $myconn;
		$errorz = '';	
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
		
		# Add New Code 
		if(isset($_POST['new_code']) && !empty($_POST['new_code'])){
			if(isset($_POST['new_code_val']) && !empty($_POST['new_code_val'])){
				$myconn->query("INSERT INTO ". db_table_pref ."newsletter_codes (lethe_code,lethe_code_val) VALUES ('". mysql_prep($_POST['new_code']) ."','". mysql_prep($_POST['new_code_val']) ."')") or die(mysqli_error());
			}
		}
		
		# Delete Codes
			if(!isset($_POST['delCode'])){$_POST['delCode']=null;}
			$exp_id=null;
			$checkbox = $_POST['delCode'];
			$countCheck = count($_POST['delCode']);
			
				for($i=0;$i<$countCheck;$i++) {
					$exp_id = $checkbox[$i];
	
					$myconn->query("delete from ". db_table_pref ."newsletter_codes where ID=". $exp_id ."") or die(mysqli_error());
				}
		
		
		if($errorz==''){
			$this->errPrint = '<div class="alert alert-success">'. lethe_u_pdated_successfully .'</div>';
		}else{
			$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
	
	} # Short Codes End **

	# **** Bounce Codes
	function bounce_codes(){
	
		global $myconn;
		$errorz = '';	
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
		
		# Add New Code 
		if(isset($_POST['new_code']) && !empty($_POST['new_code'])){
				if(isset($_POST['new_code_act']) && !empty($_POST['new_code_act'])){$active=1;}else{$active=0;}
				if(!isset($_POST['new_bounce_rule']) || !is_numeric($_POST['new_bounce_rule'])){$set_after_bounce=0;}else{$set_after_bounce=intval($_POST['new_bounce_rule']);}
				if(chkData("SELECT * FROM ". db_table_pref ."newsletter_bounce_catcher WHERE bounce_code='". mysql_prep($_POST['new_code']) ."'")){
					$myconn->query("INSERT INTO ". db_table_pref ."newsletter_bounce_catcher (bounce_code,active,bounce_rule) VALUES ('". mysql_prep($_POST['new_code']) ."',". $active .",". $set_after_bounce .")") or die(mysqli_error());
				}
		}
		
		# Delete Codes
			if(!isset($_POST['delCode'])){$_POST['delCode']=null;}
			$exp_id=null;
			$checkbox = $_POST['delCode'];
			$countCheck = count($_POST['delCode']);
			
				for($i=0;$i<$countCheck;$i++) {
					$exp_id = $checkbox[$i];
	
					$myconn->query("delete from ". db_table_pref ."newsletter_bounce_catcher where ID=". $exp_id ."") or die(mysqli_error());
				}
		
		
		if($errorz==''){
			$this->errPrint = '<div class="alert alert-success">'. lethe_u_pdated_successfully .'</div>';
		}else{
			$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
	
	} # Bounce Codes End **

	# **** Edit Subscriber Groups 
	function edit_subscriber_group(){
	
		global $myconn;
		$errorz = '';
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
	
		# ** Add New Group
		if(isset($_POST['subscrCat_new']) && !empty($_POST['subscrCat_new'])){
			if(isset($_POST['active_new']) && !empty($_POST['active_new'])){$active=1;}else{$active=0;}
			$_POST['icon_new'] = '-';
			if(!chkData("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE group_name='". mysql_prep($_POST['subscrCat_new']) ."'")){
				$errorz .= '* <strong>'. mysql_prep($_POST['subscrCat_new']) .'</strong> '. lethe_already_exists .'<br>';
			}else{
				$icon_new = '-';
				$myconn->query("INSERT INTO ". db_table_pref ."newsletter_groups (group_name,active,icon) VALUES ('". mysql_prep($_POST['subscrCat_new']) ."',". $active .",'". mysql_prep($icon_new) ."')") or die(mysqli_error());
			}
		}
		
		# ** Update Groups
		if(!isset($_POST['total_rec'])){$_POST['total_rec']=0;}
		$total_rec = intval($_POST['total_rec']);
		
		$stmt = $myconn->prepare("UPDATE 
											". db_table_pref ."newsletter_groups 
								     SET 
											group_name=?,
											active=?,
											icon=? 
									WHERE 
											ID=?
									");
		
		for($i=1;$i<=$total_rec;$i++){
			if(isset($_POST['ID'.$i])){$IDx = intval($_POST['ID'.$i]);}
			if(isset($_POST['active'.$i]) && !empty($_POST['active'.$i])){$active=1;}else{$active=0;}
			
			# Delete Proccess
			if(isset($_POST['delCat'.$i]) && !empty($_POST['delCat'.$i])){
				if(cntData("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE system_group=1 AND ID=". $IDx ."")==0){
					$myconn->query("DELETE FROM ". db_table_pref ."newsletter_groups WHERE ID=". $IDx ."") or die(mysqli_error());
					$myconn->query("DELETE FROM ". db_table_pref ."newsletter_subscribers WHERE GID=". $IDx ."") or die(mysqli_error());
					$myconn->query("DELETE FROM ". db_table_pref ."newsletter_tasks WHERE GID=". $IDx ."") or die(mysqli_error());
				}else{
					$errorz .= '* '. lethe_system_groups_cannot_be_d_elete .'<br>';
				}
			}
			
			# Update Proccess
			if(isset($_POST['subscrCat'.$i]) && !empty($_POST['subscrCat'.$i])){
				if(chkData("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE group_name='". mysql_prep($_POST['subscrCat'.$i]) ."' AND ID<>". $IDx ."")){
					if(!isset($_POST['icon'.$i]) || empty($_POST['icon'.$i])){$icon='-';}else{$icon=trim($_POST['icon'.$i]);}
						@$stmt->bind_param('sisi', mysql_prep($_POST['subscrCat'.$i]), $active, mysql_prep($icon), $IDx);
						@$stmt->execute();
				}else{
					$errorz .= '* <strong># '. $i .'</strong> '. lethe_already_exists .'<br>';
				}
			}else{
				$errorz .= '* <strong># '. $i .'</strong> '. lethe_group_name_cannot_be_empty .'<br>';
			}
		} $stmt->close();
		
		if($errorz==''){
			$errorz = '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_u_pdated_successfully .'</div>';
		}else{
			$errorz = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
		
		$this->errPrint = $errorz;
	
	} # Edit Subscriber Groups End **

	# **** Add Subscribe Form
	function add_subscribe_form($tab_name=''){
	
		global $myconn;
		$errorz = '';
		$qr1 = '';
		$qr2 = "";
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		
		# ** Form Code
		if(!isset($_POST['sub_form_code1']) || empty($_POST['sub_form_code1'])){
			$errorz .= '* '. lethe_invalid_form_code .'<br>';
		}
		
		# ** Form Title
		if(!isset($_POST['form_title1']) || empty($_POST['form_title1'])){
			$errorz .= '* '. lethe_please_enter_form_title .'<br>';
		}
		
		# ** Form Type
		if(!isset($_POST['sub_form_typ1']) || !is_numeric($_POST['sub_form_typ1'])){
			$errorz .= '* '. lethe_invalid_form_type .'<br>';
		}
		
		# ** Form Options
		if(!isset($_POST['sub_form_opt1']) || empty($_POST['sub_form_opt1'])){
			$errorz .= '* '. lethe_invalid_field_options .'<br>';
		}
		
		# ** Form Codes
		if(!isset($_POST['sub_forms_1']) || empty($_POST['sub_forms_1'])){
			$errorz .= '* '. lethe_please_enter_form_codes .'<br>';
		}
		
		# ** Form Success Text
		if(!isset($_POST['form_succ_text']) || empty($_POST['form_succ_text'])){
			$errorz .= '* '. lethe_please_enter_a_success_text .'<br>';
		}
		
		# ** Form URL Title
		if(!isset($_POST['form_succ_link_title']) || empty($_POST['form_succ_link_title'])){
			$errorz .= '* '. lethe_please_enter_a_success_url_title .'<br>';
		}
		
		# ** Form URL
		if(!isset($_POST['form_succ_link']) || !urlVal($_POST['form_succ_link'])){
			$errorz .= '* '. lethe_invalid_success_url .'<br>';
		}
		
		# ** Form Redirect Time
		if(!isset($_POST['form_succ_redir_time']) || !is_numeric($_POST['form_succ_redir_time'])){
			$errorz .= '* '. please_enter_a_redirection_time .'<br>';
		}
		
		# ** Remove Form After Registration
		if(isset($_POST['remove_after']) && $_POST['remove_after']=='YES'){
			$remove_after = 1;
		}else{
			$remove_after = 0;
		}
		
		if($errorz==''){
		
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}
		
			$smt = $myconn->prepare("INSERT INTO ". db_table_pref ."newsletter_forms (form_code,
																					  form_name,
																					  form_type,
																					  field_options,
																					  form_contents,
																					  form_succ_text,
																					  form_succ_url_title,
																					  form_succ_url,
																					  form_succ_redir_time,
																					  remove_after
																					  ) 
																			VALUES 
																					  (?,?,?,?,?,?,?,?,?,?)
											") or die(mysqli_error($myconn));
			$smt->bind_param('ssisssssii',
												$_POST['sub_form_code1'],
												$_POST['form_title1'],
												$_POST['sub_form_typ1'],
												$_POST['sub_form_opt1'],
												$_POST['sub_forms_1'],
												$_POST['form_succ_text'],
												$_POST['form_succ_link_title'],
												$_POST['form_succ_link'],
												$_POST['form_succ_redir_time'],
												$remove_after
								) or die(mysqli_error($myconn));
			$smt->execute();
			$smt->close();
		
			$this->errPrint = '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_recorded_successfully .'</div>';
			$this->errPrint .= '<script>
										$(window).load(function() {
											$("#subscr_forms div").removeClass("active");
											$("#subscr_form_modes li").removeClass("active");
											$("#'. $tab_name .'").addClass("active");
											$("#subscr_form_modes li[data-tab-id='. $tab_name .']").addClass("active");
										});
								</script>'; # Open Current Tab
		}else{
			$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
			$this->errPrint .= '<script>
										$(window).load(function() {
											$("#subscr_forms div").removeClass("active");
											$("#subscr_form_modes li").removeClass("active");
											$("#'. $tab_name .'").addClass("active");
											$("#subscr_form_modes li[data-tab-id='. $tab_name .']").addClass("active");
										});
								</script>'; # Open Current Tab
		}
	
	}	

	# **** Add Subscriber
	function add_subscriber(){
	
		global $myconn;
		$errorz = '';
		$subCode = md5(uniqid(mt_rand(), true));
		$qr1 = 'sub_code';
		$qr2 = "'". $subCode ."'";
		$remove_after = 0;
		$subFormID = 0;
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		
				$qr1 .= ",active,activated";$qr2 .= ",1,1"; # If admin added a subscriber, subscriber will activated automatically
		}else{ # Check Form on Public Area
		
			if(!isset($_POST['sub_form_code']) || empty($_POST['sub_form_code'])){
				$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_form_error .'</div>';
				return false;
			}else{
				$sForm_ID = $_POST['sub_form_code'];
				$opForm = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_forms WHERE form_code='". mysql_prep($sForm_ID) ."'") or die(mysqli_error());
				if(mysqli_num_rows($opForm)==0){
					$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_form_error .'</div>';
					return false;
				}else{
					$opFormRs = $opForm->fetch_assoc();
					$field_options = $opFormRs['field_options'];
					$remove_after = $opFormRs['remove_after'];
					$subFormID = $opFormRs['ID'];
					$successText = $opFormRs['form_succ_text'];
					$successURLTitle = $opFormRs['form_succ_url_title'];
					$successURL = $opFormRs['form_succ_url'];
					$successRedirTime = $opFormRs['form_succ_redir_time'];
					$succesCallBack = '<div style="display:none;" id="callback_lethe"></div>';
					if($successRedirTime>0){
						$succesCallBack = '<div style="display:none;" id="callback_lethe"><script>setTimeout("top.location.href = \''. $successURL .'\'",'. $successRedirTime*1000 .');</script></div>';
						$successText.=$succesCallBack.'<br>'. str_replace('[[x]]',$successRedirTime,lethe_page_will_redirect_in_x_seconds) .' <a href="'. $successURL .'">'. $successURLTitle .'</a>';
					}else{
						$successText.=$succesCallBack.'<br><a href="'. $successURL .'">'. $successURLTitle .'</a>';
					}
				}
				$opForm->free();
			}
		
		}
	
		# ** Group Choose
		if(!isset($_POST['sub_group'])){$_POST['sub_group']=0;$subGroup = 0;}else{
			$qr1 .= ",GID";
			$qr2 .= ",".intval($_POST['sub_group']);
			$subGroup = intval($_POST['sub_group']);
		}
		
		# ** E-Mail
		if(!isset($_POST['sub_mail']) || !mailVal(mysql_prep($_POST['sub_mail']))){$errorz.='* '. lethe_invalid_e_mail_address .'<br>';$errCall='INVALID_MAIL';}else{
			if(!chkData("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE GID=". intval($_POST['sub_group']) ." AND sub_mail='". mysql_prep($_POST['sub_mail']) ."'")){$errorz.='* '. lethe_e_mail_address_already_exists .'<br>';$errCall='MAIL_EXISTS';}else{
				
				# ** Check Blacklist
				if(!$this->admin_area){
					if(cntData("SELECT * FROM ". db_table_pref ."newsletter_blacklist WHERE email='". mysql_prep($_POST['sub_mail']) ."' OR ip_addr='". $_SERVER['REMOTE_ADDR'] ."'")!=0){
						$errorz.='* '. lethe_banned_e_mail .'<br>';
						$errCall='BANNED_MAIL';
					}else{
						$qr1 .= ",sub_mail";
						$qr2 .= ",'". mysql_prep($_POST['sub_mail']) ."'";
					}
				}else{				
					$qr1 .= ",sub_mail";
					$qr2 .= ",'". mysql_prep($_POST['sub_mail']) ."'";
				}
			}
		}
		
		# ** Name
		if(!$this->admin_area){
			if(strpos($field_options,'[lethe_name@required:yes]') !== false){
				if(!isset($_POST['sub_name']) || empty($_POST['sub_name'])){
					$errorz.='* '. lethe_please_enter_a_name .'<br>';
					$errCall='INVALID_NAME';
					}
				else{
					$qr1 .= ",sub_name";
					$qr2 .= ",'". mysql_prep($_POST['sub_name']) ."'";
				}
			}
			else{
				if(isset($_POST['sub_name']) && !empty($_POST['sub_name'])){
					$qr1 .= ",sub_name";
					$qr2 .= ",'". mysql_prep($_POST['sub_name']) ."'";
				}
			}
		}
		else{ # Admins Can Pass Empty This Area
			if(isset($_POST['sub_name']) && !empty($_POST['sub_name'])){
				$qr1 .= ",sub_name";
				$qr2 .= ",'". mysql_prep($_POST['sub_name']) ."'";
			}
		}
		
		# ** Company
		if(!$this->admin_area){
			if(strpos($field_options,'[lethe_company@required:yes]') !== false){
				if(!isset($_POST['sub_comp']) || empty($_POST['sub_comp'])){
					$errorz.='* '. lethe_please_enter_a_company_name .'<br>';
					$errCall='INVALID_COMPANY';
					}
				else{
					$qr1 .= ",sub_company";
					$qr2 .= ",'". mysql_prep($_POST['sub_comp']) ."'";
				}
			}
			else{
				if(isset($_POST['sub_comp']) && !empty($_POST['sub_comp'])){
					$qr1 .= ",sub_company";
					$qr2 .= ",'". mysql_prep($_POST['sub_comp']) ."'";
				}
			}
		}
		else{ # Admins Can Pass Empty This Area
			if(isset($_POST['sub_comp']) && !empty($_POST['sub_comp'])){
				$qr1 .= ",sub_company";
				$qr2 .= ",'". mysql_prep($_POST['sub_comp']) ."'";
			}
		}
		
		# ** Phone
		if(!$this->admin_area){
			if(strpos($field_options,'[lethe_phone@required:yes]') !== false){
				if(!isset($_POST['sub_phone']) || empty($_POST['sub_phone'])){
					$errorz.='* '. lethe_please_enter_a_phone .'<br>';
					$errCall='INVALID_PHONE';
					}
				else{
					$qr1 .= ",sub_phone";
					$qr2 .= ",'". mysql_prep($_POST['sub_phone']) ."'";
				}
			}
			else{
				if(isset($_POST['sub_phone']) && !empty($_POST['sub_phone'])){
					$qr1 .= ",sub_phone";
					$qr2 .= ",'". mysql_prep($_POST['sub_phone']) ."'";
				}
			}
		}
		else{ # Admins Can Pass Empty This Area
			if(isset($_POST['sub_phone']) && !empty($_POST['sub_phone'])){
				$qr1 .= ",sub_phone";
				$qr2 .= ",'". mysql_prep($_POST['sub_phone']) ."'";
			}
		}
		
		# ** Date
		if(!$this->admin_area){
			if(strpos($field_options,'[lethe_date@required:yes]') !== false){
				if(!isset($_POST['sub_date']) || empty($_POST['sub_date'])){
					$errorz.='* '. lethe_please_enter_a_date .'<br>';
					$errCall='INVALID_DATE';
					}
				else{
					$qr1 .= ",sub_date";
					$qr2 .= ",'". mysql_prep(date('Y-m-d H:i:s',strtotime($_POST['sub_date']))) ."'";
				}
			}
			else{
				if(isset($_POST['sub_date']) && !empty($_POST['sub_date'])){
					$qr1 .= ",sub_date";
					$qr2 .= ",'". mysql_prep(date('Y-m-d H:i:s',strtotime($_POST['sub_date']))) ."'";
				}
			}
		}
		else{ # Admins Can Pass Empty This Area
			if(isset($_POST['sub_date']) && !empty($_POST['sub_date'])){
				$qr1 .= ",sub_date";
				$qr2 .= ",'". mysql_prep(date('Y-m-d H:i:s',strtotime($_POST['sub_date']))) ."'";
			}
		}
		
		# ** Select
		if(!$this->admin_area){
			if(strpos($field_options,'[lethe_listbox@required:yes]') !== false){
				if(!isset($_POST['sub_select']) || empty($_POST['sub_select'])){
					$errorz.='* '. lethe_please_choose_a_option .'<br>';
					$errCall='INVALID_SELECTION';
					}
				else{
					$qr1 .= ",sub_select";
					$qr2 .= ",'". mysql_prep($_POST['sub_select']) ."'";
				}
			}
			else{
				if(isset($_POST['sub_select']) && !empty($_POST['sub_select'])){
					$qr1 .= ",sub_select";
					$qr2 .= ",'". mysql_prep($_POST['sub_select']) ."'";
				}
			}
		}
		else{ # Admins Can Pass Empty This Area
			if(isset($_POST['sub_select']) && !empty($_POST['sub_select'])){
				$qr1 .= ",sub_select";
				$qr2 .= ",'". mysql_prep($_POST['sub_select']) ."'";
			}
		}
		
		# ** Captcha
		if(!$this->admin_area){
			if(strpos($field_options,'[captcha@required:yes]') !== false){
			
			  require_once(LETHEPATH.'/recaptchalib.php');
			  $privatekey = "6LdAPPASAAAAAMIfh59QJV-orutJSQxmBZc-NIru";
			  $resp = recaptcha_check_answer ($privatekey,
											$_SERVER["REMOTE_ADDR"],
											$_POST["recaptcha_challenge_field"],
											$_POST["recaptcha_response_field"]);
			
				if (!$resp->is_valid) {$errorz.='* '. lethe_incorrect_verification_code .'<br>';$errCall='INVALID_VERIFY';}
			}
		}
		
		# ** General
			$qr1 .= ",ip_addr";
			$qr2 .= ",'". $_SERVER['REMOTE_ADDR'] ."'";
		
		
		if($errorz==''){
			$myconn->query("INSERT INTO ". db_table_pref ."newsletter_subscribers (". $qr1 .") VALUES (". $qr2 .")") or die(mysqli_error());
			
			# ** Add to Tasks in Which Group Selected for Form
			$prep = $myconn->prepare("INSERT INTO ". db_table_pref ."newsletter_tasks (NID,SID,GID,receiver,pos) VALUES (?,?,?,?,?)");
			$getMyId = getSubscriber($subCode,4);
			
				$results = $myconn->query("SELECT DISTINCT NID,pos,GID FROM ". db_table_pref ."newsletter_tasks WHERE GID=" . $subGroup) or die(mysqli_error());

				while($row = $results->fetch_array()){
					if(cntData("SELECT ID,NID,GID,receiver FROM ". db_table_pref ."newsletter_tasks WHERE NID=". $row['NID'] ." AND GID=". $subGroup ." AND receiver='". mysql_prep($_POST['sub_mail']) ."'")==0){
						$imoMail = $_POST['sub_mail'];
						$prep->bind_param('iiisi', $row['NID'],$getMyId,$row['GID'],$imoMail,$row['pos']);
						$prep->execute();
					}
				}
				$prep->close();
			
			# ** Remove After Form
			if($remove_after==1){
				$myconn->query("DELETE FROM ". db_table_pref ."newsletter_forms WHERE ID=". $subFormID ."") or die(mysqli_error());
			}
			
			# ** Send Activation
			if($this->sendActivation){
				if(!$this->admin_area){ # That's only work for public area
					$this->subscriberName = @$_POST['sub_name'];
					$this->subscriberMail = $_POST['sub_mail'];
					$this->newsletterVerification = $subCode;
					$this->subscriberDetails = getSubscriber($getMyId,5);
					$this->send_verify();
				}
			}
			
			$errorz = '<input type="hidden" id="success_call" value="SUCCESS">';
			$errorz.='<input type="hidden" id="success_reg" value="YES">';
		}else{
			$errorz = '<input type="hidden" id="success_call" value="'. $errCall .'">';
			$errorz.='<input type="hidden" id="success_reg" value="NO">';
		}
		
		$this->errPrint = $errorz;
	
	} # Add Subscriber End **
	
	# **** Edit Subscriber
	function edit_subscriber(){
	
		global $myconn;
		$errorz = '';
		$qr1 = '';
		$qr2 = "";
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
		
		# ** Delete Proccess
		if(isset($_POST['delSub'])){
			$myconn->query("DELETE FROM ". db_table_pref ."newsletter_subscribers WHERE ID=". $this->content_ID ."") or die(mysqli_error());
			$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_d_eleted_successfully .'</div>';
			return false;
		}
	
		# ** Group Choose
		if(!isset($_POST['sub_group'])){$_POST['sub_group']=0;}else{
			$qr1 .= "GID=".intval($_POST['sub_group']);
		}
		
		# ** E-Mail
		if(!isset($_POST['sub_mail']) || !mailVal(mysql_prep($_POST['sub_mail']))){$errorz.='* '. lethe_invalid_e_mail_address .'<br>';}else{
			if(!chkData("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE GID=". intval($_POST['sub_group']) ." AND sub_mail='". mysql_prep($_POST['sub_mail']) ."' AND ID<>". $this->content_ID ."")){$errorz.='* E-Mail Zaten Kayıtlı<br>';}else{
				$qr1 .= ",sub_mail='". mysql_prep($_POST['sub_mail']) ."'";
			}
		}
		
		# ** Name
		if(isset($_POST['sub_name']) && !empty($_POST['sub_name'])){
			$qr1 .= ",sub_name='". mysql_prep($_POST['sub_name']) ."'";
		}
		
		# ** Company
		if(isset($_POST['sub_comp']) && !empty($_POST['sub_comp'])){
			$qr1 .= ",sub_company='". mysql_prep($_POST['sub_comp']) ."'";
		}
		
		# ** Phone
		if(isset($_POST['sub_comp']) && !empty($_POST['sub_comp'])){
			$qr1 .= ",sub_phone='". mysql_prep($_POST['sub_phone']) ."'";
		}
		
		# ** Date
		if(isset($_POST['sub_date']) && !empty($_POST['sub_date'])){
			$subDate = $_POST['sub_date'];
			$subDate = str_replace('/','-',$subDate);
			$qr1 .= ",sub_date='". mysql_prep(date('Y-m-d H:i:s',strtotime($subDate))) ."'";
		}
		
		# ** Activation
		if(isset($_POST['activation']) && !empty($_POST['activation'])){
			$qr1 .= ",activated=1";
		}else{
			$qr1 .= ",activated=0";
		}
		# ** Active
		if(isset($_POST['active']) && !empty($_POST['active'])){
			$qr1 .= ",active=1";
		}else{
			$qr1 .= ",active=0";
		}
		
		
		if($errorz==''){
			$myconn->query("UPDATE ". db_table_pref ."newsletter_subscribers SET ". $qr1 ." WHERE ID=". $this->content_ID ."") or die(mysqli_error());
			
			$errorz = '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_u_pdated_successfully .'</div>';
		}else{
			$errorz = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
		
		$this->errPrint = $errorz;
	
	} # Edit Subscriber End **
	
	# **** Export Subscribers
	function export_subscribers(){
	
		set_time_limit(0);
		global $myconn;
		$errorz = '';
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
		
		# ** Group Choose
		$sub_group_qr = null;
		if(!isset($_POST['sub_group']) || $_POST['sub_group']==0){$errorz.='* '. lethe_please_choose_a_group .'<br>';}else{
			$sub_group = $_POST['sub_group'];
			$sub_group_qr = " AND (";
			foreach ($sub_group as $sg){
				$sub_group_qr .= "GID=". intval($sg) ." OR ";
			}
			$sub_group_qr = substr($sub_group_qr,0,-3).')';
		}
		
		if(!isset($_POST['exp_model']) || $_POST['exp_model']==0){$errorz.='* '. lethe_please_choose_a_export_model .'<br>';}

		
		if($errorz==''){			
		
		
			# ** Create Export Folder If Not Exists
			if(!file_exists(lethe_export_path)){mkdir(lethe_export_path, 0755);}
			
			# ** Design File Content
			$expLine = "";
			$opSubscribers = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE active=1 AND activated=1 ". $sub_group_qr ."") or die(mysqli_error());
			while($opSubscribersRs = $opSubscribers->fetch_assoc()){
				# Model 1 ("Name" <mail@address>,)
				if(intval($_POST['exp_model'])==1){$expLine.= '"'.$opSubscribersRs['sub_name']. '"' . ' <'. $opSubscribersRs['sub_mail'] .'>,';}
				# Model 2 ("Name" <mail@address>;)
				if(intval($_POST['exp_model'])==2){$expLine.= '"'.$opSubscribersRs['sub_name'] . '"' . ' <'. $opSubscribersRs['sub_mail'] .'>;';}
				# Model 3 (<mail@address>,)
				if(intval($_POST['exp_model'])==3){$expLine.= '<'. $opSubscribersRs['sub_mail'] .'>,';}
				# Model 4 (<mail@address>;)
				if(intval($_POST['exp_model'])==4){$expLine.= '<'. $opSubscribersRs['sub_mail'] .'>;';}
				# Model 5 (mail@address,)
				if(intval($_POST['exp_model'])==5){$expLine.= $opSubscribersRs['sub_mail'] .',';}
				# Model 6 (mail@address;)
				if(intval($_POST['exp_model'])==6){$expLine.= $opSubscribersRs['sub_mail'] .';';}
			}
			$expLine = substr($expLine,0,-1);
			
				# Process
					$pathw = lethe_export_path;
					$filez="lethe-". date('d-m-Y') ."-". uniqid() .".txt";
					$pathws = dirname(dirname(__FILE__)) . '/lethe_export';
					if (!file_exists ($pathw . '/' . $filez) ) {
					@touch ($pathw . '/' . $filez);
					}
					$conc=@fopen ($pathw . '/' . $filez,'w');
					if (!$conc) {
					$this->errPrint = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_could_not_write_file .'</div>';
					return false;
					}
					
					#************* Writing *****
					if (fputs ($conc,$expLine) ){
						//$file_url = '/' . lethe_admin_path . '/' . basename(dirname($pathw . '/' . $filez)) . '/' . $filez;
						$file_url = relDocs($pathws . '/' . $filez);
						$errorz = '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_exported_successfully .' '. lethe_click_to_link_for_download .'<br><strong><a href="'. $file_url .'" download>'. $filez .'</a></strong></div>';
					}else {
						$errorz = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_could_not_write_file .'</div>';
					}
					fclose($conc);
					#************* Writing End **
		
		}else{
			$errorz = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
		
		$this->errPrint = $errorz;
	
	} # Export Subscribers End **
	
	# **** Import Custom
	function import_custom(){
	
		set_time_limit(0);
		global $myconn;
		$errorz = '';
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
		
		# ** File content
		if(isset($_FILES['importFile'])){
			$file_name = $_FILES['importFile']['name'];
			$file_size =$_FILES['importFile']['size'];
			$file_tmp =$_FILES['importFile']['tmp_name'];
			$file_type=$_FILES['importFile']['type'];
			$new_filename = "lethe_import_".uniqid();
			
			# ** Check File Details
			
			$file_ext=explode('.',$_FILES['importFile']['name']);
			$file_ext=end($file_ext);
			$file_ext=strtolower(end(explode('.',$_FILES['importFile']['name']))); 
			
			if(in_array($file_ext,$this->import_file_type ) === false){
				$errorz .= '* '. lethe_the_file_type_not_allowed .'<br>';
			} 
			
			if($file_size > 2097152){
				$errorz .= '* '. lethe_the_file_size_is_too_large .'<br>';
			}
			
		}  else {
			$errorz .= '* '. lethe_please_choose_a_file .'<br>';
		}
		
		# ** Group
		if(!isset($_POST['sub_group']) || intval($_POST['sub_group'])==0){
			$errorz .= '* '. lethe_please_choose_a_file .'<br>';
		}
		
		# ** Import model
		if(!isset($_POST['imp_model']) || intval($_POST['imp_model'])==0){
			$errorz .= '* '. lethe_please_choose_a_import_model .'<br>';
		}
		
		if($errorz==''){
			$start_time = microtime(true);
			$pathws = dirname(dirname(__FILE__)) . '/lethe_export';
			$uploaded_file_path = $pathws.'/'.$new_filename.'.'.$file_ext;
			move_uploaded_file($file_tmp,$uploaded_file_path);
			
			# Import Routine
				$f = fopen($uploaded_file_path, "r");
				$t = 0; # Total data counter
				$v = 0; # Success
				$n = 0; # Errors
				$slp = 1;
				$sleepTime = 5; # 5 Second
				$sub_group = intval($_POST['sub_group']);
				$imp_model = intval($_POST['imp_model']);
				$imp_info = "";
				
				# Prepare
				$chkRes = $myconn->prepare("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE GID=? AND sub_mail=?");
				$stmt = $myconn->prepare("INSERT INTO
														". db_table_pref ."newsletter_subscribers 
													(
														GID,sub_name,sub_mail,sub_code,activated,active
													)
											   VALUES
													(
														?,?,?,?,?,?
													)
												  ");
				
				while(!feof($f)) {

					# Data Separator Start
						if($imp_model==1){	# "Name" <mail>,
							$data = explode(",", fgets($f));
							
								foreach( $data as $pair ) {
								  $t++;
								  if( strpos($pair, '<') ) {

									$output = explode( "<", $pair );
									$output[0] = trim( str_replace( "\"", "", $output[0] ) );
									$output[1] = trim( rtrim( $output[1], ">" ) );

								  } else {

									$output = array();
									$output[0] = '';
									$output[1] = $pair;

								  }
								  $o0 = $output[0];
								  $o1 = $output[1];
								  # Insert to database Start
									$chkRes->bind_param("is", $sub_group,$o1);
									$chkRes->execute();
									$chkRes->store_result();
									if(mailVal($output[1]) && $chkRes->num_rows == 0){
										$subCode = md5(uniqid(mt_rand(), true));
										$verif = 1;
										$actv = 1;
										$stmt->bind_param('isssii', $sub_group,
																	$o0,
																	$o1,
																	$subCode,
																	$verif,
																	$actv
															); $stmt->execute();
									$slp++;
									if($slp>=1000){$slp=1;sleep($sleepTime);}
									$v++;
									}else{
									$n++;
									}
								  # Insert to database End
								}

							
						}
						
						else if($imp_model==2){	# "Name" <mail>;
							$data = explode(";", fgets($f));
							
								foreach( $data as $pair ) {
								  $t++;
								  if( strpos($pair, '<') ) {

									$output = explode( "<", $pair );
									$output[0] = trim( str_replace( "\"", "", $output[0] ) );
									$output[1] = trim( rtrim( $output[1], ">" ) );

								  } else {

									$output = array();
									$output[0] = '';
									$output[1] = $pair;

								  }
								  $o0 = $output[0];
								  $o1 = $output[1];
								  # Insert to database Start
									$chkRes->bind_param("is", $sub_group,$o1);
									$chkRes->execute();
									$chkRes->store_result();
									if(mailVal($output[1]) && $chkRes->num_rows == 0){
										$subCode = md5(uniqid(mt_rand(), true));
										$verif = 1;
										$actv = 1;
										$stmt->bind_param('isssii', $sub_group,
																	$o0,
																	$o1,
																	$subCode,
																	$verif,
																	$actv
															); $stmt->execute();
									$slp++;
									if($slp>=1000){$slp=1;sleep($sleepTime);}
									$v++;
									}else{
									$n++;
									}

								  # Insert to database End
								}

							
						}
						
						else if($imp_model==3){	# <mail>,
							$data = explode(",", fgets($f));
							
								foreach( $data as $pair ) {
								  $t++;
								  $pair = str_replace('<','',$pair);
								  $pair = str_replace('>','',$pair);
									$output = array();
									$output[0] = '';
									$output[1] = $pair;
									
								  $o0 = $output[0];
								  $o1 = $output[1];
								  # Insert to database Start
									$chkRes->bind_param("is", $sub_group,$o1);
									$chkRes->execute();
									$chkRes->store_result();
									if(mailVal($output[1]) && $chkRes->num_rows == 0){
										$subCode = md5(uniqid(mt_rand(), true));
										$verif = 1;
										$actv = 1;
										$stmt->bind_param('isssii', $sub_group,
																	$o0,
																	$o1,
																	$subCode,
																	$verif,
																	$actv
															); $stmt->execute();
									$slp++;
									if($slp>=1000){$slp=1;sleep($sleepTime);}
									$v++;
									}else{
									$n++;
									}
								  # Insert to database End
								}

							
						}
						
						else if($imp_model==4){	# <mail>;
							$data = explode(";", fgets($f));
							
								foreach( $data as $pair ) {
								  $t++;
								  $pair = str_replace('<','',$pair);
								  $pair = str_replace('>','',$pair);
									$output = array();
									$output[0] = '';
									$output[1] = $pair;
									
								  $o0 = $output[0];
								  $o1 = $output[1];
								  # Insert to database Start
									$chkRes->bind_param("is", $sub_group,$o1);
									$chkRes->execute();
									$chkRes->store_result();
									if(mailVal($output[1]) && $chkRes->num_rows == 0){
										$subCode = md5(uniqid(mt_rand(), true));
										$verif = 1;
										$actv = 1;
										$stmt->bind_param('isssii', $sub_group,
																	$o0,
																	$o1,
																	$subCode,
																	$verif,
																	$actv
															); $stmt->execute();
									$slp++;
									if($slp>=1000){$slp=1;sleep($sleepTime);}
									$v++;
									}else{
									$n++;
									}
								  # Insert to database End
								}
							
						}
						
						else if($imp_model==5){	# mail,
							$data = explode(",", fgets($f));
							
								foreach( $data as $pair ) {
								  $t++;
								  $pair = str_replace('<','',$pair);
								  $pair = str_replace('>','',$pair);
									$output = array();
									$output[0] = '';
									$output[1] = $pair;
									
								  $o0 = $output[0];
								  $o1 = $output[1];
									
								  # Insert to database Start
									$chkRes->bind_param("is", $sub_group,$o1);
									$chkRes->execute();
									$chkRes->store_result();
									if(mailVal($output[1]) && $chkRes->num_rows == 0){
										$subCode = md5(uniqid(mt_rand(), true));
										$verif = 1;
										$actv = 1;
										$stmt->bind_param('isssii', $sub_group,
																	$o0,
																	$o1,
																	$subCode,
																	$verif,
																	$actv
															); $stmt->execute();
									$slp++;
									if($slp>=1000){$slp=1;sleep($sleepTime);}
									$v++;
									}else{
									$n++;
									}
								  # Insert to database End
								}
							
						}
						
						else if($imp_model==6){	# mail;
							$data = explode(";", fgets($f));
							
								foreach( $data as $pair ) {
								  $t++;
								  $pair = str_replace('<','',$pair);
								  $pair = str_replace('>','',$pair);
									$output = array();
									$output[0] = '';
									$output[1] = $pair;
									
								  $o0 = $output[0];
								  $o1 = $output[1];
									
								  # Insert to database Start
									$chkRes->bind_param("is", $sub_group,$o1);
									$chkRes->execute();
									$chkRes->store_result();
									if(mailVal($output[1]) && $chkRes->num_rows == 0){
										$subCode = md5(uniqid(mt_rand(), true));
										$verif = 1;
										$actv = 1;
										$stmt->bind_param('isssii', $sub_group,
																	$o0,
																	$o1,
																	$subCode,
																	$verif,
																	$actv
															); $stmt->execute();
									$slp++;
									if($slp>=1000){$slp=1;sleep($sleepTime);}
									$v++;
									}else{
									$n++;
									}
								  # Insert to database End
								}

							
						}
						else if($imp_model==7){	# mail{line_break}
						
							$data = fgets($f);
							$data = trim($data,"\n");
							$data = rtrim($data);
							if(substr($data,-1,1)==' '){$data = substr($data,0,-1);}
							$data = preg_replace('#\s+#',',',trim($data));
							$data = explode(",", $data);
							$errLister = '';
							
								foreach( $data as $pair ) {
								  $t++;
								  $pair = str_replace('<','',$pair);
								  $pair = str_replace('>','',$pair);
									$output = array();
									$output[0] = '';
									$output[1] = $pair;
									
								  $o0 = $output[0];
								  $o1 = $output[1];
									
								  # Insert to database Start
									$chkRes->bind_param("is", $sub_group,$o1);
									$chkRes->execute();
									$chkRes->store_result();
									if(mailVal($output[1]) && $chkRes->num_rows == 0){
										$subCode = md5(uniqid(mt_rand(), true));
										$verif = 1;
										$actv = 1;
										$stmt->bind_param('isssii', $sub_group,
																	$o0,
																	$o1,
																	$subCode,
																	$verif,
																	$actv
															); $stmt->execute();
									$slp++;
									if($slp>=1000){$slp=1;sleep($sleepTime);}
									$v++;
									}else{
									$n++;
									$errLister.= @$output[1];
									}
								  # Insert to database End
								}

							
						}
					
					# Data Separator End
					
					
				} 
								$stmt->close();
								$chkRes->close();
				fclose($f);
				$imp_info = "". lethe_total .": ". $t ." ~ ". lethe_success .": ". $v ." ~ ". lethe_errors .": ". $n;
			# Import End
			$endTime = number_format(microtime(true) - $start_time, 2);
			$errorz = '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_emails_imported_successfully .'<br>'. $imp_info .'<br>'. lethe_time .': '. $endTime .' '. lethe_sec .'</div>';
		}else{
			$errorz = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
		$this->errPrint = $errorz;
	
	} # Import Custom End **
	
	# **** Import Wordpress
	function import_wordpress(){
	
		global $myconn;
		$errorz = '';
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
		
		# Group
		if(!isset($_POST['sub_group']) || intval($_POST['sub_group'])==0){$errorz.='* '. lethe_please_choose_a_group .'<br>';}
		
		# DB Host Name
		if(!isset($_POST['db_host']) || empty($_POST['db_host'])){$errorz.='* '. lethe_please_enter_a_db_host_address .'<br>';}
		
		# DB Name
		if(!isset($_POST['db_name']) || empty($_POST['db_name'])){$errorz.='* '. lethe_please_enter_a_db_name .'<br>';}
		
		# DB Username
		if(!isset($_POST['db_user']) || empty($_POST['db_user'])){$errorz.='* '. lethe_please_enter_a_db_username .'<br>';}
		
		# DB Password
		// if(!isset($_POST['db_pass']) || empty($_POST['db_pass'])){$errorz.='* '. lethe_please_enter_a_db_password .'<br>';} # Remove // strings if you are not working on localhost
		
		# DB Table Prefix
		if(!isset($_POST['db_table_pref']) || empty($_POST['db_table_pref'])){$errorz.='* '. lethe_please_enter_a_db_table_prefix .'<br>';}
		
		# DB Connection
		error_reporting(0);
		$myconn2=new mysqli(@$_POST['db_host'],@$_POST['db_user'],@$_POST['db_pass'],@$_POST['db_name']);
		
		if($myconn2->connect_error) {
			$errorz.= lethe_cannot_connect_to_database.": (" . $myconn2->connect_errno . ") " . $myconn2->connect_error;
		}else{
		$myconn2->set_charset('utf8');
		$myconn2->query("SELECT * FROM ". mysql_prep($_POST['db_table_pref']) ."users") or $errorz.='* '. lethe_cannot_open_e_mail_table .'<br>';
		}
		
		if($errorz==''){
		
			# Get emails
			$t = 0;
			$v = 0;
			$n = 0;
			$slp = 1;
			$sleepTime = 5; # 5 Second
			$imp_info = "";
			$sub_group = intval($_POST['sub_group']);
			
				# Prepare
				$chkRes = $myconn->prepare("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE GID=? AND sub_mail=?");
				$stmt = $myconn->prepare("INSERT INTO
														". db_table_pref ."newsletter_subscribers 
													(
														GID,sub_name,sub_mail,sub_code,activated,active
													)
											   VALUES
													(
														?,?,?,?,?,?
													)
												  ");
			
			$opUsrTbl = $myconn2->query("SELECT * FROM ". mysql_prep($_POST['db_table_pref']) ."users") or die(mysqli_error());
			while($opUsrTblRs = $opUsrTbl->fetch_assoc()){
				$t++;
								  # Insert to database Start
									$chkRes->bind_param("is", $sub_group,mysql_prep($opUsrTblRs['user_email']));
									$chkRes->execute();
									$chkRes->store_result();
									if(mailVal(mysql_prep($opUsrTblRs['user_email'])) && $chkRes->num_rows == 0){
										$subCode = md5(uniqid(mt_rand(), true));
										$verif = 1;
										$actv = 1;
										$stmt->bind_param('isssii', $sub_group,
																	mysql_prep($opUsrTblRs['display_name']),
																	mysql_prep($opUsrTblRs['user_email']),
																	$subCode,
																	$verif,
																	$actv
															); $stmt->execute();
									$slp++;
									if($slp>=1000){$slp=1;sleep($sleepTime);}

									$v++;
									}else{
									$n++;
									}
								  # Insert to database End				
			}
			$stmt->close();
			$chkRes->close();
			$imp_info = "". lethe_total .": ". $t ." ~ ". lethe_success .": ". $v ." ~ ". lethe_errors .": ". $n ."";
			$errorz = '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_emails_imported_successfully .'<br>'. $imp_info .'</div>';
			$opUsrTbl->free();
			$myconn2->close();
		}else{
			$errorz = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
		
		$this->errPrint = $errorz;
	}
	
	# **** Import Custom CMS
	function import_custom_cms(){
	
		global $myconn;
		$errorz = '';
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
		
		# Group
		if(!isset($_POST['sub_group']) || intval($_POST['sub_group'])==0){$errorz.='* '. lethe_please_choose_a_group .'<br>';}
		
		# DB Host Name
		if(!isset($_POST['db_host']) || empty($_POST['db_host'])){$errorz.='* '. lethe_please_enter_a_db_host_address .'<br>';}
		
		# DB Name
		if(!isset($_POST['db_name']) || empty($_POST['db_name'])){$errorz.='* '. lethe_please_enter_a_db_name .'<br>';}
		
		# DB Username
		if(!isset($_POST['db_user']) || empty($_POST['db_user'])){$errorz.='* '. lethe_please_enter_a_db_username .'<br>';}
		
		# DB Password
		// if(!isset($_POST['db_pass']) || empty($_POST['db_pass'])){$errorz.='* '. lethe_please_enter_a_db_password .'<br>';} # Remove // strings if you are not working on localhost
		
		# DB Table Name
		if(!isset($_POST['db_table_name']) || empty($_POST['db_table_name'])){$errorz.='* '. lethe_please_enter_a_db_table_name .'<br>';}
		
		# DB Name Field
		if(!isset($_POST['db_name_field'])){$_POST['db_name_field']=null;}
		
		# DB Mail Field
		if(!isset($_POST['db_mail_field']) || empty($_POST['db_mail_field'])){$errorz.='* '. lethe_please_enter_a_email_field_name .'<br>';}
		
		# DB Connection
		error_reporting(0);
		$myconn2=new mysqli(@$_POST['db_host'],@$_POST['db_user'],@$_POST['db_pass'],@$_POST['db_name']);
		
		if($myconn2->connect_error) {
			$errorz.= lethe_cannot_connect_to_database.": (" . $myconn2->connect_errno . ") " . $myconn2->connect_error;
		}else{
		$myconn2->set_charset('utf8');
		$myconn2->query("SELECT * FROM ". mysql_prep($_POST['db_table_name']) ."") or $errorz.='* '. lethe_cannot_open_e_mail_table .'<br>';
		}
		
		if($errorz==''){
		
				# Prepare
				$chkRes = $myconn->prepare("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE GID=? AND sub_mail=?");
				$stmt = $myconn->prepare("INSERT INTO
														". db_table_pref ."newsletter_subscribers 
													(
														GID,sub_name,sub_mail,sub_code,activated,active
													)
											   VALUES
													(
														?,?,?,?,?,?
													)
												  ");
		
			# Get emails
			$t = 0;
			$v = 0;
			$n = 0;
			$slp = 1;
			$sleepTime = 5; # 5 Second
			$imp_info = "";
			$sub_group = intval($_POST['sub_group']);
			
			$opUsrTbl = $myconn2->query("SELECT * FROM ". mysql_prep($_POST['db_table_name']) ."") or die(mysqli_error());
			while($opUsrTblRs = $opUsrTbl->fetch_assoc()){
				$t++;
				
								  # Insert to database Start
									$chkRes->bind_param("is", $sub_group,mysql_prep(@$opUsrTblRs[$_POST['db_mail_field']]));
									$chkRes->execute();
									$chkRes->store_result();
									if(mailVal(mysql_prep(@$opUsrTblRs[$_POST['db_mail_field']])) && $chkRes->num_rows == 0){
										$subCode = md5(uniqid(mt_rand(), true));
										$verif = 1;
										$actv = 1;
										$stmt->bind_param('isssii', $sub_group,
																	mysql_prep(@$opUsrTblRs[$_POST['db_name_field']]),
																	mysql_prep(@$opUsrTblRs[$_POST['db_mail_field']]),
																	$subCode,
																	$verif,
																	$actv
															); $stmt->execute();
									$slp++;
									if($slp>=1000){$slp=1;sleep($sleepTime);}

									$v++;
									}else{
									$n++;
									}
								  # Insert to database End				
			}
			
			$imp_info = "". lethe_total .": ". $t ." ~ ". lethe_success .": ". $v ." ~ ". lethe_errors .": ". $n ."";
			$errorz = '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_emails_imported_successfully .'<br>'. $imp_info .'</div>';
			$opUsrTbl->free();
			$myconn2->close();
		}else{
			$errorz = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
		
		$this->errPrint = $errorz;
	}
	
	# **** Add Newsletter
	function add_newsletter(){
	
		global $myconn;
		$errorz = '';
		$subCode = md5(uniqid(mt_rand(), true));
		$qr1 = 'newsletter_id';
		$qr2 = "'". $subCode ."'";
		$catch_groups = "active=". intval(set_only_active) ." AND activated=". intval(set_only_verified) ."";
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
		
		# ** Web View
		if(!isset($_POST['onweb']) || empty($_POST['onweb'])){
			$qr1 .= ",web_view";
			$qr2 .= ",0";		
		}else{
			$qr1 .= ",web_view";
			$qr2 .= ",1";
		}
	
		# ** Submission Account
		if(!isset($_POST['sub_account']) || intval($_POST['sub_account'])==0){$errorz.='* '. lethe_please_choose_submission_account .'<br>';$group_lists=null;}else{
			$qr1 .= ",SUID";
			$qr2 .= ",".intval($_POST['sub_account']);
		}

		# ** Group Choose
		if(!isset($_POST['sub_group'])){$errorz.='* '. lethe_please_choose_a_group .'<br>';}else{
			$sub_group = $_POST['sub_group'];
			$catch_groups .= " AND (";
			$group_lists = '';
			foreach ($sub_group as $sg){
				$catch_groups .= "GID=". intval($sg) ." OR ";
				$group_lists .= intval($sg).',';
			}
			$catch_groups = substr($catch_groups,0,-3).')';
			$group_lists = substr($group_lists,0,-1);
			
		# Groups
			$qr1 .= ",groups";
			$qr2 .= ",'". $group_lists ."'";
			
		}
		
		# ** Subject
		if(!isset($_POST['subject']) || empty($_POST['subject'])){$errorz.='* '. lethe_please_enter_subject .'<br>';}else{
				$qr1 .= ",subject";
				$qr2 .= ",'". mysql_prep($_POST['subject']) ."'";
		}
		
		# ** Launch Date
		if(!isset($_POST['launch_date'])){$_POST['launch_date']='00-00-0000';}
		if(!isset($_POST['launch_date_h'])){$_POST['launch_date_h']='00';}
		if(!isset($_POST['launch_date_m'])){$_POST['launch_date_m']='00';}
		$rdyDate = $_POST['launch_date'] . ' ' . $_POST['launch_date_h'] . ':' . $_POST['launch_date_m'] . ':00';
		$rdyDate = date("Y-m-d H:i:s",strtotime($rdyDate));

		if(!validateMysqlDate($rdyDate) || $rdyDate<date("Y-m-d H:i:s")){$errorz.='* '. lethe_invalid_launch_date .'<br>';}else{
			$qr1 .= ",launch_date";
			$qr2 .= ",'". mysql_prep($rdyDate) ."'";
		}
		
		# ** Priority
		if(isset($_POST['importance']) && !empty($_POST['importance'])){
			$qr1 .= ",priotity";$qr2 .= ",1";
		}else{$qr1 .= ",priotity";$qr2 .= ",3";}
		
		# ** Details
		if(!isset($_POST['details']) || empty($_POST['details'])){$errorz.='* '. lethe_please_enter_details .'<br>';}else{
			$qr1 .= ",details";
			$qr2 .= ",'". mysql_prep3($_POST['details']) ."'";
		}
		
		# ** Attach File
		if(!isset($_POST['attach_file']) || empty($_POST['attach_file'])){
			$qr1 .= ",file_url";
			$qr2 .= ",''";
		}else{
			$qr1 .= ",file_url";
			$qr2 .= ",'". mysql_prep($_POST['attach_file']) ."'";
		}
		
			
		# User
			$qr1 .= ",UID";
			$qr2 .= "," . admin_ID;		
		
		
		if($errorz==''){			
			# Save Newsletter
			$myconn->query("INSERT INTO ". db_table_pref ."newsletters (". $qr1 .") VALUES (". $qr2 .")") or die(mysqli_error());
			
			# Load Tasks
			if(set_random_load==1){$qrOpt = "ORDER BY RAND()";}else{$qrOpt = "";} # Load Random or Ordered Mails
			
				# Prepare
				$stmt = $myconn->prepare("INSERT INTO
														". db_table_pref ."newsletter_tasks 
													(
														NID,SID,GID,receiver,pos
													)
											   VALUES
													(
														?,?,?,?,?
													)
												  ");
			
			$myCurrentData = intval(getNewsletter($subCode,0));
			$opGroups = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE ". $catch_groups ." ". $qrOpt ."") or die(mysqli_error());
			while($opGroupsRs = $opGroups->fetch_assoc()){
				if(cntData("SELECT NID,receiver FROM ". db_table_pref ."newsletter_tasks WHERE NID=". $myCurrentData ." AND receiver='". $opGroupsRs['sub_mail'] ."'")==0){
				
					$nPos = 0;
					
								  # Insert to database Start

										$stmt->bind_param('iiisi', $myCurrentData,
																   $opGroupsRs['ID'],
																   $opGroupsRs['GID'],
																   $opGroupsRs['sub_mail'],
																   $nPos

															); $stmt->execute();
				}
			}
			$stmt->close();
			
			# Update Newsletter
			$totalTask = mysqli_num_rows($opGroups);
			$myconn->query("UPDATE ". db_table_pref ."newsletters SET total_subscriber=". $totalTask .",position=1 WHERE ID=". $myCurrentData ."") or die(mysqli_error());
			$opGroups->free();
			unset($_POST);
			$errorz = '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_recorded_successfully . '</div>';
		}else{
			$errorz = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
		
		$this->errPrint = $errorz;
	
	} # Add Newsletter End **

	# **** Edit Newsletter
	function edit_newsletter(){
	
		global $myconn;
		$errorz = '';
		$qr1 = '';
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
	
		# ** Delete Operation
		if(isset($_POST['del']) && !empty($_POST['del'])){
			$myconn->query("DELETE FROM ". db_table_pref ."newsletters WHERE ID=". $this->content_ID ."");
			$myconn->query("DELETE FROM ". db_table_pref ."newsletter_tasks WHERE NID=". $this->content_ID ."");
			$errorz = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_d_eleted_successfully . '</div>';
			header('Location: ?pos='. @$_GET['pos'] .'&amp;ppos='. @$_GET['ppos'] .'');
			return false;
		}
	
		# ** Submission Account
		if(!isset($_POST['sub_account']) || intval($_POST['sub_account'])==0){$errorz.='* '. lethe_please_choose_submission_account .'<br>';}else{
			$qr1 .= "SUID=".intval($_POST['sub_account']);
		}
		
		# ** Web View
		if(!isset($_POST['onweb']) || empty($_POST['onweb'])){
			$qr1 .= ",web_view=0";
		}else{
			$qr1 .= ",web_view=1";
		}

		# ** Group Choose
		if(!isset($_POST['sub_group2'])){$_POST['sub_group2']=null;}
		
		if(!isset($_POST['sub_group'])){$errorz.='* '. lethe_please_choose_a_group .'<br>';}else{
			$sub_group = $_POST['sub_group'];
			$sub_group2 = $_POST['sub_group2']; # Deselected Groups
			$catch_groups = " (";
			$group_lists = '';
			$catch_ugroups = " AND (";
			$group_ulists = '';
			foreach ($sub_group as $sg){
				$catch_groups .= "GID=". intval($sg) ." OR ";
				$group_lists .= intval($sg).',';
			}
			
			if(!is_null($_POST['sub_group2'])){
				foreach ($sub_group2 as $sd){ # Catch for delete unselected groups
					$catch_ugroups .= "GID=". intval($sd) ." OR ";
					$group_ulists .= intval($sd).',';
				}
				# Deselected
				$catch_ugroups = substr($catch_ugroups,0,-3).')';
				$group_ulists = substr($group_ulists,0,-1);
			}else{
				# Deselected
				$catch_ugroups = '';
				$group_ulists = '';
			}
			
			# Selected
			$catch_groups = substr($catch_groups,0,-3).')';
			$group_lists = substr($group_lists,0,-1);
			
		}
		
		# ** Subject
		if(!isset($_POST['subject']) || empty($_POST['subject'])){$errorz.='* '. lethe_please_enter_subject .'<br>';}else{
				$qr1 .= ",subject='". mysql_prep($_POST['subject']) ."'";
		}
		
		# ** Launch Date
		if(!isset($_POST['launch_date'])){$_POST['launch_date']='00-00-0000';}
		if(!isset($_POST['launch_date_h'])){$_POST['launch_date_h']='00';}
		if(!isset($_POST['launch_date_m'])){$_POST['launch_date_m']='00';}
		$rdyDate = $_POST['launch_date'] . ' ' . $_POST['launch_date_h'] . ':' . $_POST['launch_date_m'] . ':00';
		$rdyDate = date("Y-m-d H:i:s",strtotime($rdyDate));

		 if(!validateMysqlDate($rdyDate)){$errorz.='* '. lethe_invalid_launch_date .'<br>';}else{
			 $qr1 .= ",launch_date='". mysql_prep($rdyDate) ."'";
		 }
		
		# ** Newsletter Status
		if(isset($_POST['newsletter_proc']) && !is_numeric($_POST['newsletter_proc'])){
			# Default value
		}else{$qr1 .= ",position=" . intval($_POST['newsletter_proc']);}
		
		# ** Priority
		if(isset($_POST['importance']) && !empty($_POST['importance'])){
			$qr1 .= ",priotity=1";
		}else{$qr1 .= ",priotity=3";}
		
		# ** Details
		if(!isset($_POST['details']) || empty($_POST['details'])){$errorz.='* '. lethe_please_enter_details .'<br>';}else{
			$qr1 .= ",details='". mysql_prep3($_POST['details']) ."'";
		}
		
		# ** Attach File
		if(!isset($_POST['attach_file']) || empty($_POST['attach_file'])){
			$qr1 .= ",file_url=''";
		}else{
			$qr1 .= ",file_url='". mysql_prep($_POST['attach_file']) ."'";
		}
		
		# Groups
		$qr1 .= ",groups='". $group_lists ."'";
		
		
		if($errorz==''){
			
			# Delete Before Unselected List
			if(!empty($catch_ugroups)){
				$myconn->query("DELETE FROM ". db_table_pref ."newsletter_tasks WHERE pos=0 AND NID=". $this->content_ID ." ". $catch_ugroups ."") or die(mysqli_error());
			}
			
			# Load Tasks
			if(set_random_load==1){$qrOpt = "ORDER BY RAND()";}else{$qrOpt = "";} # Load Random or Ordered Mails
			
				# Prepare
				$stmt = $myconn->prepare("INSERT INTO
														". db_table_pref ."newsletter_tasks 
													(
														NID,SID,GID,receiver
													)
											   VALUES
													(
														?,?,?,?
													)
												  ");
			
			$opGroups = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE ". $catch_groups ." ". $qrOpt ."") or die(mysqli_error());
			while($opGroupsRs = $opGroups->fetch_assoc()){
				if(cntData("SELECT NID,receiver FROM ". db_table_pref ."newsletter_tasks WHERE NID=". $this->content_ID ." AND receiver='". $opGroupsRs['sub_mail'] ."'")==0){
				
								  # Insert to database Start

										$stmt->bind_param('iiis', $this->content_ID,
																   $opGroupsRs['ID'],
																   $opGroupsRs['GID'],
																   $opGroupsRs['sub_mail']

															); $stmt->execute();
				}
			}
			$stmt->close();
			$opGroups->free();
			# Save Newsletter
			$myconn->query("UPDATE ". db_table_pref ."newsletters SET ". $qr1 ." WHERE ID=". $this->content_ID ."") or die(mysqli_error());
			
			# Reset If Field Checked
			if(isset($_POST['res']) && $_POST['res']=='YES'){
				$myconn->query("UPDATE ". db_table_pref ."newsletters SET position=1,view_hit=0,click_hit=0,bounces=0 WHERE ID=". $this->content_ID ."") or die(mysqli_error());
				$myconn->query("UPDATE ". db_table_pref ."newsletter_tasks SET sent=0 WHERE NID=". $this->content_ID ."") or die(mysqli_error());
			}
			
			$errorz = '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_u_pdated_successfully . '</div>';
		}else{
			$errorz = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
		
		$this->errPrint = $errorz;
	
	} # Edit Newsletter End **
	
	# **** Add Autoresponder
	function add_autoresponder(){
	
		global $myconn;
		$errorz = '';
		$subCode = md5(uniqid(mt_rand(), true));
		$qr1 = 'newsletter_id,data_mode';
		$qr2 = "'". $subCode ."',1";
		$catch_groups = "active=". intval(set_only_active) ." AND activated=". intval(set_only_verified) ."";
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
	
		# ** Submission Account
		if(!isset($_POST['sub_account']) || intval($_POST['sub_account'])==0){$errorz.='* '. lethe_please_choose_submission_account .'<br>';}else{
			$qr1 .= ",SUID";
			$qr2 .= ",".intval($_POST['sub_account']);
		}
		
		# ** Web View
		if(!isset($_POST['onweb']) || empty($_POST['onweb'])){
			$qr1 .= ",web_view";
			$qr2 .= ",0";		
		}else{
			$qr1 .= ",web_view";
			$qr2 .= ",1";
		}

		# ** Group Choose
		if(!isset($_POST['sub_group'])){$errorz.='* '. lethe_please_choose_a_group .'<br>';}else{
			$sub_group = $_POST['sub_group'];
			$catch_groups .= " AND (";
			$group_lists = '';
			foreach ($sub_group as $sg){
				$catch_groups .= "GID=". intval($sg) ." OR ";
				$group_lists .= intval($sg).',';
			}
			$catch_groups = substr($catch_groups,0,-3).')';
			$group_lists = substr($group_lists,0,-1);
			
		# Groups
			$qr1 .= ",groups";
			$qr2 .= ",'". @$group_lists ."'";
		}
		
		# ** Subject
		if(!isset($_POST['subject']) || empty($_POST['subject'])){$errorz.='* '. lethe_please_enter_subject .'<br>';}else{
				$qr1 .= ",subject";
				$qr2 .= ",'". mysql_prep($_POST['subject']) ."'";
		}
		
		# Actions ******************
		
		if(!isset($_POST['ar_action']) || !is_numeric($_POST['ar_action'])){$errorz.='* '. lethe_please_choose_an_action .'<br>';}else{
		
			# After Subscription
			if($_POST['ar_action']==0){
				if(!isset($_POST['ar_0_time']) || !is_numeric($_POST['ar_0_time'])){$errorz.='* '. lethe_please_enter_a_value .'<br>';}else{
					$qr1 .= ",ar_mode,ar_mode_time,ar_mode_date";
					$qr2 .= ",0,". intval($_POST['ar_0_time']) .",". intval($_POST['ar_0_date']) ."";
				}
			}
			
			# After Unsubscription
			else if($_POST['ar_action']==1){
				if(!isset($_POST['ar_1_time']) || !is_numeric($_POST['ar_1_time'])){$errorz.='* '. lethe_please_enter_a_value .'<br>';}else{
					$qr1 .= ",ar_mode,ar_mode_time,ar_mode_date";
					$qr2 .= ",1,". intval($_POST['ar_1_time']) .",". intval($_POST['ar_1_date']) ."";
				}
			}
			
			# Specific Date
			else if($_POST['ar_action']==2){
			
					$qr1 .= ",ar_mode";
					$qr2 .= ",2";

				# ** Launch Date (Required)
				if(!isset($_POST['launch_date'])){$_POST['launch_date']='00-00-0000';}
				if(!isset($_POST['launch_date_h'])){$_POST['launch_date_h']='00';}
				if(!isset($_POST['launch_date_m'])){$_POST['launch_date_m']='00';}
				$rdyDate = $_POST['launch_date'] . ' ' . $_POST['launch_date_h'] . ':' . $_POST['launch_date_m'] . ':00';
				$rdyDate = date("Y-m-d H:i:s",strtotime($rdyDate));

				if(!validateMysqlDate($rdyDate) || $rdyDate<date("Y-m-d H:i:s")){$errorz.='* '. lethe_invalid_launch_date .'<br>';}else{
					$qr1 .= ",launch_date";
					$qr2 .= ",'". mysql_prep($rdyDate) ."'";
				}
				
				# ** Finish Date
				if(!isset($_POST['finish_date'])){$_POST['finish_date']='00-00-0000';}
				if(!isset($_POST['finish_date_h'])){$_POST['finish_date_h']='00';}
				if(!isset($_POST['finish_date_m'])){$_POST['finish_date_m']='00';}
				$rdyDate = $_POST['finish_date'] . ' ' . $_POST['finish_date_h'] . ':' . $_POST['finish_date_m'] . ':00';
				$rdyDate = date("Y-m-d H:i:s",strtotime($rdyDate));
				
				# Check date if end campaign active
				if(isset($_POST['end_campaign']) && $_POST['end_campaign']=="YES"){
					if(!validateMysqlDate($rdyDate) || $rdyDate<date("Y-m-d H:i:s")){$errorz.='* '. lethe_invalid_finish_date .'<br>';}else{
						$qr1 .= ",finish_date,end_camp";
						$qr2 .= ",'". mysql_prep($rdyDate) ."',1";
					}
				}else{
					if(!validateMysqlDate($rdyDate) || $rdyDate<date("Y-m-d H:i:s")){$errorz.='* '. lethe_invalid_finish_date .'<br>';}else{
						$qr1 .= ",finish_date";
						$qr2 .= ",'". mysql_prep($rdyDate) ."'";
					}
					if(!isset($_POST['ar_2_time']) || !is_numeric($_POST['ar_2_time'])){$errorz.='* '. lethe_please_enter_a_value .'<br>';}else{
						$qr1 .= ",ar_mode_time,ar_mode_date,end_camp";
						$qr2 .= ",". intval($_POST['ar_2_time']) .",". intval($_POST['ar_2_date']) .",0";
					}
				}
				
				
				# Weekdays
				$err_week = 0;
				for($i=0;$i<=6;$i++){
					if(isset($_POST['ar_weeks_'. $i]) && $_POST['ar_weeks_'. $i]=="YES"){
						$qr1 .= ",ar_week_" . $i;
						$qr2 .= ",1";
						$err_week++;
					}else{
						$qr1 .= ",ar_week_" . $i;
						$qr2 .= ",0";					
					}
				}
				if($err_week==0){$errorz.='* '. lethe_please_choose_a_weekday .'<br>';}
			
			}
			
			# Special Days
			else if($_POST['ar_action']==3){
				if(!isset($_POST['ar_3_time']) || !is_numeric($_POST['ar_3_time'])){$errorz.='* '. lethe_please_enter_a_value .'<br>';}else{
					$qr1 .= ",ar_mode,ar_mode_time,ar_mode_date";
					$qr2 .= ",3,". intval($_POST['ar_3_time']) .",". intval($_POST['ar_3_date']) ."";
				}
			}
		
		}
		
		# Actions End ******************
		
		# ** Priority
		if(isset($_POST['importance']) && !empty($_POST['importance'])){
			$qr1 .= ",priotity";$qr2 .= ",1";
		}else{$qr1 .= ",priotity";$qr2 .= ",3";}
		
		# ** Details
		if(!isset($_POST['details']) || empty($_POST['details'])){$errorz.='* '. lethe_please_enter_details .'<br>';}else{
			$qr1 .= ",details";
			$qr2 .= ",'". mysql_prep3($_POST['details']) ."'";
		}
		
		# ** Attach File
		if(!isset($_POST['attach_file']) || empty($_POST['attach_file'])){
			$qr1 .= ",file_url";
			$qr2 .= ",''";
		}else{
			$qr1 .= ",file_url";
			$qr2 .= ",'". mysql_prep($_POST['attach_file']) ."'";
		}
			
		# User
			$qr1 .= ",UID";
			$qr2 .= "," . admin_ID;		
		
		
		if($errorz==''){			
			# Save Autoresponder
			$myconn->query("INSERT INTO ". db_table_pref ."newsletters (". $qr1 .") VALUES (". $qr2 .")") or die(mysqli_error());
			$myCurrentData = intval(getNewsletter($subCode,0));
			
			if($_POST['ar_action']==2 || $_POST['ar_action']==3){ # Only Specific Dates & Special Days Needed Loaded Tasks
				# Load Tasks
				if(set_random_load==1){$qrOpt = "ORDER BY RAND()";}else{$qrOpt = "";} # Load Random or Ordered Mails
				
				# Prepare
				$stmt = $myconn->prepare("INSERT INTO
														". db_table_pref ."newsletter_tasks 
													(
														NID,SID,GID,receiver,pos
													)
											   VALUES
													(
														?,?,?,?,?
													)
												  ");
				
				$opGroups = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE ". $catch_groups ." ". $qrOpt ."") or die(mysqli_error());
				$date_parser = array('MINUTE','HOUR','DAY','WEEK','MONTH','YEAR');
				while($opGroupsRs = $opGroups->fetch_assoc()){
					if(cntData("SELECT NID,receiver FROM ". db_table_pref ."newsletter_tasks WHERE receiver='". $opGroupsRs['sub_mail'] ."' AND NID=". $myCurrentData ." AND pos=1")==0){

						$nPos = 1;
								  # Insert to database Start

										$stmt->bind_param('iiisi', $myCurrentData,
																   $opGroupsRs['ID'],
																   $opGroupsRs['GID'],
																   $opGroupsRs['sub_mail'],
																   $nPos
																   

															); $stmt->execute();
					}
				}
				# Update Autoresponder
				$stmt->close();
				$opGroups->free();
			}
			
			# Update Autoresponder
			$myconn->query("UPDATE ". db_table_pref ."newsletters SET position=1 WHERE ID=". $myCurrentData ."") or die(mysqli_error());
			$errorz = '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_recorded_successfully . '</div>';
			unset($_POST);
			$_POST = array();
		}else{
			$errorz = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
		
		$this->errPrint = $errorz;
	
	} # Add Autoresponder End **

	# **** Edit Autoresponder
	function edit_autoresponder(){
	
		global $myconn;
		$errorz = '';
		$qr1 = '';
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End
	
		# ** Delete Operation
		if(isset($_POST['del']) && !empty($_POST['del'])){
			$myconn->query("DELETE FROM ". db_table_pref ."newsletters WHERE ID=". $this->content_ID ."");
			$myconn->query("DELETE FROM ". db_table_pref ."newsletter_tasks WHERE NID=". $this->content_ID ."");
			$errorz = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_d_eleted_successfully . '</div>';
			header('Location: ?pos='. @$_GET['pos'] .'&amp;ppos='. @$_GET['ppos'] .'');
			return false;
		}
	
		# ** Submission Account
		if(!isset($_POST['sub_account']) || intval($_POST['sub_account'])==0){$errorz.='* '. lethe_please_choose_submission_account .'<br>';}else{
			$qr1 .= "SUID=".intval($_POST['sub_account']);
		}
		
		# ** Web View
		if(!isset($_POST['onweb']) || empty($_POST['onweb'])){
			$qr1 .= ",web_view=0";
		}else{
			$qr1 .= ",web_view=1";
		}

		if(!isset($_POST['sub_group2'])){$_POST['sub_group2']=null;}
		
		# ** Group Choose
		if(!isset($_POST['sub_group'])){$errorz.='* '. lethe_please_choose_a_group .'<br>';}else{
			$sub_group = $_POST['sub_group'];
			$sub_group2 = $_POST['sub_group2']; # Deselected Groups
			$catch_groups = " (";
			$group_lists = '';
			$catch_ugroups = " AND (";
			$group_ulists = '';
			foreach ($sub_group as $sg){
				$catch_groups .= "GID=". intval($sg) ." OR ";
				$group_lists .= intval($sg).',';
			}
			
			if(!is_null($_POST['sub_group2'])){
				foreach ($sub_group2 as $sd){ # Catch for delete unselected groups
					$catch_ugroups .= "GID=". intval($sd) ." OR ";
					$group_ulists .= intval($sd).',';
				}
				# Deselected
				$catch_ugroups = substr($catch_ugroups,0,-3).')';
				$group_ulists = substr($group_ulists,0,-1);
			}else{
				# Deselected
				$catch_ugroups = '';
				$group_ulists = '';
			}
			
			# Selected
			$catch_groups = substr($catch_groups,0,-3).')';
			$group_lists = substr($group_lists,0,-1);
			
		}
		
		# ** Subject
		if(!isset($_POST['subject']) || empty($_POST['subject'])){$errorz.='* '. lethe_please_enter_subject .'<br>';}else{
				$qr1 .= ",subject='". mysql_prep($_POST['subject']) ."'";
		}
		
		# Actions ******************
		
		if(!isset($_POST['ar_action']) || !is_numeric($_POST['ar_action'])){$errorz.='* '. lethe_please_choose_an_action .'<br>';}else{
		
			# After Subscription
			if($_POST['ar_action']==0){
				if(!isset($_POST['ar_0_time']) || !is_numeric($_POST['ar_0_time'])){$errorz.='* '. lethe_please_enter_a_value .'<br>';}else{
					$qr1 .= ",ar_mode=0,ar_mode_time=". intval($_POST['ar_0_time']) .",ar_mode_date=". intval($_POST['ar_0_date']) ."";
				}
			}
			
			# After Unsubscription
			else if($_POST['ar_action']==1){
				if(!isset($_POST['ar_1_time']) || !is_numeric($_POST['ar_1_time'])){$errorz.='* '. lethe_please_enter_a_value .'<br>';}else{
					$qr1 .= ",ar_mode=1,ar_mode_time=". intval($_POST['ar_1_time']) .",ar_mode_date=". intval($_POST['ar_1_date']) ."";
				}
			}
			
			# Specific Date
			else if($_POST['ar_action']==2){
			
					$qr1 .= ",ar_mode=2";

				# ** Launch Date (Required)
				if(!isset($_POST['launch_date'])){$_POST['launch_date']='00-00-0000';}
				if(!isset($_POST['launch_date_h'])){$_POST['launch_date_h']='00';}
				if(!isset($_POST['launch_date_m'])){$_POST['launch_date_m']='00';}
				$rdyDate = $_POST['launch_date'] . ' ' . $_POST['launch_date_h'] . ':' . $_POST['launch_date_m'] . ':00';
				$rdyDate = date("Y-m-d H:i:s",strtotime($rdyDate));

				if(!validateMysqlDate($rdyDate)){$errorz.='* '. lethe_invalid_launch_date .'<br>';}else{
					$qr1 .= ",launch_date='". mysql_prep($rdyDate) ."'";
				}
				
				# ** Finish Date
				if(!isset($_POST['finish_date'])){$_POST['finish_date']='00-00-0000';}
				if(!isset($_POST['finish_date_h'])){$_POST['finish_date_h']='00';}
				if(!isset($_POST['finish_date_m'])){$_POST['finish_date_m']='00';}
				$rdyDate = $_POST['finish_date'] . ' ' . $_POST['finish_date_h'] . ':' . $_POST['finish_date_m'] . ':00';
				$rdyDate = date("Y-m-d H:i:s",strtotime($rdyDate));
				
				# Check date if end campaign active
				if(isset($_POST['end_campaign']) && $_POST['end_campaign']=="YES"){
					if(!validateMysqlDate($rdyDate) || $rdyDate<date("Y-m-d H:i:s")){$errorz.='* '. lethe_invalid_finish_date .'<br>';}else{
						$qr1 .= ",finish_date='". mysql_prep($rdyDate) ."',end_camp=1";
					}
				}else{
					if(!isset($_POST['ar_2_time']) || !is_numeric($_POST['ar_2_time'])){$errorz.='* '. lethe_please_enter_a_value .'<br>';}else{
						$qr1 .= ",ar_mode_time=". intval($_POST['ar_2_time']) .",ar_mode_date=". intval($_POST['ar_2_date']) .",end_camp=0";
					}
				}
				
				
				# Weekdays
				$err_week = 0;
				for($i=0;$i<=6;$i++){
					if(isset($_POST['ar_weeks_'. $i]) && $_POST['ar_weeks_'. $i]=="YES"){
						$qr1 .= ",ar_week_" . $i . '=1';
						$err_week++;
					}else{
						$qr1 .= ",ar_week_" . $i . '=0';
					}
				}
				if($err_week==0){$errorz.='* '. lethe_please_choose_a_weekday .'<br>';}
			
			}
			
			# Special Days
			else if($_POST['ar_action']==3){
				if(!isset($_POST['ar_3_time']) || !is_numeric($_POST['ar_3_time'])){$errorz.='* '. lethe_please_enter_a_value .'<br>';}else{
					$qr1 .= ",ar_mode=3,ar_mode_time=". intval($_POST['ar_3_time']) .",ar_mode_date=". intval($_POST['ar_3_date']) ."";
				}
			}
		
		}
		
		# Actions End ******************
		
		# ** Newsletter Status
		if(isset($_POST['newsletter_proc']) && !is_numeric($_POST['newsletter_proc'])){
			# Default value
		}else{$qr1 .= ",position=" . intval($_POST['newsletter_proc']);}
		
		# ** Priority
		if(isset($_POST['importance']) && !empty($_POST['importance'])){
			$qr1 .= ",priotity=1";
		}else{$qr1 .= ",priotity=3";}
		
		# ** Details
		if(!isset($_POST['details']) || empty($_POST['details'])){$errorz.='* '. lethe_please_enter_details .'<br>';}else{
			$qr1 .= ",details='". mysql_prep3($_POST['details']) ."'";
		}
		
		# ** Attach File
		if(!isset($_POST['attach_file']) || empty($_POST['attach_file'])){
			$qr1 .= ",file_url=''";
		}else{
			$qr1 .= ",file_url='". mysql_prep($_POST['attach_file']) ."'";
		}
		
		# Groups
		$qr1 .= ",groups='". $group_lists ."'";
		
		
		if($errorz==''){
			# Reset If Field Checked
			if(isset($_POST['res']) && $_POST['res']=='YES'){
				$myconn->query("UPDATE ". db_table_pref ."newsletter_tasks SET sent=0 WHERE NID=". $this->content_ID ."") or die(mysqli_error());
				$myconn->query("UPDATE ". db_table_pref ."newsletters SET view_hit=0,click_hit=0,bounces=0 WHERE ID=". $this->content_ID ."") or die(mysqli_error());
				$qr1.=',position=1';
			}
			
			if($_POST['ar_action']==2){ # Only Works For Specific Dates
				# Delete Before Unselected List
				if(!empty($catch_ugroups)){
					$myconn->query("DELETE FROM ". db_table_pref ."newsletter_tasks WHERE pos=1 AND NID=". $this->content_ID ." ". $catch_ugroups ."") or die(mysqli_error());
				}
				
				# Load Tasks
				if(set_random_load==1){$qrOpt = "ORDER BY RAND()";}else{$qrOpt = "";} # Load Random or Ordered Mails
				
				# Prepare
				$stmt = $myconn->prepare("INSERT INTO
														". db_table_pref ."newsletter_tasks 
													(
														NID,SID,GID,receiver
													)
											   VALUES
													(
														?,?,?,?
													)
												  ");
				
				$opGroups = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE ". $catch_groups ." ". $qrOpt ."") or die(mysqli_error());
				while($opGroupsRs = $opGroups->fetch_assoc()){
					if(cntData("SELECT NID,receiver FROM ". db_table_pref ."newsletter_tasks WHERE NID=". $this->content_ID ." AND receiver='". $opGroupsRs['sub_mail'] ."'")==0){
					
								  # Insert to database Start

										$stmt->bind_param('iiis', $this->content_ID,
																   $opGroupsRs['ID'],
																   $opGroupsRs['GID'],
																   $opGroupsRs['sub_mail']

															); $stmt->execute();
					}
				}
				$stmt->close();
				$opGroups->free();
			}
			
			# Save Newsletter
			$myconn->query("UPDATE ". db_table_pref ."newsletters SET ". $qr1 ." WHERE ID=". $this->content_ID ."") or die(mysqli_error());
			$errorz = '<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_u_pdated_successfully . '</div>';
		}else{
			$errorz = '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. $errorz .'</div>';
		}
		
		$this->errPrint = $errorz;
	
	} # Edit Autoresponder End **
	
	# **** Sending Operations
	function send_newsletter(){
	
		global $myconn;
		$errorz = '';
		$opAccDetail = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_accounts WHERE ID=". $this->subAccID ."") or die(mysqli_error());
		if(mysqli_num_rows($opAccDetail)==0){$errorz.='<p class="text-danger">'. lethe_record_not_found .'</p>';return false;}
		$opAccDetailRs = $opAccDetail->fetch_assoc();
		require_once("class.phpmailer.php");
		$receivers = $this->subscribers; 

		# ** Message Formatting
		$msgData = stripslashes($this->newsBody);
		$msgSubject = stripslashes($this->newsSubject);
				
		# ** Dynamic Values
		foreach($this->subscriberDetails as $l){
			foreach($l as $k=>$v){
				$msgData = str_replace('{'. $k .'}',$v,$msgData); # Dynamic Values
				$msgSubject = str_replace('{'. $k .'}',$v,$msgSubject); # Dynamic Values
				
			}
		}

		# ** Special Values
		$msgData = preg_replace('#\{?(unsubscribe_link@)(.*?)(@)(.*?)\}#','<a href="'. relDocs(LETHEPATH).'/lethe.newsletter.php?pos=1&amp;lethe_pos=1&amp;lethe_email='.$this->newsletterUnsubscribe .'&amp;id='.$this->newsletterLink .'" style="$4">$2</a>',$msgData);
		$msgData = preg_replace('#\{?(newsletter_link@)(.*?)(@)(.*?)\}#','<a href="'. relDocs(LETHEPATH) .'/lethe.newsletter.php?pos=3&amp;id='.$this->newsletterLink .'" style="$4">$2</a>',$msgData);
		$msgData = preg_replace('#\{?(rss_link@)(.*?)\}#','<a href="'. set_rss_url . '">$2</a>',$msgData);
		$msgData = preg_replace('#\{?(verify_link@)(.*?)\}#','<a href="'. relDocs(LETHEPATH) .'/lethe.newsletter.php?pos=2&amp;id='.$this->newsletterVerification .'">$2</a>',$msgData);
		$msgData = preg_replace_callback('#\{?(track_link@)(.*?)(@)(.*?)(@)(.*?)\}#',
										create_function(
											'$matches',
											'return \'<a href="'. relDocs(LETHEPATH) .'/lethe.tracker.php?pos=1&amp;id='.$this->newsletterLink .'&amp;redURL=\'. urlencode($matches[4]) .\'" style="$matches[6]" target="_blank">\'. $matches[2] .\'</a>\';'
										)
										,$msgData);
		$msgData = shortFormatter($msgData);
		$msgSubject = shortFormatter($msgSubject);
	
		# ** SMTP
		if($opAccDetailRs['send_type']==0){
				# **************************************************************************
						$mail = new PHPMailer();
						$mail->SetLanguage("all", 'classes/phpMailer/language/');
						$mail->IsSMTP();
						$mail->addCustomHeader("X-Mailer: " . lethe_newsletter_version);
						$mail->addCustomHeader("X-Lethe-ID: " . $this->newsletterLink);
						$mail->Host     = $opAccDetailRs['smtp_host']; // SMTP servers
						$mail->SMTPAuth = $opAccDetailRs['smtp_auth'];     // turn on SMTP authentication
						$mail->SMTPDebug = $opAccDetailRs['debug_mode'];     // Debug Mode
						$mail->SMTPKeepAlive = true;
						if($opAccDetailRs['ssl_tls']==1){# SSL
							$mail->SMTPSecure = 'ssl';
							}else if($opAccDetailRs['ssl_tls']==2){# TLS
								$mail->SMTPSecure = 'tls';
								}
						$mail->Username = $opAccDetailRs['smtp_user'];  // SMTP username
						$mail->Password = $opAccDetailRs['smtp_pass']; // SMTP password
						$mail->Port = $opAccDetailRs['smtp_port']; // Port
						$mail->Priority = $this->sendPriority; // Email priority (1 = High, 3 = Normal, 5 = low)
						$mail->Encoding = 'base64';
						$mail->CharSet = "utf-8"; // Charset
						if($opAccDetailRs['email_type']==0){
							$mail->IsHTML(true);
							$mail->ContentType = "text/html"; // Message Content Type
						}else{
							$mail->ContentType = "text/plain"; // Message Content Type
						}
						$mail->SetFrom($opAccDetailRs['sender_mail'], $name = $opAccDetailRs['sender_title']);
						$mail->AddReplyTo($opAccDetailRs['reply_mail'], $opAccDetailRs['sender_title']);
						# ** Subscribers
						foreach($receivers as $key => $value){
							$mail->AddAddress($key, $value);
							$mail->addCustomHeader("X-Lethe-Receiver: " . $key);
						}
						# **
						
						$mail->Subject  =  $msgSubject;
						$mail->Body = $msgData;
						$mail->AltBody = $msgSubject;
						if(!empty($this->newsAttach)){
							$mail->AddStringAttachment($this->newsAttach,basename($this->newsAttach),$encoding = 'base64',$type = 'application/octet-stream');
							//$mail->AddAttachment($this->newsAttach); // attachment
						}
						if(!$mail->Send())
						{
							$this->sendError = 1;
							$errorz = '<p class="text-danger">'. $mail->ErrorInfo .'</p>';
						}
						else{
							$errorz = '<p class="text-success">'. lethe_test_mail_sent_successfully .'</p>';
							$this->sendError = 0;
							}
		
		}elseif($opAccDetailRs['send_type']==1){
		# ** PHPMail
		
						$mail = new PHPMailer();
						$mail->SetLanguage("all", 'classes/phpMailer/language/');
						$mail->addCustomHeader("X-Mailer: " . lethe_newsletter_version);
						$mail->addCustomHeader("X-Lethe-ID: " . $this->newsletterLink);
						$mail->Priority = $this->sendPriority; // Email priority (1 = High, 3 = Normal, 5 = low)
						$mail->Encoding = 'base64';
						$mail->SMTPDebug = $opAccDetailRs['debug_mode'];     // Debug Mode
						$mail->CharSet = "utf-8"; // Charset
						if($opAccDetailRs['email_type']==0){
							$mail->IsHTML(true);
							$mail->ContentType = "text/html"; // Message Content Type
						}else{
							$mail->ContentType = "text/plain"; // Message Content Type
						}
						$mail->SetFrom($opAccDetailRs['sender_mail'], $name = $opAccDetailRs['sender_title']);
						$mail->AddReplyTo($opAccDetailRs['reply_mail'], $opAccDetailRs['sender_title']);
						# ** Subscribers
						foreach($receivers as $key => $value){
							$mail->AddAddress($key, $value);
							$mail->addCustomHeader("X-Lethe-Receiver: " . $key);
						}
						# **
						
						$mail->Subject  =  $msgSubject;
						$mail->Body = $msgData;
						if(!empty($this->newsAttach)){
							$mail->AddStringAttachment($this->newsAttach,basename($this->newsAttach),$encoding = 'base64',$type = 'application/octet-stream');
							//$mail->AddAttachment($this->newsAttach); // attachment
						}
						if(!$mail->Send())
						{
							$this->sendError = 1;
							$errorz = '<p class="text-danger">'. $mail->ErrorInfo .'</p>';
						}
						else{
							$errorz = '<p class="text-success">'. lethe_test_mail_sent_successfully .'</p>';
							$this->sendError = 0;
							}
		
		} # End Send
		$this->errPrint = $errorz;
		$opAccDetail->free();
		if($this->sendError == 0){return true;}else{return false;}
	}
	
	# **** Send Verification Mail
	function send_verify(){
	
		global $myconn;
		# Call Primary Account
		$opAccV = $myconn->query("SELECT ID,primary_account FROM ". db_table_pref ."newsletter_accounts WHERE primary_account=1");
		if(mysqli_num_rows($opAccV)!=0){
			$opAccVRs = $opAccV->fetch_assoc();
			
			# Call Verification Template
			$opTempV = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_templates WHERE verification=1");
			if(mysqli_num_rows($opTempV)==0){ # If not exists use default
				$verifyBody = '<p>'. lethe_hello .' {subscriber_name},</p>';
				$verifyBody .= '<p>'. lethe_please_click_link_below_for_confirm_e_mail_address .'.</p>';
				$verifyBody .= '<p>{verify_link@'. lethe_click_here .'}</p>';
				$verifyBody .= '<p>'. lethe_thank_you .'</p>';
			}else{ # Use Template
				$opTempVRs = $opTempV->fetch_assoc();
				$verifyBody = $opTempVRs['details'];
			}
			
			# Send Verification
						$this->subAccID = $opAccVRs['ID'];
						$this->newsBody = $verifyBody;
						$this->newsSubject = lethe_e_mail_verification;
						$this->subscriberName = $this->subscriberName;
						$this->subscribers = array($this->subscriberMail=>$this->subscriberName);
						$this->sendPriority = 3; # Normal
						$this->testMode = 0;
						$this->send_newsletter();
			
			$opTempV->free();
		}
		$opAccV->free();
	
	}

	# **** Update Blacklist
	function edit_blacklist(){
	
		global $myconn;
		$errorz = '';
		$qr1 = '';
		
		# ** Check Admin Area Before Proccess
		if($this->admin_area){
			if($this->admin_mode!=1){
				$errorz .= '<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'. lethe_you_dont_have_permission_to_access_view_this_page .'</div>';
				$this->errPrint = $errorz;
				return false;
			}
		}
		# ** Check Admin Area Before Proccess End

		
		if(isset($_POST['bl_mail']) && mailVal($_POST['bl_mail'])){
			
			if(!isset($_POST['bl_ip'])){$_POST['bl_ip']=null;}
			if(!isset($_POST['bl_reason'])){$_POST['bl_reason']=null;}
			
			if(cntData("SELECT * FROM ". db_table_pref ."newsletter_blacklist WHERE email='". mysql_prep($_POST['bl_mail']) ."'")==0){
				$myconn->query("INSERT INTO 
											". db_table_pref ."newsletter_blacklist
							(
											email,
											ip_addr,
											reason
							) VALUES(
											'". mysql_prep($_POST['bl_mail']) ."',
											'". mysql_prep($_POST['bl_ip']) ."',
											'". mysql_prep($_POST['bl_reason']) ."'
							)
							") or die(mysqli_error());
			}
		
		}
		
		# Delete Selected Items
		if(isset($_POST['del'])){
			
			$del_group = $_POST['del'];
			foreach ($del_group as $sg){
				$myconn->query("DELETE FROM ". db_table_pref ."newsletter_blacklist WHERE ID=". intval($sg) ."") or die();
			}
		
		}
	
	}

	# **** Password Recovery
	function pass_recovery($UID,$mail,$user){
	
		global $myconn;
		# Call Primary Account
		$opAccV = $myconn->query("SELECT ID,primary_account FROM ". db_table_pref ."newsletter_accounts WHERE primary_account=1");
		if(mysqli_num_rows($opAccV)!=0){
			$opAccVRs = $opAccV->fetch_assoc();
			
			$new_pass = base_convert(uniqid('lethe', true), 10, 36);
			$new_pass_encr = encr($new_pass);
			$myconn->query("UPDATE ". db_table_pref ."users SET user_pass='". $new_pass_encr ."' WHERE ID=". $UID ."") or die(mysql_error());
			
				$verifyBody = '<!doctype html>
								<html>
								<head>
								<meta charset="utf-8">
								<title>'. lethe_newsletter_site_name .'</title>
								</head>
								<body style="margin:0; padding:0;">
								<div style="background-color: #ccc; width: 100%; height: 100%; padding: 30px; font-family: Verdana, Geneva, sans-serif; font-size: 12px;">
								<div style="width: 400px; margin: auto; background-color: #fff; padding: 15px; border: 1px solid #999;">
								<p>Hello,</p>
								<p>Your new login information is as follows;</p>
								<p><strong>Username:</strong> '. $user .'<br>
								<strong>Password:</strong> '. $new_pass .'</p>
								<p>Please don\'t forget to change your password after logged.</p>
								<p>Thank you!</p>
								</div>
								<div style="width: 400px; margin: auto; padding:15px; font-size:10px">'. lethe_newsletter_site_name .'</div>
								</div>
								</body>
								</html>';

			
			# Send Verification
						$this->subAccID = $opAccVRs['ID'];
						$this->newsBody = $verifyBody;
						$this->newsSubject = lethe_password_recovery;
						$this->subscriberName = $user;
						$this->subscribers = array($mail=>$user);
						$this->sendPriority = 3; # Normal
						$this->testMode = 0;
						$this->send_newsletter();
						return true;
			
		}else{
			return false;
		}
		$opAccV->free();
	
	}
	} # Class End **
?>