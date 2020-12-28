(function() {
    const $wrapper = $('#safety-precheck-quiz-wrapper');

    // When the "submit precheck" button is clicked, open a modal for creating a new precheck
    $('#safety-precheck-add-btn').on('click', function() {
        // Start session variable
        $.ajax('/admin/safety/start_precheck_response').done(function() {
            // Clear the form
            $wrapper.html('');
            $('#safety-precheck-form-id').val('');
            $('.precheck-type-toggle').prop('checked', false);
            $('.precheck-modal-buttons').addClass('hidden');
            $('#safety-precheck-modal').modal();
        });
    });

    // When the edit button is clicked, open the modal with details pre-loaded
    $(document).on('click', '.safety-precheck-edit', function(ev) {
        ev.preventDefault();
        const survey_id   = $(this).data('survey_id');
        const precheck_id = $(this).data('id');

        // Put the data in a session variable
        $.ajax('/admin/safety/start_precheck_response/'+precheck_id).done(function() {
            $wrapper.html('');
            $('#safety-precheck-form-id').val(precheck_id);
            // Open the selected precheck
            $('.precheck-type-toggle[value="'+survey_id+'"]').prop('checked', true).change();
            $('.precheck-modal-buttons').addClass('hidden');
            $('#safety-precheck-modal').modal();
        });
    });

    // When the top-level precheck (car, farm) is selected/changed...
    $('.precheck-type-toggle').on('change', function () {
        const id = parseInt($('.precheck-type-toggle:checked').val());

        // Track this as the only page
        $('#precheck-modal-pages').data('pages', [id]).attr('data-pages', [id]);

        // Open the first page
        change_page(null, function() {
            // Different buttons depending on whether this is multi-page or not
            const has_children = $wrapper.find('.precheck-form').data('has_children');
            $('#precheck-modal-buttons-single').toggleClass('hidden', has_children);
            $('#precheck-modal-buttons-paginated').toggleClass('hidden', !has_children);

            // If "yes" is answered to a queston with child surveys, add the children as pages
            let paginated_prechecks = [id];
            $wrapper.find('.survey-input[data-child_id][value="yes"]:checked').each(function(i, element) {
                paginated_prechecks.push($(element).data('child_id'));
            });
            $('#precheck-modal-pages').data('pages', paginated_prechecks).attr('data-pages', paginated_prechecks);
            $('#precheck-modal-pagination-total').html(paginated_prechecks.length);
            update_pagination(id);
        });
    });

    // Keep track of prechecks to display across each page
    $wrapper.on('change', '.survey-input[data-child_id]', function () {
        // Main precheck
        const current_id = parseInt($('.precheck-type-toggle:checked').val());
        let paginated_prechecks = [current_id];

        // Each relevant child precheck
        $wrapper.find('.survey-input[data-child_id][value="yes"]:checked').each(function(i, element) {
            paginated_prechecks.push($(element).data('child_id'));
        });

        $('#precheck-modal-pagination-page').html(1);
        $('#precheck-modal-pagination-total').html(paginated_prechecks.length);
        $('#precheck-modal-pages')
            .data('pages', paginated_prechecks)
            .attr('data-pages', paginated_prechecks);
        update_pagination(current_id);
    });

    $wrapper.on('change', '.survey-input-yes_or_no .survey-input', function() {
        const has_child = $(this).parents('.precheck-form').data('has_children');
        const $checked = $(this).parents('.survey-input-yes_or_no').find(':checked');

        // Question with children => yes/no toggle determines if another page is to load.
        // Question without children => yes/no toggle determines if corrective action is needed.
        if (!has_child) {
            const $td = $(this).parents('td');
            const stock_id = $td.data('stock_id');
            const selector  = stock_id ? '~ .precheck-todo[data-stock_id="' + stock_id + '"]' : '~ .precheck-todo';
            const $todo_row = $td.parents('tr').find(selector).first();

            // If "no" was selected at one point, remember that.
            if ($checked.val() == 'no') {
                $td.addClass('had-corrective-action');
            }

            // Show the "corrective action" section, if "no" was selected. Hide if "yes" was selected.
            $todo_row.toggleClass('hidden', $checked.val() == 'yes');
            $todo_row.find(':input').prop('disabled', $checked.val() != 'no');

            // Show a link to view the "corrective action" section, if "no" was selected in the past.
            const show_corrective_action_button = $td.hasClass('had-corrective-action') && $checked.val() == 'no';
            $td.find('.precheck-view-corrective').toggleClass('hidden', !show_corrective_action_button);
        }
    });

    // When the "show corrective action" button is clicked...
    $wrapper.on('click', '.precheck-view-corrective', function () {
        // Find the to-do corresponding to the question and column.
        const $td       = $(this).parents('td');
        const stock_id  = $td.data('stock_id');
        const selector  = stock_id ? '~ .precheck-todo[data-stock_id="' + stock_id + '"]' : '~ .precheck-todo';
        const $todo_row = $td.parents('tr').find(selector).first();

        // Show the to-do (corrective action).
        $todo_row.removeClass('hidden');
    });

    // Determine if corrective action is needed.
    function corrective_action_needed()
    {
        return ($('.precheck-yes_no[value="no"]:checked').length > 0);
    }


    // Navigate using the prev/next buttons
    $('#precheck-modal-next').on('click', function() {
        change_page('next');
    });

    $('#precheck-modal-prev').on('click', function() {
        change_page('prev');
    });

    // When the last button is clicked, save the precheck
    $('#precheck-modal-last, #submit-precheck').click(function() {
        let data = get_precheck_data();
        $.ajax({
            url: '/admin/safety/ajax_save_precheck/' + $('#safety-precheck-form-id').val(),
            type: 'post',
            data: data
        }).done(function(data) {
            data = JSON.parse(data);

            $('.alert_area').first().add_alert(data.message, data.status + ' popup_box');

            // Redraw the table and dismiss the modal
            $('#safety-precheck-table').dataTable().fnDraw();
            $('#safety-precheck-modal').modal('hide');
        });
    });

    $wrapper.on('change', '.precheck-select-items', function () {
        var total    = $(this).find('option').length;
        var selected = $(this).val();
        var $table   = $(this).parents('#safety-precheck-quiz-wrapper').find('.precheck-questions-table');

        // Show the table if there is at least one column selected. Hide otherwise.
        $table.toggleClass('hidden', (selected === null || selected.length == 0));

        $table.find('td[data-stock_id], th[data-stock_id]').addClass('hidden')
            .find(':input').prop('disabled', true);

        selected && selected.forEach(value => {
            $table.find('td[data-stock_id="'+value+'"], th[data-stock_id="'+value+'"]').removeClass('hidden')
                .find(':input').prop('disabled', false);
        });

        $table.find('th[colspan]:last-child').toggleClass('hidden', total == 0);
    });


    function change_page(direction, callback)
    {
        const pages = $('#precheck-modal-pages').data('pages');
        const current_page = $wrapper.data('current_page');
        let next_page;

        if (direction == 'prev') {
            next_page = current_page && pages.indexOf(current_page) > 0
                ? pages[pages.indexOf(current_page) - 1]
                : pages[0];
        } else {
            next_page = current_page
                ? pages[pages.indexOf(current_page) + 1]
                : pages[0];
        }
        next_page = next_page ? next_page : pages[0];

        let data = get_precheck_data();
        data.new_survey_id = next_page;

        $.ajax({
            url: '/admin/safety/ajax_change_precheck_page',
            type: 'post',
            data: data,
        }).done(function(response) {
            response = JSON.parse(response);
            $wrapper
                .html(response.html)
                .data('current_page', next_page)
                .attr('data-current_page', next_page);
            update_pagination(next_page);

            $wrapper.find('.ib-combobox').combobox();
            ib_initialize_multiselects();
            ib_initialize_typeselects();
            $wrapper.find('.precheck-select-items').change();

            if (callback) {
                callback();
            }
        });
    }

    function update_pagination(id)
    {
        id = parseInt(id);
        const pages = $('#precheck-modal-pages').data('pages');
        const page_number = pages.indexOf(id) + 1;

        // Note the current page.
        $('#precheck-modal-pagination-page').html(page_number);

        // When you are at the start, "previous" button is disabled
        $('#precheck-modal-prev').prop('disabled', page_number == 1);
        // When you are at the end "next" is replaced with "save"
        $('#precheck-modal-next').toggleClass('hidden', page_number == pages.length);
        $('#precheck-modal-last').toggleClass('hidden', page_number != pages.length);
    }

    // Get data from each visible question
    function get_responses()
    {
        const $form = $('#safety-precheck-form');
        const form_data = $form.serializeArray();
        let responses = {};
        let match, stock_id, question_number, todo_name_prefix, value;

        for (let i = 0; i < form_data.length; i++) {
            if (form_data[i].name.indexOf('questions[') == 0) {
                value = form_data[i].value;

                match = form_data[i].name.match(/questions\[([0-9]*)\]\[([0-9]*)\]/);
                stock_id = match[1] || 0;
                question_number = match[2];

                todo_name_prefix = 'todos['+stock_id+']['+question_number+']';

                responses[stock_id] = responses[stock_id] || {};
                responses[stock_id][question_number] = {
                    value: value,
                    todo: get_response_corrective_action(question_number, stock_id)
                };
            }
        }

        return responses;
    }

    function get_response_corrective_action(question_id, stock_id)
    {
        const selector = parseInt(stock_id)
            ? '.precheck-todo[data-question_id="'+question_id+'"][data-stock_id="'+stock_id+'"]'
            : '.precheck-todo[data-question_id="'+question_id+'"]';
        const $todo = $(selector);

        return {
            id:          $todo.find('[name*="id"]:not(:disabled)').val(),
            summary:     $todo.find('[name*="summary"]:not(:disabled)').val(),
            assignee_id: $todo.find('[name*="assignee_id"]:not(:disabled)').val(),
            status:      $todo.find('[name*="status"]:not(:disabled)').val(),
        }
    }

    function get_precheck_data()
    {
        return {
            current_survey_id: $('#safety-precheck-quiz-wrapper').data('current_page'),
            responses: get_responses(),
            result_id: $('.precheck-form').data('survey_result_id'),
            course_id: $('#safety-precheck-form-course_id').val(),
            schedule_id: $('#safety-precheck-form-schedule_id').val(),
        }
    }

})();
