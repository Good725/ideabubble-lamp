<?php
$is_backend = (Request::current()->directory() == 'admin');
$custom_checkout = Settings::instance()->get('checkout_customization');
?>

<style>
    .profile-education-subject-wrapper {
        margin-bottom: 1em;
    }

    .profile-education-subject .button {
        overflow: hidden;
        padding-left: 2em;
        padding-right: 2em;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .profile-education-subject-icon {
        position: absolute;
        top: 1em;
        left: 2em;
    }
</style>

<?php
if (@$application['student']['photo_id']) {
?>
<div>
    <h2 class="checkout-heading contact-details-heading">
        <span class="fa fa-graduation-cap"></span>
        <?= __('Student') ?>
    </h2>
    <div class="theme-form-content">
        <div class="theme-form-inner-content">
            <h3><?= __('Photo') ?></h3>
        </div>
        <img src="/admin/files/download_file?file_id=<?=$application['student']['photo_id']?>" style="max-width: 99%;" />
    </div>
</div>
<?php
}
?>

<div>
    <?php if (!$is_backend): ?>
        <h2 class="checkout-heading contact-details-heading">
            <span class="fa fa-graduation-cap"></span>
            <?= __('Education') ?>
        </h2>
    <?php endif; ?>

    <div class="theme-form-content">
        <div class="theme-form-inner-content">
            <?php if (in_array($custom_checkout, ['bc_language', 'sls'])): ?>
                <h3><?= __('Previous Education') ?></h3>

                <div class="form-group">
                    <div class="col-sm-6">
                        <?= Form::ib_input('Current school/college', 'current_school', @$extra_data['current_school']); ?>
                    </div>

                    <?php if ($custom_checkout == 'sls'): ?>
                        <div class="col-sm-6">
                            <?php
                            $options = [
                                '' => '',
                                'poor' => 'Poor',
                                'fair' => 'Fair',
                                'good' => 'Good',
                                'very good' => 'Very good',
                                'excellent' => 'Excellent'
                            ];
                            echo Form::ib_select('Level of English', 'level_of_english', $options, @$extra_data['level_of_english']);
                            ?>
                        </div>
                    <?php endif; ?>
                </div>

            <?php elseif (in_array($custom_checkout, ['bcfe'])): ?>
                <?php ob_start(); ?>
                    <h3><?= __('Previous Education') ?></h3>

                    <div class="form-group">
                        <div class="<?= $is_backend ? 'col-sm-12 form-row' : 'col-sm-6' ?>">
                            <label for="checkout-current_School'"><?= __('Current school/college') ?></label>
                            <?= Form::ib_input(null, 'current_school', @$application['data']['current_school'], ['id' => 'checkout-current_School']); ?>
                        </div>

                        <div class="<?= $is_backend ? 'col-sm-12' : 'col-sm-6' ?>">
                            <label for="checkout-current_school'"><?= __('School roll no.') ?></label>
                            <?= Form::ib_input(null, 'school_roll_number', @$application['data']['school_roll_number'], ['id' => 'checkout-current_school']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="<?= $is_backend ? 'col-sm-12 form-row' : 'col-sm-6' ?>">
                            <label for="checkout-leaving_cert'"><?= __('Leaving Certificate') ?></label>
                            <?php
                            $options = array('' => '', 'Leaving Certificate' => 'Leaving Certificate', 'LC vocational' => 'LC vocational', 'LC applied' => 'LC applied');
                            echo Form::ib_select(null, 'leaving_cert_type', $options, @$application['data']['leaving_cert_type'], ['id' => 'checkout-leaving_cert']);
                            ?>
                        </div>

                        <div class="<?= $is_backend ? 'col-sm-12' : 'col-sm-6' ?>">
                            <label for="checkout-year'"><?= __('Year') ?></label>
                            <?= Form::ib_input('Year', 'year', @$application['data']['year'], ['id' => 'checkout-year']) ?>
                        </div>
                    </div>

                    <p><?= __('Subjects') ?></p>

                    <div class="hidden" id="checkout-subject-template">
                        <div class="form-group<?= $is_backend ? ' gutters gutters--narrow' : '' ?>">
                            <div class="<?= $is_backend ? 'col-sm-4' : 'col-sm-6' ?>">
                                <?php
                                $label = $is_backend ? 'Subject' : 'Subject name';
                                echo Form::ib_input($is_backend, null, null, ['class' => 'checkout-subject-name'])
                                ?>
                            </div>

                            <div class="<?= $is_backend ? 'col-xs-6 col-sm-4' : 'col-sm-3' ?>">
                                <?php
                                $level_options = array('' => '', 'higher' => 'Higher', 'ordinary' => 'Ordinary', 'foundation' => 'Foundation', 'n/a' => 'N/A');
                                echo Form::ib_select('Level', null, $level_options, null, array('class' => 'checkout-subject-level'));
                                ?>
                            </div>

                            <div class="<?= $is_backend ? 'col-xs-6 col-sm-4' : 'col-sm-3' ?>">
                                <?php
                                $grade_options = [
                                    '' => '',
                                    '2017 - present' => ['H1' => 'H1', 'H2' => 'H2', 'H3' => 'H3', 'H4' => 'H4', 'H5' => 'H5', 'H6' => 'H6', 'H7' => 'H7', 'H8' => 'H8', 'O1' => 'O1', 'O2' => 'O2', 'O3' => 'O3', 'O4' => 'O4', 'O5' => 'O5', 'O6' => 'O6', 'O7' => 'O7', 'O8' => 'O8'],
                                    '1992 - 2016'    => ['A1' => 'A1', 'A2' => 'A2', 'B1' => 'B1', 'B2' => 'B2', 'B3' => 'B3', 'C1' => 'C1', 'C2' => 'C2', 'C3' => 'C3', 'D1' => 'D1', 'D2' => 'D2', 'D3' => 'D3', 'E' => 'E', 'F' => 'F', 'NG' => 'NG'],
                                    'LCVP'           => ['Distinction' => 'Distinction', 'Merit' => 'Merit', 'Pass' => 'Pass'],
                                    'Other'          => ['n/a' => 'N/A']
                                ];
                                echo Form::ib_select('Grade', null, $grade_options, null, array('class' => 'checkout-subject-grade'));
                                ?>
                            </div>
                        </div>
                    </div>

                    <div id="checkout-subjects">
                    <?php
                    if (@$application['data']['subjects']) {
                        $subjectx = array_values($application['data']['subjects']);
                        foreach ($subjectx as $i => $subject) {
                    ?>
                            <div class="form-group<?= $is_backend ? ' gutters gutters--narrow' : '' ?>">
                                <div class="<?= $is_backend ? 'col-sm-4' : 'col-sm-6' ?>">
                                    <?php
                                    $label = $is_backend ? 'Subject' : 'Subject name';
                                    echo Form::ib_input($label, 'subjects[' . $i . '][name]', $subject['name'],
                                        array('class' => 'checkout-subject-name')) ?>
                                </div>

                                <div class="<?= $is_backend ? 'col-xs-6 col-sm-4' : 'col-sm-3' ?>">
                                    <?php
                                    $level_options = array(
                                        '' => '',
                                        'higher' => 'Higher',
                                        'ordinary' => 'Ordinary',
                                        'foundation' => 'Foundation',
                                        'n/a' => 'N/A'
                                    );
                                    echo Form::ib_select('Level', 'subjects[' . $i . '][level]', $level_options,
                                        $subject['level'], array('class' => 'checkout-subject-level'));
                                    ?>
                                </div>

                                <div class="<?= $is_backend ? 'col-xs-6 col-sm-4' : 'col-sm-3' ?>">
                                    <?php
                                    $grade_options = [
                                        '' => '',
                                        '2017 - present' => [
                                            'H1' => 'H1',
                                            'H2' => 'H2',
                                            'H3' => 'H3',
                                            'H4' => 'H4',
                                            'H5' => 'H5',
                                            'H6' => 'H6',
                                            'H7' => 'H7',
                                            'H8' => 'H8',
                                            'O1' => 'O1',
                                            'O2' => 'O2',
                                            'O3' => 'O3',
                                            'O4' => 'O4',
                                            'O5' => 'O5',
                                            'O6' => 'O6',
                                            'O7' => 'O7',
                                            'O8' => 'O8'
                                        ],
                                        '1992 - 2016' => [
                                            'A1' => 'A1',
                                            'A2' => 'A2',
                                            'B1' => 'B1',
                                            'B2' => 'B2',
                                            'B3' => 'B3',
                                            'C1' => 'C1',
                                            'C2' => 'C2',
                                            'C3' => 'C3',
                                            'D1' => 'D1',
                                            'D2' => 'D2',
                                            'D3' => 'D3',
                                            'E' => 'E',
                                            'F' => 'F',
                                            'NG' => 'NG'
                                        ],
                                        'LCVP' => ['Distinction' => 'Distinction', 'Merit' => 'Merit', 'Pass' => 'Pass'],
                                        'Other' => ['n/a' => 'N/A']
                                    ];

                                    $value = isset($subject['grade']) ? $subject['grade'] : null;
                                    echo Form::ib_select('Grade', 'subjects[' . $i . '][grade]', $grade_options, $value,
                                        array('class' => 'checkout-subject-grade'));
                                    ?>
                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 text-right">
                            <button type="button" class="button btn btn-success btn-lg" id="checkout-subjects-add" data-count="1"><?= __('Add another') ?></button>
                        </div>
                    </div>

                    <h3><?= __('College and career details') ?></h3>

                    <div class="form-group">
                        <div class="<?= $is_backend ? 'col-sm-12 form-row' : 'col-sm-6' ?>">
                            <?= Form::ib_input('Last college attended', 'last_college_attended', @$application['data']['last_college_attended']) ?>
                        </div>

                        <div class="<?= $is_backend ? 'col-sm-12' : 'col-sm-6' ?>">
                            <?= Form::ib_input('Course taken', 'college_course_taken', @$application['data']['college_course_taken']) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="<?= $is_backend ? 'col-sm-12 form-row' : 'col-sm-6' ?>">
                            <?php
                            $current_year = date('Y');
                            $year_options = array('' => '');
                            for ($year = $current_year; $year > $current_year - 60; $year--) {
                                $year_options[$year] = $year;
                            }

                            echo Form::ib_select('Year of entry', 'college_entry_year', $year_options, @$application['data']['college_entry_year']) ?>
                        </div>

                        <div class="<?= $is_backend ? 'col-sm-12' : 'col-sm-6' ?>">
                            <?= Form::ib_select('Year of leaving', 'college_leaving_year', $year_options, @$application['data']['college_leaving_year']) ?>
                        </div>
                    </div>

                    <h3><?= __('Work experience') ?></h3>

                    <div class="hidden" id="checkout-work_experience-template">
                        <div class="form-group">
                            <div class="<?= $is_backend ? 'col-sm-6 form-row' : 'col-sm-3' ?>">
                                <?= Form::ib_select('Year', null, $year_options, null, array('class' => 'checkout-work_experience-year')) ?>
                            </div>

                            <div class="<?= $is_backend ? 'col-sm-12' : 'col-sm-9' ?>">
                                <?= Form::ib_input('Details', null, null, array('class' => 'checkout-work_experience-details')) ?>
                            </div>
                        </div>
                    </div>

                    <div id="checkout-work_experience">
                    <?php
                    if (@$application['data']['work_experience']) {
                        $work_experiences = array_values($application['data']['work_experience']);
                        foreach ($work_experiences as $i => $work_experience) {
                    ?>
                        <div class="form-group">
                            <div class="<?= $is_backend ? 'col-sm-6 form-row' : 'col-sm-3' ?>">
                                <?= Form::ib_select('Year', 'work_experience[' . $i . '][year]', $year_options,
                                    $work_experience['year'], array('class' => 'checkout-work_experience-year')) ?>
                            </div>

                            <div class="<?= $is_backend ? 'col-sm-12' : 'col-sm-9' ?>">
                                <?= Form::ib_input('Details', 'work_experience[' . $i . '][details]',
                                    $work_experience['details'], array('class' => 'checkout-work_experience-details')) ?>
                            </div>
                        </div>

                        <?php if ($is_backend): ?>
                            <hr />
                        <?php endif; ?>
                    <?php
                        }
                    }
                    ?>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 text-right">
                            <button type="button" class="button btn btn-success btn-lg" id="checkout-work_experience-add" data-count="0"><?= __('Add more') ?></button>
                        </div>
                    </div>
                <?php $education_section = ob_get_clean(); ?>

                <?php ob_start(); ?>
                    <h3><?= __('Other') ?></h3>

                    <div class="form-group">
                        <div class="<?= $is_backend ? 'col-sm-12' : 'col-sm-6' ?>">
                            <label for="checkout-certificates_other"><?= __('Other certificates') ?></label>
                            <?= Form::ib_textarea(null, 'certificates_other', @$application['data']['certificates_other'], array('id' => 'checkout-certificates_other', 'rows' => 3)) ?>
                        </div>

                        <div class="<?= $is_backend ? 'col-sm-12' : 'col-sm-6' ?>">
                            <label for="checkout-leisure_activities"><?= __('Leisure activities (optional)') ?></label>
                            <?= Form::ib_textarea(null, 'leisure_activities', @$application['data']['leisure_activities'], array('id' => 'leisure_activities', 'rows' => 3)) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="checkout-education-other_info">
                                <?=__('Other relevant information') ?>
                            </label>

                            <?= Form::ib_textarea(null, 'application[other]', @$application['data']['application']['other'], array('id' => 'checkout-education-other_info')); ?>
                        </div>
                    </div>

                    <?php if (!$is_backend): ?>
                        <h3><?= __('Special needs') ?></h3>
                    <?php endif; ?>

                    <div class="form-group vertically_center">
                        <?php if ($is_backend): ?>
                            <div class="col-xs-7 col-sm-5">
                                <h3><?= __('Special needs') ?></h3>
                            </div>
                        <?php endif; ?>

                        <div class="<?= $is_backend ? 'col-xs-5' : 'col-xs-5 col-sm-3' ?>">
                            <?php
                            $options = array('1' => __('Yes'), '0' => __('No'));
                            $attributes = array(
                                'class' => 'stay_inline',
                                'data-form-show-option' => 1,
                                'data-target' => '#checkout-special_needs-details-wrapper',
                                'style' => 'margin-bottom: 0;'
                            );
                            $input_attributes = array('class' => 'checkout-has_special_needs-option');
                            echo Form::btn_options('has_special_needs', $options, 0, @$application['data']['special_needs_details'] == 1, $input_attributes, $attributes);
                            ?>
                        </div>

                        <?php if (!$is_backend): ?>
                            <div class="col-xs-7 col-sm-9"><?= __('This does not affect your application.') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group hidden" id="checkout-special_needs-details-wrapper">
                        <div class="col-sm-12">
                            <?= Form::ib_textarea('Details', 'special_needs_details', @$application['data']['special_needs_details'], array('rows' => '3')) ?>
                        </div>
                    </div>
                <?php $other_section = ob_get_clean(); ?>

                <?php if ($is_backend): ?>
                    <h2 style="border: 1px solid; font-size: 1.25em; margin: .5em 0; padding: .5em;">Application <?= isset($application) && !empty($application['courses']) ? ' - '.htmlspecialchars($application['courses'][0]['title']) : '' ?></h2>

                    <div class="row gutters">
                        <div class="col-md-6">
                            <div>
                                <h3><?= __('Student profile') ?></h3>

                                <div class="form-group">
                                    <div class="col-sm-12"><label for="checkout-student-pps"><?= __('PPS No') ?></label></div>

                                    <div class="col-sm-12">
                                        <?php
                                        $value = htmlspecialchars(@$application['data']['student']['pps']);
                                        $attributes = ['id' => 'checkout-student-pps'];
                                        echo Form::ib_input(null, 'student[pps]', $value, $attributes);
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12"><label for="checkout-student-dob"><?= __('Date of birth') ?></label></div>

                                    <div class="col-sm-12">
                                        <?php
                                        $value = htmlspecialchars(@$application['data']['student']['dob']);
                                        $attributes = ['id' => 'checkout-student-dob', 'class' => 'form-datepicker dob'];
                                        $args = ['icon_right' => '<span class="flaticon-calendar"></span>'];
                                        echo Form::ib_datepicker(null, 'student[dob]', $value, array(), $attributes, $args);
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12"><label for="checkout-student-gender"><?= __('Gender') ?></label></div>

                                    <div class="col-sm-12">
                                        <?php
                                        $options    = ['' => '', 'M' => __('Male'), 'F' => __('Female')];
                                        $value      = htmlspecialchars(@$application['data']['student']['gender']);
                                        $attributes = ['id' => 'checkout-profile-student-gender'];
                                        echo Form::ib_select(null, 'student[gender]', $options, $value, $attributes);
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12"><label for="checkout-student-nationality"><?= __('Nationality') ?></label></div>

                                    <div class="col-sm-12">
                                        <?php
                                        $nationality_options = Model_Country::$nationalities;
                                        array_unshift($nationality_options, '');
                                        $nationality_options = array_combine($nationality_options, $nationality_options);
                                        $value = htmlspecialchars(@$application['data']['student']['nationality_id']);
                                        $attributes = ['id' => 'checkout-profile-student-nationality'];
                                        echo Form::ib_select(null, 'student[nationality_id]', $nationality_options, $value, $attributes);
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12"><label for="checkout-student-birth_country"><?= __('Country of birth') ?></label></div>

                                    <div class="col-sm-12">
                                        <?php
                                        $country_options = '<option value=""></option>'.html::optionsFromRows('code', 'name', Model_Residence::get_all_countries(), '');
                                        $attributes = ['id' => 'checkout-profile-student-birth_country'];
                                        $value = htmlspecialchars(@$application['data']['student']['birth_country_id']);
                                        echo Form::ib_select(__('Country of birth'), 'student[birth_country_id]', $country_options, $value, $attributes);
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <?= $other_section ?>
                        </div>

                        <div class="col-md-6"><?= $education_section ?></div>
                    </div>
                <?php else: ?>
                    <?php
                    echo $education_section;
                    echo $other_section;
                    ?>
                <?php endif; ?>

                <script>
                    var $checkout_subject_template = $('#checkout-subject-template');
                    $checkout_subject_template.remove();
                    $('#checkout-subjects-add').on('click', function()
                    {
                        var count = $('#checkout-subjects > div').length;
                        $(this).data('count', count + 1);
                        var $clone = $checkout_subject_template.clone();

                        $clone.find('.checkout-subject-name' ).attr('name', 'subjects['+count+'][name]' );
                        $clone.find('.checkout-subject-level').attr('name', 'subjects['+count+'][level]');
                        $clone.find('.checkout-subject-grade').attr('name', 'subjects['+count+'][grade]');

                        $('#checkout-subjects').append($clone.html());
                        $clone.remove();
                    }).trigger('click');

                    $('#checkout-work_experience-add').on('click', function()
                    {
                        var count = $('#checkout-work_experience > div').length;
                        $(this).data('count', count+1);
                        var $clone = $('#checkout-work_experience-template').clone();

                        $clone.find('.checkout-work_experience-year'   ).attr('name', 'work_experience['+count+'][year]' );
                        $clone.find('.checkout-work_experience-details').attr('name', 'work_experience['+count+'][details]');

                        $('#checkout-work_experience').append($clone.html());
                        $clone.remove();
                    }).trigger('click');
                </script>

            <?php elseif (!in_array($custom_checkout, ['bcfe'])): ?>

                <h3><?= __('Previous Education') ?></h3>

                <div class="form-group vertically_center" style="margin-bottom: 0;">
                    <div class="col-xs-12 col-sm-3" style="white-space: nowrap;"><?= __('Years already completed') ?></div>

                    <div class="col-xs-12 col-sm-9">
                        <?php
                        $years = Model_Years::get_all_years();
                        $options = array();
                        $input_names = array();
                        $selected = array();
                        foreach ($years as $year) {

                            $checked = false;
                            if ((bool) @$application['data']['years'][$year['id']]['id']) {
                                $selected[] = $year['id'];
                                $checked = true;
                            }

                            $options[$year['id']] = array(
                                'label' => $year['year'],
                                'name' => 'application[years]['.$year['id'].'][id]'
                            );
                        }
                        $attributes = array(
                            'class'       => 'checkout-years-completed stay_inline',
                            'id'          => 'application-years-completed',
                            'data-repeat' => '#application-years-repeat',
                            'style'       => 'font-size: 11px;'
                        );
                        echo Form::btn_options('application[years][id]', $options, $selected, true, array(), $attributes)
                        ?>
                    </div>
                </div>

                <div class="form-group vertically_center">
                    <div class="col-xs-12 col-sm-3"><?=__('Repeat Year')?></div>

                    <div class="col-xs-12 col-sm-9">
                        <div class="checkout-years-repeated" id="application-years-repeat" style="display: flex; width: 100%;">
                            <?php foreach ($years as $year): ?>
                                <?php
                                $options = array(
                                    'No'  => array('label' => 'No', 'name' => 'application[years]['.$year['id'].'][repeat]'),
                                    'Yes' => array('label' => 'Yes', 'name' => 'application[years]['.$year['id'].'][repeat]'),
                                );
                                $selected   = @$application['data']['years'][$year['id']]['repeat'];
                                $visible    = ((bool) @$application['data']['years'][$year['id']]['id']);
                                $attributes = array('class' => 'stay_inline'.($visible ? '' : ' invisible'), 'style' => 'font-size: 10px');
                                echo Form::btn_options('application[years][id]', $options, $selected, false, array(), $attributes)
                                ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <table class="table--plain <?=@$application['data'] ? '' : 'hidden'?>" id="application_year_details_table">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 5em;"><?= __('Year') ?></th>
                            <th scope="col"><?= __('Academic year') ?></th>
                            <th scope="col" style="min-width: 10em;"><?= __('School') ?></th>
                            <th scope="col"><?= __('Report documents') ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach (Model_Years::get_all_years() as $year): ?>
                            <tr class="<?=@$application['data']['years'][$year['id']] ? '' : 'hidden'?>" data-year_id="<?=$year['id']?>">
                                <td><?= trim(str_replace('Year', '', $year['year'])) ?></td>

                                <td>
                                    <?php
                                    $input_name = 'application[years]['.$year['id'].'][academic_year]';
                                    $options    = '<option value=""></option>'.html::optionsFromRows('id', 'title', Model_AcademicYear::get_all(), @$application['data']['years'][$year['id']]['academic_year']);
                                    $selected   = @$application['data']['years'][$year['id']]['academic_year'];
                                    echo Form::ib_select(null, $input_name, $options, $selected);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    $input_name = 'application[years]['.$year['id'].'][school_id]';
                                    $options = '<option value=""></option>'.html::optionsFromRows('id', 'name', Model_Providers::get_all_schools(), @$application['data']['years'][$year['id']]['school_id']);
                                    echo Form::ib_select(null, $input_name, $options);
                                    ?>
                                </td>

                                <td class="file_id_container">
                                    <input type="hidden" name="application[years][<?=$year['id']?>][file_id]" class="file_id" value="<?=@$application['data']['years'][$year['id']]['file_id']?>" />

                                    <div class="file-upload row no-gutters">
                                        <div class="col-xs-8" style="padding-right: .5em;">
                                            <?= Form::ib_input(null, null, null, array('class' => 'file-upload-filename', 'readonly' => 'readonly')); ?>

                                            <?php
                                            if (@$application['data']['years'][$year['id']]['file_id']) {
                                                ?>
                                                <a href="/admin/files/download_file?file_id=<?=$application['data']['years'][$year['id']]['file_id']?>" target="_blank">download</a>
                                            <?php
                                            }
                                            ?>
                                        </div>

                                        <label class="col-xs-4">
                                            <input type="file" id="application[years][<?=$year['id']?>][file]" class="sr-only file-upload-input file_select" />

                                            <span class="btn btn-primary form-btn btn--full button"><?= __('Browse') ?></span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h3><?= __('Subject preferences') ?></h3>

                <div class="form-group">
                    <div class="col-sm-6">
                        <?php
                        $options = array('' => '', 'Junior' => __('Junior'), 'Transition' => __('Transition'), 'Senior' => __('Senior'));
                        $selected = @$application['data']['cycle'];
                        echo Form::ib_select(__('Cycle'), 'application[cycle]', $options, $selected, array('id' => 'checkout-application-cycle'));
                        ?>
                    </div>
                </div>

                <?php
                $levels = Model_Levels::get_all_levels();
                $subjects = Model_Subjects::get_all_subjects(array('publish' => 1));
                ?>

                <div class="form-row gutters">
                    <?php
                    $selected_subjects = [];

                    // If viewing an existing application, get the subjects saved to that application
                    if (isset($application) && !empty($application['data'])) {
                        $selected_subjects = @$application['data']['subject'];
                    }
                    // If creating a new application, preload with the student's existing preferences automatically filled out
                    elseif (isset($contact) && $contact->get_id()) {
                        foreach ($contact->get_subject_preferences() as $preference) {
                            $selected_subjects[$preference['subject_id']] = ['id' => $preference['subject_id'], 'level' => $preference['level_id']];
                        }
                    }
                    ?>

                    <?php for ($i = 0; $i < count($subjects); $i ++): ?>
                        <?php if (isset($subjects[$i])): ?>
                            <?php
                            $subject = $subjects[$i];
                            $subject_selected = (bool) @$selected_subjects[$subject['id']]['id'];
                            ?>

                            <div class="profile-education-subject-wrapper col-sm-6" data-cycle="<?= $subject['cycle'] ?>">
                                <div class="profile-education-subject_and_level row gutters vertically_center">
                                    <div class="col-xs-6">
                                        <label class="checkbox-icon btn--full form-action-group profile-education-subject">
                                            <input type="checkbox"
                                                   name="application[subject][<?= $subject['id'] ?>][id]"
                                                   value="<?= $subject['id'] ?>"
                                                   <?= $subject_selected ? 'checked="checked"' : '' ?>
                                                />

                                            <span
                                                class="checkbox-icon-unchecked btn form-btn btn--full <?= $is_backend ? 'btn-default' : 'button button--send inverse' ?>"
                                                title="Click to add"
                                                >
                                                <span class="profile-education-subject-icon fa fa-plus"></span>
                                                <span title="<?= $subject['name'] ?>"><?= $subject['name'] ?></span>
                                            </span>

                                            <span
                                                class="checkbox-icon-checked btn form-btn btn--full <?= $is_backend ? 'btn-primary' : 'button button--send' ?>"
                                                title="Click to remove"
                                                >
                                                <span class="profile-education-subject-icon fa fa-check"></span>
                                                <span title="<?= $subject['name'] ?>"><?= $subject['name'] ?></span>
                                            </span>
                                        </label>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="btn-group btn-group-pills btn-group-pills-regular profile-education-subject_preferences stay_inline<?= $subject_selected ? '' : ' invisible' ?>">
                                            <?php foreach ($levels as $level): ?>
                                                <label class="radio-icon" title="<?= $level['level'] ?>">
                                                    <input type="radio"
                                                           name="application[subject][<?= $subject['id'] ?>][level]"
                                                           value="<?= $level['id'] ?>"
                                                           <?= (@$selected_subjects[$subject['id']]['level'] == $level['id']) ? 'checked="checked"' : '' ?>
                                                        />
                                                    <span class="radio-icon-unchecked btn"><span><?= $level['level'][0] ?></span></span>
                                                    <span class="radio-icon-checked btn btn-primary"><span><?= $level['level'][0] ?></span></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>

                <h3><?= __('College and career details') ?></h3>

                <div class="form-group">
                    <div class="col-xs-12">
                        <label for="checkout-career-interest"><?= __('What career areas are you interested in?') ?></label>
                    </div>

                    <div class="col-xs-12">
                        <?php
                        $attributes = array(
                            'id' => 'checkout-career-interest',
                            'placeholder' => __('e.g. I want to be a pilot :)'),
                            'rows' => 4
                        );
                        echo Form::ib_textarea(null, 'application[career]', @$application['data']['career'], $attributes); ?>
                    </div>
                </div>

                <div class="form-group vertically_center">
                    <div class="col-xs-6 col-sm-4">
                        <?= __('Do you want to go to college?') ?>
                    </div>

                    <div class="col-xs-6 col-sm-3">
                        <?php
                        $options = array('Yes' => __('Yes'), 'No' => __('No'));
                        $attributes = array('class' => 'stay_inline', 'style' => 'font-size: .625em;');
                        $selected = @$application['data']['college'];
                        echo Form::btn_options('application[college]', $options, $selected, false, array(), $attributes);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <table class="table--plain" id="application-colleges-table">
                            <thead>
                                <tr>
                                    <th scope="col"><?= __('Course') ?></th>
                                    <th scope="col"><?= __('Points') ?></th>
                                    <th scope="col"><?= __('Preferred college') ?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr class="hidden">
                                    <td>
                                        <?= Form::ib_input(null, 'application[courses_want][index][course]', null, array('placeholder' => __('e.g. Life Sciences'))); ?>
                                    </td>

                                    <td>
                                        <?= Form::ib_input(null, 'application[courses_want][index][points]', null, array('placeholder' => __('e.g. 350'))); ?>
                                    </td>

                                    <td>
                                        <?php
                                        $options = '<option value="">'.__('College').'</option>'.html::optionsFromRows('id', 'name', Model_Providers::get_all_schools());
                                        echo Form::ib_select(null, 'application[courses_want][index][college]', $options);
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                if (isset($application['data']['courses_want']))
                                    foreach ($application['data']['courses_want'] as $i => $course_want) {
                                        ?>
                                        <tr class="">
                                            <td><input type="text" name="application[courses_want][<?=$i?>][course]" class="form-control" placeholder="e.g. Life Sciences" value="<?=html::chars($course_want['course'])?>" /> </td>
                                            <td><input type="text" name="application[courses_want][<?=$i?>][points]" class="form-control" placeholder="e.g. 350" value="<?=html::chars($course_want['points'])?>" /> </td>
                                            <td>
                                                <select class="form-control" name="application[courses_want][<?=$i?>][college]">
                                                    <option value=""></option>
                                                    <?=html::optionsFromRows('id', 'name', Model_Providers::get_all_schools(), html::chars($course_want['college']))?>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                ?>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colspan="3">
                                        <button
                                            type="button"
                                            class="<?= $is_backend ? 'btn btn-default' : 'button button--send inverse' ?> add"
                                            ><?=__('Add another course')?></button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <label for="checkout-education-other_info">
                            <?=__('Other relevant information') ?>
                        </label>

                        <?= Form::ib_textarea(null, 'application[other]', @$application['data']['other'], array('id' => 'checkout-education-other_info')); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    (function()
    {
        var $table      = $('#application-colleges-table');
        var $template   = $table.find('tbody > tr.hidden');
        var $add_button = $table.find('tfoot button.add');

        $template.remove();

        $add_button.on('click', function()
        {
            var $tr = $template.clone();
            var index = $table.find('tbody > tr').length;

            $tr.removeClass('hidden');
            $tr.find(':input').each(function() {
                this.name = this.name.replace("[index]", "[" + index + "]");
            });

            $table.find('tbody').append($tr);
        });

        if ($table.find('tbody tr').length == 0) {
            $add_button.trigger('click');
        }
    })();
</script>