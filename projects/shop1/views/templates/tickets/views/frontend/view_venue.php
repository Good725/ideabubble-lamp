<?php require_once Kohana::find_file('template_views', 'header'); ?>

<?php if ($venue == null): ?>
	<div class="row row--venue">
		<div class="widget">
			<div class="widget-body">
				<p><?= __('No venue found.') ?></p>
			</div>
		</div>
	</div>
<?php else: ?>
	<div class="row row--venue">
		<div class="widget row--venue-widget venue-img">
			<?php if ($venue['image_media_id']): ?>
		        <img class="col-sm-6" src="<?=$venue['image_media_url']?>" alt="" />
			<?php endif; ?>
		</div>
	</div>

	<div class="row row--venue">
		<div class="widget row--venue-widget">
			<div class="widget-body">
				<div class="row">
					<div class="columns small-12 medium-3 venue-text-block">
						<h4 class="text-secondary"><?= $venue['name'] ?></h4>
						<address>
							<?php if (trim($venue['address_1'])): ?>
								<span class="line"><?= $venue['address_1'] ?></span>
							<?php endif; ?>
							<?php if (trim($venue['address_2'])): ?>
								<span class="line"><?= $venue['address_2'] ?></span>
							<?php endif; ?>
							<?php if (trim($venue['address_3'])): ?>
								<span class="line"><?= $venue['address_3'] ?></span>
							<?php endif; ?>
							<?php if (trim($venue['city'])): ?>
								<span class="line"><?= $venue['city'] ?></span>
							<?php endif; ?>
							<?php if ( ! empty($venue['county'])): ?>
								<span class="line"><?= $venue['county'] ?></span>
							<?php endif; ?>
							<?php if ( ! empty($venue['country'])): ?>
								<span class="line"><?= $venue['country'] ?></span>
							<?php endif; ?>

							<?php $has_phone = (trim($venue['telephone']) >0) ? true : false; ?>
							<?php if ( ! empty($venue['telephone'])): ?>
								<span class="line"><?= $venue['telephone'] ?></span>
							<?php endif; ?>
						</address>

						<div>
							<?php $has_website = (isset($venue['website']) && $venue['website'] != '') ? true : false; ?>
							<div class="row widget-contact_details">
								<div class="widget-contact_details-item">
									<button class="button--plain" data-open="modal--contact_venue">
										<span class="flaticon-envelope venue_icon" style="font-size: 27px"></span>
										<?= __('Contact Venue') ?>
									</button>
								</div>

								<?php if ($has_website): ?>
									<div class="widget-contact_details-item">
										<a target="_blank" href="<?= (!preg_match('#^(http|https)://#i', $venue['website']) ? 'http://' : '') . $venue['website'] ?>" class="text-plain venue_website">
											<span><span class="flaticon-domain venue_icon" style="font-size: 27px"></span> Website</span>
										</a>
									</div>
								<?php endif; ?>
							</div>
							<div class="columns small-12" style="padding-left: 0">
								<ul class="social_media-list social_media-list--small">
									<?php if ($venue['twitter_url']): ?>
										<li>
											<a target="_blank" href="http://twitter.com/<?= $venue['twitter_url'] ?>" title="<?= __('Twitter') ?>">
												<span class="flaticon-twitter"></span>
											</a>
										</li>
									<?php endif; ?>
									<?php if ($venue['facebook_url']): ?>
										<li>
											<a target="_blank" href="http://facebook.com/<?= $venue['facebook_url'] ?>" title="<?= __('Facebook') ?>">
												<span class="flaticon-facebook"></span>
											</a>
										</li>
									<?php endif; ?>
									<?php if ( ! empty($venue['instagram_url'])): ?>
										<li>
											<a target="_blank" href="http://instagram.com/<?= $venue['instagram_url'] ?>" title="<?= __('Instagram') ?>">
												<span class="flaticon-instagram"></span>
											</a>
										</li>
									<?php endif; ?>
									<?php if ( ! empty($venue['snapchat_url'])): ?>
										<li>
											<a target="_blank" href="http://snapchat.com/add/<?= $venue['snapchat_url'] ?>" title="<?= __('Snapchat') ?>">
												<span class="flaticon-snapchat"></span>
											</a>
										</li>
									<?php endif; ?>
								</ul>
							</div>
						</div>
					</div>

					<div class="columns small-12 medium-9 venue-map-block">
						<div id="venue-map"
									 style="width: 100%; height: 300px;"
									 data-target-x="#edit-event-venue-lat"
									 data-target-y="#edit-event-venue-lng"
									 data-init-x="<?= $venue['map_lat']?>"
									 data-init-y="<?= $venue['map_lng']?>"
									 data-init-z="10"
									 data-button="#get-address-from-map"
									 data-button-target="#edit-event-venue-eircode"
									>
						</div>
					</div>

				</div>
			</div>
		</div>

		<h5 class="text-primary text-center upcoming"><?= __('Upcoming Events') ?></h5>

		<div class="row--event row--venue-widget">
            <div id="venue-events-list">
                <?php foreach ($events as $event) include 'snippets/event_widget.php'; ?>
            </div>

            <?php if ($total_events > $results_per_page): ?>
                <div class="text-center">
                    <button
                        type="button"
                        class="button large button--more_events"
                        data-offset="<?= $results_per_page ?>"
                        data-type="venue"
                        data-feed="#venue-events-list"
                        data-id="<?= $venue['id'] ?>"
                        ><?= __('See More Events')?></button>
                </div>
            <?php endif; ?>
		</div>
	</div>

	<div class="reveal" id="modal--contact_venue" data-reveal aria-labelledby="modal--contact_venue-title" aria-hidden="true" role="dialog">
		<h3 class="text-primary" id="modal--contact_venue-title">Contact Venue</h3>
		<button type="button" class="close-button" data-close aria-label="<?= __('Close') ?>">
			<span aria-hidden="true">&#215;</span>
		</button>

		<form action="/frontend/events/contact" method="post" class="validate-on-submit">
			<input type="hidden" name="venue_id" value="<?=$venue['id']?>" />

			<div class="row">
				<label class="columns small-4" for="modal--contact_venue-name">
					<?= __('Name') ?>
				</label>
				<div class="columns small-8">
					<input type="text" class="form_field validate[required]" id="modal--contact_venue-name" name="name" data-prompt-position="bottomRight:-300" />
				</div>
			</div>

			<div class="row">
				<label class="columns small-4" for="modal--contact_venue-email">
					<?= __('Email') ?>
				</label>
				<div class="columns small-8">
					<input type="text" class="form_field validate[required,custom[email]]" id="modal--contact_venue-email" name="email" data-prompt-position="bottomRight:-300" />
				</div>
			</div>

			<div class="row">
				<label class="columns small-4" for="modal--contact_venue-telephone">
					<?= __('Telephone') ?>
				</label>
				<div class="columns small-8">
					<input type="text" class="form_field" id="modal--contact_venue-email" name="telephone" />
				</div>
			</div>

			<div class="row">
				<label class="columns small-4" for="modal--contact_venue-message">
					<?= __('Message') ?>
				</label>
				<div class="columns small-8">
					<textarea class="form_field validate[required]" id="modal--contact_venue-message" name="message" rows="4" data-prompt-position="bottomRight:-300" ></textarea>
				</div>
			</div>

			<div class="row">
				<div class="columns small-offset-2 small-8">
					<button type="submit" class="button button--full secondary"><?= __('Send') ?></button>
				</div>
			</div>
		</form>
	</div>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= Settings::instance()->get('google_map_key') ?>"></script>
	<script>
		initMap('#venue-map');
	</script>
<?php endif; ?>

<?php require_once Kohana::find_file('template_views', 'footer'); ?>
