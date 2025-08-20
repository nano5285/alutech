<?php if ($this->session->userdata('group_id') >= 3): ?>
<li>
	<a href="<?php echo base_url() . ADMIN_URL; ?>/pages"><i class="fa fa-tags"></i><?php echo $this->lang->line('pages');?></a>
	<a href="<?php echo base_url(). ADMIN_URL; ?>/pages/create" class="add"><i class="fa fa-plus-circle"></i></a>
</li>
<?php endif; ?>