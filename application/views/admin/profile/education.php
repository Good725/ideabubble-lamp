<?php if (isset($alert)): ?>
    <?= $alert ?>
    <script>remove_popbox();</script>
<?php endif; ?>

<div class="edit_profile_wrapper">
    <form class="form-horizontal" id="edit-profile-form" action="/admin/profile/save?section=education" method="post">
        <?= $section_switcher ?>

        <input type="hidden" name="contact_id" value="<?= $contact3->get_id() ?>" />
        <?php foreach($notifications as $notification) { ?>
            <input name="contactdetail_id[]" type="hidden" value="<?= $notification['id'] ?>" />
            <input name="contactdetail_type_id[]" type="hidden" value="<?= $notification['type_id'] ?>" />
            <input name="contactdetail_value[]" type="hidden" value="<?= $notification['value'] ?>" />
        <?php } ?>

        <section>
            <h3>1. <?= __('Education details') ?></h3>

            <div>
                <div class="form-group">
                    <div class="col-sm-6">
                        <?php
                        $options = array('' => '');
                        foreach($academic_years as $academic_year) {
                            $options[$academic_year['id']] = $academic_year['title'];
                        }
                        $academic_year_options = $options;
                        echo Form::ib_select(__('Academic year'), 'academic_year_id', $options, $contact3->get_academic_year_id(), array('id' => 'academic_year_id'));
                        ?>
                    </div>

                    <div class="col-sm-6">
                        <?= Form::ib_checkbox_switch(__('Flexi student') , 'flexi_student', '1', $contact3->get_is_flexi_student()); ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6">
                        <?php
                        $options = array('' => '');
                        foreach ($schools as $school) {
                            $options[$school['id']] = htmlentities($school['name'].(!empty($school['address1']) ? ' - '.$school['address1'] : ''));
                        }
                        $selected = ($contact3->get_school_id() == 0) ? '' : $contact3->get_school_id();
                        $attributes = array('class' => 'ib-combobox', 'id' => 'contact_school_id');
                        echo Form::ib_select(__('School'), 'school_id', $options, $selected, $attributes);
                        ?>
                    </div>

                    <div class="col-sm-6">
                        <?php
                        $options = array();
                        foreach ($years as $year) {
                            $options[$year['id']] = $year['year'];
                        }
                        // Don't require this for school year
                        $class_attr = (Settings::instance()->get('cms_platform') !== 'training_company') ? 'validate[funcCall[validate_school_year]]' : '';
                        $attributes = array('class' => $class_attr, 'id' => 'contact_year_id');
                        echo Form::ib_select(__('School year'), 'year_id', $options, $contact3->get_year_id(), $attributes);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6">
                        <?= Form::ib_input(__('Courses I would like'), 'courses_i_would_like', $contact3->get_courses_i_would_like()); ?>
                    </div>

                    <div class="col-sm-6">
                        <?= Form::ib_input(__('Points required'), 'points_required', $contact3->get_points_required()); ?>
                    </div>
                </div>

                <h4><?= __('Download report card') ?></h4>

                <div class="form-group">
                    <div class="col-sm-4">
                        <?= Form::ib_select(__('Select year'), null, $academic_year_options, $contact3->get_academic_year_id(), ['id' => 'profile-report_card-academic_year']); ?>
                    </div>
                    <div class="col-sm-4">
                        <?php
                        $args = [
                            'multiselect_options' => [
                                'enableCaseInsensitiveFiltering' => true,
                                'enableClickableOptGroups' => true,
                                'enableFiltering' => true,
                                'includeSelectAllOption' => true,
                                'maxHeight' => 460,
                                'numberDisplayed' => 1,
                                'selectAllText' => __('ALL')
                            ]
                        ];
                        $todo_categories    = new Model_Todo_Category();
                        $todo_categories = $todo_categories->where_academic_year($contact3->get_academic_year_id())
                            ->where_student_has_todo($contact3->get_id())->where('todo.results_published_datetime',
                                '<', DB::expr('NOW()'))->find_all_undeleted()->as_array('id', 'title');
                        echo Form::ib_select(__('Select exams'), 'todo_categories[]', $todo_categories,
                            null, array('class' => 'multiple_select todo_categories', 'multiple' => 'multiple'), $args);?>
                    </div>
                    <div class="col-sm-2">
                        <button type="button"
                            class="btn form-btn btn-primary"
                            id="profile-report_card-download"
                            ><?= __('Download') ?></button>
                    </div>
                </div>

                <h3>2. <?= __('Subjects and levels') ?></h3>

                <div class="form-group vertically_center">
                    <div class="col-xs-12 col-sm-2"><?= __('Cycle') ?></div>

                    <div class="col-xs-12 col-sm-10">
                        <?php
                        $options = array('Junior' => __('Junior'), 'Transition' => __('Transition'), 'Senior' => __('Senior'));
                        echo Form::btn_options('cycle', $options, $contact3->get_cycle(), false, array(), array('class' => 'stay_inline', 'id' => 'profile-education-cycle'));
                        ?>
                    </div>
                </div>

                <div class="form-row gutters">
                    <?php for ($i = 0; $i < count($subjects); $i++): ?>
                        <?php if (isset($subjects[$i])): ?>
                            <?php
                            $subject = $subjects[$i];
                            $checked = false;
                            foreach ($subject_preferences as $subject_preference) {
                                if ($subject['id'] == $subject_preference['subject_id']) {
                                    $checked = true;
                                    break;
                                }
                            }
                            if (!$checked) {
                                $subject_preference = null;
                            }
                            ?>

                            <div class="profile-education-subject-wrapper col-sm-6<?= $subject['cycle'] == $contact3->get_cycle() ? '' : ' hidden' ?>" data-cycle="<?= $subject['cycle'] ?>">
                                <div class="profile-education-subject_and_level row gutters vertically_center">
                                    <div class="col-xs-6">
                                        <label class="checkbox-icon btn--full form-action-group profile-education-subject">
                                            <input type="hidden"   name="subject_preferences[<?= $i ?>][subject_id]" value="0" />
                                            <input type="checkbox" name="subject_preferences[<?= $i ?>][subject_id]" value="<?= $subject['id'] ?>"<?= $checked ? ' checked="checked"' : '' ?> />
                                            <span class="checkbox-icon-unchecked form-btn btn btn--full" title="Click to add">
                                                <span class="profile-education-subject-icon icon-plus"></span> <?= $subject['name'] ?>
                                            </span>

                                            <span class="checkbox-icon-checked   form-btn btn btn-primary btn--full" title="Click to remove">
                                                <span class="profile-education-subject-icon icon-check"></span> <?= $subject['name'] ?>
                                            </span>
                                        </label>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="btn-group profile-education-subject_preferences<?= $checked ? '' : ' invisible' ?>" data-toggle="buttons" id="subject_preferences[<?= $i ?>][level]">
                                            <?php foreach ($levels as $level): ?>
                                                <?php $checked = @$subject_preference['level_id'] == $level['id']; ?>
                                                <label class="btn btn-default<?= ($checked) ? ' active' : '' ?>">
                                                    <input type="radio"<?= ($checked) ? ' checked="checked"' : '' ?> value="<?=$level['id']?>" name="subject_preferences[<?= $i ?>][level_id]" id="subject_preferences[<?= $i ?>][level_id]" /><?= $level['level'][0] ?>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>
        </section>

        <section>
            <div class="form-action-group" id="ActionMenu">
                <button type="submit" name="redirect" class="btn btn-primary profile_save_btn" data-redirect="save" value="save"><?=__('Save')?></button>
                <button type="submit" name="redirect" class="btn btn-primary profile_save_btn" data-redirect="save_and_exit" value="save_and_exit"><?=__('Save & Exit')?></button>
                <button type="reset" class="btn btn-default"><?=__('Reset')?></button>
                <a class="btn btn-cancel" href="/admin">Cancel</a>
            </div>
        </section>
    </form>
