<div class="alert_area"><?= $alert ?></div>
<?php
$new = !$questionnaire->id;
$page_options = [];
// $page_options variable can be removed. It was only kept to avoid a conflict.
$template_options = [];
foreach ($templates as $template) {
    $template_options[$template['id']] = $template['name'];
}

$form = new IbForm('questionnaire-builder', '/admin/surveys/save_questionnaire/' . $questionnaire->id);
$form->load_data($questionnaire);
$form->delete_url = '/admin/surveys/delete_questionnaire/'.$questionnaire->id;

echo $form->start();
echo $form->hidden('id');

$form->tab_start('Details', 'active');
    echo $form->select('Type', 'type', $types);
    include 'questionnaire_builder.php';

if ($questionnaire->id) {
    $form->tab_start('Preview');
        echo '<a href="'. $questionnaire->get_url() .'" target="_blank">'. $questionnaire->get_url() .'</a>';
}

$form->tab_start('Share');
    $options = $roles->as_options(['name_column' => 'role', 'please_select' => false]);
    echo $form->multiselect('Select roles', 'shared_with_groups[]', $options);

$form->tab_start('Settings');
    echo $form->yes_or_no('Has expiration', 'expiry', ($questionnaire->id && $questionnaire->expiry));
    echo $form->daterangepicker('Display range', 'start_date', 'end_date');
    echo $form->yes_or_no('Display per group', 'pagination');
    echo $form->yes_or_no('Store answers', 'store_answer');
    echo $form->yes_or_no('Result on completion', 'show_score', ($new ? false : null), [], ['popover' => 'On completion of the survey, show the user their result, if applicable.']);
    echo $form->yes_or_no('PDF download', 'result_pdf_download', ($new ? false : null));
    echo $form->select('Document template', 'result_template_id', $template_options);
    echo $form->yes_or_no('Is backend', 'is_backend');
    echo $form->yes_or_no('Show thank you', 'display_thank_you');
    echo $form->combobox('Thank you page', 'thank_you_page_id', $pages);

    echo $form->yes_or_no('Course selector',   'has_course_selector',    ($new ? false : null), [], ['popover' => 'Display a course selector at the start of the survey']);
    echo $form->yes_or_no('Schedule selector', 'has_schedule_selector',  ($new ? false : null), [], ['popover' => 'Display a schedule selector at the start of the survey']);

    echo $form->yes_or_no('Stock selector',    'has_stock_selector',     ($new ? false : null), [], ['popover' => 'For pre-checks. Display a multiselect, for picking stock items. The user can answer each question once per stock item.']);
    echo $form->input('Stock selector text',   'stock_selector_text',    null,                  [], ['popover' => 'Text to display next to the stock dropdown']);
    echo $form->select('Stock category', 'stock_category_id', $stock_categories);

$form->tab_start('Responses');
    include 'questionnaire_responses.php';

echo $form->tabs();

echo $form->action_buttons();
echo $form->end();
?>

<script>
    $('#questionnaire-builder-expiry').find(':input').on('change', function() {
        const has_expiry = (document.getElementById('questionnaire-builder-expiry-yes').checked);
        $('#questionnaire-builder-display_range').parents('.form-group')
            // Hide the section, if expiration date is not used
            .toggleClass('hidden', !has_expiry)
            // Disable the hidden fields, so they are not sent to the server
            .find(':input').prop('disabled', !has_expiry);
    }).change();

    // Toggle visibility of other stock-related fields, depending on the "has stock selector" field.
    $('#questionnaire-builder-has_stock_selector').find(':input').on('change', function() {
        const has_stock_selector = (document.getElementById('questionnaire-builder-has_stock_selector-yes').checked);

        $('#questionnaire-builder-stock_selector_text, #questionnaire-builder-stock_category_id')
            .parents('.form-group')
            .toggleClass('hidden', !has_stock_selector);
    }).change();
</script>
