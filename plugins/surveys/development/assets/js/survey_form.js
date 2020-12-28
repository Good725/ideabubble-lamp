$(document).ready(function ()
{
    toggle_set_expiry_view();
    toggle_select_template();
    toggle_select_page();
    toggle_pagination();
    toggle_results_view();
    course_autocomplete();

    $('.sortable-tbody').sortable({cancel: 'a, button, :input, label'});

    setTimeout(function(){
            if( $('a[href=#reports]').parent().hasClass('active')){
                toggle_reports_tab();
            }
        },
        500
    );

    if($('input[active]').val() == '0'){
        disable_and_hide_backend_survey_inputs(true)
    }

    $('#question_already_selected').hide();

    $('#add_question_btn').on('click', function ()
    {
        var table = 'questions_list_table';
        var item = $('#list_questions').val();
        //var group = $('#list_groups').val();
        var group=$('#list_questions').find('option:selected').attr("data-grp");
        var question_added = is_item_in_rows_db(table, item);
        $("#"+table+" .sortable-tbody tr:not([class*=question_group]").each(function(){
			 if($(this).attr('data-question_id')==item && $(this).attr('data-group_id')==group){
				 question_added=true;
				 return false;
			 }else{
				question_added=false; 
			}	  
			 
		});
       
        if (question_added)
        {
            $('#question_already_selected').show();
        }
        else if (item != '' && item != 0 && item != 'undefined')
        {
            add_question(item,group);
            $('#question_already_selected').hide();
        }
    });

    $("#questions_list_table").on('click', '.remove-button', function ()
    {
        $(this).parents('tr').remove();
    });

    $(document).on('click', '#set_download_yes', function()
    {
        if ($('#documents').val() == 0)
        {
            var smg = '<div class="alert alert-warning"><a class="close" data-dismiss="alert">X</a><strong>Warning: </strong> Files and Document plugin are required to download PDF.</div>';
            $("#main").prepend(smg);
        }
    });

    $(document).on('click', '#expiry_date',function ()
    {
        toggle_set_expiry_view() ;
    });

    $(document).on('click','#display_thank_you',function()
    {
        toggle_select_page();
    });

    $(document).on('click','#set_download',function()
    {
        toggle_select_template();
    });

    $(document).on('click','#set_pagination',function()
    {
        toggle_pagination();
    });

    $("#form_add_edit_survey").validate({
        submitHandler: function (form)
        {
            $('#questions_ids').val(generate_json_for_questions_table());
            $('#sequence_list').val(generate_json_for_sequence());
            form.submit();
        }
    });

    $('#redirect_survey').on('click',function ()
    {
        var survey = $('#id').val();
        var sequence_id = $('#sequence_id').val();
        if (survey != '' || sequence_id != '')
        {
            get_questionnaire(survey, sequence_id);
        }
    });

    $('#preview_survey').on('click', function ()
    {
        $('#questions_ids').val(generate_json_for_questions_table());
        $.ajax({
            type: 'POST',
            url: '/admin/surveys/ajax_get_survey_selected_question_preview/',
            data: {questions: $('#questions_ids').val()},
            dataType: 'json'
        })
            .done(function (results)
            {
                $('#preview_tab').html('');
                var html = '';
                var input = '';
                var input_end = '';
                var q_name = true;
                var group_id = null;
                var pagination = $("input[name='pagination']:checked").val() == 1;
                $.each(results,function (key, question)
                {
                    if (group_id !== question.group_id && pagination)
                    {
                        if (group_id != null){
                            html+= '</fieldset><br>';
                        }
                        group_id = question.group_id;
                        html += '<fieldset><legend>'+ question.group_title+'</legend>';
                    }
                    var type = question.stub;
                    switch (type)
                    {
                        case 'radio':
                            input = '<input type="radio" disable="disabled" name="question_';
                            input_end = '"/>';
                            q_name = true;
                            break;
                        case 'textarea':
                            input = '<textarea rows="4" cols="40" disabled="disabled" name=question_';
                            input_end = '">Your Comment goes here</textarea>';
                            q_name = false;
                            break;
                        case 'input':
                            input = '<input type="text" disable="disabled" name="answer_';
                            input_end = '"/>';
                            q_name = false;
                            break;
                    }
                    html += '<h3 class="question_display">Q' + (key + 1) + ': ' + question.question + '</h3>';
                    $.each(question.answers ,function (k,answer)
                    {
                        html += '<span class="answer_display">'+ input
                                + (q_name ? key : k ) + input_end
                                + ' ' + answer.label + '</span><br>';
                    });
                });
                if(pagination)
                {
                    html+= '</fieldset><br>';
                }
                $('#preview_tab').html(html);
            });
    });

    $('#questionnaire_sequences').on('change', '.answer_action', function()
    {
        $(this).parents('.set-sequence-item-options').find('.sequence_answers').data('survey_action',this.value);
    });
    $('#questionnaire_sequences').on('change','.answer_target_question',function()
    {
        $(this).parents('.set-sequence-item-option').find('.sequence_answers').data('target_id',this.value);
    });

    $('#form_add_edit_survey').on('change', '#is_backend', function(){
        var is_backend = $('#is_backend').find('input[name=is_backend]:checked').val();
        if(is_backend == '1'){
            disable_and_hide_backend_survey_inputs(false);
        } else{
            disable_and_hide_backend_survey_inputs(true);
        }
    });

});

