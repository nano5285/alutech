<?php $this->lang->load('english'); ?>
<!DOCTYPE html>
<html class="login">
<head>
	<meta charset="utf-8" />
	<title><?php echo $this->config->item('admin_title'); ?></title>
	<!-- main css -->
	<link href="<?php echo $this->config->item('admin_assets_path');?>css/style.css" rel="stylesheet" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
	<!--[if lt IE 9]><script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script><![endif]-->

	<!-- jquery -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
	<script src="<?php echo $this->config->item('admin_assets_path');?>/js/jquery-ui-timepicker-addon.js"></script>
	<script src="<?php echo $this->config->item('admin_assets_path');?>js/modernizr.js"></script>

	<!-- transliterate script -->
	<script src="<?php echo $this->config->item('admin_assets_path');?>js/jquery.synctranslit.js"></script>

	<!-- initialize scripts -->
	<script src="<?php echo $this->config->item('admin_assets_path');?>js/chosen.min.js"></script>

	<!-- fancybox -->
	<script src="<?php echo $this->config->item('admin_assets_path');?>js/jquery.fancybox.pack.js"></script>
	<link href="<?php echo $this->config->item('admin_assets_path');?>css/jquery.fancybox.css" rel="stylesheet" type="text/css" />

	<!-- initialize scripts -->
	<script src="<?php echo $this->config->item('admin_assets_path');?>js/initialize.js"></script>
</head>
<body class="page-login">
	<noscript>
		<style>#main { display:none; }</style>
		<div id="nojs"><?php echo $this->lang->line('js_disabled');?></div>
	</noscript>

	<div class="login-wrapper">
		

		<div class="container login-container">		
		<div style="text-align:center" ><a href="<?php echo base_url() . ADMIN_URL; ?>"><img src="<?php echo base_url(); ?>images/logo-znak.png" /></a></div>
			<?php if($error): ?>
				<span class="info error"><i class="fa fa-exclamation-circle"></i> <?php echo $error; ?></span>
			<?php endif; ?>

			<!-- form section -->
			<?php echo form_open(ADMIN_URL . '/login', array('name' => 'login')); ?>
				<p class="field-username">
					<label for="username"><?php echo $this->lang->line('username'); ?></label><input class="focus" name="username" id="username" type="text" value="<?php echo set_value('username'); ?>">
					<?php echo form_error('username'); ?>
				</p>
				<p class="field-password">
					<label for="password"><?php echo $this->lang->line('password'); ?></label><input name="password" id="password" type="password" value="">
					<?php echo form_error('password'); ?>
				</p>
				<button class="button2 login-button" type="submit"><?php echo $this->lang->line('login'); ?></button>
			<?php echo form_close(); ?>		
			<!-- /form section -->
		</div>

		<footer style="text-align:center" >v<?php echo $this->config->item('cms_version'); ?></footer>
	</div>
</body>
</html>