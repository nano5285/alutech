<div class="buttons">
	<?php @$exclude = (isset($exclude) AND $exclude) ? $exclude : array(); ?>
	<?php if( !in_array("save", $exclude) ): ?>
		<button name="save" value="save" type="submit" class="button2"><i class="fa fa-floppy-o"></i>&nbsp; <?php echo $this->lang->line('save_list'); ?></button>
	<?php endif; ?>
	<?php if( !in_array("save_edit", $exclude) ): ?>
		<button name="save" value="save_edit" type="submit"><?php echo $this->lang->line('save_edit'); ?></button>
	<?php endif; ?>
	<?php if( !in_array("save_new", $exclude) ): ?>
		<button name="save" value="save_new" type="submit"><?php echo $this->lang->line('save_new'); ?></button>
	<?php endif; ?>
</div>