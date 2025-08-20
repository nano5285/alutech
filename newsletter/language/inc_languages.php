<?php
# +------------------------------------------------------------------------+
# | Artlantis Sirius PHP Language Editor                                   |
# +------------------------------------------------------------------------+
# | Copyright (c) Artlantis Design Studio 2013. All rights reserved.       |
# | Version       2.3                                                      |
# | Last modified 23.07.14                                                 |
# | Email         contact@artlantis.net                                    |
# | Web           http://www.artlantis.net                                 |
# +------------------------------------------------------------------------+


	   # Language Folder
	   define('sirius_language_root',dirname(__FILE__));
	   
	   # Default Language Checker
	   if(!isset($cnsLang) || empty($cnsLang)){$cnsLang='en';}
	   
	   # Check Admin Area
	   if(!isset($admin_area)){$admin_area=false;}
	   
	   # Check Cookies
	   if(isset($_COOKIE['siteLang']) && !is_null($_COOKIE['siteLang'])){$cnsLang = $_COOKIE['siteLang'];}
	   
	   # Load Settings
	   $LNG = array();
	   $_SLNG_CNF = array();
	   						
					/* Fetch Language Configs */
					if ($handle = opendir(sirius_language_root)) {
						$blacklist = array('.', '..','_inactive','index.html');
						while (false !== ($file = readdir($handle))) {
							if (!in_array($file, $blacklist) && is_dir(sirius_language_root.'/'.$file)) {
								include_once(sirius_language_root.'/'.$file.'/lang.conf.php');
							}
						}
					}
	   
/* Load Language */

include_once(sirius_language_root . '/' . $_SLNG_CNF[$cnsLang]['lfol'] . '/lang.php');

/* Define Languages */

foreach ($LNG as $key=>$val){if ( !defined($key) ){define($key, $val );}}
?>