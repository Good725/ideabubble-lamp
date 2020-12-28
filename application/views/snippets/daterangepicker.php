<div class="input-group form-daterangepicker">
    <span class="input-group-btn">
        <button type="button" class="btn btn-default form-daterangepicker-prev">
            <span class="icon-angle-left"></span>
        </button>
    </span>

    <?php
    $attributes['autocomplete'] = (isset($attributes['autocomplete']) ? $attributes['autocomplete'].' ' : '') . 'off';
    $attributes['class'] = (isset($attributes['class']) ? $attributes['class'].' ' : '') . 'form-daterangepicker-input';
    $id     = isset($attributes['id']) ? $attributes['id'] : '';
    $format = 'd/M/Y';
    $value  = $start_date ? date($format, strtotime($start_date)) : '';
    $value .= ($start_date && $end_date) ? ' - ' : '';
    $value .= $end_date ? date($format, strtotime($end_date)) : '';

    $args = ['icon' => '<span class="flaticon-calendar"></span>'];

    // This input will display the range in a user-friendly format
    echo Form::ib_input(null, null, $value, $attributes, $args);
    ?>

    <?php // These inputs will contain the dates in ISO format and should be the ones sent to the server.  ?>
    <input
        type="hidden"
        class="form-daterangepicker-start_date"
        <?= $id ? ' id="'.$id.'-start_date"' : '' ?>
        <?= !empty($start_name) ? ' name="'.$start_name.'"' : '' ?>
        value="<?= $start_date ?>"
        />

    <input
        type="hidden"
        class="form-daterangepicker-end_date"
        <?= $id ? ' id="'.$id.'-end_date"'   : '' ?>
        <?= !empty($end_name)   ? ' name="'.$end_name.'"'   : '' ?>
        value="<?= $end_date ?>"
        />

    <span class="input-group-btn">
        <button type="button" class="btn btn-default form-daterangepicker-next">
            <span class="icon-angle-right"></span>
        </button>
    </span>
</div>