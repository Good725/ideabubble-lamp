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

<form class="col-sm-12 form-horizontal" id="form_add_edit_answer" name="form_add_edit_answer"
      action="/admin/surveys/save_answer/" method="post">

    <div class="form-group">
        <div class="col-sm-9">
            <label class="sr-only" for="title">Title</label>
            <input type="text" class="form-control required" id="title" name="title"
                   placeholder="Enter answer title here" value="<?= $answer->title ?>"/>
        </div>
        <input type="hidden" id="id" name="id" value="<?= $answer->id; ?>">
    </div>

    <ul class="nav nav-tabs">
        <li><a href="#summary_tab" data-toggle="tab">Configuration</a></li>
        <li><a href="#option_tab" data-toggle="tab">Options</a></li>
    </ul>

    <div class="tab-content clearfix">
        <? // Summary Tab ?>
        <div class="col-sm-9 tab-pane active" id="summary_tab">

            <? // Group ?>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="group_name">Group</label>

                <div class="col-sm-9">
                    <input type="text" id="group_name" name="group_name" value="<? echo $answer->group_name ?>">
                </div>
            </div>

            <? // Publish ?>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="publish">Publish</label>

                <div class="btn-group col-sm-9" data-toggle="buttons">
                    <label class="btn btn-default<?= (is_null($answer->id) OR $answer->publish == '1') ? ' active' : '' ?>">
                        <input type="radio"<?= (is_null($answer->id) OR $answer->publish == '1') ? ' checked="checked"' : '' ?>  value="1" name="publish">Yes
                    </label>
                    <label class="btn btn-default<?= $answer->publish == '0' ? ' active' : '' ?>">
                        <input type="radio"<?= $answer->publish == '0' ? ' checked="checked"' : '' ?>  value="0" name="publish">No
                    </label>

                    <p class="help-inline"></p>
                </div>
            </div>

            <? // Select?>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="type_id">Type</label>

                <div class="col-sm-9">
                    <select id="type_id" name="type_id">
                        <option value="">Please Select</option>
                        <?php foreach ($types as $type):
                            $selected = $answer->type_id === $type->id ? ' selected="selected"' : '';
                            ?>
                            <option value="<?= $type->id ?>" <?= $selected ?>
                                    data-stub="<?= $type->stub ?>"
                                    data-title="<?= $type->title ?>"><?= $type->title ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

        </div>

        <? // Option Tab ?>
        <div class="tab-pane" id="option_tab">
            <div class="form-group">
                <div id="no_type_selected" class="alert alert-warning"><a class="close"
                                                                          data-dismiss="alert">&times;</a>Please select an option type before adding answers.
                </div>
                <div id="value_already_selected" class="alert alert-warning"><a class="close"
                                                                                data-dismiss="alert">&times;</a>Please enter another value.
                </div>
                <? // Enter Label?>
                <label class="col-sm-2 control-label" for="label">Answer Label</label>

                <div class="col-sm-3">
                    <input type="text" id="label" value="" placeholder="Enter answer Label">
                </div>
                <? // Enter Value ?>
                <label class="col-sm-2 control-label" for="label">Enter Value</label>

                <div class="col-sm-3">
                    <input type="number" id="value" value="" placeholder="Enter value">
                </div>
                <? // Add Option ?>
                <div class="col-sm-2">
                    <button type="button" class="btn" id="add_option_btn"
                            data-content="Add an option for the answer">Add
                    </button>
                </div>
            </div>

            <? // List options ?>
            <table id="answers_list_table" class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">Order</th>
                    <th scope="col">ID</th>
                    <th scope="col">Type</th>
                    <th scope="col">Label</th>
                    <th scope="col">Value</th>
                    <th scope="col">Remove</th>
                </tr>
                </thead>
                <tbody class="sortable-tbody">
                <?php
                $option_list = array();
                foreach ($options as $key => $option):
                    $option_list[] = array(
                        'id'        => $option->id,
                        'answer_id' => $answer->id,
                        'label'     => $option->label,
                        'value'     => $option->value
                    );
                    ?>
                    <tr data-id="<?= $option->id ?>" data-label="<?= $option->label ?>"
                        data-value="<?= $option->value ?>">
                        <td title="<?= __('Drag to reorder') ?>"><span class="icon-bars"></span></td>
                        <td><?= $option->id ?></td>
                        <td>
                            <?php foreach ($types as $type)
                            {
                                echo $type->id == $answer->type_id ? $type->title : '';
                            }
                            ?>
                        </td>
                        <td><?= $option->label ?></td>
                        <td><?= $option->value ?></td>
                        <td class="remove-row"><button type="button" class="btn-link remove-button"><span class="icon-remove"></span></button></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <? // List Options ?>
            <input type="hidden" name="option_list" id="option_list" value="<?= serialize($option_list) ?>">
        </div>


    </div>

    <? // Action Buttons ?>
    <input type="hidden" id="save_exit" name="save_exit" value="false"/>

    <div class="col-sm-12">
        <div class="well">
            <button type="submit" id="save_button" data-redirect="self" class="btn btn-primary save_button"
                    data-content="Save the answer and reload the form">Save
            </button>
            <button type="submit" data-redirect="answer" class="btn btn-success save_button"
                    data-content="Save the answer and go back to the list of answers."
                    onclick="$('#save_exit')[0].setAttribute('value', 'true');">Save &amp; Exit
            </button>
            <button type="reset" class="btn">Reset</button>
        </div>
    </div>
</form>

