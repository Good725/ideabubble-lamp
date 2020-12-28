<?php include 'template_views/html_document_header.php'; ?>
<?php
$args                 = Kohana::sanitize($_GET);
$filters['make']      = isset($args['make'])       ? $args['make']       : '';
$filters['model']     = isset($args['model'])      ? $args['model']      : '';
$filters['fuel']      = isset($args['fuel'])       ? $args['fuel']       : '';
$filters['min_year']  = isset($args['min_year'])   ? $args['min_year']   : '';
$filters['max_year']  = isset($args['max_year'])   ? $args['max_year']   : '';
$filters['min_price'] = isset($args['min_price'])  ? $args['min_price']  : '';
$filters['max_price'] = isset($args['max_price'])  ? $args['max_price']  : '';
$offset           = isset($args['offset']) ? $args['offset'] : 0;
$cars             = Model_Cars::get_search_results($filters, 10, $offset);
$count_cars       = Model_Cars::count_search_results($filters);
$current_page     = $offset / 10 - $offset % 10 + 1;
$is_first_page    = ($offset == 0);
$is_last_page     = ($count_cars - $offset <= 10);
?>
<body class="content_layout">
    <div id="container" class="container">
        <?php include 'header.php' ?>

        <div id="content" class="content_area">
            <div id="banner" class="banner_area">
                <?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']); // Helper banners ?>

                <div id="content_panels" class="panel_area content_panel_area">
                    <?= Model_Panels::get_panels_feed('content_center'); ?>
                </div>
            </div>
            <div class="content">
                <?= $page_data['content'] ?>
				<header class="search_results_summary">
					<h2>Your Search Results</h2>

                     <p class="subtitle"><?= ($filters['min_year']  != '') ? 'Min year: '       .$filters['min_year'] .' ' : '' ?>
						<?= ($filters['max_year']  != '') ? 'Max year: '       .$filters['max_year'] .' ' : ''?><br />
						<?= ($filters['min_price'] != '') ? 'Min price: &euro;'.$filters['min_price'].' ' : '' ?>
						<?= ($filters['max_price'] != '') ? 'Max price: &euro;'.$filters['max_price'].' ' : ''?>
					</p> 
				</header>

				<div id="search_options" class="search_options">
					<div>
						<h3>Search Options</h3>
					</div>
					<div>
						<ul>
							<?php $query_string = '?make='.$filters['make'].'&model='.$filters['model'].'&fuel='.$filters['fuel'].
								'&min_year='.$filters['min_year'].'&max_year='.$filters['max_year'].
								'&min_price='.$filters['min_price'].'&max_price='.$filters['max_price'] ?>

							<li><a href="/<?= $query_string ?>">Refine your search</a></li>
						</ul>
					</div>
				</div>

				<?php include('template_views/carlistings_pagination.php') ?>

				<?php if (count($cars) > 0): ?>
					<div class="search_results_wrapper">
						<?php foreach($cars as $car): ?>
							<div class="search_result_car">
								<div class="search_result_description">
									<div class="search_result_image"><a href="/cardetail.html/<?= $car['id'] ?>"><?php
										if ($car['photo'] != ''): ?>
											<img src="<?= str_replace('/lightbox_full/', '/thumbnail/', preg_replace('/\,.*/', '', $car['photo'])) ?>" alt="" />
										<?php else: ?>
											<img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'','products').'_thumbs/no_image_available.jpg' ?>" />
										<?php endif;
										?></a></div>
									<div class="search_result_text">
										<h3><?= ($car['year'] != '') ? $car['year'].' - ' : '' ?><?= $car['make'] ?> <?= $car['model'] ?></h3>
										<p><?= $car['description'] ?></p>
										<p><?= $car['comments'] ?></p>
									</div>
								</div>
								<div class="search_result_data quick_data">
									<dl>
										<?php // The potentially empty dl tags are to ensure everything stays lined up ?>
										<?php if ($car['odometer'] != '' AND $car['odometer'] != 0): ?>
											<dt class="label-odometer">Odometer</dt>
											<dd><?= number_format(floatval($car['odometer'])) ?></dd>
											<?php endif; ?>
									</dl>
									<dl>
										<?php if ($car['color'] != ''): ?>
											<dt class="label-color">Colour</dt>
											<dd><?= $car['color'] ?></dd>
											<?php endif; ?>
									</dl>
									<dl>
										<?php if ($car['price'] != '' AND $car['price'] != 0): ?>
											<dt class="label-price">Price</dt>
											<dd>&euro;<?= number_format(floatval($car['price'])) ?></dd>
											<?php endif; ?>
									</dl>

									<a href="/cardetail.html/<?= $car['id'] ?>" class="more_link primary_button">More</a>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
					<?php include('template_views/carlistings_pagination.php') ?>
				<?php else: ?>
					<p class="no_results_found">No results found.</p>
				<?php endif; ?>
            </div>
        </div>

    <!-- /content -->

    <?php include 'footer.php' ?>

    </div>
    <?= Settings::instance()->get('footer_html'); ?>
    </body>
<?php include 'template_views/html_document_footer.php'; ?>