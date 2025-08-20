<?php if ($this->session->userdata('group_id') >= 2): ?>
<li>
	<a href="<?php echo base_url() . ADMIN_URL; ?>/publish/posts" class="sub"><i class="fa fa-text-width"></i><?php echo $this->lang->line('publish');?></a>
	<a href="<?php echo base_url() . ADMIN_URL; ?>/publish/posts/create" class="add"><i class="fa fa-plus-circle"></i></a>
	<ul>
		<li>
			<a href="<?php echo base_url() . ADMIN_URL; ?>/publish/category" class="label"><?php echo $this->lang->line('categories');?></a>
			<a href="<?php echo base_url() . ADMIN_URL; ?>/publish/category/create" class="add"><i class="fa fa-plus-circle"></i></a>
		</li>
		<li>
			<a href="<?php echo base_url() . ADMIN_URL; ?>/publish/posts" class="label"><?php echo $this->lang->line('posts');?></a>
			<a href="<?php echo base_url() . ADMIN_URL; ?>/publish/posts/create" class="add"><i class="fa fa-plus-circle"></i></a>
		</li>
	</ul>
</li>
<?php endif; ?>