<section id="main-slider" class="carousel novosti">
	<div class="container">
        <div class="box">
            <div class="row">
				<div class="carousel-inner">
					<div class="item active">
						<div class="container">
							<div class="carousel-content">
								<div class="col-sm-3"></div>
									<div class="col-sm-6">
										<h2><?php echo $title; ?></h2>
										<p class="lead">
										<a class="natrag" href="<?php echo @$category->permalink; ?>">
										<?php
											if ( is_lang('fr') ) { echo "RETOUR AUX NOUVELLES"; } 
											elseif ( is_lang('it') ) { echo "TORNA ALLE NEWS"; } 
											elseif ( is_lang('de') ) { echo "ZURÜCK ZU DEN NEWS"; } 
											elseif ( is_lang('en') ) { echo "BACK TO NEWS"; } 
											else { echo "NATRAG NA NOVOSTI"; }
											?>
										</a>
										</p>
									</div>
								<div class="col-sm-3"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-12 title-separator">
				</div> 
			</div>
		</div>
	</div> 
	</section>
	
	<section id="novosti-sadrzaji">
		<div class="container">
			<div class="row">
				<div class="col-md-8">
					<div class="box novost">
						<div class="datum">
						<a href="<?php echo @$category->permalink; ?>"><?php echo @$category->title; ?></a> | <?php echo date($this->config->item('log_date_format'), strtotime($date)); ?>
						<?php if($author_name): ?>
							| <?php echo $author_name; ?>
						<?php endif; ?></div>
						
						<?php if( is_lang('hr') ): ?>
						<?php if($main_image): ?>
							<a href="<?php echo media_path(array('file' => $main_image->name)); ?>" class="fancybox" rel="gallery" title="<?php echo $main_image->title; ?>">
								<img class="img-responsive" src="<?php echo image(array('file' => $main_image->name, 'width' => 800, 'height' => auto)); ?>" alt="<?php echo $main_image->alt; ?>" title="<?php echo $main_image->title; ?>" />
							</a>
							<hr>
						<?php endif; ?>
						
					<?php endif; ?>
						
						<?php echo $content; ?>
						
					<?php $i = 1; ?><hr>
					<div class="galerija">
					<?php foreach ($images as $image): ?>
						<?php if($i > 1): ?>
							<a href="<?php echo media_path(array('file' => $image->name)); ?>" class="fancybox" rel="gallery" title="<?php echo $image->title; ?>">
								<img src="<?php echo image(array('file' => $image->name, 'width' => 230, 'height' => auto)); ?>" alt="<?php echo $image->alt; ?>" title="<?php echo $image->title; ?>" />
							</a>
						<?php endif; ?>
						<?php $i++; ?>
					<?php endforeach; ?>
					</div></div>
				</div>
				<div class="col-md-4">
					<div class="box novost lista">
						<h2><?php
							if ( is_lang('fr') ) { echo "NOUVELLES"; } 
								elseif ( is_lang('it') ) { echo "NOVITÀ"; } 
								elseif ( is_lang('de') ) { echo "NACHRICHTEN"; } 
								elseif ( is_lang('en') ) { echo "NEWS"; } 
								else { echo "NOVOSTI"; }
							?>
						</h2>
							
						<?php $posts = get_posts(array('per_page' => 10, 'category' => '57')); ?>
							<?php if($posts): ?>
								<ul>
									<?php foreach ($posts as $post): ?>
									<li>
									<?php $onemoguci_link = @custom_block('onemoguci_link', $post->id); ?>
											<?php if($onemoguci_link): ?> 
											<?php echo $post->title;?>
										<?php else: ?>
										<a href="<?php echo @$post->permalink; ?>"><?php echo $post->title;?></a>
									<?php endif; ?>
								</li>
									
								<?php endforeach; ?>
							<?php else: ?>
								<p class="nema-sadrzaja">Trenutno nema sadržaja.</p>
							</ul>
							<?php endif; ?>

					</div>
				</div>
			</div>
		</div>
	</section>
