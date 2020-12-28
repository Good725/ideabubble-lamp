// When the "add question" button is clicked...
$(document).on('click', '.questionnaire-question-add-btn[data-type="question"]', function(ev) {
    var $this = $(this);

    // Prevent event firing more than once
    if (!$this.hasClass('adding')) {
        $this.addClass('adding');
        var group_number = $(this).parents('.questionnaire-group-li').data('number');
        var question_number = get_next_number('.questionnaire-question-li');
        console.log(group_number);
        // Clone the question template
        var $clone = $('#questionnaire-question-template').find('> li').clone();

        // Ensure the new question has a number higher than the others.
        set_question_number($clone, question_number, group_number);

        // Add to the list adjacent to the add button that was clicked.
        $this.parents('.form-action-group').prevAll('ul').append($clone);

        $clone.find('.questionnaire-option').each(function(index, element) {
            set_option_number($(element), index + 1);
        });

        setTimeout(function() {
            $this.removeClass('adding');
        }, 10);
        console.log(question_number);
        CKEDITOR.replace( 'questionnaire-builder-question_' + group_number + '_' + question_number, {
            toolbar :
                [
                    [
                        'Bold', 'Italic', 'Underline', 'Strike', '-',
                        'NumberedList', 'BulletedList', '-',
                        'JustifyLeft', 'JustifyCenter', 'JustifyRight', '-',
                        'Image', 'Link', 'Unlink', 'Table', 'SpecialChar', '-',
                        'Undo', 'Redo'
                    ]
                ],
                    height : '100px'
        });
    }
});

$(document).on('click', '.questionnaire-question-clone-btn', function(ev) {
    var $this = $(this);

    // Prevent event firing more than once
    if (!$this.hasClass('adding')) {
        $this.addClass('adding');
        var group_number = $this.parents('.questionnaire-group-li').data('number');
        var initial_question_number = $this.parents('.questionnaire-question-li').data('number');
        console.log(initial_question_number);
        var question_number = get_next_number('.questionnaire-question-li');
        var initial_question_type = $('[name="questions[' + initial_question_number + '][type_id]"]').val();
        // Clone the question template
        var initial_question_text = CKEDITOR.instances['questionnaire-builder-question_' + group_number + '_' + initial_question_number].getData();
        CKEDITOR.instances['questionnaire-builder-question_' + group_number + '_' + initial_question_number].destroy();
        var $clone = $this.closest('.questionnaire-question-li').clone();
        // Ensure the new question has a number higher than the others.
        set_question_number($clone, question_number, group_number);
        // Add to the list adjacent to the add button that was clicked.
        $clone.find('.questionnaire-question-clone-btn').removeClass('adding');
        $clone.find('.questionnaire-option').each(function(index, element) {
            set_option_number($(element), index + 1);
        });
        $clone.find('[name="questions['+question_number+'][type_id]"]').val(initial_question_type).trigger('change');
        $this.closest('.questionnaire-group-questions').append($clone);

        setTimeout(function() {
            $this.removeClass('adding');
        }, 10);
        CKEDITOR.replace( 'questionnaire-builder-question_' + group_number + '_' + initial_question_number, {
            toolbar :
                [
                    [
                        'Bold', 'Italic', 'Underline', 'Strike', '-',
                        'NumberedList', 'BulletedList', '-',
                        'JustifyLeft', 'JustifyCenter', 'JustifyRight', '-',
                        'Image', 'Link', 'Unlink', 'Table', 'SpecialChar', '-',
                        'Undo', 'Redo'
                    ]
                ],
            height : '100px'
        });
        CKEDITOR.replace( 'questionnaire-builder-question_' + group_number + '_' + question_number, {
            toolbar :
                [
                    [
                        'Bold', 'Italic', 'Underline', 'Strike', '-',
                        'NumberedList', 'BulletedList', '-',
                        'JustifyLeft', 'JustifyCenter', 'JustifyRight', '-',
                        'Image', 'Link', 'Unlink', 'Table', 'SpecialChar', '-',
                        'Undo', 'Redo'
                    ]
                ],
            height : '100px'
        });
        CKEDITOR.instances['questionnaire-builder-question_' + group_number + '_' + initial_question_number].setData(initial_question_text);
        CKEDITOR.instances['questionnaire-builder-question_' + group_number + '_' + question_number].setData(initial_question_text);
    }
});