function add_question(id,group)
{
    if (id != '')
    {
        var table = 'questions_list_table';
        var question = $('#list_questions').find('[value="' + id + '"]');
        var row = '';
        if(group == '') {group='0';}
        row += '<tr id="' + add_item_to_rows_db(table, question.val()) + '" data-id="" data-question_id="' + question.val() + '" data-group_id="'+group+'">';
        row += '<td><span class="icon-bars"></span></td>';
        row += '<td>' + question.data('name') + '</td>';
        row += '<td>' + question.data('answer') + '</td>';
        row += '<td class="remove-row"><button class="btn-link remove-button"><span class="icon-remove"></span> Remove</button></td>';
        row += '</tr>';
        $('#questions_list_table').children('.sortable-tbody').children('[data-group_id="'+group+'"]:last').after(row);
       // var index = $('#questions_list_table').filter(' > tr [data-group_id="'+group+'"]').insertAfter(row);
       // $('#' + table).append(row);
    }
}

function toggle_set_expiry_view()
{
    if ($('#form_add_edit_survey').find('input[name="expiry"]:checked').val() == 1)
    {
        $('#set_expiry_date').show();
    }
    else
    {
        $('#set_expiry_date').hide();
    }
}

function toggle_results_view()
{
    if ($('#form_add_edit_survey').find('input[name="store_answer"]:checked').val() == 1)
    {
        $('#result_survey').show();
    }
    else
    {
        $('#result_survey').hide();
    }
}

function toggle_pagination()
{
    if ($('#form_add_edit_survey').find('input[name="pagination"]:checked').val() == 0)
    {
        $('.question_group').hide();
        $('#group_tab').hide();
        if ($('#id').val() != '')
        {
            $('#redirect_survey').show();
        }
        else
        {
            $('#redirect_survey').hide();
        }
    }
    else
    {
        $('#redirect_survey').hide();
        $('.question_group').show();
        var id = $('#id').val();
        if (id != '')
        {
            $.post('/admin/surveys/ajax_publish_sequence_for_survey', {survey_id: id}, function (data)
            {
                if (data.status === 'success')
                {}
                else
                {}
            }, "json");
        }
    }
}

function generate_json_for_questions_table()
{
    var data = [];
    var group = null;

    $('#questions_list_table').children('tbody').children('tr').each(function ()
    {
        if ($(this).data('group_row')==true && group != $(this).data('group_id'))
        {
            group = $(this).data('group_id');
        }
        else
        {
            var question = {
                id: $(this).data('id'),
                question_id: $(this).data('question_id'),
                group_id: group
            };
            data.push(question);
        }
    });

    return JSON.stringify(data);
}

