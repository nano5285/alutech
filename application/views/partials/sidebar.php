<aside class="sidebar">
	<div class="widget categories clear">
		<h5><?php echo @block('categories'); ?></h5>
		<?php
		echo category_tree(array(
			'list_attr' => 'class="sidebar-menu"',
			'before_item' => '<i class="fa fa-chevron-circle-right"></i>',
			'item_id' => 'item' 
		));
		?>
	</div>

	<div class="widget widget-latest clear">
		<h5><?php echo @block('latest_post'); ?></h5>
		<?php $posts = get_posts(array('per_page' => 1)); ?>
		<?php if($posts): ?>			
			<?php foreach ($posts as $post): ?>
				<article class="special-post">
					<h4><a href="<?php echo $post->permalink; ?>"><?php echo $post->title; ?></a></h4>
					<?php if(isset($post->main_image->name)): ?>
						<a href="<?php echo $post->permalink; ?>">
							<img src="<?php echo @image(array('file' => $post->main_image->name, 'width' => 80, 'height' => 80)); ?>" alt="<?php echo $post->main_image->alt; ?>" title="<?php echo $post->main_image->title; ?>" />
						</a>
					<?php endif; ?>
					<?php echo $post->excerpt; ?>
					<a href="<?php echo $post->permalink; ?>" class="details">Read more <i class="fa fa-chevron-right"></i></a>
				</article>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<div class="widget widget-featured">
		<h5><?php echo @block('featured_post'); ?></h5>
		<?php $posts = get_posts(array('per_page' => 1, 'featured' => true)); ?>
		<?php if($posts): ?>			
			<?php foreach ($posts as $post): ?>
				<article class="special-post">
					<h4><a href="<?php echo $post->permalink; ?>"><?php echo $post->title; ?></a></h4>
					<?php if(isset($post->main_image->name)): ?>
						<a href="<?php echo $post->permalink; ?>">
							<img src="<?php echo @image(array('file' => $post->main_image->name, 'width' => 80, 'height' => 80)); ?>" alt="<?php echo $post->main_image->alt; ?>" title="<?php echo $post->main_image->title; ?>" />
						</a>
					<?php endif; ?>
					<?php echo $post->excerpt; ?>
					<a href="<?php echo $post->permalink; ?>" class="details">Read more <i class="fa fa-chevron-right"></i></a>
				</article>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</aside>