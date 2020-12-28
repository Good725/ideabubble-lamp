<?= isset($alert) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<?php
$currencies = Model_Currency::getCurrencies(true);
$cursym = $currencies[@$event['currency'] ? $event['currency'] : 'EUR']['symbol'];
?>
<div>

	<ul class="nav nav-tabs<?= ! is_numeric($event['id']) ? ' hidden' : '' ?>" role="tablist">
		<li role="presentation" class="active"><a href="#edit-event-tab-details"  aria-controls="edit-event-tab-details"  role="tab" data-toggle="tab"><?= __('Details')  ?></a></li>
        <?php if (is_numeric($event['id'])) { ?>
		<li role="presentation"><a href="#edit-event-attendees" aria-controls="edit-event-attendees" role="tab" data-toggle="tab"><?= __('Attendees') ?></a></li>
		<li role="presentation"><a href="#edit-event-checkin" aria-controls="edit-event-checkin" role="tab" data-toggle="tab"><?= __('Check In') ?></a></li>
		<?php if($edit_seo) { ?>
		<li role="presentation"><a href="#edit-event-seo" aria-controls="edit-event-seo" role="tab" data-toggle="tab"><?= __('SEO') ?></a></li>
		<?php } ?>
        <?php } ?>
	</ul>

	<div class="tab-content">

        <!-- event editor -->
		<div role="tabpanel" class="tab-pane active" id="edit-event-tab-details">
			<form id="event-edit" class="form-horizontal event-edit validate-on-submit" method="post" action="/admin/events/save_event/<?= $event['id'] ?>">
				<input type="hidden" id="edit-event-id" name="id" value="<?= $event['id'] ?>" />
				<input type="hidden" name="is_onsale" value="<?= $event['is_onsale'] ? $event['is_onsale'] : 0 ?>" />
				<input type="hidden" name="publish" value="<?=$event['publish']?>"  />
                <div class="req-field"> <?= __('* indicates mandatory fields') ?></div>
				<section class="form-section active">
					<h2>1. <?= __('Event Details') ?></h2>
					<div class="edit-event-details_section">
						<div class="form-group clearfix">
							<label class="col-sm-2 control-label control-label-fixed" for="edit-event-name"><?= __('Event Name') ?> <span>*</span></label>
							<div class="col-sm-10">
								<input type="text" class="form-control ib_text_title_input required validate[required]" id="edit-event-name" name="name" value="<?= $event['name'] ?>" placeholder="<?= __('Event name') ?>" />
							</div>
						</div>

						<div class="form-group">
                            <div class="col-sm-12 text-danger text-center error-area" id="event-image-error-area"></div>

							<label class="col-sm-12 control-label" for="edit-event-banner"><?= __('Event Image') ?> <span>*</span></label>
							<div class="col-sm-12 image-upload-wrapper">
								<div>
									<?= View::factory('multiple_upload', array('name' => 'event_image', 'single' => true, 'preset' => 'Event banners', 'onsuccess' => 'event_image_uploaded', 'presetmodal' => 'no', 'duplicate' => 0)) ?>
								</div>
							</div>
							<div class="col-sm-12 saved-image">
                                <input type="hidden" name="event_image_media_id" value="<?=$event['image_media_id']?>" />
                                <?php if ($event['image_media_id']) { ?>
                                <img id="event_image_saved" class="col-sm-6 saved-image" src="<?=$event['image_media_url']?>" alt="" title="<?__('Click to edit')?>" style="cursor:pointer;" />
                                <button type="button" class="btn-link saved-image-remove">
                                    <span class="icon-trash"></span>
                                </button>
                                <?php } ?>
                            </div>

						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label control-label-fixed" for="edit-event-web_address_url"><?= __('Event URL') ?></label>
							<div class="col-sm-10">
								<div class="input-group" style="width: 100%;">
									<input type="text" class="form-control" id="edit-event-web_address" placeholder="https://<?=$_SERVER['HTTP_HOST']?>/event/" disabled="disabled" style="width: 50%;" />
									<input type="text" class="form-control validate[required]" id="edit-event-web_address_url" name="url" value="<?=$event['url']?>" placeholder="<?= __('Web URL') ?> *" style="width: 50%;" pattern="[a-zA-Z0-9\-]+" />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label control-label-fixed" for="edit-event-video-1"><?= __('Event Videos') ?></label>
							<div class="col-sm-7" id="event-videos">
							</div>
							<div class="col-sm-3">
								<button type="button" class="btn btn-actions btn-full video_btn" data-toggle="collapse" data-target="#edit-event-add-new-video-panel">
									<span class="icon-plus"></span> <?= __('Add Another Video') ?>
								</button>

							</div>
						</div>

						<div>
							<div class="collapse" id="edit-event-add-new-video-panel">
								<div class="panel panel-default">
									<div class="panel-body">
										<div class="col-sm-5">
											<input type="text" class="form-control" id="edit-event-video-url-new" />
										</div>
										<div class="col-sm-2">
											<button type="button" class="btn btn-default" id="edit-event-add-video-btn"><?= __('Add') ?></button>
										</div>
										<div class="col-sm-3"><i>Enter a video url to showcase the event</i></div>
										<div class="col-sm-2 text-right">
											<button type="button" id="event-view-new-hide-btn" class="btn-link" data-toggle="collapse" data-target="#edit-event-add-new-video-panel"><?= __('hide') ?></button>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>

					<div class="form-group" style="margin-bottom: 0;">
						<div class="col-sm-12">
							<h2 class="location-event-header"><?= __('Location') ?></h2>
						</div>
						<div class="col-sm-12" id="edit-event-location-fields">

							<?php /*
							<div class="clearfix">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="sr-only" for="edit-event-venue"><?= __('Choose venue') ?></label>
										<select class="form-control" id="edit-event-venue" name="venue_id">
											<option value="">-- Choose an existing venue -- </option>
											<?=html::optionsFromRows('id', 'name', $venues, $event['venue_id'])?>
										</select>
									</div>
								</div>
							</div>
							*/ ?>

							<div class="clearfix">
								<div class="col-sm-6">
									<input type="hidden" id="edit-event-venue-id" name="venue[id]" value="<?=$event['venue']['id']?>" />
									<div class="form-group">
										<label class="sr-only" for="edit-event-venue-name"><?= __('Venue Name') ?> <span>*</span></label>
										<input type="text" class="form-control validate[required]" id="edit-event-venue-name" name="venue[name]" value="<?=$event['venue']['name']?>" placeholder="<?= __('Venue Name') ?> *" />
									</div>

									<div class="form-group">
										<label class="sr-only" for="edit-event-venue-address_1"><?= __('Address line 1') ?> <span>*</span></label>
										<input type="text" class="form-control validate[required]" id="edit-event-venue-address_1" name="venue[address_1]" value="<?=$event['venue']['address_1']?>" placeholder="<?= __('Address Line 1') ?> *" />
									</div>

									<div class="form-group">
										<label class="sr-only" for="edit-event-venue-address_2"><?= __('Address line 2') ?></label>
										<input type="text" class="form-control" id="edit-event-venue-address_2" name="venue[address_2]" value="<?=$event['venue']['address_2']?>" placeholder="<?= __('Address Line 2') ?>" />
									</div>

									<div class="form-group">
										<label class="sr-only" for="edit-event-venue-city"><?= __('Town/City') ?></label>
										<input type="text" class="form-control validate[required]" id="edit-event-venue-city" name="venue[city]" value="<?=$event['venue']['city']?>" placeholder="<?= __('Town/City') ?> *" />
									</div>
									<div class="form-group">
										<label class="sr-only" for="edit-event-venue-country"><?= __('Country') ?> <span>*</span></label>
										<select name="venue[country_id]" class="form-control validate[required]" id="edit-event-venue-country">
											<option value=""><?= __('Country') ?> *</option>
											<?=html::optionsFromRows('id', 'name', $countries, $event['venue']['country_id'])?>
										</select>
									</div>
									<script>
										window.venue_countries = <?=json_encode($countries)?>;
									</script>

                                    <?php
                                    $county_set = false;
                                    if (@$event['venue']['country_id']) {
                                        foreach ($countries as $ecountry) {
                                            if ($ecountry['id'] == $event['venue']['country_id']) {
                                                $county_set = true;
                                                break;
                                            }
                                        }
                                    }
                                    ?>
									<div class="form-group">
										<label class="sr-only" for="edit-event-venue-county"><?= __('County') ?> <span>*</span></label>
										<select name="venue[county_id]" class="form-control validate[required]" id="edit-event-venue-county" <?= (@$event['venue']['country_id'] && (!isset($ecountry) || empty($ecountry['counties']))) ? 'style="display: none;"' : '' ?>>
											<option value=""><?= __('County') ?> *</option>
                                            <?php
                                            if ($event['venue']['country_id'] && $county_set == true) {
                                                echo html::optionsFromRows('id', 'name', $ecountry['counties'], $event['venue']['county_id']);
                                            }
                                            ?>
										</select>
									</div>

									<div class="form-group">
										<label class="sr-only" for="edit-event-venue-lat"><?= __('Latitude') ?></label>
										<input type="text" class="form-control" id="edit-event-venue-lat" name="venue[map_lat]" value="<?= (isset($event['venue']) AND isset($event['venue']['map_lat'])) ? $event['venue']['map_lat'] : '' ?>" readonly="readonly" placeholder="<?= __('Latitude') ?>" />
									</div>

									<div class="form-group">
										<label class="sr-only" for="edit-event-venue-lng"><?= __('Longitude') ?></label>
										<input type="text" class="form-control" id="edit-event-venue-lng" name="venue[map_lng]" value="<?= (isset($event['venue']) AND isset($event['venue']['map_lng'])) ? $event['venue']['map_lng'] : '' ?>" readonly="readonly" placeholder="<?= __('Longitude') ?>" />
									</div>

									<div class="form-group">
										<div class="form-group">
											<div class="col-sm-6">
												<button type="button" class="btn btn-full btn-actions" id="edit-event-location-reset-button"><?= __('Reset location') ?></button>
											</div>
											<div class="col-sm-6">
												<button type="button" class="btn btn-full btn-actions" data-toggle="collapse" data-target="#edit-event-contact_details-panel"><?= __('Add Venue Contact Details?') ?></button>
											</div>
										</div>
									</div>

								</div>
								<div class="col-sm-6 edit-event-map-wrapper">
									<!-- <iframe style="border:1px solid #ccc;width:100%;height:220px;" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed"></iframe> -->
									<div class="form-group">
										<div class="col-sm-12">
											<div class="map-container"
												 style="width: 100%; height: 276px;"
												 data-target-x="#edit-event-venue-lat"
												 data-target-y="#edit-event-venue-lng"
												 data-init-x="<?=@$event['venue']['map_lat']?>"
												 data-init-y="<?=@$event['venue']['map_lng']?>"
												 data-init-z="10"
												 data-button="#get-address-from-map"
												 data-button-target="#edit-event-venue-eircode"
												></div>
											<input type="text" class="hidden form-control" id="edit-event-map-search" />
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<button id="get-address-from-map" type="button" class="btn btn-primary btn-event-primary"><?= __('Find Location') ?></button>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-12 text-right" style="font-size: 16px;margin-top: 14px;">
											<input type="checkbox" name="display_map" value="1"<?= @$event['display_map'] ? ' checked="checked"' : '' ?> />
											<?= __('Show map on event page') ?>
										</label>
									</div>
								</div>
							</div>

						</div>
					</div>

					<div class="collapse" id="edit-event-contact_details-panel">
						<div class="panel panel-default">
							<div class="panel-heading">
								<div class="form-group">
									<div class="col-sm-10"><?= __('Add Venue Contact Details?') ?></div>
									<div class="col-sm-2 text-right">
										<button type="button" class="btn-link" data-toggle="collapse" data-target="#edit-event-contact_details-panel"><?= __('hide') ?></button>
									</div>
								</div>
							</div>

							<div class="panel-body">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="sr-only" for="edit-event-venue-email"><?= __('Email') ?></label>
										<input type="email" class="form-control validate[custom[email]]" id="edit-event-venue-email" name="venue[email]" value="<?=$event['venue']['email']?>" placeholder="<?= __('Email') ?>" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" />
									</div>
									<div class="form-group">
										<label class="sr-only" for="edit-event-venue-telephone"><?= __('Telephone') ?></label>
										<input type="text" class="form-control" id="edit-event-venue-telephone" name="venue[telephone]" value="<?=$event['venue']['telephone']?>" placeholder="<?= __('Telephone') ?>" />
									</div>

									<div class="form-group">
										<label class="sr-only" for="edit-event-venue-website"><?= __('Website Address') ?></label>
										<input type="text" class="form-control validate[custom[urlOptionalProtocol]]" id="edit-event-venue-website" name="venue[website]" value="<?=$event['venue']['website']?>" placeholder="<?= __('Website Address') ?>" />
									</div>

									<div class="form-group">
										<label class="sr-only" for="edit-event-venue-facebook_url"><?= __('Facebook Address') ?></label>
                                        <div class="input-group" style="width: 100%;">
                                            <input type="text" class="form-control" placeholder="https://www.facebook.com/" disabled="disabled" style="width: 60%"/>
                                            <input type="text" class="form-control validate[custom[urldir]]" id="edit-event-venue-facebook_url" name="venue[facebook_url]" value="<?=$event['venue']['facebook_url']?>" placeholder="<?= __('Facebook Address') ?>" style="width: 40%" />
                                        </div>
									</div>

									<div class="form-group">
										<label class="sr-only" for="edit-event-venue-twitter_url"><?= __('Twitter Address') ?></label>
                                        <div class="input-group" style="width: 100%;">
                                            <input type="text" class="form-control" placeholder="https://twitter.com/" disabled="disabled" style="width: 60%"/>
                                            <input type="text" class="form-control validate[custom[urldir]]" id="edit-event-venue-twitter_url" name="venue[twitter_url]" value="<?=$event['venue']['twitter_url']?>" placeholder="<?= __('Twitter Address') ?>" style="width: 40%" pattern="[a-zA-Z0-9\-_]+" />
                                        </div>
									</div>

									<div class="form-group">
										<label class="sr-only" for="edit-event-venue-snapchat_url"><?= __('Snapchat Address') ?></label>
                                        <div class="input-group" style="width: 100%;">
                                            <input type="text" class="form-control" placeholder="http://snapchat.com/add/" disabled="disabled" style="width: 60%"/>
                                            <input type="text" class="form-control validate[custom[urldir]]" id="edit-event-venue-snapchatr_url" name="venue[snapchat_url]" value="<?=$event['venue']['snapchat_url']?>" placeholder="<?= __('Snapchat Address') ?>" style="width: 40%" pattern="[a-zA-Z0-9\-_]+" />
                                        </div>
									</div>

									<div class="form-group">
										<label class="sr-only" for="edit-event-venue-instagram_url"><?= __('Instagram Address') ?></label>
                                        <div class="input-group" style="width: 100%;">
                                            <input type="text" class="form-control" placeholder="http://instagram.com/" disabled="disabled" style="width: 60%"/>
                                            <input type="text" class="form-control validate[custom[urldir]]" id="edit-event-venue-instagram_url" name="venue[instagram_url]" value="<?=$event['venue']['instagram_url']?>" placeholder="<?= __('Instagram Address') ?>" style="width: 40%" pattern="[a-zA-Z0-9\-_]+" />
                                        </div>
									</div>
								</div>
								<div class="col-sm-6">
									<p><i><?= __('Enter contact details for the venue to help your customers get more information about your event.') ?></i></p>
								</div>
							</div>
						</div>
					</div>

					<hr />

					<div class="form-group">
						<label class="col-sm-12 control-label" for="edit-venue-file-preview"><?= __('Venue Image') ?></label>
						<div class="col-sm-12 image-upload-wrapper">
							<div>
								<?= View::factory('multiple_upload', array('name' => 'venue_file', 'single' => true, 'preset' => 'Venue banners', 'onsuccess' => 'venue_image_uploaded', 'presetmodal' => 'no', 'duplicate' => 0)) ?>
							</div>
						</div>
						<div class="col-sm-12 saved-image">
                            <input type="hidden" id="venue-image_media_id" name="venue[image_media_id]" value="<?=@$event['venue']['image_media_id']?>" />

                            <img class="col-sm-6 <?=@$event['venue']['image_media_id'] ? '' : 'hidden'?>" id="venue_image_saved" src="<?=$event['venue']['image_media_url']?>" alt="" style="cursor:pointer;" title="<?=__('Click to edit')?>" />
                            <button type="button" id="venue-image_media_remove" class="btn-link saved-image-remove <?=@$event['venue']['image_media_id'] ? '' : 'hidden'?>">
                                <span class="icon-trash"></span>
                            </button>

                        </div>

					</div>

					<ul class="list-unstyled" id="event-dates">
						<li class="form-group template">
							<input type="hidden" name="date_id[]" value="" />
							<div class="col-sm-12 form-group">
								<h2 class="col-sm-12"><?= __('Starts') ?></h2>
								<div class="col-sm-5">
									<span>*</span>
									<label class="input-group">
										<input readonly="readonly" style="background-color: #fff" type="text" class="form-control datetimepicker date-start" name="start_date[]" id="edit-event-date-start" value="" autocomplete="off" style="margin-top: 0;" placeholder="Date Starts" pattern="\d\d\d\d-\d\d-\d\d" />
										<span class="input-group-addon"><span class="icon-calendar"></span></span>
									</label>
								</div>

								<div class="col-sm-5">
									<span>*</span>
									<label class="input-group">
										<input readonly="readonly" style="background-color: #fff" type="text" class="form-control datetimepicker time-start" name="start_time[]" id="edit-event-time-start" value="" autocomplete="off" style="margin-top: 0;" placeholder="Time Starts" pattern="\d\d:\d\d"/>
										<span class="input-group-addon"><span class="icon-clock-o"></span></span>
									</label>
								</div>

                                <label class="col-sm-2 remove hidden">
                                    <button type="button" class="btn-link" onclick="$(this).parents('li').remove()">
                                        <span class="icon-trash"></span>
                                    </button>
                                </label>
							</div>
							<div class="col-sm-12 form-group">
								<h2 class="col-sm-12"><?= __('Ends') ?></h2>
								<div class="col-sm-5">
									<label class="input-group">
										<input readonly="readonly" style="background-color: #fff" type="text" class="form-control datetimepicker date-end" name="end_date[]" id="edit-event-date-end" value="" autocomplete="off" style="margin-top: 0;" placeholder="Date Ends" pattern="\d\d\d\d-\d\d-\d\d" />
										<span class="input-group-addon"><span class="icon-calendar"></span></span>
									</label>
									<p>(<?=__('Leave blank if no end date')?>)</p>
								</div>
								<div class="col-sm-5">
									<label class="input-group">
										<input readonly="readonly" style="background-color: #fff" type="text" class="form-control datetimepicker time-end" name="end_time[]" id="edit-event-time-end" value="" autocomplete="off" style="margin-top: 0;" placeholder="Time Ends"  pattern="\d\d:\d\d"/>
										<span class="input-group-addon"><span class="icon-clock-o"></span></span>
									</label>
								</div>
                                <div class="col-sm-2">
                                    <button type="button" class="btn-link" data-clear_fields="#edit-event-date-end, #edit-event-time-end" title="<?= __('Clear') ?>">
                                        <span class="icon-remove"></span>
                                    </button>
                                </div>
							</div>

                            <?php if (Auth::instance()->has_access('events_edit')): ?>
                                <div class="col-sm-6 form-group">
                                    <h2 class="col-sm-12"><?= __('Countdown Time') ?></h2>
                                    <div class="col-sm-5">
                                        <label class="input-group">
                                            <input type="text" class="form-control count-down-time" name="count_down_time" id="count_down_time" autocomplete="off" placeholder="Countdown Time"  pattern="\d\d:\d\d:\d\d" value="<?= $event['countdown_formatted'] ?>" />
                                            <span class="input-group-addon"><span class="icon-clock-o"></span></span>
                                        </label>
                                    </div>
                                </div>
                            <?php endif; ?>
							<div class="form-group col-sm-6">
								<label class="col-sm-6 control-label"><?= __('On Sale') ?></label>
								<div class="col-sm-6 form-group">
									<select class="form-control on_sale" name="date_onsale[]" >
										<option value="1"><?=__('Yes')?></option>
										<option value="0"><?=__('No')?></option>
									</select>
								</div>
							</div>
						</li>
					</ul>

					<ul class="list-unstyled form-group">
						<li class="col-sm-4">
							<label>

								<input type="checkbox" name="display_start" value="1" <?= isset($event["display_start"]) ? (@$event["display_start"]==0 ? '' : 'checked="checked"') :'checked="checked"' ?> /> <?= __('Display start time on event page') ?>
							</label>
						</li>
						<li class="col-sm-4">
							<label>
								<input type="checkbox" name="display_end" value="1" <?=@$event['display_end'] ? 'checked="checked"' : ''?>/> <?= __('Display end time on event page') ?>
							</label>
						</li>
						<li class="col-sm-4">
							<label>
								<input type="checkbox" name="display_timezone" value="1" <?=@$event['display_timezone'] ? 'checked="checked"' : ''?>/> <?= __('Display time zone on event page') ?>
							</label>
						</li>
						<li class="col-sm-4">
							<label>
								<input type="checkbox" name="display_othertime" value="1" <?=@$event['display_othertime'] ? 'checked="checked"' : ''?>/> <?= __('Display other times on event page') ?>
							</label>
						</li>
					</ul>

					<div class="clearfix" id="edit-event-weather-forecast">
						<input type="hidden" id="forecast_api_key" value="745daddf6a1ab992478d025a389b2238" />

						<canvas class="left" id="edit-event-weather-icon" width="28" height="18"></canvas>
						<div id="edit-event-weather-forecast-summary"><?= $event['forecast_summary'] ?></div>
						<input type="hidden" id="edit-event-forecast_icon"    name="forecast_icon"    value="<?= $event['forecast_icon'] ?>" />
						<input type="hidden" id="edit-event-forecast_summary" name="forecast_summary" value="<?= $event['forecast_summary'] ?>" />
						<input type="hidden" id="edit-event-forecast_json"    name="forecast_json"    value="<?= htmlspecialchars($event['forecast_json']) ?>" />
					</div>

					<div class="event-button-group">
						<button type="button" class="btn btn-actions" data-toggle="collapse" data-target="#edit-event-multiple_events-panel"><?= __('Schedule multiple events') ?></button>
						<button type="button" class="btn btn-actions" data-toggle="modal" data-target="#edit-event-time_settings-modal"><?= __('Timezone and date settings') ?> (GMT)</button>
						<button type="button" class="btn btn-actions" data-toggle="collapse" data-target="#edit-event-other_times-panel"><?= __('Add other times') ?></button>
					</div>

					<div class="collapse" id="edit-event-multiple_events-panel">
						<div class="panel panel-default">
							<div class="panel-heading">
								<div class="form-group">
									<div class="col-sm-10"><?= __('Multiple Events') ?></div>
									<div class="col-sm-2 text-right">
										<button type="button" class="btn-link" data-toggle="collapse" data-target="#edit-event-multiple_events-panel"><?= __('hide') ?></button>
									</div>
								</div>
							</div>
							<div class="panel-body">
								<div class="form-group">
									<label class="col-sm-2 control-label"><?=__('Payment')?></label>
									<div class="col-sm-4 form-group">
										<select class="form-control" name="one_ticket_for_all_dates">
											<option value="1" <?=$event['one_ticket_for_all_dates'] == 1 ? 'selected="selected"' : ''?>><?=__('One ticket for all date/time')?></option>
											<option value="0" <?=$event['one_ticket_for_all_dates'] != 1 ? 'selected="selected"' : ''?>><?=__('One ticket for each date/times')?></option>

										</select>
									</div>
								</div>
								<div class="form-group" style="margin-bottom: 0;">
									<label class="col-sm-12 control-label">Dates</label>
									<div class="col-sm-12 form-group" style="margin-bottom: 0;">
										<div class="col-sm-offset-9 col-sm-3">
											<button id="event-edit-add-multi-date-btn" type="button" class="btn-link"><?= __('Add another date') ?></button>
										</div>
										<ul id="event-multi-dates" class="list-unstyled col-sm-12">

										</ul>

									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="modal fade" tabindex="-1" id="edit-event-time_settings-modal">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<div class="form-group">
										<div class="col-sm-10"><?= __('Timezone &amp; date settings') ?> (<span id="selected-tz-display">GMT</span>)</div>
										<div class="col-sm-2 text-right">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
										</div>
									</div>
								</div>

								<div class="modal-body">
									<p class="text-uppercase"><label for="edit-event-form-timezone"><?= __('Select your time zone') ?></label></p>
									<select id="edit-event-form-timezone" name="timezone" class="form-control">
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
										<?=html::optionsFromArray($tzlist, @$event['timezone'] ? @$event['timezone'] : "1")?>
									</select>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-event-inverse" data-dismiss="modal"><?= __('Cancel') ?></button>
									<button type="button" class="btn btn-event" data-dismiss="modal"><?= ('Save') ?></button>
								</div>
							</div>
						</div>
					</div>

					<div class="collapse" id="edit-event-other_times-panel">
						<div class="panel panel-default">
							<div class="panel-heading">
								<div class="form-group">
									<div class="col-sm-10"><?= __('Add other times') ?></div>
									<div class="col-sm-2 text-right">
										<button type="button" class="btn-link" data-toggle="collapse" data-target="#edit-event-other_times-panel"><?= __('hide') ?></button>
									</div>
								</div>
							</div>
							<div class="panel-body">
								<div class="form-group" style="margin-bottom: 0;">
									<label class="col-sm-12 control-label">Time</label>
									<div class="col-sm-8 form-group" style="margin-bottom: 0;">
										<ul id="custom-times" class="list-unstyled col-sm-12">
											<li class="col-sm-12 template">
												<label class="col-sm-5">
													<input type="text" class="form-control title" placeholder="Enter time title" name="custom_time[title][]" />
												</label>
												<label class="col-sm-5">
													<input type="text" class="form-control time" placeholder="Enter Time" name="custom_time[time][]" />
												</label>
												<label class="col-sm-2">
													<button type="button" class="btn-link" onclick="$(this).parents('li').remove()">
														<span class="icon-trash"></span>
													</button>
												</label>
											</li>
										</ul>
										<div class="col-sm-12">
											<button id="event-edit-add-custom-time-btn" type="button" class="btn-link"><?= __('Add another time') ?></button>
										</div>

									</div>
									<div class="col-sm-4">
										<i><?= __('Add other times that might be relevant to your event e.g. Gate opens, door time, etc.') ?></i>
									</div>
								</div>
							</div>
						</div>
					</div>

					<hr />

					<div class="form-group">
						<div class="col-sm-12">
							<h2><?= __('Event details') ?></h2>
						</div>
						<div class="col-sm-12">
							<textarea class="form-control ckeditor-simple" id="edit-event-description" name="description" rows="6"><?= $event['description'] ?></textarea>
						</div>
					</div>

					<div class="form-group" style="margin-bottom: 35px;">
						<div class="col-sm-12">
							<button type="button" class="btn btn-actions" data-toggle="modal" data-target="#edit-event-add-faqs-panel">
								<span class="icon-plus"></span> <?= __('Add FAQs') ?>
							</button>
						</div>
					</div>


					<div class="modal fade" tabindex="-" role="dialog" id="edit-event-add-faqs-panel">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<div class="form-group">
										<div class="col-sm-10"><?= __('Add FAQs') ?></div>
										<div class="col-sm-2 text-right">
											<button type="button" class="btn-link" data-dismiss="modal"><?= __('hide') ?></button>
										</div>
									</div>
								</div>

								<div class="modal-body">
									<p><?= __('Common questions about the event') ?></p>
									<ul class="list-unstyled" id="edit-event-faq-list">
										<li>
											<label>
												<input type="checkbox" />
												<span><?= __('Are there ID requirements or an age limit to enter the event?') ?></span>
											</label>
										</li>
										<li>
											<label>
												<input type="checkbox" />
												<span><?= __('What are my transport parking/options getting to the event?') ?></span>
											</label>
										</li>
										<li>
											<label>
												<input type="checkbox" />
												<span><?= __("What can/can't I bring to the event?") ?></span>
											</label>
										</li>
									</ul>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
									<button type="button" class="btn btn-event add" data-dismiss="modal"><?= __('Add') ?></button>
								</div>
							</div>
						</div>
					</div>

					<div class="edit-event-organizer-section">
						<div class="form-group">
							<label class="col-sm-2 control-label control-label-fixed" for="edit-event-organizer_name"><?= __('Organiser name') ?><span>*</span></label>
							<div class="col-sm-6">
								<input type="hidden" class="organizer-contact-id" id="edit-event-organizer-contact-id" name="organizers[0][contact_id]" value="<?=@$event['organizers'][0]['contact_id']?>" />
								<input type="text" class="form-control validate[required]" id="edit-event-organizer-contact-name" placeholder="<?= __('Enter the event organiser&apos;s name') ?>"  name="organizers[0][name]"  value="<?=@$event['organizers'][0]['first_name']?>" />
							</div>
                            <div class="col-sm-4">
                                <label class="checkbox">
                                    <input type="checkbox" class="organiser_checkbox" name="organizers[0][is_primary]" value="1" checked="checked"> Make Primary
                                </label>
                            </div>
						</div>

						<div class="event-button-group">
							<button
								type="button"
								class="btn btn-actions"
								data-toggle="collapse"
								data-target="#edit-event-organizer_contact_details-panel"><?= __('Add Organiser Contact Details') ?></button>
							<button
								type="button"
								class="btn btn-actions"
								id="add-another-organizer-btn"><?= __('Add another organiser') ?></button>
						</div>

						<div id="edit-event-additional-organizers">
							<div class="collapse" id="edit-event-organizer_contact_details-panel">
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="form-group">
											<div class="col-sm-10"><?= __('Organiser Contact Details') ?></div>
											<div class="col-sm-2 text-right">
												<button type="button" class="btn-link" data-toggle="collapse" data-target="#edit-event-organizer_contact_details-panel"><?= __('hide') ?></button>
											</div>
										</div>
									</div>

									<div class="panel-body">
										<div class="col-sm-6">
											<div class="form-group">
												<label class="sr-only" for="edit-event-organizer-telephone"><?= __('Telephone') ?></label>
												<input type="text" class="form-control" id="edit-event-organizer-telephone" name="organizers[0][telephone]" placeholder="<?= __('Telephone') ?>" value="<?=@$event['organizers'][0]['phone']?>" />
											</div>
											<div class="form-group">
												<label class="sr-only" for="edit-event-organizer-email"><?= __('Email') ?></label>
												<input type="email" class="form-control validate[required] organizer-email" id="edit-event-organizer-email" name="organizers[0][email]" placeholder="<?= __('Email') ?> *" value="<?=@$event['organizers'][0]['email']?>" />
											</div>
											<div class="form-group">
												<label class="sr-only" for="edit-event-organizer-website"><?= __('Website Address') ?></label>
												<input type="text" class="form-control validate[custom[urlOptionalProtocol]]" id="edit-event-organizer-website" name="organizers[0][website]" placeholder="<?= __('Website Address') ?>" value="<?=@$event['organizers'][0]['website']?>" />
											</div>

											<div class="form-group">
												<label class="sr-only" for="edit-event-organizer-facebook_url"><?= __('Facebook Address') ?></label>
												<div class="input-group" style="width: 100%;">
													<input type="text" class="form-control" placeholder="https://www.facebook.com/" disabled="disabled" style="width: 60%"/>
													<input type="text" class="form-control" id="edit-event-organizer-facebook_url" name="organizers[0][facebook_url]" placeholder="<?= __('Facebook Address') ?>" value="<?=@$event['organizers'][0]['facebook']?>" style="width: 40%" pattern="[a-zA-Z0-9\-_]+" />
												</div>
											</div>

											<div class="form-group">
												<label class="sr-only" for="edit-event-organizer-twitter_url"><?= __('Twitter Address') ?></label>
												<div class="input-group" style="width: 100%;">
													<input type="text" class="form-control" placeholder="https://twitter.com/" disabled="disabled" style="width: 60%"/>
													<input type="text" class="form-control" id="edit-event-organizer-twitter_url" name="organizers[0][twitter_url]" placeholder="<?= __('Twitter Address') ?>" value="<?=@$event['organizers'][0]['twitter']?>" style="width: 40%" pattern="[a-zA-Z0-9\-_]+" />
												</div>
											</div>

											<div class="form-group">
												<label class="sr-only" for="edit-event-organizer-snapchat_url"><?= __('Snapchat Address') ?></label>
												<div class="input-group" style="width: 100%;">
													<input type="text" class="form-control" placeholder="http://snapchat.com/add/" disabled="disabled" style="width: 60%"/>
													<input type="text" class="form-control" id="edit-event-organizer-snapchat_url" name="organizers[0][snapchat_url]" placeholder="<?= __('Snapchat Address') ?>" value="<?=@$event['organizers'][0]['snapchat']?>" style="width: 40%" pattern="[a-zA-Z0-9\-_]+" />
												</div>
											</div>

											<div class="form-group">
												<label class="sr-only" for="edit-event-organizer-instagram_url"><?= __('Instagram Address') ?></label>
												<div class="input-group" style="width: 100%;">
													<input type="text" class="form-control" placeholder="http://instagram.com/" disabled="disabled" style="width: 60%"/>
													<input type="text" class="form-control" id="edit-event-organizer-instagram_url" name="organizers[0][instagram_url]" placeholder="<?= __('Instagram Address') ?>" value="<?=@$event['organizers'][0]['instagram']?>" style="width: 40%" pattern="[a-zA-Z0-9\-_]+" />
												</div>
											</div>
										</div>
										<div class="col-sm-6">
											<p><i><?= __('Enter contact details to help them gain recognition and sell more tickets for their events.') ?></i></p>
										</div>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-12 control-label" for="edit-organiser-profile-image-0"><?= __('Organiser Profile Image') ?></label>
								<div class="col-sm-12 image-upload-wrapper">
									<div>
										<?= View::factory('multiple_upload', array('name' => 'organiser_0', 'single' => true, 'preset' => 'Organizer profiles', 'onsuccess' => 'organizerp_image_uploaded', 'presetmodal' => 'no', 'duplicate' => 0)) ?>
									</div>
								</div>
								<div class="col-sm-12 saved-image">
									<input type="hidden" id="edit-event-organizer-contact-id-0" name="organizers[0][profile_media_id]" value="<?=@$event['organizers'][0]['profile_media_id']?>" />
                                    <img class="col-sm-6 organizer_profile_image_saved <?=@$event['organizers'][0]['profile_media_url'] ? '' : 'hidden'?>" src="<?=@$event['organizers'][0]['profile_media_url']?: 'about:blank'?>" alt="" style="cursor:pointer;" title="<?=__('Click to edit')?>" />
                                    <button type="button" class="btn-link saved-image-remove <?=@$event['organizers'][0]['profile_media_url'] ? '' : 'hidden'?>">
                                        <span class="icon-trash"></span>
                                    </button>
								</div>

							</div>

							<div class="form-group">
								<label class="col-sm-12 control-label" for="edit-organiser-banner-image-0"><?= __('Organiser Banner Image') ?></label>
								<div class="col-sm-12 image-upload-wrapper">
									<div>
										<?= View::factory('multiple_upload', array('name' => 'banner_file_id_0', 'single' => true, 'preset' => 'Organizer banners', 'onsuccess' => 'organizer_image_uploaded', 'presetmodal' => 'no', 'duplicate' => 0)) ?>
									</div>
								</div>
								<div class="col-sm-12 saved-image">
									<input type="hidden"  name="organizers[0][banner_media_id]" value="<?=@$event['organizers'][0]['banner_media_id']?>" />
									<img class="col-sm-6 organiser_banner_image_saved <?=@$event['organizers'][0]['banner_media_url'] ? '' : 'hidden'?>" src="<?=@$event['organizers'][0]['banner_media_url']?: 'about:blank'?>" alt="" style="cursor:pointer;" title="<?=__('Click to edit')?>" />
									<button type="button" class="btn-link saved-image-remove <?=@$event['organizers'][0]['banner_media_url'] ? '' : 'hidden'?>">
										<span class="icon-trash"></span>
									</button>

								</div>

							</div>

							<?php if (isset($event['organizers']) AND count($event['organizers']) > 1)
							{
								foreach ($event['organizers'] as $key => $organizer)
								{
									if ($key > 0)
									{
										include 'snippets/additional_contact.php';
									}
								}
							}
							?>
						</div>
					</div>

					<div class="col-sm-12 text-danger text-center error-area" id="organizers-primary-error-area" style="display: none;" data-error="<?=__('Only one organiser can be primary')?>"></div>
					<div class="col-sm-12 text-danger text-center error-area" id="organizers-email-error-area" style="display: none;" data-error="<?=__('Organizers have duplicate emails')?>"></div>
                    <div class="col-sm-12 text-danger text-center error-area" id="organizers-duplicate-error-area" style="display: none;" data-error="<?=__('You can not add same organizer more than once')?>"></div>

					<?php
					// Black template for additional organizers
					unset($organizer);
					$key = -1;
					include 'snippets/additional_contact.php';
					?>

					<hr />

					<section class="form-group" id="create-tickets-section">
						<div class="col-sm-12">
							<h2>2. <?= __('Create Tickets') ?></h2><span> At least one Free, Paid or Donation ticket *</span>
						</div>

						<div class="col-sm-12 text-danger text-center error-area" id="create-tickets-error-area"></div>

						<br clear="both" />
						<br />

						<?php if (Auth::instance()->has_access('events_edit')) { ?>
						<div class="col-sm-12">
							<div class="form-group clearfix">
								<label class="col-sm-3 control-label" for="commission_fixed_amount" title="<?=__('Leave empty to use default fixed fee from settings')?>"><?= __('Fee Fixed Amount') ?></label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="commission_fixed_amount" name="commission_fixed_amount" value="<?= @$event['commission_fixed_amount'] ?>" placeholder="<?= $commission['fixed_charge_amount'] ?>" />
								</div>
							</div>

							<div class="form-group clearfix">
								<label class="col-sm-3 control-label" for="commission_amount" title="<?=__('Leave empty to use default rated fee from settings')?>"><?= __('Fee Amount') ?></label>
								<div class="col-sm-9">
									<select class="form-control col-sm-3" name="commission_type" id="commission_type"><?=html::optionsFromArray(array('Fixed' => __('Fixed'), 'Percent' => __('Percent')), @$event['commission_type'] ?: 'Percent')?></select>
									<input style="clear: none" type="text" class="form-control col-sm-3" id="commission_amount" name="commission_amount" value="<?= @$event['commission_amount'] ?>" placeholder="<?=$commission['amount']?>" />
								</div>
							</div>
						</div>
						<?php } else { ?>
						<input type="hidden" id="commission_fixed_amount" readonly="readonly" value="<?= @$event['commission_fixed_amount'] ?>"/>
						<input type="hidden" id="commission_type" readonly="readonly" value="<?= @$event['commission_type'] ?>"/>
						<input type="hidden" id="commission_amount" readonly="readonly" value="<?= @$event['commission_amount'] ?>"/>
						<?php } ?>

						<div class="col-sm-12">
							<table class="table edit-event-ticket-table" id="edit-event-ticket-table">
								<thead class="hidden">
									<tr>
                                        <th></th>
										<th scope="col"><?= __('Ticket name') ?> <span>*</span></th>
										<th scope="col"><?= __('Quantity available') ?> 	<span>*</span></th>
										<th scope="col"><?= __('Price') ?> 	<span>*</span></th>
										<th scope="col"><?= __('Actions') ?></th>
									</tr>
								</thead>
								<tbody class="sortable-tbody edit-event-tickets-list" id="edit-event-tickets-list">
									<tr class="ticket-template basic" id="create-ticket-templates-paid">
										<td><div class="draggable-handle"><span class="icon-arrows"></span></div></td>
										<td><input type="text" class="form-control ticket-name validate[required]" name="ticket_name[]" placeholder="Early Bird, etc." /></td>
										<td>
                                            <label class="quantity-field">
                                                <span class="quantity-field-mask" aria-hidden="true"></span>
                                                <input type="number" data-sold="0" class="form-control ticket-quantity validate[required]" name="ticket_quantity[]" placeholder="e.g. 100" />
                                            </label>
                                        </td>
										<td>
											<div class="type Free"><?= __('Free') ?></div>
											<div class="type Donation"><?= __('Donation') ?></div>
											<div class="type Paid">
												<label class="input-group">
													<span class="input-group-addon"><span class="currency-sym"><?=$cursym?></span></span>
													<input type="text" class="form-control text-right ticket-price validate[required]" name="ticket_price[]" placeholder="e.g. 10.00" />
												</label>
												<?= __('Final price') ?>
												<button type="button"
														class="btn-link popover-init price-display-calculation"
														data-toggle="popover"
														data-html="true"
														data-trigger="focus hover"
														data-content=""
													></button>
											</div>
										</td>
										<td>
											<ul class="list-unstyled">
												<li><button type="button" class="btn btn-default edit-event-ticket-settings-icon">Ticket Settings</button></li>
												<li><button type="button" class="btn btn-default edit-event-ticket-delete-icon hidden">Delete</button></li>
												<li><button type="button" class="btn btn-default edit-event-ticket-archive-icon hidden">Archive</button></li>
												<li><button type="button" class="btn btn-default edit-event-ticket-unarchive-icon hidden">Un Archive</button></li>
												<input type="hidden" class="ticket-archived" name="ticket_archived[]" />
											</ul>
										</td>
									</tr>
									<tr class="ticket-template details hidden">
										<td colspan="5">
											<input type="hidden" class="ticket-type-id" name="ticket_type_id[]" />
											<input type="hidden" class="ticket-type" name="ticket_type[]" />

											<h5>Settings</h5>
											<div class="form-group">
												<div class="col-sm-12 control-label">
                                                    <label for="edit-event-ticket-description_0">Ticket description</label>
                                                </div>
												<div class="col-sm-6">
													<textarea class="form-control ticket-description" id="edit-event-ticket-description_0" name="ticket_description[]"></textarea>
													<label>
														<input class="ticket-show_description" type="checkbox" name="ticket_show_description[]" value="1" checked="checked" />
														<?=__('Show ticket description on event page')?>
													</label>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12 control-label">
                                                    <label for="edit-event-ticket-fees_0">Fees <span>*</span></label>
                                                </div>
												<div class="col-sm-4">
													<select class="form-control ticket-include_commission" name="ticket_include_commission[]" id="edit-event-ticket-fees_0">
														<?=html::optionsFromArray(array(
															0 => __('Pass fees on'),
															1 => __('Absorb fees')
														), null)?>
													</select>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12 control-label">
                                                    <label for="edit-event-ticket-sale_starts_0"><?= __('Ticket sales start') ?> <span>*</span></label>
                                                </div>
												<div class="col-sm-4">
													<input type="text" class="form-control ticket-sale_starts datetime-picker_no-auto-close" id="edit-event-ticket-sale_starts_0" name="ticket_sale_starts[]" />
												</div>

												<div class="col-sm-12 control-label">
                                                    <label for="edit-event-ticket-sale_ends_0"><?= __('Ticket sales end') ?> <span>*</span></label>
                                                </div>
												<div class="col-sm-4">
													<input type="text" class="form-control ticket-sale_ends datetime-picker_no-auto-close" id="edit-event-ticket-sale_ends_0" name="ticket_sale_ends[]" />
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12 control-label"><?= __('Ticket Visibility') ?></div>
												<div class="col-sm-12">
													<ul class="list-unstyled">
														<li>
															<label>
																<input type="radio" class="ticket-visible yes" checked value="1" name="ticket_visible[]" />
																<?= __('Hide when ticket is not on sale') ?>
															</label>
														</li>
														<li>
															<label>
																<input type="radio" class="ticket-visible no" value="0" name="ticket_visible[]" />
																<?= __('Hide for custom date and time') ?>
															</label>
														</li>
													</ul>
												</div>
											</div>

											<div class="form-group tthide">
												<label class="col-sm-12 control-label text-left" for="edit-event-ticket-hide_before_0"><?= __('Hide Before') ?></label>
												<div class="col-sm-4">
													<input type="text" class="form-control ticket-hide_before datetime-picker" id="edit-event-ticket-hide_before_0" name="ticket_hide_before[]" />
												</div>

												<label class="col-sm-12 control-label text-left" for="edit-event-ticket-hide_after_0"><?= __('Hide After') ?></label>
												<div class="col-sm-4">
													<input type="text" class="form-control ticket-hide_after datetime-picker" id="edit-event-ticket-hide_after_0" name="ticket_hide_after[]" />
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12"><?= __('Tickets allowed per order') ?></div>
												<div class="col-sm-4">
													<input type="number" class="form-control ticket-min_per_order" id="edit-event-ticket-min_per_order_0" name="ticket_min_per_order[]" min="0" />
													<label for="edit-event-ticket-min_per_order_0"><?= __('Minimum') ?></label>
												</div>
												<div class="col-sm-4">
													<input type="number" class="form-control ticket-max_per_order" id="edit-event-ticket-max_per_order_0" name="ticket_max_per_order[]" min="0" />
													<label for="edit-event-ticket-max_per_order_0"><?= __('Maximum') ?></label>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12"><?= __('Sleeping') ?></div>
												<div class="col-sm-4">
													<input type="number" class="form-control ticket-sleep_capacity" id="edit-event-ticket-sleep_capacity_0" name="ticket_sleep_capacity[]" min="0" />
													<label for="edit-event-ticket-sleep_capacity"><?= __('Total Capacity') ?></label>
												</div>
											</div>

                                            <?php if (Auth::instance()->has_access('events_edit_advanced')) { ?>
                                                <div class="form-group">
                                                    <div class="col-sm-3 control-label"><?= __('Payment Plan') ?></div>
                                                    <div class="col-sm-9">
                                                    </div>
                                                </div>

                                                <table class="table paymentplan">
                                                    <thead>
                                                    <tr><th><?=__('Title')?></th><th>Payment Type</th><th><?=__('Amount')?></th><th><?=__('Due Date')?></th><th></th></tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr class="payment-plan-stage hidden">
                                                        <td>
                                                            <input type="hidden" name="ticket_paymentplan[ticket_index][index][id]" class="pp-id" />
                                                            <input type="hidden" name="ticket_paymentplan[ticket_index][index][tickettype_id]" class="pp-tickettype_id" />
                                                            <input type="text" name="ticket_paymentplan[ticket_index][index][title]" class="pp-title" />
                                                        </td>
                                                        <td>
                                                            <select name="ticket_paymentplan[ticket_index][index][payment_type]" class="pp-type">
                                                                <option value="Percent" selected="selected"><?=__('Percent')?></option>
                                                                <option value="Fixed"><?=__('Fixed')?></option>
                                                            </select>
                                                        </td>
                                                        <td style= "width: 120px;"><input type="text" name="ticket_paymentplan[ticket_index][index][payment_amount]" class="pp-amount" /></td>
                                                        <td>
                                                            <input type="hidden" name="ticket_paymentplan[ticket_index][index][due_date]" value="" /><?php // If the due date field is disabled, fall back to this ?>
                                                            <input type="text" name="ticket_paymentplan[ticket_index][index][due_date]" class="pp-due_date validate[required]" readonly="readonly" />
                                                        </td>
                                                        <td><a class="remove btn"><?=__('remove')?></a></td>
                                                    </tr>
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="4">
                                                            <button type="button" class="add"><?=__('Add payment stage')?></button>
                                                        </td>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                            <?php } ?>

											<div class="right">
												<button type="button" class="btn-link hide-ticket-settings"><?= __('hide') ?></button>
											</div>
										</td>
									</tr>
								</tbody>
								<tfoot>

								</tfoot>
							</table>

							<div class="edit-event-ticket-buttons text-center" id="edit-event-ticket-buttons">
								<button type="button" class="btn btn-event" data-type="Free"><span class="plus-icon"></span> <?= __('Free Ticket') ?></button>
								<button type="button" class="btn btn-event" data-type="Paid"><span class="plus-icon"></span> <?= __('Paid Ticket') ?></button>
								<button type="button" class="btn btn-event" data-type="Donation"><span class="plus-icon"></span> <?= __('Donation')    ?></button>
							</div>
						</div>

						<table class="hidden ticket-price-breakdown" id="ticket-price-breakdown-template">
							<tbody>
								<tr>
									<td><?= __('Ticket price') ?></td>
									<td><span class="ticket-price-breakdown-base_price"></span> (<span class="ticket-price-breakdown-back_to_org"></span>back to organiser)</td>
								</tr>
								<tr>
									<td><?= __('Booking fee') ?></td>
									<td><span class="ticket-price-breakdown-fee"></span> (<span class="ticket-price-breakdown-fee_breakdown"></span>)</td>
								</tr>
								<tr>
									<td><strong>Total to buyer</strong></td>
									<td><span class="ticket-price-breakdown-final_price"></span></td>
								</tr>
							</tbody>
						</table>

					</section>


					<div style="max-width: 660px; margin:auto;">
						<div class="form-group">
							<div class="col-sm-3">
								<button type="button" class="btn btn-actions discount-add-btn"  data-toggle="collapse" data-target="#edit-event-discounts-panel"><?= __('Add a Discount?') ?></button>
							</div>
							<label class="col-sm-2 text-right" for="edit-event-ticket-quantity" style="font-size: 16px;padding-top: 10px;">
								<?= __('Total Capacity') ?>
							</label>
							<div class="col-sm-2">
								<input type="number" class="form-control" id="edit-event-ticket-quantity" name="quantity" value="<?=$event['quantity']?>" data-default="<?=$event['quantity']?>" />
							</div>
						</div>
					</div>
					<div class="form-group discount-table hidden">
						<table id="discounts-list" class="table">
							<caption>Discounts</caption>
							<thead>
							<tr>
								<th>Code</th>
								<th>Type</th>
								<th>Value</th>
								<th>Availablity</th>
								<th>Usage Limit</th>
								<th>Current Usage</th>
								<th>Date</th>
								<th>Actions</th>
							</tr>
							</thead>
							<tbody>
                                <tr class="discount-row-template" data-discount-id="">
                                    <td class="discount-code">
                                        <input type="hidden" name="discount_id[]" />
                                        <input type="hidden" name="discount_type[]" />
                                        <input type="hidden" name="discount_amount[]" />
                                        <input type="hidden" name="discount_code[]" />
                                        <select name="discount_ticket_type[][]" multiple="multiple" style="display:none"></select>
                                        <input type="hidden" name="discount_quantity[]" />
                                        <input type="hidden" name="discount_starts[]" />
                                        <input type="hidden" name="discount_ends[]" />
                                        <span></span>
                                    </td>
                                    <td class="discount-type"></td>
                                    <td class="discount-amount"></td>
                                    <td class="discount-ticket-types"></td>
                                    <td class="discount-quantity"></td>
                                    <td class="discount-used">123</td>
                                    <td class="discount-date"></td>
                                    <td class="discount-actions">
                                        <button type="button" class="btn-link delete"
                                                data-toggle="modal"
                                                data-target="#edit-event-discount-delete-modal">delete</button>
                                    </td>
                                </tr>
								<?php if(is_array(@$event['discounts']))foreach ($event['discounts'] as $discount): ?>
									<tr class="discount-row-template" data-discount-id="<?= $discount['id'] ?>">
										<td class="discount-code">
											<input type="hidden" name="discount_id[]" value="<?= $discount['id'] ?>" />
											<input type="hidden" name="discount_type[]" value="<?= $discount['type'] ?>" />
											<input type="hidden" name="discount_amount[]" value="<?= $discount['amount'] ?>" />
											<input type="hidden" name="discount_code[]" value="<?= $discount['code'] ?>" />
											<select name="discount_ticket_type[][]" multiple="multiple" style="display:none">
												<?php if ( count($event['discounts']) > 0 ): ?>
													<?php foreach ($discount['ticket_types'] as $ticket_type): ?>
														<option value="<?= $ticket_type ?>"></option>
													<?php endforeach; ?>
												<?php else: ?>
													<option value="0"></option>
												<?php endif; ?>
											</select>
											<input type="hidden" name="discount_quantity[]" value="<?= $discount['quantity'] ?>" />
											<input type="hidden" name="discount_starts[]" value="<?= $discount['starts'] ?>" />
											<input type="hidden" name="discount_ends[]" value="<?= $discount['ends'] ?>" />
											<span><?= $discount['code'] ?></span>
										</td>
										<td class="discount-type"><?= $discount['type'] ?></td>
										<td class="discount-amount"><?= $discount['amount'] ?></td>
										<td class="discount-ticket-types"><?= join(', ', $discount['ticket_types']) ?></td>
										<td class="discount-quantity"><?= $discount['quantity'] ?></td>
										<td class="discount-used"></td>
										<td class="discount-date"><?= $discount['starts'] ?> &ndash; <?= $discount['ends'] ?></td>
										<td class="discount-actions">
											<button type="button" class="btn-link delete"
													data-toggle="modal"
													data-target="#edit-event-discount-delete-modal">delete</button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<div class="collapse" id="edit-event-discounts-panel">
						<div class="panel panel-default">
							<div class="panel-heading">
								<div class="form-group">
									<div class="col-sm-10"><?= __('Add a discount') ?></div>
									<div class="col-sm-2 text-right">
										<button type="button" class="btn-link" data-toggle="collapse" data-target="#edit-event-discounts-panel"><?= __('hide') ?></button>
									</div>
								</div>
							</div>
							<div class="panel-body">

								<div class="form-group">
									<div class="col-sm-12 control-label">
                                        <label for="edit-event-discount-code"><?= __('Discount code') ?></label>
                                    </div>
									<div class="col-sm-5">
										<input type="text" class="form-control discount-code" id="edit-event-discount-code" />
									</div>
								</div>

								<div class="form-group">
									<input type="hidden" id="edit-event-discount-id" />
									<label class="col-sm-12 control-label text-left" for="edit-event-discount-type"><?= __('Type') ?></label>
									<div class="col-sm-4">
										<select class="form-control discount-type" id="edit-event-discount-type">
											<option value="Fixed" data-type="fixed"><?= __('Fixed Amount') ?></option>
											<option value="Percentage" data-type="percentage"><?= __('Percentage') ?></option>
										</select>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-12 control-label text-left" for="edit-event-discount-amount"><?= __('Discount value') ?></label>
									<div class="col-sm-4">
										<div class="input-group">
											<div class="input-group-addon" id="edit-event-discount-amount-unit"><?=$cursym?></div>
											<input type="text" class="form-control amount" id="edit-event-discount-amount" />
										</div>
									</div>
								</div>

								<div class="form-group">
									<div class="col-sm-12 control-label">
                                        <label for="edit-event-discount-ticket_type"><?= __('Discount available for') ?></label>
                                    </div>
									<div class="col-sm-4">
										<select multiple="multiple" class="form-control discount-ticket_type" id="edit-event-discount-ticket_type">
											<option value="">All types</option>
										</select>
									</div>
								</div>

								<div class="form-group">
									<div class="col-sm-12 control-label">
                                        <label for="edit-event-discount-quantity"><?= __('Number available') ?></label>
                                    </div>
									<div class="col-sm-4">
										<input type="number" class="form-control discount-quantity" id="edit-event-discount-quantity"/>
									</div>
								</div>

								<div class="form-group">
									<div class="col-sm-4">
										<label class="control-label" for="edit-event-discount-starts"><?= __('Starts') ?></label>
										<input type="text" class="form-control discount-starts datetime-picker" id="edit-event-discount-starts" />
									</div>
									<div class="col-sm-4">
										<label class="control-label" for="edit-event-discount-ends"><?= __('Ends') ?></label>
										<input type="text" class="form-control discount-ends datetime-picker" id="edit-event-discount-ends" />
									</div>
								</div>

								<button type="button" class="btn btn-event" id="edit-event-discount-save"><?= __('Save')?></button>


							</div>
						</div>
					</div>

                    <section id="event-payment-plan">
						<?php if (Auth::instance()->has_access('events_edit_advanced')) { ?>
                        <div class="form-group">
                            <div class="col-sm-3 control-label"><?= __('Enable Group Payment') ?></div>
                            <div class="col-sm-9">
                                <?= html::toggle_button('enable_multiple_payers',	__('Yes'), __('No'), $event['enable_multiple_payers'] == 'YES') ?>
                            </div>
                        </div>
						<?php } ?>
                    </section>
					<hr />

                    <section>
                        <?php if( !$account['use_stripe_connect'] ): ?>
                            <?php if(empty($account['iban']) || empty($account['bic'])): ?>
								<div id="payment-details" class="hidden">
                                <h2><?=__('Receive payments via')?></h2>
								<div class="col-sm-12 text-danger text-center error-area" id="billing-error-area"></div>
                                <div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label class="sr-only" for="use_stripe_connect"><?=__('Receive payments via')?></label>
											<span>*</span>
                                            <select class="form-control validate[required]" name="use_stripe_connect" id="use_stripe_connect">
                                                <option value="1" <?=$account['use_stripe_connect'] == 1 ? 'selected="selected"' : ''?>><?=__('Stripe (as tickets are sold)')?></option>
                                                <option value="0" <?=$account['use_stripe_connect'] != 1 ? 'selected="selected"' : ''?>><?=__('Bank Transfer (1st working day after event)')?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="use-stripe">
                                        <?php if (!$account['stripe_auth']): ?>
                                            <p>
                                                <i><?=__('You currently do not have a connected stripe account.')?></i><br />
												<button type="submit" name="action" value="save_stripe_connect" class="btn btn-default continue-button" id="edit-event-save-btn"><?= __('Connect Stripe Account') ?></button>
                                            </p>
                                        <?php else: ?>
                                            <p>
                                                <?=__('You have a connected stripe account.')?> <?=__('Your connected stripe user id:') . $account['stripe_auth']['stripe_user_id']?><br />

												<button type="submit" name="action" value="save_stripe_disconnect" class="btn btn-default continue-button" id="edit-event-save-btn"><?= __('Disconnect Stripe Account') ?></button>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="bank-details">
                                        <div class="form-group">
                                            <label class="sr-only bank" for="event-account-details-iban">IBAN</label>
                                            <div class="col-sm-12 bank">
												<span>*</span>
                                                <input class="form-control validate[required]" id="event-account-details-iban" type="text" name="iban" placeholder="IBAN" value="<?= $account['iban'] ?>" />
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="sr-only bank" for="event-account-details-bic">BIC</label>
                                            <div class="col-sm-12 bank">
												<span>*</span>
                                                <input class="form-control validate[required]" id="event-account-details-bic" type="text" name="bic" placeholder="BIC" value="<?= $account['bic'] ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
								</div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <div class="form-group">
                            <label class="col-sm-6 control-label" for="edit-event-ticket-currency"><?= __('In which currency would you like to be paid?') ?> <span>*</span></label>
                            <div class="col-sm-2">
                                <select class="form-control validate[required]" name="currency" id="edit_event-ticket-currency">
                                    <option value=""><?= __('Please select Currency')?></option>
                                    <?=html::optionsFromRows('currency', 'name', Model_Currency::getCurrencies(true), @$event['currency'] ? $event['currency'] : 'EUR')?>
                                </select>
                            </div>
                            <div class="col-sm-4 control-label">
                                <?php $vat_rate = number_format(Settings::instance()->get('vat_rate') * 100, 2) ?>
                            </div>
                        </div>
                    </section>

					<div class="form-group">
						<div class="col-sm-12">
							<h2>Email note</h2>
						</div>
						<div class="col-sm-12" style="margin-bottom: 45px;">
							<textarea class="form-control ckeditor-simple" id="edit-event-email_note" name="email_note"><?=$event['email_note']?></textarea>
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-12">
							<h2><?= __('Ticket note (max 25 words)') ?></h2>
						</div>
						<div class="col-sm-12" style="margin-bottom: 45px;">
							<textarea class="form-control" id="edit-event-ticket_note" name="ticket_note" data-maxlength="25"><?= isset($event['ticket_note']) ? $event['ticket_note'] : '' ?></textarea>
						</div>
					</div>

					<h2>3. <?= __('Additional Settings') ?></h2>

					<div class="form-group">
						<div class="col-sm-12 control-label"><?= __('Age restriction') ?></div>
						<div class="col-sm-12">
							<ul class="list-unstyled">
								<li>
									<label>
										<input type="radio" name="age_restriction" <?=$event['age_restriction'] == 16 ? 'checked="checked"' : ''?> value="16" /> Over 16's
									</label>
								</li>
								<li>
									<label>
										<input type="radio" name="age_restriction" <?=$event['age_restriction'] == 18 ? 'checked="checked"' : ''?> value="18" /> Over 18's
									</label>
								</li>
								<li>
									<label>
										<input type="radio" name="age_restriction" <?=$event['age_restriction'] == 21 ? 'checked="checked"' : ''?> value="21" /> Over 21's
									</label>
								</li>
								<li>
									<label>
										<input type="radio" name="age_restriction" <?=$event['age_restriction'] == 0 || !$event['age_restriction'] ? 'checked="checked"' : ''?> value="0" /> All ages event
									</label>
								</li>
							</ul>
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-12 control-label"><?= __('Listing privacy') ?> <span>*</span></div>
						<div class="col-sm-12">
							<ul class="list-unstyled">
								<li>
									<label>
										<input type="radio" name="is_public" value="1" <?=@$event['is_public'] != 0 ? 'checked="checked"' : ''?> /> public page : list this event on <?= URL::base() ?> and search engines.
									</label>
								</li>
								<li>
									<label>
										<input type="radio" name="is_public" value="0" <?=@$event['is_public'] == 0 ? 'checked="checked"' : ''?> /> private page : do not list this event publicly.
									</label>
								</li>
							</ul>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label control-label-fixed" for="edit-event-event_type"><?= __('Event type') ?> <span>*</span></label>
						<div class="col-sm-6">
							<select class="form-control validate[required]" id="edit-event-event_type" name="category_id" style="max-width: 365px;">
								<option value=""><?= __('Select the type of event')?></option>
								<?=html::optionsFromRows('value', 'label', $categories, $event['category_id']);?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label control-label-fixed" for="edit-event-event_topic"><?= __('Event topic') ?> <span>*</span></label>
						<div class="col-sm-6">
							<select class="form-control validate[required]" id="edit-event-event_topic" name="topic_id"style="max-width: 365px;">
								<option value=""><?= __('Select a topic') ?></option>
								<?php foreach ($topics as $topic): ?>
									<?php $selected = (isset($event['topic_id']) AND $event['topic_id'] == $topic['value']) ? ' selected' : ''; ?>
									<option value="<?= $topic['value'] ?>"<?= $selected ?>><?= htmlentities($topic['label']) ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label control-label-fixed" for="edit-event-add_tag"><?= __('Tags') ?></label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="edit-event-add_tag" placeholder="<?= __('Type to add a #tag') ?>" style="max-width: 365px;" />
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<button type="button" class="btn btn-actions" id="tag-add-btn"><span class="icon-plus"></span> <?= __('Add Tag') ?></button>
						</div>
						<ul class="col-sm-12" id="edit-event-tags-list">
						</ul>
					</div>

					<?php if (Auth::instance()->has_access('events_edit')): ?>
						<div class="form-group">
							<div class="col-sm-2 control-label"><?= __('Recommended') ?></div>
							<div class="col-sm-10">
								<?= html::toggle_button('featured',	__('Yes'), __('No'), $event['featured']) ?>
							</div>
						</div>

                        <div class="form-group">
                            <div class="col-sm-2 control-label"><?= __('Home Banner') ?></div>
                            <div class="col-sm-10">
								<?php $home_banner = !empty($event) ? $event['is_home_banner'] : true ?>
								<?= html::toggle_button('is_home_banner',	__('Yes'), __('No'), $home_banner) ?>
                            </div>
                        </div>
					<?php endif; ?>

					<label>
						<input type="hidden" name="show_remaining_tickets" value="0" />
						<input type="checkbox" name="show_remaining_tickets" value="1"<?= $event['show_remaining_tickets'] == 1 ? ' checked="checked"' : '' ?> />
						<?= __('Show the number of remaining tickets on the registration page') ?>
					</label>

					<div class="clearfix col-sm-12"> </div>

					<div class="clearfix col-sm-12"> </div>

					<hr />

					<h2 class="text-center"><?= 'Great! You&#39;re nearly there.' ?></h2>

					<div class="text-center form-action-group" style="margin-top: 22px;">
						<?php if($event['status']=='Live' && ($event['is_public'] == 1 && $event['is_onsale'] == 1)) : ?>
							<button type="submit" name="action" value="save" class="btn btn-default continue-button" id="edit-event-save-btn"><?= __('Save') ?></button>
                        <?php else : ?>
							<button type="submit" name="action" value="save_draft" class="btn btn-default continue-button" id="edit-event-save-btn"><?= __('Save as Draft') ?></button>
						<?php endif; ?>

						<?php if($event['status']=='Live' && ($event['is_public'] == 1 && $event['is_onsale'] == 1)): ?>
							<button type="submit" name="action" value="make_offline" class="btn btn-event event_offline"><?= __('Take Event Offline') ?></button>
						<?php else: ?>
							<button type="submit" name="action" value="make_live" class="btn btn-primary"><?= __('Make Your Event Live') ?></button>
						<?php endif; ?>
						<button type="submit" name="action" value="preview" class="btn btn-primary btn-event-primary" id="edit-event-preview-button"><?= __('Preview') ?></button>
						<?php if ($event['id']): ?>
							<?php if($event['status']=='Live'): ?>
								<a href="/admin/events/" class="btn btn-default"><?= __('Cancel') ?></a>
							<?php endif; ?>

                            <?php if (Auth::instance()->has_access('events_delete') || Auth::instance()->has_access('events_delete_limited')): ?>
                                <button
                                    type="button"
                                    class="btn btn-danger"
                                    data-toggle="modal"
                                    data-target="#edit-event-delete-modal"
                                ><?= __('Delete') ?></button>
                            <?php endif; ?>
						<?php endif; ?>
					</div>
				</section>
		</div>

		<?php if($edit_seo) { ?>
		<!--	seo     -->
		<div role="tabpanel" class="tab-pane col-sm-12" id="edit-event-seo">
			<div class="req-field"><?= __('* indicates mandatory fields') ?></div>
			<div class="form-group clearfix">
				<label class="col-sm-2 control-label control-label-fixed" for="seo-edit-event-name"><?= __('Page Title') ?> <span>*</span></label>
				<div class="col-sm-10">
					<input type="text" class="form-control required validate[required]" id="seo-edit-event-name" name="name" value="<?= $event['name'] ?>" placeholder="<?= __('Event name') ?>" />
				</div>
			</div>

			<div class="form-group clearfix">
				<label class="col-sm-2 control-label control-label-fixed" for="edit_event_seo_keywords">Keywords</label>
				<div class="col-sm-10">
					<textarea class="form-control" rows="2" name="seo_keywords" id="edit_event_seo_keywords"><?=trim($event['seo_keywords'])?></textarea>
				</div>
			</div>

			<div class="form-group clearfix">
				<label class="col-sm-2 control-label control-label-fixed" for="edit_event_seo_description">Meta Description</label>
				<div class="col-sm-8">
					<textarea class="form-control" rows="2" name="seo_description" id="edit_event_seo_description"><?=trim($event['seo_description'])?></textarea>
				</div>
			</div>

			<div class="form-group clearfix">
				<label class="col-sm-2 control-label control-label-fixed" for="footer_editor">Footer Text</label>
				<div class="col-sm-8">
					<textarea class="form-control" id="footer_editor" rows="2" name="footer" id="footer_editor"><?=$event['footer']?></textarea>
				</div>
			</div>

			<div class="form-group clearfix">
				<label class="col-sm-2 control-label control-label-fixed" for="seo-edit-event-web_address_url"><?= __('Event URL') ?></label>
				<div class="col-sm-10">
					<div class="input-group" style="width: 100%;">
						<input type="text" class="form-control" id="seo-edit-event-web_address" placeholder="https://<?=$_SERVER['HTTP_HOST']?>/event/" disabled="disabled" style="width: 50%;" />
						<input type="text" class="form-control validate[required]" id="seo-edit-event-web_address_url" name="url" value="<?=$event['url']?>" placeholder="<?= __('Web URL') ?> *" style="width: 50%;" pattern="[a-zA-Z0-9\-]+" />
					</div>
				</div>
			</div>

			<div class="form-group clearfix">
				<label class="col-sm-2 control-label control-label-fixed" for="edit_event_x_robots_tag">X-Robots-Tag</label>
				<div class="col-sm-2">
					<select class="form-control" name="x_robots_tag" id="edit_event_x_robots_tag">
						<option value=""></option>
						<?=HTML::optionsFromArray(
							array(
								'noindex' => 'noindex',
								'nofollow' => 'nofollow',
								'noarchive' => 'noarchive',
								'noindex,nofollow' => 'noindex,nofollow'
							),
							@$event['x_robots_tag']
						)?>
					</select>
				</div>
			</div>
		</div>
		<?php } ?>

		</form>

        <!-- attendees -->
        <div role="tabpanel" class="tab-pane col-sm-12" id="edit-event-attendees">
            <table class="table table-striped dataTable table-condensed " id="list-orders-table">
                <thead>
                <tr>
                    <th scope="col"><?= __('Order#') ?></th>
                    <th scope="col">Buyer</th>
                    <th scope="col">Email</th>
                    <th scope="col">Item</th>
                    <th scope="col">Order Date</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Country</th>
                    <th><a id="select-all-attendees">Select All</a></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($attendees as $order) {
                    ?>
                    <tr>
                        <td><a href="/admin/events/order_details/<?=$order['id']?>"><?=$order['id']?></a></td>
                        <td><a href="/admin/events/order_details/<?=$order['id']?>"><?=$order['firstname'] . ' ' . $order['lastname']?></a></td>
                        <td><a href="/admin/events/order_details/<?=$order['id']?>"><?=$order['email']?></a></td>
                        <td><a href="/admin/events/order_details/<?=$order['id']?>"><?=$order['tickets']?></a></td>
                        <td><a href="/admin/events/order_details/<?=$order['id']?>"><?=$order['created']?></a></td>
                        <td><a href="/admin/events/order_details/<?=$order['id']?>"><?=($order['total'] != $order['total_paid'] ? $order['currency'] . $order['total_paid'] . ' / ' : '') . $order['currency'].$order['total']?></a></td>
                        <td><a href="/admin/events/order_details/<?=$order['id']?>"><?=$order['ticket_quantity']?></a></td>
                        <td><a href="/admin/events/order_details/<?=$order['id']?>"><?=$order['telephone']?></a></td>
                        <td><a href="/admin/events/order_details/<?=$order['id']?>"><?=$order['country']?></a></td>
                        <td>
                            <input type="checkbox" name="order_id[]" value="<?=$order['id']?>" />
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9" align="center">
                            <button type="button" id="show-email-attendees-modal"
                                    class="btn"
                                    data-toggle="modal"
                                    data-target="#email-attendees-modal">Email Selected</button>
                        </td>
                    </tr>
                </tfoot>
            </table>

        </div>

		<!-- check in -->
		<?php if (is_numeric($event['id'])) { ?>
		<div role="tabpanel" class="tab-pane col-sm-12" id="edit-event-checkin">
			<form method="post" action="/admin/events/checkin">
				<input type="hidden" name="event_id" value="<?=$event['id']?>" />
				<ul>
					<?php foreach ($event['dates'] as $date) { ?>
						<li>
							<b><?=$date['starts']?></b>
							<table class="table">
								<thead>
								<tr><td>Ticket Code</td><th>Buyer</th><th>Checked In</th><th>Note</th></tr>
								</thead>
								<tbody>
								<?php foreach ($tickets as $ticket) { ?>
									<?php if ($ticket['date_id'] == $date['id']) { ?>
									<tr>
										<td><?=$ticket['code']?></td>
										<td><?=$ticket['buyer']?></td>
										<td><input type="checkbox" name="ticket[<?=$ticket['id']?>][checked]" <?=$ticket['checked'] ? 'checked="checked" disabled="disabled"' : ''?> value="1" /> <?=$ticket['checked']?></td>
										<td><input type="text" name="ticket[<?=$ticket['id']?>][note]" value="<?=html::chars($ticket['checked_note'])?>" <?=$ticket['checked'] ? 'disabled="disabled"' : ''?> /></td>
									</tr>
									<?php } ?>
								<?php } ?>
								</tbody>
							</table>
						</li>
					<?php } ?>
				</ul>

				<button class="btn" type="submit">Save</button>
			</form>
		</div>
		<?php } ?>
	</div>


<?php if (!empty($event['id']) && (Auth::instance()->has_access('events_delete') || Auth::instance()->has_access('events_delete_limited'))): ?>
    <?php ob_start() ?>
        <form action="/admin/events/delete_event" method="post">
            <input type="hidden" name="id" value="<?= $event['id'] ?>" />

            <button type="submit" class="btn btn-danger"><?= __('Delete') ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
        </form>
    <?php $buttons = ob_get_clean(); ?>

    <?php
    echo View::factory('snippets/modal')->set([
        'id'     => 'edit-event-delete-modal',
        'title'  => __('Confirm delete'),
        'body'   => __('Are you sure you want to delete this event?'),
        'footer' => $buttons
    ]);
    ?>
<?php endif; ?>

<div class="modal fade" id="edit-event-ticket-delete-modal" tabindex="-1" role="dialog" aria-labelledby="edit-event-ticket-delete-modal-label">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="edit-event-ticket-delete-modal-label"><?= __('Confirm delete') ?></h4>
			</div>
			<div class="modal-body">
				<p><?= __('Are you sure you want to delete this ticket type?') ?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="edit-event-ticket-delete-modal-btn" data-row_class=""><?= __('Delete') ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="edit-event-discount-delete-modal" tabindex="-1" role="dialog" aria-labelledby="edit-event-discount-delete-modal-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="edit-event-discount-delete-modal-label"><?= __('Confirm delete') ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __('Are you sure you want to delete this discount?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="edit-event-discount-delete-modal-btn" data-row-index=""><?= __('Delete') ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-event-image-delete-modal" tabindex="-1" role="dialog" aria-labelledby="edit-event-image-delete-modal-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="edit-event-discount-delete-modal-label"><?= __('Confirm delete') ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __('Are you sure you want to delete this image?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="edit-event-image-delete-modal-btn" data-row-index=""><?= __('Delete') ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade email-attendees-modal" id="email-attendees-modal" tabindex="-1" role="dialog" aria-labelledby="email-attendees-modal-label">
    <div class="modal-dialog" role="document">
        <form class="modal-content" id="email_attendees_form" action="/admin/events/email_attendees" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="email-attendees-modal-label"><?= __('Email Attendees') ?></h4>
            </div>
            <div class="modal-body clearfix">
                <input type="hidden" name="event_id" id="email-attendees-event_id" value="<?=$event['id']?>" />
                <input type="hidden" name="redirect" value="event_details" />

                <div class="form-group">
                    <label class="col-sm-12"><?= __('Subject') ?>
                        <input type="text" class="form-control" name="subject" />
                    </label>
                </div>

                <div class="form-group">
                    <label class="col-sm-12"><?= __('Message') ?>
                        <textarea class="form-control ckeditor-simple" id="email-attendees-message" name="message" rows="5"></textarea>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary"><?= __('Send') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .event-edit img {
        max-width: 100%;
        height: auto;
    }

    input[type="text"].ib_text_title_input {
        width: 100%;
    }

    .form-section {
        display: none;
    }
    .req-field{
		font-weight: 600;
	}
    .form-section.active {
        display: block;
    }

    .form-section .panel-heading {
        background: none;
        width: 100%;
        text-align: left;
        border-width: 0 0 1px;
    }
	.modal-header .form-group,
	.panel-heading .form-group {
		margin-bottom: 0;
	}

	/*
    .form-section .panel-heading:after {
        float: right;
        content: 'show';
    }

    .form-section .panel-heading[aria-expanded="true"]:after {
        float: right;
        content: 'hide';
    }
    */

    .form-section .well {
        clear: both;
    }
    .edit-event-images-table {
        width: 100%;
    }
    .edit-event-images-table td {
        padding-bottom: .5em;
    }
    .text-left {
        text-align: left ! important;
    }

	.btn-event-inverse {
		background: none;
		border: 2px solid #31ceb4;
		color: #31ceb4;
	}
	.btn-event,
	.btn-event-inverse:hover,
	.btn-event-inverse:focus {
		background: #31ceb4;
		border: 2px solid #31ceb4;
		box-shadow: none;
		color: #fff;
		text-shadow: none;
	}
	.btn-event:hover,
	.btn-event:focus {
		color: #fff;
	}

	.event-edit .btn-lg {
		font-size: 1.7em;
	}

	.edit-event-ticket-table {
		margin: 1em 0;
		width: 100%;
	}

    .edit-event-ticket-table {
        table-layout: fixed;
    }

    .edit-event-ticket-table thead th:last-child  { width: 11em; }

    .ticket-template:nth-child(odd) .form-control {
        width: 100%;
    }

    .edit-event-ticket-table th,
	.edit-event-ticket-table td {
		padding: .3em;
	}
	.edit-event-ticket-table td:last-child {
		white-space: nowrap;
	}
	.edit-event-ticket-table td:last-child .btn {
		margin-bottom: 5px;
		min-width: 110px;
	}

    .edit-event-ticket-buttons .btn {
        margin-bottom: .5em;
    }

   .btn.btn-event.event_offline {
  	background-color: grey;
  	border: 1px solid grey;
	}
	#edit-event-add-new-video-panel.collapse.in {
	  float: left;
	  margin-top: 15px;
		width: 100%;
	}
	#edit-event-add-new-video-panel .text-right .btn-link {
	  margin: 0;
	}

    @media screen and (min-width: 768px)
    {
        .req-field {
            text-align: right;
        }
    }

</style>
<script>
    window.eventEditData = <?=json_encode($event)?>;
    window.currencies = <?=json_encode(Model_Currency::getCurrencies(true))?>;
	window.commission = <?=json_encode($commission)?>;
	window.vatRate = <?=json_encode(Settings::instance()->get('vat_rate'))?>;
    // Dragging sortability
    $('.sortable-tbody').sortable({cancel: 'a, button, :input, label', handle: '.draggable-handle'});
	/*$('#event_image_file_id,#edit-event-organizer-image,#edit-venue-image,#organiser_banner').on('click',function(){
		//existing_image_editor(this.src);
	})*/

    $("#use_stripe_connect").on("change", function(){
        if (this.value == 1) {
            $(".use-stripe").show();
            $(".bank-details").hide();
        } else {
            $(".use-stripe").hide();
            $(".bank-details").show();
        }
    });
    $("#use_stripe_connect").change();
</script>
