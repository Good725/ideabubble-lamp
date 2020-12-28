$(document).ready(function(){

    var oTable;
    var parameters = [];
    var series = [];
    var keywords_table =  $("#keywords_table").dataTable();

    $("#chart_publish_toggle button").click(function(){
        $("#chart_publish").val($(this).val());
    });

    $("#report_publish_toggle button").click(function(){
        $("#publish").val($(this).val());
    });

    $("#delete_button").click(function(){
        var report_id = $(this).data('report_id');
        $.post('/admin/reports/delete_report',{report_id:report_id},function(){
            window.location.href = '/admin/reports/';
        });
    });

    $(document).on('click','#report_table tbody tr',function(){
        if($("#link_url").val() !== undefined && $("#link_url").val() != '')
        {
            var link_column = $("#link_column").val();
            var i = 1;
            var final = 1;
            $('#report_table thead tr').each(function(){
                if($(this).text() == link_column)
                {
                    final = i;
                }
                i++;
            });
            console.log($("#link_url").val()+'/'+$(':nth-child('+final+')',this).text());
            window.location.href = $("#link_url").val()+$(':nth-child('+final+')',this).text();
        }
    });

    $('#print_report').click(function(ev)
    {
        ev.preventDefault();
        if (document.getElementById('report_table').getElementsByTagName('tbody').length == 0)
        {
            alert('You have not run the report. Please click "Run Report" and if you are happy, try again.');
        }
        else
        {
            create_print_window();
        }

    });

    function create_print_window()
    {
        var today = new Date();
        var date = today;
        var filters_text = '<ul style="list-style: none;">';
        var label;
        var form_element;
        var value;
        var selected;
        var date_overwritten = false;
        var first_date_param = false;

        $('#temporary_parameters').find('> div').each(function() {
            label            = $(this).find('label.control-label').html().trim();
            form_element     = $(this).find('input, textarea');
            first_date_param = false;

            if (form_element.length > 0) {
                value = form_element.val();
            } else {
                form_element = $(this).find('select');
                selected = [];
                value = '';
                form_element.find('option:selected').each(function() {
                    value += this.text+'; ';
                });
                value = value.replace(/; $/, '');
            }

            if (value && (label.toLowerCase() == 'date' || form_element.hasClass('datepicker'))) {

                /*
                   todo: switch to using the standard datepicker which uses a hidden field with the date in ISO format.
                   (Verify that this update won't break any existing reports.)
                 */

                // For now, assume __/__/____ and __-__-____ mean DD/MM/YYYY and DD-MM-YYYY and convert to ISO format
                if (value.match(/[0-9]{2}-[0-9]{2}-[0-9]{4}/)) {
                    value = new Date(value.split('-').reverse().join('-'));
                } else if (value.match(/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/)) {
                    value = new Date(value.split('/').reverse().join('-'));
                } else {
                    value = new Date(clean_date_string(value));
                }

                if (!date_overwritten) {
                    date = value;
                    date_overwritten = true;
                    first_date_param = true;
                }
            }

            if (!first_date_param && value) {
                filters_text += '<li>' + value + '</li>';
            }
        });

        filters_text += '</ul>';
        $('#report_table').dataTable().fnDestroy();
        var number_of_records = document.getElementById('report_table').getElementsByTagName('tbody')[0].getElementsByTagName('tr').length;
        var thead = document.getElementById('report_table').getElementsByTagName('thead')[0].innerHTML;
        var tbody = document.getElementById('report_table').getElementsByTagName('tbody')[0].innerHTML;
        $('#report_table').dataTable({"aaSorting": []});
        var item_date_formatted = new moment(date).format('Do MMMM YYYY');
        var today_formatted = new moment().format('Do MMMM YYYY');

        var window_html =
            '<html>' +
            '    <head>' +
            '        <title>Print Report</title>' +
            '        <style>' +
            '           body{color:#000;font-family:Helvetica,Arial,sans-serif;margin:0;padding:20px;}' +
            '           h1{font-size:28px;margin:5px 0;}' +
            '           p{margin:5px 0;}' +
            '           dl,dd,dt,ul,ol,li{margin:0;padding:0;}' +
            '           dt{float:left;clear:left;width:100px;}' +
            '           dt:after{content:":"}' +
            '           dd{margin:0 0 5px 110px;}' +
            '           li{margin-bottom:5px;}'+
            '           table{border-collapse:collapse;border-spacing:0;}' +
            '           td, th{padding:10px 10px;}' +
            '           th{font-weight:bold;text-align:left;}' +
            '           tbody tr:nth-child(odd){background:#efefef;}' +
            '           input,select{-webkit-appearance:none;background:none;border:0;font-size:inherit;width:auto;}' +
            '       </style>' +
            '    </head>' +
            '    <body>' +
            '       <h1>'+ ((document.getElementById('name') === null) ? $("#navbar-breadcrumbs").find("h1").first().text() : document.getElementById('name').value) +'</h1>' +

            '       <div style="float: left;">' +
            '           <dl>' +
            '               <dt>Date</dt>' +
            '               <dd>' + item_date_formatted + '</dd>' +
            '               <dt>Records</dt>' +
            '               <dd>' + number_of_records + '</dd>' +
            '           </dl>' +
            '       </div>' +

            '       <div style="float: right;">' + filters_text + '</div>' +

            '        <p style="clear: both; text-align: right;">Printed: '+today_formatted+'</p>' +

            '        <table class="report_table" id="report_table" border="1">' +
            '           <thead>'+thead+'</thead>'+
            '           <tbody>'+tbody+'</tbody>' +
            '       </table>' +
            '    </body>' +
            '</html>' +
            '<script>' +
            '(function()' +
            '{' +
            '   window.document.close();' +  // necessary for IE >= 10
            '   window.focus();' +  // necessary for IE >= 10
            '   window.print();' +
            '   window.close();' +
            '})();'+
            '</script>';

        var width    = 1000;
        var height   = 600;
        var left     = (screen.width  / 2) - (width  / 2);
        var top      = (screen.height / 2) - (height / 2);
        var mywindow = window.open('', 'Print Report', 'height='+height+',width='+width+',left='+left+',top='+top);
        mywindow.document.write(window_html);

    }

    $(".save_btn").click(function(){
        parameters = [];
        var action = $(this).data('action');
        if((action == "rollback_to_version" || action == "load_version") && $("#rollback_to_version").prop("selectedIndex") < 1){
            alert("Please select a version");
            return false;
        }

        $('#parameter_tab').find('.parameter .form-group').each(function()
        {
            var value = '';
            switch($(this).find('.parameter_picker').val())
            {
                case 'text':
                    value = $(this).children('.input_text').val();
                    break;
                case 'date':
                    value = $(this).children('.input_date').val();
                    break;
                case 'sql':
                    value = $(this).children('.input_select').val();
                    break;
                case 'dropdown':
                    value = $(this).children('.input_text').val();
                    break;
                case 'custom':
                    value = '(' + $(this).children('.input_textarea').val().trim() + ')';
                    break;
				case 'user_role':
					break;
                default:
                    value = $(this).children('.input_text').val();
                    break;
            }
            var is_multiselect = $(this).find('.is_multiselect').prop('checked') ? 1 : 0;
            var always_today = $(this).find('input.always_today').prop('checked') ? 1 : 0;
            var element = [
                $(this).children(':nth-child(1)').val(),
                $(this).children('.parameter_picker').val(),
                $(this).children(':nth-child(3)').val(),
                value,
                is_multiselect,
                always_today
            ];
            parameters.push(element);
        });

        var temporary_keywords = generate_keywords_json(keywords_table);
        $("#temporary_keywords").val(temporary_keywords);
        $('#parameter_fields').val(JSON.stringify(parameters));
        $("#action").val(action);
        prepare_form();
    });

    $("#cancel_button").click(function(){
        window.location.href = '/admin/reports/';
    });

    $(".run_report").click(function(){
        var report_id = $(this).data('report-id');
    });

    $(".show_sql").click(function()
    {
        var sql            = $(this.getAttribute('data-source')).val();
        var $target        = $(this.getAttribute('data-target'));

        $.post('/admin/reports/beautify_sql',{sql: sql},function(data){
            $target.html(data);
        });
        $target.slideToggle('slow');
    });

    $("#send_bulk_notification").click(function()
    {
        $("#report_edit_form .alert").addClass("hide");
        var idVal = $("#id").val(),
            date = $('.month_range').val(),
            sql = $("#sql").val();

        sql = replaceDateRangeParams(date, sql);

        $('#prependedInput').val('');

        prepare_parameters();
        send_table(idVal, sql);

        if (cms_ns)
        {
            cms_ns.clear_modified_input('month_value');
        }
    });

    $("#generate_report").click(function()
    {
        $("#report_edit_form .alert").addClass("hide");
        var idVal = $("#id").val(),
            date = $('.month_range').val(),
            sql = $("#sql").val();

        sql = replaceDateRangeParams(date, sql);

        $('#prependedInput').val('');

        prepare_parameters();
        draw_table(idVal, sql);
        draw_widget(idVal);
        draw_chart(idVal);

        if (cms_ns)
        {
            cms_ns.clear_modified_input('month_value');
        }
    });

    $("#add_new_parameter").click(function(){
        $.post('/admin/reports/get_new_parameter',null,function(data){
            $("#parameter_area").append(data);
        }).done(function(){
            parameter_sorting();
        });
    });

    $("#widget_publish_toggle button").click(function(){
        $("#widget_publish").val($(this).val());
    });

    $(document).ajaxStart(function() {
        $(".loading-warning").show();
    });

    $(document).ajaxStop(function() {
        $(".loading-warning").hide();
    });

    redraw_temporary_parameters();

    $("#report_type").change(function(){
        if($(this).val() == 'serp')
        {
            $("#sql_data_tab").hide();
            $("#serp_data_tab").show();
        }
        else
        {
            $("#sql_data_tab").show();
            $("#serp_data_tab").hide();
        }
    });

    $("#save_keywords_button").click(function(){
        if($("#id").val() == "")
        {
            keywords_table.fnAddData(['N/A',$("#new_keywords_dialog .keywords").val(),$("#new_keywords_dialog .url").val(),'N/A','<i class="icon-remove"></i>']);
        }
        else
        {
            $.post('/admin/reports/save_keyword',{keyword: $("#new_keywords_dialog .keywords").val(),url:$("#new_keywords_dialog .url").val(),report_id:$("#id").val()},function(result){
                draw_keywords_table(result);
                $('#new_keywords_dialog').modal('hide');
                $('#new_keywords_dialog .keywords').val('');
            });
        }
    });

    $("#keywords_table").on('click','tr td i.icon-remove',function(){
        var parent = $(this).closest('tr');
        var target_row = $(this).closest("tr").get(0);
        var position = keywords_table.fnGetPosition(target_row);
        if($("#id").val() == "")
        {
            keywords_table.fnDeleteRow(position);
        }
        else
        {
            $.post('/admin/reports/delete_keyword',{report_id:$("#id").val(),keyword_id:$(':nth-child(1)',parent).text()},function(){
                keywords_table.fnDeleteRow(position);
            });
        }
    });

    parameter_sorting();

    $(document).on('change','.parameter_picker',function()
    {
        var $this = $(this);
        $this.siblings('.input_text').hide();
        $this.siblings('.input_select').hide();
        $this.siblings('.input_date').hide();
        $this.siblings('.input_textarea').hide();
        $this.siblings('.input_textarea').hide();
        $this.siblings('.always_today').hide();

        switch ($(this).val())
        {
            case 'date':
                $this.siblings('.always_today').show();
            case 'dropdown':
                $this.siblings('.input_date').show();
                break;
            case 'text':
                $this.siblings('.input_text').show();
                break;
            case 'sql':
                $this.siblings('.input_select').show();
                $.post('/admin/reports/get_sql_parameters', null, function(result)
                {
                    $this.siblings('.value_select').replaceWith(result);
                });
                break;
            case 'custom':
                var customTextarea = $this.siblings('.input_textarea').text();
                $(this).siblings('.input_textarea').text(customTextarea.trim());
                $(this).siblings('.input_textarea').show();
                break;
			case 'user_role':
				break;
        }
    });

    $(document).on('click','#parameter_area .delete',function(){
        $(this).parents('div.parameter').remove();
    });

    $("#generate_csv").on('click','a',function(ev)
    {
        ev.preventDefault();
        var date = $('.month_range').val(),
            sql = $("#sql").val();
        sql = replaceDateRangeParams(date, sql);
        $("#csv_parameters").val(prepare_parameters());
        $("#csv_sql").val(sql);
        $("#csv_form").submit();
    });

    $('body').on('click', '#report_link', function(){
        this.click();
    });

    $('.get_report_option').on('click', function()
    {
        $.ajax(
            {
                url  : '/admin/reports/ajax_get_report',
                type : 'POST',
                data : {
                    report_parameters : prepare_parameters(),
                    report_sql        : replaceDateRangeParams($('.month_range').val(), document.getElementById('sql').value),
                    report_format     : this.getAttribute('data-format'),
                    select_purpose    : this.getAttribute('data-action'),
                    report_id         : document.getElementById('report_id').value
                },
                success: function(response)
                {
                    response = JSON.parse(response);
                    switch (response.status)
                    {
                        case 'success':
                            if (response.file)
                            {
                                location.href = '/admin/files/download_file?file_id=' + response.file;
                            }
                            else if (response.email_status == 'success')
                            {
                                add_alert('The report has been sent to ' + response.email, 'success');
                            }
                            else
                            {
                                add_alert('Error sending email', 'error');
                            }
                            break;

                        case 'error':
                            add_alert('Error. Please check your SQL.', 'error');
                            break;

                        case 'empty':
                            add_alert('The result of the query is empty.', 'error');
                            break;
                    }
                }
            });

    });

    $("#report_autoload_toggle button").click(function(){
        $("#autoload").val($(this).val());
    });

    $("#report_checkbox_column_toggle button").click(function(){
        $("#checkbox_column").val($(this).val());
    });

    $("#report_action_button button").click(function(){
        $("#action_button").val($(this).val());
    });

    $("#report_autosum button").click(function(){
        $("#autosum").val($(this).val());
    });

    $("#report_dashboard_toggle button").click(function(){
        $("#dashboard").val($(this).val());
    });

    $("#autocheck_toggle button").click(function(){
        $("#autocheck").val($(this).val());
    });



    if($("#autoload_report").val() == "1")
    {
        $("#generate_report").click();
    }

    $("#sql").on('change',function(){
        $.post('/admin/reports/get_table_columns',{id: $("#id").val(), sql:$("#sql").val()},function(data){
            var result = $.parseJSON(data);
            if(data.length > 0)
            {
                $("#table_column_list").show();
                $("#table_column_list").html('');
                $.each(result,function(i,n){
                    $("#table_column_list").append('<b>'+i+'</b><div style="">'+ n+'</div>');
                });
            }
            else
            {
                $("#table_column_list").hide();
            }
        });
    });

    $("#sql").change();

    $("#sql").on('change',function() {
        $("#generate_report").prop("disabled", "true").attr("title", "Have you saved your report?");
    });

    //total settlement
    $(document).on('change', 'input.row_check', function() {
        get_checkbox_totals();
    });

    $('.datepicker').datepicker({
        format: window.ibcms.date_format,
        autoclose: true,
        orientation:'bottom'
    });

    $("[name=generate_documents_template_file_id]").on("change", function(){
        $("#generate_document").prop("disabled", this.selectedIndex <= 0);
    });

    $('#generate_document, #generate_document_bulk, #generate_document_no_print, #generate_document_bulk_no_print, #generate_document_no_print_zip').click(function(ev){
        var btn = this;
        ev.preventDefault();

            var btnhtml = $(btn).html();
            try {
                $("#generate_document_result").html("");
                $(btn).html("Please wait...");
                $(btn).prop("disabled", true);
                var data = {};
                data.report_id = $("#report_id").val();
                data.bulk = (this.id == 'generate_document_bulk' || this.id == 'generate_document_bulk_no_print' ? 1 : 0);
                data.noprint = (this.id == 'generate_document_no_print' || this.id == 'generate_document_bulk_no_print' || this.id == 'generate_document_no_print_zip') ? 1 : 0;
                data.zip = this.id == 'generate_document_no_print_zip' ? 1 : 0;
                //data.parameters = prepare_parameters();
                data.parameters = {};
                $("#temporary_parameters input, #temporary_parameters select, #temporary_parameters textarea").each(function(){
                    var param = $(this).data("param");
                    if (!param)return;
                    
                    if (this.multiple && this.options[0].selected) { // All
                        for (var i = 0; i < this.options.length; ++i) {
                            this.options[i].selected = true;
                        }
                    }

                    data.parameters[param] = $(this).val();
                    if (param.indexOf('_id') > 0) {
                        if (this.options) {
                            var selectedCount = 0;
                            for (var i = 0; i < this.options.length; ++i) {
                                if (this.options[i].selected) {
                                    ++selectedCount;
                                }
                            }
                            if (selectedCount > 1) {
                                data.bulk = 1;
                            }
                            if (this.options[this.selectedIndex]) {
                                data.parameters[param.replace('_id', '')] = this.options[this.selectedIndex].innerHTML;
                            }
                        }
                    }

                });
                data.table = [];
                if (data.bulk == 0) {
                    var $th = $('#report_table thead tr th');
                    $('#report_table tbody tr').each(function () {
                        var row = {};
                        var $td = $(this).find("td");
                        for (var i = 0; i < $td.length; ++i) {
                            var name = $th[i].innerHTML;
                            var value = "";
                            if ($($td[i]).find("input").length > 0) {
                                value = $($td[i]).find("input").val();
                            } else if ($($td[i]).find("select").length > 0) {
                                value = $($td[i]).find("select option:selected").text();
                            } else {
                                value = $td[i].innerHTML;
                            }
                            row[name] = value;
                        }
                        data.table.push(row);
                    });
                }
                if (data.table.length == 0) {
                    data.bulk = 1;
                }
                data.table = JSON.stringify(data.table);
                $.post(
                    "/admin/reports/generate_documents",
                    data,
                    function (response) {
                        function printGenerated(result) {
                            if (result.messageCreated) {
                                $("#generate_document_result").append(
                                    '<p>Message has been created. <a target="_blank" href="/admin/messaging/details?message_id=' + result.messageCreated + '">' + result.messageCreated + '</a>'
                                );
                            } else {
                                if (!result.noprint) {
                                    $("#generate_document_result").append(
                                        '<p>Your statement has not been printed</p>'
                                    );
                                }
                            }
                            if (result.files && result.files.length) {
                                $("#generate_document_result").append('<p>Generated Documents</p>');
                                for (var i = 0; i < result.files.length; ++i) {
                                    $("#generate_document_result").append(
                                        '<a target="_blank" href="/admin/files/download_file?file_id=' + result.files[i].file_id + '">' + result.files[i].filename + '</a><br />'
                                    );
                                }
                            }
                        }
                        if (data.bulk == 0) {
                            printGenerated(response);
                        } else {
                            for(var n = 0 ; n < response.results.length ; ++n) {
                                if (response.results[n].print) {
                                    printGenerated(response.results[n].print);
                                }
                            }
                        }
                        $(btn).html(btnhtml);
                        $(btn).prop("disabled", false);
                    }
                );
            } catch (exc) {
                $(btn).html(btnhtml);
                $(btn).prop("disabled", false);
            }
    });
});

