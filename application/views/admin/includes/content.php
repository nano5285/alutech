<?php if(isset($config) AND $config): ?>
<label for="text"><?php echo $this->lang->line('content'); ?></label>
<div class="clear"></div>
<p class="field-text">
	<?php 
	$content = (!$query == '') ? $query->content . set_value('content') : set_value('content');
	if ($config == 'full') {
		echo form_wysiwyg("content", true, htmlspecialchars_decode( $content ), 'full');
	} elseif($config == 'simple') {
		echo form_wysiwyg("content", true, htmlspecialchars_decode( $content ), 'simple');
	} elseif($config == 'none') {
		echo '<textarea name="content">'.htmlspecialchars_decode( $content ).'</textarea>';
	}
	?>
</p>
<?php else: ?>
	<input type="hidden" name="content" value="<?php if($query): ?><?php echo $query->content; ?><?php endif; ?>">
<?php endif; ?>