<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title><?php echo $this->config->item('admin_title'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<!-- main css -->
	<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link href="<?php echo $this->config->item('admin_assets_path');?>css/style.css" rel="stylesheet" type="text/css" />
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<script src="<?php echo $this->config->item('admin_assets_path');?>js/modernizr.js"></script>
	<!--[if lt IE 9]><script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script><![endif]-->

	<!-- jquery -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
	<script src="<?php echo $this->config->item('admin_assets_path');?>/js/jquery-ui-timepicker-addon.js"></script>

	<!-- transliterate script -->
	<script src="<?php echo $this->config->item('admin_assets_path');?>js/jquery.synctranslit.js"></script>

	<!-- fancybox -->
	<script src="<?php echo $this->config->item('admin_assets_path');?>js/jquery.fancybox.pack.js"></script>
	<link href="<?php echo $this->config->item('admin_assets_path');?>css/jquery.fancybox.css" rel="stylesheet" type="text/css" />

	<!-- initialize scripts -->
	<script src="<?php echo $this->config->item('admin_assets_path');?>js/chosen.min.js"></script>

	<!-- initialize scripts -->
	<script src="<?php echo $this->config->item('admin_assets_path');?>js/initialize.js"></script>
	<script>
	function openDialog(obj, targetUrl) {
		$(obj).dialog({
			buttons : {
				"<?php echo $this->lang->line('confirm'); ?>" : function() {
					window.location.href = targetUrl;
				},
				"<?php echo $this->lang->line('cancel'); ?>" : function() {
					$(this).dialog("close");
				}
			}
		});
		$(obj).dialog("open");	
	}

	function openUserDialog(obj, targetUrl) {
		$(obj).dialog({
			buttons : {
				"<?php echo $this->lang->line('confirm'); ?>" : function() {
					altUser = $('input[name=user_radio]:checked');
					altUserId = altUser.val();
					altUser.change(function() {
						altUserId = $(this).val();
					});
					window.location.href = targetUrl + "/" + altUserId;
				},
				"<?php echo $this->lang->line('cancel'); ?>" : function() {
					$(this).dialog("close");
				}
			}
		});
		$(obj).dialog("open");	
	}
	</script>
</head>
<body>

<noscript>
	<style>#main { display:none; }</style>
	<div id="nojs"><?php echo $this->lang->line('js_disabled');?></div>
</noscript>

<div class="g-row" id="main">
	<aside class="g-col sidebar">
		<div class="sidebar-content fixed">
			<div style="padding:30px 50px;"><a href="<?php echo base_url() . ADMIN_URL; ?>"><img src="<?php echo base_url(); ?>images/logo-znak.png" /></a></div>
			<ul class="nav">
				<li class="profile">
					<a class="sub" href="<?php echo base_url() . ADMIN_URL; ?>/users/profile">
						<?php if( $this->session->userdata('name') ): ?>
							<?php echo $this->session->userdata('name'); ?>
						<?php else: ?>
							<?php echo $this->session->userdata('username'); ?>
						<?php endif; ?>
					</a>
					<ul>
						<li><a href="<?php echo base_url() . ADMIN_URL; ?>/logout"><i class="fa fa-sign-out"></i><?php echo $this->lang->line('logout'); ?></a></li>
					</ul>
				</li>
				<li><a href="<?php echo base_url() . ADMIN_URL; ?>"><i class="fa fa-tachometer"></i><?php echo $this->lang->line('dashboard'); ?></a></li>
				<?php 
					$module_path = APPPATH . 'modules/';
					$menu_path = '/includes/admin-menu.php';
					
					foreach ($this->config->item('cms_modules') as $module) {
						if (file_exists($module_path . $module) AND file_exists($module_path . $module . $menu_path)) {
							include ($module_path . $module . $menu_path);
						}
					}
				?>
			</ul>

			<div class="cms-info">
				<p>v<strong><?php echo $this->config->item('cms_version'); ?></strong></p>
			</div>
		</div>	
	</aside>

	<section class="g-col main">
		<section class="main-content">
			<!-- confirmation box -->
			<div id="dialog" class="confirm-dialog" title="<?php echo $this->lang->line('confirmation_title'); ?>">
				<?php echo $this->lang->line('delete_confirmation'); ?>
			</div>

			<?php echo @$content; ?>
		</section>
	</section>
</div>

</body>
</html>