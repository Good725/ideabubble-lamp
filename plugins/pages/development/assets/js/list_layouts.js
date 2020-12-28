$(document).ready(function()
{
	var $table = $('#list-layouts-table');

	// Server-side datatable
	$table.ready(function()
	{
		var ajax_source = '/admin/settings/ajax_get_layouts_datatable';
		var settings = {
			"bAutoWidth"      : true,
			"bSearchable"     : true,
			"sPaginationType" : "bootstrap",
			"aoColumnDefs": [{
				"aTargets": [1],
				"fnCreatedCell": function (nTd, sData, oData, iRow, iCol)
				{
					// Add data attribute, with the contact ID to each row
					$(nTd).parent().attr({'data-id': oData[0]});
				}
			}]
		};
		var drawback_settings = {"fnDrawCallback": function()
			{
				$table.find('[data-toggle="tooltip"]').tooltip();
			}
		};
			$table.ib_serverSideTable(ajax_source, settings, drawback_settings);
	});

	// Search by individual columns
	$table.find('.search_init').on('change', function ()
	{
		$table.dataTable().fnFilter(this.value, $table.find('tr .search_init').index(this) );
	});

});