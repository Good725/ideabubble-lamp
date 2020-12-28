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
<form name="group-edit" id="group-edit" method="post" action="/admin/propman/edit_group/<?=@$group['id']?>">
	<input type="hidden" name="id" value="<?=@$group['id']?>" />

	<div class="form-group clearfix">
		<label class="sr-only" for="edit-property-group-name"><?= __('Enter group title') ?></label>
		<div class="col-sm-10">
			<input type="text" class="form-control ib-title-input required" id="edit-property-group-name" name="name" value="<?=@$group['name']?>" placeholder="<?= __('Enter group title') ?>" />
		</div>
		<div class="col-sm-2">
			<label>
				<span class="sr-only"><?= __('Publish') ?></span>
				<input type="hidden" name="published" value="0" /><?php // If the checkbox is unticked, this value will get sent to the server  ?>
				<input type="checkbox" name="published" value="1"<?= ( ! isset($group['published']) OR $group['published'] == 1) ? ' checked="checked"' : ''?> data-toggle="toggle" data-onstyle="success" data-on="<?= __('Published') ?>" data-off="<?= __('Unpublished') ?>" />
			</label>
		</div>
	</div>

	<div class="col-sm-12">

		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#edit-group-tab-address"     aria-controls="edit-group-tab-address"     role="tab" data-toggle="tab"><?= __('Address')     ?></a></li>
            <?php
            if (is_numeric(@$group['id'])) {
            ?>
            <li role="presentation"><a href="#edit-group-tab-properties" aria-controls="edit-group-tab-properties"   role="tab" data-toggle="tab"><?= __('Properties') ?></a></li>
            <?php
            }
            ?>
			<li role="presentation"><a href="#edit-group-tab-information" aria-controls="edit-group-tab-information" role="tab" data-toggle="tab"><?= __('Information') ?></a></li>
			<li role="presentation"><a href="#edit-group-tab-calendar" aria-controls="edit-group-tab-calendar" role="tab" data-toggle="tab"><?= __('Calendar') ?></a></li>
		</ul>

		<div class="tab-content">

			<!-- Address tab -->
			<div role="tabpanel" class="tab-pane active" id="edit-group-tab-address">
				<div class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="edit-group-address1"><?= __('Address 1') ?></label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="edit-group-address1" name="address1" value="<?=@$group['address1']?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="edit-group-address2"><?= __('Address 2') ?></label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="edit-group-address2" name="address2" value="<?=@$group['address2']?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="edit-group-country_id"><?= __('Country') ?></label>
						<div class="col-sm-5">
							<select class="form-control" id="edit-group-country_id" name="country_id">
								<?=HTML::optionsFromArray(
									Model_Propman::countries(),
									@$group['countryId'],
									array('value' => '', 'label' => __('Please select'))
								);?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="edit-group-county_id"><?= __('County') ?></label>
						<div class="col-sm-5">
							<select class="form-control" id="edit-group-county_id" name="county_id">
								<?=HTML::optionsFromArray(
									Model_Propman::counties(@$group['countryId']),
									@$group['countyId'],
									array('value' => '', 'label' => __('Please select'))
								);?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="edit-group-city"><?= __('City') ?></label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="edit-group-city" name="city" value="<?=@$group['city']?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="edit-group-eircode"><?= __('Eircode') ?></label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="edit-group-eircode" name="postcode" value="<?=@$group['postcode']?>" />
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
                                 data-target-x="#edit-group-xcoordinates"
                                 data-target-y="#edit-group-ycoordinates"
                                 data-init-x="<?=@$group['latitude']?>"
                                 data-init-y="<?=@$group['longitude']?>"
                                 data-init-z="10"
                                 data-button="#get-address-from-map"
                                 data-button-target="#edit-group-eircode"
                                ></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-offset-2 col-sm-2 control-label" for="edit-group-xcoordinates" ><?= __('X co-ordinates') ?></label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="edit-group-xcoordinates" name="latitude" value="<?=@$group['latitude']?>" />
						</div>
						<label class="col-sm-2 control-label" for="edit-group-ycoordinates" ><?= __('Y co-ordinates') ?></label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="edit-group-ycoordinates" name="longitude" value="<?=@$group['longitude']?>" />
						</div>
					</div>
				</div>
			</div>

			<!-- Properties tab -->
			<div role="tabpanel" class="tab-pane" id="edit-group-tab-properties">
				<div class="col-sm-12">
					<table class="table table-striped" id="edit-group-properties-table">
						<thead>
							<tr>
								<th scope="col"><?= __('Order') ?></th>
								<th scope="col"><?= __('Thumb') ?></th>
								<th scope="col"><?= __('ID') ?></th>
								<th scope="col"><?= __('Title') ?></th>
                                <th scope="col"><?= __('Type') ?></th>
                                <th scope="col"><?= __('Max Sleep') ?></th>
								<th scope="col"><?= __('Remove') ?></th>
							</tr>
						</thead>
						<tbody class="sortable-tbody">
						<?php
						foreach ($properties as $property) {
						?>
							<tr data-id="<?=$property['id']?>">
								<td title="<?= __('Drag to reorder') ?>">
									<span class="icon-bars"></span>
								</td>
								<td><?=$property['thumb_url'] ? '<img src="' . $property['thumb_url'] . '" style="max-width:50px; max-height:50px;" />' : ''?></td>
								<td><?=$property['id']?></td>
								<td><?=$property['name']?></td>
                                <td><?=$property['property_type'] ?></td>
                                <td><?=$property['max_occupancy'] ?></td>
								<td>
									<button data-id="<?=$property['id']?>"
                                            data-refcode="<?=$property['ref_code']?>"
                                            type="button"
                                            class="btn-link btn-remove"
                                            title="<?= __('Remove') ?>"
                                            data-toggle="modal"
                                            data-target="#delete-property-modal">
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

			<!-- Information tab -->
			<div role="tabpanel" class="tab-pane" id="edit-group-tab-information">
				<div class="form-horizontal col-sm-12">
					<div class="form-group">
						<label class="col-sm-12" for="edit-group-host_contact"><?= __('Host Contact') ?></label>
						<div class="col-sm-6">
							<select class="form-control ib-combobox" id="edit-group-host_contact" name="host_contact_id" data-placeholder="<?= __('Select a contact') ?>">
								<?=HTML::optionsFromArray($ocontacts, @$group['host_contact_id'], array('value' => '', 'label' => ''));?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-12" for="edit-group-arrival_details">Arrival Details</label>
						<div class="col-sm-12">
							<textarea class="form-control ckeditor" id="edit-group-arrival_details" name="arrival_details"><?=@$group['arrival_details']?></textarea>
						</div>
					</div>
				</div>

			</div>

            <div role="tabpanel" class="tab-pane" id="edit-group-tab-calendar">
                <div class="col-sm-12 form-horizontal">

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
                    </div>

                    <div class="col-sm-12 ib-calendar" id="edit-group-calendar">
                        <input type="hidden" name="calendar" value="<?=isset($group['calendar']) ? HTML::chars(json_encode($group['calendar'])) : ''?>" />
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
		</div>

	</div>

	<div class="col-sm-12" style="clear: both;">
		<div class="well">
			<button type="submit" class="btn btn-primary" name="action" value="save"><?= __('Save') ?></button>
			<button type="submit" class="btn btn-primary" name="action" value="save_and_exit"><?= __('Save & Exit') ?></button>
			<button type="reset" class="btn btn-default"><?= __('Reset') ?></button>
            <?php
            if (is_numeric(@$group['id'])) {
            ?>
            <button type="button" class="btn btn-danger" data-toggle="modal"
                    data-target="#delete-property-group-modal"><?= __('Delete') ?></button>
            <?php
            }
            ?>
            <a href="/admin/propman/groups" class="btn btn-default"><?= __('Cancel') ?></a>
		</div>
	</div>

</form>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-property-group-modal">
	<div class="modal-dialog">
		<div class="modal-content">
            <form action="/admin/propman/delete_group" method="post">
                <input type="hidden" name="id" value="<?=@$group['id']?>" />
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?= __('Delete group') ?></h4>
			</div>
			<div class="modal-body">
				<p><?= sprintf(__('Are you sure you want to delete %s?'), @$group['name']) ?></p>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-danger" id="delete-property-group-button"><?= __('Delete') ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
			</div>
            </form>
		</div>
	</div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-property-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/propman/delete_property?redirect=group&group_id=<?=@$group['id']?>" method="post">
                <input type="hidden" name="id" value="<?=@$group['id']?>" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= __('Delete property') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?= __('Are you sure you want to delete <span class="ref_code"></span>?') ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="delete-property-button" data-dismiss="modal"><?= __('Delete') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="edit-group-date-range-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?= __('Update these dates') ?></h4>
			</div>
			<div class="modal-body col-sm-12 form-horizontal">
				<div class="col-sm-6">
					<label class="col-sm-12" for="edit-group-check_in"><?= __('Check in') ?></label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="edit-group-check_in" />
					</div>
				</div>

				<div class="col-sm-6">
					<label class="col-sm-12" for="edit-group-check_out"><?= __('Check out') ?></label>
					<div class="col-sm-12">
						<input type="text" class="form-control" id="edit-group-check_out" />
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary"><?= __('OK') ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
			</div>
		</div>
	</div>
</div>
<script>
var countries = <?=json_encode(Model_Propman::countries())?>;
var counties = <?=json_encode(Model_Propman::counties('all'))?>;
</script>