// When the "add group" button is clicked
$('#questionnaire-builder-add-group').on('click', function() {
    // Clone the group template.
    var $clone = $('#questionnaire-group-template').find('> li').clone();

    // Ensure the new group has a number higher than the others.
    set_group_number($clone, get_next_number('.questionnaire-group-li'));

    // Add it to the list.
    $('#questionnaire-wrapper').append($clone);
});
// When the "add prompt" button is clicked
$('#questionnaire-builder-add-prompt').on('click', function() {
    // Clone the prompt template.
    var $this = $(this);

    var $clone = $('#questionnaire-prompt-template').find('> li').clone();
    $this.addClass('adding');

    // Ensure the new group has a number higher than the others.
    console.log(get_next_number('.questionnaire-group-li'));
    var group_number = get_next_number('.questionnaire-group-li');
    set_group_number($clone, get_next_number('.questionnaire-group-li'));
    // Add it to the list.
    $('#questionnaire-wrapper').append($clone);
    setTimeout(function() {
        $this.removeClass('adding');
    }, 10);
    CKEDITOR.replace( 'questionnaire-builder-prompt_' + group_number, {
        toolbar :
            [
                [
                    'Bold', 'Italic', 'Underline', 'Strike', '-',
                    'NumberedList', 'BulletedList', '-',
                    'JustifyLeft', 'JustifyCenter', 'JustifyRight', '-',
                    'Image', 'Link', 'Unlink', 'Table', 'SpecialChar', '-',
                    'Undo', 'Redo'
                ]
            ],
        height : '100px'
    });
});

$(document).on('click', '.questionnaire-group-clone-btn', function() {
    var $this = $(this);

    // Prevent event firing more than once
    if (!$this.hasClass('adding')) {
        $this.addClass('adding');
        var group_number = $this.parents('.questionnaire-group-li').data('number');
        var new_group_number = get_next_number('.questionnaire-group-li');

        var ck_editor_texts = [];
        $.each(CKEDITOR.instances, function(id, editor) {
            if(id.includes('questionnaire-builder-question_' + group_number)) {
                ck_editor_texts[id] = editor.getData();
            }
        });
        var $clone =  $this.parents('.questionnaire-group-li').clone();
        $clone.find('.questionnaire-group-clone-btn').removeClass('adding');
        set_group_number($clone, new_group_number);
        $clone.find('[name="groups[' + new_group_number + '][id]"]').val('');
        $('#questionnaire-wrapper').append($clone);
        for(var id in ck_editor_texts) {
            if(id.includes('questionnaire-builder-question_')) {
                var ids = id.split('_');
                var new_group_id = '';
                if (ids[1] != 0) {
                    new_group_id  = ids[0] + '_' + new_group_number + '_' + ids[2];
                    CKEDITOR.replace( new_group_id, {
                        toolbar :
                            [
                                [
                                    'Bold', 'Italic', 'Underline', 'Strike', '-',
                                    'NumberedList', 'BulletedList', '-',
                                    'JustifyLeft', 'JustifyCenter', 'JustifyRight', '-',
                                    'Image', 'Link', 'Unlink', 'Table', 'SpecialChar', '-',
                                    'Undo', 'Redo'
                                ]
                            ],
                        height : '100px'
                    });

                    CKEDITOR.instances[id].setData(ck_editor_texts[id]);
                    CKEDITOR.instances[new_group_id].setData(ck_editor_texts[id]);
                }

            }
        }
        for(var id in ck_editor_texts) {
            if(id.includes('questionnaire-builder-question_' + group_number)) {
                var ids = id.split('_');
                $.each($('#cke_questionnaire-builder-question_' + new_group_number+'_' + ids[2]), function(key, el){
                    if ($(el).hasClass('cke_editor_questionnaire-builder-question_' + group_number + '_' + ids[2])) {
                        $(el).remove();
                    }
                });



            }
        }
        setTimeout(function() {
            $this.removeClass('adding');
        }, 10);


    }
});

