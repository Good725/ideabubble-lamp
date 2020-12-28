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

<form class="col-sm-12 form-horizontal" id="form_add_edit_question" name="form_add_edit_question"
      action="/admin/surveys/save_question/" method="post">

    <div class="form-group">
        <div class="col-sm-9">
            <label class="sr-only" for="title">Title</label>
            <input type="text" class="form-control required" id="title" name="title"
                   placeholder="Enter Your Question here" value="<?= $question->title ?>"/>
        </div>
        <input type="hidden" id="id" name="id" value="<?= $question->id; ?>">
    </div>
    <div id="no_answer_alert"class="alert alert-warning"><a class="close"
                                                  data-dismiss="alert">&times;</a>Please select one answer to add to the question.</div>

    <ul class="nav nav-tabs">
        <li><a href="#summary_tab" data-toggle="tab">Details</a></li>
        <li><a href="#answer_tab" data-toggle="tab">Answers</a></li>
    </ul>

    <div class="tab-content clearfix">
        <? // Summary Tab ?>
        <div class="col-sm-9 tab-pane active" id="summary_tab">

            <? // Publish ?>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="publish">Publish</label>

                <div class="btn-group col-sm-9" data-toggle="buttons">
                    <label class="btn btn-default<?= (is_null($question->id) OR $question->publish == '1') ? ' active' : '' ?>">
                        <input type="radio"<?= (is_null($question->id) OR $question->publish == '1') ? ' checked' : '' ?>  value="1" name="publish">Yes
                    </label>
                    <label class="btn btn-default<?= ($question->publish == '0') ? ' active' : '' ?>">
                        <input type="radio"<?= ($question->publish == '0') ? ' checked' : '' ?>  value="0" name="publish">No
                    </label>

                    <p class="help-inline"></p>
                </div>
            </div>
        </div>

        <? // Tab ?>
        <div class="tab-pane" id="answer_tab">
            <div class="form-group">
                <? // Select Question to Add?>
                <label class="col-sm-3 control-label" for="answer_id">Add Question</label>

                <div class="col-sm-9">
                    <select id="answer_id" name="answer_id" class=" validate[required]">
                        <option value="">Please Select Answer</option>
                        <?php foreach ($answers as $answer):
                            $selected = $question->answer_id == $answer->id ? ' selected="selected"' : '';
                            ?>
                            <option value="<?= $answer->id ?>"
                                    data-name="<?= $answer->title ?>" <?= $selected ?>><?= $answer->id ?> - <?= $answer->title ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
<!--                <div class="col-sm-3">-->
<!--                    <button type="button" class="btn" id="add_question_btn"-->
<!--                            data-content="Add the selected answers to the question">Add-->
<!--                    </button>-->
<!--                </div>-->
            </div>

            <? // List selected Questions ?>
            <table id="answer_list_table" class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">Order</th>
                    <th scope="col">ID</th>
                    <th scope="col">Label</th>
                    <th scope="col">Type</th>
                    <th scope="col">Value</th>
                </tr>
                </thead>
                <tbody>
                <?php if ( ! is_null($options)):
                    foreach ($options as $option):
                        ?>
                        <tr>
                            <td><?= ($option['order']+1) ?></td>
                            <td><?= $option['id'] ?></td>
                            <td><?= $option['label'] ?></td>
                            <td><?= $option['type'] ?></td>
                            <td><?= $option['value'] ?></td>
                        </tr>
                    <?php
                    endforeach;
                endif; ?>
                </tbody>
            </table>

        </div>


    </div>

    <? // Action Buttons ?>
    <input type="hidden" id="save_exit" name="save_exit" value="false"/>

    <div class="col-sm-12">
        <div class="well">
            <button type="submit" id="save_button" data-redirect="self" class="btn btn-primary save_button">Save
            </button>
            <button type="submit" data-redirect="products" class="btn btn-success save_button"
                    onclick="$('#save_exit')[0].setAttribute('value', 'true');">Save &amp; Exit
            </button>
            <button type="reset" class="btn">Reset</button>
        </div>
    </div>
</form>


