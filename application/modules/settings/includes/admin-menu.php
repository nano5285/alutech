<?php if ($this->session->userdata('group_id') >= 4):?>
<li>
	<a href="<?php echo base_url() . ADMIN_URL; ?>/settings/translate" class="sub"><i class="fa fa-gears"></i><?php echo $this->lang->line('settings'); ?></a>
	<ul>
		<li><a href="<?php echo base_url() . ADMIN_URL; ?>/settings/translate"><?php echo $this->lang->line('translate'); ?></a></li>
		<?php if($this->config->item('cache_enable')): ?>
			<li><a href="<?php echo base_url() . ADMIN_URL; ?>/settings/clear_cache"><?php echo $this->lang->line('clear_cache'); ?></a></li>
		<?php endif; ?>
	</ul>
</li>
<?php endif; ?>