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
/* Default Recording Filter */
function mysql_prep( $value ) {
	global $myconn;
	$value = trim($value);
	$value = str_replace('<','&lt;',$value);
	$value = str_replace('>','&gt;',$value);
	$value = str_replace("'",'&#39;',$value);
	$value = str_replace('"','&quot;',$value);
	$value = str_replace('<!--','&lt;!--',$value);
	$magic_quotes_active = get_magic_quotes_gpc();
	$new_enough_php = function_exists( "mysqli_real_escape_string" ); // i.e. PHP >= v4.3.0
	if( $new_enough_php ) { // PHP v4.3.0 or higher
	// undo any magic quote effects so mysql_real_escape_string can do the work
	if( $magic_quotes_active ) { $value = stripslashes( $value ); }
	$value = $myconn->real_escape_string( $value );
	} else { // before PHP v4.3.0
	// if magic quotes aren't already on then add slashes manually
	if( !$magic_quotes_active ) { $value = addslashes( $value ); }
	// if magic quotes are active, then the slashes already exist
	}
	return $value;
}

/* HTML Recording Filter */
function mysql_prep2( $value ) {
	global $myconn;
	$value = trim($value);
	$value = str_replace("'",'&#39;',$value);
	$value = str_replace("where","&#119here",$value);
	$value = str_replace("drop","&#100rop",$value);
	$value = str_replace("select","&#115elect",$value);
	$value = str_replace("union","&#117nion",$value);
	$value = str_replace("like","&#108ike",$value);
	$value = str_replace("update","&#117pdate",$value);
	$value = str_replace("delete","&#100elete",$value);
	$value = str_replace("insert","&#105nsert",$value);
	$value = str_replace("show","&#115how",$value);
	$value = str_replace("alter","&#97lter",$value);
	$magic_quotes_active = get_magic_quotes_gpc();
	$new_enough_php = function_exists( "mysql_real_escape_string" ); // i.e. PHP >= v4.3.0
	if( $new_enough_php ) { // PHP v4.3.0 or higher
	// undo any magic quote effects so mysql_real_escape_string can do the work
	if( $magic_quotes_active ) { $value = stripslashes( $value ); }
	$value = $myconn->real_escape_string( $value );
	} else { // before PHP v4.3.0
	// if magic quotes aren't already on then add slashes manually
	if( !$magic_quotes_active ) { $value = addslashes( $value ); }
	// if magic quotes are active, then the slashes already exist
	}
	return $value;
}

/* HTML Template Filter */
function mysql_prep3( $value ) {
	global $myconn;
	$value = trim($value);
	$magic_quotes_active = get_magic_quotes_gpc();
	$new_enough_php = function_exists( "mysql_real_escape_string" ); // i.e. PHP >= v4.3.0
	if( $new_enough_php ) { // PHP v4.3.0 or higher
	// undo any magic quote effects so mysql_real_escape_string can do the work
	if( $magic_quotes_active ) { $value = stripslashes( $value ); }
	$value = $myconn->real_escape_string( $value );
	} else { // before PHP v4.3.0
	// if magic quotes aren't already on then add slashes manually
	if( !$magic_quotes_active ) { $value = addslashes( $value ); }
	// if magic quotes are active, then the slashes already exist
	}
	return $value;
}

/* Login Filter */
function loginFilter($v){
	global $myconn;
	$v = trim($v);
	$v = $myconn->real_escape_string($v);	
	return $v;
	}
	
/* Add Zero */
function addZero($v){
	$v = trim($v);
	if(strlen($v)<2){
		$v = "0".$v;
	}else{
		$v = $v;
	}
	return $v;
	}
/* Get Percent */
function percentage($val1, $val2, $precision) 
{
	if($val1!=0 && $val2!=0){
		$res = round( ($val1 / $val2) * 100, $precision );
	}else{
		$res=0;
	}
	
	return $res;
}

/* Current URL */
function curPageURL() {
	 $pageURL = 'http';
	 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	 $pageURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 }
	 return $pageURL;
}

