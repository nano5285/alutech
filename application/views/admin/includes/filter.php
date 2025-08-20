<?php echo form_open('', array('name' => 'filter_form', 'class' => 'search-form', 'method' => 'get')); ?>	
<div class="sidebar-content">
	<p class="field-filter-q toggle-label">
		<label for="q"><?php echo $this->lang->line('enter_search_term'); ?></label>
		<input type="text" name="q" id="q" class="focus" value="<?php echo $this->input->get('q'); ?>">
	</p>

	<?php $lang_array = $this->config->item('lang_desc'); ?>
	<?php if(count($lang_array) > 1): ?>
	<p class="field-lang">
		<label for="lang"><?php echo $this->lang->line('language'); ?></label>
		<select name="lang" id="lang">
			<option value=""><?php echo $this->lang->line('all_languages'); ?></option>
			<?php foreach ($this->config->item('lang_desc') as $key => $value): ?>
			<option value="<?php echo $key;?>"<?php echo set_select('lang',$key); ?><?php if($this->input->get('lang') == $key): ?> selected="selected"<?php endif; ?>><?php echo $value;?> (<?php echo $key; ?>)</option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php endif; ?>
</div>
<div class="buttons">
	<?php echo form_button(array('class' => 'button2', 'type' => 'submit', 'content' => $this->lang->line('filter'))); ?>
</div>
<?php echo form_close(); ?>