</div>

<?php
echo View::factory('snippets/modal')->set([
    'id'     => 'profile-report_card-academic_year-modal',
    'title'  => 'Academic year missing',
    'body'   => 'Please select a year first and at least one exam first.',
    'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>'
]);

?>

<style>
    .profile-education-subject_and_level {
        margin-bottom: 1.25em;
    }

    .profile-education-subject .btn {
        padding-left: 2.5em;
        padding-right: 2.5em;
    }

    .profile-education-subject-icon {
        position: absolute;
        top: 1em;
        left: 2em;
    }
</style>

<script>
    $('.edit_profile_wrapper').on('change', '.profile-education-subject [type="checkbox"]', function() {
        var $levels = $(this).parents('.profile-education-subject_and_level').find('.profile-education-subject_preferences');
        if (this.checked) {
            $levels.removeClass('invisible');
        } else {
            $levels.addClass('invisible')
        }
    });

    $('#profile-education-cycle').find(':radio').on('change', function()
    {
        var cycle = $('#profile-education-cycle').find(':radio:checked').val();

        $('.profile-education-subject-wrapper').addClass('hidden');
        $('.profile-education-subject-wrapper[data-cycle="'+cycle+'"]').removeClass('hidden');
    });

    $('#profile-report_card-download').on('click', function() {
        var academic_year_id = $('#profile-report_card-academic_year').val();
        var todo_categories = $('.todo_categories').val();
        if (academic_year_id && todo_categories !== null) {
            var data = {"academic_year_id" : academic_year_id, "todo_categories" : todo_categories};
            var query = $.param(data);
            window.location = '/admin/documents/ajax_generate_kes_document?direct_download=1&contact_id=<?= $contact_id ?>&document_name=student_report_card&' + query;
        } else {
            $('#profile-report_card-academic_year-modal').modal();
        }
    });
</script>