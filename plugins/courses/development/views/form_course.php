<?php
$data = (count($_POST) > 0) ? $_POST : (isset($data) ? $data : array());
$please_select = ['value' => '', 'label' => '--'.__('Please select').'--'];
$please_select_multi = ['multiselect_options' => ['defaultText' => '-- '.__('Please select').' --']];
$search_icon = ['icon' => '<span class="flip-horizontally"><span class="icon_search"></span></span>'];
?>

<div class="col-sm-12" id="showalert">
    <?php if(isset($alert)): ?>
        <?= $alert ?>
        <script>remove_popbox();</script>
    <?php endif; ?>
</div>

<form class="form-horizontal" id="form_add_edit_course" name="form_add_edit_course" action="/admin/courses/save_course/" method="post">
    <?php
    echo View::factory('form_title')
     ->set([
         'name'            => $data['title'],
         'name_field'      => 'title',
         'name_attributes' => ['placeholder' => __('Enter course title')],
         'published'       => $data['publish'],
         'publish_field'   => 'publish'
     ]);
    ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#summary_tab" data-toggle="tab">Summary</a></li>
        <li><a href="#images_tab" data-toggle="tab">Media</a></li>
        <li><a href="#topics_tab" data-toggle="tab">Topics</a></li>
        <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'franchisee')) { ?>
            <li><a href="#schedule_defaults_tab" data-toggle="tab">Schedule Defaults</a></li>
        <?php } ?>
        <li><a href="#schedules_tab" data-toggle="tab">Schedules</a></li>
        <li><a href="#preview_tab" data-toggle="tab">Preview</a></li>
    </ul>
    <div class="tab-content">
        <!-- Summary -->
        <div class="tab-pane active" id="summary_tab">
            <input type="hidden" id="redirect" name="redirect"/>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="code">Code</label>

                <div class="col-sm-5">
                    <?= Form::ib_input(null, 'code', $data['code'], ['id' => 'code']); ?>
                </div>
            </div>

            <?php if (@Kohana::$config->load('config')->fulltime_course_booking_enable) { ?>
                <!-- Full time -->
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="is_fulltime">Full Time</label>

                    <div class="col-sm-5">
                        <div class="selectbox">
                            <select class="form-control" id="is_fulltime" name="is_fulltime">
                                <?=html::optionsFromArray(array('NO' => 'NO', 'YES' => 'YES'), (@$data['is_fulltime'] ?: 'NO'))?>
                            </select>
                        </div>
                    </div>
                </div>

                <fieldset class="fulltime_param hidden">
                    <legend>Full Time Parameters</legend>

                    <div class="form-group fulltime_param hidden">
                        <label class="col-sm-2 control-label" for="fulltime_price">Price</label>

                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="fulltime_price" name="fulltime_price"
                                   value="<?= isset($data['fulltime_price']) ? $data['fulltime_price'] : '' ?>"/>
                        </div>
                    </div>

                    <div class="form-group fulltime_param hidden">
                        <label class="col-sm-2 control-label" for="payment_options">Payment Options</label>

                        <div class="col-sm-10">
                            <table class="table" id="paymentoptions">
                                <thead>
                                <tr><th>#</th><th>Plan&nbsp;Type</th><th>Deposit</th><th>Months</th><th>Interest Rate</th><th><button class="btn add" type="button">add</button> </th></tr>
                                </thead>
                                <tbody><?php
                                if (is_array(@$data['paymentoptions']))
                                foreach ($data['paymentoptions'] as $poindex => $paymentoption) {
                                ?>
                                    <tr class="payment_option" data-index="<?=$poindex?>">
                                        <td class="c1"><span class="po_id">#<?=$paymentoption['id']?></span><input type="hidden" name="paymentoption[<?=$poindex?>][id]" class="po_id" value="<?=$paymentoption['id']?>" /> </td>
                                        <td class="c2"><?=Form::ib_select(null, 'paymentoption['.$poindex.'][interest_type]', array('Percent' => 'Percent', 'Custom' => 'Custom'), $paymentoption['interest_type'], ['class' => 'interest_type'])?></td>
                                        <td class="c3" <?=$paymentoption['interest_type'] == 'Custom' ? 'colspan="3"' : ''?>>
                                            <input type="text" name="paymentoption[<?=$poindex?>][deposit]" class="deposit <?=$paymentoption['interest_type'] == 'Custom' ? ' hidden' : ''?>" value="<?=$paymentoption['deposit']?>" />
                                            <table class="table custom payment_plan <?=$paymentoption['interest_type'] == 'Custom' ? '' : 'hidden'?>">
                                                <thead>
                                                <th>Amount</th><th>Interest Amount</th><th style="min-width:110px;">Due&nbsp;Date</th><th>Total</th><th><button type="button" class="btn add_custom">Add</button></th>
                                                </thead>
                                                <tbody>
                                                <?php
                                                if ($paymentoption['custom_payments'])
                                                    foreach ($paymentoption['custom_payments'] as $poindex2 => $custom_payment) {
                                                        ?>
                                                        <tr class="custom_option" data-index2="<?=$poindex2?>">
                                                            <td><?= Form::ib_input(null, 'paymentoption['.$poindex.'][custom_payments]['.$poindex2.'][amount]', $custom_payment['amount'], ['class' => 'amount'], ['icon' => '<span>€</span>']); ?></td>
                                                            <td><?= Form::ib_input(null, 'paymentoption['.$poindex.'][custom_payments]['.$poindex2.'][interest]', $custom_payment['interest'], ['class' => 'interest'], ['icon' => '<span>€</span>']); ?></td>
                                                            <td><?= Form::ib_input(null, 'paymentoption['.$poindex.'][custom_payments]['.$poindex2.'][due_date]', $custom_payment['due_date'], ['class' => 'due_date']); ?></td>
                                                            <td><?= Form::ib_input(null, 'paymentoption['.$poindex.'][custom_payments]['.$poindex2.'][total]', $custom_payment['total'], ['class' => 'total'], ['icon' => '<span>€</span>']); ?></td>
                                                            <td><button type="button" class="btn remove_custom">remove</button></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                ?>
                                                </tbody>
                                                <tfoot>
                                                </tfoot>
                                            </table>
                                        </td>
                                        <td class="c4 <?=$paymentoption['interest_type'] == 'Custom' ? 'hidden' : ''?>"><input type="text" name="paymentoption[<?=$poindex?>][months]" min="2" class="months" value="<?=$paymentoption['months']?>" /> </td>
                                        <td class="c5 <?=$paymentoption['interest_type'] == 'Custom' ? 'hidden' : ''?>"><input type="text" name="paymentoption[<?=$poindex?>][interest_rate]" placeholder="%" class="interest_rate" value="<?=$paymentoption['interest_rate']?>"/> </td>
                                        <td><button type="button" class="btn btn-outline-danger remove">remove</button> </td>
                                    </tr>
                                <?php
                                }
                                ?></tbody>
                                <tfoot>
                                    <tr class="payment_option hidden">
                                        <td class="c1" ><span class="po_id"></span><input type="hidden" name="paymentoption[index][id]" class="po_id" /> </td>
                                        <td class="c2"><?=Form::ib_select(null, 'paymentoption[index][interest_type]', array('Percent' => 'Percent', 'Custom' => 'Custom'), 'Percent', ['class' => 'interest_type'])?></td>
                                        <td class="c3"><input type="text" name="paymentoption[index][deposit]" class="deposit" /></td>
                                        <td class="c4" ><input type="text" name="paymentoption[index][months]" min="2" class="months" /> </td>
                                        <td class="c5"><input type="text" name="paymentoption[index][interest_rate]" placeholder="%" class="interest_rate" /> </td>
                                        <td><button type="button" class="btn remove">remove</button> </td>
                                    </tr>
                                </tfoot>
                            </table>

                            <table class="table hidden custom payment_plan tpl">
                                <thead>
                                <th>Amount</th><th>Interest Amount</th><th style="min-width:110px;">Due&nbsp;Date</th><th>Total</th><th><button type="button" class="btn add_custom">Add</button></th>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                <tr class="custom_option hidden">
                                    <td><?= Form::ib_input(null, 'paymentoption[index][custom_payments][index2][amount]', null, ['class' => 'amount'], ['icon' => '<span>€</span>']); ?></td>
                                    <td><?= Form::ib_input(null, 'paymentoption[index][custom_payments][index2][interest]', null, ['class' => 'interest'], ['icon' => '<span>€</span>']); ?></td>
                                    <td><?= Form::ib_input(null, 'paymentoption[index][custom_payments][index2][due_date]', null, ['class' => 'due_date']); ?></td>
                                    <td><?= Form::ib_input(null, 'paymentoption[index][custom_payments][index2][total]', null, ['class' => 'total'], ['icon' => '<span>€</span>']); ?></td>
                                    <td><button type="button" class="btn remove_custom">remove</button></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </fieldset>
            <?php } ?>

            <!-- Year -->
            <?php if (Auth::instance()->has_access('courses_year_edit')): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="year_id">Year</label>

                    <div class="col-sm-5">
                        <?php if (count($years)): ?>
                            <?php
                            $options = html::optionsFromRows('id', 'year', $years, $data['year_ids']);
                            echo Form::ib_select(null, 'year_id[]', $options, null, ['id' => 'year_id', 'multiple' => 'multiple'], $please_select_multi);
                            ?>
                        <?php else: ?>
                            <input type="hidden" name="year_id[]" value="" />
                            <p class="control-label text-left"><?= __('No years currently available.') ?> <a href="/admin/courses/add_year" target="_blank"><strong><?= __('Add year') ?></strong></a>.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Level -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="level_id">Level</label>

                <div class="col-sm-5">
                    <?php if (count($levels)): ?>
                        <?php
                        $options = html::optionsFromRows('id', 'level', $levels, $data['level_id'], $please_select);
                        echo Form::ib_select(null, 'level_id', $options, $data['level_id'], ['id' => 'level_id']);
                        ?>
                    <?php else: ?>
                        <input type="hidden" name="level_id" value="" />
                        <p class="control-label text-left"><?= __('No levels currently available.') ?> <a href="/admin/courses/add_level" target="_blank"><strong><?= __('Add level') ?></strong></a>.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Category -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="category_id">Category</label>

                <div class="col-sm-5">
                    <?php if (count($categories)): ?>
                        <?php
                        $options = html::optionsFromRows('id', 'category', $categories, $data['category_id'], $please_select);
                        echo Form::ib_select(null, 'category_id', $options, $data['category_id'], ['id' => 'category_id']);
                        ?>
                    <?php else: ?>
                        <input type="hidden" name="category_id" value="" />
                        <p class="control-label text-left"><?= __('No categories currently available.') ?> <a href="/admin/courses/add_category" target="_blank"><strong><?= __('Add category') ?></strong></a>.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Type -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="type_id">Type</label>

                <div class="col-sm-5">
                    <?php if (count($types)): ?>
                        <?php
                        $options = html::optionsFromRows('id', 'type', $types, $data['type_id'], $please_select);
                        echo Form::ib_select(null, 'type_id', $options, null, ['id' => 'type_id']);
                        ?>
                    <?php else: ?>
                        <input type="hidden" name="type_id" value="" />
                        <p class="control-label text-left"><?= __('No types currently available.') ?> <a href="/admin/courses/add_type" target="_blank"><strong><?= __('Add type') ?></strong></a>.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Subject -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="course_subject_id">Subject</label>
                <div class="col-sm-5">
                    <?php if (count($subjects)): ?>
                        <?php
                        $options = html::optionsFromRows('id', 'name', $subjects, $data['subject_id'], $please_select);
                        echo Form::ib_select(null, 'subject_id', $options, null, ['id' => 'subject_id']);
                        ?>
                    <?php else: ?>
                        <input type="hidden" name="subject_id" value="" />
                        <p class="control-label text-left"><?= __('No types currently available.') ?> <a href="/admin/courses/add_subject" target="_blank"><strong><?= __('Add subject') ?></strong></a>.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Curriculum -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="course_curriculum">Curriculum</label>
                <div class="col-sm-5">
                    <?php if (!empty($curriculums) && count($curriculums)): ?>
                        <?php
                        $options = html::optionsFromRows('id', 'title', $curriculums, $data['curriculum_id'], $please_select);
                        echo Form::ib_select(null, 'curriculum_id', $options, null, ['id' => 'curriculum_id']);
                        ?>
                    <?php else: ?>
                        <input type="hidden" name="curriculum_id" value="" />
                        <p class="control-label text-left"><?= __('No curriculums currently available.') ?> <a href="/admin/courses/curriculums" target="_blank"><strong><?= __('Add curriculum') ?></strong></a>.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Provider -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="provider_id">Provider</label>

                <div class="col-sm-5">
                    <?php if (count($providers)): ?>
                        <?php
                        $options = html::optionsFromRows('id', 'name', $providers, $data['has_providers']);
                        echo Form::ib_select(null, 'has_providers[]', $options, null, ['id' => 'provider_id', 'multiple' => 'multiple'], $please_select_multi);
                        ?>
                    <?php else: ?>
                        <input type="hidden" name="has_providers[]" value="" />
                        <p class="control-label text-left"><?= __('No providers currently available.') ?> <a href="/admin/courses/add_provider" target="_blank"><strong><?= __('Add provider') ?></strong></a>.</p>
                    <?php endif; ?>
                </div>
            </div>
            <!--Accredited BY-->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="accredited_by">Accredited by</label>
                <div class="col-sm-5">
                    <?php if(isset($accreditation_bodies) && count($accreditation_bodies)):?>
                    <?php
                        $accreditation_options = html::optionsFromRows('id', 'name', $accreditation_bodies, $data['accredited_by']);
                        echo Form::ib_select(null, 'accredited_by[]', $accreditation_options, null, ['id' => 'provider_id', 'multiple' => 'multiple'], $please_select_multi);
                        ?>
                    <?php endif?>
                </div>
            </div>

            <!-- GDPR type -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="course-edit-gdpr_type">
                    <abbr title="General Data Protection Regulation">GDPR</abbr> type
                </label>

                <div class="col-sm-5">
                    <?php
                    $options = [
                        '' => '-- Please select --',
                        'gdpr1' => 'GDPR 1: Standard',
                        'gdpr2' => 'GDPR 2: Accredited'
                    ];
                    echo Form::ib_select(null, 'gdpr_type', $options, $data['gdpr_type'], ['id' => 'course-edit-gdpr_type']);
                    ?>
                </div>
            </div>


            <!-- Summary -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="summary">Summary</label>

                <div class="col-sm-10">
                    <textarea class="ckeditor-simple form-input" id="summary" name="summary" rows="4"><?= $data['summary'] ?></textarea>
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="col-sm-2 control-label" for="description">Description</label>

                <div class="col-sm-10">
                    <textarea class="ckeditor form-input" id="description" name="description" rows="4"><?= $data['description'] ?></textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="booking_button">Show Book Course</label>
                <div class="col-sm-5">
                    <?php $show_book_course = (isset($data['book_button']) && $data['book_button'] == '1'); ?>
                    <input type="hidden" name="book_button" value="0" />
                    <?= Form::ib_checkbox_switch(null, 'book_button', 1, $show_book_course) ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="display_availability">Display Availability</label>
                <div class="col-sm-5">
                    <?php
                    $options = html::optionsFromArray(array('per_course' => 'Per Course', 'per_schedule' => 'Per Schedule'), @$data['display_availability']);
                    echo Form::ib_select(null, 'display_availability', $options, null, ['id' => 'display_availability'])
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="booking_button">Show Course Description</label>
                <div class="col-sm-5">
                    <?php $show_description_button = (isset($data['description_button']) && $data['description_button'] == '1'); ?>
                    <input type="hidden" name="description_button" value="0" />
                    <?= Form::ib_checkbox_switch(null, 'description_button', 1, $show_description_button) ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="booking_button">Third party course link</label>
                <div class="col-sm-5">
                    <?= Form::ib_input(null, 'third_party_link', $data['third_party_link'], ['id' => 'third_party_link']); ?>
                </div>
            </div>
            
            <!-- Category Identifier -->
            <input type="hidden" id="id" name="id" value="<?= isset($data['id']) ? $data['id'] : '' ?>"/>

            <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'franchisee')) { ?>
                <input type="hidden" name="schedule_is_fee_required" value="<?=@$data['schedule_is_fee_required']?>" />
                <input type="hidden" name="schedule_fee_amount" value="<?=@$data['schedule_fee_amount']?>" />
                <input type="hidden" name="schedule_fee_per" value="<?=@$data['schedule_fee_per'] ?: 'Schedule'?>" />
                <input type="hidden" name="schedule_allow_price_override" value="<?=@$data['schedule_allow_price_override'] ?: 0?>" />
            <?php } ?>
        </div>

        <?php if (Model_Plugin::is_enabled_for_role('Administrator', 'franchisee')) { ?>
            <!-- Schedule Defaults -->
            <div class="tab-pane" id="schedule_defaults_tab">
                <!-- is fee required -->
                <?php $fee_required = isset($data['schedule_is_fee_required']) ? (bool) $data['schedule_is_fee_required'] : true; ?>

                <div class="form-group">
                    <label class="col-sm-3 col-md-2 control-label" for="schedule_is_fee_required">Fee required</label>

                    <div class="col-sm-9 col-md-10">
                        <input type="hidden" name="schedule_is_fee_required" value="0" />
                        <?php
                        $attributes = ['id' => 'schedule_is_fee_required', 'data-hide_toggle' => '#edit-course-fee-fields'];
                        echo Form::ib_checkbox_switch(null, 'schedule_is_fee_required', 1, $fee_required, $attributes);
                        ?>
                    </div>
                </div>

                <div class="form-group<?= $fee_required ? '' : ' hidden' ?>" id="edit-course-fee-fields">
                    <label class="col-sm-3 col-md-2 control-label" for="schedule_fee_amount">Fee</label>

                    <div class="col-sm-4">
                        <?php
                        $value = isset($data['schedule_fee_amount']) ? $data['schedule_fee_amount'] : '';
                        echo Form::ib_input(null, 'schedule_fee_amount', $value, ['id' => 'schedule_fee_amount'], ['icon' => '<span>€</span>']);
                        ?>
                    </div>

                    <div class="col-sm-4">
                        <?php
                        $options  = ['Timeslot' => __('Timeslot'), 'Day' => __('Day'), 'Schedule' => __('Schedule')];
                        $selected = @$data['schedule_fee_per'] ?: 'Schedule';
                        echo Form::ib_select(null, 'schedule_fee_per', $options, $selected, ['id' => 'schedule_fee_per']);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 col-md-2 control-label" for="schedule_allow_price_override">Allow Price Override</label>

                    <div class="col-sm-9">
                        <?php $schedule_allow_price_override = isset($data['schedule_allow_price_override']) ? (bool) $data['schedule_allow_price_override'] : false; ?>
                        <input type="hidden" name="schedule_allow_price_override" value="0" />
                        <?= Form::ib_checkbox_switch(null, 'schedule_allow_price_override', 1, $schedule_allow_price_override, ['id' => 'schedule_allow_price_override']); ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Media -->
        <div class="tab-pane form-horizontal" id="images_tab">
            <!-- Banner -->
            <?php if (count($banner_images) > 0): ?>
                <h2>Banner image</h2>
                <?php $project_id = isset(Kohana::$config->load('config')->project_media_folder) ? Kohana::$config->load('config')->project_media_folder : ''; ?>
                <?php $media_path = Model_Media::get_path_to_media_item_admin($project_id, '', 'courses').'_thumbs/'; ?>
                <div class="form-group">
                    <label class="col-sm-3 col-sm-2 control-label text-left" for="edit-course-banner_image"><?= __('Banner Image') ?></label>

                    <div class="col-sm-6 col-md-5">
                        <?php
                        $selected = isset($data['banner']) ? $data['banner'] : null;
                        $options  = html::optionsFromRows('filename', 'filename', $banner_images, $selected, ['value' => '', 'label' =>'']);
                        $options .= '<option value=" ">None</option>';
                        $attributes = ['class' => 'ib-combobox', 'data-path' => $media_path, 'id' => 'edit-course-banner_image'];
                        echo Form::ib_select(null, 'banner', $options, $selected, $attributes);
                        ?>
                    </div>

                    <div class="col-sm-3 col-md-5">
                        <?php if (isset($data['banner']) && trim($data['banner'])): ?>
                            <img src="<?= $media_path.$data['banner'] ?>" id="edit-course-banner_image-preview" style="max-width: 100%;" />
                        <?php else: ?>
                            <img src="" id="edit-course-banner_image-preview" class="hidden" style="max-width: 100%;" />
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <h2><?= __('Content images') ?></h2>

            <!-- Selector & Button -->
            <div class="form-group<?= (empty($images)) ? ' hidden' : '' ?>">
                <label class="col-sm-3 col-md-2 control-label text-left" for="image">Select image</label>

                <div class="col-sm-6 col-md-5">
                    <?php
                    $options = html::optionsFromRows('filename', 'filename', $images, null, $please_select);
                    echo Form::ib_select(null, null, $options, null, ['id' => 'image']);
                    ?>
                </div>

                <div class="col-sm-3">
                    <button type="button" class="btn form-btn add" id="add_image">Add</button>
                </div>
            </div>

            <div<?= (empty($images)) ? ' class="hidden"' : '' ?>>
                <!-- Table -->
                <table class="table table-striped hidden" id="images_table">
                    <thead>
                    <tr>
                        <th scope="col">Thumb</th>
                        <th scope="col">File Name</th>
                        <th scope="col">Remove</th>
                    </tr>
                    </thead>

                    <tbody></tbody>
                </table>
            </div>

            <?php if (empty($images)): ?>
                <p>No course images available. <a href="/admin/media" target="_blank"><strong>Upload images</strong></a></p>
            <?php endif; ?>

            <h2 style="clear: both;"><?= __('Documents') ?></h2>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="booking_button">Use brochure template</label>
                <div class="col-sm-5" rel="popover" data-content="The brochure template can be found and edited within the files plugin">
                    <input type="hidden" name="use_brochure_template" value="0" />
                    <?= Form::ib_checkbox_switch(null, 'use_brochure_template', 1, !empty($data['use_brochure_template'])) ?>
                </div>
            </div>

            <!-- Document -->
            <div class="form-group">
                <label class="col-sm-3 col-md-2 control-label text-left" for="file_id">Select&nbsp;document</label>

                <div class="col-sm-6 col-md-5">
                    <?php
                    $options = html::optionsFromRows('filename', 'filename', $documents, $data['file_id'], $please_select);
                    echo Form::ib_select(null, 'file_id', $options, $data['file_id'], ['id' => 'file_id']);
                    ?>
                </div>
            </div>
        </div>

        <!-- Topics -->
        <div class="tab-pane" id="topics_tab">

            <!-- Selector & Button -->
            <div class="form-group<?= (empty($images)) ? ' hidden' : '' ?>">
                <label class="col-sm-3 col-md-2 control-label text-left" for="topic_select">Assign topics</label>
                <div class="col-sm-6 col-md-5">
                    <?php
                    $attributes = ['id' => 'topic_select'];
                    if (count($topics) > 10) {
                        $options = html::optionsFromRows('id', 'name', $topics, null, ['value' => '', 'label' => '']);
                        $attributes['class'] = 'ib-combobox';
                        $attributes['data-placeholder'] = __('Please select');
                        $args = $search_icon;
                    } else {
                        $options = html::optionsFromRows('id', 'name', $topics, null, $please_select);
                        $args = [];
                    }

                    echo Form::ib_select(null, null, $options, null, $attributes, $args);
                    ?>
                </div>

                <div class="col-sm-3 col-md-5">
                    <button type="button" class="btn btn-default form-btn" id="topic_select-add">Add</button>
                </div>
            </div>

            <!-- Table -->
            <div<?= (empty($topics)) ? ' class="hidden"' : '' ?>>
                <table class="table table-striped" id="course_topics_table">
                    <thead>
                        <tr>
                            <th scope="col">Topic Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Remove</th>
                        </tr>
                    </thead>

                    <tbody></tbody>
                </table>
            </div>

            <?php if (empty($topics)): ?>
                <p>No topics available. <a href="/admin/courses/add_topic" target="_blank"><strong>Add topic</strong></a></p>
            <?php endif; ?>
        </div>

        <!-- Schedules -->
        <div class="tab-pane" id="schedules_tab">
            <?= View::factory('list_schedules'); ?>
        </div>
        <div class="tab-pane" id="preview_tab">
        		<?php $url_name = str_replace('%2F', '', urlencode($data['title']));?>
                <iframe style="width:1024px; min-height:400px;" src="<?php echo URL::base() . 'course-detail/' . $url_name .'html/?id= ' . $data['id'] ?>" id="course_live_preview"></iframe>
        </div>
    </div>

    <div class="well form-action-group">
        <button type="button" class="btn btn-primary save_button" data-redirect="save">Save</button>
        <button type="button" class="btn btn-primary save_button" data-redirect="save_and_exit">Save &amp; Exit</button>
        <button type="reset" class="btn">Reset</button>
        <?php if (isset($data['id'])) : ?>
            <a class="btn btn-danger" id="btn_delete" data-id="<?= $data['id'] ?>">Delete</a>
        <?php endif; ?>
    </div>
</form>

<?php if (isset($data['id'])) : ?>
    <div class="modal fade" id="confirm_delete_course">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3>Warning!</h3>
				</div>

				<div class="modal-body">
					<p>This action is <strong>irreversible</strong>! Please confirm you want to delete the selected course.</p>
				</div>

				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal">Cancel</a>
					<a href="#" data-id="<?= $data['id'] ?? '0' ?>" class="btn btn-danger" id="btn_delete_yes">Delete</a>
				</div>
			</div>
		</div>
    </div>
<?php endif; ?>

<div class="modal fade" id="confirm_delete_image">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3>Warning!</h3>
			</div>

			<div class="modal-body">
				<p>This action is <strong>irreversible</strong>! Please confirm you want to remove this image from course.</p>
			</div>

			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
				<a href="#" data-id="0" class="btn btn-danger" id="btn_delete_course_yes">Delete</a>
			</div>
		</div>
	</div>
</div>
