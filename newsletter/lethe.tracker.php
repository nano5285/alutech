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
if(!isset($_GET['pos']) || !is_numeric($_GET['pos'])){$pos=0;}else{$pos=intval($_GET['pos']);}
if(!isset($_GET['rcvid']) || !mailVal($_GET['rcvid'])){$rcvid='';}else{$rcvid=$_GET['rcvid'];}

if($pos==0){ # View Hit
if(isset($_GET['id']) && is_numeric($_GET['id'])){ # Newsletter ID
	if(cntData("SELECT * FROM ". db_table_pref ."open_tracker WHERE NID=". intval($_GET['id']) ." AND receiver_mail='". mysql_prep($rcvid) ."'")==0){
		$myconn->query("UPDATE ". db_table_pref ."newsletters SET view_hit=view_hit+1 WHERE ID=". intval($_GET['id']) ."") or die();
		//header('Location: '. set_site_url .'lethe.png');
		header("Content-type: image/gif");
		header("Content-length: 43");
		$fp = fopen("php://output","wb");
		fwrite($fp,"GIF89a\x01\x00\x01\x00\x80\x00\x00\xFF\xFF",15);
		fwrite($fp,"\xFF\x00\x00\x00\x21\xF9\x04\x01\x00\x00\x00\x00",12);
		fwrite($fp,"\x2C\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02",12);
		fwrite($fp,"\x44\x01\x00\x3B",4);
		fclose($fp);
		$myconn->query("INSERT INTO ". db_table_pref ."open_tracker (NID,receiver_mail) VALUES (". intval($_GET['id']) .",'". mysql_prep($rcvid) ."')") or die();
	}
} 
}else if($pos==1){ # Click Hit
	if(!isset($_GET['redURL']) || empty($_GET['redURL'])){$redURL=set_site_url;}else{$redURL=$_GET['redURL'];}
	if(isset($_GET['id']) || !empty($_GET['id'])){ # Newsletter ID
		$myconn->query("UPDATE ". db_table_pref ."newsletters SET click_hit=click_hit+1 WHERE newsletter_id='". mysql_prep($_GET['id']) ."'") or die();
	}
	header('Location:' . $redURL);
}
$myconn->close();
die();
?>