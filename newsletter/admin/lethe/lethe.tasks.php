<?php 
# +------------------------------------------------------------------------+
# | Artlantis CMS Solutions                                                |
# +------------------------------------------------------------------------+
# | Lethe Newsletter & Mailing System                                      |
# | Copyright (c) Artlantis Design Studio 2014. All rights reserved.       |
# | Version       1.1.4                                                    |
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

$date_prep = date('Y-m-d H:i:s');

# Update Daily Limits
$myconn->query("UPDATE ". db_table_pref ."users SET dailySent=0,resetDate = DATE_ADD('". $date_prep ."' , INTERVAL 1 DAY) WHERE resetDate<='". $date_prep ."' AND admin_mode=2") or die(mysqli_error());
$myconn->query("UPDATE ". db_table_pref ."newsletter_accounts SET dailySent=0,resetDaily = DATE_ADD('". $date_prep ."' , INTERVAL 1 DAY) WHERE resetDaily<='". $date_prep ."'") or die(mysqli_error());

# Check Unique Task Code
# if(isset($_GET['u']) && md5($_GET['u'])==md5(set_unique_code)){	

	# Load Autoresponder
	//include_once('lethe.autoresponder.php');

	# Newsletters Sending Start
	
		$opNewsletters = $myconn->query("SELECT * FROM ". db_table_pref ."newsletters WHERE (position=1 OR position=2) AND launch_date<'". $date_prep ."' AND (data_mode=0) ORDER BY launch_date ASC") or die(mysqli_error());
		while($opNewslettersRs = $opNewsletters->fetch_assoc()){ # Newsletters Opened
		
			# Update Position to Process If Its on Load Position
			if($opNewslettersRs['position']==1){
				$myconn->query("UPDATE ". db_table_pref ."newsletters SET position=2 WHERE ID=". $opNewslettersRs['ID'] ."") or die(mysqli_error());
			}	

			# Check User's Daily Limit
			if(getUser($opNewslettersRs['UID'],1)){

				# Open Submission Account
				$opAccount = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_accounts WHERE ID=". $opNewslettersRs['SUID'] ." AND (dailySent<send_mail_limit)") or die(mysqli_error());
				if(mysqli_num_rows($opAccount)!=0){
					$opAccountRs = $opAccount->fetch_assoc();
					$dailyLimitRemain = intval($opAccountRs['send_mail_limit']-$opAccountRs['dailySent']);

					# Open Tasks
						$opTaskList = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_tasks WHERE NID=". $opNewslettersRs['ID'] ." AND pos=0 AND sent=0 AND unsubscribed=0 LIMIT 0,". $dailyLimitRemain ."") or die(mysqli_error());
						if(mysqli_num_rows($opTaskList)==0){ # Daily Send Limit Exceeded.
							if(cntData("SELECT * FROM ". db_table_pref ."newsletter_tasks WHERE NID=". $opNewslettersRs['ID'] ." AND sent=0")==0){
								# All Mails Was Sent, Mark Newsletter to Complete
								$myconn->query("UPDATE ". db_table_pref ."newsletters SET position=4 WHERE ID=". $opNewslettersRs['ID'] ."") or die(mysqli_error());
							}
						}else{
						
							# Start Sending
							$receiver_list = array();
							$total_tasks = mysqli_num_rows($opTaskList);
							while($opTaskListRs = $opTaskList->fetch_assoc()){
								$mailData = getSubscriber($opTaskListRs['SID'],1);

								if(!$mailData){ # Remove from list if there subscriber data error
									$myconn->query("DELETE FROM ". db_table_pref ."newsletter_tasks WHERE ID=". $opTaskListRs['ID'] ."") or die(mysqli_error());
									}else{
									$receiver_list[] = $mailData;
									
									# Updates
									$myconn->query("UPDATE ". db_table_pref ."newsletter_tasks SET sent=1,last_send='". $date_prep ."' WHERE ID=". $opTaskListRs['ID'] ."");
									$myconn->query("UPDATE ". db_table_pref ."newsletter_accounts SET dailySent=dailySent+1 WHERE ID=". $opAccountRs['ID'] ."");
									$myconn->query("UPDATE ". db_table_pref ."users SET dailySent=dailySent+1 WHERE ID=". $opNewslettersRs['UID'] ."");
									}

									if(count($receiver_list)>=$opAccountRs['mail_limit_per_con'] || $total_tasks<$opAccountRs['mail_limit_per_con']){

										# Send Mails Here **
										
										foreach($receiver_list as $key => $value){
												$lethe = new lethe;
												$lethe->subAccID = $opAccountRs['ID'];
												$lethe->newsBody = $opNewslettersRs['details'] . '<img style="display:none;" src="'. relDocs(LETHEPATH) .'/lethe.tracker.php?id='. $opNewslettersRs['ID'] .'&rcvid='. $receiver_list[$key][0] .'">';
												$lethe->newsSubject = $opNewslettersRs['subject'];
												$lethe->newsAttach = $opNewslettersRs['file_url'];
												$lethe->subscriberDetails = $receiver_list[$key][3];
												$lethe->newsletterUnsubscribe = $receiver_list[$key][2];
												$lethe->newsletterLink = $opNewslettersRs['newsletter_id'];
												$lethe->subscribers = array($receiver_list[$key][0]=>$receiver_list[$key][1]);
												$lethe->sendPriority = $opNewslettersRs['priotity']; 
												$lethe->testMode = 0;
												$lethe->send_newsletter();
												if($opAccountRs['debug_mode']==1){echo($receiver_list[$key][0].' Sent Successfully<br>');}
										}
										//@$mail->SmtpClose();
										# Clear Cache
										unset_all_vars($receiver_list);
										$receiver_list = array();
										# ******************
										sleep($opAccountRs['send_mail_duration']);
									}
									
							} # Task List End
							# End Sending
						
						} $opTaskList->free();
					# Tasks End
					
				} $opAccount->free(); # Submission Account Passed Daily Limit Control
				} # User Passed Daily Limit Control
		} $opNewsletters->free(); # Newsletter List End
	
	# Newsletters Sending End
	
# } #End
}

$myconn->close();
die();
?>