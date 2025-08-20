<?php echo form_open(); ?>
<div class="g-row">
	<div class="g-col col1">
		<h1><i class="fa fa-th-list"></i> <?php echo $this->lang->line('change_order');?></h1>
		<?php $this->load->view('admin/includes/notifications'); ?>

		<p class="info" style="margin-bottom:20px;"><i class="fa fa-info-circle"></i> <?php echo $this->lang->line('change_order_help');?></p>	

		<script src="<?php echo $this->config->item('admin_assets_path');?>js/jquery.mjs.nestedSortable.js"></script>
		<script>
		$(document).ready(function() {
			$('#item_list').nestedSortable({
				handle: 'div',
				items:'li',
				toleranceElement: '> div',
				stop: function(i) {
					$.post("<?php echo base_url(); ?>reorder_<?php echo $this->uri->segment(2);?>/", { items: $("#item_list").nestedSortable('toArray'), menu_id: <?php echo $this->uri->segment(4); ?>, <?php echo $this->config->item('csrf_token_name'); ?>: $('input[name="ci_token"]').val() });				   
				}
			});
		});
		</script>
	
		<?php 
		echo menu(array('menu_id' => $this->uri->segment(4), 'menu_attr' => 'id="item_list" class="reorder"', 'item_id' => 'item_', 'layout' => 'ol', 'link' => false, 'before_item' => '<div>', 'after_item' => '</div>')); 
		?>

	</div>

	<aside class="g-col sidebar-right">
		<div class="container fixed">
			<div class="buttons">
				<a class="button" href="<?php echo base_url() . ADMIN_URL; ?>/menuitems/<?php echo $this->uri->segment(4); ?>" class="button2"><?php echo $this->lang->line('back');?></a>
			</div>
		</div>
	</aside>
</div>	
<?php echo form_close(); ?>	