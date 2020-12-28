$(document).ready(function()
{
    var $countdown = $('#my_content-lesson-countdown');

    // Open a different lesson
    $('.my_content-lesson-type-toggle').on('click', function() {
        var type = $(this).data('type');

        // Hide the progress section for quizzes, which have their own buttons
        $('#my_content-lesson-progress').toggleClass('hidden', (type == 'quiz'));
        var content_id = $(this).data('content_id');

        // Load the content
        if (content_id) {
            $.ajax('/admin/content/ajax_get_content/'+content_id).done(function(data) {
                $('#my_content-lesson-text')
                    .attr('data-id', content_id)
                    .data('id', content_id)
                    .removeClass('hidden')
                    .html(data.html);

                // Set the title
                $('#mycontent-lesson-title').text(data.name);

                // Format and autostart the video
                if (data.type == 'video') {
                    var selector = '#content-'+data.id+'-video';
                    if ($(selector).find('video').length) {
                        selector = selector+ ' video';
                    }

                    var player = new Plyr(selector);
                    player.play();
                }
            });
        }

        // Reset the countdown
        $countdown.data('seconds', $(this).data('duration'));
    });

    // Open the last available lesson
    var target = $('#my_content-column-sidebar').find('.panel-heading[aria-expanded="true"]').data('target');
    if($('#content-allow_skipping').val()) {
        $(target).find('.my_content-lesson-type-toggle[data-previous-subsection-complete=1]:last').click();
    } else {
        $(target).find('.my_content-lesson-type-toggle:not(:disabled):last').click();
    }
        setInterval(function() {
            var seconds = $countdown.data('seconds');
            if($('#content-allow_skipping').val() != 1) {
                $('#my_content-next').prop('disabled', seconds > 0);
            } else {
                $('#my_content-next').prop('disabled', false);
            }
            if (seconds >= 0) {
                $countdown.text(seconds_to_time(seconds));
                $countdown.data('seconds', seconds - 1);
            }
        }, 1000);
});

// Marking a lesson as complete
$('.my_content-lesson-complete').on('change', function() {
    var data = {
        is_complete : this.checked ? 1 : 0,
        content_id  : $('#content_id').val(),
        section_id  : $(this).data('content_id')
    };

    var $counter  = $(this).parents('.my_content-section').find('.my_content-section-complete_count');
    var $subsections = $(this).parents('.my_content-section').find('.my-content-section-count_subsections').html();
    var count     = $counter.html() ? +$counter.html().trim() : 0;
    if (count < $subsections) {
        var new_count = this.checked ? count + 1 : count - 1;
    }
    $counter.html(new_count);

    window.disableScreenDiv.hide = false;
    $.ajax({ method: 'post', url: '/admin/content/ajax_save_progress', data: data }).done(function() {
        window.disableScreenDiv.hide = true;
    });
});

// Progressing via the "prev" and "next" buttons
$('#my_content-prev, #my_content-next').on('click', function() {
    var is_next_button     = (this.id != 'my_content-prev');
    var current_content_id = $('#my_content-lesson-text').data('id');
    var $current_checkbox  = $('.my_content-lesson-complete[data-content_id="'+current_content_id+'"]');
    var current_section    = $current_checkbox.data('section');
    var current_subsection = $current_checkbox.data('subsection');

    // Get the checkbox for the subsection to open
    var next_subsection     = is_next_button ? current_subsection + 1 : current_subsection - 1;
    var $next_checkbox      = $('.my_content-lesson-complete[data-section="'+current_section+'"][data-subsection="'+next_subsection+'"]');

    // If there were no more checkboxes in the section, go onto the next or previous section.
    if ($next_checkbox.length == 0) {
        var next_section = is_next_button ? current_section + 1 : current_section - 1;
        var next_subsection_position = is_next_button ? 'first' : 'last';
        $next_checkbox = $('.my_content-lesson-complete[data-section="'+next_section+'"]:'+next_subsection_position);

        // Open the section, if closed
        $next_checkbox.parents('.my_content-section').find('.panel-heading[aria-expanded="false"]').click();
    }

    var $next_lesson_button = $next_checkbox.parents('.my_content-lesson').find('.my_content-lesson-type-toggle');

    if (is_next_button) {
        // Mark this lesson as done
        $current_checkbox.prop('checked', true).change();
    }

    // Open the next lesson
    $next_lesson_button.prop('disabled', false);
    $next_lesson_button.click();
});


