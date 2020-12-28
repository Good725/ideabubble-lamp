<section class="home-banner">
	<?php if ($page_data['layout'] != 'Content-Panels-News'): ?>
    	<aside class="sidebar">
			<article class="newsBlock">
				<h2>NEWS</h2>
				<?= Model_News::get_plugin_items_front_end_feed('News') ?>
			</article>
			<section class="calendar">
				<h2>CALENDAR</h2>
				<div id="eventCalendarDefault">
					<script>
						$(document).ready(function() {
							$("#eventCalendarDefault").eventCalendar({
								eventsjson: '<?=URL::site()?>frontend/courses/get_calendar_event_feed',
								jsonDateFormat: 'human',
								cacheJson: false
							});
						});
					</script>
				</div>
				<div class="clear"></div>
			</section>
    	</aside>
	<?php endif; ?>
	<div class="page_right">
		<?php if ($page_data['name_tag'] == "home.html"): ?>
			<?= Model_Panels::get_panels_feed('home_right'); ?>
			<iframe
				src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fkilmartineducationalservices+&amp;width=264&amp;height=590&amp;colorscheme=light&amp;show_faces=true&amp;header=true&amp;stream=true&amp;show_border=true&amp;appId=342171322499222"
				scrolling="no" frameborder="0"
				allowTransparency="true"></iframe>
		<?php else: ?>
			<?= Model_Panels::get_panels_feed('content_right') ?>
		<?php endif; ?>
	</div>
	<?php if ($page_data['layout'] == 'Content-Panels-News'): ?>
    	<aside class="sidebar">
			<article class="newsBlock">
				<h2>NEWS</h2>
				<?= Model_News::get_plugin_items_front_end_feed('News') ?>
			</article>
    	</aside>
	<?php endif; ?>
</section>