var family_contact_editor = {
    setup: function(container) {

        container.find(".add_note.family").on("click", function(){
            var family_id = container.find(".family-form [name=family_id]").val();
            display_note_editor(
                "Family",
                family_id,
                "",
                container,
                "#family-notes-tab .table.notes"
            );
        });

        container.find("input.family.autocomplete").on("change", function(){
            if (this.value == "") {
                $(this).parent().find(".family_id").val("");
            }
        });

        container.find("input.family.autocomplete").autocomplete({
            source: function(data, callback){
                $(this).parent().find(".family_id").val("");
                $.get("/admin/families/autocomplete",
                    data,
                    function(response){
                        callback(response);
                    });
            },
            open: function () {
                $(this).parent().find(".family_id").val("");
            },
            select: function (event, ui) {
                $(this).parent().find(".family_id").val(ui.item.id);
            }
        });

        container.find(".family-form button.save_button").on("click", function(){
            var button = this;
            var form = this.form;
            var data = $(form).serialize();
            $.ajax(
                "/admin/families/save/",
                {
                    data: data,
                    method: 'POST',
                    cache: false,
                    success: function (response) {
                        $("#contacts2-editor-container").html(response);
                        setup_contact_editor($("#contacts2-editor-container"));
                        var $table = $('#list_contacts_table');
                        if ($table.length >0) {
                            setup_contacts_list_datatable();
                        }
                    },
                    error: function() {

                    }
                }
            );
            return false;
        });

        container.find(".add_new_family_member_link").on("click", function(){
            var family_id = container.find("[name=family_id]").val();
            load_contact(null, null, 'new?family_id=' + family_id + '&' + Math.random());
            return false;
        });

        container.find("#list_family_members_table tbody tr").on("click", function(){
            var tr = this;
            var family_id = container.find("[name=family_id]").val();
            var contact_id = $(tr).data("id");

            load_contact(contact_id);
            return false;
        });
    },

    validate: function(container) {

    }
};

window.contact_editor.extensions.push(family_contact_editor);

$(document).ready(function(){
    if ($("#list_families_table").length > 0){
        setup_families_list_datatable();
    }
});

$(document).on('click', '[href="#family-accounts-tab"]', function(){
	loadTransactions('family');
});

$(document).on('click', '[href="#family-member-accounts-tab"]', function(){
	loadTransactions('member');
});

function loadTransactions(table)
{
	var params, tab, tableId;
	if (table == 'family')
	{
		params  = 'family_id=' + $('#edit_family').find('[name="family_id"]').val();
		tab     = $('#family-accounts-tab');
		tableId = 'family_transaction_table';

		tab.find('.content-area').load('/admin/families/ajax_get_family_accounts?' + params, function() {
			$(this).find('.dataTable').attr('id', tableId).dataTable({"aaSorting": []});
		});
	}
	else
	{
		params  = 'contact_id=' + $('#contact-editor').find('.span_client_id').val();
		tab     = $('#family-member-accounts-tab');
		tableId = 'family_member_transaction_table';

		tab.find('.content-area').load('/admin/families/ajax_get_family_member_accounts?' + params, function() {
			$(this).find('.dataTable').attr('id', tableId).dataTable({"aaSorting": []});
		});
	}
}

function setup_families_list_datatable()
{
    // Server-side datatable
    var $table = $('#list_families_table');
    $table.ready(function() {
        var ajax_source = '/admin/families/list_datatable';
        var settings = {
            "aLengthMenu"    : [10, 25, 50, 100],
            "sPaginationType" : "bootstrap",
            "aaSorting"      : [[ 2, "desc" ]],
            "aoColumnDefs": [{
                "aTargets": [1],
                "fnCreatedCell": function (nTd, sData, oData, iRow, iCol)
                {
                    // Add data attribute, with the contact ID to each row
                    $(nTd).parent().attr({'data-id': oData[0]});
                }
            }]
        };
        $table.ib_serverSideTable(ajax_source, settings);

    });

    // Search by individual columns
    $table.find('.search_init').on('change', function () {
        $table.dataTable().fnFilter(this.value, $table.find('tr .search_init').index(this) );
    });

    $("#list_families_table tbody").on(
        "click",
        "tr",
        function(){
			$(this).parents('tbody').find('> .selected').removeClass('selected');
			$(this).addClass('selected');
            var id = $(this).data("id");
            var last_modification = $(this).find(">td:nth-child(3)").html();
            if (parseInt(id)) {
                load_contact("", last_modification, '&family_id=' + id);
            }
        }
    );

    $("#family-list-add").on(
        "click",
        function(e){
            load_contact("", "", '?family_id=new');
            return false;
        }
    );
}