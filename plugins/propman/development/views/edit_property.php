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
<script src="<?=URL::get_engine_plugin_assets_base('media') .'js/dialog.js'?>"></script>
<script>
window.counties = <?=json_encode(Model_Propman::counties('all'))?>;
window.bookedDays = <?=json_encode($bookedDays);?>;
</script>
<form name="property-edit" id="property-edit" method="post" action="/admin/propman/edit_property/<?= isset($property['id']) ? $property['id'] : '' ?>">
	<input type="hidden" name="id" value="<?= isset($property['id']) ? $property['id'] : '' ?>" />

	<div class="form-group clearfix">
		<label class="sr-only" for="edit-property-name"><?= __('Enter property title') ?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control ib-title-input required" id="edit-property-name" name="name" placeholder="<?= __('Enter property title') ?>" value="<?=@$property['name']?>" />
		</div>
		<div class="col-sm-2">
			<label>
				<span class="sr-only"><?= __('Publish') ?></span>
				<input type="hidden" name="published" value="0" /><?php // If the checkbox is unticked, this value will get sent to the server  ?>
				<input type="checkbox" name="published" value="1" <?=( ! isset($property['published']) OR $property['published'] == 1) ? 'checked="checked"' : ''?> data-toggle="toggle" data-onstyle="success" data-on="<?= __('Published') ?>" data-off="<?= __('Unpublished') ?>" />
			</label>
		</div>
	</div>
	<div class="form-group clearfix">
		<label class="sr-only" for="edit-property-name"><?= __('Url') ?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control ib-title-input" id="edit-property-url" value="<?=@$property['url']?>" readonly="readonly" />
		</div>
	</div>

	<div class="col-sm-12">

		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#edit-property-tab-details"  aria-controls="edit-property-tab-details"  role="tab" data-toggle="tab"><?= __('Details')  ?></a></li>
			<li role="presentation"><a href="#edit-property-tab-location" aria-controls="edit-property-tab-location" role="tab" data-toggle="tab"><?= __('Location') ?></a></li>
			<li role="presentation"><a href="#edit-property-tab-photos"   aria-controls="edit-property-tab-photos"   role="tab" data-toggle="tab"><?= __('Photos')   ?></a></li>
			<li role="presentation"><a href="#edit-property-tab-related"  aria-controls="edit-property-tab-related"  role="tab" data-toggle="tab"><?= __('Related')  ?></a></li>
			<li role="presentation"><a href="#edit-property-tab-calendar" aria-controls="edit-property-tab-calendar" role="tab" data-toggle="tab"><?= __('Calendar') ?></a></li>
			<li role="presentation"><a href="#edit-property-tab-prices"   aria-controls="edit-property-tab-prices"   role="tab" data-toggle="tab"><?= __('Prices') ?></a></li>
		</ul>

		<div class="tab-content">
			<!-- Details tab -->
			<div role="tabpanel" class="tab-pane active" id="edit-property-tab-details"><?php include 'includes/edit_property_details.php'; ?></div>

			<!-- Location tab -->
			<div role="tabpanel" class="tab-pane" id="edit-property-tab-location">
				<div class="form-horizontal">

					<div class="form-group">
						<div class="col-sm-2 control-label"><?= __('Use group address') ?></div>
						<div class="col-sm-5">
							<?php $use_group_address = false; ?>
							<div class="btn-group col-sm-9" data-toggle="buttons" id="expiry_date">
								<label class="btn btn-default<?= ($use_group_address) ? ' active' : '' ?>">
									<input type="radio"<?= $use_group_address ? ' checked="checked"' : '' ?>  value="1" name="use_group_address" id="use_group_address_yes" /><?= __('Yes') ?>
								</label>
								<label class="btn btn-default<?= ( ! $use_group_address) ? ' active' : '' ?>" >
									<input type="radio"<?= ( ! $use_group_address) ? ' checked' : '' ?>  value="0" name="use_group_address" id="use_group_address_no" /><?= __('No') ?>
								</label>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="edit-property-address_1"><?= __('Address 1') ?></label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="edit-property-address_1" name="address1" value="<?=@$property['address1']?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="edit-property-address_2"><?= __('Address 2') ?></label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="edit-property-address_2" name="address2" value="<?=@$property['address2']?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="edit-property-country"><?= __('Country') ?></label>
						<div class="col-sm-5">
							<select class="form-control" id="edit-property-country" name="country_id">
								<?=HTML::optionsFromArray(
									Model_Propman::countries(),
									@$property['country_id'],
									array('value' => '', 'label' => __('Please select'))
								);?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="edit-property-county"><?= __('County') ?></label>
						<div class="col-sm-5">
							<select class="form-control" id="edit-property-county" name="county_id">
								<?=HTML::optionsFromArray(
										Model_Propman::counties(@$property['country_id']),
										@$property['county_id'],
										array('value' => '', 'label' => __('Please select'))
								);?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="edit-property-city"><?= __('City') ?></label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="edit-property-city" name="city" value="<?=@$property['city']?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="edit-property-eircode"><?= __('Eircode') ?></label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="edit-property-eircode" name="eircode" value="<?=@$property['postcode']?>" />
						</div>
						<div class="col-sm-5">
							<button id="get-address-from-map" type="button" class="btn btn-default"><?= __('Find address on map') ?></button>
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-2 control-label"><?= __('Location') ?></div>
						<div class="col-sm-8">
							<div class="map-container"
								 style="width: 100%; height: 300px;"
								 data-target-x="#edit-property-xcoordinates"
								 data-target-y="#edit-property-ycoordinates"
								 data-init-x="<?=@$property['latitude']?>"
								 data-init-y="<?=@$property['longitude']?>"
								 data-init-z="10"
								 data-button="#get-address-from-map"
								 data-button-target="#edit-property-eircode"
							></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-offset-2 col-sm-2 control-label" for="edit-property-xcoordinates" ><?= __('X co-ordinates') ?></label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="edit-property-xcoordinates" name="latitude" value="<?=@$property['latitude']?>" />
						</div>
						<label class="col-sm-2 control-label" for="edit-property-ycoordinates" ><?= __('Y co-ordinates') ?></label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="edit-property-ycoordinates" name="longitude" value="<?=@$property['longitude']?>" />
						</div>
					</div>

				</div>
			</div>

			<!-- Photos tab -->
			<div role="tabpanel" class="tab-pane" id="edit-property-tab-photos">
				<div class="form-horizontal">
					<div class="col-sm-12">
						<div class="btn-group">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<?= __('Add Image') ?> <span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li><a class="add_existing_image_button"><?= __('Add existing') ?></a></li>
								<li><a class="multi_upload_button"><?= __('Upload new') ?></a></li>
							</ul>
						</div>
					</div>

					<div class="col-sm-12">
						<table class="table table-striped" id="edit-property-images-table">
							<thead>
								<tr>
									<th scope="col"><?= __('Order') ?></th>
									<th scope="col"><?= __('Thumb') ?></th>
									<th scope="col"><?= __('Filename') ?></th>
									<th scope="col"><?= __('Remove') ?></th>
								</tr>
							</thead>
							<tbody class="sortable-tbody">
								<!-- <tr data-id="1">
									<td title="<?= __('Drag to reorder') ?>">
										<span class="icon-bars"></span>
									</td>
									<td></td>
									<td>front-house-shot.jpg</td>
									<td>
										<button type="button" class="btn-link" title="<?= __('Remove') ?>">
											<span class="icon-times"></span>
										</button>
									</td>
								</tr> -->
							<?php
							if (@$property['medias'])
							foreach ($property['medias'] as $media) {
								if ($contentImages)
								foreach ($contentImages as $contentImage) {
									if ($contentImage['id'] == $media['media_id']) {
										break;
									}
								}
							?>
							<tr>
								<td><span class="icon-bars"></span></td>
								<td><img src="<?=$contentImage['url']?>" style="max-width: 50px; max-height: 50px;" /></td>
								<td><?=$contentImage['filename']?></td>
								<td>
									<input type="hidden" name="shared_media_id[]" value="<?=$contentImage['id']?>" />
									<button type="button" class="btn-link btn-remove" title="Remove">
										<span class="icon-times"></span>
									</button>
								</td>
							</tr>
							<?php
							}
							?>
							</tbody>
						</table>
					</div>



				</div>
			</div>

			<!-- Related tab -->
			<div role="tabpanel" class="tab-pane form-horizontal" id="edit-property-tab-related">

				<div class="col-sm-12">
					<div class="form-group">
						<label class="sr-only" for="edit-property-select_related_property"><?= __('Select similar properties to link') ?></label>
						<div class="col-sm-6">
							<select class="form-control ib-combobox" id="edit-property-select_related_property" data-placeholder="<?= __('Select similar properties to link') ?>">
								<option value=""></option>
								<?php
								foreach ($linkableProperties as $linkableProperty) {
								?>
								<option value="<?=$linkableProperty['id']?>"
										data-refcode="<?=$linkableProperty['ref_code']?>"
										data-name="<?=$linkableProperty['name']?>"
										data-id="<?=$linkableProperty['id']?>"
										data-group="<?=$linkableProperty['group']?>"
								><?=
									'#' . $linkableProperty['id'] . '; ' . $linkableProperty['group'] . '; ' . $linkableProperty['name']
								?></option>
								<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-2">
							<button type="button" class="btn btn-default" id="related-property-add-button"><?= __('Add') ?></button>
						</div>
					</div>
				</div>

				<div class="col-sm-12">
					<table class="table table-striped" id="edit-property-related-table">
						<thead>
							<tr>
								<th scope="col"><?= __('Order') ?></th>
								<th scope="col"><?= __('Thumb') ?></th>
								<th scope="col"><?= __('ID') ?></th>
								<th scope="col"><?= __('Refcode') ?></th>
								<th scope="col"><?= __('Title') ?></th>
								<th scope="col"><?= __('Remove') ?></th>
							</tr>
						</thead>
						<tbody class="sortable-tbody">
							<?php
							if (isset($property['linkedProperties']))
							foreach ($property['linkedProperties'] as $linkedProperty) {
							?>
							<tr data-id="<?=$linkedProperty['id']?>">
								<td title="<?= __('Drag to reorder') ?>">
									<span class="icon-bars"></span>
								</td>
								<td></td>
								<td><?=$linkedProperty['id']?></td>
								<td><?=$linkedProperty['ref_code']?></td>
								<td><?=$linkedProperty['name']?></td>
								<td>
									<input type="hidden" name="linked_property_id[]" value="<?=$linkedProperty['id']?>" />
									<button type="button" class="btn-remove" title="<?= __('Remove') ?>">
										<span class="icon-times"></span>
									</button>
								</td>
							</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>

			</div>

			<!-- Calendar tab -->
			<div role="tabpanel" class="tab-pane" id="edit-property-tab-calendar">
				<div class="col-sm-12 form-horizontal">

					<div class="form-group">
						<div class="col-sm-3 control-label"><?= __('Override Group Calendar') ?></div>
						<div class="col-sm-5">
							<div class="btn-group col-sm-9" data-toggle="buttons" id="expiry_date">
								<label class="btn btn-default<?= ($property['override_group_calendar']) ? ' active' : '' ?>">
									<input type="radio"<?= $property['override_group_calendar'] == 1 ? ' checked="checked"' : '' ?>  value="1" name="override_group_calendar" id="override_group_calendar_yes" /><?= __('Yes') ?>
								</label>
								<label class="btn btn-default<?= ( ! $property['override_group_calendar']) ? ' active' : '' ?>" >
									<input type="radio"<?= ($property['override_group_calendar'] == 0) ? ' checked="checked' : '' ?>  value="0" name="override_group_calendar" id="override_group_calendar_no" /><?= __('No') ?>
								</label>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-12" for="edit-property-period"><?= __('Period') ?></label>
						<div class="col-sm-5">
							<select class="form-control" id="edit-property-period">
								<option value=""><?= __('Select Period') ?></option>
								<?php
								foreach ($periods as $period) {
									?>
									<option value="<?=$period['id']?>"
											data-starts="<?=$period['starts']?>"
											data-ends="<?=$period['ends']?>"><?= date('M Y',
												strtotime($period['starts'])) ?> &ndash; <?= date('M Y',
												strtotime($period['ends'])) ?></option>
									<?php
								}
								?>
							</select>
						</div>
					</div>

					<div class="col-sm-12">
						<span class="ib-calendar-key ib-calendar-key-available"></span> <?= __('Available') ?>
						<span class="ib-calendar-key ib-calendar-key-unavailable"></span> <?= __('Unavailable') ?>
						<span class="ib-calendar-key ib-calendar-key-web_booking"></span> <?= __('Web Booking') ?>
					</div>

					<div class="col-sm-12 ib-calendar" id="edit-property-calendar">
						<input type="hidden" name="calendar" value="<?=isset($property['calendar']) ? HTML::chars(json_encode($property['calendar'])) : ''?>" />
                        <input type="hidden" name="group_calendar" value="<?=isset($property['group_calendar']) ? HTML::chars(json_encode($property['group_calendar'])) : ''?>" />
						<?php
						foreach ($periods as $pi => $period) {

							?>
							<div class="ib-calendar-period has-select-range" id="ib-calendar-period-<?=$period['id']?>" style="display: none;">
								<?php
								foreach ($period['calendar']['months'] as $pmonth) {
									$year = $pmonth['year'];
									$month = $pmonth['month'];
									$mfirst = $pmonth['start'];
									$mlast = $pmonth['end'];
									$skipfirst = date('w', mktime(0, 0, 0, $month, 1, $year));
									$skiplast = 7 - date('w', mktime(0, 0, 0, $month, $mlast, $year));

									?>
									<div class="ib-calendar-month">
										<div class="ib-calendar-month-header"><?= date('F Y',
													mktime(0, 0, 0, $month, 1, $year)) ?></div>
										<div class="ib-calendar-day-headers">
											<span class="ib-calendar-day-header">Sun</span>
											<span class="ib-calendar-day-header">Mon</span>
											<span class="ib-calendar-day-header">Tue</span>
											<span class="ib-calendar-day-header">Wed</span>
											<span class="ib-calendar-day-header">Thu</span>
											<span class="ib-calendar-day-header">Fri</span>
											<span class="ib-calendar-day-header">Sat</span>
										</div>

										<?php
										$mday = 1;
										for ($week = 1 ; $week <= 6 ; ++$week) {
											?>
											<div class="ib-calendar-week">
												<?php
												for ($wday = 0 ; $wday < 7 ; ++$wday) {
													if (($week == 1 && $wday < $skipfirst) || ($mday > $mlast) || ($mday < $mfirst)) {
														?>
														<span class="ib-calendar-day"></span>
														<?php
													} else {
														?>
														<span tabindex="0" class="ib-calendar-day" data-date="<?=$year .'-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($mday, 2, '0', STR_PAD_LEFT)?>"><?=$mday?></span>
														<?php
														++$mday;
													}
													?>
													<?php
												}
												?>
											</div>
											<?php
										}
										?>
									</div>
									<?php
								}
								?>
							</div>
							<?php
						}
						?>
					</div>

				</div>
			</div>

            <!-- Price tab -->
            <di	v role="tabpanel" class="tab-pane form-horizontal" id="edit-property-tab-prices">
                <div class="col-sm-12">


                    <div class="col-sm-12">
                        <table class="table table-striped dataTable property-property-prices-table" id="property-property-prices-table">
                            <thead>
                            <tr>
                                <th scope="col"><?= __('ID') ?></th>
                                <th scope="col"><?= __('Rate Card') ?></th>
								<th scope="col"><?= __('Starts') ?></th>
								<th scope="col"><?= __('Ends') ?></th>
                                <th scope="col"><?= __('Weekly') ?></th>
                                <th scope="col"><?= __('Short Stay') ?></th>
                                <th scope="col"><?= __('Additional') ?></th>
                                <th scope="col"><?= __('Min. stay') ?></th>
                                <th scope="col"><?= __('Price type') ?></th>
                                <th scope="col"><?= __('Weekly discount') ?></th>
                                <th scope="col"><?= __('Arrival') ?></th>
                                <th scope="col"><?= __('Actions') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (isset($property['ratecards']))
                            foreach ($property['ratecards'] as $ratecard) {
                            ?>
                                <tr data-ratecard-id="<?=$ratecard['id']?>">
                                    <td><a href="/admin/propman/edit_rate_card/<?=$ratecard['id']?>"><?=$ratecard['id']?></a></td>
                                    <td><a href="/admin/propman/edit_rate_card/<?=$ratecard['id']?>"><?=$ratecard['name']?></a></td>
									<td><?=$ratecard['starts']?></td>
									<td><?=$ratecard['ends']?></td>
                                    <td><?=$ratecard['weekly_price']?></td>
                                    <td><?=$ratecard['short_stay_price']?></td>
                                    <td><?=$ratecard['additional_nights_price']?></td>
                                    <td><?=$ratecard['min_stay']?></td>
                                    <td><?=$ratecard['pricing']?></td>
                                    <td><?=$ratecard['discount']?></td>
                                    <td><?=$ratecard['arrival']?></td>
                                    <td>
                                        <input type="hidden" name="has_ratecard_id[]" value="<?=$ratecard['id']?>" />
                                        <button
                                            type="button"
                                            class="btn-link list-delete-button"
                                            title="<?= __('Delete') ?>"
                                            data-ratecard-id="<?=$ratecard['id']?>"
                                            onclick="$(this).parents('tr').remove();">
                                            <span class="icon-times"></span> <?= __('Delete') ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
	</div>

	<div class="col-sm-12" style="clear: both;">
		<div class="well">
            <input type="hidden" id="save_exit" name="save_exit" value="false"/>

			<div class="btn-group">
				<button type="submit" class="btn btn-primary" name="action" value="save"><?= __('Save') ?></button>
				<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
				</button>
				<ul class="dropdown-menu">
					<li><button type="submit" name="action" value="save_and_exit" onclick="$('#save_exit')[0].setAttribute('value', 'true');"><?= __('Save & Exit') ?></button></li>
					<?php if ( ! empty($property['id']) AND Settings::instance()->get('twitter_api_access') == 1): ?>
						<li><a
								href="http://twitter.com/home/?status=<?= urlencode("New property ".$property['name']."\n".URL::site().'property-details.html/'.$property['url']) ?>"
								type="button"
								class="tweet-item-btn"
								>Tweet</a>
						</li>
					<?php endif; ?>
				</ul>
			</div>

			<button type="reset" class="btn btn-default"><?= __('Reset') ?></button>
			<?php
			if (is_numeric(@$property['id'])) {
			?>
			<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete-property-modal"><?= __('Delete') ?></button>
			<?php
			}
			?>
			<a href="/admin/propman" class="btn btn-default"><?= __('Cancel') ?></a>
		</div>
	</div>

</form>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-property-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="delete-property" method="post" action="/admin/propman/delete_property">
				<input type="hidden" name="id" value="<?=@$property['id']?>" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?= __('Delete property') ?></h4>
				</div>
				<div class="modal-body">
					<p><?= __('Are you sure you want to delete this property?') ?></p>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger" id="delete-property-button"><?= __('Delete') ?></button>
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-has-ratecard-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="delete-property-ratecard" method="post" action="/admin/propman/delete_has_ratecard">
                <input type="hidden" name="property_id" value="<?=@$property['id']?>" />
                <input type="hidden" name="ratecard_id" value="" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= __('Unlink rate card') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?= __('Are you sure you want to unlink this ratecard?') ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="delete-has-ratecard-button" data-dismiss="modal"><?= __('Delete') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
	$('.sortable-tbody').sortable();
</script>
