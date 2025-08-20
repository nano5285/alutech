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
include_once(dirname(__FILE__).'/inc/inc_connector.php'); 
include_once(dirname(__FILE__).'/inc/inc_functions.php');
include_once(LETHEPATH.'/'. lethe_admin_path .'/lethe.config.php');
include_once(LETHEPATH.'/'. lethe_admin_path .'/classes/class.lethe.php');
if(!isset($_GET['pos'])){$pos=0;}else{$pos=intval($_GET['pos']);} # 1 - Normal Form
if(!isset($_GET['id'])){$id='';}else{$id=trim($_GET['id']);} # Action ID
$errText = '';

if($pos==1){
# *** Defined Variables
if(!isset($_REQUEST['lethe_pos']) || intval($_REQUEST['lethe_pos'])==0){$lethe_pos=0;}else{$lethe_pos=intval($_REQUEST['lethe_pos']);} # 0 - Add // 1 - Remove
if(!isset($_REQUEST['lethe_subscribe_group']) || intval($_REQUEST['lethe_subscribe_group'])==0){$lethe_subscribe_group=0;}else{$lethe_subscribe_group=intval($_REQUEST['lethe_subscribe_group']);} # Group
if(!isset($_REQUEST['lethe_email'])){$lethe_email='';}else{$lethe_email=$_REQUEST['lethe_email'];} # E-Mail Address
if(!isset($_REQUEST['lethe_name']) || empty($_REQUEST['lethe_name'])){$lethe_name='';}else{$lethe_name=$_REQUEST['lethe_name'];} # Name
if(!isset($_REQUEST['lethe_company']) || empty($_REQUEST['lethe_company'])){$lethe_company='';}else{$lethe_company=$_REQUEST['lethe_company'];} # Company
if(!isset($_REQUEST['lethe_phone']) || empty($_REQUEST['lethe_phone'])){$lethe_phone='';}else{$lethe_phone=$_REQUEST['lethe_phone'];} # Phone
if(!isset($_REQUEST['lethe_date']) || empty($_REQUEST['lethe_date'])){$lethe_date='';}else{$lethe_date=$_REQUEST['lethe_date'];} # Date
if(!isset($_REQUEST['lethe_listbox']) || empty($_REQUEST['lethe_listbox'])){$lethe_listbox='';}else{$lethe_listbox=$_REQUEST['lethe_listbox'];} # Listbox
if(!isset($_REQUEST['lkey']) || empty($_REQUEST['lkey'])){$lkey='';}else{$lkey=$_REQUEST['lkey'];} # Form Key


	if($lethe_pos==0){ # Subscribe
		if(demo_mode){echo(lethe_demo_mode_active);}else{
			# Define POST variables
			$_POST['sub_group'] = intval($lethe_subscribe_group);
			$_POST['sub_mail'] = mysql_prep($lethe_email);
			$_POST['sub_name'] = mysql_prep($lethe_name);
			$_POST['sub_comp'] = mysql_prep($lethe_company);
			$_POST['sub_phone'] = mysql_prep($lethe_phone);
			$_POST['sub_date'] = mysql_prep($lethe_date);
			$_POST['sub_select'] = mysql_prep($lethe_listbox);
			$_POST['sub_form_code'] = mysql_prep($lkey);
			$lethe = new lethe;
			$lethe->admin_area = 0; # Mark it true, if you wanna only admins can add subscriber.
			$lethe->sendActivation = set_send_verification;
			$lethe->add_subscriber();
			echo($lethe->errPrint);
			}
		}
	elseif($lethe_pos==1){ # Unsubscribe
		if(demo_mode){echo(lethe_demo_mode_active);}else{
		# Thats remove e-mail address on database
		$remArea = '';
		if(!mailVal($lethe_email)){ # If request value is subscriber code
			$remArea = "sub_code='". mysql_prep($lethe_email) ."'";
		}else{ # If request value is subscriber e-mail
			$remArea = "sub_mail='". mysql_prep($lethe_email) ."'";
		}
		if(!empty($remArea)){
		
			# Get ID
			$SID = null;
			$opSub = $myconn->query("SELECT ID,sub_mail,sub_code FROM ". db_table_pref ."newsletter_subscribers WHERE ". $remArea ."") or die(mysqli_error());
			if(mysqli_num_rows($opSub)==0){ # There no found subscriber
				die();
			}else{
			$opSubRs = $opSub->fetch_assoc();
			$SID = $opSubRs['ID'];
			}
			$opSub->free();
			
			if(set_after_unsubscribe==0){ # Mark It Inactive
				$NID = getNewsletter(mysql_prep($id),0);
				$myconn->query("UPDATE ". db_table_pref ."newsletter_subscribers SET active=0, unsubscribed=1, del_date=NOW() WHERE ID=". $SID ."") or die();
				$myconn->query("UPDATE ". db_table_pref ."newsletter_tasks SET unsubscribed=1 WHERE SID=". $SID ." AND NID=". $NID ."") or die();
			}else if(set_after_unsubscribe==1){ # Remove From List
				$NID = getNewsletter(mysql_prep($id),0);
				$myconn->query("UPDATE ". db_table_pref ."newsletter_tasks SET unsubscribed=1 WHERE SID=". $SID ." AND NID=". $NID ."") or die();
			}else if(set_after_unsubscribe==2){ # Remove From Database
				$myconn->query("DELETE FROM ". db_table_pref ."newsletter_subscribers WHERE ID=". $SID ."") or die();
				$myconn->query("DELETE FROM ". db_table_pref ."newsletter_tasks WHERE SID=". $SID ."") or die();
			}else if(set_after_unsubscribe==3){ # Move To Unsubscriber Group
				$USG_ID = getSubscriber(0,2);
				$USG_ID = intval($USG_ID);
				if($USG_ID!=0){
					$myconn->query("UPDATE ". db_table_pref ."newsletter_subscribers SET GID=". $USG_ID .", active=1, unsubscribed=0, del_date=NOW() WHERE ID=". $SID ."") or die();
				}else{
					
				}
			}
		
			//$myconn->query("DELETE FROM ". db_table_pref ."newsletter_subscribers WHERE ". $remArea ."") or die(mysqli_error());
			echo('<div class="alert alert-info">'. lethe_your_e_mail_address_d_eleted_successfully .'!</div>'); # This area can be designed by your style.
		}
		}
		}
}
elseif($pos==2){ # Verification
	if(demo_mode){echo(lethe_demo_mode_active);}else{
	if(chkData("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE sub_code='". mysql_prep($id) ."' AND activated=0")){
		echo('<div style="width:500px; text-align:center; font-size:24px; margin:25px auto;">'. lethe_record_not_found .'</div>'); # This area can be designed by your style.		
		}else{
			$myconn->query("UPDATE ". db_table_pref ."newsletter_subscribers SET activated=1,active=1 WHERE sub_code='". mysql_prep($id) ."'") or die(mysqli_error());
			echo('<div style="width:500px; text-align:center; font-size:24px; margin:25px auto;">'. lethe_your_e_mail_address_verified_successfully .'<br><br>'. lethe_thank_you .'</div>'); # This area can be designed by your style.
		}
		}
	}
