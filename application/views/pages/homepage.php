<section id="main-slider" class="carousel naslovnica">
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
					<div class="item">
						<div class="container">
						   <div class="carousel-content">
								<div class="col-sm-2"></div>
									<div class="col-sm-8">
										<?php echo @custom_block('slider_2', $id); ?>
									</div>
								<div class="col-sm-2"></div>
							</div>
						</div>
					</div>
				</div>
				<a class="prev" href="#main-slider" data-slide="prev"><img src="<?php echo base_url(); ?>images/strelica-lijevo.png"></a>
				<a class="next" href="#main-slider" data-slide="next"><img src="<?php echo base_url(); ?>images/strelica-desno.png"></a>
				<div class="col-sm-12">
					<a data-toggle="anchor-elementi" class="scroll" href="#footer"><img src="<?php echo base_url(); ?>images/strelica-dolje.png"></a>
				</div> 
			</div>
		</div>
	</div> 
</section>

<section id="moduli-naslovnica">
	<div class="box">
		<div class="container">
			<div class="row mt-50">
				<div class="col-lg-6 col-sm-12">
					<div class="center">
						<?php echo @custom_block('extra_content1', $id); ?>
					</div>
				</div>
				<div class="col-lg-6 col-sm-12">
					<div class="center">
						<?php echo @custom_block('extra_content2', $id); ?>
					</div>
				</div>
				<div class="col-lg-6 col-sm-12">
					<div class="center">
						<?php echo @custom_block('extra_content3', $id); ?>
					</div>
				</div>
				<div class="col-lg-6 col-sm-12">
					<div class="center">
						<?php echo @custom_block('extra_content4', $id); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>