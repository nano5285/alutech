<?php echo form_open(); ?>
<div class="g-row">
	<div class="g-col col1">
		<h1><i class="fa fa-gears"></i> <?php echo $this->lang->line('admin_translations'); ?></h1>
		<?php $this->load->view('admin/includes/notifications'); ?>
		<table style="width:100%" class="table">
			<thead>
				<tr>
					<th width="10"></th>
					<th width="40%">English</th>
					<th><?php echo $this->lang->line('translation'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php $i = 1; ?>
			<?php foreach($labels as $key => $value): ?>
				<tr<?php if($i % 2 == 0): ?> class="alt"<?php endif; ?>>
					<td width="10"><span class="num"><?php echo $i; ?>.</span> </td>
					<td width="30%"><?php echo strip_tags($value); ?></td>
					<td><input type="text" name="<?php echo $key; ?>" value="<?php echo $this->lang->line($key); ?>"></td>
				</tr>
			<?php $i++; ?>	
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<aside class="g-col sidebar-right">
		<div class="container fixed">
			<?php $this->load->view('admin/includes/buttons', array('exclude' => array('save_edit', 'save_new'))); ?>
		</div>
	</aside>
</div>	
<?php echo form_close(); ?>	