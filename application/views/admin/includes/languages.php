<?php $lang_array = $this->config->item('lang_desc'); ?>
<?php if(count($lang_array) > 1): ?>
	<p class="field-lang">
		<label for="lang"><?php echo $this->lang->line('language'); ?></label>
		<select id="lang" name="lang">
			<?php foreach ($this->config->item('lang_desc') as $key => $value): ?>
			<option<?php if ($query && $query->lang == $key):?> selected="selected"<?php endif; ?> value="<?php echo $key;?>"<?php echo set_select('lang',$key); ?>><?php echo $value;?> (<?php echo $key; ?>)</option>
			<?php endforeach; ?>
		</select>
	</p>
<?php else: ?>
	<input type="hidden" name="lang" value="<?php echo $this->config->item('language_abbr'); ?>">
<?php endif; ?>	