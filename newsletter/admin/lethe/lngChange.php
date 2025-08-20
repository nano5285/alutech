<?php if(empty($_GET['l'])){$l="en";}else{$l=$_GET['l'];} setcookie("siteLang", $l, time() + (10 * 365 * 24 * 60 * 60),"/"); 
header('Location:index.php');?>