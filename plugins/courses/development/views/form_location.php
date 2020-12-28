<? $data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data : array()) ?>

<div class="col-sm-12">
	<?=(isset($alert)) ? $alert : ''?>
	<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
	?>
</div>

<form class="col-sm-12 form-horizontal" id="form_add_edit_location" name="form_add_edit_location" action="/admin/courses/save_location/" method="post">

    <input type="hidden" id="redirect" name="redirect" />

    <!-- Location -->
    <div class="form-group">
    	<div class="col-sm-9">
	        <input type="text" class="form-control required" id="name" name="name" placeholder="Enter location name here" value="<?=isset($data['name']) ? $data['name'] : ''?>"/>
    	</div>
	</div>

    <!-- Parent -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="parent_id">Parent</label>
        <div class="col-sm-7">
            <select class="form-control" id="parent_id" name="parent_id">
                <option value="" <?=( ! isset($data['parent_id']) OR ($data['parent_id'] == '') ) ? 'selected="selected"' : '' ?>>No Parent</option>
                <?php foreach ($locations as $item): ?>
					<option
                        data-address1="<?=$item['address1']?>"
                        data-address2="<?=$item['address2']?>"
                        data-address3="<?=$item['address3']?>"
                        data-county_id="<?=$item['county_id']?>"
                        data-city_id="<?=$item['city_id']?>"
                        data-email="<?=$item['email']?>"
                        data-phone="<?=$item['phone']?>"
                        data-lat="<?=$item['lat']?>"
                        data-lng="<?=$item['lng']?>"
                        value="<?=$item['id']?>" <?=( isset($data['parent_id']) AND ($data['parent_id'] == $item['id']) ) ? 'selected="selected"' : '' ?>><?=$item['name']?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>


    <!-- Location type -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="location_type_id">Location type</label>
        <div class="col-sm-3">
            <select class="required form-control" id="location_type_id" name="location_type_id">
                <option value="" <?=( ! isset($data['location_type_id']) OR ($data['location_type_id'] == '') ) ? 'selected="selected"' : '' ?>>Select location type</option>

                <?php foreach ($types as $type): ?>
					<option value="<?=$type['id']?>" <?=( isset($data['location_type_id']) AND ($data['location_type_id'] == $type['id']) ) ? 'selected="selected"' : '' ?>><?=$type['type']?></option>
                <?php endforeach; ?>
            </select>
		</div>
		<div class="col-sm-3">
            <input type="text" class="form-control" id="new_type" name="new_type" value="" placeholder="Type name to add new"/>
		</div>
		<div class="col-sm-1">
            <button class="btn btn-primary" id="add_type" type="button">Add</button>
        </div>
    </div>    
    
    <!-- Max capacity -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="capacity">Total Capacity</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="capacity" name="capacity" value="<?=isset($data['capacity']) ? $data['capacity'] : ''?>"/>
        </div>
    </div>

    <!-- Online capacity -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="online_capacity">Online Capacity</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="online_capacity" name="online_capacity" value="<?=isset($data['online_capacity']) ? $data['online_capacity'] : ''?>"/>
        </div>
    </div>

    <!-- Address 1 -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="address1">Address 1</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="address1" name="address1" value="<?=isset($data['address1']) ? $data['address1'] : ''?>"/>
        </div>
    </div>

    <!-- Address 2 -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="address2">Address 2</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="address2" name="address2" value="<?=isset($data['address2']) ? $data['address2'] : ''?>"/>
        </div>
    </div>

    <!-- Address 3 -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="address3">Address 3</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="address3" name="address3" value="<?=isset($data['address3']) ? $data['address3'] : ''?>"/>
        </div>
    </div>

    <!-- County -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="county_id">County</label>
        <div class="col-sm-7">
            <select class="required form-control" id="county_id" name="county_id" data-default="<?=@$data['county_id']?>">
                <option value="" <?=( ! isset($data['county_id']) OR ($data['county_id'] == '') ) ? 'selected="selected"' : '' ?>>Select county</option>

                <?php foreach ($counties as $county): ?>
                    <option value="<?=$county['id']?>" <?=( isset($data['county_id']) AND ($data['county_id'] == $county['id']) ) ? 'selected="selected"' : '' ?>><?=$county['name']?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- City -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="city_id">City</label>
        <div class="col-sm-3">
            <select class="required form-control" id="city_id" name="city_id" data-default="<?=@$data['city_id']?>">
                <?php if (@$data['city_id']): ?>
                    <option value="<?=$data['city_id']?>" selected="selected"><?=$data['city']?></option>
                <?php else: ?>
                    <option value="" selected="selected">Please select county first</option>
                <?php endif; ?>
            </select>
		</div>
		<div class="col-sm-3">
			<input type="text" class="form-control" id="new_city" name="new_city" value="" placeholder="Type name to add new"/>
		</div>
		<div class="col-sm-1">
            <button class="btn btn-primary" id="add_city">Add</button>
        </div>
    </div>

    <!-- Email -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="email">Email</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="email" name="email" value="<?=isset($data['email']) ? $data['email'] : ''?>"/>
        </div>
    </div>

    <!-- Phone -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="phone">Phone</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="phone" name="phone" value="<?=isset($data['phone']) ? $data['phone'] : ''?>"/>
        </div>
    </div>

    <!-- Directions -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="directions">Directions</label>
        <div class="col-sm-7">
            <textarea  class="form-control" id="directions" name="directions" rows="4"><?=isset($data['directions']) ? $data['directions'] : ''?></textarea>
        </div>
    </div>

    <!-- gmap -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="directions">Map</label>
        <div class="col-sm-7">

            <div class="mb-3">
                <input type="text" class="sr-only" id="edit-location-map_search" />

                <button id="edit-location-find_location" type="button" class="btn btn-primary btn-lg btn--full d-block">Find location</button>
            </div>

            <div class="map-container mb-3"
                 style="width: 100%; height: 276px;"
                 data-target-x="#edit-lat"
                 data-target-y="#edit-lng"
                 data-init-x="<?=@$data['lat']?>"
                 data-init-y="<?=@$data['lng']?>"
                 data-init-z="10"
                 data-button="#get-address-from-map"
                 data-button-target="#edit-map"
            ></div>

            <div class="mb-3">
                <label class="sr-only" for="edit-lat"><?= __('Latitude') ?></label>
                <input type="text" class="form-control" id="edit-lat" name="lat" value="<?= (isset($data['lat']) AND isset($data['lat'])) ? $data['lat'] : '' ?>" placeholder="<?= __('Latitude') ?>" />
            </div>

            <div class="mb-3">
                <label class="sr-only" for="edit-lng"><?= __('Longitude') ?></label>
                <input type="text" class="form-control" id="edit-lng" name="lng" value="<?= (isset($data['lng']) AND isset($data['lng'])) ? $data['lng'] : '' ?>" placeholder="<?= __('Longitude') ?>" />
            </div>

            <button type="button" class="btn btn-full btn-actions" id="location-reset-button"><?= __('Reset location') ?></button>
        </div>
    </div>

    <?php if(sizeof($rows) > 0) { ?>
    <!-- Rows -->
    <h2 class="border-title"><span>Rows</span></h2>
    <div class="" id="add_rows_section">
        <div class="form-group" >
            <label class="col-sm-2 control-label" for="city_id">Add rows in room</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="new_row" value="" placeholder="Type name/number of row"/>
            </div>
            <label class="col-sm-2 control-label" for="seats_for_new_row">Number of Seats</label>
            <div class="col-sm-2">
                <input type="number" min="0" class="form-control" id="seats_for_new_row" value="" />
            </div>
            <div class="col-sm-1">
                <button class="btn btn-primary" id="add_row_to_the_sub_location">Add</button>
            </div>
        </div>
        <div class="form-group" id="added_rows_container">
            <span>Added room rows</span>
            <?php
            foreach ($rows as $row) {
                $str = '';
                $str .= '<div class="form-group">';
                $str .= '<div class="col-sm-4"><input readonly type="text" class="form-control row-name" id="new_row" value="'.$row["name"].'" placeholder="Type name/number of row"/></div>';
                $str .= '<div class="col-sm-2"><input readonly type="number" min="0" class="form-control row-seats" value="'.$row["seats"].'" /></div>';
                $str .= '<div class="col-sm-2"><button class="btn btn-primary change-row">Change</button></div>';
                $str .= '<div class="col-sm-2"><button class="btn btn-primary remove-row">Remove</button></div>';
                $str .= '</div>';
                echo $str;
            }
            ?>
        </div>
    </div>
    <?php } ?>

    <!-- Location Identifier -->
    <input type="hidden" id="id" name="id" value="<?=isset($data['id']) ? $data['id'] : ''?>"/>

    <div class="well">
        <button type="button" class="btn btn-primary save_button location" data-redirect="save">Save</button>
        <button type="button" class="btn btn-primary save_button location" data-redirect="save_and_exit">Save &amp; Exit</button>
        <button type="reset" class="btn">Reset</button>
        <?php if (isset($data['id'])) : ?>
            <a href="#" class="btn btn-danger" id="btn_delete" data-id="<?=$data['id']?>">Delete</a>
        <?php endif; ?>
    </div>
    <?php if (@$modal) { ?>
    <input type="hidden" name="modal" value="1" />
    <?php } ?>
</form>
<?php if (isset($data['id'])) : ?>
	<div class="modal fade" id="confirm_delete">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3>Warning!</h3>
				</div>

				<div class="modal-body">
					<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected location.</p>
				</div>

				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal">Cancel</a>
					<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