function getMonth(monthyear)
{
    var date = monthyear.split('y');
    var year = date[1],
        month = date[0];
    if (month.length == 1) {
        month = '0' + month;
    }
    var dayInMonth = new Date(year, month, 0).getDate(),
        from = year + '-' + month + '-' + '01',
        to = year + '-' + month + '-' + dayInMonth;
    return {from: from, to: to};
}

function replaceDateRangeParams(date, sql){
    if (typeof date != 'undefined' && date.length) {
        if (typeof date == 'object'){
            var rgx = /([a-z]+)\s*>=\s*'{!month_range_from!}'\s*and\s*([a-z]+)\s*<=\s*'{!month_range_to!}'/g.exec(sql);
            var orl = [];
            for (var i = 0 ; i < date.length ; ++i) {
                var month = getMonth(date[i]);
                orl.push (
                    "(" + rgx[1] + " >= '" + month.from + "' AND " + rgx[2] + " <= '" + month.to + "')"
                );
            }
            sql = sql.replace(rgx[0], ' (' + orl.join(' OR ') + ') ');
        } else {
            var month = getMonth(date);

            sql = sql.replace(/{!month_range_from!}/g, month.from);
            sql = sql.replace(/{!month_range_to!}/g, month.to);
        }
    }
    return sql;
}

