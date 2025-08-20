<?php echo form_open(base_url() . uri_string()); ?>
	<div class="row">
		<div class="col-sm-12">
			<div class="form-group">
				<input type="text" class="form-control" required="required" name="name" id="name" value="<?php echo set_value('name'); ?>" placeholder="<?php
						if ( is_lang('fr') ) {
							echo "Nom";
						} elseif ( is_lang('it') ) {
							echo "Nome";
						} elseif ( is_lang('de') ) {
							echo "Name";
						} elseif ( is_lang('en') ) {
							echo "Name";
						} else {
							echo "Ime";
						}
						?>" />
			<?php if(form_error('name') AND is_lang('de')): ?>
				<span class="error">Pflichtfeld</span>
			<?php elseif(form_error('name') AND is_lang('it')): ?>
				<span class="error">Campo obbligatorio</span>
			<?php elseif(form_error('name') AND is_lang('fr')): ?>
				<span class="error">Champ requis</span>
			<?php elseif(form_error('name') AND is_lang('en')): ?>
				<span class="error">Required field</span>
			<?php elseif(form_error('name')): ?>
				<span class="error">Obavezno polje</span>
			<?php endif; ?>
            </div>
        </div>
		<div class="col-sm-12">
            <div class="form-group">
				<input type="text" class="form-control" required="required" name="email" id="email" value="<?php echo set_value('email'); ?>" placeholder="Email" />
			<?php if(form_error('email') AND is_lang('de')): ?>
				<span class="error">Pflichtfeld</span>
			<?php elseif(form_error('email') AND is_lang('it')): ?>
				<span class="error">Campo obbligatorio</span>
			<?php elseif(form_error('email') AND is_lang('fr')): ?>
				<span class="error">Champ requis</span>
			<?php elseif(form_error('email') AND is_lang('en')): ?>
				<span class="error">Required field</span>
			<?php elseif(form_error('email')): ?>
				<span class="error">Obavezno polje</span>
			<?php endif; ?>
            </div>
        </div>
		<div class="col-sm-12">
			<div class="form-group">
				<input type="text" class="form-control" required="required" name="subject" id="subject" value="<?php echo set_value('subject'); ?>" placeholder="<?php
						if ( is_lang('fr') ) {
							echo "Sujet";
						} elseif ( is_lang('it') ) {
							echo "Soggetto";
						} elseif ( is_lang('de') ) {
							echo "Thema";
						} elseif ( is_lang('en') ) {
							echo "Subject";
						} else {
							echo "Naslov poruke";
						}
						?>"
						/>
			<?php if(form_error('subject') AND is_lang('de')): ?>
				<span class="error">Pflichtfeld</span>
			<?php elseif(form_error('subject') AND is_lang('it')): ?>
				<span class="error">Campo obbligatorio</span>
			<?php elseif(form_error('subject') AND is_lang('fr')): ?>
				<span class="error">Champ requis</span>
			<?php elseif(form_error('subject') AND is_lang('en')): ?>
				<span class="error">Required field</span>
			<?php elseif(form_error('subject')): ?>
				<span class="error">Obavezno polje</span>
			<?php endif; ?>
			</div>
        </div>

		<div class="col-sm-12">
            <div class="form-group">
			<textarea rows="" cols="" name="message" id="message" required="required" class="form-control" placeholder="<?php
						if ( is_lang('fr') ) {
							echo "Un message";
						} elseif ( is_lang('it') ) {
							echo "Messaggio";
						} elseif ( is_lang('de') ) {
							echo "Nachricht";
						} elseif ( is_lang('en') ) {
							echo "Message";
						} else {
							echo "Poruka";
						} 
						?>" ><?php echo set_value('message'); ?></textarea>
			<?php if(form_error('message') AND is_lang('de')): ?>
				<span class="error">Pflichtfeld</span>
			<?php elseif(form_error('message') AND is_lang('it')): ?>
				<span class="error">Campo obbligatorio</span>
			<?php elseif(form_error('message') AND is_lang('fr')): ?>
				<span class="error">Champ requis</span>
			<?php elseif(form_error('message') AND is_lang('en')): ?>
				<span class="error">Required field</span>
			<?php elseif(form_error('message')): ?>
				<span class="error">Obavezno polje</span>
			<?php endif; ?>
		
            </div>
        </div>
		<div class="col-sm-12">
            <div class="form-group">
				<input type="checkbox" name="privacy_agreement" value="1"> <?php if ( is_lang('en') ) { echo "I agree to the"; } else { echo "Slažem se sa"; } ?> <?php if ( is_lang('en') ) { echo "<a href=\"http://www.alutech.hr/en/terms-of-use\" target=\"_self\">General Terms</a>"; } else { echo "<a href=\"http://www.alutech.hr/uvjeti-koristenja\" target=\"_self\">Uvjetima korištenja</a>"; } ?> <?php if ( is_lang('en') ) { echo "and"; } else { echo "i"; } ?> <?php if ( is_lang('en') ) { echo "<a href=\"http://www.alutech.hr/en/privacy-policy\" target=\"_self\">Privacy Policy</a>."; } else { echo "<a href=\"http://www.alutech.hr/pravila-privatnosti\" target=\"_self\">Pravilima privatnosti</a>."; } ?> <?php if ( is_lang('en') ) { echo "I give the privilege for using my personal data."; } else { echo "Dajem privolu za korištenje mojih osobnih podataka."; } ?>
            </div>
         </div>
		<div class="col-sm-12">
            <div class="form-group">
				<input type="text" name="secure_code" value="" />
				<button type="submit" name="submit" class="btn btn-primary" value="submit">
					<?php
						if ( is_lang('fr') ) {
							echo "ENVOYER";
						} elseif ( is_lang('it') ) {
							echo "INVIARE";
						} elseif ( is_lang('de') ) {
							echo "SENDEN";
						} elseif ( is_lang('en') ) {
							echo "SEND";
						} else {
							echo "POŠALJI";
						}
					?>
				</button>
            </div>
         </div>
    </div>

<?php echo form_close(); ?>