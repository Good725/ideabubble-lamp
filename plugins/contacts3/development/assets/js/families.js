$(document).ready(function(){
	// Server-side datatable
    var $families_table = $('#list_families_table');
    $families_table.ready(function()
    {
            var ajax_source = '/admin/contacts3/ajax_get_family_datatable';
            var settings = {
                "aLengthMenu"    : [10],
                "aoColumnDefs": [{
                    "aTargets": [1],
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol)
                    {
                        // Add data attribute, with the contact ID to each row
                        $(nTd).parent().attr({'data-id': oData[0]});
                    }
                }]
            };
            $families_table.ib_serverSideTable(ajax_source, settings);

    });

    // Search by individual columns
    $families_table.find('.search_init').on('change', function ()
    {
        $families_table.dataTable().fnFilter(this.value, $families_table.find('tr .search_init').index(this) );
    });
	
    $(document).on('click','#add_edit_family .save_button',function(ev){
        ev.preventDefault();
		this.disabled = true;
        address = generate_json_for_address(address);
        $("#address").val(address);
        save_family($(this).data('action'));
    });

    primary_contact_autocomplete();

    var form = $('#add_edit_family');
    form.find('.btn[data-content]').popover({placement:'top',trigger:'hover'});
});

function generate_json_for_address()
{
    var address = [];
    address.push({address1:$("#address1").val()});
    address.push({address2:$("#address2").val()});
    address.push({address3:$("#address3").val()});
    address.push({town:$("#town").val()});
    address.push({postcode:$("#postcode").val()});
    return JSON.stringify(address);
}

// Save family. Use ajax and reload if on the list page
function save_family(action)
{
    var form = $('#add_edit_family');

    if (form.validationEngine('validate'))
    {
        var family_list_page  = $('#list_families_table')[0] ? true : false;
        var contact_list_page = $('#list_contacts_table')[0] ? true : false;
        if (action == 'save' && (family_list_page || contact_list_page))
        {
            $.ajax({
                url      : '/admin/contacts3/ajax_save_family/',
                data     : form.serialize(),
                type     : 'post',
                dataType : 'json'
            }).success(function(result)
                {
                    if (family_list_page)
                    {
                        load_family(document.getElementById('family_id').value, result.alerts);
                        //remove_popbox();
                    }
                    else
                    {
                        var contact_id = $('#primary_contact_id').val();
                        load_family_for_contact(contact_id, result.alerts);
                    }
                });
        }
        else
        {
            $('#family_action').val(action);
            form.submit();
        }
    }
    else
    {
        setTimeout("$('.formError').remove()", 10000);
    }
}

// Autocomplete for selecting the primary contact
function primary_contact_autocomplete()
{
    $('#family_primary_contact').autocomplete({
        source :'/admin/contacts3/ajax_get_all_family_members_ui/'+$('#family_id').val(),
        open   : function () {$(this).data("uiAutocomplete").menu.element.addClass('educate_ac');},
        select : function (event, ui){$('#family_primary_contact_id').val(ui.item.id);}
    });

    // If the primary contact field is blanked, remove the primary contact id
    $('#family_primary_contact', function()
    {
        (this.value == '') ? $('#family_primary_contact_id').val('') : null;
    });
}

// Display the modal box when user click delete family
$(document).on('click', '#add_edit_family .action-buttons .delete_button' , function()
{
    $('#family_confirm_delete').modal();
});
