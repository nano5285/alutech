<?php echo form_open(); ?>

<!-- If updating the page, disable auto slug creation (jQuery) -->
<input type="hidden" name="action" id="action" value="<?php echo $this->uri->segment(3); ?>" />	

<div class="g-row">
	<div class="g-col col1">
		<h1><i class="fa fa-tags"></i> <?php echo $this->lang->line('add_new_page'); ?></h1>
		<?php $this->load->view('admin/includes/notifications'); ?>
		<?php $this->load->view('admin/includes/title'); ?>
		<?php $this->load->view('admin/includes/slug'); ?>
		<?php $this->load->view('admin/includes/content', array('config' => $this->config->item('pages_content'))); ?>

		<?php $this->load->view('admin/includes/custom_fields', array('custom_fields' => $this->config->item('pages_custom_fields'), 'uri_segment' => 4, 'module' => PAGES_M)); ?>
	</div>

	<aside class="g-col sidebar-right">
		<div class="container fixed">
			<div class="sidebar-content">
				<?php $this->load->view('admin/includes/languages'); ?>

				<?php $this->load->view('admin/includes/templates', array('templates' => $this->config->item('templates'))); ?>
				<?php $this->load->view('admin/includes/seo', array('config' => $this->config->item('pages_seo'))); ?>
			</div>
			<?php $this->load->view('admin/includes/buttons'); ?>
		</div>
	</aside>
</div>	
<?php echo form_close(); ?>