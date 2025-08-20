<!DOCTYPE html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo @$category->title; ?> | <?php echo @$page_title; ?> | AluTech</title>
	<?php echo @$meta_keywords; ?>
	<?php echo @$meta_description; ?>
	<meta name="author" content="Design: www.2fgstudio.hr | Development: www.egomedia.hr" />
	<link href="<?php echo base_url(); ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>css/font-awesome.min.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>css/leaflet.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>css/stil.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>css/responsive.css" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery.fancybox.min.css" type="text/css" />
	<link href="https://fonts.googleapis.com/css?family=Oswald|Catamaran:300,400,700,900|Titillium+Web:300,400,400i,700,700i&amp;subset=latin-ext" rel="stylesheet">
    <!--[if lt IE 7]>
            <p class="browsehappy">
			<?php if ( is_lang('fr') ) {
					echo "Vous utilisez un <strong> pas à jour</strong> navigateur. Se il vous plaît mettre à jour votre navigateur pour améliorer votre expérience.";
					} elseif ( is_lang('it') ) {
						echo "Si sta utilizzando un<strong> obsoleto</strong> del browser. Aggiornare il browser per migliorare la vostra esperienza.";
					} elseif ( is_lang('de') ) {
						echo "Sie verwenden ein<strong> überholt</strong> Browser. Bitte aktualisiere deinen Browser, um für Sie zu verbessern.";
					} elseif ( is_lang('en') ) {
						echo "You are using an<strong>outdated</strong> browser. Please upgrade your browser to improve your experience.";
					} else {
						echo "Vi koristite<strong> zastarjeli</strong> preglednik (browser). Molimo nadogradite svoj preglednik za poboljšanje prikaza naših stranica.";
				}
			?></p>
        <![endif]-->
    <!--[if lt IE 9]>
    <script src="<?php echo base_url(); ?>js/html5shiv.js"></script>
    <script src="<?php echo base_url(); ?>js/respond.min.js"></script>
    <![endif]-->
</head>
<body data-spy="scroll" data-target="#navbar" data-offset="0">
<!--?php if( is_homepage() ): ?>
	<div id="obavijest">
        <div id="obavijestSadrzaj">
			Proizvodnja zaštitnih vizira
			<br>za potrebe medicinskih djelatnika u borbi sa virusom covid19<br><br>
			<a href="http://alutech.hr/novosti/proizvodnja-zastitnih-vizira-za-potrebe-medicinskih-djelatnika-u-borbi-sa-virusom-covid19" target="_self">>>> NASTAVI ČITATI</a>
            <div id="obavijestZatvori">Zatvori (X)</div>
	   </div>
    </div-->