function toggle_select_template()
{
    if ($('#documents').val() == 0)
    {
        $('#set_download_no').click();
        $('#select_template_download').hide();
        $('#set_download').prop("disabled", true);
    }
    else
    {
        if ($('#form_add_edit_survey').find('input[name="result_pdf_download"]:checked').val() == 1)
        {
            $('#select_template_download').show();
        }
        else
        {
            $('#select_template_download').hide();
        }
    }
}
function toggle_reports_tab(){

            /* Result Table*/
    /*$.ajax({
     type : 'POST',
     async : true,
     data : {
     id : 29,
     },
     dataType : 'text',
     url : '/admin/reports/get_report_table',
     success : function(result){
     console.log(result);
     //$('#report_table').html(result);
     if(result == 'undefined')
     {
     //do nothing...
     }
     else
     {
     $('#report_table').html(result);
     }
     }
     });*/
    draw_table(29)

    /* Question Time Chart*/
    var charts = [];
    charts[24] = 14;
    charts[26] = 16;
    for(var a in charts){
        $.ajax({
            type : 'POST',
            async : true,
            data : {'parameters':''},
            url : '/admin/reports/chart_json/'+a,
            beforeSend : function(){
                $('#chart_'+charts[a]).empty();
            },
            complete : function(d){},
            success : function(result){
                if(result == 'undefined')
                {
                    //do nothing...
                }
                else
                {
                    eval(result);
                }
            }
        })
    }
}
function draw_table(id){

    if($('#report_table tbody tr').length > 0)    {
        $('#report_table').dataTable().fnDestroy();
    }

    $.post('/admin/reports/get_report_table',{
        id: id,
        parameters: $("#parameter_fields").val()
    },function(result){
        $('#report_table').html(result);
        autocheck_boxes();
        if($("[name=checkbox_column]:checked").val() == "1"){
            get_checkbox_totals();
        }
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
function autocheck_boxes()
{
    if($("[name=autocheck]:checked").val() == "1" && $("[name=checkbox_column]:checked").val() == "1")
    {
        $("#report_table").find("tbody input[type='checkbox']").prop("checked", true);
    }
}

function toggle_select_page()
{
    if ($('#display_thank_you').find('input[name="display_thank_you"]:checked').val() == 1)
    {
        $('#select_page_id').show();
    }
    else
    {
        $('#select_page_id').hide();
    }
}

function get_questionnaire(survey_id,id)
{
    $('#questions_ids').val(generate_json_for_questions_table());

    $.ajax({
        type:'POST',
        url:'/admin/surveys/ajax_get_survey_for_sequence/',
        data:{survey_id:survey_id,id:id}
    })
        .success(function(result)
        {
            $('#questionnaire_sequences').html('');
            $('#questionnaire_sequences').html(result);
        });
}

function generate_json_for_sequence()
{
    var data = [];

    $('#questionnaire_sequences').find('.sequence_answers').each(function()
    {
        var question = {
            id          : $(this).data('id'),
            question_id : $(this).data('question_id'),
            action      : $(this).data('survey_action'),
            answer_id   : $(this).data('answer_id'),
            target_id   : $(this).data('target_id')
        };
        data.push(question);
    });

    return JSON.stringify(data);
}

function disable_and_hide_backend_survey_inputs(disable = true){
    $('#link_course_group').disableAndHide(disable);
    $('#link_subcontact_group').disableAndHide(disable);
}

function course_autocomplete(){
    var last_id = null;
    var last_label = null;

    $('#link_course_name').autocomplete({
        source: function(data, callback) {
            $.getJSON(
                "/frontend/courses/search_course", {
                    term: $("#link_course_name").val(),
                    with_id: 1
                }, callback
            )
        },
        select: function (event, ui) {
            console.log(event);
            console.log(ui);
            var id = ui.item.label.split(" - ")[0];
            $('input#course_id').val(id);
        }
    });
}