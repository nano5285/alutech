<?php echo form_open(); ?>
<div class="g-row">
	<div class="g-col col1">
		<h1>
			<i class="fa fa-users"></i> 
			<?php if ($this->uri->segment(3) != 'profile'): ?>
				<?php echo $this->lang->line('add_user'); ?>
			<?php else: ?>
				<?php echo $this->lang->line('edit_profile'); ?>
			<?php endif; ?>
		</h1>

		<?php $this->load->view('admin/includes/notifications'); ?>

		<div class="field-wrapper">
			<p class="field-user-username field-error col50">
				<?php echo form_error('username'); ?>
				<label for="username"><?php echo $this->lang->line('username');?></label>
				<input type="text" class="focus" id="username" name="username" value="<?php if($query): ?><?php echo $query->username;?><?php endif; ?><?php echo set_value('username'); ?>">
			</p>
			<p class="field-name col50 last">
				<label for="name"><?php echo $this->lang->line('name');?></label>
				<input type="text" id="name" name="name" value="<?php if($query): ?><?php echo $query->name;?><?php endif; ?><?php echo set_value('name'); ?>">
			</p>
		</div>

		<div class="field-wrapper">
			<p class="field-user-password field-error col50">
				<?php echo form_error('password'); ?>
				<label for="password"><?php echo $this->lang->line('password');?></label>
				<input type="password" id="password" name="password" value="">
			</p>
			<p class="field-user-password2 field-error col50 last">
				<?php echo form_error('password2'); ?>
				<label for="password2"><?php echo $this->lang->line('confirm_password');?></label>
				<input type="password" id="password2" name="password2" value="">
			</p>
			<?php if ($this->uri->segment(3) != 'create'): ?><p class="leave-blank"><?php echo $this->lang->line('leave_password');?></p><?php endif; ?>
		</div>

		<p class="field-bio">
			<label for="bio"><?php echo $this->lang->line('bio');?></label>
			<textarea name="bio" id="bio" cols="30" rows="10"><?php if($query): ?><?php echo $query->bio;?><?php endif; ?><?php echo set_value('bio'); ?></textarea>
		</p>

		<?php if ($this->uri->segment(3) != 'profile'): ?>
		<p class="field-group">
			<label for="group"><?php echo $this->lang->line('user_group');?></label>
			<select id="group" name="group">
				<?php foreach ($groups as $row): ?>
				<option value="<?php echo $row->id;?>" <?php if ($query && $query->group_id == $row->id):?>selected="selected"<?php endif; ?> <?php echo set_select('group',$row->id); ?>><?php echo $row->description;?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php endif; ?>
	</div>

	<aside class="g-col sidebar-right">
		<div class="container fixed">
			<?php if($this->uri->segment(3) == 'profile'): ?>
				<?php $this->load->view('admin/includes/buttons', array('exclude' => array('save_edit', 'save_new'))); ?>
			<?php else: ?>
				<?php $this->load->view('admin/includes/buttons'); ?>
			<?php endif; ?>
		</div>
	</aside>	
</div>	
<?php echo form_close(); ?>