/* Current Host URL */
function curHostURL() {
	 $pageURL = 'http';
	 if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {$pageURL .= "s";}
	 $pageURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"];
	 }
	 return $pageURL;
}

/* Current Page */
function curPageName() {
	$currentUrl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
	if($_SERVER['QUERY_STRING']){$currentUrl = $currentUrl . '?' . $_SERVER['QUERY_STRING'];}
	return $currentUrl;
}

/* CMS Help Link Function */
function cmsHelp($v){
	$cmsHelpBody = '<a href="'. cms_help_source . $v .'" tabindex="-1" data-fancybox-type="iframe" data-fancybox-width="500" data-fancybox-height="500" class="fancybox glyphicon glyphicon-question-sign help-link"></a>';
	return $cmsHelpBody;
}

/* E-Mail Validation */
function mailVal($v){
		if (!filter_var($v, FILTER_VALIDATE_EMAIL)) {
			return false;
		}
		else {return true;}
	}
	
/* URL Validation */
function urlVal($v){
		if (!filter_var($v, FILTER_VALIDATE_URL)) {
			return false;
		}
		else {return true;}
	}
	
/* Character Limits */
function chrCont($v,$min,$max){  
	$l = strlen($v);
    if($l >= $min && $l <= $max){
		return false;
		} else {return true;}
} 

/* Data Checker */
function chkData($qry){
	global $myconn;
	$getQD = $myconn->query($qry) or die(mysqli_error());
	$optCount = mysqli_num_rows($getQD);
	$getQD->free();
	if($optCount==0){
		return true;
		}
	else{
		return false;
		}
	}
	
/* Encryptor */
function encr($v){
	$v = sha1($v);
	$v = md5($v);
	$v = sha1($v);
	$v = md5($v);
	return $v;
	}
	
/* Selectbox and Checkbox Marker */
function formSelector($f1,$f2,$ty){
	# f1 - First Option
	# f2 - Second Option
	# ty - Form Type (0=Selectbox, 1=Checkbox, 2=Radio, 3=Link, 4=Required)
	if($ty==0){
		$cc = ' selected';
		}
	elseif($ty==1){
		$cc = ' checked';
		}
	elseif($ty==2){
		$cc = ' checked';
		}
	elseif($ty==3){
		$cc = ' class="selected-link"';
	}
	elseif($ty==4){
		$cc = ' required';
	}
	if($f1==$f2){return $cc;} else {return '';}
	}
	
/* Date / Time Optimization */
function setMyDate($tarih,$bicim){
	
	$myDate = date_create($tarih);
	
	# 1 - 2012-03-24 17:45:12
	# 2 - 24/03/2012 17:45:12
	# 3 - 24/03/12
	# 4 - 5:45pm on Saturday 24th March 2012
	# 5 - 24.03.2012
	# 6 - 24.03.2012 17:45:12
	# 7 - 09 March 2012 Sat
	# 8 - October 23, 2013, 12:43 am
	
	if($bicim==1){return date_format($myDate, 'Y-m-d H:i:s');}
	if($bicim==2){return date_format($myDate, 'd/m/Y H:i:s');}
	if($bicim==3){return date_format($myDate, 'd/m/Y');}
	if($bicim==4){return date_format($myDate, 'g:ia \o\n l jS F Y');}
	if($bicim==5){return date_format($myDate, 'd.m.Y');}
	if($bicim==6){return date_format($myDate, 'd.m.Y H:i:s');}
	if($bicim==7){
		
		$strToTimeDate = strtotime($tarih);
		return date("d",$strToTimeDate) . ' ' . dateLang(date("n",$strToTimeDate),2,1) . ' ' . date("Y",$strToTimeDate) . ' ' . dateLang(date("N",$strToTimeDate),1,1);
		
		}
	if($bicim==8){
		
		$strToTimeDate = strtotime($tarih);
		return dateLang(date("n",$strToTimeDate),2,1) . ' ' . date("d",$strToTimeDate) . ', ' . date("Y",$strToTimeDate) . ', ' . date("g:i a",$strToTimeDate);
		
		}
	
	}
	
