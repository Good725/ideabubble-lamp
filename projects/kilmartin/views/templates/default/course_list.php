<?php include 'template_views/html_document_header.php'; ?>
<body class="template-default">
	<?php include 'template_views/header.php'; ?>
	<div id="main">
		<?php include 'template_views/menu.php'; ?>
		<?php include 'template_views/search_bar_snippet.php'; ?>
		<div class="breadcrumb">
			<?= IbHelpers::breadcrumb_navigation_v2() ?>
		</div>
		<?= $page_data['content'] ?>

		<?php include 'template_views/course_feed_snippet.php'; ?>

	</div>
	<?php include 'template_views/footer.php'; ?>
    <?= Settings::instance()->get('footer_html'); ?>
<?php include 'template_views/html_document_footer.php'; ?>