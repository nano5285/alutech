<h3>	
	<?php
		if ( is_lang('fr') ) {
			echo "Merci de nous contacter.";
		} elseif ( is_lang('it') ) {
			echo "Grazie per averci contattato.";
		} elseif ( is_lang('de') ) {
			echo "Vielen Dank für Ihre Kontaktaufnahme.";
		} elseif ( is_lang('en') ) {
			echo "Thank you for contacting us.";
		} else {
			echo "Hvala vam što ste nas kontaktirali.";
		}
	?>
</h3>
<p>
	<?php
		if ( is_lang('fr') ) {
			echo "Votre message a été envoyé avec succès. Nous vous répondrons dès que possible.";
		} elseif ( is_lang('it') ) {
			echo "Il tuo messaggio è stato inviato con successo. Vi risponderemo al più presto.";
		} elseif ( is_lang('de') ) {
			echo "Ihre Nachricht wurde erfolgreich versendet. Wir antworten so bald wie möglich.";
		} elseif ( is_lang('en') ) {
			echo "Your message has been sent successfully. We appreciate that you’ve taken the time to write us. We will reply as soon as possible.";
		} else {
			echo "Vaša poruka je uspješno poslana. Odgovoriti ćemo Vam u najkraćem mogućem roku.";
		}
	?>
</p>