<?php include 'template_views/header.php' ?>
<?php $rates = Model_Propman::getRates($property_data->group_id, $property_data->property_type_id, time()); ?>
<?php if ($property_data->id): ?>
	<div class="property-details-wrapper<?= ( ! isset($property_photos[0])) ? ' property-details-nobanner' : '' ?>" id="property-details-wrapper">
		<div class="property-data">
			<div class="property-data-summary">
                <input type="hidden" id="property_id" value="<?=$property_data->id?>"/>
                <script>get_unavailable_dates();</script>
				<h1><?= $property_data->name ?></h1>

				<ul class="property-quick-details">
					<li><?= $property_data->building_type->name ?></li>
					<li>Sleeps <?= $property_data->max_occupancy ?></li>
					<li><?= $property_data->count_beds() ?> Beds</li>
				</ul>

				<section>
					<?= $property_data->description ?>
				</section>

				<?php $facilities = $property_data->facility_types->order_by('sort')->find_all_published(); ?>
				<?php $facility_count = count($facilities); ?>

				<?php if ($facility_count > 0): ?>
					<section class="property-list-group">
						<h3><?= __('Facilities') ?></h3>

						<?php // Put each group of six into a separate list  ?>
						<?php foreach ($facilities as $key => $facility): ?>
							<?php if ($key % 6 == 0): ?>
								<div class="col-xsmall-12 col-small-4">
									<ul>
							<?php endif; ?>
										<li><?= $facility->name ?></li>
							<?php if ($key % 6 == 5 OR $key == $facility_count - 1): ?>
									</ul>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</section>
				<?php endif; ?>

				<?php $suitabilities = $property_data->suitability_types->order_by('sort')->find_all_published(); ?>
				<?php $suitability_count = count($suitabilities); ?>

				<?php if ($suitability_count > 0): ?>
					<section class="property-list-group">
						<h3><?= __('Suitabilities') ?></h3>

						<?php // Put each group of six into a separate list  ?>
						<?php foreach ($suitabilities as $key => $suitability): ?>
							<?php if ($key % 6 == 0): ?>
								<div class="col-xsmall-12 col-small-4">
									<ul>
							<?php endif; ?>
										<li><?= $suitability->name ?></li>
							<?php if ($key % 6 == 5 OR $key == $suitability_count - 1): ?>
									</ul>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</section>
				<?php endif; ?>
			</div>

			<?php if (count($rates) > 0): ?>
				<button type="button"
						class="btn-plain collapse-toggle property-rates-collapse"
						data-target="#property-rates-section"
						data-show_text="<?= __('+ Show rates') ?>"
						data-hide_text="<?= __('- Hide rates') ?>"
					><?= __('+ Show rates') ?></button>
				<section id="property-rates-section" style="display: none;">

					<table class="table-striped">
						<thead>
							<tr>
								<th scope="col"><?= __('Date') ?></th>
								<th scope="col"><?= __('Weekly') ?></th>
								<th scope="col"><?= __('Short stay') ?></th>
								<th scope="col"><?= __('Additional nights') ?></th>
								<th scope="col"><?= __('Min. stay') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($rates as $rate): ?>
								<tr>
									<td>
										<?= date('j M Y', strtotime($rate['starts'])) ?>&nbsp;&ndash;
										<?= date('j M Y', strtotime($rate['ends'])) ?>
									</td>
									<td>&euro;<?= number_format($rate['weekly_price'], 2) ?></td>
									<td>&euro;<?= number_format($rate['short_stay_price'], 2) ?></td>
									<td>&euro;<?= number_format($rate['additional_nights_price'], 2) ?></td>
									<td><?= $rate['min_stay'] ?> days</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</section>
			<?php endif; ?>

			<?php if (isset($property_photos) AND count($property_photos) > 1): ?>
				<?php
				$gallery_data = array();
				foreach ($property_photos as $key => $photo)
				{
					// First image is the banner
					if ($key != 0)
					{
						$gallery_data[] = array( 'src' => $photo->filepath, 'w' => $photo->width, 'h' => $photo->height);
					}
				}
				?>

				<section class="clearfix compact-cols property-data-images">
					<?php for ($i = 1; $i < count($property_photos) AND $i <= 3; $i++): ?>
						<?php if ($i == count($property_photos) - 1 OR $i == 3): ?>
							<div class="col-xsmall-12 col-small-4">
								<figure class="see-all-photos-wrapper">
									<img class="image-full" src="<?= $property_photos[$i]->thumb_filepath ?>" />
									<figcaption class="see-all-photos-caption">
										<button
											type="button"
											class="button-link see-all-photos-button photoswipe-button"
											data-images="<?= htmlentities(json_encode($gallery_data)) ?>"
											>See all photos</button>
									</figcaption>
								</figure>
							</div>
						<?php else: ?>
							<div class="col-xsmall-6 col-small-4">
								<img class="image-full" src="<?= $property_photos[$i]->thumb_filepath ?>" />
							</div>
						<?php endif; ?>
					<?php endfor; ?>
				</section>
			<?php endif; ?>

			<?php // Map ?>
			<?php if ($property_data->latitude and $property_data->longitude): ?>
				<?php
				$needles = array(array($property_data->name, $property_data->latitude , $property_data->longitude));
				$info_windows = array(array('<div class="map-window"><div class="related-property">'.View::factory('property_thumbnail')->set('property_item', $property_data)->set('description', FALSE).'</div></div>'));
				?>
				<section class="property-map" id="property-data-map">
					<div class="map-canvas" id="map-canvas" data-needles="<?= htmlentities(json_encode($needles)) ?>" data-infowindows="<?= htmlentities(json_encode($info_windows)) ?>"></div>
				</section>
			<?php endif; ?>

			<?php $related_properties = $property_data->linked_properties->order_by('sort')->find_all() ?>

			<?php if (count($related_properties) > 0): ?>
				<section class="related-properties-wrapper">
					<h2><?= __('You might also like') ?></h2>
					<div class="space-between-cols related-properties">
						<?php foreach ($related_properties as $related_property): ?>
							<div class="col-xsmall-6 col-small-4 related-property">
								<?= View::factory('property_thumbnail')->set('property_item', $related_property) ?>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>
		</div>

		<div class="space-between-cols property-options-wrapper" id="property-data-sidebar">
			<div class="clearfix" id="property-data-sidebar-inner">
                <?php if(isset($rates[0]) AND isset($rates[0]['weekly_price'])): ?>
				<form class="col-xsmall-12 col-small-6 col-medium-12 validate-on-submit" action="/booking.html" id="property-booking-form">
					<input type="hidden" name="property_id" value="<?= $property_data->id ?>"/>
					<div class="property-data-ratecard" id="property-data-ratecard">
						<?php if (isset($rates[0]) AND isset($rates[0]['weekly_price'])): ?>
							<span class="property-data-ratecard-price">&euro;<?= $rates[0]['weekly_price'] ?></span>
							<span class="property-data-ratecard-rate">Per week</span>
						<?php endif; ?>
					</div>
					<div class="property-options">
						<div class="daterangepicker property-options-fields" id="booking-date-range" data-check-rates="yes" >
							<div class="col-xsmall-5 col-small-12 col-medium-5">
								<label for="property-details-check_in"><?= __('Check in') ?></label>
								<div class="select">
									<input type="text" class="input-styled validate[required] daterangepicker-start" id="property-details-check_in" name="check_in" placeholder="dd/mm/yyyy" value="<?=isset($_GET['check_in']) ? htmlspecialchars($_GET['check_in']) : ''?>" />
								</div>
							</div>

							<div class="col-xsmall-5 col-small-12 col-medium-5">
								<label for="property-details-check_out"><?= __('Check out') ?></label>
								<div class="select">
									<input type="text" class="input-styled validate[required] daterangepicker-end" id="property-details-check_out" name="check_out" placeholder="dd/mm/yyyy" value="<?=isset($_GET['check_in']) ? htmlspecialchars($_GET['check_out']) : ''?>"  />
								</div>
							</div>
							<div class="col-xsmall-2 col-small-12 col-medium-2">
								<label for="property-details-number_of_guests"><?= __('Guests') ?></label>
								<div class="select">
									<select class="input-styled" id="property-details-number_of_guests" name="guests">
										<?php $guests = isset($_GET['guests']) ? $_GET['guests'] : 1 ?>
										<?php for ($i = 1; $i <= $property_data->max_occupancy; $i++): ?>
											<option value="<?= $i ?>"<?= ($guests == $i) ? ' selected="selected"' : ''?>>
												<?= $i ?>
											</option>
										<?php endfor; ?>
									</select>
								</div>
							</div>
						</div>

						<div class="property-details-pricing">
							<?php
							$price = null;
							if (isset($_GET['check_in']) && isset($_GET['check_out']) && isset($_GET['guests'])) {
								$price = Model_Propman::calculatePrice(
                                    $property_data->id,
                                    date::dmy_to_ymd($_GET['check_in']),
                                    date::dmy_to_ymd($_GET['check_out']),
                                    $_GET['guests']
                                );
							}
							$showPrice = $price && ($price['error'] == false);
							?>
							<table class="property-details-pricing-table">
								<tbody>
								<tr <?=$showPrice == false ? 'style="display:none;"' : ''?>>
									<td width="90%">Rental Rate (<span id="booking-nights-count"><?= $showPrice ? $price['nights'] : '' ?></span> Nights)</td>
									<td>&euro;<span id="booking-fee"><?= $showPrice ? $price['fee'] : '' ?></span> </td>
								</tr>
                                <tr <?=$showPrice == false ? 'style="display:none;"' : ''?>>
									<td width="90%"><?= __('Booking fee') ?></td>
									<td>&euro;<span id="booking-booking-fee"><?= $showPrice ? 10 : '' ?></span></td>
								</tr>
								<tr <?=$showPrice == false ? 'style="display:none;"' : ''?>>
									<td width="90%"><?= __('Discount') ?></td>
									<td>&euro;<span id="booking-discount"><?= $showPrice ? $price['discount'] : '' ?></span></td>
								</tr>
								<tr <?=$showPrice == false ? 'style="display:none;"' : ''?>>
									<td width="90%"><strong><?= __('Total') ?></strong></td>
									<td>&euro;<span id="booking-total"><?= $showPrice ? ($price['fee'] + 10 - $price['discount']): '' ?></span></td>
								</tr>
								</tbody>
							</table>
						</div>

                        <div id="not-available-details" style="display: none;"><p></p></div>

						<div class="property-options-booking">
							<button type="submit" class="button-primary book-button" <?=$showPrice ? '' : 'disabled="disabled"'?>><?=__('Book Now')?></button>
						</div>
					</div>
				</form>
                <?php endif; ?>

				<div class="col-xsmall-12 col-small-6 col-medium-12">
					<div class="property-options">
						<?php if($property_data->ref_code !== '' OR !is_null($property_data->ref_code)): ?>
							<h3><?= __('Property Reference:') ?></h3>
						<?php endif; ?>
						<div class="compact-cols">
							<div class="col-xsmall-6 col-small-12 col-medium-6 property-options-reference-number"><?= $property_data->ref_code ?></div>
							<div class="col-xsmall-6 col-small-12 col-medium-6">
								<?php
								$on_wishlist          = $property_data->is_on_wishlist();
								$wishlist_remove_text = __('Remove from wishlist');
								$wishlist_add_text    = __('Add to wishlist');?>

								<button
									type="button"
									class="button-full button-wishlist button-wishlist-<?= $on_wishlist ? 'remove' : 'add' ?>"
									data-id="<?= $property_data->id ?>"
									data-add_text="<?= $wishlist_add_text ?>"
									data-remove_text="<?= $wishlist_remove_text ?>"
									><?= $on_wishlist ? $wishlist_remove_text : $wishlist_add_text ?></button>
							</div>
						</div>
					</div>
					<div class="compact-cols property-option-buttons">
						<div class="col-xsmall-6 col-small-12 col-medium-6"><a href="/contact-us.html?contact_type=callback&property_id=<?= $property_data->id ?>" class="button-callback button-full"><?= __('Call me back')?></a></div>
						<div class="col-xsmall-6 col-small-12 col-medium-6"><a href="/contact-us.html?contact_type=email&property_id=<?= $property_data->id ?>" class="button-email button-full"><?= __('Email')?></a></div>
						<? /*
					<div class="col-xsmall-4 col-small-12 col-medium-4"><a href="" class="button-more button-full"><?= __('More...')?></a></div>
 					*/ ?>
					</div>
				</div>

			</div>
		</div>

	</div>
<?php endif; ?>
<?php include 'template_views/footer.php' ?>