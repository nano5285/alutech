<?php if(validation_errors() OR $this->session->flashdata('info') OR $this->session->flashdata('error')): ?>
	<div class="notifications clear">
<?php endif; ?>

<?php if( validation_errors() ): ?>
	<?php echo validation_errors(); ?>
<?php endif; ?>

<!-- Info, error, success and other notifications -->
<?php if(!$this->session->flashdata('error') AND $this->session->flashdata('info')): ?>
	<div class="info success"><i class="fa fa-check-circle"></i> <?php echo $this->session->flashdata('info');?></div>	
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>		
	<div class="info error"><i class="fa fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error');?></div>	
<?php endif; ?>

<?php if(validation_errors() OR $this->session->flashdata('info') OR $this->session->flashdata('error')): ?>
	</div>
<?php endif; ?>