function add_date_picker()
{
    $(".series_div span input.datepicker").each(function(){
        $(this).datepicker({
			format: window.ibcms.date_format,
			autoclose: true,
			orientation:'bottom'
		});
    });
}

function draw_keywords_table(result)
{
    $("#keywords_table").html(result);
    keywords_table = $("#keywords_table").dataTable();
}

function draw_table(id,sql){

    if($('#report_table tbody tr').length > 0)    {
        $('#report_table').dataTable().fnDestroy();
    }
    console.log("Logging SQL");
    console.log(sql);
    $.post('/admin/reports/get_report_table',{
        id: id,
        sql:sql,
        parameters: $("#parameter_fields").val()
    },function(result){
        $('#report_table').html(result);

        if (document.getElementById('report_table-records_found')) {
            var number_of_records = document.getElementById('report_table').querySelectorAll('tbody tr').length;

            document.getElementById('report_table-records_found-amount').innerHTML = number_of_records;
            document.getElementById('report_table-records_found').classList.remove('hidden');
        }

        autocheck_boxes();
        post_run_rules();
        if($("[name=checkbox_column]:checked").val() == "1"){
            get_checkbox_totals();
        }
        $('#report_table').dataTable({"aaSorting": []});
        //$('#report_table').dataTable({"aoColumns":[{"bSortable":false}]});
        //
        $(document).on('keyup',"tfoot input",function (){
            $('#report_table').dataTable().fnFilter( this.value, $("tfoot input").index(this) );
        });

        $("tfoot input").focus(function(){
            if(this.className == "search_init" ){
                this.className = "";
                this.value = "";
            }
        }).blur(function(i){
            if(this.value == ""){
                this.className = "search_init";
                this.value = asInitVals[$("tfoot input").index(this)];
            }
        });
    });
}

