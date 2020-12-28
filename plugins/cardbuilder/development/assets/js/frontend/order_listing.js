var cards = [];
$(document).ready(function()
{
    make_sortable(document.getElementById('orders_table'));
    $('#approve_order_button').on('click', function()
    {
        var boxes = document.querySelectorAll('.approve_checkbox:checked');
        if (boxes.length < 1)
        {
            $('#approval_failed_modal').fadeIn();
        }
        else
        {
            $('#order_processing_modal').fadeIn(function()
            {
                var controls = document.getElementById('card_builder_controls');
                var images    = [];
                var field;
                for (var i = 0; i < boxes.length; i++)
                {
                    $.ajax({url:'/frontend/cardbuilder/ajax_get_card_details/'+boxes[i].getAttribute('data-id'),async:true}).done(function(results)
                    {
                        results = JSON.parse(results);
                        for (var key in results)
                        {
                            if (results.hasOwnProperty(key))
                            {
                                field = controls.querySelector('[name="'+key+'"]');
                                if (field) field.value = results[key];
                            }
                        }
                        draw_card();
                        var image_url = document.getElementById('card_builder_canvas').toDataURL('image/png');
                        console.log(image_url);
                        images.push(image_url);

                        if (document.querySelectorAll('.approve_checkbox:checked').length == images.length)
                        {
                            $.post('/frontend/cardbuilder/generate',{cards:cards,images:images},function(data)
                            {
                                if (data == 'OK')
                                {
                                    console.log(data);
                                    $('#orders_table_wrapper').load('card-builder-orders.html #orders_table', function()
                                    {
                                        make_sortable(document.getElementById('orders_table'));
                                        cards = [];
                                        $('#order_processing_modal').hide();
                                        $('#order_placed_modal').show();
                                    });
                                }
                            });
                        }

                    });
                }
            });

        }

    });

    $('#orders_table').on('click', '.delete_icon', function()
    {
        var id = this.getAttribute('data-id');
        document.getElementById('confirm_delete_sign_id').innerHTML = id;
        document.getElementById('delete_card_button').href = '/frontend/cardbuilder/delete_card/'+id;
        $('#delete_card_modal').fadeIn();
    });

    $('.cb-modal-dismiss').on('click', function()
    {
        $(this).parents('.cb-modal-overlay').hide();
    });

    $('#approve_checkboxes_all').on('change', function()
    {
        $('.approve_checkbox:not(:disabled)').prop('checked', this.checked).trigger('change');
    });

    $('#filter_by_status').on('change', function()
    {
        var status = this.value;
        var table = $('#orders_table');
        if (status == '')
        {
            table.find('tbody tr').removeClass('hidden');
        }
        else
        {
            table.find('tbody tr').addClass('hidden');
            table.find('.card_'+status).removeClass('hidden');
        }
    });

});

$(document).on('change', '.approve_checkbox', function()
{
    // Hidden 1 or 0, so checkbox column is sortable
    this.parentNode.parentNode.getElementsByClassName('checked_value')[0].innerHTML = ((this.checked) ? 1 : 0);

    if (this.checked == true)
    {
        cards.push($(this).data('id'));
    }
    else
    {
        cards.splice(cards.indexOf($(this).data('id')),1);
    }
});


/* Sortable tables */
function make_sortable(table)
{
    var thead = table.tHead;
    thead && (thead = thead.rows[0]) && (thead = thead.cells);
    if (thead)
    {
        var i = thead.length;
        while (--i >= 0) (function (i)
        {
            var dir = 1;
            thead[i].addEventListener('click', function(ev)
            {
                var tag = ev.toElement.tagName;
                if (tag != 'LABEL' && tag != 'INPUT')
                {
                    sort_table(table, i, (dir = 1 - dir));
                }
            });
            thead[i].className += (thead[i].className +' sortable_heading').trim();
        }(i));
    }
}

function sort_table(table, col, reverse)
{
    var tbody = table.tBodies[0];
    var tr    = Array.prototype.slice.call(tbody.rows, 0);

    reverse = -((+reverse) || -1);
    tr = tr.sort(function (a, b)
    {
        return reverse * (a.cells[col].textContent.trim().localeCompare(b.cells[col].textContent.trim()));
    });
    for (var i = 0; i < tr.length; ++i)
    {
        tbody.appendChild(tr[i]); // append each row in order
    }
}