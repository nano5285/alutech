	<section id="main-slider" class="carousel obradni-centar">
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
						<a data-toggle="anchor-elementi" class="scroll" href="#obradni-centar"><img src="<?php echo base_url(); ?>images/strelica-dolje.png"></a>
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
				<div class="row mt-20">
					<div class="col-sm-12">
						<div class="center">
							<h1><?php
							if ( is_lang('fr') ) { echo "Une carte"; } 
								elseif ( is_lang('it') ) { echo "Mappa di localizzazione"; } 
								elseif ( is_lang('de') ) { echo "Lageplan"; } 
								elseif ( is_lang('en') ) { echo "Location map"; } 
								else { echo "Lokacijska karta "; }
							?></h1>
							<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2883.804070164099!2d15.906816815867801!3d43.7146206791193!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x13352597b75859d7%3A0x209148dd8d8a262a!2sPoduzetni%C4%8Dki%20Inkubator%20PIN%20%C5%A0ibenik!5e0!3m2!1shr!2shr!4v1606109626351!5m2!1shr!2shr" width="100%" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	
