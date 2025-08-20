<?php echo form_open(); ?>
<div class="g-row">
	<div class="g-col col1">
		<h1><i class="fa fa-th-list"></i> <?php echo $this->lang->line('new_menu'); ?></h1>
		<?php $this->load->view('admin/includes/notifications'); ?>
		<table class="table table-menu-items">
			<thead>
				<tr>
					<th class="checkbox delete-item">
						<?php echo $this->lang->line('delete');?>
					</th>
					<th class="title"><?php echo $this->lang->line('title');?></th>
					<th><?php echo $this->lang->line('url');?></th>
					<th class="checkbox published"><?php echo $this->lang->line('published');?></th>
					<th class="checkbox blank"><?php echo $this->lang->line('blank');?></th>
				</tr>
			</thead>
			<tbody>
				<?php $i = 0 ;?>
				<?php foreach($query as $row): ?>
				<tr id="item_<?php echo $row->id;?>"<?php if($i % 2 == 0): ?> class="alt"<?php endif; ?>>
					<td class="checkbox delete-item">
						<input type="hidden" name="menu_item[<?php echo $row->id;?>][id]" value="<?php echo $row->id; ?>">
						<input name="menu_item[<?php echo $row->id;?>][delete]" id="menu_item_delete_<?php echo $i; ?>" type="checkbox" class="delete-checkbox" value="1" />
						<label for="menu_item_delete_<?php echo $i; ?>">&nbsp;</label>
					</td>
					<td><input name="menu_item[<?php echo $row->id;?>][title]" type="text" value="<?php echo $row->title; ?>" /></td>
					<td><input name="menu_item[<?php echo $row->id;?>][url]" type="text" value="<?php echo $row->url; ?>" /></td>
					<td class="checkbox published">
						<input name="menu_item[<?php echo $row->id; ?>][status]" id="menu_item_status_<?php echo $i; ?>" type="checkbox" value="1"<?php if ($row->status == 1): ;?> checked="checked"<?php endif;?> />
						<label for="menu_item_status_<?php echo $i; ?>">&nbsp;</label>
					</td>
					<td class="checkbox blank">
						<input name="menu_item[<?php echo $row->id; ?>][target]" id="menu_item_target_<?php echo $i; ?>" type="checkbox" value="1"<?php if ($row->target == 1): ;?> checked="checked"<?php endif;?> />
						<label for="menu_item_target_<?php echo $i; ?>">&nbsp;</label>
					</td>
				</tr>
				<?php $i++ ;?>
				<?php endforeach;?>
				<tr>
					<td>&nbsp;</td>
					<td><input name="title" class="focus" type="text" value="<?php echo set_value('title'); ?>" placeholder="<?php echo $this->lang->line('new_item_title'); ?>" /></td>
					<td><input name="url" type="text" value="<?php echo set_value('url'); ?>" placeholder="<?php echo $this->lang->line('new_item_url'); ?>" /></td>
					<td class="checkbox blank">
						<input name="status" id="status" type="checkbox" value="1" checked />
						<label for="status">&nbsp;</label>
					</td>
					<td class="checkbox blank">
						<input name="target" type="checkbox" id="target" value="1" />
						<label for="target">&nbsp;</label>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<aside class="g-col sidebar-right">
		<div class="container fixed">
			<?php $this->load->view('admin/includes/buttons', array('exclude' => array('save_edit', 'save_new'))); ?>
			<div class="buttons">
				<a class="button" style="" href="<?php echo base_url() . ADMIN_URL; ?>/menuitems/reorder/<?php echo $this->uri->segment(3);?>/"><?php echo $this->lang->line('change_order');?></a>
			</div>
		</div>
	</aside>
</div>
<?php echo form_close(); ?>	