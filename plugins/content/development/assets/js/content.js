$.fn.serializeControls = function() {
    var data = {};

    function buildInputObject(arr, val) {
        if (arr.length < 1)
            return val;
        var objkey = arr[0];
        if (objkey.slice(-1) == "]") {
            objkey = objkey.slice(0,-1);
        }
        var result = {};
        if (arr.length == 1){
            result[objkey] = val;
        } else {
            arr.shift();
            var nestedVal = buildInputObject(arr,val);
            result[objkey] = nestedVal;
        }
        return result;
    }

    $.each(this.serializeArray(), function() {
        var val = this.value;
        var c = this.name.split("[");
        if ((this.name.indexOf('title') > -1 && this.name.indexOf('title')) || (this.name.indexOf('text') > -1 && this.name.indexOf('text')) ) {
            var title_obj = $('[name="' + this.name+  '"]');//$(this.attr());
            if (title_obj.hasClass('ckeditor') || title_obj.hasClass('ckeditor-simple')) {
                if (CKEDITOR.instances[title_obj.attr('id')]) {
                    val = CKEDITOR.instances[title_obj.attr('id')].getData();
                } else {
                    val = title_obj.val();
                }
            }
        }
        var a = buildInputObject(c, val);
        $.extend(true, data, a);
    });

    return data;
}

/** Adding a topic **/
// By pressing enter on the name field
$('.content-topic-add-name').on('keydown', function(ev) {
    if (ev.keyCode == 13) {
        // If this is inside a larger form, don't submit it
        ev.preventDefault();

        // Find the corresponding add button and click it. (Effectively submit this section as though it were a form.)
        $(ev.target).parents('.content-topic-add-form').find('.content-topic-add-btn').click();
    }
});

// By clicking the add button
$('#content-tree').on('click', '.content-topic-add-btn', function(ev) {
    // Don't submit the parent form
    ev.preventDefault();

    // This uses a div, rather than an actual form, because it can potentially be embedded inside other form tags.
    var $form = $(this).parents('.content-topic-add-form');
    var name = $form.find('.content-topic-add-name').val();
    var parent_id = $form.find('.content-topic-add-parent_id').val();

    if (!name) {
        alert('Please enter a topic name.');
        return false;
    }

    var form_data = {
        depth: $form.find('.content-topic-add-depth').val() + 1,
        name: name,
        order: $form.parents('.content-topic-subtopics').find('.content-topic').length + 1,
        parent_id: parent_id
    };

    // These properties are only saved to the top-level item
    if (!parent_id) {
        form_data.allow_skipping = +$('#content-allow_skipping').is(':checked');
        form_data.label = $('#content-label').val();
        form_data.available_days_before = $('#content-available_days_before').val();
        form_data.available_days_after = $('#content-available_days_after').val();
    }

    $.ajax({url: '/admin/content/ajax_add_content', data: form_data}).done(function(data) {
        // Display notification
        $('#content-alert_area').add_alert(data.message, data.success ? 'success popup_box' : 'danger popup_box');

        // if successful, add HTML for new topic
        if (data.success) {
            $form.parent().find('> .content-topics-list').append(data.html);
            $form.find(':input:not(:hidden)').val('');

            // Update the counter
            var $counter = $form.parents('li').find('.content-tree-subtopic-counter:first');
            update_content_subsection_counter($counter, +1);
        }

        // If this is the first item added, set the master ID.
        var $master_id = $('#content-master_id');
        if (!$master_id.val()) {
            $master_id.val(data.parent_id);
        }
    });
});

