<fieldset>
    <legend>Family Information</legend>
    <div class="form-group">
        <label class="col-sm-3 control-label" for="family_id">Family</label>
        <div class="col-sm-9">
            <input type="text" class="form-control family autocomplete" name="family[0][family]" value="<?=@$data['family']['family']?>"/>
            <input type="hidden" class="family_id" name="family[0][id]"  value="<?=@$data['family']['id']?>"/>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label" for="role">Role</label>
        <div class="col-sm-9">
            <select class="form-control" name="family[0][role]">
                <?php
                $family_role = '';
                if (isset($data['family']['members'])) {
                    foreach ($data['family']['members'] as $fmember) {
                        if ($fmember['id'] == @$contact['id']) {
                            $family_role = $fmember['role'];
                            break;
                        }
                    }
                }
                ?>
                <option value="">     </option>
                <?=html::optionsFromArray(
                    array('Student' => 'Student', 'Parent' => 'Parent', 'Mature' => 'Mature'),
                    $family_role
                )?>
            </select>
        </div>
    </div>

</fieldset>