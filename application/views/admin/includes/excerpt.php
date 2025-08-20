<?php if(isset($config) AND $config): ?>
<label for="excerpt"><?php echo $this->lang->line('excerpt'); ?></label>
<div class="clear"></div>
<p class="field-excerpt">
	<?php 
	$content = (!$query == '') ? $query->excerpt . set_value('excerpt') : set_value('excerpt');
	if ($config == 'full') {
		echo form_wysiwyg("excerpt", true, htmlspecialchars_decode( $content ), 'full');
	} elseif($config == 'simple') {
		echo form_wysiwyg("excerpt", true, htmlspecialchars_decode( $content ), 'simple');
	} elseif($config == 'none') {
		echo '<textarea name="excerpt">'.htmlspecialchars_decode( $content ).'</textarea>';
	}
	?>
</p>
<?php else: ?>
	<input type="hidden" name="excerpt" value="<?php if($query): ?><?php echo $query->excerpt; ?><?php endif; ?>">
<?php endif; ?>