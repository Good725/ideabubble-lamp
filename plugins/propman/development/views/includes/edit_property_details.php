<ul class="nav nav-tabs" role="tablist">
	<li role="presentation" class="active"><a href="#edit-property-details-tab-information" aria-controls="edit-property-details-tab-information" role="tab" data-toggle="tab"><?= __('Key Information') ?></a></li>
	<li role="presentation"><a href="#edit-property-details-tab-description" aria-controls="edit-property-details-tab-description" role="tab" data-toggle="tab"><?= __('Description')     ?></a></li>
	<li role="presentation"><a href="#edit-property-details-tab-facilities"  aria-controls="edit-property-details-tab-facilities"  role="tab" data-toggle="tab"><?= __('Facilities')      ?></a></li>
	<li role="presentation"><a href="#edit-property-details-tab-suitability" aria-controls="edit-property-details-tab-suitability" role="tab" data-toggle="tab"><?= __('Suitability')     ?></a></li>
</ul>

<div class="tab-content">

	<div role="tabpanel" class="tab-pane active" id="edit-property-details-tab-information">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-2 control-label" for="edit-property-building_type"><?= __('Building Type') ?></label>
				<div class="col-sm-5">
					<select class="form-control" id="edit-property-building_type" name="building_type_id">
						<option value=""><?= __('Select a building type') ?></option>
						<?=HTML::optionsFromRows('id', 'name', $buildingTypes, @$property['building_type_id'])?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label" for="edit-property-type"><?= __('Property Type') ?></label>
				<div class="col-sm-5">
					<select class="form-control" id="edit-property-type" name="property_type_id">
						<option value=""><?= __('Select a property type') ?></option>
						<?=HTML::optionsFromRows('id', 'name', $propertyTypes, @$property['property_type_id'])?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label" for="edit-property-group"><?= __('Link Group') ?></label>
				<div class="col-sm-5">
					<select class="form-control" id="edit-property-group" name="group_id">
						<option value=""><?= __('Select a group') ?></option>
						<?=HTML::optionsFromRows('id', 'name', $groups, @$property['group_id'])?>
					</select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label" for="edit-property-refcode"><?= __('Reference code') ?></label>
				<div class="col-sm-5">
					<input type="text" class="form-control" id="edit-property-refcode" name="refcode" value="<?=@$property['ref_code']?>" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<h3><?= __('Beds') ?></h3>

					<div class="form-group">
						<label class="col-sm-3 control-label" for="edit-property-beds_single"><?= __('Single') ?></label>
						<div class="col-sm-4">
							<input type="number" class="form-control" id="edit-property-beds_single" name="beds_single" value="<?=@$property['beds_single']?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label" for="edit-property-beds_double"><?= __('Double') ?></label>
						<div class="col-sm-4">
							<input type="number" class="form-control" id="edit-property-beds_double" name="beds_double" value="<?=@$property['beds_double']?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label" for="edit-property-beds_king"><?= __('King') ?></label>
						<div class="col-sm-4">
							<input type="number" class="form-control" id="edit-property-beds_king" name="beds_king" value="<?=@$property['beds_king']?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label" for="edit-property-beds_bunks"><?= __('Bunks') ?></label>
						<div class="col-sm-4">
							<input type="number" class="form-control" id="edit-property-beds_bunks" name="beds_bunks" value="<?=@$property['beds_bunks']?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label" for="edit-property-max_occupancy"><?= __('Maximum occupancy') ?></label>
						<div class="col-sm-4">
							<input type="number" class="form-control" id="edit-property-max_occupancy" name="max_occupancy" value="<?=@$property['max_occupancy']?>" />
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<h3><?= __('Rooms') ?></h3>

					<div class="form-group">
						<label class="col-sm-3 control-label" for="edit-property-rooms_ensuite"><?= __('Ensuite') ?></label>
						<div class="col-sm-4">
							<input type="number" class="form-control" id="edit-property-rooms_ensuite" name="rooms_ensuite" value="<?=@$property['rooms_ensuite']?>" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label" for="edit-property-rooms_bathrooms"><?= __('Bathrooms') ?></label>
						<div class="col-sm-4">
							<input type="number" class="form-control" id="edit-property-rooms_bathrooms" name="rooms_bathrooms" value="<?=@$property['rooms_bathrooms']?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Description tab -->
	<div role="tabpanel" class="tab-pane" id="edit-property-details-tab-description">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-12" for="edit-property-search_summary"><?= __('Search summary') ?></label>
				<div class="col-sm-12">
					<textarea class="form-control ckeditor" id="edit-property-search_summary" name="summary"><?=htmlentities(@$property['summary'])?></textarea>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-12" for="edit-property-description"><?= __('Description') ?></label>
				<div class="col-sm-12">
					<textarea class="form-control ckeditor" id="edit-property-description" name="description"><?=htmlentities(@$property['description'])?></textarea>
				</div>
			</div>
		</div>
	</div>

	<!-- Facilities tab -->
	<div role="tabpanel" class="tab-pane" id="edit-property-details-tab-facilities">
		<?php
		foreach ($facilityGroups as $facilityGroup) {
			?>
			<div class="col-sm-4">
				<h3><?= __($facilityGroup['name']) ?></h3>
				<ul class="list-unstyled">
					<?php
					foreach ($facilityTypes as $facilityType) {
						if ($facilityType['facility_group_id'] != $facilityGroup['id']) {
							continue;
						}

						$checked = false;
						$surcharge = null;
						if (@$property['facilities'])
							foreach ($property['facilities'] as $facility) {
								if ($facility['facility_type_id'] == $facilityType['id']) {
									$checked = true;
									$surcharge = $facility['surcharge'];
									break;
								}
							}
						?>
						<li>
							<label>
								<input type="checkbox"
									   name="facility[<?=$facilityType['id']?>]"
									   value="1" <?=$checked ? 'checked="checked"' : ''?>
								/> <?= __($facilityType['name']) ?>
							</label>
							<div class="surcharge <?=$surcharge ? 'yes' : ''?>">
								<select name="has_surcharge[<?=$facilityType['id']?>]">
									<option value="free">Free</option>
									<option value="yes" <?=$surcharge ? 'selected="selected"' : ''?>>Surcharge</option>
								</select>
								<input type="text" name="surcharge[<?=$facilityType['id']?>]" value="<?=$surcharge?>" size="3" <?=$surcharge == null ? 'disabled="disabled"' : ''?> />
							</div>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
			<?php
		}
		?>
	</div>

	<!-- Suitability tab -->
	<div role="tabpanel" class="tab-pane" id="edit-property-details-tab-suitability">
		<div class="form-horizontal">
			<?php
			foreach ($suitabilityGroups as $suitabilityGroup) {
				?>
				<div class="col-sm-6 form-group">
					<label class="col-sm-12" for="edit-property-children"><?= __($suitabilityGroup['name']) ?></label>

					<div class="col-sm-12">
						<select class="form-control" name="suitability[<?=$suitabilityGroup['id']?>]">
							<option value=""><?= __('Please select') ?></option>
							<?php
							foreach ($suitabilityTypes as $suitabilityType) {
								if ($suitabilityType['suitability_group_id'] != $suitabilityGroup['id']) {
									continue;
								}

								$selected = false;
								if (@$property['suitabilities'])
									foreach ($property['suitabilities'] as $suitability) {
										if ($suitability['suitability_type_id'] == $suitabilityType['id']) {
											$selected = true;
											break;
										}
									}
								?>
								<option value="<?=$suitabilityType['id']?>" <?=$selected ? 'selected="selected"' : ''?>><?=__($suitabilityType['name'])?></option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>