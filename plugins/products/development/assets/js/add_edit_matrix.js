var matrix_id = $("#id").val();
var option_1  = $("#option_1").val();
var option_2  = $("#option_2").val();

$(document).ready(function(){
    var ok = check_dropdowns();
    console.log("OK:"+ok);
    if(ok)
    {
        generate_table();
    }

    $('#add_edit_matrix_form').on('change','select',function(){
        var ok = check_dropdowns();
        if(ok)
        {
            generate_table();
        }
    });

    $(document).on('click','.add_edit_option',function(){
        if($(this).parent('td').children('.secondary_matrix').val() != "")
        {
			var json = $(this).parent('td').children('.secondary_matrix').val();
			if (json == '') json = 'null';
            var data = $.parseJSON(json);
            if (data != null && data != "null" && data != "undefined" && data[0].option_group != "undefined")
            {
                $("#modal_option_select").val(data[0].option_group);
                $("#modal_option_select").change();
                setTimeout(function(){
                    $.each(data,function(){
                        var selector = $("#third_option_table_holder table tbody tr td[data-option_2_id='"+this.option3+"'] span");
                        $(selector).data('price_adjustment', parseInt(this.price_adjustment));
                        $(selector).data('price',parseInt(this.price));
                    });
                },1500);
            }
        }

        $("#option2").data('option_2_id',$(this).closest('td').data('option_2_id'));
        $("#option2").data('option_1_id',$(this).closest('td').data('option_1_id'));
        $("#extra_data").data('option1',$("#option_1").val());
        $("#extra_data").data('option2',$("#option_2").val());
        $('#edit_modal').modal();
    });

    $("#modal_option_select").on('change',function(){
        var item = $(this);
        $.post('/admin/products/generate_matrix_sub_option',{option1:$("#option2").data('option_2_id'),option2:$(item).val()},function(data){
            $("#third_option_table_holder").html(data);
        });
    });

    $(document).on('click','.third_modal_close',function(){
        if($("#save_association").data('is_sub_option') == '1')
        {
            $('#edit_modal').modal('show');
            $('#association_modal').modal('hide');
        }
    });

    $(document).on('click','#third_option_table_holder span',function(){
        var item = $(this);
        if($(this).hasClass('icon-remove'))
        {
            $("#save_association").data('is_sub_option',1);
            $("#save_association").data('option_1_id',$(item).closest('td').data('option_1_id'));
            $("#save_association").data('option_2_id',$(item).closest('td').data('option_2_id'));
            console.log("Option1: "+$(item).closest('td').data('option_1_id'));
            $("#additional_price").val($("#third_option_table_holder table tbody tr td[data-option_1_id='"+$(item).closest('td').data('option_1_id')+"'][data-option_2_id='"+$(item).closest('td').data('option_2_id')+"'] span").data('price'));
            $("#additional_price_toggle").val($("#third_option_table_holder table tbody tr td[data-option_1_id='"+$(item).closest('td').data('option_1_id')+"'][data-option_2_id='"+$(item).closest('td').data('option_2_id')+"'] span").data('price_adjustment'));
            if($("#additional_price_toggle").val() == 1)
            {
                $("#association_modal .btn-group button[value='0']").removeClass('active');
                $("#association_modal .btn-group button[value='1']").addClass('active');
            }
            else
            {
                $("#association_modal .btn-group button[value='0']").addClass('active');
                $("#association_modal .btn-group button[value='1']").removeClass('active');
            }

            $(this).removeClass('icon-remove');
            $(this).addClass('icon-ok');
            $('#edit_modal').modal('hide');
            $('#association_modal').modal();
        }
        else
        {
            $(this).removeClass('icon-ok');
            $(this).addClass('icon-remove');
        }
    });

    $(document).on('click','#matrix_table span',function(){
        var item = $(this);
        if($(this).hasClass('icon-remove'))
        {
            $(item).data('publish',1);
            $("#save_association").data('is_sub_option',0);
            $("#save_association").data('option_1_id',$(item).closest('td').data('option_1_id'));
            $("#save_association").data('option_2_id',$(item).closest('td').data('option_2_id'));
            $(this).removeClass('icon-remove');
            $(this).addClass('icon-ok');
            $('#edit_modal').modal('hide');
            $('#association_modal_subtitle').html(this.getAttribute('data-option_1_label')+' &times; '+this.getAttribute('data-option_2_label'));
			$('#image_selector').val(item.data('image'));
            $('#association_modal').modal();
        }
        else
        {
            $(item).data('publish',0);
            $(this).removeClass('icon-ok');
            $(this).addClass('icon-remove');
        }
    });

    $(document).on('click','#save_association',function()
    {
        var option1 = $(this).data('option_1_id');
        var option2 = $(this).data('option_2_id');
        if($(this).data('is_sub_option') == '0')
        {
            $("#matrix_table tr td[data-option_1_id='"+option1+"'][data-option_2_id='"+option2+"'] span").data('price_adjustment',$("#additional_price_toggle").val());
            $("#matrix_table tr td[data-option_1_id='"+option1+"'][data-option_2_id='"+option2+"'] span").data('price',$("#additional_price").val());
            $("#matrix_table tr td[data-option_1_id='"+option1+"'][data-option_2_id='"+option2+"'] span").data('image',$("#image_selector").val());
        }
        else
        {
            $("#third_option_table_holder table tbody tr td[data-option_1_id='"+option1+"'][data-option_2_id='"+option2+"'] span").data('price_adjustment',$("#additional_price_toggle").val());
            $("#third_option_table_holder table tbody tr td[data-option_1_id='"+option1+"'][data-option_2_id='"+option2+"'] span").data('price',$("#additional_price").val());
            $("#third_option_table_holder table tbody tr td[data-option_1_id='"+option1+"'][data-option_2_id='"+option2+"'] span").data('image',$("#image_selector").val());
        }

        $("#additional_price").val('');
    });

    $(document).on('click','#add_edit_sub_matrix',function(){
        var data = [];
        var option1 = $("#option2").data('option_1_id');
        var option2 = $("#option2").data('option_2_id');

        $("#third_option_table_holder table tbody tr td:nth-child(2)").each(function(){
            data.push({option1:option1,option2:$(this).data('option_1_id'),option3:$(this).data('option_2_id'),price_adjustment:$('span',this).data('price_adjustment'),price:$('span',this).data('price'),option_group:$("#modal_option_select").val()});
        });

        $("#matrix_table tbody tr td[data-option_2_id='"+option2+"'][data-option_1_id='"+option1+"'] .secondary_matrix").val(JSON.stringify(data));
    });

    $(".save_btn").on('click',function(){
        $("#matrix_data").val(generate_json_for_matrix());
        $("#add_edit_matrix_form").submit();
    });
});

