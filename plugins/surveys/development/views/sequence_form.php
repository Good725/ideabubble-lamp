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

<form class="col-sm-12 form-horizontal" id="form_add_edit_sequence" name="form_add_edit_sequence" action="/admin/surveys/save_sequence/" method="post">

    <div class="form-group">
        <div class="col-sm-9">
            <label class="sr-only" for="title">Title</label>
            <input type="text" class="form-control required" id="title" name="title" placeholder="Enter product title here" value="<?= $sequence->title?>"/>
        </div>
        <input type="hidden" id="id" name="id" value="<?= $sequence->id;?>">
    </div>

    <ul class="nav nav-tabs">
        <li><a href="#summary_tab" data-toggle="tab">Configuration</a></li>
        <li id="set_sequence"><a href="#sequence_tab" data-toggle="tab">Set the sequence</a></li>
    </ul>

    <div class="tab-content clearfix">
        <? // Summary Tab ?>
        <div class="col-sm-9 tab-pane active" id="summary_tab">

            <? // Publish ?>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="publish">Publish</label>
                <div class="btn-group col-sm-9" data-toggle="buttons">
                    <?php $publish = $sequence->publish == '1'; ?>
                    <label class="btn btn-default<?= $publish ? ' active' : '' ?>">
                        <input type="radio"<?= $publish ? ' checked' : '' ?>  value="1" name="publish">Yes
                    </label>
                    <label class="btn btn-default<?= ! $publish ? ' active' : '' ?>">
                        <input type="radio"<?= ! $publish ? ' checked' : '' ?>  value="0" name="publish">No
                    </label>
                    <p class="help-inline"></p>
                </div>
            </div>

            <? // Select?>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="survey_id">Survey</label>
                <div class="col-sm-9" >
                    <select id="survey_id" name="survey_id">
                        <option value="">Please Select</option>
                        <?php foreach($surveys as $option):
                            $selected = $sequence->survey_id == $option->id ? ' selected="selected" ' : '' ;
                            ?>
                            <option value="<?= $option->id?>" <?= $selected?>><?= $option->id?> - <?= $option->title ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

        </div>

        <? // Tab ?>
        <div class="col-sm-9 tab-pane" id="sequence_tab">
            <div id="no_survey_selected" class="alert alert-warning"><a class="close"
                                                                        data-dismiss="alert">&times;</a>Please select a Survey to proceed.
            </div>
            <div id="questionnaire_sequences"></div>
            <input type="hidden" id="sequence_list" name="sequence_list" value="">
        </div>


    </div>

    <!-- Product Identifier -->
    <input type="hidden" id="save_exit" name="save_exit" value="false" />
    <div class="col-sm-12">
        <div class="well">
            <button type="submit" id="save_button" data-redirect="self" class="btn btn-primary save_button">Save</button>
            <button type="submit" data-redirect="products" class="btn btn-success save_button" onclick="$('#save_exit')[0].setAttribute('value', 'true');">Save &amp; Exit</button>
            <button type="reset" class="btn">Reset</button>
        </div>
    </div>
</form>


<script type="text/javascript">
    $(document).ready(function(){
        $('[rel="popover"]').popover();
    });
</script>
