<?php if ($this->session->userdata('group_id') >= 3): ?>
<li>
	<a href="<?php echo base_url() . ADMIN_URL; ?>/menus"><i class="fa fa-th-list"></i><?php echo $this->lang->line('menus')?></a>
	<?php if ($this->session->userdata('group_id') == 4):?><a href="<?php echo base_url() . ADMIN_URL; ?>/menus/create" class="add"><i class="fa fa-plus-circle"></i></a><?php endif; ?>
</li>
<?php endif; ?>