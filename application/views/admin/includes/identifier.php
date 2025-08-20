<p class="field-identifier field-error">
	<?php echo form_error('identifier'); ?>
	<label for="identifier"><?php echo $this->lang->line('identifier');?></label>
	<input type="text" id="identifier" name="identifier" value="<?php if($query): ?><?php echo $query->identifier?><?php endif; ?><?php echo set_value('identifier'); ?>">
</p>