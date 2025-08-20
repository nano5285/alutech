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
										<?php echo $title;?>
									</div>
								<div class="col-sm-3"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-12">
					<a data-toggle="anchor-elementi" class="logo-znak" href="index.html"><img src="<?php echo base_url(); ?>images/logo-znak.png"></a>
				</div> 
			</div>
		</div>
	</div> 
	</section>
	
	<section id="novosti-sadrzaji">
		<div class="container">
			<div class="row">
				<?php if($query): ?>
				<?php foreach ($query as $row): ?>
				<div class="col-md-6">
					<div class="box">
						<h3>
						<?php $onemoguci_link = @custom_block('onemoguci_link', $row->id); ?>
							<?php if($onemoguci_link): ?> 
								<?php echo $row->title;?>
							<?php else: ?>
							<a href="<?php echo $row->permalink; ?>"><?php echo $row->title;?></a>
						<?php endif; ?>
						</h3>
							
						<div class="datum">
							<?php echo date($this->config->item('log_date_format'), strtotime($row->date)); ?> | 
							<span class="izvor">
							<?php $autor_izvora = @custom_block('autor_izvora', $row->id); ?>
								<?php if($autor_izvora): ?> 
									<?php echo $autor_izvora; ?>
								<?php else: ?>
								<?php echo $row->author_name; ?>
							<?php endif; ?>
							</span>
						</div>
						
						<?php if( is_lang('hr') ): ?>
	<?php if(isset($row->main_image->name)): ?>
						<a href="<?php echo $row->permalink; ?>">
							<img class="img-responsive" src="<?php echo @image(array('file' => $row->main_image->name, 'width' => 600, 'height' => auto)); ?>" alt="<?php echo $row->main_image->alt; ?>" title="<?php echo $row->main_image->title; ?>" />
						</a>
						

						<?php endif; ?>
<?php endif; ?>
						
						
					
						<?php echo $row->excerpt; ?>
		
					</div>
				</div>
				<?php endforeach; ?>
				<?php else: ?>
				<p>There are no posts at this time.</p>
				<?php endif; ?>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="center">
					<button type="button" class="vise-vijesti" data-loading-text="Loading..."> (+) </button>
					<button type="button" class="manje-vijesti" data-loading-text="Loading..."> (-) </button>
					</div>
				</div>
			</div>
		</div>
	</section>
	