function send_table(id,sql){

    if($('#report_table tbody tr').length > 0)    {
        $('#report_table').dataTable().fnDestroy();
    }

    $.post('/admin/reports/send_report',{
        id: id,
        sql:sql,
        parameters: $("#parameter_fields").val()
    }, function(result){
        alert("Completed");
    });
}

function draw_widget(id)
{
    $.post('/admin/reports/widget_json/'+id,{parameters: $("#parameter_fields").val(),sql:$("#sql").val()},function(result){
        if(result == 'undefined')
        {
            //do nothing...
        }
        else
        {
            eval(result);
        }
        $("svg text:last").remove();
    });
}

function draw_chart(id)
{
    $.post('/admin/reports/chart_json/'+id,{parameters: $("#parameter_fields").val()},function(result){
        if(result == 'undefined')
        {
            //do nothing...
        }
        else
        {
            eval(result);
        }
        $("svg text:last").remove();
    });
}

function form_submit()
{
    $("#report_edit_form").submit();
}

$('#report_edit_form').on('submit', function()
{
	if ( ! $(this).validationEngine('validate'))
	{
		return false;
	}
});

function prepare_form()
{
    form_submit();
}

function first(obj) {
    for(var a in obj) return obj[a];
}

function redraw_custom_parameter(input, dynamic_parameters)
{
    var values = get_temporary_parameter_values();
    var param = input.getAttribute("data-param");
    var redraw = false;
    values.report_id = $("#report_id").val();
    values.parameter_fields = $("#parameter_fields").val();
    if(dynamic_parameters && dynamic_parameters.length > 0){
        for(var custom_input_id in dynamic_parameters){
            if(dynamic_parameters[custom_input_id][param]){
                values.query = "";
                values.param = custom_input_id;
                $.ajax({
                    type: 'POST',
                    url: '/admin/reports/ajax_get_custom_fields',
                    data: values,
                    success: function(data) {
                        var answer = data;
                        var param = answer.param;
                        var $replace = $("[data-param='" + param + "']");
                        console.log("loaded: " + param);
                        var ihtml = '';
                        var is_multiselect = $(".parameter [value='" + param + "']").parent().find(".is_multiselect").prop("checked")
                        switch(answer.status)
						{
                            case 'success':

								var $clone = $('#report-parameter-template-select').clone();
								$clone.find('select')
									.data('param', param)
									.attr('data-param', param);

								if (is_multiselect) {
									$clone.find('select').attr('multiple', 'multiple').addClass('multipleselect');
								}

								var options = '<option value="">' + (is_multiselect ? 'All' : '     ') + '</option>';

								for(var key in answer.rows)
								{
									var first = true;
									var value = null;
									var text = null;
									for(var x in answer.rows[key]){
										if(first){
											first = false;
											value = text = answer.rows[key][x];
										}
										else {
											text += (text != "" ? " - " : "") + answer.rows[key][x];
										}
                                        if (text == null) {
                                            text = "";
                                        }
									}
									options += '<option value="' + value + '">' + text + '</option>';
								}

								$clone.find('select').html(options);
                                ihtml += $clone.html();
                                break;

                            case 'error':
                                ihtml = '<label data-param="' + param + '" class="label label-danger" style="clear:left;float:left;display:block;">SQL statement has some mistake(s)</label>';
                                break;

                            case 'empty':
                                ihtml = '<label data-param="' + param + '" class="label label-warning" style="clear:left;float:left;display:block;">Result of the query is empty</label>';
                                break;
                        }
                        $replace.replaceWith(ihtml);
                    },
                    error:  function(msg, str){
                        $('.loading-warning').text('Can\'t connect to server').show();
                    }
                });
            }
        }
    }
}

