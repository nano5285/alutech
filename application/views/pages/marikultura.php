	<section id="main-slider" class="carousel marikultura">
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
						<a data-toggle="anchor-elementi" class="scroll" href="#marikultura"><img src="<?php echo base_url(); ?>images/strelica-dolje.png"></a>
					</div> 
				</div>
			</div>
		</div> 
	</section>

	<section id="obradni-centar">
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
				<div class="row mt-20">
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
				</div>
				<div class="row mt-20">
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
				<div class="row mt-20">
					<div class="col-lg-6 col-sm-12">
						<div class="center">
							<?php echo @custom_block('extra_content5', $id); ?>
						</div>
					</div>
					<div class="col-lg-6 col-sm-12">
						<div class="center">
							<?php echo @custom_block('extra_content6', $id); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	
