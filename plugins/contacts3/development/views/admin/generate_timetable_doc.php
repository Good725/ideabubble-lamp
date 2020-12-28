<?php
$label_grid_classes = 'col-xs-3 col-sm-2 col-md-1 control-label text-left';
$input_grid_classes = 'col-xs-9 col-sm-4 col-md-3';
?>

<form class="form-horizontal" action="/admin/contacts3/generate_timetable_doc" method="post">
    <input type="hidden" name="student_id" value="" id="student_id" />

    <div class="form-group">
        <!-- From -->
        <label class="<?= $label_grid_classes ?>" for="print_filter_from">From</label>

        <div class="<?= $input_grid_classes ?>">
            <?= Form::ib_input(null, 'from', null, ['autocomplete' => 'off', 'class' => 'datepicker', 'id' => 'print_filter_from']) ?>
        </div>

        <div class="form-group hidden-sm hidden-md hidden-lg"></div><?php // Add a dividing space that only appears on certain screen sizes ?>

        <!-- To -->
        <label class="<?= $label_grid_classes ?>" for="print_filter_to">To</label>

        <div class="<?= $input_grid_classes ?>">
            <?= Form::ib_input(null, 'to', null, ['autocomplete' => 'off', 'class' => 'datepicker', 'id' => 'print_filter_to']) ?>
        </div>

        <div class="form-group hidden-md hidden-lg"></div>

        <!-- Category -->
        <label class="<?= $label_grid_classes ?>" for="print_filter_category">Category</label>

        <div class="<?= $input_grid_classes ?>">
            <?php
            $options = '<option value=""></option>'.html::optionsFromRows('id', 'category', Model_Categories::get_all_categories());
            echo Form::ib_select(null, 'category_id', $options, null, ['id' => 'print_filter_category']);
            ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10 col-md-offset-1 col-md-9">
            <button class="btn btn-primary form-btn" type="submit" id="print_timetable" name="print_timetable" value="1"><?=__('Print')?></button>

            <button class="btn btn-primary form-btn" type="submit" id="email_timetable" name="email_timetable" value="1"><?=__('Email')?></button>
        </div>
    </div>
</form>
