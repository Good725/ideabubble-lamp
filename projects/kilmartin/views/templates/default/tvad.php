<?php
/**
 * This is all very crude and hardcoded, due to time constraints.
 * A cleaner system should soon be set up. See KES-1490
 */
$location = (strpos('.'.$page_data['name_tag'], 'rab-') == 1) ? ucfirst(str_replace('.html', '', substr($page_data['name_tag'], 1+strpos($page_data['name_tag'], '-')))) : '';
$time = time();
if (@$_GET['time']) {
    $time = strtotime($_GET['time']);
}

$dt_start = date('Y-m-d H:i:s', $time);
$dt_end = date('Y-m-d 23:59:59', $time);
$rab_sql = "SELECT
	CONCAT(TIME_FORMAT(`event`.`datetime_start`, '%H:%i'),  ' &ndash;',  TIME_FORMAT(`event`.`datetime_end`, '%H:%i')) AS `Time`,
	`event`.`datetime_start`, `event`.`datetime_end`,
	DATE_FORMAT(`event`.`datetime_start`, '%W') AS `Day`,
	`schedule`.`name` AS `Class`,
	`location`.`name` AS `Room`,
	CONCAT(`trainer`.`first_name`,  ' ', `trainer`.`last_name`) AS `Trainer`
FROM
	`plugin_courses_schedules_events` `event`
	JOIN `plugin_courses_schedules` `schedule` ON `event`.`schedule_id` = `schedule`.`id`
	JOIN `plugin_courses_courses` `course` ON `schedule`.`course_id` = `course`.`id`
	LEFT JOIN (SELECT * FROM `plugin_courses_locations`  WHERE `delete` = 0  AND `location_type_id` = 2) `location` ON `schedule`.`location_id` = `location`.`id`
	LEFT JOIN (SELECT * FROM `plugin_contacts3_contacts` WHERE `delete` = 0) `trainer` ON `schedule`.`trainer_id` = `trainer`.`id`
	LEFT JOIN (SELECT * FROM `plugin_courses_locations`  WHERE `delete` = 0) `parent_location` ON `location`.`parent_id` = `parent_location`.`id`
WHERE
	`event`.`delete` = 0
	AND `schedule`.`delete` = 0
	AND `course`.`deleted` = 0";
if (trim($location))
{
	$rab_sql .= "
	AND `parent_location`.`name` = '$location'";
}
$rab_sql .="
	AND
	(
		(`event`.`datetime_start` >= '" . $dt_start . "' AND `event`.`datetime_start` <= '" . $dt_end . "')
		OR
		(`event`.`datetime_end` >= '" . $dt_start . "' AND `event`.`datetime_end` <= '" . $dt_end . "')
	)
ORDER BY  `datetime_start` ASC;";

$rab_data = DB::query(Database::SELECT, $rab_sql)->execute()->as_array();

$news_category = $location == '' ? 'Ticker' : trim('Ticker - '.$location);

$news = DB::select()
	->from(array('plugin_news', 'news'))
	->join(array('plugin_news_categories', 'category'))->on('news.category_id', '=', 'category.id')
	->where('category', '=', $news_category)
	->and_where('news.publish', '=', 1)
	->and_where('news.deleted', '=', 0)
	->and_where_open()
		->where('news.date_publish', 'IS', NULL)
		->or_where('news.date_publish', '<', DB::expr('NOW()'))
	->and_where_close()
	->and_where_open()
		->where('news.date_remove', 'IS', NULL)
		->or_where('news.date_remove', '>', DB::expr('NOW()'))
	->and_where_close()
	->order_by('news.order', 'ASC')
	->order_by('news.event_date', 'DESC')
	->execute()
	->as_array();