<!--?php endif; ?-->
    <header id="header" role="banner">
        <div class="container">
			<div class="row">
				<nav class="navbar navbar-default">
					<div class="navbar-header">
					  <a class="navbar-brand" href="<?php echo homepage_link(); ?>"><img class="img-responsive" src="<?php echo base_url(); ?>images/logo.png"></a>
					  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					  </button>
					</div>
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					  <ul class="nav navbar-nav">
						<li><a href="<?php echo homepage_link(); ?>"><i class="fa fa-home"></i></a></li>
						<li><a href="<?php echo base_url(); ?><?php if ( is_lang('en') ) { echo "en/center"; } else { echo "centar"; }?>"><?php if ( is_lang('en') ) { echo "Center"; } else { echo "Centar"; }?></a></li>
						<li class="dropdown">
						  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php if ( is_lang('en') ) { echo "Departments"; } else { echo "Odjeli"; }?> <span class="caret"></span></a>
						  <ul class="dropdown-menu">
							 <?php 
								$menu = menu(array(
									'identifier' => 'main',
									'layout' => 'a',
									'before_item' => '',
									'after_item' => '',
								
								));
								echo $menu; 
								?>
						  </ul>
						</li>
						<li><a href="<?php echo base_url(); ?><?php if ( is_lang('en') ) { echo "en/services"; } else { echo "usluge"; }?>"><?php if ( is_lang('en') ) { echo "Services"; } else { echo "Usluge"; }?></a></li>
						<li><a href="<?php echo base_url(); ?><?php if ( is_lang('en') ) { echo "en/documents"; } else { echo "dokumenti"; }?>"><?php if ( is_lang('en') ) { echo "Documents"; } else { echo "Dokumenti"; }?></a></li>
						<li><a href="<?php echo base_url(); ?><?php if ( is_lang('en') ) { echo "en/news"; } else { echo "novosti"; }?>"><?php if ( is_lang('en') ) { echo "News"; } else { echo "Novosti"; }?></a></li>
						<li><a href="http://projekt.alutech.hr/<?php if ( is_lang('en') ) { echo "en/"; } else { echo ""; }?>"><?php if ( is_lang('en') ) { echo "Project"; } else { echo "Projekt"; }?></a></li>
						<li><a href="<?php echo base_url(); ?><?php if ( is_lang('en') ) { echo "en/contact"; } else { echo "kontakt"; }?>"><?php if ( is_lang('en') ) { echo "Contact"; } else { echo "Kontakt"; }?></a></li>
						<?php echo list_languages(array('layout' => 'li', 'title' => 'code')); ?>
					</ul>
					</div>

					<div class="header-adresa">
						<div class="col-sm-12">
						   <?php if ( is_lang('en') ) { echo "Development Innovation Center AluTech"; } else { echo "Razvojno inovacijski centar AluTech"; }?> <i class="fa fa-map-marker" aria-hidden="true"></i> Velimira Škorpika 6, 22000 Šibenik, <?php if ( is_lang('en') ) { echo "Croatia"; } else { echo "Hrvatska"; }?> <i class="fa fa-phone" aria-hidden="true"></i> +385 22 200 474 <i class="fa fa-fax" aria-hidden="true"></i> +385 22 217 114 <i class="fa fa-envelope" aria-hidden="true"></i> info@alutech.hr
						</div>
					</div>
				</nav>
			</div>
		</div>
    </header>

	<?php echo $content; ?>

    <footer id="footer">
        <div class="container">         
               <div class="row">
			   <div class="col-sm-6  newsletteri">
                     <?php
						if ( is_lang('fr') ) {
							echo "Version en ligne de la newsletter: ";
						} elseif ( is_lang('it') ) {
							echo "Versione web della newsletter: ";
						} elseif ( is_lang('de') ) {
							echo "Online-Version des Newsletters: ";
						} elseif ( is_lang('en') ) {
							echo "Newsletters: ";
						} else {
							echo "Newsletteri: ";
						}
						?><a href="<?php echo base_url(); ?>newsletter/2015_ozujak/index.html" target="_blank"> ISSUE 1 </a> / <a href="<?php echo base_url(); ?>newsletter/2015_kolovoz/index.html" target="_blank"> ISSUE 2</a> / <a href="<?php echo base_url(); ?>newsletter/2016_veljaca/index.html" target="_blank"> ISSUE 3 / <a href="<?php echo base_url(); ?>newsletter/2016_lipanj/index.html" target="_blank"> ISSUE 4</a>
                </div>
                <div class="col-sm-3 text-center gray">
                     <a href="<?php echo base_url(); ?>uploads/dokumenti/alutech_brochure.pdf" target="_blank"> <?php if ( is_lang('en') ) { echo "DOWNLOAD PDF BROCHURE"; } else { echo "DOWNLOAD PDF KATALOGA"; } ?></a>
                </div>
				<div class="col-sm-3 text-center blue">
                     <a href="<?php echo base_url(); ?>uploads/dokumenti/<?php if ( is_lang('en') ) { echo "AluTech_Equipment_List.pdf"; } else { echo "AluTech_popis_opreme.pdf"; } ?>" target="_blank"> <?php if ( is_lang('en') ) { echo "EQUIPMENT LIST"; } else { echo "POPIS OPREME"; } ?></a>
                </div>
            </div>
			<div class="row">
                <div class="col-sm-12">
                     <img class="img-responsive pull-left footer-logotipi" src="<?php echo base_url(); ?>images/logotipi.png" alt="" title="">
                </div>
            </div>
			<div class="row">
                <div class="col-sm-12">
					<?php echo @block('sadrzaj_publikacije', false); ?>
					
                </div>
				<hr>
            </div>
			<div class="row">
                <div class="col-sm-6">
                    <?php echo @block('copyright', false); ?>
					<ul style="list-style:none;padding:0; margin:0px; float: left;">
						<li><?php if ( is_lang('en') ) { echo "<a href=\"http://www.alutech.hr/en/terms-of-use\" target=\"_self\">General Terms</a>"; } else { echo "<a href=\"http://www.alutech.hr/uvjeti-koristenja\" target=\"_self\">Uvjeti korištenja</a>"; } ?></li>
						<li><?php if ( is_lang('en') ) { echo "<a href=\"http://www.alutech.hr/en/privacy-policy\" target=\"_self\">Privacy Policy</a>"; } else { echo "<a href=\"http://www.alutech.hr/pravila-privatnosti\" target=\"_self\">Pravila privatnosti</a>"; } ?></li>
						<li><?php if ( is_lang('en') ) { echo "<a href=\"http://www.alutech.hr/en/cookie-statement\" target=\"_self\">Cookie Statement</a>"; } else { echo "<a href=\"http://www.alutech.hr/izjava-o-kolacicima\" target=\"_self\">Izjava o kolačićima</a>"; } ?></li>
					</ul>
                </div>
                <div class="col-sm-6">
                    <?php echo @block('web_design_development', false); ?>
                </div>
            </div>
			<div class="row">
                <div class="col-sm-12">
					<a class="naVrh" href="#"><img src="<?php echo base_url(); ?>images/strelica-gore.png"></a>
                </div>
            </div>
        </div>
		<div class="kolacici">
			<?php
				if ( is_lang('fr') ) { echo "Pour vous donner la meilleure expérience possible, alutech.hr site utilise des cookies les paramètres de cookies.<br>Peuvent être contrôlés et configurés dans votre navigateur Web.<br>En continuant à utiliser notre site, vous donnez votre consentement aux cookies utilisés.";
				} elseif ( is_lang('it') ) { echo "Per darvi la migliore esperienza possibile, alutech.hr sito utilizza i cookie impostazioni.<br>Cookie può essere controllato e configurato nel browser Web.<br>Continuando a utilizzare il nostro sito si sta dando il vostro consenso per i cookie utilizzati.";
				} elseif ( is_lang('de') ) { echo "Um Ihnen die bestmögliche Erfahrung zu geben, alutech.hr Website verwendet Cookies.<br>Cookie-Einstellungen kann gesteuert und konfiguriert werden in Ihrem Webbrowser.<br>Durch die Fortsetzung unserer Website geben Sie Ihre Zustimmung werden die Verwendung von Cookies verwendet.";
				} elseif ( is_lang('en') ) { echo "To give you the best possible experience, alutech.hr website uses cookies.<br>Cookie settings can be controlled and configured in your web browser.<br>By continuing to use our website you are giving your consent to cookies being used.";
				} else { echo "alutech.hr koristi kolačiće (eng.cookies) za pružanje boljeg korisničkog iskustva i funkcionalnosti.<br>Cookie postavke možete sami kontrolirati i konfigurirati u vašem web pregledniku.<br>Klikom na gumb PRIHVAĆAM suglasni ste sa korištenjem kolačića.";
				}
			?>
			<button id="prihvati" class="button small"><?php if ( is_lang('fr') ) { echo "Accepte"; } elseif ( is_lang('it') ) { echo "Accetto"; } elseif ( is_lang('de') ) { echo "Genau"; } elseif ( is_lang('en') ) { echo "I agree"; } else { echo "Prihvaćam"; } ?></button>
		</div>
    </footer>
	
    <script src="<?php echo base_url(); ?>js/jquery.js"></script>
    <script src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
	<script src="<?php echo base_url(); ?>js/funkcije.js"></script>
	<script>
		$(document).ready(function() {
			if (!$.cookie('prihvati')) {
				$('.kolacici').show();
			}
			$("#prihvati").click(function() {
				$('.kolacici').hide();
				$.cookie('prihvati', 'svi_vole_slatko', { expires: 365, path: '/' });
			});
			});
	</script>
	<script src="<?php echo base_url(); ?>js/jquery.cookie.js"></script>
	
	<script src="<?php echo base_url(); ?>js/leaflet.js" ></script>

	<script>
		var cities = L.layerGroup();

		L.marker([43.71305, 15.90919]).bindPopup('<p><br><img class="img-responsive" src="images/logo.png" alt="" /><br><?php if ( is_lang('fr') ) { echo "Centre dinnovation pour le développement  AluTech<br>Velimira Škorpika 6<br>HR-22000 Šibenik, Croatie"; } elseif ( is_lang('it') ) { echo "Development Innovation Center AluTech<br>Velimira Škorpika 6<br>HR-22000 Šibenik, Croazia"; } elseif ( is_lang('de') ) { echo "Entwicklungsinnovationszentrum AluTech<br>Velimira Škorpika 6<br>HR-22000 Šibenik, Kroatien"; } elseif ( is_lang('en') ) { echo "Development Innovation Center AluTech<br>Velimira Škorpika 6<br>HR-22000 Šibenik, Croatia"; } else { echo "Razvojno inovacijski centar AluTech<br>Velimira Škorpika 6<br>HR-22000 Šibenik, Hrvatska"; } ?></p>').addTo(cities);

		var mbAttr = 'Custom EGO MEDIA map for ALUTECH | Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
				'<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
				'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
			mbUrl = 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw';

		var	streets  = L.tileLayer(mbUrl, {id: 'mapbox/streets-v11',   attribution: mbAttr}), 
		grayscale   = L.tileLayer(mbUrl, {id: 'mapbox/light-v9', attribution: mbAttr});
			

		var map = L.map('map', {
			center: [43.71305, 15.90919],
			zoom: 6,
			layers: [grayscale, cities]
		});

		var baseLayers = {
			"Grayscale": grayscale,
			"Streets": streets
			
			
		};

		var overlays = {
			"Cities": cities
		};

		L.control.layers(baseLayers, overlays).addTo(map);
	</script>
	
	<script type="text/javascript">
    var speed = 1000;
    $(window).load( function() {
        screenHeight = $(window).height();
        screenWidth = $(window).width();
        elemWidth = $('#obavijest').outerWidth(true);
        elemHeight = $('#obavijest').outerHeight(true)
         
        leftPosition = (screenWidth / 2) - (elemWidth / 2);
        topPosition = (screenHeight / 2) - (elemHeight / 2);
         
        $('#obavijest').css({
            'left' : leftPosition + 'px',
            'top' : -elemHeight + 'px'
        });
        $('#obavijest').show().animate({
            'top' : topPosition
        }, speed);

        $('#obavijestZatvori').click( function() {
           
            $('#obavijest').animate({
            'top' : -elemHeight + 'px'
        }, speed, function() {
            
                $(this).remove();
            });
             
        });
    });
     
</script>

</body>
</html>