// When the answer options are opened...
$(document).on('show.bs.collapse', '.questionnaire-question-answer-options', function() {
    var $question = $(this).parents('.questionnaire-question-li');
    var type = $question.find('.questionnaire-builder-question-type').find(':selected').data('type');

    // Enable the fields relevant to the question type. Disable and hide otherwise.
    $question.find('.questionnaire-question-answer-options-type').addClass('hidden')
        .find(':input:not(button)').prop('disabled', true);
    $question.find('.questionnaire-question-answer-options-type[data-type="' + type + '"]').removeClass('hidden')
        .find('[name]').prop('disabled', false);
});



function toggle_questions($el, reset_scores) {
    console.log('hererere');
    const $question = $el.parents('.questionnaire-question-li');
    const $group = $question.closest('.questionnaire-group-li');
    const $options = $question.find('.questionnaire-question-answer-options');
    const $toggle = $question.find('[data-toggle="collapse"]');
    const type = $el.find(':selected').data('type');
    const $total_mark = $question.find('.questionnaire-question-max_score');
    const group_number = $group.data('number');
    const question_number = $question.data('number');
    if (group_number !== undefined && question_number !== undefined && !CKEDITOR.instances['questionnaire-builder-question_' + group_number + '_' + question_number]) {
        CKEDITOR.replace( 'questionnaire-builder-question_' + group_number + '_' + question_number, {
            toolbar :
                [
                    [
                        'Bold', 'Italic', 'Underline', 'Strike', '-',
                        'NumberedList', 'BulletedList', '-',
                        'JustifyLeft', 'JustifyCenter', 'JustifyRight', '-',
                        'Image', 'Link', 'Unlink', 'Table', 'SpecialChar', '-',
                        'Undo', 'Redo'
                    ]
                ],
            height : '100px'
        });

    }
    // Show/hide the dropdown toggle depending on the type.
    const types_that_expand = JSON.parse($('#questionnaire-builder-types_that_expand').val());
    const has_expandable_section = (types_that_expand.indexOf(type)) > -1;

    const has_options = (['checkbox', 'radio', 'select', 'yes_or_no'].indexOf(type)) > -1;

    $options.collapse(has_options ? 'show' : 'hide').trigger('show.bs.collapse');
    $toggle.toggleClass('hidden', !has_expandable_section);
    if (type === 'radio' || type === 'checkbox'  || type ==='yes_or_no') {
        if(reset_scores) {
            var options_marks = $question.find('.answer-score');
            $.each(options_marks, function(key, option_mark){
                $(option_mark).removeAttr('readonly').val(0);
            });
        }
        $total_mark.attr('readonly', true);
    } else {
        $total_mark.removeAttr('readonly');
    }
}

function toggle_prompts($el) {
    const $prompt = $el.parents('.questionnaire-prompt-li');
    const $group = $prompt.closest('.questionnaire-group-li');
    const group_number = $group.data('number');
    const group_type = $group.data('type');

    if (group_number !== undefined && group_number !== 0 && group_type == 'prompt' && !CKEDITOR.instances['questionnaire-builder-prompt_' + group_number]) {
        CKEDITOR.replace( 'questionnaire-builder-prompt_' + group_number, {
            toolbar :
                [
                    [
                        'Bold', 'Italic', 'Underline', 'Strike', '-',
                        'NumberedList', 'BulletedList', '-',
                        'JustifyLeft', 'JustifyCenter', 'JustifyRight', '-',
                        'Image', 'Link', 'Unlink', 'Table', 'SpecialChar', '-',
                        'Undo', 'Redo'
                    ]
                ],
            height : '100px'
        });
    }
}

// When the question type is changed...
$(document).on('change', '.questionnaire-builder-question-type', function() {
    toggle_questions($(this), true);
});
$(document).ready(function(){
    $('.questionnaire-builder-question-type').each(function(){
        toggle_questions($(this), false);
    });
    $('.prompt-text').each(function(){
        toggle_prompts($(this));
    });
});


/** Deleting a question **/
// Pass the ID from the button that opened the modal into the modal
$('#question-delete-modal').on('show.bs.modal', function(ev) {
    var number = $(ev.relatedTarget).data('number');
    $('#question-delete-modal-confirm').data('number', number);
});

// Delete the question when the modal is submitted
$('#question-delete-modal-confirm').on('click', function() {
    var number = $(this).data('number');
    $('.questionnaire-question-li[data-number='+number+']').remove();

    $('#question-delete-modal').modal('hide');
});