function redraw_temporary_parameters()
{
    var html = '',
        custom_label_id = 0,
        custom_input_id = 0,
        custom_input = [];

    var dynamic_parameters = {length:0};

    $("#parameter_area .parameter .form-group").each(function(){
        var type = $(this).children(".parameter_picker").val();
        if(type == 'custom'){
            var param = $(this).children(':nth-child(3)').val();
            var sql = $(this).find('textarea').val();
            var dparams = sql.match(/\{\!([a-z0-9_]+)\!\}/gi);
            if(dparams && dparams.length > 0){
                ++dynamic_parameters.length;
                for(var i in dparams){
                    var dparam = dparams[i].replace('{!', '').replace('!}', '');
                    if(!dynamic_parameters[param]){
                        dynamic_parameters[param] = {};
                    }
                    dynamic_parameters[param][dparam] = dparam;
                }
            }
        }
    });

    var ajax_complete = 0;
    $('#parameter_area').find('.form-group').each(function(){
        var type = $(this).children(".parameter_picker").val();
        var input = '';
		var value;
        var is_multiselect = $(this).find(".is_multiselect").prop("checked") ? true : false;
        var param = $(this).children(':nth-child(3)').val();
        switch(type) {
            case 'date':
				value  = $(this).children(".input_date").val();
				var $clone = $('#report-parameter-template-date').clone();
				$clone.find('input')
					.data('param', param)
					.attr('data-param', param)
					.attr('value', value)
					.val(value)
				;

				input = $clone.html();
				$clone.remove();
                break;

            case 'month':
                var selected = $(this).children(".input_text").val();
                var monthDisplay = 26;
                var options = '';
                var currentYear = new Date().getFullYear();
                var currentMonth = new Date().getMonth();
                var monthNames = [ "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December" ];

                for(var months = 0; months < monthDisplay; months++){
                    if (currentMonth == -1){
                        currentMonth = 11;
                        currentYear--;
                    }
                    value = currentMonth + 1 + 'y' + currentYear;
                    var isSelected = (value == selected) ? 'selected' : '';
                    options += '<option value="' + value + '"' + isSelected + '>' + monthNames[currentMonth] + ' ' + currentYear + '</option>';
                    currentMonth--;
                }

                input = '<select data-param="' + param + '" class="temporary_value form-input month_range' + (is_multiselect ? " multipleselect" : "") + '" name="month_value" ' + (is_multiselect ? "multiple" : "") + '>' + options + '<select>';
                break;

            case 'text':
                input = '<input data-param="' + param + '" type="text" class="form-input temporary_value input_text" value="'+$(this).children(".input_text").val()+'"/>';
                break;

            case 'sql':
                input = '<select data-param="' + param + '" class="temporary_value form-input value_input input_select' + (is_multiselect ? "multipleselect" : "") + '" ' + (is_multiselect ? "multiple" : "") + '>'+$(this).children(".input_select").html()+'</select>';
                $(input).val($(this).children(".input_select").val());
                break;

            case 'dropdown':
                var dropdownOptions = $(this).children(".input_text").val().split(';');
                input = '<select data-param="' + param + '" class="temporary_value form-input value_input input_select' + (is_multiselect ? " multipleselect" : "") + '" ' + (is_multiselect ? "multiple" : "") + '>';
                for(var key in dropdownOptions){
                    if(dropdownOptions[key]){
                        input += '<option value="' + dropdownOptions[key].trim() + '">' + dropdownOptions[key].trim() + '</option>';
                    }
                }
                input += '</select>';
                break;

            case 'user_id':
                input = '<select data-param="' + param + '" class="temporary_value form-input value_input input_select' + (is_multiselect ? " multipleselect" : "") + '" ' + (is_multiselect ? "multiple" : "") + '>';
                input += '</select>';
                $.ajax({
                    type: 'POST',
                    url: '/admin/reports/ajax_get_users',
                    data: {
                        param: param,
                        role_id: "",
                        report_id: $("#report_edit_form #report_id").val(),
                        id: custom_input_id
                    },
                    success: function(data) {
                        var param = data.param;
                        var select = $(".temporary_value[data-param=" + param + "]");
                        select.find("option").remove();
                        select.append('<option value=""></option>')
                        select.append('<option value="logged">Logged User</option>')
                        for (var i = 0 ; i < data.users.length ; ++i) {
                            select.append('<option value="' + data.users[i].id + '">' + data.users[i].full_name + '</option>');
                        }
                    },
                    error:  function(msg, str){
                        $('.loading-warning').text('Can\'t connect to server').show();
                    }
                });
                break;

            case 'custom':
                input = '<label data-param="' + param + '" id="custom_'+ custom_label_id +'" class="label label-success" style="float:left;clear:left;display:block;">Waiting...</label>';
                custom_label_id++;

                ++ajax_complete;
                $.ajax({
                    type: 'POST',
                    url: '/admin/reports/ajax_get_custom_fields',
                    data: {
                        query: $(this).children(".input_textarea").val().trim(),
                        id: custom_input_id,
                        report_id: $('#report_id').val(),
                        param: param,
                        parameter_fields: $("#parameter_fields").val()
                    },
                    success: function(data) {
                        --ajax_complete;
                        var answer = data,
                            label,
                            input_id = answer.custom_input_id;
                        switch(answer.status)
						{
                            case 'success':
								$clone = $('#report-parameter-template-select').clone();
								$clone.find('select')
									.data('param', param)
									.attr('data-param', param)
									.attr('id', 'custom_'+ custom_label_id);

								if (is_multiselect) {
									$clone.find('select').attr('multiple', 'multiple').addClass('multipleselect');
								}

								var options = '<option value="">' + (is_multiselect ? 'All' : '     ') + '</option>';

                                for(var key in answer.rows){
                                    label = '';
                                    for(var id in answer.rows[key]){
                                        label += (answer.rows[key][id] != null ? answer.rows[key][id] + '-' : '');
                                    }
                                    options += '<option value="' + first(answer.rows[key]) + '">' + label.slice(0, -1) + '</option>';
                                }
								$clone.find('select').html(options);

                                // Select lists with more than 20 options to become type selects
                                if (answer.rows.length > 20) {
                                    $clone.find('select').addClass('report-combobox');
                                }

                                custom_input[input_id] = $clone.html();
								break;

                            case 'error':
                                custom_input[input_id] = '<label data-param="' + param + '" id="custom_'+ custom_label_id +'" class="label label-danger" style="float:left;clear:left;display:block;">SQL statement has some mistake(s)</label>';
                                break;

                            case 'empty':
                                custom_input[input_id] = '<label data-param="' + param + '" id="custom_'+ custom_label_id +'" class="label label-warning" style="float:left;clear:left;display:block;">Result of the query is empty</label>';
                                break;
                        }
                        $("#custom_" + input_id).replaceWith(custom_input[input_id]);

                        $('#temporary_parameters').find('.report-combobox').combobox();
                    },
                    error:  function(msg, str){
                        // $('.loading-warning').text('Can\'t connect to server').show();
                    }
                });
                custom_input_id++;
                break;
            default:
                input = '<input data-param="' + param + '" type="text" class="form-input temporary_value" value="'+$(this).children(".input_date").val()+'"/>';
                break;
        }

		// var $clone = $('#report-parameter-template-date').clone();
		// $clone.removeAttr('id');
		// $clone.find('input').replaceWith(input);

        html+= '<div class="col-sm-4"><div class="form-group" style="margin: 0 0 25px;">' +
            '<label class="control-label" style="display: block;text-align: left;">'+$(this).children(":nth-child(3)").val()+'</label>' + input +
			'</div></div>';
    });
    $("#temporary_parameters").html(html);
    $("#temporary_parameters input.datepicker").off("change");
    $("#temporary_parameters input.datepicker").on('changeDate', function (ev) {
        //$(this).change();
    });

    function temporary_parameter_onchange_handler()
    {
        $(document).off("change", "#temporary_parameters [data-param]");
        redraw_custom_parameter(this, dynamic_parameters);
        $(document).on("change", "#temporary_parameters [data-param]", temporary_parameter_onchange_handler);
    }

    $(document).on("change", "#temporary_parameters [data-param]", temporary_parameter_onchange_handler);


    /* Colour picker */
    var palette = document.getElementById('color_palette');

	if (palette)
	{
		var custom_color_link = $('#custom_color_link');

		// Dismiss palette when clicked away from
		$(window).on('click', function(ev)
		{
			ev.stopPropagation();
			palette.style.display = 'none';
		});
		// Show palette when colour box is clicked
		$('.select_color_preview').on('click', function(ev)
		{
			ev.stopPropagation();
			$(palette).appendTo($(this).parent());
			palette.style.display = 'block';
		});

		// Set colour, when a colour from the palette is clicked
		$('#color_palette').on('click touchup', 'tbody td[style]:not([colspan])', function()
		{
			var color  = ($(this).hasClass('transparent_option')) ? 'transparent' : $(this).css('background-color');
			$(this).parents('.form-group').find('.color_picker_input').val(color);
			$(this).parents('.form-group').find('.select_color_preview').css('background-color', color);
			palette.style.display = 'none';
		});

		// make spectrum appear when "custom" is clicked
		custom_color_link.on('click touchup', function(ev)
		{
			ev.preventDefault();
			$(this).find('input').spectrum();
			$(this).find('.sp-replacer').click();
			$('.sp-container').appendTo(document.getElementById('color_palette'));

			// Custom colour picker dismissed when the colour is clicked
			$('.sp-dragger').on('mouseup touchend', function()
			{
				$('.sp-choose').click();
			});
			return false;
		});

		// put value from spectrum into empty custom colour cell
		custom_color_link.find('input').on('change', function()
		{
			var custom_palette = $('.custom_palette');
			// Create a new row, if necessary
			if (custom_palette.find('td:not([style])').length == 0)
			{
				custom_palette.append('<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
			}

			var color = custom_color_link.find('.sp-preview-inner').css('background-color');
			custom_palette.find('td:not([style])').first().css('background-color', color);
		});
	}
}

function get_temporary_parameter_values()
{
    prepare_parameters();
    var parameters = {};
    if($("#temporary_parameters div.form-group").size() > 0)
    {
        var type = '';
        $('#temporary_parameters div.form-group').each(function()
        {
            var param = $(this).find('>label.control-label').text();
            var val = $(this).find('input, select').val();

			if (typeof param == 'string') param = param.trim().replace('Waiting...', '');

            parameters[param] = val;
        });
    }
    else
    {
        $('.parameter .form-input').each(function()
        {
            var param = $(this).children(':nth-child(3)').val();
            var val = $(this).children(':nth-child(4)').val();

			if (typeof param == 'string') param = param.trim().replace('Waiting...', '');

            parameters[param] = val;
        });
    }
    return parameters;
}

function prepare_parameters()
{
    var parameters = [];
    if($("#temporary_parameters div.form-group").size() > 0)
    {
        var type = '';
        $('#temporary_parameters div.form-group').each(function()
        {
            var multi_select = $(this).find('select[multiple]')[0];
            if (multi_select && multi_select.options[0].selected && multi_select.options[0].value == "") { // all selected
                for (var i = 1 ; i < multi_select.options.length ; ++i) {
                    multi_select.options[i].selected = true;
                }
            }
            type = this.getElementsByClassName('input_date')[0] ? 'date' : 'text';
            if (type == 'text') {
                var var_type = $("#parameter_area .parameter input[value='" + $(this).find('>label.control-label').text() + "']")
                    .parent().find(".parameter_picker").val();
                if (var_type == 'user_id') {
                    type = var_type;
                }
            }
            var element = [
                'parameter_id_',
                type,
                $(this).find('>label.control-label').text().trim(),
                $(this).find('input, select').val(),
                ($(this).find('select[multiple]').length > 0 ? 1 : 0)
            ];

            parameters.push(element);
        });
    }
    else
    {
        $('.parameter .form-input').each(function()
        {
            var element = [
                $(this).children(':nth-child(1)').val(),
                $(this).children(':nth-child(2)').val(),
                $(this).children(':nth-child(3)').val(),
                $(this).children(':nth-child(4)').val(),
                ((this).find('.is_multiselect').prop('checked') ? 1 : 0)
            ]
            parameters.push(element);
        });
    }
    $('#parameter_fields').val(JSON.stringify(parameters));
    return $('#parameter_fields').val();
}

function parameter_sorting()
{
    $(".parameter_picker").each(function(){
        switch($(this).val())
        {
            case 'date':
                $(this).siblings('.input_text').hide();
                $(this).siblings('.input_select').hide();
                $(this).siblings('.input_date').show();
                $(this).siblings('.input_textarea').hide();
                break;
            case 'month':
                $(this).siblings('.input_text').hide();
                $(this).siblings('.input_select').hide();
                $(this).siblings('.input_date').hide();
                $(this).siblings('.input_textarea').hide();
                break;
            case 'text':
                $(this).siblings('.input_text').show();
                $(this).siblings('.input_select').hide();
                $(this).siblings('.input_date').hide();
                $(this).siblings('.input_textarea').hide();
                break;
            case 'sql':
                $(this).siblings('.input_text').hide();
                $(this).siblings('.input_select').show();
                $(this).siblings('.input_date').hide();
                $(this).siblings('.input_textarea').hide();
                break;
            case 'custom':
                $(this).siblings('.input_text').hide();
                $(this).siblings('.input_select').hide();
                $(this).siblings('.input_date').hide();
                var customTextarea = $(this).siblings('.input_textarea').text();
                $(this).siblings('.input_textarea').text(customTextarea.trim());
                $(this).siblings('.input_textarea').show();
                break;
            case 'dropdown':
                $(this).siblings('.input_text').show();
                $(this).siblings('.input_select').hide();
                $(this).siblings('.input_date').hide();
                $(this).siblings('.input_textarea').hide();
                break;
            default:
                $(this).siblings('.input_text').show();
                $(this).siblings('.input_select').hide();
                $(this).siblings('.input_date').hide();
                $(this).siblings('.input_textarea').hide();
                break;
        }
    });
}

function generate_keywords_json(keywords_table)
{
    var result = [];
    $('tbody tr',keywords_table).each(function(){
        var keyword = $(this).children('td:nth-child(2)').text();
        var url     = $(this).children('td:nth-child(3)').text();
        result.push({keyword:keyword,url:url});
    });

    return JSON.stringify(result);
}

function invoke_custom_script(button)
{
    if($('#report_table tr').length>0){
        //$('#report_table').dataTable().fnDestroy();
        try {
            var code = $("#action_event").val();
            console.log(code);
            eval(code);
        } catch(exc) {
            console.log(exc);
        }
        //$('#report_table').dataTable();
    }else{
        button.disabled = false;
    }
}

function get_column_id(column_text)
{
    var $th = $("#report_table thead tr th");
    for(var i = 0 ; i < $th.length ; ++i){
        if($th[i].innerHTML == column_text){
            return i;
        }
    }
    return $("#report_table thead tr th").index($("#report_table thead tr th:contains('"+column_text+"')"));
}

function get_column_values(column_id,where_checked)
{
    column_id = column_id + 1;
    var result = [];

    if(where_checked == true)
    {
        $('input.row_check:checked',"#report_table tbody tr").closest('tr').each(function(){
            result.push($(":nth-child("+column_id+")",this).text());
        });
    }
    else
    {
        $("#report_table tbody tr").each(function(){
            result.push($(":nth-child("+column_id+")",this).text());
        });
    }

    return result;
}

function as_json(array)
{
    return JSON.stringify(array);
}

function autocheck_boxes()
{
    if($("[name=autocheck]:checked").val() == "1" && $("[name=checkbox_column]:checked").val() == "1")
    {
        $("#report_table").find("tbody input[type='checkbox']").prop("checked", true);
    }
}

function get_checkbox_totals(){
    //finding column number by <th> content
    var count = 0;
    var column_number = 0;
    $("#report_table th").each(function(){

        if($(this).attr('id') == 'selected') column_number = count;

        count++;

    });

    var checkbox_column_id = get_column_id($('[name=checkbox_column_label]').val());

    var total = 0.0;
    var $tr = $('#report_table tbody tr');
    for(var i = 0 ; i < $tr.length ; ++i){
        var row = $tr[i];
        if($(row).find('.row_check').prop('checked')){
            var td = $(row).children('td').eq(column_number);
            var value = parseFloat(td.html().replace(",", ""));

            if(value){
                total += value;
            }
        }
    }
    $('input.total_settlement').val(total.toFixed(2));
}

function post_run_rules()
{
    if($("#custom_report_rules").val() != '')
    {
        try {
            console.log("Executing Rules");
            eval($("#custom_report_rules").val());
        } catch(exc) {
            console.log(exc);
        }
    }
}

function add_alert(message, type)
{
    document.getElementById('edit_report_alert_area').innerHTML +=
        '<div class="alert'+((type) ? ' alert-'+type : '')+'">' +
        '<a href="#" class="close" data-dismiss="alert">&times;</a> ' + message +
        '</div>';
}
