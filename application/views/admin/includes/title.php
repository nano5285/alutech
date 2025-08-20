<p class="field-title field-error toggle-label">
	<?php echo form_error('title'); ?>
	<label for="title"><?php echo $this->lang->line('title'); ?></label>
	<input class="focus" type="text" name="title" id="title" value="<?php if($query): ?><?php echo $query->title;?><?php endif; ?><?php echo set_value('title'); ?>">
</p>