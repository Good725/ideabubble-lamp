var rows_db = [];

$(document).ready(function ()
{
    $('form').find('button[data-content]').popover({placement: 'top', trigger: 'hover'});
    $('[rel="popover"]').popover();
    survey_initialisation();
});

function add_item_to_rows_db(table_name, item_id)
{
    var row_id = _generate_row_id(table_name, item_id);
    var tr_id = (_is_row_in_db(row_id)) ? null : _generate_tr_id();

    if (tr_id != null)
    {
        rows_db.push(row_id);
    }

    return tr_id;
}

function _generate_row_id(table_name, id)
{
    return table_name + '-' + id;
}

function _is_row_in_db(row_id)
{
    return (rows_db.indexOf(row_id) != -1);
}

function _generate_tr_id()
{
    return 'row-id-' + (rows_db.length);
}

function is_item_in_rows_db(table_name, item_id)
{
    return _is_row_in_db(_generate_row_id(table_name, item_id));
}

function survey_initialisation(){
    if (!Date.nowTime) {
        Date.nowTime = function () {
            return new Date().getTime();
        }
    }
    var question_start_time = {start: Date.nowTime()};
    console.log(question_start_time.start);
    $(document).on('click', '#survey-question-next', function () {
        var answers = [];
        var survey_id = $('.survey-wrapper').data('id');
        var course_id = ($('.survey-wrapper').data('course-id') !== null) ? $('.survey-wrapper').data('course-id') : null;
        var booking_id = $('.linked-bookings-dataTable').find('.selected[data-booking_id]').first().data('booking_id') || null;
        var warn = false;

        if (!$('#survey-page').validationEngine('validate')) {
            return false;
        }

        $('.survey-question-block').each(function () {
            var $question_block = $(this);
            var answer = {};
            answer.type = $question_block.data('type');
            answer.question_id = $question_block.data('question_id');
            if (answer.type == 'radio' || answer.type == 'yes_or_no') {
                answer.answer_id = $question_block.find('.survey-input:checked').val();
            } else if (answer.type == 'checkbox') {
                answer.answer_id = [];
                $question_block.find('.survey-input:checked').each(function () {
                    answer.answer_id.push(this.value);
                });
            } else {
                answer.answer_id = $question_block.find('.survey-input').val();
            }

            answer.group_id = $('#survey-question-blocks').data('group');
            answer.question_time = Date.nowTime() - question_start_time.start;

            answers.push(answer);
        });

        $.post('/frontend/surveys/ajax_get_next_question', {
            answers: answers,
            survey_id: survey_id,
            course_id: course_id,
            booking_id : booking_id
        }).done(function (results) {
            results = JSON.parse(results);
            if (results.question_id != 0) {
                $('#survey-question-blocks').data('group', results.group);
                results.booking_id = $('.linked-bookings-dataTable').find('.selected[data-booking_id]').first().data('booking_id') || null;
                var survey_page_load = (results.survey_backend == '0') ? location.href + ' #survey-page' : '/admin/surveys/ajax_display_survey_details/';
                $('#survey-page').load(survey_page_load, results, function () {
                    $('.survey-input:first').focus();
                });
            } else {
                $.post('/frontend/surveys/get_survey_download', {survey_id: survey_id, result_id: results.result_id})
                    .done(function (results) {
                        results = JSON.parse(results);

                        $('#survey-download-button').data('redirect', results.page_name ? '/'+results.page_name : '');

                        var redirect = false;
                        if (results.download == true) {
                            $('#download').show();
                            $('#thank-you').hide();
                            $('#download_doc').show();
                        } else if (results.thanks == true && results.page_name != '') {
                            redirect = true;
                        } else {
                            $('#download').hide();
                            $('#thank-you').show();
                            $('#download_doc').hide();
                        }

                        // show score, if applicable
                        $('#survey-complete-result').html(results.score_html);
                        $('#survey-complete-result-wrapper').toggle(results.show_score);

                        if (redirect) {
                            location.href = results.page_name ? '/'+results.page_name : '/thank-you.html';
                        } else {
                            var $modal = $('#survey-modal-complete');
                            $modal.show();
                            $modal.find('button.survey-modal-close').focus();
                        }
                    });
            }
            question_start_time.start = Date.nowTime();
        });
    });

    // Move onto the next question if the user hits enter on an input field
    $(document).on('keypress', '[type="text"].survey-input, [type="radio"].survey-input', function (ev) {
        if (ev.keyCode == '13') {
            $('#survey-question-next').trigger('click');
        }
    });

    $(document).on('click', '#survey-question-prev', function () {
        var question_id = $('.survey-question-block').data('question_id');
        var survey_id = $('.survey-wrapper').data('id');

        $.post('/frontend/surveys/ajax_get_prev_question', {
            question_id: question_id,
            survey_id: survey_id
        }).done(function (results) {
            results = JSON.parse(results);
            if (results.question_id != 0) {
                results.booking_id = $('.linked-bookings-dataTable').find('.selected[data-booking_id]').first().data('booking_id') || null;
                var survey_page_load = (results.survey_backend == '0') ? location.href + ' #survey-page' : '/admin/surveys/ajax_display_survey_details/';
                $('#survey-page').load(survey_page_load + ' #survey-page', results);
            } else {
                $('#survey-modal-complete').show();
            }
        });
    });

    $(document).on('click', '#survey-download-button', function (ev) {
        ev.preventDefault();
        // Open the download link in new tab
        window.open(this.href);

        // Redirect current page to success page
        window.location = $(this).data('redirect') || '/thank-you.html';
        window.focus();
    });

    $(document).on('click', '.survey-modal-close',function () {
        $(this).parents('.survey-modal').fadeOut();
    });

    $(document).on('click', '#survey-modal-complete .survey-modal-close',function () {
        // Survey finished
        $('#survey_booking_wrapper').html('');
    });
}
