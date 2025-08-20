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

$date_parser = array('MINUTE','HOUR','DAY','WEEK','MONTH','YEAR');

# After Subscription Tasks

		$opAutoresponder = $myconn->query("SELECT * FROM ". db_table_pref ."newsletters WHERE (position=1) AND (data_mode=1) AND (ar_mode=0 OR ar_mode=1)") or die(mysqli_error());
		while($AutoresponderRs = $opAutoresponder->fetch_assoc()){ # Autoresponder Opened
				# Open Submission Account
				$opAccount = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_accounts WHERE ID=". $AutoresponderRs['SUID'] ." AND (dailySent<send_mail_limit)") or die(mysqli_error());
				if(mysqli_num_rows($opAccount)!=0){

					$opAccountRs = $opAccount->fetch_assoc();
					$dailyLimitRemain = intval($opAccountRs['send_mail_limit']-$opAccountRs['dailySent']);
					
					# Open Tasks
						$t = 1;
						$receiver_list = array();
						$subGroups = $AutoresponderRs['groups'];
						$subGroups = ' (GID='. str_replace(',',' OR GID=',$subGroups) .') ';
						
						if($AutoresponderRs['ar_mode']==1){
							$getUnsubscribed=' AND (unsubscribed=1) ';
							$date_condition = " AND (del_date > date_sub('". $date_prep ."', interval ". $AutoresponderRs['ar_mode_time'] ." ". $date_parser[$AutoresponderRs['ar_mode_date']] ."))";
						}
						else if($AutoresponderRs['ar_mode']==0){
							$getUnsubscribed='';
							$date_condition = " AND (add_date > date_sub('". $date_prep ."', interval ". $AutoresponderRs['ar_mode_time'] ." ". $date_parser[$AutoresponderRs['ar_mode_date']] ."))";
						}

						$opTaskList = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE ". $subGroups ." ". $getUnsubscribed ." ". $date_condition ." LIMIT 0,". $dailyLimitRemain ."") or die(mysqli_error());
						if(mysqli_num_rows($opTaskList)==0){ # There no subscribers founded.
							# **
						}else{
							# Start Sending
							while($opTaskListRs = $opTaskList->fetch_assoc()){
								$mailData = getSubscriber($opTaskListRs['ID'],1);
								if(!$mailData){ # Remove from list if there subscriber data error
									# **
									}else{
									if($AutoresponderRs['ar_mode']==0){
										$myconn->query("UPDATE ". db_table_pref ."newsletter_subscribers SET add_date=date_sub('". $date_prep ."', interval ". $AutoresponderRs['ar_mode_time'] ." ". $date_parser[$AutoresponderRs['ar_mode_date']] .") WHERE ID=". $opTaskListRs['ID'] ."");
									}else{
										$myconn->query("UPDATE ". db_table_pref ."newsletter_subscribers SET del_date=date_sub('". $date_prep ."', interval ". $AutoresponderRs['ar_mode_time'] ." ". $date_parser[$AutoresponderRs['ar_mode_date']] .") WHERE ID=". $opTaskListRs['ID'] ."");
									}
									
									$receiver_list[] = $mailData;
									}
								$t++;

									//die(print_r($receiver_list));

										$t=1;
										# Send Mails Here **
											foreach($receiver_list as $key => $value){
												$lethe = new lethe;
												$lethe->subAccID = $opAccountRs['ID'];
												$lethe->newsBody = $AutoresponderRs['details'] . '<img style="display:none;" src="'. relDocs(LETHEPATH) . '/lethe.tracker.php?id='. $AutoresponderRs['ID'] .'&rcvid='. $receiver_list[$key][0] .'">';
												$lethe->newsSubject = $AutoresponderRs['subject'];
												$lethe->newsAttach = $AutoresponderRs['file_url'];
												$lethe->subscriberDetails = $receiver_list[$key][3];
												$lethe->subscriberName = $receiver_list[$key][1];
												$lethe->newsletterUnsubscribe = $receiver_list[$key][2];
												$lethe->newsletterLink = $AutoresponderRs['newsletter_id'];
												$lethe->subscribers = array($receiver_list[$key][0]=>$receiver_list[$key][1]);
												$lethe->sendPriority = $AutoresponderRs['priotity']; 
												$lethe->testMode = 0;
												$lethe->send_newsletter();
												
									# Updates
									$myconn->query("UPDATE ". db_table_pref ."newsletter_accounts SET dailySent=dailySent+1 WHERE ID=". $opAccountRs['ID'] ."");
									$myconn->query("UPDATE ". db_table_pref ."users SET dailySent=dailySent+1 WHERE ID=". $AutoresponderRs['UID'] ."");
												
											}

													
										# Clear Cache
										unset($send_list);
										$send_list = array();
										# ******************
										sleep($opAccountRs['send_mail_duration']);

									
							} # Task List End
							# End Sending
						
						} $opTaskList->free();
					# Tasks End
					
					
				}
				$opAccount->free();
		}
		$opAutoresponder->free();


