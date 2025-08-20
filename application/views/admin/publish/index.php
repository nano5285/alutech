<div class="g-row">
	<div class="g-col col1">
		<?php if( $this->pagination->create_links() ): ?>
		<div class="pagination pagination-top">
			<?php echo $this->pagination->create_links();?>
		</div>
		<?php endif; ?>

		<h1><i class="fa fa-bullhorn"></i> <?php echo $this->lang->line('posts');?> <span class="total-items">(<?php echo $total; ?>)</span></h1>

		<?php $this->load->view('admin/includes/notifications'); ?>

		<?php $lang_array = $this->config->item('lang_desc'); ?>
		<table class="table table-publish">
			<thead>
				<tr>
				
					<th class="title"><?php echo $this->lang->line('title');?></th>		
					<th class="created"><?php echo $this->lang->line('created');?></th>
					<th class="category"><?php echo $this->lang->line('category');?></th>
					<?php if($this->config->item('publish_admin_author_column')): ?>
						<th class="author"><?php echo $this->lang->line('author');?></th>
					<?php endif; ?>
					<th class="status"><?php echo $this->lang->line('published');?></th>
					<?php if($this->config->item('publish_featured_post')): ?>
						<th class="status"><?php echo $this->lang->line('featured');?></th>
					<?php endif; ?>
					<?php if(count($lang_array) > 1): ?><th class="lang"><?php echo $this->lang->line('language');?></th><?php endif; ?>
					<th class="icons">&nbsp;</th>
				</tr>
			</thead>
			<tbody>	
				<?php $i = 0; ?>
				<?php $category_slugs = $this->db->select('id,slug')->get('categories')->result(); ?>
				<?php foreach($query as $row): ?>
				<tr id="item_<?php echo $row->id;?>"<?php if($i % 2 == 0): ?> class="alt"<?php endif; ?>>
		
				
				
					<td class="title"><a href="<?php echo base_url() . ADMIN_URL; ?>/publish/posts/edit/<?php echo $row->id;?>" title="<?php echo $this->lang->line('edit'); ?>"><?php echo $row->title;?></a></td>
					<td><?php echo date($this->config->item('log_date_format'), strtotime($row->date)); ?> / <?php echo date($this->config->item('log_time_format'), strtotime($row->date)); ?></td>
					<td><?php echo $row->category_title; ?></td>
					<?php if($this->config->item('publish_admin_author_column')): ?>
						<td class="author"><?php echo $row->author; ?></td>
					<?php endif; ?>
					<td class="status">
						<?php if($row->status == 1): ?>
						<i class="fa fa-check-circle"></i>
						<?php else: ?>
						<i class="fa fa-times-circle"></i>
						<?php endif; ?>				
					</td>
					<?php if($this->config->item('publish_featured_post')): ?>
					<td class="status">
						<?php if($row->featured == 1): ?>
						<i class="fa fa-check-circle"></i>
						<?php else: ?>
						<i class="fa fa-times-circle"></i>
						<?php endif; ?>				
					</td>
					<?php endif; ?>
					<?php if(count($lang_array) > 1): ?><td class="lang"><?php echo $row->lang;?></td><?php endif; ?>
					<td class="icons">
						<?php
						/**
						 * This section will check if the post belongs to the 
						 * level 1 or level 2 category and build the preview url
						 */
						if($row->category_parent != 0) {
							$category_parent = explode('.', $row->category_position);
							$category_parent = $this->db->get_where(CATEGORIES_DB_TABLE, array('position' => $category_parent[0]))->row()->slug;
							$category_slug = $category_parent . '/' . $row->category_slug;
						} else {
							$category_slug = $row->category_slug;
						}
						?>
						<a href="<?php echo base_url(); ?><?php if ($row->lang != $this->config->item('language_abbr')): ?><?php echo $row->lang;?>/<?php endif; ?><?php echo $category_slug; ?>/<?php echo $row->slug;?>" target="_blank"><i class="fa fa-eye"></i></a>						
						<a href="<?php echo base_url() . ADMIN_URL; ?>/publish/posts/edit/<?php echo $row->id;?>" class="edit" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil-square-o"></i></a>
						<a href="<?php echo base_url() . ADMIN_URL; ?>/publish/posts/delete/<?php echo $row->id;?>" class="delete" id="del_<?php echo $row->id;?>" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash-o"></i></a>
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