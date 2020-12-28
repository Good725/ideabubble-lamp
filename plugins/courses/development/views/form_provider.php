<?php $data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data : array()) ?>

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

<form class="col-sm-12 form-horizontal" id="form_add_edit_location" name="form_add_edit_location" action="/admin/courses/save_provider/" method="post">

    <input type="hidden" id="redirect" name="redirect" />

    <!-- Provider -->
    <div class="form-group">
		<div class="col-sm-7">
			<label class="sr-only" for="name">Name</label>
			<input type="text" class="form-control required" id="name" name="name" placeholder="Enter provider name here" value="<?=isset($data['name']) ? $data['name'] : ''?>"/>
		</div>
    </div>

    <!-- Provider type -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="type_id">Provider type</label>
        <div class="col-sm-5">
            <select class="required form-control" id="type_id" name="type_id">
                <option value="" <?=( ! isset($data['type_id']) OR ($data['type_id'] == '') ) ? 'selected="selected"' : '' ?>>Select provider type</option>

                <?php foreach ($types as $type): ?>
                    <option value="<?=$type['id']?>" <?=( isset($data['type_id']) AND ($data['type_id'] == $type['id']) ) ? 'selected="selected"' : '' ?>><?=$type['type']?></option>
                <?php endforeach; ?>
            </select>
<!--            <input type="text" class="input" id="new_type" name="new_type" value="" placeholder="Type name to add new"/>-->
<!--            <button class="btn btn-primary" id="add_type">Add</button>-->
        </div>
    </div>

    <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'franchisee')) { ?>
    <!-- Franchisee -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="franchisee_id">Franchisee</label>
        <div class="col-sm-5">
            <select class="required form-control" id="franchisee_id" name="franchisee_id">
                <option value="">Select Franchisee</option>
                <?=html::optionsFromRows('id', 'email', $franchisees, @$data['franchisee_id'])?>
            </select>
        </div>
    </div>
    <?php } ?>

    <!-- Address 1 -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="address1">Address 1</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" id="address1" name="address1" value="<?=isset($data['address1']) ? $data['address1'] : ''?>"/>
        </div>
    </div>

    <!-- Address 2 -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="address2">Address 2</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" id="address2" name="address2" value="<?=isset($data['address2']) ? $data['address2'] : ''?>"/>
        </div>
    </div>

    <!-- Address 3 -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="address3">Address 3</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" id="address3" name="address3" value="<?=isset($data['address3']) ? $data['address3'] : ''?>"/>
        </div>
    </div>

    <!-- County -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="county_id">County</label>
        <div class="col-sm-5">
            <select class="form-control" id="county_id" name="county_id">
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
        <div class="col-sm-5">
            <select class="form-control" id="city_id" name="city_id">
                <?php if (@$data['city_id']): ?>
                    <option value="<?=$data['city_id']?>" selected="selected"><?=$data['city']?></option>
                <?php else: ?>
                    <option value="" selected="selected">Please select county first</option>
                <?php endif; ?>
            </select>
        </div>
    </div>

    <!-- Web address -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="web_address">Web address</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" id="web_address" name="web_address" value="<?=isset($data['web_address']) ? $data['web_address'] : ''?>"/>
        </div>
    </div>

    <!-- Web address -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="list_url">List Url</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" id="list_url" name="list_url" value="<?=isset($data['list_url']) ? $data['list_url'] : ''?>"/>
        </div>
    </div>

    <!-- Email -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="email">Email</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" id="email" name="email" value="<?=isset($data['email']) ? $data['email'] : ''?>"/>
        </div>
    </div>

    <!-- Phone -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="phone">Phone</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" id="phone" name="phone" value="<?=isset($data['phone']) ? $data['phone'] : ''?>"/>
        </div>
    </div>

    <!-- Publish -->
    <div class="form-group">
        <label class="col-sm-2 control-label" for="publish">Publish</label>
        <div class="col-sm-5">
            <div class="btn-group" data-toggle="buttons">
				<?php $publish = ( ! isset($data['publish']) OR $data['publish'] == '1'); ?>
				<label class="btn btn-plain<?= $publish ? ' active' : '' ?>">
					<input type="radio" name="publish" value="1" id="publish_yes"<?= $publish ? ' checked' : '' ?> />Yes
				</label>
				<label class="btn btn-plain<?= ( ! $publish) ? ' active' : '' ?>">
					<input type="radio" name="publish" value="0" id="publish_no"<?= ( ! $publish) ? ' checked' : '' ?> />No
				</label>


			</div>
        </div>
    </div>

    <!-- Location Identifier -->
    <input type="hidden" id="id" name="id" value="<?=isset($data['id']) ? $data['id'] : ''?>"/>

	<div class="well">
		<button type="button" class="btn btn-primary save_button" data-redirect="save">Save</button>
		<button type="button" class="btn btn-primary save_button" data-redirect="save_and_exit">Save &amp; Exit</button>
		<button type="reset" class="btn">Reset</button>
		<?php if (isset($data['id'])) : ?>
			<a class="btn btn-danger" id="btn_delete" data-id="<?=$data['id']?>">Delete</a>
		<?php endif; ?>
	</div>
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
					<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected provider.</p>
				</div>

				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal">Cancel</a>
					<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