/** Updating a topic via the modal **/
// Pass the ID from the button that opened the modal into the modal
$('#content-add-modal').on('show.bs.modal', function(ev) {

    // Check that the "add modal" is being opened, not a sub modal.
    if ($($(ev.relatedTarget).data('target')).attr('id') == 'content-add-modal') {

        var id = $(ev.relatedTarget).data('id');
        $('#content-add-modal-submit').data('id', id);

        // Fetch existing data
        $.ajax('/admin/content/ajax_get_content/' + id).done(function (data) {
            $('#content-add-modal').find('.hidden--pdf, .hidden--questionnaire, .hidden--text, .hidden--video').addClass('hidden');
            $('#content-add-modal-name').val(data.name);
            $('#content-add-modal-order').val(data.order);
            $('.content-add-modal-type[data-type="text"]').prop('checked', true); // Default, if there is no type_id
            $('.content-add-modal-type[value="' + data.type_id + '"]').prop('checked', true);
            toggle_content_modal_fields_by_type();
            $('#content-add-modal-file_id').val(data.file_id);
            $('#content-add-modal-file_url').val(data.file_url);
            $('#content-add-modal-file_url_hidden').val(data.file_url);
            $('#content-add-modal-text').val(data.text);
            CKEDITOR.instances["content-add-modal-text"].setData(data.text);
            $('#content-add-modal-available_from').val(data.available_from).change();
            $('#content-add-modal-available_to').val(data.available_to).change();
            $('#content-add-modal-available_days_before').val(data.available_days_before).change();
            $('#content-add-modal-available_days_after').val(data.available_days_after).change();
            if (data.shuffle_groups) {
                $('#content-shuffle-groups').prop('checked', true);
            }
            if (data.shuffle_groups) {
                $('#content-shuffle-questions').prop('checked', true);
            }
            $('#content-add-modal-duration').val(data.duration == 0 ? '' : data.duration_formatted);
            $('#content-add-modal-learning_outcomes').val(data.learning_outcome_ids).multiselect('refresh');
            $('#content-add-modal-survey_id').val(data.survey_id || '');
            $('#content-add-modal-survey-wrapper').html(data.survey_html || '');
        });
    }
});

// Save when the modal fields are changed or submit button is clicked
$('.content-add-modal-autosave').on('change', function() { content_modal_autosave(false) });
$('#content-add-modal-submit'  ).on('click',  function() { content_modal_autosave(true)  });

function content_modal_autosave(dismiss)
{
    dismiss = dismiss || false;

    var $modal  = $('#content-add-modal');
    var id      = $('#content-add-modal-submit').data('id');
    var file_id = +$('#content-add-modal-file_id').val();
    var data = $('#questionnaire-wrapper').find(':input').serializeControls();
    console.log(data);
    var form_data = {
        available_from: $('#content-add-modal-available_from').val(),
        available_to:   $('#content-add-modal-available_to').val(),
        available_days_before: $('#content-add-modal-available_days_before').val(),
        available_days_after: $('#content-add-modal-available_days_after').val(),
        duration:  $('#content-add-modal-duration').val(),
        file_id:   file_id,
        file_url:  file_id ? null :  $('#content-add-modal-file_url').val(),
        learning_outcome_ids: $('#content-add-modal-learning_outcomes').val() || false,
        name:      $('#content-add-modal-name').val(),
        survey_id: $('#content-add-modal-survey_id').val(),
        survey_data: data,
        shuffle_questions : $('#content-tree-settings').find('#content-shuffle-questions').val(),
        shuffle_groups : $('#content-tree-settings').find('#content-shuffle-groups').val(),
        text:      CKEDITOR.instances['content-add-modal-text'].getData(),
        type_id:   ($modal.find('.content-add-modal-type:checked').val() || '')
    };
    console.log('here');
    // Don't blackout the screen every time a field is changed
    window.disableScreenDiv.hide = false;

    // Save the content
    $.post('/admin/content/ajax_save_content/'+id, form_data).done(function(data) {
        window.disableScreenDiv.hide = true;
        // Display notification
        if (dismiss || !data.success) {
            $('#content-alert_area').add_alert(data.message, data.success ? 'success popup_box' : 'danger popup_box');
        }

        if (data.success) {
            // Update the details in the list screen
            $('.content-topic-name[data-id="' + id + '"]').text(data.name);
            var $details = $('.content-topic-details[data-id="'+id+'"]');
            $details.find('.content-topic-duration').text(data.content.duration_formatted);
            $details.find('.content-topic-duration').text(data.content.duration_formatted);
            $details.find('[class*="icon-"]').attr('class', 'icon-'+data.content.icon);
            $details.removeClass('hidden');

            $('#content-add-modal-survey_id').val(data.survey_id);

            // Change the state of the modal toggle
            var $modal_toggle = $('.content-topic-add-modal-trigger[data-id="'+id+'"]');
            $modal_toggle.find('.add_mode').addClass('hidden');
            $modal_toggle.find('.edit_mode').removeClass('hidden');

            // Reset the modal
            if (data.success && dismiss) {
                $modal.find('[type="text"], textarea, select, .form-datepicker-iso').val('');
                $modal.find(':radio, :checkbox').prop('checked', false);
                $('.content-add-modal-type[data-type="text"]').prop('checked', true);
                $('#content-add-modal-learning_outcome').multiselect('refresh');
                toggle_content_modal_fields_by_type();
                $modal.find('.hidden--pdf, .hidden--questionnaire, .hidden--text, .hidden--video').addClass('hidden');
                $modal.modal('hide');
            }
        }
    }).fail(function() {
        window.disableScreenDiv.hide = true;
    });
}

