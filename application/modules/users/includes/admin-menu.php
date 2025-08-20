<?php if ($this->session->userdata('group_id') >= 3):?>
<li>
	<a href="<?php echo base_url() . ADMIN_URL; ?>/users"><i class="fa fa-users"></i><?php echo $this->lang->line('users'); ?></a>
	<a href="<?php echo base_url() . ADMIN_URL; ?>/users/create" class="add"><i class="fa fa-plus-circle"></i></a>
</li>
<?php endif; ?>