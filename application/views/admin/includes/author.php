<?php if(isset($config) AND $config): ?>
	<p class="field-author">
		<label for="author"><?php echo $this->lang->line('author'); ?></label>
		<select name="author" id="author">
			<?php foreach ($authors as $author): ?>
				<?php 
				if($query) {
					$selected = ($author->id == $query->author) ? ' selected="selected"' : '';
				} else {
					$selected = ($author->id == $this->session->userdata('user_id')) ? ' selected="selected"' : '';
				}
				?>
				<option value="<?php echo $author->id; ?>"<?php echo set_select('author',$author->id); ?><?php echo $selected; ?>>
					<?php if($author->name == ''): ?>
						<?php echo $author->username; ?>
					<?php else: ?>
						<?php echo $author->name; ?>
					<?php endif; ?>
				</option>
			<?php endforeach ?>
		</select>
	</p>
<?php else: ?>
	<input type="hidden" name="author" value="<?php echo $this->session->userdata('user_id'); ?>">
<?php endif; ?>