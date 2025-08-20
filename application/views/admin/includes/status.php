<p class="field-status">
	<input id="status" name="status" type="checkbox" value="1" <?php echo set_checkbox('status','1'); ?><?php if ($query && $query->status == 1): ?>checked="checked"<?php endif; ?> />
	<label for="status"><?php echo $this->lang->line('published'); ?></label>
</p>