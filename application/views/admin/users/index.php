<!-- /confirmation box -->
<div id="user-dialog" class="user-confirm-dialog" title="<?php echo $this->lang->line('confirmation_title'); ?>">
	<p style="padding:0 0 9px 0;"><?php echo $this->lang->line('delete_confirmation_user'); ?>:</p>
	<div class="radio-wrapper">
	<?php $r = 1; ?>
		<?php foreach($query as $row): ?>		
			<p class="row-user row-user-<?php echo $row->id; ?>">
				<input type="radio" name="user_radio" value="<?php echo $row->id; ?>" id="userradio<?php echo $r; ?>"<?php if($row->id == $this->session->userdata('user_id')): ?> checked<?php endif; ?>>
				<label for="userradio<?php echo $r; ?>">
				<?php if ($row->name == false): ?>
					<?php echo $row->username; ?>
				<?php else: ?>
					<?php echo $row->name;?> (<?php echo $row->username; ?>)
				<?php endif; ?>
				</label>
			</p>
			<?php $r++; ?>
		<?php endforeach; ?>
	</div>
</div>

<div class="g-row">
	<div class="g-col col1">
		<?php if( $this->pagination->create_links() ): ?>
		<div class="pagination pagination-top">
			<?php echo $this->pagination->create_links();?>
		</div>
		<?php endif; ?>

		<h1><i class="fa fa-users"></i> <?php echo $this->lang->line('users'); ?> <span class="total-items">(<?php echo $total; ?>)</span></h1>

		<?php $this->load->view('admin/includes/notifications'); ?>

		<table class="table">
			<thead>
				<tr>
					<th class="title"><?php echo $this->lang->line('username');?></th>
					<th><?php echo $this->lang->line('name');?></th>
					<th width="200"><?php echo $this->lang->line('user_group');?></th>
					<th class="icons">&nbsp;</th>
				</tr>
			</thead>
			<tbody>				
				<?php $i = 1; ?>
				<?php foreach($query as $row): ?>
				<tr id="item_<?php echo $row->id;?>"<?php if($i % 2 == 0): ?> class="alt"<?php endif; ?>>			
					<td>
						<?php if($row->group_id <= $this->session->userdata('group_id')): ?>
							<a href="<?php echo base_url() . ADMIN_URL; ?>/users/edit/<?php echo $row->id;?>" title="<?php echo $this->lang->line('edit'); ?>"><?php echo $row->username;?></a>
						<?php else: ?>
							<?php echo $row->username;?>
						<?php endif; ?>
					</td>
					<td><?php if ($row->name == false): ?>-<?php else: ?><?php echo $row->name;?><?php endif; ?></td>
					<td><?php echo $row->user_group_title;?></td>
					<td class="icons">
						<?php if($row->group_id <= $this->session->userdata('group_id')): ?>
							<a href="<?php echo base_url() . ADMIN_URL; ?>/users/edit/<?php echo $row->id;?>" class="edit" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil-square-o"></i></a>	
							<?php if($row->id != $this->session->userdata('user_id')): ?>
								<a href="<?php echo base_url() . ADMIN_URL; ?>/users/delete/<?php echo $row->id;?>" data-userid="<?php echo $row->id;?>" class="<?php if($row->post_num == 0): ?>delete<?php else: ?>user-delete<?php endif; ?>" id="del_<?php echo $row->id;?>" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash-o"></i></a>
							<?php endif; ?>
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
</div>	