$('#my_content-sidebar-toggle-hide').on('click', function() {
    $('#my_content-column-content').removeClass('col-sm-8').addClass('col-sm-12');
    $('#my_content-column-sidebar').addClass('hidden');
    $('#my_content-sidebar-toggle-show').removeClass('hidden');
});

$('#my_content-sidebar-toggle-show').on('click', function() {
    $('#my_content-column-content').removeClass('col-sm-12').addClass('col-sm-8');
    $('#my_content-column-sidebar').removeClass('hidden');
    $('#my_content-sidebar-toggle-show').addClass('hidden');
});
$(document).ready(function()
{
    if (!Date.nowTime) {
        Date.nowTime = function() { return new Date().getTime(); }
    }
    var question_start_time = { start : Date.nowTime() };
    console.log(question_start_time.start);
    $(document).on('click', '#survey-question-next', function()
    {
        var answers = [];
        var survey_id = $('.survey-wrapper').data('id');
        var warn = false;

        //If the form fails validation, show messages and don't continue
        if (!$('#survey-page').validationEngine('validate')) {
            return false;
        }

        $('.survey-question-block').each(function(){
            var $question_block = $(this);
            var answer = {};
            answer.type = $question_block.data('type');
            answer.question_id = $question_block.data('question_id');
            if (answer.type == 'radio' || answer.type == 'yes_or_no') {
                answer.answer_id = $question_block.find('.survey-input:checked').val();
            } else if (answer.type == 'checkbox') {
                answer.answer_id = [];
                $question_block.find('.survey-input:checked').each(function(){
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
            survey_id : survey_id
        }).done(function(results)
        {
            results = JSON.parse(results);
            if (results.question_id != 0)
            {
                $('#survey-question-blocks').data('group',results.group);
                $('#survey-page').load('/survey/' + results.survey_id + ' #survey-page', results, function()
                {
                    $('.survey-input:first').focus();
                });
            }
            else
            {
                $.post('/frontend/surveys/get_survey_download', {survey_id: survey_id, result_id: results.result_id})
                    .done(function(results)
                    {
                        results = JSON.parse(results);

                        $('#survey-download-button').data('redirect', results.page_name ? '/'+results.page_name : '');

                        var redirect = false;
                        if (results.download == true)
                        {
                            $('#download').show();
                            $('#thank-you').hide();
                            $('#download_doc').show();
                        }
                        else if (results.thanks == true && results.page_name != '')
                        {
                            redirect = true;
                        }
                        else
                        {
                            $('#download').hide();
                            $('#thank-you').show();
                            $('#download_doc').hide();
                        }

                        // show score, if applicable
                        $('#survey-complete-result').html(results.score_html);
                        $('#survey-complete-result-wrapper').toggle(results.show_score ? true : false);

                        if (redirect)
                        {
                            location.href = results.page_name ? '/'+results.page_name : '/thank-you.html';
                        }
                        else
                        {
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
    $(document).on('keypress', '[type="text"].survey-input, [type="radio"].survey-input', function(ev)
    {
        if (ev.keyCode == '13')
        {
            $('#survey-question-next').trigger('click');
        }
    });

    $(document).on('click', '#survey-question-prev', function()
    {
        var question_id = $('.survey-question-block').data('question_id');
        var survey_id   = $('.survey-wrapper').data('id');


        $.post('/frontend/surveys/ajax_get_prev_question', {
            question_id: question_id,
            survey_id: survey_id
        }).done(function(results)
        {
            results = JSON.parse(results);
            if (results.question_id != 0)
            {
                $('#survey-page').load('/survey/' + results.id + ' #survey-page', results);
            }
            else
            {
                $('#survey-modal-complete').show();
            }
        });
    });

    $(document).on('click', '#survey-download-button', function(ev)
    {
        ev.preventDefault();
        // Open the download link in new tab
        window.open(this.href);

        // Redirect current page to success page
        window.location = $(this).data('redirect') || '/thank-you.html';
        window.focus();
    });

    $('.survey-modal-close').on('click', function()
    {
        $(this).parents('.survey-modal').fadeOut();
    });
});
