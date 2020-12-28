<div class="form-group">
    <div class="col-sm-12">
        <label class="control-label" for="<?= $id_prefix ?>country">Country</label>

        <?php
        $countries  = Model_Residence::get_all_countries();
        $name       = isset($country_field) ? $country_field : 'country';
        $selected   = (empty($country)) ? 'IE' : $country;
        $options    = html::optionsFromRows('code', 'name', $countries, $selected, ['value' => '', 'label' => '']);
        $attributes = ['class' => 'ib-combobox validate[required]', 'id' => $id_prefix.'country', 'data-placeholder' => 'Please select'];
        echo Form::ib_select(null, $name, $options, $selected, $attributes);
        ?>
    </div>
</div>

<div class="form-group">
    <div class="col-sm-12">
        <label class="control-label" for="<?= $id_prefix ?>address1">Address 1</label>
    </div>

    <div class="col-sm-12">
        <?php
        $name = isset($address_1_field) ? $address_1_field : 'address_1';
        $address1 = isset($address1) ? $address1 : null;
        $attributes = ['class' => 'enforce_ucfirst', 'id' => $id_prefix.$name];
        echo Form::ib_input(null, $name, $address1, $attributes);
        ?>
    </div>
</div>

<div class="form-group">
    <div class="col-sm-12">
        <label class="control-label" for="<?= $id_prefix ?>address2">Address 2</label>
    </div>

    <div class="col-sm-12">
        <?php
        $name = isset($address_2_field) ? $address_2_field : 'address_2';
        $address2 = isset($address2) ? $address2 : null;
        $attributes = ['class' => 'enforce_ucfirst', 'id' => $id_prefix.$name];
        echo Form::ib_input(null, $name, $address2, $attributes);
        ?>
    </div>
</div>

<div class="form-group">
    <div class="col-sm-12">
        <label class="control-label" for="<?= $id_prefix ?>address3">Address 3</label>
    </div>

    <div class="col-sm-12">
        <?php
        $name = isset($address_3_field) ? $address_3_field : 'address_3';
        $address3 = isset($address3) ? $address3 : null;
        $attributes = ['class' => 'enforce_ucfirst', 'id' => $id_prefix.$name];
        echo Form::ib_input(null, $name, $address3, $attributes);
        ?>
    </div>
</div>

<?php $town_field = isset($town_field) ? $town_field : 'town'; ?>
<?php if ($town_field !== false): ?>
    <div class="form-group">
        <div class="col-sm-12">
            <label class="control-label" for="<?= $id_prefix ?>town">Town</label>
        </div>

        <div class="col-sm-12">
            <?php
            $town = isset($town) ? $town : null;
            $attributes = ['class' => 'enforce_ucfirst', 'id' => $id_prefix.$town_field];
            echo Form::ib_input(null, $town_field, $town, $attributes);
            ?>
        </div>
    </div>
<?php endif; ?>

<?php $county_field = isset($county_field) ? $county_field : 'county'; ?>
<div class="form-group">
    <div class="col-sm-12">
        <label class="control-label" for="<?= $id_prefix ?>county">County</label>

        <?php
        $selected = isset($county) ? $county : null;
        if (isset($counties)) {
            $options = $counties;
        } else {
            $counties = Model_Residence::get_all_counties('plugin_courses_counties');
            $options = html::optionsFromRows('id', 'name', $counties, $selected, ['value' => '', 'label' => '']);
        }
       
        $attributes = ['class' => 'ib-combobox', 'id' => $id_prefix.'county', 'data-placeholder' => 'Please select'];
        echo Form::ib_select(null, $county_field, $options, null, $attributes);
        ?>
    </div>
</div>

