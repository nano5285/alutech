<?php 
# +------------------------------------------------------------------------+
# | Artlantis CMS Solutions                                                |
# +------------------------------------------------------------------------+
# | Lethe Newsletter & Mailing System                                      |
# | Copyright (c) Artlantis Design Studio 2014. All rights reserved.       |
# | Version       1.1.6                                                    |
# | Last modified 11.03.14                                                 |
# | Email         developer@artlantis.net                                  |
# | Web           http://www.artlantis.net                                 |
# +------------------------------------------------------------------------+
ob_start();header("Content-Type: text/html; charset=UTF-8"); 

define('LETHEPATH',dirname(dirname(__FILE__)));

$errText = '';
define('sets_default_language','en');
//$start_time = microtime(true);
if(!isset($_COOKIE["siteLang"]) || is_null($_COOKIE["siteLang"])){$cnsLang = sets_default_language;} else {$cnsLang = $_COOKIE["siteLang"];}

#*************** DEMO VERSION ****************************
define('demo_mode',0); # Turn false 
define('beta_mode',false); 
define('lethe_newsletter_site_name','Lethe PHP Newsletter & Mailing System v.1.1.6');
define('lethe_newsletter_version','Lethe Newsletter v.1.1.6 (http://www.artlantis.net/)');
//error_reporting(demo_mode ? 0 : E_STRICT);
error_reporting(0);
#*************** LOAD AREA LANGUAGES *********************
$admin_area = true;

#*************** DATABASE INFORMATIONS *******************
    require_once(LETHEPATH .'/inc/lethe.db.config.php');

#*************** ADMIN PATH *********************
define('lethe_export_path',LETHEPATH.'/'.lethe_admin_path.'/lethe_export');

#*************** LANGUAGE START **************************
if(file_exists(LETHEPATH .'/language/inc_languages.php')){
require_once(LETHEPATH .'/language/inc_languages.php');
	$site_dil_key = $_SLNG_CNF[$cnsLang]['lkey']; 
} 
else 
{die('Error: Language file error has occurred!');}
define('cms_lang_key',$_SLNG_CNF[$cnsLang]['lkey']);

#*************** STATIC ARRAYS ***************************
$lethe_mail_method = array('SMTP','PHP Mail');
$lethe_mail_secure = array('Off','SSL','TLS');
$lethe_short_codes = array('subscriber_name',
						   'subscriber_mail',
						   'subscriber_phone',
						   'subscriber_company',
						   'unsubscribe_link@TEXT@STYLE',
						   'newsletter_link@TEXT@STYLE',
						   'track_link@TEXT@URL@STYLE',
						   'rss_link@TEXT',
						   'curr_date',
						   'curr_month',
						   'curr_year',
						   );
$lethe_status_mode = array(lethe_pending,lethe_loaded,lethe_process,lethe_stopped,lethe_completed);
$lethe_form_models = array(lethe_form,lethe_link,lethe_custom);
$lethe_ar_models = array(lethe_after_subs_cription,lethe_after_unsubs_cription,lethe_specific_date,lethe_special_days);
$lethe_ar_dates = array(lethe_minute,lethe_hour,lethe_day,lethe_week,lethe_month,lethe_year);
$lethe_date_field_models = array('mm/dd/yy'=>'mm/dd/yy',
								 'yy-mm-dd'=>'ISO 8601 - yy-mm-dd');
	$lethe_weekList = array(
		'normal'=>array(lethe_week_long_sunday,
						lethe_week_long_monday,
						lethe_week_long_tuesday,
						lethe_week_long_wednesday,
						lethe_week_long_thursday,
						lethe_week_long_friday,
						lethe_week_long_saturday),
		'short'=>array(lethe_week_short_sunday,
						lethe_week_short_monday,
						lethe_week_short_tuesday,
						lethe_week_short_wednesday,
						lethe_week_short_thursday,
						lethe_week_short_friday,
						lethe_week_short_saturday)
	);
	$lethe_monthList = array(
		'normal'=>array(null,lethe_month_long_january,
						lethe_month_long_february,
						lethe_month_long_march,
						lethe_month_long_april,
						lethe_month_long_may,
						lethe_month_long_june,
						lethe_month_long_july,
						lethe_month_long_august,
						lethe_month_long_september,
						lethe_month_long_october,
						lethe_month_long_november,
						lethe_month_long_december),
		'short'=>array(null,lethe_month_short_january,
						lethe_month_short_february,
						lethe_month_short_march,
						lethe_month_short_april,
						lethe_month_short_may,
						lethe_month_short_june,
						lethe_month_short_july,
						lethe_month_short_august,
						lethe_month_short_september,
						lethe_month_short_october,
						lethe_month_short_november,
						lethe_month_short_december)
	);

# ** DB Connection
$myconn=new mysqli(db_host,db_login,db_pass,db_name) or die(mysqli_error());
$myconn->set_charset('utf8');
?>