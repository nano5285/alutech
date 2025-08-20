<div class="g-row">
	<div class="g-col col1">
		<?php if( $this->pagination->create_links() ): ?>
		<div class="pagination pagination-top">
			<?php echo $this->pagination->create_links();?>
		</div>
		<?php endif; ?>

		<h1><i class="fa fa-bullhorn"></i> <?php echo $this->lang->line('categories');?> <span class="total-items">(<?php echo $total; ?>)</span></h1>

		<?php $this->load->view('admin/includes/notifications'); ?>

		<?php $lang_array = $this->config->item('lang_desc'); ?>
		<table class="table">
			<thead>
				<tr>
			
					<th class="title"><?php echo $this->lang->line('title'); ?></th>
					<th class="url"><?php echo $this->lang->line('url'); ?></th>
					<th class="position"><?php echo $this->lang->line('position'); ?></th>
					<th class="level"><?php echo $this->lang->line('level'); ?></th>
					<th class="status"><?php echo $this->lang->line('published'); ?></th>
					<?php if(count($lang_array) > 1): ?><th class="lang"><?php echo $this->lang->line('language'); ?></th><?php endif; ?>
					<th class="icons">&nbsp;</th>
				</tr>
			</thead>
			<tbody>	
				<?php $i = 0; ?>
				<?php $category_slugs = $this->db->select('id,slug')->get('categories')->result(); ?>
				<?php foreach($query as $row): ?>
				<tr id="item_<?php echo $row->id;?>"<?php if($i % 2 == 0): ?> class="alt"<?php endif; ?>>
				
					<td class="title"><a href="<?php echo base_url() . ADMIN_URL; ?>/publish/category/edit/<?php echo $row->id;?>" title="<?php echo $this->lang->line('edit'); ?>"><?php echo $row->title;?></a></td>
					<td class="url"><a href="<?php echo base_url(); ?><?php if ($row->lang != $this->config->item('language_abbr')): ?><?php echo $row->lang;?>/<?php endif; ?><?php echo $row->slug;?>" target="_blank"><?php echo $row->slug;?></a></td>
					<td class="position"><?php echo $row->position; ?></td>
					<td class="level"><?php echo $row->level; ?></td>
					<td class="status">
						<?php if($row->status == 1): ?>
						<i class="fa fa-check-circle"></i>
						<?php else: ?>
						<i class="fa fa-times-circle"></i>
						<?php endif; ?>				
					</td>
					<?php if(count($lang_array) > 1): ?><td class="lang"><?php echo $row->lang; ?></td><?php endif; ?>
					<td class="icons">
						<?php
						/**
						 * This section will check if the post belongs to the 
						 * level 1 or level 2 category and build the preview url
						 */
						if($row->parent != 0) {
							$category_parent = explode('.', $row->position);
							$category_parent = $this->db->get_where(CATEGORIES_DB_TABLE, array('position' => $category_parent[0]))->row()->slug;
							$category_slug = $category_parent . '/' . $row->slug;
						} else {
							$category_slug = $row->slug;
						}
						?>
						<a href="<?php echo base_url(); ?><?php if ($row->lang != $this->config->item('language_abbr')): ?><?php echo $row->lang;?>/<?php endif; ?><?php echo $category_slug;?>" target="_blank"><i class="fa fa-eye"></i></a>						
						<a href="<?php echo base_url() . ADMIN_URL; ?>/publish/category/edit/<?php echo $row->id;?>" class="edit" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil-square-o"></i></a>
						<a href="<?php echo base_url() . ADMIN_URL; ?>/publish/category/delete/<?php echo $row->id;?>" class="delete" id="del_<?php echo $row->id;?>" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash-o"></i></a>
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