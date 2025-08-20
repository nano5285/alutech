<section id="main-slider" class="carousel projekt">
	<div class="container">
        <div class="box">
            <div class="row">
				<div class="col-sm-12">
					<div class="col-sm-3 text-right"><img class="img-responsive rotate" src="<?php echo base_url(); ?>images/zupcanik-m.png"></div>
					<div class="col-sm-6 center zupcanik-v"><img class="img-responsive rotate" src="<?php echo base_url(); ?>images/zupcanik-v.png"></div>
					<div class="col-sm-3 text-left"><img class="img-responsive rotate" src="<?php echo base_url(); ?>images/zupcanik-m.png"></div>
				</div>
			</div>
			<div class="col-sm-12">
				<a data-toggle="anchor-elementi" class="scroll" href="#projekt"><img src="<?php echo base_url(); ?>images/strelica-dolje.png"></a>
			</div> 
		</div>
	</div> 
	</section>

	<section id="projekt">
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
	
	<div id="ciljevi">
	<section id="red-1">
		<div class="box">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="center">
						<?php echo @custom_block('extra_content1', $id); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	<section id="red-2">
		<div class="box">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="center">
						<?php echo @custom_block('extra_content2', $id); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	<section id="red-1">
		<div class="box">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="center">
						<?php echo @custom_block('extra_content3', $id); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	<section id="red-2">
		<div class="box">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="center">
							<?php echo @custom_block('extra_content4', $id); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	<section id="red-1">
		<div class="box">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="center">
							<?php echo @custom_block('extra_content5', $id); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	</div>	