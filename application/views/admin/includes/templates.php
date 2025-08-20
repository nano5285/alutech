<?php if(isset($templates) AND $templates): ?>
	<p class="field-template">
		<label for="template"><?php echo $this->lang->line('template'); ?></label>
		<select id="template" name="template">
			<?php if(isset($allow_none) AND $allow_none): ?>
				<option value="default">-</option>
			<?php endif; ?>
			<?php foreach ($templates as $key => $value): ?>
			<option <?php if ($query):?><?php if ($query->tpl == $key):?>selected="selected"<?php endif; ?><?php endif; ?> value="<?php echo $key;?>" <?php echo set_select('template',$key); ?>><?php echo $value;?></option>
			<?php endforeach; ?>
		</select>
	</p>
<?php else: ?>
	<input type="hidden" name="template" value="default">
<?php endif; ?>