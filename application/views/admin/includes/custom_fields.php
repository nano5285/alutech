<?php if(isset($custom_fields) AND $custom_fields): ?>
<!-- Custom fields -->
<div class="container">
	<h2><i class="fa fa-cubes"></i>&nbsp; <?php echo $this->lang->line('custom_fields'); ?></h2>
	<div class="cnt">
		<?php foreach($custom_fields as $field => $value): ?>
		<?php if( !isset($value['limit_id']) OR isset($value['limit_id']) AND in_array($this->uri->segment(4), $value['limit_id']) ): ?>
			<?php
			$field_id = '';	

			if ($query) {
				$field_value = $this->commonmodel->get_post_meta(array('post_id' => $this->uri->segment($uri_segment), 'module' => $module, 'identifier' => $field))->row();
				$field_id = ($field_value) ? $field_value->id : '';
			}
			?>
			<p class="add-block">
				<?php if($value['field_type'] != 'checkbox'): ?>
					<label for="field-<?php echo $field; ?>"><?php echo $value['title']; ?> (<?php echo $this->lang->line('identifier'); ?>: <?php echo $field; ?>)</label>				
				<?php endif; ?>
				<input type="hidden" name="custom_field[<?php echo $field; ?>][id]" value="<?php if($query AND $field_value): ?><?php echo $field_value->id; ?><?php endif; ?><?php echo set_value('custom_field['.$field.'][id]'); ?>">
				<input type="hidden" name="custom_field[<?php echo $field; ?>][identifier]" value="<?php echo $field; ?>">

				<?php if($value['field_type'] == 'checkbox'): ?>
					<?php $checked = ($query AND $field_value && $field_value->content == 1) ? 'checked' : ''; ?>
					<input type="hidden" name="custom_field[<?php echo $field; ?>][editor]" value="0">
					<input type="checkbox" id="field-<?php echo $field; ?>" name="custom_field[<?php echo $field; ?>][content]" value="1"<?php echo set_checkbox('custom_field['.$field.'][content]',1); ?><?php echo $checked; ?>>
					<label for="field-<?php echo $field; ?>"><?php echo $value['title']; ?> (<?php echo $this->lang->line('identifier'); ?>: <?php echo $field; ?>)</label>				
				<?php endif; ?>

				<?php if($value['field_type'] == 'textarea'): ?>
					<input type="hidden" name="custom_field[<?php echo $field; ?>][editor]" value="0">
					<textarea name="custom_field[<?php echo $field; ?>][content]" id="field-<?php echo $field; ?>"><?php if($query AND $field_value): ?><?php echo $field_value->content;?><?php endif; ?><?php echo set_value('custom_field['.$field.'][content]'); ?></textarea>		
				<?php endif; ?>
				
				<?php if($value['field_type'] == 'simple_wysiwyg'): ?>
					<input type="hidden" name="custom_field[<?php echo $field; ?>][editor]" value="1">
					<?php $post_meta_content = ($query AND $field_value) ? $field_value->content . set_value('custom_field['.$field.'][content]') : set_value('custom_field['.$field.'][content]'); ?>
					<?php echo form_wysiwyg('custom_field['.$field.'][content]', true, htmlspecialchars_decode( $post_meta_content ), 'simple' ); ?>
				<?php endif; ?>

				<?php if($value['field_type'] == 'wysiwyg'): ?>
					<input type="hidden" name="custom_field[<?php echo $field; ?>][editor]" value="1">
					<?php $post_meta_content = ($query AND $field_value) ? $field_value->content . set_value('custom_field['.$field.'][content]') : set_value('custom_field['.$field.'][content]'); ?>
					<?php echo form_wysiwyg('custom_field['.$field.'][content]', true, htmlspecialchars_decode( $post_meta_content ), 'full' ); ?>
				<?php endif; ?>

				<?php if($value['field_type'] == 'text'): ?>
					<input type="hidden" name="custom_field[<?php echo $field; ?>][editor]" value="0">
					<input type="text" name="custom_field[<?php echo $field; ?>][content]" id="field-<?php echo $field; ?>" value="<?php if($query AND $field_value): ?><?php echo $field_value->content;?><?php endif; ?><?php echo set_value('custom_field['.$field.'][content]'); ?>">
				<?php endif; ?>
			</p>
		<?php endif; ?>
		<?php endforeach; ?>
		<div class="clear"></div>
	</div>
</div>
<!-- /Custom fields -->
<?php endif; ?>