$('#content-allow_skipping, ' +
    '#content-label,' +
    '#content-available_days_before, ' +
    '#content-available_days_after, ' +
    '#content-shuffle-groups, ' +
    '#content-shuffle-questions, ' +
    '#content-add-modal-available_from,' +
    '#content-add-modal-available_to').on('change', function() {
    var master_id = $('#content-master_id').val();
    var data = {
        allow_skipping: +$('#content-allow_skipping').is(':checked'),
        label: $('#content-label').val(),
        shuffle_questions: $('#content-shuffle-questions').is(':checked'),
        shuffle_groups: $('#content-shuffle-groups').is(':checked'),
        available_days_before: $('#content-available_days_before').val(),
        available_days_after: $('#content-available_days_after').val()
    };
    if (master_id) {
        window.disableScreenDiv.hide = false;
        $.post('/admin/content/ajax_save_content/'+master_id, data).done(function() {
            window.disableScreenDiv.hide = true;
        });
    }
});

$('#content-label').on('keyup', function() {
    var label = this.value || 'Section';
    $('.content-topic-name').attr('data-section_label', label).data('label', label);
});

// Toggle field visibility, depending on the content type
$('.content-add-modal-type').on('change', toggle_content_modal_fields_by_type);

function toggle_content_modal_fields_by_type()
{
    var type = $('.content-add-modal-type:checked').data('type');
    var $modal = $('#content-add-modal');

    $modal.find('.hidden--pdf, .hidden--questionnaire, .hidden--text, .hidden--video').removeClass('hidden');
    $modal.find('.hidden--'+type).addClass('hidden');
}

/** Deleting a topic **/
// Pass the ID from the button that opened the modal into the modal
$('#content-delete-modal').on('show.bs.modal', function(ev) {
    var id = $(ev.relatedTarget).data('id');
    $('#content-delete-modal-confirm').data('id', id);
});

// Delete the topic when the modal is submitted
$('#content-delete-modal-confirm').on('click', function() {
    var id = $(this).data('id');

    $.ajax('/admin/content/ajax_delete_content/'+id).done(function(data) {
        // Display notification
        $('#content-alert_area').add_alert(data.message, data.success ? 'success popup_box' : 'danger popup_box');

        // If successful, remove from the DOM
        if (data.success) {
            var $deleted_topic = $('.content-topic[data-id='+id+']');
            var $counter = $deleted_topic.parents('li').find('.content-tree-subtopic-counter:first');

            $deleted_topic.remove();

            // Update the counter
            update_content_subsection_counter($counter, -1);

            $('#content-delete-modal').modal('hide');
        }
    });
});

function update_content_subsection_counter($counter, difference)
{
    var $amount  = $counter.find('.content-tree-subtopic-counter-amount');

    if ($amount.length) {
        var new_amount = parseInt($amount.html().trim() || 0) + difference;

        $amount.html(new_amount);
        $counter.find('.singular').toggleClass('hidden', new_amount != 1);
        $counter.find('.plural'  ).toggleClass('hidden', new_amount == 1);
    }
}

/** Re-ordering topics **/
$('.content-topics-list').sortable({
    placeholder : '',
    forcePlaceholderSize: '88',
    connectWith : '.content-topics-list',
    items       : '.content-topic',
    update      : function(ev, ui)
    {
        var orders = [];
        $('.content-topic').each(function() {
            orders[this.getAttribute('data-id')] = $(this).index() + 1;
        });

        window.disableScreenDiv.hide = false;
        $.ajax({url: '/admin/content/ajax_save_content_order', data: {orders: orders}}).done(function() {
            window.disableScreenDiv.hide = true;
        });
    }
});

/* Callback function used by the media uploader. */
function add_content_file(file, path, data, uploader)
{
    $('#content-add-modal-file_id').val(data.media_id);
    $('#content-add-modal-file_url').val(data.files[0]).prop('readonly', true);
    $('#content-add-modal-file_url_hidden').val(data.files[0]).prop('readonly', true);
}

$('#content-add-modal-file_url').on('change', function(){
    if ($('#content-add-modal-file_url').val() != $('#content-add-modal-file_url_hidden').val()) {
        $('#content-add-modal-file_url_hidden').val('');
        $('#content-add-modal-file_id').val('');
    }
});

$(document).on(':ib-browse-image-selected', function(ev, file_id) {
    var filename = $(ev.target).find('.filename').html();

    $('#content-add-modal-file_id').val(file_id);
    $('#content-add-modal-file_url').val(filename).prop('readonly', true);
    $('#content-add-modal-file_url_hidden').val(filename).prop('readonly', true);
});