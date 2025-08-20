<h1><span><?php echo $title; ?></span></h1>

<?php echo $content; ?>

<?php if($images): ?>
<div class="images">
	<?php $i = 1; ?>
	<?php foreach ($images as $image): ?>
		<article class="gallery-post<?php if($i % 3 == 0): ?> last<?php endif; ?>">
			<a href="<?php echo media_path(array('file' => $image->name)); ?>" class="fancybox" rel="gallery" title="<?php echo $image->title; ?>">
				<img src="<?php echo image(array('file' => $image->name, 'width' => 275, 'height' => 275)); ?>" alt="<?php echo $image->alt; ?>" title="<?php echo $image->title; ?>" />
			</a>
			<h2><?php echo $image->title; ?></h2>
			<div class="excerpt"><?php echo $image->alt; ?></div>
		</article>	
	<?php $i++; ?>
	<?php endforeach; ?>
</div>	
<?php endif; ?>