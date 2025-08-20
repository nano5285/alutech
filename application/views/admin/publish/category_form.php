<?php echo form_open_multipart(); ?>
<input type="hidden" name="action" id="action" value="<?php echo $this->uri->segment(4); ?>">
<div class="g-row">
	<div class="g-col col1">
		<h1><i class="fa fa-bullhorn"></i> <?php echo $this->lang->line('new_category'); ?></h1>
		<?php $this->load->view('admin/includes/notifications'); ?>
		<?php $this->load->view('admin/includes/title'); ?>
		<?php $this->load->view('admin/includes/slug'); ?>
		<?php $this->load->view('admin/includes/content', array('config' => $this->config->item('publish_category_content'))); ?>

		<?php if($this->config->item('publish_category_image')): ?>
		<div class="container">
			<table class="table table-images">
				<thead>
					<tr>
						<?php if($query && $image_name): ?>
							<th width="80">&nbsp;</th>
						<?php endif; ?>
						<th><?php echo $this->lang->line('image'); ?></th>
						<th><?php echo $this->lang->line('title'); ?> (title tag)</th>
						<th><?php echo $this->lang->line('description'); ?> (alt tag)</th>
						<?php if($image_name && $this->uri->segment(5) != ''): ?>
						<th class="checkbox delete-item"><?php echo $this->lang->line('delete'); ?></th>
						<?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<tr class="alt">
						<?php if($query && $image_name): ?>
						<td align="center">			
							<a class="fancybox" href="<?php echo base_url(); ?><?php echo $this->config->item('upload_folder'); ?><?php echo $image_name; ?>">
								<img title="<?php echo $image_name; ?>" src="<?php echo base_url(); ?><?php echo $this->config->item('upload_folder'); ?><?php echo $image_name; ?>" height="60">
							</a>
						</td>
						<?php endif; ?>
						<td>
							<div class="image-upload">
								<input type="hidden" name="image_id" value="<?php echo $image_id; ?>">
								<input type="hidden" name="image_name" value="<?php echo $image_name; ?>">
								<input type="file" id="file" name="userfile" size="60" />
								<div class="file-info">max: <?php echo $this->config->item('upload_max_size'); ?> kb (<?php echo $this->config->item('upload_allowed_types'); ?>)</div>
							</div>
						</td>
						<td><input type="text" name="image_title" value="<?php if($query): ?><?php echo $image_title; ?><?php endif; ?><?php echo set_value('image_title'); ?>"></td>
						<td><input type="text" name="image_alt" value="<?php if($query): ?><?php echo $image_alt; ?><?php endif; ?><?php echo set_value('image_alt'); ?>"></td>
						<?php if($image_name && $this->uri->segment(5) != ''): ?>
						<td class="checkbox delete-item">
							<input type="checkbox" id="delete_image" name="delete_image" value="1">
							<label for="delete_image">&nbsp;</label>
						</td>
						<?php endif; ?>
					</tr>
				</tbody>
			</table>
		</div>
		<?php else: ?>
			<input type="hidden" name="image_title" value="<?php if($query): ?><?php echo $query->image_title; ?><?php endif; ?>">
			<input type="hidden" name="image_alt" value="<?php if($query): ?><?php echo $query->image_alt; ?><?php endif; ?>">
		<?php endif; ?>
	</div>

	<aside class="g-col sidebar-right">
		<div class="container fixed">
			<div class="sidebar-content">
				<?php $this->load->view('admin/includes/languages'); ?>
				<?php $lang_array = $this->config->item('lang_desc'); ?>
				<?php $this->load->view('admin/includes/status'); ?>
				<?php $this->load->view('admin/includes/templates', array('templates' => $this->config->item('publish_templates'), 'allow_none' => TRUE)); ?>
				<p class="field-position field-error">
					<?php echo form_error('position'); ?>
					<label for="position"><?php echo $this->lang->line('position'); ?></label>
					<input type="text" name="position" id="position" value="<?php if($query): ?><?php echo $query->position;?><?php endif; ?><?php echo set_value('position'); ?>">
				</p>
				<?php $this->load->view('admin/includes/publish_date', array('config' => $this->config->item('publish_category_publish_date'))); ?>
				<?php $this->load->view('admin/includes/seo', array('config' => $this->config->item('publish_category_seo'))); ?>										
			</div>
			<?php $this->load->view('admin/includes/buttons'); ?>
		</div>
	</aside>
</div>	

<script>
// Disable auto slug creation if updating existing category
$(function() {
	if ($("#action").val() != "edit") {
		$('#title').syncTranslit({destination:"slug"});
	}
});
</script>
<?php echo form_close(); ?>	