?><!doctype html>
<html lang="en">
	<head>
		<meta name="robots" content="noindex" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?= $page_data['title'] ?></title>
		<meta name="description" content="<?= $page_data['seo_description'] ?>" />
		<meta name="keywords" content="<?= $page_data['seo_keywords'] ?>" />
		<meta name="google-site-verification" content="<?= Settings::instance()->get('google_webmaster_code') ?>" />
		<meta name="msvalidate.01" content="<?= Settings::instance()->get('bing_webmaster_code') ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<link rel="shortcut icon" href="<?= URL::site() ?>assets/default/images/favicon.ico" type="image/ico" />
		<?= Settings::get_google_analitycs_script() ?>
		<meta name="google-site-verification" content="<?= Settings::instance()->get('google_webmaster_code') ?>" />
		<meta name="msvalidate.01" content="<?= Settings::instance()->get('bing_webmaster_code') ?>" />

		<link rel="stylesheet" type="text/css" href="/engine/plugins/customscroller/js/front_end/bxslider/jquery.bxslider.css" />

		<style>
			html, body, div, span, applet, object, iframe,
			h1, h2, h3, h4, h5, h6, p, blockquote, pre,
			a, abbr, acronym, address, big, cite, code,
			del, dfn, em, img, ins, kbd, q, s, samp,
			small, strike, strong, sub, sup, tt, var,
			b, u, i, center,
			dl, dt, dd, ol, ul, li,
			fieldset, form, label, legend,
			table, caption, tbody, tfoot, thead, tr, th, td,
			article, aside, canvas, details, embed,
			figure, figcaption, footer, header, hgroup,
			menu, nav, output, ruby, section, summary,
			time, mark, audio, video {
				margin: 0;
				padding: 0;
				border: 0;
				font-size: 100%;
				font: inherit;
				vertical-align: baseline;
			}
				/* HTML5 display-role reset for older browsers */
			article, aside, details, figcaption, figure,
			footer, header, hgroup, menu, nav, section {
				display: block;
			}
			body {
				line-height: 1;
			}
			ol, ul {
				list-style: none;
			}
			table {
				border-collapse: collapse;
				border-spacing: 0;
			}


			html, body, .banner-slider, .bx-wrapper, .ad-slider, #ad-slider > li, .ad-slider-figure {
				height: 100%;
			}
			.bx-viewport {
				height: 100%!important;
			}
			html {
				font-family: Helvetica, Arila, sans-serif;
			}
			.bx-wrapper .bx-viewport {
				box-shadow: none;
				border: none;
				left: 0;
			}
			.slider-image {
				width: 100%;
				height: 100%;
			}
			.rab-table-wrapper {
				max-height: 100%;

			}
			.rab-table {
				width: 100%;
				max-height: 100%;
				font-size: 2em;
			}
			.rab-table,
			.rab-table caption {
				background: #a1d594;
			}
			.rab-table tbody tr:nth-child(odd) {
				background: #55b364;
				color: #fff;
			}
			.rab-table td,
			.rab-table th {
				padding: .5em;
			}
			.rab-table th {
				font-weight: bold;
			}

			.news-section {
				font-size: 1.7em;
				padding-left: 60px;
				position: fixed;
				bottom: 0;
				background: #fff;
			}
			.news-section:before {
				content: '';
				background: url('/assets/default/images/owl.png');
				background-size: cover;
				display: block;
				margin-top: -88px;
				position: relative;
				left: -54px;
				top: 93px;
				width: 50px;
				height: 84px;
				float: left;
			}
			.news-ticker-title {
				background: #0E2A6B;
				color: #fff;
				padding: .5em;
			}
			.news-ticker-summary {
				color: #354387;
				white-space: nowrap;
			}
			.news-ticker-summary li {
				padding: .3em;
			}



		</style>

		<!--[if lt IE 9]>
		<script src="/assets/default/js/modernizr-2.5.3.js?"></script>
		<![endif]-->

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript" src="/engine/plugins/customscroller/js/front_end/bxslider/jquery.bxslider.js"></script>
	</head>
	<body class="template-default layout-tvad">
		<?php
		$banner_data = Model_Pagebanner::get_banner_data($page_data['banner_photo']);
		$sequence_data = FALSE;
		if (isset($banner_data) AND isset($banner_data['id']))
		{
			$sequence_data = DB::select()->from('plugin_custom_scroller_sequences')
				->where('id', '=', $banner_data['id'])->where('publish', '=', '1')->where('deleted', '=', '0')
				->execute()->current();
		}
		?>

		<?php if ($banner_data AND $banner_data['banner_type'] == '3'): ?>
			<?php
			$sequence_items = DB::select()->from('plugin_custom_scroller_sequence_items')
				->where('sequence_id', '=', $banner_data['sequence_id'])->where('publish', '=', '1')->where('deleted', '=', '0')
				->order_by('order_no')
				->execute()->as_array();
			?>
        <?php endif?>


        <?php if($rab_data):?>
        <?php ob_start(); ?>
        <div class="rab-table-wrapper">
            <table class="rab-table">

                <caption>
                    <?= $location ?> RAB | <?= date('j M', $time) ?>
                </caption>
                <thead>
                <tr>
                    <th scope="col"><?= __('Time') ?></th>
                    <th scope="col"><?= __('Class') ?></th>
                    <th scope="col"><?= __('Room') ?></th>
                    <th scope="col"><?= __('Teacher') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rab_data as $result): ?>
                    <tr>
                        <td><?= $result['Time'] ?></td>
                        <td><?= $result['Class'] ?></td>
                        <td><?= $result['Room'] ?></td>
                        <td><?= $result['Trainer'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>
        <?php elseif (!$rab_data): ?>
        <?php ob_start(); ?>
        <div class="rab-table-wrapper">
            <table class="rab-table">

                <caption>
                    <?= $location ?> RAB | <?= date('j M', $time) ?> | <?= date('H.i', $time) ?> &ndash; <?= date('H.i', strtotime('+1 HOUR', $time)) ?>
                </caption>
                <thead>
                <tr>
                    <th scope="col"><?= __('Time') ?></th>
                    <th scope="col"><?= __('Class') ?></th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2" align="center">There are currently no classes scheduled for today</td>
                    </tr>
                </tbody>
            </table>

        </div>
        <?php endif?>
			<?php $rab_html = ob_get_clean(); ?>

			<section class="banner-section">
				<ul class="ad-slider" id="ad-slider">
					<?php if (isset($sequence_items)): ?>
						<?php foreach ($sequence_items as $sequence_item): ?>
							<li>
								<figure class="ad-slider-figure">
									<?php if ($sequence_item['link_url']): ?>
										<a href="/<?= $sequence_item['link_url'] ?>">
											<img class="slider-image" src="<?= Model_Media::get_image_path($sequence_item['image'], 'banners') ?>" />
										</a>
									<?php else: ?>
										<img class="slider-image" src="<?= Model_Media::get_image_path($sequence_item['image'], 'banners') ?>" />
									<?php endif; ?>
									<figcaption><?= $sequence_item['title'] ?></figcaption>
								</figure>
							</li>
							<li><?= $rab_html ?></li>
						<?php endforeach; ?>
					<?php else: ?>
						<li><?= $rab_html ?></li>
					<?php endif; ?>
				</ul>
			</section>

			<section class="news-section">
				<?php if (count($news)> 0): ?>
					<ul id="news-ticker-titles">
						<?php foreach ($news as $news_item): ?>
							<li>
								<h3 class="news-ticker-title"><?= $news_item['title'] ?></h3>
								<ul class="news-ticker-summary">
									<li><?= $news_item['summary'] ?></li>
								</ul>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</section>
<!--		--><?php //endif; ?>

		<script>
			$(document).ready(function(){
				$('#ad-slider').bxSlider({
					auto: true,
					speed: '<?= (isset($sequence_data['animation_type']) AND trim($sequence_data['animation_type'])) ? $sequence_data['animation_type'] : 1 ?>',
					pause: '<?= (isset($sequence_data['rotating_speed']) AND trim($sequence_data['rotating_speed'])) ? $sequence_data['animation_type'] : 8000 ?>',
					controls: false,
					pager: false,
					useCSS: true
				});
				$('#news-ticker-titles').bxSlider({
					auto: true,
					speed: 1,
					pause: 8000,
					controls: false,
					pager: false,
					useCSS: true
				});
				$('.news-ticker-summary').bxSlider({
					auto: true,
					speed: 50000,
					pause: 1,
					controls: false,
					ticker: true,
					slideMargin: '5em',
					pager: false,
					useCSS: true
				});

				var $rows = null;
				var first_display_timeout = null;
				var rotate_interval = null;
				var page = 0;
				function paginate_table_body()
				{
					if ($rows == null) {
						$rows = $(".rab-table tbody > tr");
					}
					var row_height = 0;
					if ($rows[0]) {
						row_height = $rows[0].offsetHeight;
					}

					var wheight = $(window).height();
					var header_height = $(".rab-table > caption").height() + $(".rab-table > thead").height();
					var news_height = $(".news-section").height();
					var available_height = wheight - header_height - news_height;
					console.log("ah:" + available_height + "; rh: " + row_height);

					if (($rows.length * row_height) > available_height) {
						$rows.remove();


						var rows_per_page = Math.ceil(available_height / row_height) - 2;
						var page_count = Math.ceil($rows.length / rows_per_page);
						$(".rab-table").append("<tfoot><tr><th colspan='4' style='text-align: center'></th></tr>");
						function rotate_pages()
						{
							//console.log("display :" + rows_per_page + ":" + page + "/" + page_count);
							$(".rab-table tfoot th").html("displaying " + (page + 1) + " / " + page_count);
							$(".rab-table tbody > tr").remove();
							var start = page * rows_per_page;
							for(var i = 0; i < rows_per_page && i < $rows.length ; ++i) {
								$(".rab-table tbody").append($rows[start + i]);
							}
							++page;
							page = page % page_count;
						}
						first_display_timeout = setTimeout(rotate_pages, 8000);
						rotate_interval = setInterval(rotate_pages, 16000);
					}
				}

				paginate_table_body();

				setTimeout(function(){
					window.location.reload();
				}, 3600000);
			});
		</script>

	</body>
</html>