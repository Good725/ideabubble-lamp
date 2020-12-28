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

<form class="col-sm-12 form-horizontal" id="form_add_edit_survey" name="form_add_edit_survey"
      action="/admin/surveys/save_survey/" method="post">

    <div class="form-group">
        <div class="col-sm-9">
            <label class="sr-only" for="title">Title</label>
            <input type="text" class="form-control required" id="title" name="title"
                   placeholder="Enter Survey title here" value="<?= $survey->title ?>"/>
        </div>
        <input type="hidden" id="id" name="id" value="<?= $survey->id; ?>">
    </div>

    <ul class="nav nav-tabs">
        <li><a href="#summary_tab" data-toggle="tab">Details</a></li>
        <li id="question_survey"><a href="#questions_tab" data-toggle="tab">Questions</a></li>
        <li id="redirect_survey"><a href="#redirects_tab" data-toggle="tab">Redirect</a></li>
        <li id="preview_survey"><a href="#preview_tab" data-toggle="tab">Preview</a></li>
    </ul>

    <div class="tab-content clearfix">
        <? // Summary Tab ?>
        <div class="col-sm-9 tab-pane active" id="summary_tab">

            <? // Publish ?>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="publish">Publish</label>

                <div class="btn-group col-sm-9" data-toggle="buttons">
                    <label class="btn btn-default<?= (is_null($survey->publish) OR $survey->publish === '1') ? ' active' : '' ?>">
                        <input type="radio"<?= (is_null($survey->publish) OR $survey->publish === '1') ? ' checked' : '' ?>  value="1" name="publish">Yes
                    </label>
                    <label class="btn btn-default<?= $survey->publish === '0' ? ' active' : '' ?>">
                        <input type="radio"<?= $survey->publish === '0' ? ' checked' : '' ?>  value="0" name="publish">No
                    </label>

                    <p class="help-inline"></p>
                </div>
            </div>

            <? // Set Expiry Dates ?>
            <div class="form-group" id="expiry">
                <label class="col-sm-3 control-label" for="expiry">Expiry</label>

                <div class="btn-group col-sm-9" data-toggle="buttons" id="expiry_date">
                    <label class="btn btn-default<?= (is_null($survey->publish) OR $survey->expiry == '1') ? ' active' : '' ?>">
                        <input type="radio"<?= (is_null($survey->publish) OR $survey->expiry == '1') ? ' checked' : '' ?>  value="1" name="expiry" id="expiry_date_yes">Yes
                    </label>
                    <label class="btn btn-default<?=$survey->expiry == '0' ? ' active' : '' ?>" >
                        <input type="radio"<?= $survey->expiry == '0' ? ' checked' : '' ?>  value="0" name="expiry" id="expiry_date_no">No
                    </label>

                    <p class="help-inline"></p>
                </div>
            </div>

            <? // Select Start End Date ?>
            <div class="form-group" id="set_expiry_date">
                <label class="col-sm-3 control-label" for="start_date">Start Date</label>

                <div class="col-sm-2">
                    <input type="text" class="form-control datepicker" id="start_date" name="start_date"
                           value="<?= (! is_null($survey->start_date)) ? date("d-m-Y",strtotime($survey->start_date)):''; ?>"/>
                </div>
                <label class="col-sm-2 control-label" for="end_date">End Date</label>

                <div class="col-sm-2">
                    <input type="text" class="form-control datepicker" id="end_date" name="end_date"
                           value="<?= (! is_null($survey->end_date)) ?  date("d-m-Y",strtotime($survey->end_date)):''; ?>"/>
                </div>
            </div>

            <? // Store Answers ?>
            <div class="form-group" id="store_answer">
                <label class="col-sm-3 control-label" for="store_answer">Store Answers</label>

                <div class="btn-group col-sm-9" data-toggle="buttons">
                    <label class="btn btn-default<?= $survey->store_answer == '1' ? ' active' : '' ?>">
                        <input type="radio"<?= $survey->store_answer == '1' ? ' checked' : '' ?>  value="1" name="store_answer">Yes
                    </label>
                    <label class="btn btn-default<?= (is_null($survey->publish) OR $survey->store_answer == '0') ? ' active' : '' ?>">
                        <input type="radio"<?= (is_null($survey->publish) OR $survey->store_answer == '0') ? ' checked' : '' ?>  value="0" name="store_answer">No
                    </label>

                    <p class="help-inline"></p>
                </div>
            </div>

            <input type="hidden" id="documents" value="<?= $documents?>">
            <? // Download PDF ?>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="result_pdf_download">Download PDF</label>

                <div class="btn-group col-sm-9" data-toggle="buttons" id="set_download">
                    <label class="btn btn-default<?= ($documents == 1 AND (is_null($survey->publish) OR $survey->result_pdf_download == '1')) ? ' active' : '' ?>">
                        <input type="radio"<?= ($documents == 1 AND (is_null($survey->publish) OR $survey->result_pdf_download == '1'))  ? ' checked' : '' ?>  value="1"
                               name="result_pdf_download" id="set_download_yes">Yes
                    </label>
                    <label class="btn btn-default<?= $survey->result_pdf_download == '0' ? ' active' : '' ?>">
                        <input type="radio"<?= $survey->result_pdf_download == '0' ? ' checked' : '' ?>  value="0"
                               name="result_pdf_download"  id="set_download_no">No
                    </label>

                    <p class="help-inline"></p>
                </div>
            </div>

            <? // Select Template?>
            <div class="form-group" id="select_template_download">
                <label class="col-sm-3 control-label" for="result_template_id">Document Template</label>

                <div class="col-sm-9">
                    <select class="form-control" id="result_template_id" name="result_template_id">
                        <option value="">Please Select a template</option>
                        <?php foreach ($templates as $template):
                            $selected = ($survey->result_template_id == $template['id']) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?= $template['id'] ?>"<?= $selected ?>><?= $template['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <? // Display thank you page ?>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="display_thank_you">Display thank you page</label>

                <div class="btn-group col-sm-9" data-toggle="buttons" id="display_thank_you">
                    <label class="btn btn-default<?= $survey->display_thank_you == '1' ? ' active' : '' ?>">
                        <input type="radio"<?= $survey->display_thank_you == '1' ? ' checked' : '' ?>  value="1" name="display_thank_you">Yes
                    </label>
                    <label class="btn btn-default<?= (is_null($survey->display_thank_you) OR $survey->display_thank_you == '0') ? ' active' : '' ?>">
                        <input type="radio"<?= (is_null($survey->display_thank_you) OR $survey->display_thank_you == '0') ? ' checked' : '' ?>  value="0" name="display_thank_you">No
                    </label>

                    <p class="help-inline"></p>
                </div>
            </div>

            <? // Select thank you page?>
            <div class="form-group" id="select_page_id">
                <label class="col-sm-3 control-label" for="edit_survey_thank_you_page_id">Thank you page</label>

                <div class="col-sm-6">
                    <select class="form-control" id="edit_survey_thank_you_page_id" name="thank_you_page_id">
                        <option value="">Please select a page</option>
                        <?php foreach ($pages as $page):
                            $selected = ($survey->thank_you_page_id == $page['id']) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?= $page['id'] ?>"<?= $selected ?>><?= $page['title'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group" id="is_backend-group">
                <label class="col-sm-3 control-label" for="is_backend">Backend Survey</label>

                <div class="btn-group col-sm-9" data-toggle="buttons" id="is_backend">
                    <label class="btn btn-default<?= (!is_null($survey->is_backend) && $survey->is_backend == '1') ? ' active' : '' ?>">
                        <input type="radio"<?= (!is_null($survey->is_backend) && $survey->is_backend == '1') ? ' checked' : '' ?>
                               value="1" name="is_backend" id="is_backend_yes">Yes
                    </label>
                    <label class="btn btn-default<?= (is_null($survey->is_backend) || $survey->is_backend == '0') ? ' active' : '' ?>">
                        <input type="radio"<?= (is_null($survey->is_backend) || $survey->is_backend == '0') ? ' active' : '' ?> value="0" name="is_backend"
                               id="is_backend_no">No
                    </label>

                    <p class="help-inline"></p>
                </div>
            </div>
            <div class="form-group" id="link_course_group">
                <label class="col-sm-3 control-label" for="link-course">Course</label>

                <div class="col-sm-9" data-toggle="buttons" id="link-course">
                    <input class="form-control enforce_ucfirst ui-autocomplete-input" type="text" id="link_course_name"
                           name="link-course" value="<?= ($course_info != NULL) ? "{$course_info['id']} - {$course_info['title']}" : ''?>" placeholder="Type to select">
                    <input type="hidden" id="course_id" name="course_id" value="<?= $course_info['id'] ?>">
                </div>
            </div>
            <div class="form-group" id="link_subcontact_group">
                <label class="col-sm-3 control-label" for="link-contact">Contact Type</label>
                <div class="col-sm-6">
                    <select class="form-control" id="contact_id" name="contact_id">
                        <?php foreach ($contact_types as $key => $contact_type) {
                            $selected = (isset($survey->contact_id) && $survey->contact_id == $contact_type['id']) ? ' selected="selected" ' : '';
                            ?>
                            <option value="<?= $contact_type['contact_type_id'] ?>" <?= $selected ?>><?= $contact_type['display_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

        </div>

        <? // Add Questions Tab ?>
        <div class="col-sm-12 tab-pane" id="questions_tab">
            <? // Pagination ?>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="pagination">Display Per Group</label>

                <div class="btn-group col-sm-9" data-toggle="buttons" id="set_pagination">
                    <label class="btn btn-default<?=$survey->pagination === '1' ? ' active' : '' ?>">
                        <input type="radio"<?= (! is_null($survey->pagination) OR $survey->pagination === '1') ? ' checked' : '' ?>  value="1" name="pagination" id="set_pagination_yes">On
                    </label>
                    <label class="btn btn-default<?= (is_null($survey->pagination) OR  $survey->pagination === '0' ) ? ' active' : '' ?>">
                        <input type="radio" <?=(is_null($survey->pagination) OR  $survey->pagination === '0' ) ? ' checked' : '' ?>  value="0" name="pagination" id="set_pagination_no">Off
                    </label>

                    <p class="help-inline"></p>
                </div>
            </div>
            <? // Select Question to add?>
            <div class="form-group">
                <div id="question_already_selected" class="alert alert-warning"><a class="close"
                                                                                data-dismiss="alert">&times;</a>Question Already in the list.
                </div>
                <label class="col-sm-2 control-label" for="list_questions">Questions</label>

                <div class="col-sm-5">
                    <select class="form-control"  id="list_questions">
                        <option value="">Please Select Question</option>
                        <?php foreach ($question_grp as $ques): ?>
                            <option value="<?= $ques['id']; ?>"
                                    data-name="<?= $ques['title'] ?>" data-grp="<?= $ques['grpid'] ?>" data-answer="<?= $ques['anstitle'] ?>"><?= $ques['title'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
               <!--<div class="col-sm-3 question_group">
                   <select class="form-control" id="list_groups">
                       <option value="">No Group</option>
                      <?php foreach ($groups as $group): ?>
                           <option value="<?=$group->id?>" data-name="<?=$group->title?>"><? $group->title?></option>
                      <?php endforeach; ?>
                   </select>
               </div>-->
                <div class="col-sm-2">
                    <button type="button" class="btn" id="add_question_btn">Add</button>
                </div>
            </div>

            <? // List selected Questions ?>
            <table id="questions_list_table" class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">Order</th>
                    <th scope="col">Title</th>
                    <th scope="col">Answer</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody class="sortable-tbody">
                <?php
                $questions_list_ids = array();
                $groups_ids = array();
                $current_group = -1 ;
                foreach ($question_list as $key => $question):
                    $questions_list_ids[] = $question->id;
                    $current = $current_group == $question->group_id ;
                    $current_group = $question->group_id;
                    $group = '';
                    if ( ! $current) {
                        foreach ($groups as $g)
                        {
                            if ($g->id == $question->group_id)
                            {
                                $group = $g->title;
                                $groups_ids[] = $question->group_id;
                            }
                        }
                    }
                    ?>
                    <? // Set the Undefined Group as first row ?>
                    <?php if($key == 0):?>
                    <?php $groups_ids[]= '0';?>
                        <tr class="question_group" data-id="" data-question_id="" data-group_id="0" data-group_row="true">
                            <td title="<?= __('Drag to reorder') ?>"><span class="icon-bars"></span></td>
                            <td colspan="3"><h3>No Group</h3></td>
                        </tr>
                    <?php endif; ?>

                    <? // Set a group Row ?>
                    <?php if(! $current AND $question->group_id != 0):?>
                        <tr class="question_group" data-id="" data-question_id="" data-group_id="<?= $question->group_id ?>" data-group_row="true">
                            <td title="<?= __('Drag to reorder') ?>"><span class="icon-bars"></span></td>
                            <td colspan="3"><h3><?= $group ?></h3></td>
                        </tr>
                    <? // Add the questions in the group ?>
                    <?php endif; ?>
                    <tr data-id="<?= $question->id ?>" data-question_id="<?= $question->question_id ?>" data-group_id="<?= $question->group_id ?>"  data-group_row="false">
                        <td title="<?= __('Drag to reorder') ?>"><span class="icon-bars"></span></td>
                        <td class="question_title">
                            <?php foreach ($questions as $q)
                            {
                                echo $q->id == $question->question_id ? $q->title : '';
                            }
                            ?>
                        </td>
                        <td>
                            <?php foreach ($questions as $q)
                            {
                                echo $q->id == $question->question_id ? $q->answer->title : '';
                            }
                            ?>
                        </td>
                        <td class="remove-row">
							<button type="button" class="btn-link remove-button"><span class="icon-remove"></span> Remove</button>
                        </td>
                    </tr>

                <?php endforeach; ?>

                <? // Add remaining groups ?>
                 <? if ( ! in_array('0',$groups_ids)): ?>
                        <tr class="question_group" data-id="" data-question_id="" data-group_id="0" data-group_row="true">
                            <td title="<?= __('Drag to reorder') ?>"><span class="icon-bars"></span></td>
                            <td colspan="3"><h3>No Group</h3></td>
                        </tr>
                  <?php endif; ?>
                <?php foreach ($groups as $k=>$g):
                    if ( ! in_array($g->id,$groups_ids)):
                    ?>
                        <tr class="question_group" data-id="" data-question_id="" data-group_id="<?= $g->id ?>" data-group_row="true">
                            <td title="<?= __('Drag to reorder') ?>"><span class="icon-bars"></span></td>
                            <td colspan="3"><h3><?= $g->title ?></h3></td>
                        </tr>
                        <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>

            <? // List of Question ?>
            <input type="hidden" id="questions_ids" name="questions_ids" value="<?= serialize($questions_list_ids); ?>">
        </div>

        <? // Redirects ?>
        <div class="col-sm-12 tab-pane" id="redirects_tab">
            <input type="hidden" id="sequence_id" name="sequence_id" value="<?= $sequence->id; ?>">
            <div id="questionnaire_sequences"></div>
            <input type="hidden" id="sequence_list" name="sequence_list" value="">
        </div>

        <? // Preview Tab ?>
        <div id="preview_tab" class="col-sm-9 tab-pane">

        </div>
    </div>

    <? // Action Buttons ?>
    <input type="hidden" id="save_exit" name="save_exit" value="false"/>

    <div id="action_buttons" class="col-sm-12">
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

<div class="modal fade manage-question-group-modal" id="manage-question-group-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">Manage group</h2>
            </div>
            <div class="modal-body">
                <input type="hidden" id="manage-question-group-question-id" value=""/>
                <input type="hidden" id="manage-question-group-group-id" value=""/>
                <h3 class="col-sm-12" id="manage-question-group-question-name"></h3>
                <hr>
                <div class="manage-question-group-lists clearfix">
                    <h4 class="col-sm-6">All groups</h4>
                    <h4 class="col-sm-6">Question Group</h4>
                    <div class="col-sm-6">
                        <ul class="manage-question-group-list" id="manage-question-group-excluded">
                            <?php foreach ($groups as $count=>$group): ?>
                                <li class="btn btn-default btn-block group-button"
                                    data-id="<?= $group->id ?>"
                                    role="button"
                                    tabindex='0"'><?= $group->title ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <input type="hidden" id="groups_count" value="<?= $count ?>">
                    <div class="col-sm-6">
                        <ul class="manage-question-group-list" id="manage-question-group-included">
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="manage-question-group-save">Save changes</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
