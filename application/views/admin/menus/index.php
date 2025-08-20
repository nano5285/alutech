<div class="g-row">
	<div class="g-col col1">
		<?php if( $this->pagination->create_links() ): ?>
		<div class="pagination pagination-top">
			<?php echo $this->pagination->create_links();?>
		</div>
		<?php endif; ?>

		<h1><i class="fa fa-th-list"></i> <?php echo $this->lang->line('menus'); ?> <span class="total-items">(<?php echo $total; ?>)</span></h1>

		<?php $this->load->view('admin/includes/notifications'); ?>

		<?php $lang_array = $this->config->item('lang_desc'); ?>
		<table class="table">
			<thead>
				<tr>
					<th class="icons icons-left"></th>
					<th><?php echo $this->lang->line('identifier')?></th>
					<th class="title"><?php echo $this->lang->line('title')?></th>
					<?php if(count($lang_array) > 1): ?><th class="lang"><?php echo $this->lang->line('language'); ?></th><?php endif; ?>
					<?php if ($this->session->userdata('group_id') == 4):?>
					<th class="icons" style="width:80px;">&nbsp;</th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>	
				<?php $i = 0; ?>
				<?php foreach($query as $row): ?>
				<tr id="item_<?php echo $row->id;?>"<?php if($i % 2 == 0): ?> class="alt"<?php endif; ?>>
					<td class="icons icons-left">
						<a href="<?php echo base_url() . ADMIN_URL; ?>/menuitems/<?php echo $row->id?>/" title="<?php echo $this->lang->line('edit_menu_items'); ?>"><i class="fa fa-bars"></i></a>
						<a href="<?php echo base_url() . ADMIN_URL; ?>/menuitems/reorder/<?php echo $row->id;?>" class="reorder-icon" id="del_<?php echo $row->id;?>" title="<?php echo $this->lang->line('change_order'); ?>"><i class="fa fa-sort"></i></a>						
					</td>
					<td><a href="<?php echo base_url() . ADMIN_URL; ?>/menuitems/<?php echo $row->id?>/" title="<?php echo $this->lang->line('change_order'); ?>"><?php echo $row->identifier?></a></td>
					<td><?php echo $row->title; ?>&nbsp;</td>
					<?php if(count($lang_array) > 1): ?><td class="lang" align="center"><?php echo $row->lang?></td><?php endif; ?>
					<?php if ($this->session->userdata('group_id') == 4):?>
					<td class="icons">
						<a href="<?php echo base_url() . ADMIN_URL; ?>/menus/edit/<?php echo $row->id?>" class="edit" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil-square-o"></i></a> 
						<a href="<?php echo base_url() . ADMIN_URL; ?>/menus/delete/<?php echo $row->id?>" class="delete" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash-o"></i></a>
					</td>
					<?php endif; ?>
				</tr>
				<?php $i++; ?>
				<?php endforeach; ?>
			</tbody> 
		</table>

		<?php if( $this->pagination->create_links() ): ?>
		<div class="pagination pagination-bottom">
			<?php echo $this->pagination->create_links();?>
		</div>
		<?php endif; ?>
	</div>
</div>	