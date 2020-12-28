<?php include 'template_views/html_document_header.php'; ?>
<body class="layout-home">
<div class="wrapper">

	<div class="main_content_in">
		<div class="logo">
			<a href="/">
				<img src="<?= $page_data['logo'] ?>" width="224" height="106" alt="logo">
			</a>
		</div>

		<div class="main_content_in_rit">
			<?php include 'header.php'; ?>
			<div class="main_content highlighted_panels">

				<?php
				$home_panels = Model_Panels::get_panels_feed('home_content');
				$replace_count = 1;
				$home_panels = str_replace('class="panels', 'class="panels home_panels main_content_in1', $home_panels);
				echo str_replace('{home_panels}', $home_panels, str_replace('<p>{home_panels}</p>', '{home_panels}', $page_data['content']), $replace_count);
				?>

			</div>
			<div class="customers">
				<h3>Our Customers</h3>

				<div class="customers_in">
					<?php menuhelper::add_menu_editable_heading('company_logos') ?>
				</div>
			</div>
		</div>
		<div class="main_content_in_lef">

			<div class="solutions">
				<?php menuhelper::add_menu_editable_heading('side_menu') ?>
			</div>

			<div class="schedule">

				<div class="schedule_in">
					<?= Model_Panels::get_panels_feed('content_left'); ?>
				</div>
				<?= Model_News::get_plugin_items_front_end_feed('News'); ?>

			</div>
		</div>

		<?php include 'footer.php' ?>

	</div>

</div>
</body>
<?php include 'template_views/html_document_footer.php'; ?>
