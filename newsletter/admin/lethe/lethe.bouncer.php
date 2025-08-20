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
include_once(dirname(dirname(dirname(__FILE__))).'/inc/inc_connector.php'); 
include_once(LETHEPATH.'/inc/inc_functions.php');
include_once('classes/class.lethe.php');
include_once('lethe.config.php');
if(demo_mode){echo(lethe_demo_mode_active);}else{
@set_time_limit(0);
# Check Unique Task Code
# if(isset($_GET['u']) && md5($_GET['u'])==md5(set_unique_code)){

		# Get Bounce List & Rules
		$bounce_rules = array();
		$opBounceRules = $myconn->query("SELECT bounce_code,bounce_rule FROM ". db_table_pref ."newsletter_bounce_catcher WHERE active=1") or die(mysqli_error());
		while($opBounceRulesRs = $opBounceRules->fetch_row()){
			$bounce_rules[] = $opBounceRulesRs;
			};
			$opBounceRules->free();
		//print_r($bounce_rules);
		
	$opSubAcc = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_accounts WHERE active=1 AND (pop3_host IS NOT NULL OR pop3_host <> '') AND (pop3_port <> 0)") or die(mysqli_error());
	while($opSubAccRs = $opSubAcc->fetch_assoc()){
		$ssl_pos = '';
		if($opSubAccRs['ssl_tls']==1){$ssl_pos='/ssl';}
		else if($opSubAccRs['ssl_tls']==2){$ssl_pos='/tls';}
		else{$ssl_pos='/novalidate-cert';}
		if($opSubAccRs['bounce_acc']==0){
			$inst=pop3_login($opSubAccRs['pop3_host'],$opSubAccRs['pop3_port'],$opSubAccRs['pop3_user'],$opSubAccRs['pop3_pass'],$folder='INBOX',$ssl_pos);
		}else{
			$inst=pop3_login($opSubAccRs['imap_host'],$opSubAccRs['imap_port'],$opSubAccRs['imap_user'],$opSubAccRs['imap_pass'],$folder='INBOX',$ssl_pos);
		}
		if($inst){ # If connected to mailbox
		//echo('Connect: ' . $opSubAccRs['smtp_user'].'<br>');
		$stat=pop3_stat($inst);
		$list=pop3_list($inst);
		
		//print_r($list);
		
		if($stat['Unread']>0){
			foreach($list as $row){
				$msgBody = imap_fetchbody($inst, $row['msgno'],1);
				$header = explode("\n", $msgBody);
				$bounce_pos=0;
				# Check Bounces
				foreach ($bounce_rules as $spamword) {
						if (strrpos($msgBody, $spamword[0])) {
							# Bounced Mail Catched
								# Parse
								if (is_array($header) && count($header)) {
										$head = array();
										foreach($header as $line) { # Get Specific Lethe Head Lines
											// is line with additional header?
											if (eregi("^X-", $line)) {
												// separate name and value
												eregi("^([^:]*): (.*)", $line, $arg);
												$head[$arg[1]] = $arg[2];
											}
										}
										if(isset($head['X-Lethe-ID'])){$letheID = mysql_prep($head['X-Lethe-ID']);}else{$letheID = '';};
										if(isset($head['X-Lethe-Receiver'])){$letheRec = mysql_prep($head['X-Lethe-Receiver']);}else{$letheRec = '';};
									}
								# Parse

								# Operations Start
									if($spamword[1]==0){ # Mark it inactive
										$myconn->query("UPDATE ". db_table_pref ."newsletter_subscribers SET active=0 WHERE sub_mail='". mysql_prep($letheRec) ."'") or die();
										$myconn->query("DELETE FROM ". db_table_pref ."newsletter_tasks WHERE receiver='". mysql_prep($letheRec) ."'") or die();
										}
									else if($spamword[1]==1){ # Remove from list
										$myconn->query("DELETE FROM ". db_table_pref ."newsletter_subscribers WHERE sub_mail='". mysql_prep($letheRec) ."'") or die();
										$myconn->query("DELETE FROM ". db_table_pref ."newsletter_tasks WHERE receiver='". mysql_prep($letheRec) ."'") or die();
										}
									else if($spamword[1]==2){ # Remove from list & add blacklist
										$myconn->query("DELETE FROM ". db_table_pref ."newsletter_subscribers WHERE sub_mail='". mysql_prep($letheRec) ."'") or die();
										$myconn->query("DELETE FROM ". db_table_pref ."newsletter_tasks WHERE receiver='". mysql_prep($letheRec) ."'") or die();
										if(cntData("SELECT * FROM ". db_table_pref ."newsletter_blacklist WHERE reason='". lethe_bounce ."',email='". mysql_prep($letheRec) ."'")==0){
											$myconn->query("INSERT INTO ". db_table_pref ."newsletter_blacklist (email) VALUES ('". mysql_prep($letheRec) ."')") or die();
										}
										}
										
								# Add Bounce Stat
								$myconn->query("UPDATE ". db_table_pref ."newsletters SET bounces=bounces+1 WHERE newsletter_id='". mysql_prep($letheID) ."'") or die();
								
								# Operations End
							# **
							$bounce_pos = 1;
							@imap_delete($inst,$row['msgno'],FT_UID); # Bounced Message Deleted
							break;
							} # Bounce Handler End
					} # Bounce Rule List End
			} # Post List End
			//echo '<br>';
			@imap_expunge($inst);
			$errs = @imap_errors();
			@imap_close($inst); # Mailbox Closed
		} # Unread Post Operations End
	} # Connected Mailbox
	} # Account List End
	$opSubAcc->free();
		
# }
}
$myconn->close();
?>