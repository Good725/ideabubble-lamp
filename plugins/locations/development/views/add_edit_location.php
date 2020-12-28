<? $data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data : array()) ?>

<div class="row">
    <div class="span12">
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
</div>

<form class="col-sm-9 form-horizontal" id="form_add_edit_location" name="form_add_edit_location" action="/admin/locations/save/" method="post">
    <!-- Title -->
    <div class="form-group">
		<div class="col-sm-12">
			<input type="text" class="form-control required validate[required]" id="title" name="title" placeholder="Enter location title here" value="<?=isset($data['title']) ? $data['title'] : ''?>"/>
		</div>
    </div>

    <!-- Type -->
    <div class="form-group">
        <label class="col-sm-3 control-label" for="type">Type</label>
        <div class="col-sm-4">
            <select class="form-control" id="type" name="type">
                <?php if ( ! isset($data['type']) OR ($data['type'] == '') ): ?>
                    <option value="">-- Select Type --</option>
                <?php endif; ?>

                <?php foreach ($types as $item): ?>
                    <option value="<?=$item?>" <?=( isset($data['type']) AND ($data['type'] == $item) ) ? 'selected="selected"' : '' ?>><?=$item?></option>
                <?php endforeach; ?>
            </select>
		</div>
		<div class="col-sm-5">
            <input type="text" class="form-control" id="new_type" name="new_type" placeholder="Enter new type text" value="<?=isset($data['new_type']) ? $data['new_type'] : ''?>">
        </div>
    </div>

    <!-- Address 1 -->
    <div class="form-group">
        <label class="col-sm-3 control-label" for="address_1">Address 1</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="address_1" name="address_1" value="<?=isset($data['address_1']) ? $data['address_1'] : ''?>"/>
        </div>
    </div>

    <!-- Address 2 -->
    <div class="form-group">
        <label class="col-sm-3 control-label" for="address_2">Address 2</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="address_2" name="address_2" value="<?=isset($data['address_2']) ? $data['address_2'] : ''?>"/>
        </div>
    </div>

    <!-- Address 3 -->
    <div class="form-group">
        <label class="col-sm-3 control-label" for="address_3">Address 3</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="address_3" name="address_3" value="<?=isset($data['address_3']) ? $data['address_3'] : ''?>"/>
        </div>
    </div>

    <!-- County -->
    <div class="form-group">
        <label class="col-sm-3 control-label" for="county">County</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="county" name="county" value="<?=isset($data['county']) ? $data['county'] : ''?>"/>
        </div>
    </div>

    <!-- Phone -->
    <div class="form-group">
        <label class="col-sm-3 control-label" for="phone">Phone</label>
        <div class="col-sm-9">
            <input type="text" class="form-control validate[custom[phone]]"" id="phone" name="phone" value="<?=isset($data['phone']) ? $data['phone'] : ''?>"/>
        </div>
    </div>

    <!-- Email -->
    <div class="form-group">
        <label class="col-sm-3 control-label" for="email">Email</label>
        <div class="col-sm-9">
            <input type="text" class="form-control validate[custom[email]]" id="email" name="email" value="<?=isset($data['email']) ? $data['email'] : ''?>"/>
        </div>
    </div>

    <!-- Map Reference -->
    <div class="form-group">
        <label class="col-sm-3 control-label" for="map_reference">Map Reference</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="map_reference" name="map_reference" value="<?=isset($data['map_reference']) ? $data['map_reference'] : ''?>"/>
        </div>
    </div>

    <!-- Publish -->
    <div class="form-group">
        <label class="col-sm-3 control-label" for="publish">Publish</label>
        <div class="col-sm-9">
            <div class="btn-group" data-toggle="buttons">
                <label class="btn btn-default<?= (! isset($data['publish']) OR $data['publish'] == '1') ? ' active' : '' ?>">
                    <input type="radio"<?= (! isset($data['publish']) OR $data['publish'] == '1') ? ' checked="checked"' : '' ?> value="1" name="publish">Yes
                </label>
                <label class="btn btn-default<?= (isset($data['publish']) AND $data['publish'] == '0') ? ' active' : '' ?>">
                    <input type="radio"<?= ( isset($data['publish']) AND $data['publish'] == '0') ? ' checked="checked"' : '' ?> value="0" name="publish">No
                </label>
            </div>
        </div>
    </div>

    <!-- Location Identifier -->
    <input type="hidden" id="id" name="id" value="<?=isset($data['id']) ? $data['id'] : ''?>"/>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn clear">Clear</button>
    </div>
</form>
