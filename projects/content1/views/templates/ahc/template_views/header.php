<?php
$assets_folder_path = Kohana::$config->load('config')->assets_folder_path;
$assets_folder_code_path = PROJECTPATH.'www/assets/'.$assets_folder_path;
?><!DOCTYPE html>
<?php // The "no-js" class gets replaced with "js", after Modernizr runs ?>
<!--[if IE 8]> <html class="no-js lt-ie9" lang="en" > <![endif]-->
<!--[if IE 9]> <html class="no-js lt-ie10" lang="en" > <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="en" > <!--<![endif]-->
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<?php if (Settings::instance()->get('search_engine_indexing') === 'FALSE'): ?>
			<meta name="robots" content="noindex, nofollow"/>
		<?php endif; ?>
		<meta name="google-site-verification" content="<?= settings::instance()->get('google_webmaster_code') ?>"/>
		<meta name="msvalidate.01" content="<?= settings::instance()->get('bing_webmaster_code') ?>"/>

		<title><?= $page_data['title'] ?></title>
        <?php // Suffix URL with modification date. A different URL each time the file is changed will force the cache to clear.  ?>
		<link rel="shortcut icon" href="/assets/<?= $assets_folder_path ?>/images/favicon.ico?ts<?= filemtime($assets_folder_code_path.'/images/favicon.ico') ?>" type="image/ico" />

		<?php // SEO ?>
		<meta name="description" content="<?= $page_data['seo_description'] ?>" />
		<link rel="canonical" href="<?= str_replace('.html.html', '.html', URL::base().$page_data['name_tag'].'.html') ?>" />
		<meta name="keywords" content="<?= $page_data['seo_keywords'] ?>"/>

		<meta property="og:locale" content="en_GB" />
		<meta property="og:type" content="website" />
		<meta property="og:title" content="<?= $page_data['title'] ?>" />
		<meta property="og:description" content="<?= $page_data['seo_description'] ?>" />
		<meta property="og:url" content="<?= str_replace('.html.html', '.html', URL::base().'/'.$page_data['name_tag'].'.html') ?>" />
		<meta property="og:site_name" content="<?= Settings::instance()->get('company_name') ?>" />
		<?php /*
		<meta property="og:image" content="http://ailesburyhairclinic.com/wp-content/uploads/2015/05/home-hero.jpg" />
		<script type='application/ld+json'>{"@context":"http:\/\/schema.org","@type":"WebSite","url":"http:\/\/ailesburyhairclinic.com\/","name":"Ailesbury Hair Clinic","potentialAction":{"@type":"SearchAction","target":"http:\/\/ailesburyhairclinic.com\/?s={search_term_string}","query-input":"required name=search_term_string"}}</script>
		<script type='application/ld+json'>{"@context":"http:\/\/schema.org","@type":"Organization","url":"http:\/\/ailesburyhairclinic.com\/","sameAs":[],"name":"Ailesbury Hair Clinic","logo":""}</script>
 		*/ ?>

		<?php /*
		<link rel="alternate" type="application/rss+xml" title="Ailesbury Hair &raquo; Home Comments Feed" href="http://ailesburyhairclinic.com/home/feed/" />
		*/ ?>

		<?php /* Emoticons
		<script type="text/javascript">
			window._wpemojiSettings = {"baseUrl":"https:\/\/s.w.org\/images\/core\/emoji\/72x72\/","ext":".png","source":{"concatemoji":"http:\/\/ailesburyhairclinic.com\/wp-includes\/js\/wp-emoji-release.min.js?ver=4.4.2"}};
			!function(a,b,c){function d(a){var c,d=b.createElement("canvas"),e=d.getContext&&d.getContext("2d"),f=String.fromCharCode;return e&&e.fillText?(e.textBaseline="top",e.font="600 32px Arial","flag"===a?(e.fillText(f(55356,56806,55356,56826),0,0),d.toDataURL().length>3e3):"diversity"===a?(e.fillText(f(55356,57221),0,0),c=e.getImageData(16,16,1,1).data.toString(),e.fillText(f(55356,57221,55356,57343),0,0),c!==e.getImageData(16,16,1,1).data.toString()):("simple"===a?e.fillText(f(55357,56835),0,0):e.fillText(f(55356,57135),0,0),0!==e.getImageData(16,16,1,1).data[0])):!1}function e(a){var c=b.createElement("script");c.src=a,c.type="text/javascript",b.getElementsByTagName("head")[0].appendChild(c)}var f,g;c.supports={simple:d("simple"),flag:d("flag"),unicode8:d("unicode8"),diversity:d("diversity")},c.DOMReady=!1,c.readyCallback=function(){c.DOMReady=!0},c.supports.simple&&c.supports.flag&&c.supports.unicode8&&c.supports.diversity||(g=function(){c.readyCallback()},b.addEventListener?(b.addEventListener("DOMContentLoaded",g,!1),a.addEventListener("load",g,!1)):(a.attachEvent("onload",g),b.attachEvent("onreadystatechange",function(){"complete"===b.readyState&&c.readyCallback()})),f=c.source||{},f.concatemoji?e(f.concatemoji):f.wpemoji&&f.twemoji&&(e(f.twemoji),e(f.wpemoji)))}(window,document,window._wpemojiSettings);
		</script>
		<style type="text/css">
			img.emoji,img.wp-smiley{display:inline!important;border:none!important;box-shadow:none!important;height:1em!important;width:1em!important;margin:0 .07em!important;vertical-align:-.1em!important;background:0 0!important;padding:0!important}
		</style>

 		*/ ?>

		<?php // CSS ?>
		<link rel="stylesheet" href="/assets/shared/css/browserorg/style.css" />
		<link rel="stylesheet" href="/assets/<?= $assets_folder_path ?>/css/styles.css" type="text/css" media="all" id="main-css" />

		<?php // JS ?>
		<?= settings::get_google_analitycs_script(); ?>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="<?= URL::get_engine_assets_base() ?>js/libs/jquery-1.7.2.min.js"><\/script>')</script>
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/assets/<?= $assets_folder_path ?>/js/respond.src.js"></script>
		<script src="//ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
		<![endif]-->
		<script type="text/javascript">
			var test_mode = <?= (Settings::instance()->get('browser_sniffer_testmode') == 1) ? 'true' : 'false' ?>;
		</script>
		<script src="<?= URL::site() ?>assets/shared/js/browserorg/main.js"></script>
		<script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/general.js"></script>

		<?= Settings::instance()->get('head_html') ?>

		<?php /*
		<link rel='https://api.w.org/' href='http://ailesburyhairclinic.com/wp-json/' />
		<link rel="EditURI" type="application/rsd+xml" title="RSD" href="http://ailesburyhairclinic.com/xmlrpc.php?rsd" />
		<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="http://ailesburyhairclinic.com/wp-includes/wlwmanifest.xml" />
		<meta name="generator" content="WordPress 4.4.2" />
		<link rel='shortlink' href='http://ailesburyhairclinic.com/' />
		<meta name="theme-color" content="">
		<script type="text/javascript">
			var google_replace_number="353 1 269 0933 ";
			(function(a,e,c,f,g,b,d){var h={ak:"972662554",cl:"eN3zCK7-vWEQms7mzwM"};a[c]=a[c]||function(){(a[c].q=a[c].q||[]).push(arguments)};a[f]||(a[f]=h.ak);b=e.createElement(g);b.async=1;b.src="//www.gstatic.com/wcm/loader.js";d=e.getElementsByTagName(g)[0];d.parentNode.insertBefore(b,d);a._googWcmGet=function(b,d,e){a[c](2,b,h,d,null,new Date,e)}})(window,document,"_googWcmImpl","_googWcmAk","script");
		</script>
 		*/ ?>

		<?php if (Settings::instance()->get('cookie_enabled') === 'TRUE'): ?>
			<!-- Cookie consent plugin by Silktide - http://silktide.com/cookieconsent -->
			<script type="text/javascript">
				<?php
                $cookie_message      = Settings::instance()->get('cookie_text');
                $cookie_dismiss_text = Settings::instance()->get('hide_notice_message');
                $cookie_link         = Settings::instance()->get('cookie_page');
                $cookie_link_text    = Settings::instance()->get('link_text');
                $cookie_message      = $cookie_message      ? $cookie_message      : 'This website uses cookies to ensure you get the best experience on our website';
                $cookie_dismiss_text = $cookie_dismiss_text ? $cookie_dismiss_text : 'Got it!';
                $cookie_link_text    = $cookie_link_text    ? $cookie_link_text    : 'More info';
                $cookie_consent_options = array(
                    "message" => $cookie_message,
                    "dismiss" => $cookie_dismiss_text,
                    "learnMore" => $cookie_link_text,
                    "link" => $cookie_link ? Model_Pages::get_page_by_id($cookie_link) : null,
                    "theme" => "dark-bottom"
                );
                ?>
				window.cookieconsent_options = <?=json_encode($cookie_consent_options)?>; // use proper js encoding to handle special characters ' " \ / etc...
			</script>
			<script src="<?= URL::site() ?>assets/shared/js/cookieconsent/cookieconsent.min.js"></script>
		<?php endif; ?>
	</head>

	<body class="<?= $page_data['layout'] ?> page page-id-<?= $page_data['id'] ?> page-template-<?= $page_data['category'] ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="0" height="0" style="position:absolute" />
		<header>
			<div class="row">
				<div class="small-6 medium-3 columns">
					<a href="/<?= $page_data['theme_home_page'] ?>">
						<img src="/assets/<?= $assets_folder_path ?>/images/logo.png" class="logo-image" />
					</a>
				</div>
				<div class="small-6 medium-9 columns">
					<div class="menu-icon"><span class="show-for-medium-up"><?= __('View Menu') ?> </span>&#9776;
						<div class="menu-main-menu-container">
							<?= menuhelper::add_menu_editable_heading('main', 'menu')?>
						</div>
					</div>
					<?php $phone_number = Settings::instance()->get('telephone'); ?>
					<?php if (trim($phone_number)): ?>
						<p class="show-for-medium-up header-phone"><?= $phone_number ?></p>
					<?php endif; ?>
				</div>
			</div>
		</header>
