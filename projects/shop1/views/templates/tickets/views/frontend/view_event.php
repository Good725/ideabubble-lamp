<?php require_once Kohana::find_file('template_views', 'header');?>
<?= @$alerts?>

<?php if ($event == null OR $event['has_ended']): ?>
	<div class="row row--event">
		<div class="widget">
			<div class="widget-body">
				<p><?= __('No event found.') ?></p>
			</div>
		</div>
	</div>
<?php else: ?>
	<div class="row row--event row--event--top">
		<div class="columns small-12 medium-7 large-8 row--event-left">
			<?php if ($event['image_media_id']): ?>
				<div class="widget">
					<img src="<?=$event['image_media_url'] ?>" alt="" />
				</div>
			<?php endif; ?>

			<?php
			$ticket_widget_display = 'mobile';
			include 'snippets/ticket_widget.php';
			?>

			<div class="widget location-event widget--event_details">
				<div class="widget-body">
					<div class="row">
						<div class="columns small-12 medium-12 large-12">
							<h3 class="event-title"><?= $event['name'] ?></h3>
						</div>
					</div>
					<div class="row">
                        <?php ( ! empty($event['venue']) AND $event['display_map']) ? $descColWidth = '4' : $descColWidth = '12' ?>
						<div class="event-details-column columns small-<?= $descColWidth ?> medium-12 large-<?= $descColWidth ?>">
							<?php if ( ! empty($event['venue'])): ?>
								<h5 class="text-primary"><?= __('Event Location') ?></h5>
								<address>
									<?php if (trim($event['venue']['name'])): ?>
										<span class="line"><?= $event['venue']['name'] ?></span>
									<?php endif; ?>
									<?php if (trim($event['venue']['address_1'])): ?>
										<span class="line"><?= $event['venue']['address_1'] ?></span>
									<?php endif; ?>
									<?php if (trim($event['venue']['address_2'])): ?>
										<span class="line"><?= $event['venue']['address_2'] ?></span>
									<?php endif; ?>
									<?php if (trim($event['venue']['city'])): ?>
										<span class="line"><?= $event['venue']['city'] ?></span>
									<?php endif; ?>
									<?php if ( ! empty($event['venue']['county'])): ?>
										<span class="line">Co. <?= $event['venue']['county'] ?></span>
									<?php endif; ?>
									<?php if ( ! empty($event['venue']['country']) AND $event['venue']['country'] != 'Ireland'): ?>
										<span class="line"><?= $event['venue']['country'] ?></span>
									<?php endif; ?>
								</address>
							<?php endif; ?>

							<?php if ($event['age_restriction'] > 0) { ?>
								<h5 class="text-primary"><?= __('Age Restriction') ?></h5>
								<p class="text">+<?=$event['age_restriction']?></p>
							<?php } ?>

							<?php if ( ! empty($event['dates'])): ?>
								<?php
								$tzlist = array(
                                    "-1" => "(UTC-01:00) Azores Time Zone",
                                    "0" => "(UTC+00:00) Western European Time Zone",
                                    "1" => "(UTC+00:00) Irish Time Zone",
                                    "2" => "(UTC+00:00) Greenwich Mean Time Zone",
                                    "3" => "(UTC+01:00) Central European Time Zone",
                                    "4" => "(UTC+02:00) Eastern European Time Zone",
                                    "5" => "(UTC+03:00) Moscow Time Zone",
                                    "6" => "(UTC+03:00) Further-eastern European Time",
								);
								?>
								<?php if ($event['display_timezone']) { ?>
									<h5 class="text-primary"><?= __('Time Zone') ?></h5>
									<p class="text"><?=$tzlist[$event['timezone']]?></p>
								<?php } ?>
								<?php if ($event['display_start'] || $event['display_end']) { ?>
									<h5 class="text-primary"><?= __('Date and Time') ?></h5>
									<ul class="list-unstyled">
										<?php foreach ($event['dates'] as $date): ?>
											<?php if ($event['display_start'] && $event['display_end']) { ?>
												<li><?= date('F j, g:ia', strtotime($date['starts'])) . ($date['ends'] ? ' - ' . date('F j, g:ia', strtotime($date['ends'])) : '') ?></li>
											<?php } else if ($event['display_end'] && $date['ends']) { ?>
												<li><?= __('Ends: ') . date('F j, g:ia', strtotime($date['ends'])) ?></li>
											<?php } else { ?>
												<li><?= date('F j, g:ia', strtotime($date['starts'])) ?></li>
											<?php } ?>
										<?php endforeach; ?>
									</ul>
								<?php } ?>

								<?php if ($event['display_othertime'] == 1 && isset($event['other_times']->title) && count($event['other_times']->title) > 0 && @$event['other_times']->title[0]) { ?>
									<h5 class="text-primary"><?= __('Other Times') ?></h5>
									<ul class="list-unstyled">
										<?php foreach ($event['other_times']->title as $otIndex => $otherTime): ?>
											<li><?= __($otherTime) . ': ' . date('g:ia', strtotime($event['other_times']->time[$otIndex])) ?></li>
										<?php endforeach; ?>
									</ul>
								<?php } ?>
							<?php endif; ?>
						</div>
						<div class="event-map-column columns small-8 medium-12 large-8">
							<?php if ( ! empty($event['venue']) && $event['display_map']): ?>
								<div class="map-event">
									<div id="event-map"
										 style="width: 100%; height: 220px;"
										 data-target-x="#edit-event-venue-lat"
										 data-target-y="#edit-event-venue-lng"
										 data-init-x="<?= $event['venue']['map_lat']?>"
										 data-init-y="<?= $event['venue']['map_lng']?>"
										 data-init-z="10"
										 data-button="#get-address-from-map"
										 data-button-target="#edit-event-venue-eircode"
									></div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<div class="widget-body">
					<?= $event['description'] ?>
				</div>
			</div>

			<?php foreach ($event['videos'] as $video): ?>
				<?php if (preg_match("/(youtube.com|youtu.be)\/(watch)?(\?v=)?(\S+)?/", $video, $match)): // check if this is a valid YouTube URL ?>
					<div class="widget widget--video widget--medium">
						<iframe src="https://www.youtube.com/embed/<?= $match[4] ?>"></iframe>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>

		</div>
		<div class="columns small-12 medium-5 large-4 row--event-right">

			<?php
			$ticket_widget_display = 'desktop';
			include 'snippets/ticket_widget.php';
			?>

			<?php if ( ! empty($event['organizers']) ): ?>
				<?php $organizer = $event['organizers'][0]; ?>
				<div class="widget widget--organizers">
					<div class="widget-heading">
						<h3 class="widget-title text-primary"><a href="/organiser/<?= $organizer['url'] ?>"><?= __('Organiser Details') ?></a></h3>
					</div>
					<div class="widget-body text-center">
						<h2 class="widget--organizers-name"><?= trim($organizer['first_name'] . ' ' . $organizer['last_name']) ?></h2>

						<div class="row widget-contact_details text-center" style="font-size: .8em;">
							<?php $has_email = (trim($organizer['email'])) ? true : false; ?>
							<?php $has_website = (trim($organizer['website'])) ? true : false; ?>

							<?php if ($has_email) { ?>
								<span class="widget-contact_details-item">
									<button class="button--plain" data-open="modal--contact_organizer--primary">
										<span class="flaticon-envelope"></span>
										<?= __('Contact Organiser') ?>
									</button>
								</span>
							<?php } ?>

							<?php if ($has_website): ?>
								<span class="widget-contact_details-item">
									<a target="_blank" href="<?= (!preg_match('#^(http|https)://#i', $organizer['website']) ? 'http://' : '') . $organizer['website'] ?>" class="text-plain">
										<span class="flaticon-domain"></span>
										<?= __('Website') ?>
									</a>
								</span>
							<?php endif; ?>
						</div>

						<?php if ($organizer['twitter'] OR $organizer['facebook'] OR $organizer['instagram'] OR $organizer['snapchat']): ?>
							<ul class="social_media-list social_media-list--small">
								<?php if ($organizer['twitter']): ?>
									<li>
										<a target="_blank" href="http://twitter.com/<?= $organizer['twitter'] ?>" title="<?= __('Twitter') ?>">
											<span class="flaticon-twitter"></span>
										</a>
									</li>
								<?php endif; ?>
								<?php if ($organizer['facebook']): ?>
									<li>
										<a target="_blank" href="http://facebook.com/<?= $organizer['facebook'] ?>" title="<?= __('Facebook') ?>">
											<span class="flaticon-facebook"></span>
										</a>
									</li>
								<?php endif; ?>
								<?php if ( ! empty($organizer['instagram'])): ?>
									<li>
										<a target="_blank" href="http://instagram.com/<?= $organizer['instagram'] ?>" title="<?= __('Instagram') ?>">
											<span class="flaticon-instagram"></span>
										</a>
									</li>
								<?php endif; ?>
								<?php if ( ! empty($organizer['snapchat'])): ?>
									<li>
										<a target="_blank" href="http://snapchat.com/add/<?= $organizer['snapchat'] ?>" title="<?= __('Snapchat') ?>">
											<span class="flaticon-snapchat"></span>
										</a>
									</li>
								<?php endif; ?>
							</ul>
						<?php endif; ?>

						<p class="widget-view_more">
							<a href="/organiser/<?= $organizer['url']?>"><?= __('View More from Organiser') ?> <span class="sprite sprite-arrow"></span></a>
						</p>

					</div>

					<?php if (count($event['organizers']) > 1): ?>
						<div class="widget--organizers-other text-center">
							<p><?= (count($event['organizers']) == 2) ? __('Other Organiser') : __('Other Organisers') ?></p>

							<div class="clearfix">
								<?php foreach ($event['organizers'] as $oi => $other_organizer): ?>
									<?php $organizer_name = trim($other_organizer['first_name'].' '.$other_organizer['last_name']); ?>
									<?php if($oi == 0) continue; /*skip the first one, which is already displayed*/?>
									<a class="widget--organizers-other-link<?= (count($event['organizers']) > 4) ? ' fullwidth' : '' ?>" href="/organiser/<?= $organizer_name ?>" title="<?= $organizer_name ?>">
										<img src="/frontend/events/organiser_image/<?=$other_organizer['contact_id']?>" alt="<?= $organizer_name ?>" />
										<span class="widget--organizers-other-name"><?= $organizer_name ?></span>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty($event['venue'])): ?>
				<div class="widget widget--venue">
					<div class="widget-heading">
						<a href="/venue/<?= $event['venue']['url'] ?>"><h3 class="widget-title text-primary"><?= __('Venue Details') ?></h3></a>
					</div>
					<div class="widget-body text-center">
						<address>
							<span class="line"><?= $event['venue']['name']?></span>

							<?php if (trim($event['venue']['address_1'])): ?>
								<span class="line"><?= $event['venue']['address_1'] ?></span>
							<?php endif; ?>

							<?php if (trim($event['venue']['address_2'])): ?>
								<span class="line"><?= $event['venue']['address_2'] ?></span>
							<?php endif; ?>

							<?php if (trim($event['venue']['city'])): ?>
								<span class="line"><?= $event['venue']['city'] ?></span>
							<?php endif; ?>

						</address>

						<div class="row widget-contact_details text-center" style="font-size: .8em;">
							<?php $has_website = (trim($event['venue']['website'])) ? true : false; ?>
                            <?php $has_phone = (trim($event['venue']['telephone']) > 0) ? true : false; ?>
                            <?php $has_email = (trim($event['venue']['email'])) ? true : false; ?>

                            <?php if ($has_email): ?>
								<span class="widget-contact_details-item">
									<button class="button--plain" data-open="modal--contact_venue">
										<span class="flaticon-envelope"></span>
										<?= __('Contact Venue') ?>
									</button>
								</span>
                            <?php endif; ?>
							<?php if ($has_website): ?>
								<span class="widget-contact_details-item">
									<a target="_blank" href="<?= (!preg_match('#^(http|https)://#i', $event['venue']['website']) ? 'http://' : '') . $event['venue']['website'] ?>" class="text-plain">
										<span class="flaticon-domain venue_domain"></span>
										<?= __('Website') ?>
									</a>
								</span>
							<?php endif; ?>
						</div>

						<?php if ($event['venue']['facebook_url'] OR $event['venue']['twitter_url'] OR $event['venue']['instagram_url'] OR $event['venue']['snapchat_url']): ?>
							<ul class="social_media-list social_media-list--small">
								<?php if ($event['venue']['twitter_url']): ?>
									<li>
										<a target="_blank" href="http://twitter.com/<?= $event['venue']['twitter_url'] ?>" title="<?= __('Twitter') ?>">
											<span class="flaticon-twitter"></span>
										</a>
									</li>
								<?php endif; ?>
								<?php if ($event['venue']['facebook_url']): ?>
									<li>
										<a target="_blank" href="http://facebook.com/<?= $event['venue']['facebook_url'] ?>" title="<?= __('Facebook') ?>">
											<span class="flaticon-facebook"></span>
										</a>
									</li>
								<?php endif; ?>
								<?php if ( ! empty($event['venue']['instagram_url'])): ?>
									<li>
										<a target="_blank" href="http://instagram.com/<?= $event['venue']['instagram_url'] ?>" title="<?= __('Instagram') ?>">
											<span class="flaticon-instagram"></span>
										</a>
									</li>
								<?php endif; ?>
								<?php if ( ! empty($event['venue']['snapchat_url'])): ?>
									<li>
										<a target="_blank" href="http://snapchat.com/add/<?= $event['venue']['snapchat_url'] ?>" title="<?= __('Snapchat') ?>">
											<span class="flaticon-snapchat"></span>
										</a>
									</li>
								<?php endif; ?>
							</ul>
						<?php endif; ?>

						<p class="widget-view_more">
							<a href="/venue/<?= $event['venue']['url'] ?>"><?= __('View More from Venue') ?> <span class="sprite sprite-arrow"></span></a>
						</p>

					</div>
				</div>
			<?php endif; ?>

			<?php foreach ($event['videos'] as $video): ?>
				<?php if (preg_match("/(youtube.com|youtu.be)\/(watch)?(\?v=)?(\S+)?/", $video, $match)): // check if this is a valid YouTube URL ?>
					<div class="widget widget--video widget--small widget--large">
						<iframe src="https://www.youtube.com/embed/<?= $match[4] ?>"></iframe>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>

		</div>
	</div>

	<?php if ( ! empty($event['venue'])): ?>
		<div class="reveal" id="modal--contact_venue" data-reveal aria-labelledby="modal--contact_venue-title" aria-hidden="true" role="dialog">
			<h3 class="text-primary" id="modal--contact_venue-title">Contact Venue</h3>
			<button type="button" class="close-button" data-close aria-label="<?= __('Close') ?>">
				<span aria-hidden="true">&#215;</span>
			</button>

			<form action="/frontend/events/contact" method="post" class="validate-on-submit">
				<input type="hidden" name="venue_id" value="<?=$event['venue']['id']?>" />
				<input type="hidden" name="event_id" value="<?=$event['id']?>" />

                <div class="row">
                    <label class="columns small-4" for="modal--contact_venue-name">
                        <?= __('Name') ?>
                    </label>
                    <div class="columns small-8">
                        <input type="text" class="form_field validate[required]" id="modal--contact_venue-name" name="name" />
                    </div>
                </div>

                <div class="row">
					<label class="columns small-4" for="modal--contact_venue-email">
						<?= __('Email') ?>
					</label>
					<div class="columns small-8">
						<input type="text" class="form_field validate[required,custom[email]]" id="modal--contact_venue-email" name="email" />
					</div>
				</div>

				<div class="row">
					<label class="columns small-4" for="modal--contact_venue-telephone">
						<?= __('Telephone') ?>
					</label>
					<div class="columns small-8">
						<input type="text" class="form_field" id="modal--contact_venue-telephone" name="telephone" />
					</div>
				</div>

				<div class="row">
					<label class="columns small-4" for="modal--contact_venue-message">
						<?= __('Message') ?>
					</label>
					<div class="columns small-8">
						<textarea class="form_field validate[required]" id="modal--contact_venue-message" name="message" rows="4"></textarea>
					</div>
				</div>

				<div class="row">
					<div class="columns small-offset-2 small-8">
						<button type="submit" class="button button--full secondary"><?= __('Send') ?></button>
					</div>
				</div>
			</form>
		</div>
    <?php endif; ?>

	<?php if ( ! empty($event['organizers']) ): ?>
		<?php $organizer = $event['organizers'][0]; /*first organizer is always the primary one and only primary one*/ ?>
			<div class="reveal" id="modal--contact_organizer--primary" data-reveal aria-labelledby="modal--contact_organizer--primary-title" aria-hidden="true" role="dialog">
				<h3 class="text-primary" id="modal--contact_organizer--primary-title"><?= __('Contact Organiser') ?></h3>
				<button type="button" class="close-button" data-close aria-label="<?= __('Close') ?>">
					<span aria-hidden="true">&#215;</span>
				</button>
				<form action="/frontend/events/contact" method="post" class="validate-on-submit">
					<input type="hidden" name="event_id" value="<?=$event['id']?>" />
					<input type="hidden" name="organiser_id" value="<?=$organizer['id']?>" />
					<div class="row">
						<label class="columns small-4" for="modal--contact_organizer-name">
							<?= __('Name') ?>
						</label>
						<div class="columns small-8">
							<input type="text" class="form_field" id="modal--contact_organizer-name" name="name" />
						</div>
					</div>

					<div class="row">
						<label class="columns small-4" for="modal--contact_organizer-email">
							<?= __('Email') ?>
						</label>
						<div class="columns small-8">
							<input type="text" class="form_field validate[required,custom[email]]" id="modal--contact_organizer-email" name="email" />
						</div>
					</div>

					<div class="row">
						<label class="columns small-4" for="modal--contact_organizer-telephone">
							<?= __('Telephone') ?>
						</label>
						<div class="columns small-8">
							<input type="text" class="form_field" id="modal--contact_organizer-email" name="telephone" />
						</div>
					</div>

					<div class="row">
						<label class="columns small-4" for="modal--contact_organizer-message">
							<?= __('Message') ?>
						</label>
						<div class="columns small-8">
							<textarea class="form_field" id="modal--contact_organizer-message" name="message" rows="4"></textarea>
						</div>
					</div>

					<div class="row">
						<div class="columns small-offset-2 small-8">
							<button type="submit" class="button button--full secondary"><?= __('Send') ?></button>
						</div>
					</div>
				</form>
			</div>
	<?php endif; ?>

	<?php if ( ! empty($event['venue']) && $event['display_map']): ?>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= Settings::instance()->get('google_map_key') ?>"></script>
		<script>
			initMap('#event-map');
		</script>
	<?php endif; ?>
	<script>
		window.currencies = <?=json_encode(Model_Currency::getCurrencies(true))?>;
        <?php if ($preview) { ?>
        try {
            window.opener.document.getElementById("edit-event-id").value = <?=$event['id']?>;
        } catch (exc) {
            console.log(exc);
        }
        <?php } ?>
		$(".event-book[value=buy]").on("click", function(ev){		
			if ($(this).parents('form').validationEngine('validate'))
			{
				var $form = $('.checkout_form:visible');
				var validate = false;
				$form.find('.form_field').each(function(){
					var res=$(this).val();
					ev.preventDefault();
				    if(res > 0 && res != null){
				      validate = true;
				    }
				});
				if(!validate){
					ev.preventDefault();
					$form.find('.qty_error').show();
				}else{
				    $form.submit();
			    }
			}else{
				ev.preventDefault();
			}
		});
		$(".ticket_error").on("click", function(){	
			$(this).hide();
		});
		$('.checkout_form .form_field').on('click keyup', function(e){
			var $form = $(this).parents('form');
			$form.find('.qty_error').hide();
			var remaining_tckt=$(this).attr('data-remaining');
			var selcted_tckt=$(this).val();
			var max_tckt=$(this).attr('max');
			if(parseInt(selcted_tckt) > parseInt(remaining_tckt)){
				$(this).parent('.ticket-val').parent('.ticket-container').prev(".ticket_error").show();
				$(this).val(max_tckt);	
			}else if((parseInt(max_tckt) != 0) && (parseInt(selcted_tckt) > parseInt(max_tckt))){
				  $(this).parent('.ticket-val').parent('.ticket-container').prev(".ticket_error").html('Max Limit Reached For This Ticket').show();
				  $(this).val(max_tckt);
			}else{
				$(this).parent('.ticket-val').parent('.ticket-container').prev(".ticket_error").hide();
			}	
		});
        $(".validate-on-submit").on("submit", function(){
            if (/[a-z][a-z0-9\-\._]+@[a-z0-9\-_\.]+\.[a-z]{2,3}/i.test(this.email.value) == false) {
                alert("Please enter a valid email!");
                this.email.focus();
                return false;
            }
            if (this.message.value == "") {
                alert("Please enter your message!");
                this.message.focus();
                return false;
            }
            return true;
        });
	</script>
<?php endif; ?>
<?php require_once Kohana::find_file('template_views', 'footer'); ?>
