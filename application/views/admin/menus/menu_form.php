<?php echo form_open(); ?>
<div class="g-row">
	<div class="g-col col1">
		<h1><i class="fa fa-th-list"></i> <?php echo $this->lang->line('new_menu'); ?></h1>
		<?php $this->load->view('admin/includes/notifications'); ?>
		<?php $this->load->view('admin/includes/title'); ?>
		<?php $this->load->view('admin/includes/identifier'); ?>
	</div>

	<aside class="g-col sidebar-right">
		<div class="container fixed">
			<div class="sidebar-content">
				<?php $this->load->view('admin/includes/languages'); ?>	
			</div>
			<?php $this->load->view('admin/includes/buttons'); ?>
		</div>
	</aside>
</div>	
<?php echo form_close(); ?>	