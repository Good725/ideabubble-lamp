<?php include 'template_views/html_document_header.php'; ?>
<?php
$makes  = Model_Cars::get_all_makes();
$models = Model_Cars::get_all_models();
$fuels  = Model_Cars::get_all_fuels();

$args                 = Kohana::sanitize($_GET);
$filters['make']      = isset($args['make'])       ? $args['make']       : '';
$filters['model']     = isset($args['model'])      ? $args['model']      : '';
$filters['fuel']      = isset($args['fuel'])       ? $args['fuel']       : '';
$filters['min_year']  = isset($args['min_year'])   ? $args['min_year']   : '';
$filters['max_year']  = isset($args['max_year'])   ? $args['max_year']   : '';
$filters['min_price'] = isset($args['min_price'])  ? $args['min_price']  : '';
$filters['max_price'] = isset($args['max_price'])  ? $args['max_price']  : '';

?>
<body class="home_layout">
    <div id="container" class="container">
        <?php include 'header.php' ?>

        <div class="search_filter_wrapper">
            <form id="search_filter" class="search_filter" method="get" action="/car-listings.html">
				<label for="search_filter_make" class="accessible-hide">Make</label>
                <select id="search_filter_make" name="make">
                    <option value="">Select Make</option>
					<?php foreach($makes as $make): ?>
						<option value="<?= $make['make'] ?>"<?= $filters['make'] == $make['make'] ? ' selected="selected"' : '' ?>><?= $make['make'] ?></option>
					<?php endforeach; ?>
                </select>
				<label for="search_filter_model" class="accessible-hide">Model</label>
                <select id="search_filter_model" name="model">
                    <option value="">Select Model</option>
					<?php foreach($models as $model): ?>
						<option value="<?= $model['model'] ?>"<?= $filters['model'] == $model['model'] ? ' selected="selected"' : '' ?>><?= $model['model'] ?></option>
					<?php endforeach; ?>
                </select>
				<label for="search_filter_fuel" class="accessible-hide">Engine Type</label>
                <select id="search_filter_fuel" name="fuel">
                    <option value="">Select Engine Type</option>
					<?php foreach($fuels as $fuel): ?>
						<option value="<?= $fuel['fuel'] ?>"<?= $filters['fuel'] == $fuel['fuel'] ? ' selected="selected"' : '' ?>><?= $fuel['fuel'] ?></option>
					<?php endforeach; ?>
                </select>

                <div class="search_ranges">
                    <div>
                        <label>Year</label>
						<label for="search_filter_min_year" class="accessible-hide">Minimum Year</label>
                        <input id="search_filter_min_year" name="min_year" type="text" placeholder="MIN" value="<?= $filters['min_year'] ?>" />

						<label for="search_filter_max_year" class="accessible-hide">Maximum Year</label>
                        <input id="search_filter_max_year" name="max_year" type="text" placeholder="MAX" value="<?= $filters['max_year'] ?>" />
                    </div>
                    <div>
                        <label>Price</label>
						<label for="search_filter_min_price" class="accessible-hide">Minimum Price</label>
						<input id="search_filter_max_price" name="min_price" type="text" placeholder="&euro; MIN" value="<?= $filters['min_price'] ?>" />

						<label for="search_filter_max_price" class="accessible-hide">Maximum Price</label>
                        <input id="search_filter_max_price" name="max_price" type="text" placeholder="&euro; MAX"  value="<?= $filters['max_price'] ?>"/>
                    </div>
                    <button class="search_btn" type="submit" >Search Used Cars</button>
                </div>

            </form>
        </div><!-- /search_filter -->

        <div id="content" class="content_area">
            <div id="banner" class="banner_area">
                <?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>

                <div id="home_panels" class="panel_area home_panel_area">
                    <?= Model_Panels::get_panels_feed('home_content'); ?>
                </div>
            </div>

            <div class="content">
                <?=$page_data['content']?>
            </div>

        </div><!-- /content -->

        <?php include 'footer.php' ?>

    </div>
    <?= Settings::instance()->get('footer_html'); ?>
</body>
<?php include 'template_views/html_document_footer.php'; ?>