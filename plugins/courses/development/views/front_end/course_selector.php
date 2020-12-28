<form action="/course-detail" method="get" class="course-selector-form validate-on-submit">
    <div class="form-select-plain">
        <?php
        $options = html::optionsFromRows('id', 'title', $courses, null, ['value' => '', 'label' => 'Select Course']);
        echo Form::ib_select(null, 'id', $options, null, ['class' => 'validate[required]']);
        ?>
    </div>

    <button type="submit" class="button bg-category w-100">View details</button>
</form>