/** Deleting a group todo: genericise the delete workflow **/
// Pass the ID from the button that opened the modal into the modal
$('#question-group-delete-modal').on('show.bs.modal', function(ev) {
    var number = $(ev.relatedTarget).data('number');
    $('#question-group-delete-modal-confirm').data('number', number);
});

// Delete the group when the modal is submitted
$('#question-group-delete-modal-confirm').on('click', function() {
    var number = $(this).data('number');
    $('.questionnaire-group-li[data-number='+number+']').remove();

    $('#question-group-delete-modal').modal('hide');
});
/** Delete end **/

// When the "add option" button is clicked...
$(document).on('click', '.questionnaire-add-option', function() {
    var $question = $(this).parents('.questionnaire-question-li');
    var type = $question.find('.questionnaire-builder-question-type :selected').data('type');
    var question_number = $question.data('number');

    // Ensure the new row has a number higher than the others.
    var option_numbers = $question.find('[data-option_number]').map(function() { return $(this).data('option_number'); }).get();
    option_numbers.push(0); // ensure there is at least one number in the array.
    var option_number = Math.max.apply(Math, option_numbers) + 1;

    var $clone = $('#questionnaire-question-template')
        .find('.questionnaire-question-answer-options-type[data-type="'+type+'"]')
        .find('.questionnaire-option').first().clone();

    set_question_number($clone, question_number);
    set_option_number($clone, option_number);

    $question.find('.questionnaire-question-answer-options-type[data-type="'+type+'"]').append($clone);
});

// Delete an option.
$(document).on('click', '.questionnaire-remove-option', function() {
    $(this).parents('.questionnaire-option').remove();
});

$(document).on('change','.answer-score', function(){
   var el = $(this);
   var el_id = el.attr('id');
   var total_mark = 0;
   var $total_mark = el.closest('.question').find('.questionnaire-question-max_score');
    if (el.hasClass('answer-radio') || el.hasClass('answer-select') || el.hasClass('answer-toggle')) {
        var radios = [];
        if (el.hasClass('answer-radio')) {
            radios = el.closest('.question').find('.answer-score.answer-radio');
        } else if(el.hasClass('answer-select')) {
            radios = el.closest('.question').find('.answer-score.answer-select');
        } else {
            radios = el.closest('.question').find('.answer-score.answer-toggle');
        }
        if (el.val() != 0 && el.val()  != '') {
            $.each(radios, function (key, radio){
                var $radio = $(radio);
                if($radio.attr('id') !== el_id) {
                    $radio.val(0).attr('readonly',true);
                } else {
                    var mark = parseInt($radio.val());
                    console.log(mark);
                    total_mark = isNaN(mark) ?  0 : mark;
                }
            });
        } else {
            $.each(radios, function (key, radio) {
                var $radio = $(radio);
                $radio.val(0).removeAttr('readonly');
                total_mark = 0;
            });
        }

   } else {
       var checkboxes = el.closest('.question').find('.answer-score.answer-checkbox');
       $.each(checkboxes, function (key, checkbox){
           var $checkbox = $(checkbox);

           var mark = parseInt($checkbox.val());
           total_mark += isNaN(mark) ?  0 : mark;
       });
   }
    $total_mark.val(total_mark).attr('readonly', true);
});

$('#questionnaire-wrapper').sortable({
    connectWith: '.questionnaire-group-questions',
    placeholder : '',
    forcePlaceholderSize: '58',
    dropOnEmpty: true,
    containment: 'parent',
    tolerance: 'intersect',
    items       : '.questionnaire-li',
    update      : function(ev, ui) {
        $('#questionnaire-wrapper').find('.questionnaire-question-li').each(function() {
            set_question_number($(this), $(this).index() + 1);
        });

        $('.questionnaire-group-li').each(function() {
            set_group_number($(this), $(this).index() + 1);
        });
    }
});

$('ul.questionnaire-question-answer-options-type').sortable({
    placeholder : '',
    forcePlaceholderSize: '58',
    items       : '.questionnaire-option',
    update      : function(ev, ui) {
        $('#questionnaire-wrapper').find('.questionnaire-option').each(function() {
            var number = $(this).index() + 1;
            set_option_number($(this), number);
        });
    }
});

function get_next_number(selector)
{
    let numbers = $(selector).map(function() {
        return $(this).data('number');
    }).get();

    numbers.push(0); // Ensure there is at least one number in the array.

    return Math.max.apply(Math, numbers) +1;
}

