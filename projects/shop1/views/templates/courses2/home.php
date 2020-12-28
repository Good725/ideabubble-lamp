<?php include 'template_views/header.php' ?>
	<div class="content-wrapper content">
		<div class="panel-feed home-panel-feed">
			<?=Model_Panels::get_panels_feed('home_content');?>
		</div>
		<?= $page_data['content'] ?>
	</div>
<?php include 'template_views/footer.php' ?>