<?php include 'template_views/header.php'; ?>

<div class="content-columns">
	<div class="row content-columns">
		<?php include 'template_views/sidebar.php'; ?>

		<div class="content_area">
			<div class="page-content"><?= trim($page_data['content']) ?></div>

			<?php if($page_data['name_tag'] == 'pay-online.html'): ?>
				<div class="page-content"><?php require_once 'template_views/pay_online.php'; ?></div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php include 'template_views/footer.php'; ?>
