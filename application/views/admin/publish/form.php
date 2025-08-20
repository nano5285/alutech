<?php echo form_open_multipart(); ?>
<script>
$(function() {	
	$("#lang").change(function() {
		$("#lang option:selected").each(function () {	
			$.post("<?php echo base_url(); ?>get_admin_category_tree", { lang: $("#lang").val(), <?php echo $this->config->item('csrf_token_name'); ?>: $('input[name="ci_token"]').val()}, function(data) {
				$("#category").html(data);
				$("#category").trigger("chosen:updated");
			});	
		});
	});
});
</script>
<input type="hidden" name="action" id="action" value="<?php echo $this->uri->segment(4); ?>">

<div class="g-row">
	<div class="g-col col1">
		<h1><i class="fa fa-bullhorn"></i> <?php echo $this->lang->line('add_post'); ?></h1>
		<?php $this->load->view('admin/includes/notifications'); ?>
		<?php $this->load->view('admin/includes/title'); ?>
		<?php $this->load->view('admin/includes/slug'); ?>	
		<?php $this->load->view('admin/includes/excerpt', array('config' => $this->config->item('publish_excerpt'))); ?>
		<?php $this->load->view('admin/includes/content', array('config' => $this->config->item('publish_content'))); ?>

		<?php if($this->config->item('publish_images') == TRUE): ?>
			<div class="container container-select-images">
				<table class="table table-images">
					<thead>
						<tr>
							<th><?php echo $this->lang->line('image'); ?></th>
							<th><?php echo $this->lang->line('title'); ?> (title tag)</th>
							<th><?php echo $this->lang->line('description'); ?> (alt tag)</th>
						</tr>
					</thead>
					<tbody>
						<?php for($i = 1; $i <= $this->config->item('publish_image_fields'); $i++): ?>
						<tr<?php if($i % 2 == 1): ?> class="alt"<?php endif; ?>>
							<td class="image-file">
								<input type="file" name="userfile<?php echo $i; ?>" size="60" />
								<div class="file-info">max: <?php echo $this->config->item('upload_max_size'); ?> kb (<?php echo $this->config->item('upload_allowed_types'); ?>)</div>
							</td>
							<td class="image-title"><input type="text" name="image_title<?php echo $i; ?>"></td>
							<td class="image-alt"><input type="text" name="image_alt<?php echo $i; ?>"></td>
						</tr>
						<?php endfor; ?>
					</tbody>
				</table>
			</div>	
			<!-- /new images -->

			<!-- images -->
			<?php if($images): ?>
			<div class="container">
				<table class="table table-images">
					<thead>
						<tr>
							<th width="80">&nbsp;</th>
							<th><?php echo $this->lang->line('image'); ?></th>
							<th class="image-position"><?php echo $this->lang->line('position'); ?></th>
							<th class="image-title"><?php echo $this->lang->line('title'); ?> (title tag)</th>
							<th class="image-description"><?php echo $this->lang->line('description'); ?> (alt tag)</th>
							<th class="checkbox delete-item"><?php echo $this->lang->line('delete'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($images as $image): ?>
						<tr class="alt">				
							<td align="center">			
								<input type="hidden" name="image[<?php echo $image->id;?>][image_file]" value="<?php echo $image->name; ?>">
								<input type="hidden" name="image[<?php echo $image->id;?>][image_id]" value="<?php echo $image->id; ?>">
								<!--a class="fancybox" rel="gallery" href="<?php echo base_url(); ?><?php echo $this->config->item('upload_folder'); ?><?php echo $image->name; ?>">
									<img title="<?php echo $image->name; ?>" src="<?php echo base_url(); ?><?php echo $this->config->item('upload_folder'); ?><?php echo $image->name; ?>" height="60">
								</a-->
							</td>
							<td>
								<input type="file" name="new_image_file<?php echo $image->id; ?>" size="60" />
								<div class="file-info">max: <?php echo $this->config->item('upload_max_size'); ?> kb (<?php echo $this->config->item('upload_allowed_types'); ?>)</div>
							</td>
							<td class="image-position"><input type="text" name="image[<?php echo $image->id;?>][image_position]" value="<?php echo $image->position; ?><?php echo set_value('image_position'); ?>" style="text-align:center;"></td>
							<td class="image-title"><input type="text" name="image[<?php echo $image->id;?>][image_title]" value="<?php echo $image->title; ?><?php echo set_value('image_title'); ?>"></td>
							<td class="image-description"><input type="text" name="image[<?php echo $image->id;?>][image_alt]" value="<?php echo $image->alt; ?><?php echo set_value('image_alt'); ?>"></td>
							<td class="checkbox delete-item">
								<input id="delete_image[<?php echo $image->id;?>]" type="checkbox" value="1" name="image[<?php echo $image->id;?>][delete]">
								<label for="delete_image[<?php echo $image->id;?>]">&nbsp;</label>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>	
			<?php endif; ?>
		<?php endif; ?>
		<!-- images -->	

		<?php $this->load->view('admin/includes/custom_fields', array('custom_fields' => $this->config->item('publish_custom_fields'), 'uri_segment' => 5, 'module' => PUBLISH_M)); ?>		
	</div>

	<aside class="g-col sidebar-right">
		<div class="container fixed">
			<div class="sidebar-content">
				<?php $this->load->view('admin/includes/languages'); ?>
				<?php $this->load->view('admin/includes/status'); ?>
				<?php if($this->config->item('publish_featured_post') == TRUE): ?>
					<p>
						<input type="checkbox" name="featured" id="featured" value="1" <?php echo set_checkbox('featured','1'); ?><?php if ($query && $query->featured == 1): ?>checked="checked"<?php endif; ?>>
						<label for="featured"><?php echo $this->lang->line('featured');?></label></span>
					</p>
				<?php endif; ?>				
				<?php $this->load->view('admin/includes/publish_date', array('config' => $this->config->item('publish_date'))); ?>

				<p class="field-category">
					<label for="category"><?php echo $this->lang->line('category'); ?></label>
					<select id="category" name="category">
						<?php echo get_category_tree($query->lang, 0, $query->category); ?>
					</select>
				</p>
				<?php $this->load->view('admin/includes/author', array('config' => $this->config->item('publish_author'))); ?>	
				<?php $this->load->view('admin/includes/seo', array('config' => $this->config->item('publish_seo'))); ?>										
			</div>
			<?php $this->load->view('admin/includes/buttons'); ?>
		</div>
	</aside>
</div>
<?php echo form_close(); ?>