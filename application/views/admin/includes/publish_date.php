<?php if(isset($config) AND $config): ?>
	<p class="field-date">
		<label for="date"><?php echo $this->lang->line('publish_date'); ?></label>
		<input type="text" name="date" id="date" class="datepicker" readonly="readonly" value="<?php if($query): ?><?php echo $query->date;?><?php else: ?><?php echo $date; ?><?php endif; ?><?php echo set_value('date'); ?>">
	</p>
<?php else: ?>
	<input type="hidden" name="date" id="date" class="datepicker" readonly="readonly" value="<?php if($query): ?><?php echo $query->date;?><?php else: ?><?php echo $date; ?><?php endif; ?><?php echo set_value('date'); ?>">
<?php endif; ?>