function get_current_number(selector) {
    let numbers = $(selector).map(function() {
        return $(this).data('number');
    }).get();
    numbers.push(0);
    return Math.max.apply(Math, numbers);
}

function set_group_number($group, number)
{
    const old_number = $group.attr('data-number') || 0;
    // Update data attributes
    $group.data('number', number).attr('data-number', number);
    $group.find('[data-number]').each(function() {
        $(this).attr('data-number', number).data('number', number);
    });

    // Update field names
    $group.find('[name*="groups\\[' + old_number + '\\]"]').each(function() {
        this.name = this.name.replace('groups['+old_number+']', 'groups[' + number + ']');
        $(this).prop('disabled', false).removeAttr('disabled');
        $(this).parents('.disabled').removeClass('disabled');
    });
    $group.find('[name*="prompts\\[' + old_number + '\\]"]').each(function() {
        this.name = this.name.replace('prompts['+old_number+']', 'prompts[' + number + ']');
        $(this).prop('disabled', false).removeAttr('disabled');
        $(this).parents('.disabled').removeClass('disabled');
    });

    // Update field values
    $group.find('.questionnaire-question-group_number').val(number);
    $group.find('.questionnaire-group-order').val(get_next_number('.questionnaire-li'));
    // Update IDs
    $group.find('[id*="-' + old_number + '-"]').each(function() {
        this.id = this.id.replace('-' + old_number + '-', '-' + number + '-');
    });

    $group.find('[id*="_' + old_number + '_"]').each(function() {
        this.id = this.id.replace('_' + old_number + '_', '_' + number + '_');
    });
    $group.find('[id*="_' + old_number + '"]').each(function() {
        this.id = this.id.replace('_' + old_number, '_' + number);
    });

    // Update data attributes that reference the IDs
    $group.find('[data-target*="group-'+old_number+'-"]').each(function() {
        $(this).data('target', $(this).data('target').replace('group-'+old_number+'-', 'group-'+number+'-'));
        $(this).attr('data-target', $(this).attr('data-target').replace('group-'+old_number+'-', 'group-'+number+'-'));
    });

}

function set_question_number($question, number, group_number)
{
    group_number = group_number || $(this).parents('.questionnaire-group-li').data('number');

    const old_number = $question.attr('data-number') || 0;

    $question.data('number', number).attr('data-number', number);
    $question.find('.questionnaire-question-answer-options').attr('id', 'questionnaire-question-' + number + '-collapsible');
    $question.find('.questionnaire-question-heading').attr('data-target', '#questionnaire-question-' + number + '-collapsible');
    $question.find('.questionnaire-builder-question').attr('id', 'questionnaire-builder-question_' + group_number + '_' + number);

    $question.find('.questionnaire-question-order').val(number);
    $question.find('.questionnaire-question-group_number').val(group_number);

    $question.find('[name*="questions\\[' + old_number + '\\]"]').each(function() {
        this.name = this.name.replace('questions['+old_number+']', 'questions[' + number + ']');
        $(this).prop('disabled', false).removeAttr('disabled');
        $(this).parents('.disabled').removeClass('disabled');
    });

    $question.find('[id*="question-' + old_number + '-"]').each(function() {
        this.id = this.id.replace('question-' + old_number, 'question-' + number + '-');
    });

    $question.find('[data-number="' + old_number + '"]').each(function() {
        $(this).attr('data-number', number).data('number', number);
    });

    $question.find('[data-target*="question-'+old_number+'-"]').each(function() {
        $(this).data('target', $(this).data('target').replace('question-'+old_number+'-', 'question-'+number+'-'));
        $(this).attr('data-target', $(this).attr('data-target').replace('question-'+old_number+'-', 'question-'+number+'-'));
    });
}

function set_option_number($option, number)
{
    const old_number = $option.attr('data-option_number') || 0;

    $option.find('.questionnaire-option-order').val(number);

    $option.attr('data-option_number', number).data('option_number', number);

    $option.find('[name*="answer_options\\]\\['+old_number+'\\]"]').each(function() {
        this.name = this.name.replace('answer_options]['+old_number+']', 'answer_options][' + number + ']');
    });

    $option.find('[data-option_number="0"]').each(function() {
        $(this).attr('data-option_number', number).data('option_number', number);
    });
}