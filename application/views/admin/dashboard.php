<div class="g-row">
	<div class="g-col dashboard-col1">
		<h1><i class="fa fa-tachometer"></i> <?php echo $this->lang->line('dashboard'); ?> <span class="total-items"></span></h1>

		<?php $this->load->view('admin/includes/notifications'); ?>
		
		<?php if ($this->session->userdata('group_id') >= 3):?><a href="<?php echo base_url() . ADMIN_URL; ?>/blocks" class="dashboard-button"><i class="fa fa-cube"></i><?php echo $this->lang->line('blocks'); ?></a><?php endif; ?>
		<?php if ($this->session->userdata('group_id') >= 3):?><a href="<?php echo base_url() . ADMIN_URL; ?>/menus" class="dashboard-button"><i class="fa fa-th-list"></i><?php echo $this->lang->line('menus'); ?></a><?php endif; ?>
		<?php if ($this->session->userdata('group_id') >= 3): ?><a href="<?php echo base_url() . ADMIN_URL; ?>/pages" class="dashboard-button"><i class="fa fa-tags"></i><?php echo $this->lang->line('pages'); ?></a><?php endif; ?>
		<?php if ($this->session->userdata('group_id') >= 2):?><a href="<?php echo base_url() . ADMIN_URL; ?>/publish/category" class="dashboard-button"><i class="fa fa-bullhorn"></i><?php echo $this->lang->line('categories');?></a><?php endif; ?>
		<?php if ($this->session->userdata('group_id') >= 2):?><a href="<?php echo base_url() . ADMIN_URL; ?>/publish/posts" class="dashboard-button"><i class="fa fa-text-width"></i><?php echo $this->lang->line('publish'); ?></a><?php endif; ?>
		<?php if ($this->session->userdata('group_id') >= 3): ?><a href="<?php echo base_url() . ADMIN_URL; ?>/users" class="dashboard-button"><i class="fa fa-users"></i><?php echo $this->lang->line('users'); ?></a><?php endif; ?>
		<?php if ($this->session->userdata('group_id') >= 4): ?><a href="<?php echo base_url() . ADMIN_URL; ?>/users" class="dashboard-button"><i class="fa fa-gears"></i><?php echo $this->lang->line('settings'); ?></a><?php endif; ?>
		<a href="<?php echo base_url() . ADMIN_URL; ?>/users/profile" class="dashboard-button"><i class="fa fa-user"></i><?php echo $this->lang->line('edit_profile'); ?></a>
		<a href="<?php echo base_url() . ADMIN_URL; ?>settings/translate" class="dashboard-button"><i class="fa fa-globe"></i><?php echo $this->lang->line('translate'); ?></a>
		<a href="<?php echo base_url() . ADMIN_URL; ?>" class="dashboard-button"><i class="fa fa-tachometer"></i><?php echo $this->lang->line('dashboard'); ?></a>
		<a href="<?php echo base_url() . ADMIN_URL; ?>/logout" class="dashboard-button"><i class="fa fa-sign-out"></i><?php echo $this->lang->line('logout'); ?></a>
		<a href="http://www.egomedia.hr/kontakt" target="_blank" class="dashboard-button"><i class="fa fa-question"></i>EGO MEDIA SUPPORT</a>
	</div>
</div>