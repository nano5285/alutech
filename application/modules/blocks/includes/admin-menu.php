<?php if ($this->session->userdata('group_id') >= 3):?>
<li>
	<a href="<?php echo base_url() . ADMIN_URL; ?>/blocks"><i class="fa fa-cube"></i><?php echo $this->lang->line('blocks'); ?></a>
	<?php if ($this->session->userdata('group_id') == 4):?><a href="<?php echo base_url() . ADMIN_URL; ?>/blocks/create" class="add"><i class="fa fa-plus-circle"></i></a><?php endif; ?>
</li>
<?php endif; ?>