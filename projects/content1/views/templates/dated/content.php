<?php $assets_folder_path = Kohana::$config->load('config')->assets_folder_path; ?>
<!DOCTYPE html>
<html>
<head>
	<title><?= $page_data['title']; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta name="Author" content="David Graham - Thinwire Interactive">
	<meta name="description" content="<?= $page_data['seo_description']; ?>">
	<meta name="keywords" content="<?= $page_data['seo_keywords']; ?>">
	<link href="<?= URL::base() ?>assets/<?= $assets_folder_path ?>/css/styles.css" rel="stylesheet" type="text/css" />
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
<body>
<table class="main">
	<tr>
		<td>
			<table class="header">
				<tr>
					<td>
						<a href="/" target="_self" name="top">
							<img src="<?= URL::base() ?>assets/<?= $assets_folder_path ?>/images/logo.jpg" alt="Fountain of Knowledge Logo Image" width="170" height="80" />
						</a>
					</td>
					<td class="headerbg"></td>
					<td class="header_mov">
						<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
								codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0"
								width="570" height="80">
							<param name="movie" value="images/header.swf">
							<param name="quality" value="high">
							<embed src="<?= URL::base() ?>assets/<?= $assets_folder_path ?>/images/header.swf" quality="high"
								   pluginspage="http://www.macromedia.com/go/getflashplayer"
								   type="application/x-shockwave-flash" width="570" height="80"></embed>
						</object>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<!-- content to START here -->
			<table class="content_table">
				<tbody>
				<tr>
					<td class="navbg">
						<div class="nav">
							<?php menuhelper::add_menu_editable_heading('main', 'side-nav')?>
						</div>
					</td>
					<td>
						<?= $page_data['content'] ?>
					</td>
				</tr>
				</tbody>
			</table>
			<!-- END of content -->
		</td>
	</tr>
	<tr>
		<td class="bottom">Solely owned and operated by Jim and Tyree McLeod &copy; 2006 : <a
				href="mailto:info@learningcentre.ie" class="bottom">info@learningcentre.ie</a></td>
	</tr>
</table>
</body>
</html>
