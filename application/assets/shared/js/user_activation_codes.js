/**
 * Created by dale on 31/07/2014.
 */
$(document).ready(function(){
    var ajax_source = "/admin/settings/get_activation_codes_list";
    var settings = {
        'aaSorting'       : [],
        'sPaginationType' : 'bootstrap',
        'sDom'            : "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
        "fnServerData": function (sSource, aoData, fnCallback) {
            $.getJSON(sSource, aoData, function (json) {
                fnCallback(json)
            });
        }
    };
    $("#activation_codes_list").ib_serverSideTable(ajax_source, settings);
});