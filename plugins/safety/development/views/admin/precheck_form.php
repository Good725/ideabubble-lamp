<div class="alert_area"><?= isset($alert) ? $alert : '' ?></div>

<style>
    .table-sticky th {
        background: var(--primary);
        background: linear-gradient(#fff, var(--primary) 1px);
        color: #fff;
    }

    .table-sticky.table-sticky th {
        border: none;
    }

    .table-sticky th:not(:empty) {
        position: sticky;
        top: 0;
        z-index: 2;
    }
</style>

<?php
ob_start();
$form = new IbForm('safety-precheck-form', '#', 'post', ['layout' => 'vertical']);
$form->published_field = false;

echo $form->start(['title' => false]);
echo $form->hidden('id');
?>

<div class="precheck-modal-page in-use">
    <p>Select the type for this pre-check:</p>

    <div class="text-center mb-3">
        <label class="radio-icon">
            <input type="radio" class="precheck-type-toggle" name="precheck_type" value="car" />
            <span class="radio-icon-unchecked btn btn-lg btn-default">Car safety</span>
            <span class="radio-icon-checked btn btn-lg btn-primary">Car safety</span>
        </label>

        <label class="radio-icon">
            <input type="radio" class="precheck-type-toggle" name="precheck_type" value="farm" />
            <span class="radio-icon-unchecked btn btn-lg btn-default">Farm safety</span>
            <span class="radio-icon-checked btn btn-lg btn-primary">Farm safety</span>
        </label>
    </div>

    <div class="safety-precheck-quiz-wrapper hidden" data-type="farm">
        <h2>Farm safety</h2>
        <table class="table table-sticky" style="margin-bottom: 11em;">
            <thead>
                <tr>
                    <th scope="col" rowspan="2">Question</th>
                    <th scope="col" style="width: 11em;">Answer</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($farm_prechecks as $number => $type): ?>
                    <tr>
                        <td><?= htmlspecialchars($type['question']) ?></td>
                        <td data-type="<?= htmlspecialchars($type['type']) ?>">
                            <?php
                            $name = 'type_'.$number;
                            $options = ['yes' => 'Yes', 'no' => 'No'];
                            $attributes = ['class' => 'precheck-type-yes_no validate[groupRequired['.$name.']]'];
                            $group_attributes = ['class' => 'stay_inline'];
                            echo Form::btn_options($name, $options, null, false, $attributes, $group_attributes); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>


    <div class="safety-precheck-quiz-wrapper hidden" data-type="car">
        <?php
        $type = 'car';
        $heading = 'Car safety';
        $columns = null; // [1 => 'Ford', 2 => 'Zetor', 3 => 'ATV', 4 => 'Loader', 5 => 'Trailer'];
        $questions = $dummy_questions['cars'];
        include 'precheck_response.php';
        ?>
    </div>
</div>

<?php foreach ($farm_prechecks as $farm_precheck): ?>
    <div class="precheck-modal-page hidden" data-type="<?= htmlspecialchars($farm_precheck['type']) ?>">
        <?= View::factory('admin/precheck_response', $farm_precheck)->set('form', $form); ?>
    </div>
<?php endforeach ?>

<div class="precheck-modal-page hidden" id="precheck-modal-page-corrective_action">
    <h2><?= __('Safety Action List') ?></h2>
    <p><?= __('Where your assessments have indicated safety controls that are missing you must show in the action list
        below what action you will take to put that control in place. This action should have a date for completion.
        When the control is in place the action should be signed off indicating that the control is now in place.') ?></p>

    <table class="table table-sticky">
        <thead>
            <tr>
                <th scope="col">Safety assessment</th>
                <th scope="col">Missing safety control measures</th>
                <th scope="col">Action I must take</th>
                <th scope="col">Date</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Machinery</td>
                <td>PTO cover and "0" guard missing on vacuum tanker</td>
                <td><?= Form::ib_input(null, null, null) ?></td>
                <td><?= Form::ib_datepicker() ?></td>
            </tr>
        </tbody>
    </table>
</div>

<div class="precheck-modal-buttons hidden" data-type="car">
    <div class="form-action-buttons form-action-group">
        <?= $form->modal_action_buttons([]); ?>
    </div>
</div>

<div class="d-flex align-items-center form-action-group precheck-modal-buttons hidden"
     data-type="farm" style="justify-content: center;">
    <button type="button" class="btn btn-default mx-0" id="precheck-modal-prev">← Previous</button>

    <div class="mx-3">
        Page
        <span id="precheck-modal-pagination-page"></span> of
        <span id="precheck-modal-pagination-total"></span>
    </div>

    <button type="button" class="btn btn-default mx-0" id="precheck-modal-next">Next →</button>
    <button type="button" class="btn btn-primary mx-0 hidden" id="precheck-modal-last">Submit</button>
</div>

<?php
echo $form->end();

$form = ob_get_clean();

echo View::factory('snippets/modal')->set([
    'id'     => 'safety-precheck-modal',
    'title'  => 'Pre-check',
    'size'   => 'lg',
    'body'   => $form,
]);
?>
<script>
    // When the top-level precheck (car, farm) is selected/changed...
    $('.precheck-type-toggle').on('change', function () {
        // show the questions for that precheck. Hide the questions for the other prechecks.
        $('.precheck-type-toggle').each(function () {
            $('.safety-precheck-quiz-wrapper[data-type="' + this.value + '"]').toggleClass('hidden', !this.checked);
        });

        // Show the button group relevant to the pre-check
        const type = $('.precheck-type-toggle:checked').val();
        $('.precheck-modal-buttons').addClass('hidden');
        $('.precheck-modal-buttons[data-type="' + type + '"]').removeClass('hidden');

        // Force the pagination section to update, if applicable.
        $('.precheck-type-yes_no').trigger('change');
    });

    // When a toggle on the first page is changed
    $('.precheck-type-yes_no').on('change', function () {
        const $td = $(this).parents('td');
        const valid = $td.find(':checked').val() == 'yes';
        const type = $td.data('type');

        // Flag whether or not the question group corresponding to the toggle is to have a page.
        $('.precheck-modal-page[data-type="' + type + '"]').toggleClass('in-use', valid);

        // Update the "Page X of Y" text.
        update_page_counter();
    });

    // Some prechecks have dropdowns where the columns to show are selected.
    $('.precheck-select-items').on('change', function () {
        var total = $(this).find('option').length;
        var selected = $(this).val();
        var $table = $(this).parents('.safety-precheck-quiz-wrapper').find('.precheck-questions-table');

        // Show the table if there is at least one column selected. Hide otherwise.
        $table.toggleClass('hidden', (selected === null || selected.length == 0));

        // Show/hide the relevant columns
        for (let i = 1; i <= total; i++) {
            var is_selected = selected.indexOf('' + i) > -1;
            $table.find('td:nth-child(' + (2 + i) + ')').toggleClass('hidden', !is_selected);
            $table.find('th:nth-child(' + (2 + i) + ')').toggleClass('hidden', !is_selected);
        }
        $table.find('th[colspan]:last-child').toggleClass('hidden', total == 0);
    });

    // When a yes/no question is answered...
    $('.precheck-yes_no').on('change', function () {
        const $td = $(this).parents('td');
        const yes_selected = $td.find(':checked').val() == 'yes';
        const stock = $td.data('stock');
        const $todo_row = $td.parents('tr').find('~ .precheck-todo[data-stock="' + stock + '"]').first();

        // If "no" was selected at one point, remember that.
        if (!yes_selected) {
            $td.addClass('had-corrective-action');
        }

        // An extra page for corrective action appears at the end, if action is needed for at least one item.
        $('#precheck-modal-page-corrective_action').toggleClass('in-use', corrective_action_needed());

        // Since toggling an answer here can affect the number of pages, ensure the counter is up-to-date.
        update_page_counter();

        // Show the "corrective action" section, if "no" was selected. Hide if "yes" was selected.
        $todo_row.toggleClass('hidden', yes_selected);

        // Show a link to view the "corrective action" section, if "no" was selected in the past.
        const show_corrective_action_button = $td.hasClass('had-corrective-action') && !yes_selected;
        $td.find('.precheck-view-corrective').toggleClass('hidden', !show_corrective_action_button);
    });

    // When the "previous" pagination button is clicked...
    $('#precheck-modal-prev').on('click', function () {
        const $current_page = $('.precheck-modal-page:not(.hidden)');
        const $prev_page = $current_page.prevAll('.precheck-modal-page.in-use').first();
        const prev_page_is_first = ($prev_page.prevAll('.precheck-modal-page.in-use').first().length == 0);

        // Only continue if there is a previous page.
        if ($prev_page.length) {
            // Hide the current page
            $current_page.addClass('hidden');

            // Show the previous page, _after_ the current page is hidden.
            setTimeout(function () {
                $prev_page.removeClass('hidden');
            }, 1);

            // Show what the current page is. (Update the "X" in "Page X of Y".)
            const page_number = 1 + $('.precheck-modal-page.in-use').index($prev_page);
            $('#precheck-modal-pagination-page').html(page_number);

            // We've just moved back one page, meaning there is a next page.
            // (Show the "next" button instead of the "end" button.)
            $('#precheck-modal-last').addClass('hidden');
            $('#precheck-modal-next').removeClass('hidden');
        }

        // Disable the button if there are no more previous pages.
        $('#precheck-modal-prev').toggleClass('disabled', prev_page_is_first);
    });

    // When the "next" pagination button is clicked...
    $('#precheck-modal-next').on('click', function () {
        const $current_page = $('.precheck-modal-page:not(.hidden)');
        const $next_page = $current_page.find('~ .precheck-modal-page.in-use').first();
        const next_page_is_last = ($next_page.find('~ .precheck-modal-page.in-use').first().length == 0);

        // Only continue if there is a next page
        if ($next_page.length) {
            // Only continue if the page passes form validation.
            const valid = $('#safety-precheck-form').validationEngine('validate', {showArrowOnRadioAndCheckbox: true});
            if (!valid) {
                return false;
            }

            // Hide the current page.
            $current_page.addClass('hidden');
            // Make the next page visible _after_ the current page is hidden.
            setTimeout(function () {
                $next_page.removeClass('hidden');
            }, 1);

            // Show what the current page is. (Update the "X" in "Page X of Y".)
            const page_number = 1 + $('.precheck-modal-page.in-use').index($next_page);
            $('#precheck-modal-pagination-page').html(page_number);

            // We've just moved forward one page, meaning there is a previous page.
            // Don't disable the previous button.
            $('#precheck-modal-prev').removeClass('disabled');
        }

        // If there are more pages, show the "next" button.
        // If this is the last page, instead show the "end" button.
        if (next_page_is_last) {
            $('#precheck-modal-next').addClass('hidden');
            $('#precheck-modal-last').removeClass('hidden');
        }
    });

    // When the "end" button is clicked...
    $('#precheck-modal-last').on('click', function () {
        // Only continue if the form is valid.
        const valid = $('#safety-precheck-form').validationEngine('validate', {showArrowOnRadioAndCheckbox: true});

        if (!valid) {
            return false;
        }
    });

    // When the "show corrective action" button is clicked...
    $('.precheck-view-corrective').on('click', function () {
        // Find the to-do corresponding to the question and column.
        const $td = $(this).parents('td');
        const stock = $td.data('stock');
        const $todo_row = $td.parents('tr').find('~ .precheck-todo[data-stock="' + stock + '"]').first();

        // Show the to-do (correcctive action).
        $todo_row.removeClass('hidden');
    });

    // Determine if corrective action is needed.
    function corrective_action_needed()
    {
        return ($('.precheck-yes_no[value="no"]:checked').length > 0);
    }

    function update_page_counter()
    {
        // Set the number of pages to be shown.
        const $pages_in_use = $('.precheck-modal-page.in-use');
        const total_pages = $pages_in_use.length;
        $('#precheck-modal-pagination-total').html(total_pages);

        // Set the number of the current page.
        const $current_page = $('.precheck-modal-page.in-use:not(.hidden)');
        const page_number = 1 + $pages_in_use.index($current_page);
        $('#precheck-modal-pagination-page').html(page_number);

        // If this is last page, show the "last" button instead of the "next button".
        $('#precheck-modal-last').toggleClass('hidden', page_number != total_pages);
        $('#precheck-modal-next').toggleClass('hidden', page_number == total_pages);
    }
</script>