<?php $city_field = isset($city_field) ? $city_field : 'city_id'; ?>
<?php if ($city_field !== false): ?>
    <div class="form-group">
        <div class="col-sm-12">
            <label class="control-label" for="location-modal-city">City</label>
        </div>

        <?php if (!empty($cities)): ?>
            <div class="col-sm-6">
                <?php
                echo Form::ib_select(null, 'city_id', $cities, null, ['id' => 'location-modal-city']);
                ?>
            </div>

            <div class="col-sm-1 text-center">
                <label class="control-label">OR</label>
            </div>
        <?php endif; ?>

        <div class="<?= empty($cities) ? 'col-sm-12' : 'col-sm-5' ?>">
            <?= Form::ib_input(null, 'new_city', null, ['id' => 'location-modal-new_city', 'placeholder' => 'Enter a new city']) ?>
        </div>
    </div>
<?php endif; ?>

<?php $postcode_field = isset($postcode_field) ? $postcode_field : 'postcode'; ?>

<?php if ($postcode_field !== false): ?>
    <div class="form-group">
        <div class="col-sm-12">
            <label for="<?= $id_prefix ?>postcode">Postcode</label>

            <?php
            $postcode = isset($postcode) ? $postcode : null;
            $attributes = ['id' => $id_prefix.$postcode_field, 'placeholder' => 'e.g. V94 Y58Y'];
            echo Form::ib_input(null, $postcode_field, $postcode, $attributes);
            ?>
        </div>
    </div>
<?php endif; ?>

<div class="form-group">
    <div class="col-sm-12">
        <button id="<?= $id_prefix ?>find_location" type="button" class="btn btn-primary btn-lg btn--full d-block add">Find location</button>
    </div>
</div>

<div class="google-map-box">
    <h3 class="border-title">Google map</h3>

    <div class="form-group">
        <div class="col-sm-12">
            <input type="text" class="sr-only" id="<?= $id_prefix ?>map_search" />
            <div class="border mt-2" id="<?= $id_prefix ?>map_summary" style="height: 250px;" class="map"></div>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="col-sm-12">
        <label class="control-label" for="<?= $id_prefix ?>coordinates">Coordinates</label>

        <?php
        $latitude  = isset($latitude)  ? $latitude  : '';
        $longitude = isset($longitude) ? $longitude : '';

        if (empty($latitude) && isset($coordinates) && strpos($coordinates, ',') !== false) {
            $latitude  = trim(explode(',', $coordinates)[0]);
            $longitude = trim(explode(',', $coordinates)[1]);
        }

        $coordinates = trim(trim($latitude.','.$longitude), ',');
        ?>

        <input type="hidden" name="latitude"  id="<?= $id_prefix ?>latitude"  value="<?= $latitude  ?>" />
        <input type="hidden" name="longitude" id="<?= $id_prefix ?>longitude" value="<?= $longitude ?>" />

        <?php
        $name = isset($coordinates_field) ? $coordinates_field : 'coordinates';
        $attributes = ['id' => $id_prefix.'coordinates', 'placeholder' => ('Click on the map'), 'readonly' => true];
        echo Form::ib_input(null, $name, $coordinates, $attributes);
        ?>
    </div>
</div>

<?php // Get the "Find Location" button to centre the map (This can/should be genericised to only be declared once) ?>
<script>
    $(document).on('click', '#<?= $id_prefix ?>find_location', function()
    {
        var address1 = document.getElementById('<?= $id_prefix ?>address1').value;
        var address2 = document.getElementById('<?= $id_prefix ?>address2').value;
        var address3 = document.getElementById('<?= $id_prefix ?>address3').value;
        var town     = document.getElementById('<?= $id_prefix ?>town').value;
        var county   = $('#<?= $id_prefix ?>county').find(':not([value=""]):selected').html();
        var country  = $('#<?= $id_prefix ?>country').find(':not([value=""]):selected').html();
        county  = (county  == null) ? '' : county;
        country = (country == null) ? '' : country;

        var address = (address1 + ' ' + address2 + ' ' + address3 + ' ' + town + ' ' + county + ' ' + country).trim();
        var $search = $('#<?= $id_prefix ?>map_search');
        $search.val(address);

        google.maps.event.trigger($search[0], 'focus', {});
        google.maps.event.trigger($search[0], 'keydown', { keyCode: 13 });
    });


</script>