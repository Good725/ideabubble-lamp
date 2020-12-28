var answer = [];
$(document).ready(function ()
{
    $('#no_answer_alert').hide();

    $('#answer_id').on('change', function ()
    {
        if ($(this).val() != '')
        {
            add_answer($(this).val());
        }
    });

    $("#answer_list_table").on('click', '.remove-button', function ()
    {
        $(this).parents('tr').remove();
    });

    $('.save_button').click(function(ev)
    {
        ev.preventDefault();
        if ($('#answer_id').val() === '')
        {
            $('#no_answer_alert').show();
            return;
        }
        else
        {
            $('#form_add_edit_question').submit();
        }
    });
});


function add_answer(id)
{
    $.ajax({
        type:'POST',
        url:'/admin/surveys/ajax_get_question_answer/',
        data:{id:id},
        dataType:'json'
    })
        .done(function(results)
        {
            var table = 'answer_list_table';
            $('#answer_list_table tbody tr').each(function()
            {
                $(this).remove();
            });
            $.each(results,function(k,result)
            {
                var row = '';

                row += '<tr id="' + add_item_to_rows_db(table, result.value) + '"data-id="" data-label="' + result.label + '" data-value="' + result.value + '">';
                row += '<td>' + result['order'] + '</td>';
                row += '<td>' + result['id'] + '</td>';
                row += '<td>' + result['label'] + '</td>';
                row += '<td>' + result['type'] + '</td>'
                row += '<td>' + result['value'] + '</td>';
                row += '</tr>';

                $('#answer_list_table').append(row);
            });
        });
}