/* Check Date Format */
	
function validateMysqlDate( $date ){ 
    if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $date, $matches)) { 
        if (checkdate($matches[2], $matches[3], $matches[1])) { 
            return true; 
        } 
    } 
    return false; 
} 

/* Check Date Format for Forms */
function validate_Date($mydate,$format = 'DD-MM-YYYY') {

    if ($format == 'YYYY-MM-DD') list($year, $month, $day) = explode('-', $mydate);
    if ($format == 'YYYY/MM/DD') list($year, $month, $day) = explode('/', $mydate);
    if ($format == 'YYYY.MM.DD') list($year, $month, $day) = explode('.', $mydate);

    if ($format == 'DD-MM-YYYY') list($day, $month, $year) = explode('-', $mydate);
    if ($format == 'DD/MM/YYYY') list($day, $month, $year) = explode('/', $mydate);
    if ($format == 'DD.MM.YYYY') list($day, $month, $year) = explode('.', $mydate);

    if ($format == 'MM-DD-YYYY') list($month, $day, $year) = explode('-', $mydate);
    if ($format == 'MM/DD/YYYY') list($month, $day, $year) = explode('/', $mydate);
    if ($format == 'MM.DD.YYYY') list($month, $day, $year) = explode('.', $mydate);       

    if (is_numeric($year) && is_numeric($month) && is_numeric($day))
        return checkdate($month,$day,$year);
    return false;           
} 
	
/* Data Counter */
function cntData($qry){
	global $myconn;
	$getQD = $myconn->query($qry);
	$optCount = mysqli_num_rows($getQD);
	$getQD->free();
	return $optCount;
	}
		
