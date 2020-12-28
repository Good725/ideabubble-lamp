/* Dragging sortability */
$('.sortable-tbody').sortable({cancel: 'a, button, :input, label'});

var answers_list_table = 'answers_list_table';
$(document).ready(function ()
{
    $('#no_type_selected').hide();
    $('#value_already_selected').hide();

    $('#type_id').on('change', function ()
    {
        if ($(this).val() != '')
        {
            $('#no_type_selected').hide();
        }
    });

    $('#add_option_btn').on('click', function ()
    {
        if ($('#type_id').val() != '')
        {
            var option = {label: $('#label').val(), value: $('#value').val()};
            var value_exist = is_item_in_rows_db('answers_list_table', option.value);
            if ($('#value').val() == '' || value_exist)
            {
                $('#value_already_selected').show();
            }
            else if (option != '' && option != 'undefined')
            {
                add_option(option);
            }
        }
        else
        {
            $('#no_type_selected').show();
        }
    });

    $("#answers_list_table").on('click', '.remove-button', function ()
    {
        $(this).parents('tr').remove();
    });

    $("#form_add_edit_answer").validate({
        submitHandler: function (form)
        {
            $('#option_list').val(generate_json_for_options_table());
            form.submit();
        }
    });

});

function add_option(option)
{
    if (option)
    {
        var table = 'answers_list_table';
        var type = $('#type_id').find('[value="' + $('#type_id').val() + '"]');
        var row = '';

        row += '<tr id="' + add_item_to_rows_db(table, option.value) + '"data-id="" data-label="' + option.label + '" data-value="' + option.value + '">';
        row += '<td></td>';
        row += '<td></td>';
        row += '<td>' + type.data('title') + '</td>';
        row += '<td>' + option.label + '</td>';
        row += '<td>' + option.value + '</td>';
        row += '<td class="remove-row"><button type="button" class="btn-link remove-button"><span class="icon-remove"></span></button></td>';
        row += '</tr>';

        $('#' + table).append(row);
        $('#label').val('');
        $('#value').val('');
        $('#value_already_selected').hide();
    }
}

function generate_json_for_options_table()
{
    var data = [];

    $('#answers_list_table').children('tbody').children('tr').each(function ()
    {
        var question = {id: $(this).data('id'), label: $(this).data('label'), value: $(this).data('value')}
        data.push(question);
    });

    return JSON.stringify(data);
}