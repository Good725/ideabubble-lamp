<?php
$parsed_url = explode('/', urldecode(trim($_SERVER['SCRIPT_URL'], '/')));
$id         = (count($parsed_url) > 1) ? $parsed_url[count($parsed_url) - 1] : NULL;
$car        = new Model_Cars($id);
$images     = $car->get_photos();
$car        = $car->get(FALSE);
?>
<?php include 'template_views/html_document_header.php'; ?>
    <body class="content_layout">
    <div id="container" class="container">
        <?php include 'header.php' ?>

        <div id="content" class="content_area">
            <div class="content"><?= $page_data['content'] ?></div>

			<?php if (count($car) < 1): ?>
				<p>Error: No such car</p>
			<?php else: ?>
				<h1 class="product_name"><?= (($car['year'] != '') ? $car['year'].' - ' : '').$car['make']. ' '.$car['model'] ?></h1>

				<div class="car_details_wrapper">
					<div id="car_details_images" class="car_details_images">
						<div id="main_car_image" class="main_car_image">
							<?php if (isset($images[0]) AND $images[0] != ''): ?>
								<img src="<?= $images[0] ?>" />
							<?php else: ?>
								<img src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'','products').'no_image_available.jpg' ?>" />
							<?php endif; ?>
						</div>

						<?php if (isset($images[1])): ?>
							<div class="alternate_car_images" id="alternate_car_images">
								<ul>
									<?php for ($i = 0; $i < count($images); $i++): ?>
										<li<?= ($i == 0) ? ' class="selected"' : '' ?>>
											<img src="<?= $images[$i] ?>" alt="" />
										</li>
									<?php endfor; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
					<div class="car_details_description">
						<div class="car_details_data quick_data">
							<?php if ($car['odometer'] != '' AND $car['odometer'] != 0): ?>
								<dl>
									<dt class="label-odometer">Odometer</dt>
									<dd><?= number_format(floatval($car['odometer'])) ?></dd>
								</dl>
							<?php endif; ?>
							<?php if ($car['color'] != ''): ?>
								<dl>
									<dt class="label-color">Colour</dt>
									<dd><?= $car['color'] ?></dd>
								</dl>
							<?php endif; ?>
							<?php if ($car['price'] != '' AND $car['price'] != 0): ?>
								<dl>
									<dt class="label-price">Price</dt>
									<dd>&euro;<?= number_format(floatval($car['price'])) ?></dd>
								</dl>
							<?php endif; ?>
						</div>

						<div class="full_car_data">
							<dl>
								<dt>Dealer</dt>
								<dd>Donal Ryan Motor Group (<?= $car['dealer_domain'] ?>) Ltd.</dd>

								<dt>Make</dt>
								<dd><?= $car['make'] ?></dd>

								<dt>Model</dt>
								<dd><?= $car['model'] ?></dd>

								<dt>Description</dt>
								<dd><?= $car['description'] ?></dd>

								<dt>Vehicle Type</dt>
								<dd><?= $car['category'] ?></dd>

								<dt>Body Type</dt>
								<dd><?= $car['body_type'] ?></dd>

								<dt>Year</dt>
								<dd><?= $car['year'] ?></dd>

								<dt>Price</dt>
								<dd><?= $car['price'] ?></dd>

								<dt>Engine</dt>
								<dd><?= $car['engine'].(($car['fuel'] != '') ? ' ('.$car['fuel'].')' : '') ?></dd>

								<dt>Transmission</dt>
								<dd><?= $car['transmission'] ?></dd>

								<dt>Odometer</dt>
								<dd><?= ($car['odometer'] != '') ? $car['odometer'].' Kilometers' : '' ?></dd>

								<dt>Colour</dt>
								<dd><?= $car['color'] ?></dd>

								<dt>No. of Seats</dt>
								<dd><?= $car['no_of_seats'] ?></dd>

								<dt>No. of Doors</dt>
								<dd><?= $car['doors'] ?></dd>

								<dt>Extra Comments</dt>
								<dd class="prose_section"><?= $car['comments'] ?></dd>

								<dt>Interested?</dt>
								<dd class="prose_section"></dd>
							</dl>
						</div>
					</div><!-- .car_details_description -->
				</div><!-- .car_details_wrapper -->
			<?php endif; ?>
        </div><!-- #content -->
        <?php include 'footer.php' ?>

    </div>
    <?= Settings::instance()->get('footer_html'); ?>
    </body>
<?php include 'template_views/html_document_footer.php'; ?>
<script>
	$('#alternate_car_images').on('click', 'img', function()
	{
		$('#alternate_car_images').find('.selected').removeClass('selected');
		$(this).parents('li').addClass('selected');
		$('#main_car_image').find('img').attr('src', this.src);
	});
</script>