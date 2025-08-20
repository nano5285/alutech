<?php 
$pagination = array(
	'last_link' => '>>',
	'first_link' => '<<',		   
	'cur_tag_open' => '<span class="current">',
	'cur_tag_close' => '</span>',
	'prev_link' => '<',
	'next_link' => '>',
);

$this->pagination->initialize($pagination);
?>

<div class="pagination">
	<?php echo $this->pagination->create_links(); ?>
</div>