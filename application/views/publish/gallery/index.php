	<h1><span><?php echo $title;?></span></h1>
	<?php if($content): ?>
		<div class="content"><?php echo $content; ?></div>
	<?php endif; ?>

	<div class="gallery-items">
		<?php $i = 1; ?>
		<?php if($query): ?>
			<?php foreach ($query as $row): ?>
				<article class="gallery-post<?php if($i % 3 == 0): ?> last<?php endif; ?>">
					<?php if(isset($row->main_image->name)): ?>
						<a href="<?php echo $row->permalink; ?>">
							<img src="<?php echo image(array('file' => $row->main_image->name, 'width' => 275, 'height' => 275));?>" width="275" height="275" alt="<?php echo $row->main_image->alt; ?>" title="<?php echo $row->main_image->title; ?>" />
						</a>
					<?php endif; ?>
					<h2><a href="<?php echo $row->permalink; ?>"><?php echo $row->title;?></a></h2>
					<div class="excerpt"><?php echo $row->excerpt; ?></div>
				</article>	
			<?php $i++; ?>
			<?php endforeach; ?>
		<?php else: ?>
			<p class="not-available">There are no posts at this time</p>
		<?php endif; ?>
		
		<?php $this->load->view('publish/pagination'); ?>
	</div>