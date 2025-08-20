    <section id="main-slider" class="carousel partneri">
	<div class="container">
        <div class="box">
            <div class="row">
				<div class="carousel-inner">
					<div class="item active">
						<div class="container">
							<div class="carousel-content">
								<div class="col-sm-2"></div>
									<div class="col-sm-8">
										<?php echo @custom_block('slider_1', $id); ?>
									</div>
								<div class="col-sm-2"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-12">
					<a data-toggle="anchor-elementi" class="scroll" href="#partneri"><img src="<?php echo base_url(); ?>images/strelica-dolje.png"></a>
				</div> 
			</div>
		</div>
	</div> 
	</section>

	<section id="partneri">
		<div class="box">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="center">
							<h1><?php echo $title;?></h1>
							<?php echo $content; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	