# Special Days
		$opAutoresponder = $myconn->query("SELECT * FROM ". db_table_pref ."newsletters WHERE (position=1) AND (data_mode=1) AND (ar_mode=3)") or die(mysqli_error());
		while($AutoresponderRs = $opAutoresponder->fetch_assoc()){ # Autoresponder Opened
		
				# Open Submission Account
				$opAccount = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_accounts WHERE ID=". $AutoresponderRs['SUID'] ." AND (dailySent<send_mail_limit)") or die(mysqli_error());
				if(mysqli_num_rows($opAccount)!=0){
				
					$opAccountRs = $opAccount->fetch_assoc();
					$dailyLimitRemain = intval($opAccountRs['send_mail_limit']-$opAccountRs['dailySent']);
					
					# Open Tasks
						$opTaskList = $myconn->query("
														SELECT 
																*,
																NT.ID AS NTID,
																NS.ID AS NSID
																
														FROM 
																". db_table_pref ."newsletter_tasks AS NT,
																". db_table_pref ."newsletter_subscribers AS NS
														WHERE 
																(NT.SID=NS.ID)
														AND
																(NT.NID=". $AutoresponderRs['ID'] .")
														AND 
																(DATE_FORMAT(NS.sub_date,'%m-%d') = DATE_FORMAT(DATE_ADD('". $date_prep ."',INTERVAL ". $AutoresponderRs['ar_mode_time'] ." ". $date_parser[$AutoresponderRs['ar_mode_date']] ."),'%m-%d'))
														AND
																(DATE_FORMAT(NT.last_send,'%Y')<>DATE_FORMAT('". $date_prep ."','%Y'))
														LIMIT 
																0,". $dailyLimitRemain ."
														") or die(mysqli_error());
						if(mysqli_num_rows($opTaskList)==0){ # There no subscribers founded.
							# **

						}else{
						
							# Start Sending
							$receiver_list = array();
							$t=1;
							while($opTaskListRs = $opTaskList->fetch_assoc()){
								$mailData = getSubscriber($opTaskListRs['SID'],1);
								if(!$mailData){ # Remove from list if there subscriber data error
									# **
									}else{
									
									# Update Sent Date
									$myconn->query("UPDATE ". db_table_pref ."newsletter_tasks SET last_send='". $date_prep ."' WHERE ID=". $opTaskListRs['NTID'] ."");
									
									$receiver_list[] = $mailData;
									}
								$t++;
							} # Task List End
							
										# Send Mails Here **
											foreach($receiver_list as $key => $value){
												$lethe = new lethe;
												$lethe->subAccID = $opAccountRs['ID'];
												$lethe->newsBody = $AutoresponderRs['details'] . '<img style="display:none;" src="'. relDocs(LETHEPATH) . '/lethe.tracker.php?id='. $AutoresponderRs['ID'] .'&rcvid='. $receiver_list[$key][0] .'">';
												$lethe->newsSubject = $AutoresponderRs['subject'];
												$lethe->newsAttach = $AutoresponderRs['file_url'];
												$lethe->subscriberDetails = $receiver_list[$key][3];
												$lethe->subscriberName = $receiver_list[$key][1];
												$lethe->newsletterUnsubscribe = $receiver_list[$key][2];
												$lethe->newsletterLink = $AutoresponderRs['newsletter_id'];
												$lethe->subscribers = array($receiver_list[$key][0]=>$receiver_list[$key][1]);
												$lethe->sendPriority = $AutoresponderRs['priotity']; 
												$lethe->testMode = 0;
												$lethe->send_newsletter();
												
									# Updates
									$myconn->query("UPDATE ". db_table_pref ."newsletter_accounts SET dailySent=dailySent+1 WHERE ID=". $opAccountRs['ID'] ."");
									$myconn->query("UPDATE ". db_table_pref ."users SET dailySent=dailySent+1 WHERE ID=". $AutoresponderRs['UID'] ."");
												
											}

													
										# Clear Cache
										unset($send_list);
										$send_list = array();
										# ******************
										sleep($opAccountRs['send_mail_duration']);
							
							# Debug List Active
							# print_r($receiver_list);
							# echo('<br>'.count($receiver_list));
						
						}
					$opTaskList->free();
				
				}
				$opAccount->free();
		
		}
		$opAutoresponder->free();

		
# Specific Date Tasks

	# Autoresponder Sending Start
	
		$opNewsletters = $myconn->query("SELECT * FROM ". db_table_pref ."newsletters WHERE (position=1 OR position=2) AND launch_date<'". $date_prep ."' AND (data_mode=1) AND (ar_mode=2) AND (ar_week_". date("w") ."=1) ORDER BY launch_date ASC") or die(mysqli_error());
		while($opNewslettersRs = $opNewsletters->fetch_assoc()){ # Newsletters Opened
		
			$run = true;
		
			# Update Position to Process If Its on Load Position
			if($opNewslettersRs['position']==1){
				$myconn->query("UPDATE ". db_table_pref ."newsletters SET position=2 WHERE ID=". $opNewslettersRs['ID'] ."") or die(mysqli_error());
			}

			# Stop Campaign If Finish Date Came *********
			if($opNewslettersRs['end_camp']==1){
				if(date('Y-m-d H:i:s',strtotime($opNewslettersRs['finish_date']))<=date('Y-m-d H:i:s')){
					$myconn->query("UPDATE ". db_table_pref ."newsletters SET position=4 WHERE ID=". $opNewslettersRs['ID'] ."") or die(mysqli_error());
					$run = false;
				}
			}else{ # Reset Newsletter If End Campaign Not Checked
				if(date('Y-m-d H:i:s',strtotime($opNewslettersRs['finish_date']))<=date('Y-m-d H:i:s')){
					# Newsletter launch/finish date set to defined Next Launch Date value
					if(date('Y',strtotime($opNewslettersRs['finish_date']))<date('Y')){$fsh_date = date("Y-m-d H:i:s");}else{$fsh_date = $opNewslettersRs['finish_date'];}
					$myconn->query("UPDATE 
										". db_table_pref ."newsletters 
								SET 
										position=1,
										launch_date=DATE_ADD(launch_date,INTERVAL ". $opNewslettersRs['ar_mode_time'] ." ". $date_parser[$opNewslettersRs['ar_mode_date']] ."),
										finish_date=DATE_ADD(". strtotime($fsh_date) .",INTERVAL ". $opNewslettersRs['ar_mode_time'] ." ". $date_parser[$opNewslettersRs['ar_mode_date']] .") 									
										
								WHERE 
										ID=". $opNewslettersRs['ID']) or die(mysqli_error($myconn));
					$myconn->query("UPDATE ". db_table_pref ."newsletter_tasks SET sent=0 WHERE NID=". $opNewslettersRs['ID'] ."") or die(mysqli_error());
					$run = false;
				}
			}
			# Stop Campaign If Finish Date Came *********
		
		if($run){
			# Check User's Daily Limit
			if(getUser($opNewslettersRs['UID'],1)){

				# Open Submission Account
				$opAccount = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_accounts WHERE ID=". $opNewslettersRs['SUID'] ." AND (dailySent<send_mail_limit)") or die(mysqli_error());
				if(mysqli_num_rows($opAccount)!=0){
					$opAccountRs = $opAccount->fetch_assoc();
					$dailyLimitRemain = intval($opAccountRs['send_mail_limit']-$opAccountRs['dailySent']);
					
					# Open Tasks
						$opTaskList = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_tasks WHERE NID=". $opNewslettersRs['ID'] ." AND pos=1 AND sent=0 AND unsubscribed=0 LIMIT 0,". $dailyLimitRemain ."") or die(mysqli_error());
						if(mysqli_num_rows($opTaskList)==0){ # Daily Send Limit Exceeded.
							# Check Unsent Subscribers Today
							if(cntData("SELECT * FROM 
														". db_table_pref ."newsletter_tasks 
												WHERE 
														(NID = ". $opNewslettersRs['ID'] .")
												AND
														(pos = 1)
												AND
														(sent=1)
												AND
														(DATE_FORMAT(last_send, '%d-%m-%Y')<>DATE_FORMAT('". $date_prep ."', '%d-%m-%Y'))
												")!=0){ # Update Unsent Founded Subscribers
							# Update Now
							$myconn->query("UPDATE ". db_table_pref ."newsletter_tasks SET sent=0 WHERE (NID=". $opNewslettersRs['ID'] .") AND (DATE_FORMAT(last_send, '%d-%m-%Y')<>DATE_FORMAT('". $date_prep ."', '%d-%m-%Y'))") or die();
							}
						}else{
						
							# Start Sending
							$receiver_list = array();
							while($opTaskListRs = $opTaskList->fetch_assoc()){
								$mailData = getSubscriber($opTaskListRs['SID'],1);
								if(!$mailData){ # Remove from list if there subscriber data error
									$myconn->query("DELETE FROM ". db_table_pref ."newsletter_tasks WHERE ID=". $opTaskListRs['ID'] ."") or die(mysqli_error());
									}else{
									$receiver_list[] = $mailData;
									# Updates
									$myconn->query("UPDATE ". db_table_pref ."newsletter_tasks SET sent=1,last_send='". $date_prep ."' WHERE ID=". $opTaskListRs['ID'] ."");
									}
									
									if(count($receiver_list)>=$opAccountRs['mail_limit_per_con']){
										# Send Mails Here **
											foreach($receiver_list as $key => $value){
												$lethe = new lethe;
												$lethe->subAccID = $opAccountRs['ID'];
												$lethe->newsBody = $opNewslettersRs['details'] . '<img style="display:none;" src="'. relDocs(LETHEPATH) .'/lethe.tracker.php?id='. $opNewslettersRs['ID'] .'&rcvid='. $receiver_list[$key][0] .'">';
												$lethe->newsSubject = $opNewslettersRs['subject'];
												$lethe->newsAttach = $opNewslettersRs['file_url'];
												$lethe->subscriberDetails = $receiver_list[$key][3];
												$lethe->subscriberName = $receiver_list[$key][1];
												$lethe->newsletterUnsubscribe = $receiver_list[$key][2];
												$lethe->newsletterLink = $opNewslettersRs['newsletter_id'];
												$lethe->subscribers = array($receiver_list[$key][0]=>$receiver_list[$key][1]);
												$lethe->sendPriority = $opNewslettersRs['priotity']; 
												$lethe->testMode = 0;
												$lethe->send_newsletter();
									$myconn->query("UPDATE ". db_table_pref ."newsletter_accounts SET dailySent=dailySent+1 WHERE ID=". $opAccountRs['ID'] ."");
									$myconn->query("UPDATE ". db_table_pref ."users SET dailySent=dailySent+1 WHERE ID=". $opNewslettersRs['UID'] ."");
											}
													
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
			} # Run Mode Checked
		} $opNewsletters->free(); # Newsletter List End
	
	# Newsletters Sending End

	} // Demo Check End
?>