elseif($pos==3){ # Web View
	$opData = $myconn->query("SELECT * FROM ". db_table_pref ."newsletters WHERE newsletter_id='". mysql_prep($id) ."' AND web_view=1") or die(mysqli_error());
	if(mysqli_num_rows($opData)==0){
		echo(lethe_record_not_found);
	}else{
		$opDataRs = $opData->fetch_assoc();
		$styleWEB = $opDataRs['details'];
		
		$find_str = array('{subscriber_name}','{subscriber_mail}','{subscriber_phone}','{subscriber_company}');
		$repl_str = array('','','','');
		$styleWEB = str_replace($find_str,$repl_str,$styleWEB); # Dynamic Values
	
		$styleWEB = str_replace('{subscriber_name}','',$styleWEB); # Dynamic Values
		$styleWEB = str_replace('{subscriber_mail}','',$styleWEB); # Dynamic Values
		$styleWEB = preg_replace('#\{?(unsubscribe_link@)(.*?)\}#','click unsubscribe link on your mailbox',$styleWEB);
		$styleWEB = preg_replace('#\{?(rss_link@)(.*?)\}#','<a href="'. relDocs(LETHEPATH) .'/lethe.newsletter.php?pos=4' .'">$2</a>',$styleWEB);
		$styleWEB = preg_replace('#\{?(newsletter_link@)(.*?)\}#','<a href="'. relDocs(LETHEPATH) .'/lethe.newsletter.php?pos=3&amp;id='.$id .'">$2</a>',$styleWEB);
		$styleWEB = preg_replace('#\{?(verify_link@)(.*?)\}#','click verify link on your mailbox',$styleWEB);
		$styleWEB = preg_replace_callback('#\{?(track_link@)(.*?)(@)(.*?)\}#',
										create_function(
											'$matches',
											'return \'<a href="'. relDocs(LETHEPATH) .'/lethe.tracker.php?pos=1&amp;id='.$id .'&amp;redURL=\'. urlencode($matches[4]) .\'" target="_blank">\'. $matches[2] .\'</a>\';'
										)
										,$styleWEB);
		$styleWEB = shortFormatter($styleWEB);
		$myconn->query("UPDATE ". db_table_pref ."newsletters SET view_hit=view_hit+1 WHERE newsletter_id='". mysql_prep($id) ."'") or die(mysqli_error());
		echo($styleWEB);
	}
	$opData->free();
}
elseif($pos==4){ # RSS View
	header ("Content-type: text/xml");
	$rss_title = lethe_newsletter_site_name;
    $rssfeed = '<?xml version="1.0" encoding="UTF-8"?>';
    $rssfeed .= '<rss version="2.0">';
    $rssfeed .= '<channel>';
    $rssfeed .= '<title>'. rss_filter($rss_title) .'</title>';
    $rssfeed .= '<link>http://'. set_site_url .'</link>';
    $rssfeed .= '<description>'. rss_filter($rss_title . ' RSS feed') .'</description>';
    $rssfeed .= '<language>en_EN</language>';
    $rssfeed .= '<copyright>Copyright (C) '. date("Y") .' artlantis.net</copyright>';
	
    $query = "SELECT ID,web_view,subject,add_date FROM ". db_table_pref ."newsletters WHERE web_view=1 ORDER BY ID Desc";
    $result = $myconn->query($query) or die ("Could not execute query");
 
    while($row = $result->fetch_assoc()) {
	 
        $rssfeed .= '<item>';
        $rssfeed .= '<title>' . rss_filter($row['subject']) . '</title>';
        $rssfeed .= '<link>'. relDocs(LETHEPATH).'/lethe.newsletter.php?pos=3&amp;id='.$row['ID'] .'</link>';
        $rssfeed .= '<pubDate>' . date("D, d M Y H:i:s O", strtotime($row['add_date'])) . '</pubDate>';
        $rssfeed .= '</item>';
    }
 
    $rssfeed .= '</channel>';
    $rssfeed .= '</rss>';
 
    echo $rssfeed;
	$result->free();
}
$myconn->close();
die();
?>