<?php $settings_instance = Settings::instance() ?>
<?php if (Settings::instance()->get('search_engine_indexing') === 'FALSE'): ?>
	<meta name="robots" content="NOINDEX, NOFOLLOW"/>
<?php endif; ?>
<link rel="shortcut icon" href="<?= URL::get_skin_urlpath() ?>images/favicon.ico" type="image/ico"/>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="description" content="<?= trim($page_data['seo_description']) ?>" />
<meta name="keywords" content="<?= trim($page_data['seo_keywords']) ?>" />
<?php $author = Settings::instance()->get('company_title'); ?>
<?php if (trim($author)): ?>
    <meta name="author" content="<?= trim($author) ?>" />
<?php endif; ?>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
<meta name="google-site-verification" content="<?= settings::instance()->get('google_webmaster_code') ?>"/>
<meta name="msvalidate.01" content="<?= settings::instance()->get('bing_webmaster_code') ?>"/>
<title><?= $page_data['title']; ?></title>

<link href="<?= URL::site() ?><?= URL::get_skin_urlpath() ?>css/validation.css" rel="stylesheet" type="text/css" />
<?php
// Replace with a more generic check for sites using jQuery 2
if (isset(Kohana::$config->load('config')->template_folder_path) AND Kohana::$config->load('config')->template_folder_path == 'tickets'): ?>
	<script src="//<?= URL::get_engine_assets_base(); ?>js/libs/jquery-2.1.4.min.js"></script>
<?php else: ?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="<?= URL::get_engine_assets_base() ?>js/libs/jquery-1.7.2.min.js"><\/script>')</script>
<?php endif; ?>
<script type="text/javascript" src="<?= URL::site() ?><?= URL::get_skin_urlpath() ?>js/jquery.validationEngine2.js"></script>
<script type="text/javascript" src="<?= URL::site() ?><?= URL::get_skin_urlpath() ?>js/jquery.validationEngine2-en.js"></script>
<!--[if lt IE 9]>
<script type="text/javascript" src="<?= URL::site() ?><?= URL::get_skin_urlpath() ?>js/respond.src.js"></script>
<script src="//ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
<![endif]-->

<?php include 'background_switcher.php' ?>
<?= Settings::instance()->get('head_html'); ?>

<!-- Browser detection -->
<script type="text/javascript">
	var test_mode = <?= (Settings::instance()->get('browser_sniffer_testmode') == 1) ? 'true' : 'false' ?>;
</script>
<script src="<?= URL::site() ?>assets/shared/js/browserorg/main.js"></script>
<link rel="stylesheet" href="<?= URL::site() ?>assets/shared/css/browserorg/style.css" />
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

<?php
$pixel_enabled_sitewide = $settings_instance->get('facebook_pixel_enabled_sitewide');
$pixel_enabled_per_user = $settings_instance->get('facebook_pixel_enabled_per_user');
$item_owner_has_pixel   = (!empty($item_owner) && !empty($item_owner->facebook_pixel_enabled) && trim($item_owner->facebook_pixel_code));

// If the owner of the item in question has Facebook Pixel set up, use their campaign
if ($pixel_enabled_per_user && !empty($item_owner) && $item_owner_has_pixel) {
    $facebook_pixel_code = trim($item_owner->facebook_pixel_code);
}
// Otherwise, use the site-wide Facebook Pixel campaign
else if ($pixel_enabled_sitewide) {
    $facebook_pixel_code = trim($settings_instance->get('facebook_pixel_code'));
}
?>
<?php if (!empty($facebook_pixel_code)): ?>
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?= $facebook_pixel_code ?>');
        fbq('track', 'PageView');

        <?php if (isset($_GET['registered']) && $_GET['registered'] == 'success'): ?>
            fbq('track', 'CompleteRegistration');
        <?php endif; ?>
    </script>

    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=<?= $facebook_pixel_code ?>&ev=PageView&noscript=1"
    /></noscript>
<?php endif; ?>