function check_dropdowns()
{
    $("#option_1 option").each(function(){
        $(this).prop('disabled',false);
    });
    $("#option_2 option").each(function(){
        $(this).prop('disabled',false);
    });

    $("#modal_option_select option").each(function(){
        $(this).prop('disabled',false);
    });

    var option_1 = $("#option_1").val();
    var option_2 = $("#option_2").val();

    if(option_1 != "")
    {
        $("#option_2 option[value='"+option_1+"']").prop('disabled',true);
    }

    if(option_2 != "")
    {
        $("#option_1 option[value='"+option_2+"']").prop('disabled',true);
    }

    if(option_2 != "" && option_1 != "")
    {
        $("#modal_option_select option[value='"+option_1+"']").prop('disabled',true);
        $("#modal_option_select option[value='"+option_2+"']").prop('disabled',true);
        $("#modal_option_select").val('');
    }

    var result = (option_1 != "" && option_2 != "") ? true : false;
    return result;
}

function generate_table()
{
    $.post('/admin/products/generate_matrix',{matrix_id:$("#id").val(),option1:$("#option_1").val(),option2:$("#option_2").val()},function(data){
        data = $.parseJSON(data);
        $(".table_holder").html(data.html);
        $.each(data.data,function(){
            var item = $("#matrix_table tbody tr td[data-option_2_id='"+this.option2+"'][data-option_1_id='"+this.option1+"'] span:first");
            $(item).data('price_adjustment',this.price_adjustment);
            $(item).data('price',this.price);
            $(item).siblings('.display_price').text('+'+this.price);
            $(item).data('publish',this.publish);
            $(item).data('image',this.image);
            if(this.secondary != "")
            {
                $("#matrix_table tbody tr td[data-option_2_id='"+this.option2+"'][data-option_1_id='"+this.option1+"']").children('.secondary_matrix').val(this.secondary);
            }

            if(this.publish == 1)
            {
                $(item).removeClass('icon-remove');
                $(item).addClass('icon-ok');
            }

        });
    });
}

function generate_json_for_matrix()
{
    var data = [];
    $("#matrix_table tbody tr td.matrix_item").each(function(){
        data.push({option1:$(this).data('option_1_id'),option2:$(this).data('option_2_id'),publish:$('span',this).data('publish'),price:$('span',this).data('price'),price_adjustment:$('span',this).data('price_adjustment'),image:$('span',this).data('image'),secondary:$('.secondary_matrix',this).val()});
    });
    return JSON.stringify(data);
}