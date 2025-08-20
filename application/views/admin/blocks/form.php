<?php echo form_open(); ?>

<div class="g-row">
	<div class="g-col col1">
		<h1><i class="fa fa-cube"></i> <?php echo $this->lang->line('new_block'); ?></h1>
		<?php $this->load->view('admin/includes/notifications'); ?>
		<?php $this->load->view('admin/includes/title'); ?>

		<?php if ($this->session->userdata('group_id') == 4):?>
			<?php $this->load->view('admin/includes/identifier'); ?>
		<?php else: ?>
			<input type="hidden" name="identifier" value="<?php if($query): ?><?php echo $query->identifier;?><?php endif; ?>">
		<?php endif; ?>

		<p>
		<?php
		if (!$query) {
			// if new block form is opened... checked
			echo form_wysiwyg("content", true, htmlspecialchars_decode( set_value('content') ));
		} else {
			// else if edited block...
			if ($query->editor == 1) {
				// if editor is enabled, checked
				echo form_wysiwyg("content", true, htmlspecialchars_decode( $query->content . set_value('content') ));
			} else {
				// if editor is disabled, unchecked
				echo '<textarea name="content" cols="30" rows="10">'.$query->content . set_value('content').'</textarea>';
				echo form_wysiwyg("content", false);
			}
		}
		?>
		</p>
	</div>

	<aside class="g-col sidebar-right">
		<div class="container fixed">
			<div class="sidebar-content">
				<?php $this->load->view('admin/includes/languages'); ?>		

				<!-- Only Super Admin can enable or disable editor -->
				<?php if ($this->session->userdata('group_id') == 4):?>
				<p class="field-toggle-editor">
					<input type="checkbox" name="use_editor" id="use_editor" value="1" <?php if(!$query || $query->editor == 1):?>checked<?php endif; ?>>
					<label for="use_editor"><?php echo $this->lang->line('toggle_editor'); ?></label>	
				</p>
				<?php endif; ?>					
			</div>
			<?php $this->load->view('admin/includes/buttons'); ?>
		</div>
	</aside>
</div>
<?php echo form_close(); ?>	

<script>
	$(function() { 
		$('#use_editor').change(function() { 
			if ($(this).attr('checked')) { 
				CKEDITOR.replace('content', cfg); 
			} else { 
				CKEDITOR.instances.content.destroy(); 
			}	
		}); 
	});
</script>