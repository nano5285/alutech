<p class="field-url clear">
	<?php echo form_error('slug'); ?>
	<label for="slug"><strong><?php echo $this->lang->line('url'); ?>:</strong> </label>
	<input type="text" name="slug" id="slug" value="<?php if($query): ?><?php echo $query->slug;?><?php endif; ?><?php echo set_value('slug'); ?>">
</p>