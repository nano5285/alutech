<div class="g-row">
	<div class="g-col col1">
		<?php if( $this->pagination->create_links() ): ?>
		<div class="pagination pagination-top">
			<?php echo $this->pagination->create_links();?>
		</div>
		<?php endif; ?>

		<h1><i class="fa fa-cube"></i> <?php echo $this->lang->line('blocks'); ?> <span class="total-items">(<?php echo $total; ?>)</span></h1>

		<?php $this->load->view('admin/includes/notifications'); ?>

		<?php $lang_array = $this->config->item('lang_desc'); ?>
		<table style="width:100%" class="table">
			<thead>
				<tr>
					<th><?php echo $this->lang->line('identifier')?></th>
					<th class="title"><?php echo $this->lang->line('title')?></th>
					<?php if(count($lang_array) > 1): ?><th class="lang"><?php echo $this->lang->line('language'); ?></th><?php endif; ?>
					<th class="icons">&nbsp;</th>
				</tr>
			</thead>
			<tbody>	
				<?php $i = 0; ?>
				<?php foreach($query as $row): ?>
				<tr id="item_<?php echo $row->id;?>"<?php if($i % 2 == 0): ?> class="alt"<?php endif; ?>>
					<td class="title"><a href="<?php echo base_url() . ADMIN_URL; ?>/blocks/edit/<?php echo $row->id?>"><?php echo $row->identifier?></a></td>
					<td><?php echo $row->title?>&nbsp;</td>
					<?php if(count($lang_array) > 1): ?><td class="lang"><?php echo $row->lang?></td><?php endif; ?>
					<td class="icons">
						<a href="<?php echo base_url() . ADMIN_URL; ?>/blocks/edit/<?php echo $row->id?>" class="edit"><i class="fa fa-pencil-square-o"></i></a> 
						<?php if ($this->session->userdata('group_id') == 4):?>
							<a href="<?php echo base_url() . ADMIN_URL; ?>/blocks/delete/<?php echo $row->id?>" class="delete" id="del_<?php echo $row->id;?>"><i class="fa fa-trash-o"></i></a>
						<?php endif; ?>
					</td>
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

	<aside class="g-col sidebar-right">
		<div class="container fixed">
			<?php $this->load->view('admin/includes/filter'); ?>
		</div>
	</aside>
</div>	