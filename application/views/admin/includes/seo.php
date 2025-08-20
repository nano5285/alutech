<?php if(isset($config) AND $config): ?>
	<p class="field-seo-title">
		<label for="seo_h1_title"><?php echo $this->lang->line('seo_title'); ?></label>
		<input type="text" id="seo_h1_title" name="seo_title" value="<?php if($query): ?><?php echo $query->seo_title;?><?php endif; ?><?php echo set_value('seo_title'); ?>">
	</p>
	<p class="field-seo-keywords">
		<label for="meta_keywords"><?php echo $this->lang->line('meta_keywords'); ?></label>
		<input type="text" id="meta_keywords" name="meta_keywords" value="<?php if($query): ?><?php echo $query->meta_keywords;?><?php endif; ?><?php echo set_value('meta_keywords'); ?>">
	</p>
	<p class="field-seo-description">
		<label for="meta_description"><?php echo $this->lang->line('meta_description'); ?></label>
		<input type="text" id="meta_description" name="meta_description" value="<?php if($query): ?><?php echo $query->meta_description;?><?php endif; ?><?php echo set_value('meta_description'); ?>">
	</p>
<?php else: ?>
	<input type="hidden" id="seo_h1_title" name="seo_title" value="<?php if($query): ?><?php echo $query->seo_title;?><?php endif; ?><?php echo set_value('seo_title'); ?>">
	<input type="hidden" id="meta_keywords" name="meta_keywords" value="<?php if($query): ?><?php echo $query->meta_keywords;?><?php endif; ?><?php echo set_value('meta_keywords'); ?>">
	<input type="hidden" id="meta_description" name="meta_description" value="<?php if($query): ?><?php echo $query->meta_description;?><?php endif; ?><?php echo set_value('meta_description'); ?>">
<?php endif; ?>