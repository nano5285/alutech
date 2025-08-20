<?php
# +------------------------------------------------------------------------+
# | Artlantis CMS Solutions                                                |
# +------------------------------------------------------------------------+
# | Lethe Newsletter & Mailing System                                      |
# | Copyright (c) Artlantis Design Studio 2014. All rights reserved.       |
# | Version       1.1.5                                                    |
# | Last modified 06-03-2015 08.48.08                                      |
# | Email         developer@artlantis.net                                  |
# | Web           http://www.artlantis.net                                 |
# +------------------------------------------------------------------------+

# General Settings
define('set_site_url','http://www.alutech.hr/'); # Site URL
define('set_rss_url','http://alutech.hr/newsletter/lethe.newsletter.php?pos=4'); # RSS URL
define('set_only_verified',1); # Select Only Verified Mails
define('set_only_active',1); # Select Only Active Mails
define('set_random_load',1); # Select Random Mails
define('set_send_verification',1); # Send Verification Mail
define('set_unique_code','2036tsei27wuerk9'); # Unique Code
define('set_template_permission',1); # Template Permission
define('set_subgrp_permission',1); # Subscribe Group Permission
define('set_subscr_permission',1); # Subscriber Permission
define('set_exmp_imp_permission',1); # Import / Export Permission
define('set_newsletter_permission',1); # Newsletter Permission
define('set_autoresponder_permission',1); # Autoresponder Permission
define('set_after_user_delete',1); # After User Delete
define('set_def_timezone','Europe/Zagreb'); # Default Timezone
define('set_after_unsubscribe',2); # After Unsubscribe
/* ************************************* */
date_default_timezone_set(set_def_timezone);
define('set_lethe_powered','<div id="lethe-powered">Lethe PHP Newsletter & Mailing System v.1.1.6<br>Powered by <a href="http://www.artlantis.net" target="_blank">Artlantis Design Studio</a></div>'); # Powered
/* ************************************* */
?>