/* Sanitize String */
function sanitize($url)
{
$url = trim($url);
$find = array('<b>', '</b>');
$url = str_replace ($find, '', $url);
$url = preg_replace('/<(\/{0,1})img(.*?)(\/{0,1})\>/', 'image', $url);
$find = array(' ', '&amp;amp;amp;quot;', '&amp;amp;amp;amp;', '&amp;amp;amp;', '\r\n', '\n', '/', '\\', '+', '<', '>');
$url = str_replace ($find, '-', $url);
$find = array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ë', 'Ê','?','?','?');
$url = str_replace ($find, 'e', $url);
$find = array('í', 'y', 'ì', 'î', 'ï', 'I', 'Y', 'Í', 'Ì', 'Î', 'Ï','İ','ı','?','?','?','?','?');
$url = str_replace ($find, 'i', $url);
$find = array('ó', 'ö', 'Ö', 'ò', 'ô', 'Ó', 'Ò', 'Ô','ø','Ø','?','?','?');
$url = str_replace ($find, 'o', $url);
$find = array('á', 'ä', 'â', 'à', 'â', 'Ä', 'Â', 'Á', 'À', 'Â','?','?','a','a','?','?');
$url = str_replace ($find, 'a', $url);
$find = array('?','ß');
$url = str_replace ($find, 'b', $url);
$find = array('?','?');
$url = str_replace ($find, 'f', $url);
$find = array('?','?');
$url = str_replace ($find, 'g', $url);
$find = array('?','?');
$url = str_replace ($find, 'h', $url);
$find = array('?','?');
$url = str_replace ($find, 'k', $url);
$find = array('?','?');
$url = str_replace ($find, 'l', $url);
$find = array('?');
$url = str_replace ($find, 't', $url);
$find = array('µ','?');
$url = str_replace ($find, 'm', $url);
$find = array('?','?');
$url = str_replace ($find, 'n', $url);
$find = array('?');
$url = str_replace ($find, 'p', $url);
$find = array('?','?');
$url = str_replace ($find, 'ps', $url);
$find = array('?','?');
$url = str_replace ($find, 'd', $url);
$find = array('?','?');
$url = str_replace ($find, 't', $url);
$find = array('?');
$url = str_replace ($find, 'y', $url);
$find = array('?');
$url = str_replace ($find, 'x', $url);
$find = array('?','?');
$url = str_replace ($find, 'z', $url);
$find = array('ú', 'ü', 'Ü', 'ù', 'û', 'Ú', 'Ù', 'Û','?','?');
$url = str_replace ($find, 'u', $url);
$find = array('ç', 'Ç','?','?');
$url = str_replace ($find, 'c', $url);
$find = array('?', '?','ş','Ş','š','?','??');
$url = str_replace ($find, 's', $url);
$find = array('?', '?','ğ','Ğ');
$url = str_replace ($find, 'g', $url);
$find = array('/[^A-Za-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
$repl = array('', '-', '');
$url = preg_replace ($find, $repl, $url);
$url = str_replace ('--', '-', $url);
$url = strtolower($url);
return $url;
}

/* Username Validation */
function nicknameVal($v){
	
	$allowed = array(".","_"); // you can add here more value, you want to allow.
	
	if(ctype_alnum(str_replace($allowed, '', $v ))) { 
		if(strlen($v)<5 or strlen($v)>15){
			return false;
			} else {
				return true;
				}
	} else {
		return false;
		}
	
	}

/* Is Active */
function isActive($v){
	if($v==1){
		return '<span class="label label-success"><span class="glyphicon glyphicon-ok"></span></span>';
	}else{
		return '<span class="label label-danger"><span class="glyphicon glyphicon-remove"></span></span>';
	}
}

/* Subscriber Data */
function getSubscriber($v,$p){

	# 0 - Get Subscriber Group Name
	# 1 - Get Subscriber Mail/Name/SubCode by ID for array
	# 2 - Get Unsubscribe Group ID
	# 3 - Get Subscriber Details by ID for array (Short Codes)
	# 4 - Get Subscriber ID by subCode
	# 5 - Get Subscriber details by ID

	global $myconn;
	
	if($p==0){
		$subData = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE ID=". intval($v) ."") or die(mysqli_error());
		if(mysqli_num_rows($subData)==0){return lethe_ungrouped;}else{$subDataRs = mysqli_fetch_assoc($subData);return $subDataRs['group_name'];}
		$subData->free();
	}
	else if($p==1){
		$subData = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE ID=". intval($v) ."") or die(mysqli_error());
		if(mysqli_num_rows($subData)==0){
			return false;
		}else{
			$subDataRs = mysqli_fetch_assoc($subData);
			$subDataArr = array();
			$subDataArr[] = $subDataRs['sub_mail'];
			$subDataArr[] = $subDataRs['sub_name'];
			$subDataArr[] = $subDataRs['sub_code'];
			$subDataArr[] = array(0=>array(
												'subscriber_name'=>$subDataRs['sub_name'],
												'subscriber_mail'=>$subDataRs['sub_mail'],
												'subscriber_phone'=>$subDataRs['sub_phone'],
												'subscriber_company'=>$subDataRs['sub_company']
												));
			return $subDataArr;
		}
		$subData->free();
	}
	else if($p==2){
		$subData = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_groups WHERE unsubscriber=1") or die(mysqli_error($myconn));
		if(mysqli_num_rows($subData)==0){return 0;}else{$subDataRs = $subData->fetch_assoc();return $subDataRs['ID'];}
		$subData->free();
	}
	else if($p==3){
		$subData = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE ID=". intval($v) ."") or die(mysqli_error());
		if(mysqli_num_rows($subData)==0){
			$subData->free();
			return false;
		}else{
			
			$subDataRs = $subData->fetch_assoc();
						$subDetailArr = array();
											
			$subData->free();
			return $subDetailArr;
		}
	}
	else if($p==4){
		$subData = $myconn->query("SELECT ID,sub_code FROM ". db_table_pref ."newsletter_subscribers WHERE sub_code='". $v ."'") or die(mysqli_error());
		if(mysqli_num_rows($subData)==0){return false;}else{$subDataRs = mysqli_fetch_assoc($subData);return $subDataRs['ID'];}
		$subData->free();
	}
	else if($p==5){
		$subData = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_subscribers WHERE ID=". intval($v) ."") or die(mysqli_error());
		if(mysqli_num_rows($subData)==0){
			return false;
		}else{
			$subDataRs = mysqli_fetch_assoc($subData);
			$subDataArr =  array(0=>array(
												'subscriber_name'=>$subDataRs['sub_name'],
												'subscriber_mail'=>$subDataRs['sub_mail'],
												'subscriber_phone'=>$subDataRs['sub_phone'],
												'subscriber_company'=>$subDataRs['sub_company']
												));
			return $subDataArr;
		}
		$subData->free();
	}

}

# ** Newsletter Data
function getNewsletter($v,$p){

	global $myconn;
	# 0 - Get Newsletter ID by unique
	
	if($p==0){
		$subData = $myconn->query("SELECT ID,newsletter_id FROM ". db_table_pref ."newsletters WHERE newsletter_id='". $v ."'") or die(mysqli_error());
		$subDataRs = mysqli_fetch_assoc($subData);
		return $subDataRs['ID'];
		$subData->free();
	}

}

# ** User Data
function getUser($v,$p){
	
	global $myconn;
	# 0 - Primary User ID
	# 1 - User Daily Limit Checker
	
	if($p==0){
		$opUser = $myconn->query("SELECT ID,primary_user FROM ". db_table_pref ."users") or die();
		if(mysqli_num_rows($opUser)==0){
			return false; # There no found user
		}else{
			$opUserRs = mysqli_fetch_assoc($opUser);
			return $opUserRs['ID'];
		}
		$opUser->free();
	}
	else if($p==1){
	
		$opUser = $myconn->query("SELECT ID,sendLimit,dailySent,resetDate,admin_mode FROM ". db_table_pref ."users WHERE ID=". intval($v) ."") or die();
		if(mysqli_num_rows($opUser)==0){
			return false; # There no found user
		}else{
			$opUserRs = mysqli_fetch_assoc($opUser);
			if($opUserRs['admin_mode']==1){
				return true; # No limits for Super Admin
			}else{
				if($opUserRs['resetDate']>date('Y-m-d H:i:s')){
					if($opUserRs['dailySent']>=$opUserRs['sendLimit']){
						return false;
					}else{
						return true;
					}
				}
				else{ # Reset and return true
					$myconn->query("UPDATE ". db_table_pref ."users SET dailySent=0,resetDate = DATE_ADD(NOW() , INTERVAL 1 DAY) WHERE ID=". intval($v) ."") or die();
					return true;
				}
			}
		}
		$opUser->free();
	}
	# **
	
}

# ** Short Code Formatter
function shortFormatter($v){
	global $myconn;
	$opCodes = $myconn->query("SELECT * FROM ". db_table_pref ."newsletter_codes") or die(mysqli_error());
	while($opCodesRs = mysqli_fetch_assoc($opCodes)){
		$v = str_replace('{'. $opCodesRs['lethe_code'] .'}',$opCodesRs['lethe_code_val'],$v);
	}
	$opCodes->free();
	
		$find_str = array('{curr_year}','{curr_date}','{curr_month}');
		$repl_str = array(date('Y'),date('d.m.Y'),date('F'));
		$v = str_replace($find_str,$repl_str,$v);
	
	return $v;
}

# ** Mailbox Controllers
	# Mailbox Connector
		function pop3_login($host,$port,$user,$pass,$folder="INBOX",$ssl='') 
		{ 
			// $ssl=($ssl==false)?"/novalidate-cert":""; 
			return (imap_open("{"."$host:$port/pop3". $ssl .""."}$folder",$user,$pass,OP_SILENT)); 
		} 
	# Mailbox Statistic
		function pop3_stat($connection)        
		{ 
			$check = imap_mailboxmsginfo($connection); 
			return ((array)$check); 
		} 
	# Mailbox Post List
		function pop3_list($connection,$message="") 
		{ 
			 if(!isset($result)){$result=null;}
			if ($message) 
			{ 
				$range=$message; 
			} else { 
				$MC = imap_check($connection); 
				$range = "1:".$MC->Nmsgs; 
			} 
			$response = imap_fetch_overview($connection,$range);
			foreach ($response as $msg)$result[$msg->msgno]=(array)$msg; 
				return $result; 
		} 
	# Mailbox Post Header Fetch
		function pop3_retr($connection,$message) 
		{ 
			return(imap_fetchheader($connection,$message,FT_PREFETCHTEXT)); 
		} 
	# Mailbox Post Remover
		function pop3_dele($connection,$message) 
		{ 
			return(imap_delete($connection,$message) or false); 
		} 
		
# ** Unset Variables
function unset_all_vars($a)
{ foreach($a as $key => $val)
  { unset($GLOBALS[$key]); }
  return serialize($a); }
  
# ** Timezones
function timezone_list() {
	global $myconn;
	$opLocList = $myconn->query("SELECT * FROM ". db_table_pref ."loc_timezones ORDER BY loc_gmt ASC") or die(mysqli_error());
	$timezones = array();
	while($opLocListRs = mysqli_fetch_assoc($opLocList)){
		$timezones[] = $opLocListRs;
	}
	$opLocList->free();
return $timezones;
}

# ** Get Position Style
function getMyActPos($p){
							if($p==0){ # Paused, This wont be run
								$pos_status = '<span class="glyphicon glyphicon-pause" data-toggle="tooltip" data-placement="top" title="'. lethe_waiting .'"></span>';
								}
							else if($p==1){ # Loaded, This waiting for launch date
								$pos_status = '<span style="color:#4D90FE" class="glyphicon glyphicon-time" data-toggle="tooltip" data-placement="top" title="'. lethe_loaded .'"></span>';
								}
							else if($p==2){ # Process, Sending operation started
								$pos_status = '<span style="color:#009B57" class="glyphicon glyphicon-send" data-toggle="tooltip" data-placement="top" title="'. lethe_processing .'"></span>';
								}
							else if($p==3){ # Stop, Sending operation stopped
								$pos_status = '<span style="color:#DA4631" class="glyphicon glyphicon-stop" data-toggle="tooltip" data-placement="top" title="'. lethe_stopped .'"></span>';
								}
							else if($p==4){ # Complete, Sending operation is done
								$pos_status = '<span style="color:#009756" class="glyphicon glyphicon-ok" data-toggle="tooltip" data-placement="top" title="'. lethe_completed .'"></span>';
								}
							return $pos_status;
}

function getMyActProg($p){
	if($p==0){
		$pos_status = ' progress-bar-warning';
	}
	else if($p==1){
		$pos_status = ' progress-bar-warning';
	}
	else if($p==2){
		$pos_status = ' progress-bar-primary';
	}
	else if($p==3){
		$pos_status = ' progress-bar-danger';
	}
	else if($p==4){
		$pos_status = ' progress-bar-success';
	}
	return $pos_status;
}

function rss_filter($v){
	$cF = array('&quot;','&#39;','&lt;','&gt;','{subscriber_name}','{subscriber_mail}','{subscriber_phone}','{subscriber_company}');
	$cR = array('"',"'",'<','>','','','','');
	$rss_str = str_replace($cF, $cR, $v);
	$rss_str = '<![CDATA['.$rss_str.']]>';
	$rss_str = shortFormatter($rss_str);
	return $rss_str;
}

/* Rel Document Builder */
function relDocs($filePath){

        $filePath = str_replace('\\','/',$filePath);
        $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true : false;
        $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $_SERVER['SERVER_PORT'];
        $stringPort = ((!$ssl && ($port == '80' || $port=='8080')) || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        $filePath = preg_replace('/(\/+)/','/',$filePath);
		$fileUrl = str_replace($_SERVER['DOCUMENT_ROOT'] ,$protocol . '://' . $host . $stringPort, $filePath); 
		
		return $fileUrl;

}

?>