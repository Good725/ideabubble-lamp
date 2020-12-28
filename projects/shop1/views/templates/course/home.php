	<?php include 'template_views/html_document_header.php'; ?>
	<body id="<?= $page_data['layout'] ?>" class="home_layout <?= $page_data['category'] ?>">
		<div class="wrapper">
			<div class="container">

				<?php include 'header.php' ?>

				<div class="main_content_wrapper">
					<div class="main_content">
						<section class="banner_section">
							<?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']); ?>
						</section>

						<section class="news_section">
							<div id="eventCalendarDefault" class="upcoming_events_calendar">
								<script>
									$(document).ready(function() {
										$("#eventCalendarDefault").eventCalendar({
											eventsjson: '<?=URL::site()?>frontend/courses/get_calendar_event_feed/?show_schedules=1',
											jsonDateFormat: 'human',
											cacheJson: false
										});
									});
								</script>
							</div>
						</section>

						<section class="panels_section home_panels_section">
							<?= Model_Panels::get_panels_feed('home_content'); ?>
						</section>

						<section class="content_section">
							<?= $page_data['content'] ?>
						</section>
					</div>
				</div>

				<?php include 'footer.php' ?>
			</div>
		</div>
	</body>
</html>