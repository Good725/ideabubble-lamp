<div class="col-sm-12">
    <?= (isset($alert)) ? $alert : '' ?>
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

<form class="col-sm-12 form-horizontal" id="form_add_edit_group" name="form_add_edit_group"
      action="/admin/surveys/save_group/" method="post">

    <div class="form-group">
        <div class="col-sm-9">
            <label class="sr-only" for="title">Title</label>
            <input type="text" class="form-control required" id="title" name="title"
                   placeholder="Enter group title here" value="<?= $group->title ?>"/>
        </div>
        <input type="hidden" id="id" name="id" value="<?= $group->id; ?>">
    </div>

    <? // Publish ?>
    <div class="form-group">
        <label class="col-sm-3 control-label" for="publish">Publish</label>

        <div class="btn-group col-sm-9" data-toggle="buttons">
            <label class="btn btn-default<?= (is_null($group->id) OR $group->publish == '1') ? ' active' : '' ?>">
                <input type="radio"<?= (is_null($group->id) OR $group->publish == '1') ? ' checked="checked"' : '' ?>  value="1" name="publish">Yes
            </label>
            <label class="btn btn-default<?= $group->publish == '0' ? ' active' : '' ?>">
                <input type="radio"<?= $group->publish == '0' ? ' checked="checked"' : '' ?>  value="0" name="publish">No
            </label>

            <p class="help-inline"></p>
        </div>
    </div>

    <? // Action Buttons ?>
    <input type="hidden" id="save_exit" name="save_exit" value="false"/>

    <div class="col-sm-12">
        <div class="well">
            <button type="submit" id="save_button" data-redirect="self" class="btn btn-primary save_button"
                    data-content="Save the group and reload the form">Save
            </button>
            <button type="submit" data-redirect="answer" class="btn btn-success save_button"
                    data-content="Save the group and go back to the list of groups."
                    onclick="$('#save_exit')[0].setAttribute('value', 'true');">Save &amp; Exit
            </button>
            <button type="reset" class="btn">Reset</button>
        </div>
    </div>
</form>
