$(document).ready(function()
{

	$(".multipleselect").multiselect();

    initTable('#images_table');

    initCourseTopicsTable('#course_topics_table');

    jQuery.extend(jQuery.validator.messages, {
        required: "Required!"
    });

    $("#form_add_edit_course").validate();
    $("#publish_yes").click(function(ev){
        ev.preventDefault();
        $("#publish").val('1');
    });
    $("#publish_no").click(function(ev){
        ev.preventDefault();
        $("#publish").val('0');
    });
    $("#book_button_yes").click(function (ev)
    {
        $("#book_button").val('0');
    });
    $("#book_button_no").click(function (ev)
    {
        $("#book_button").val('1');
    });
    $("#btn_delete").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data("id");
        $("#btn_delete_yes").data('id', id);
        $("#confirm_delete_course").modal();
    });
    $("#confirm_delete_course").find("#btn_delete_yes").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $.post('/admin/courses/remove_course', {id: id}, function (data) {
            if (data.redirect !== '' || data.redirect !== undefined) {
                window.location = data.redirect;
            } else {
                var smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">&times;</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                $("#main").prepend(smg);
            }
            $("#confirm_delete").modal('hide');

        }, "json");
    });

	$("#add_image").click(function(ev)
	{
		ev.preventDefault();
		var image = $("#image").val();
		var course = parseInt($("#id").val());
		if (course > 0)
		{
			$.post('/admin/courses/ajax_add_image_to_course', {course_id: course, image: image})
				.done(function()
				{
					initTable('#images_table')
				});
		}
		else
		{
			var category_id = $("#category_id").val();
			var title = $("#title").val();
			var code = $("#code").val();
			var year_id = $("#year_id").val();
			var type_id = $("#type_id").val();
			var study_mode_id = $("#study_mode_id").val();
			var provider_id = $("#provider_id").val();
			var file_id = $("#file_id").val();
			var level_id = $("#level_id").val();
			var summary = $("#summary").val();
			var description = $("#description").val();
			var publish = $("#publish").val();
			$.post('/admin/courses/ajax_save_course', {
				category_id: category_id,
				title: title,
				code: code,
				year_id: year_id,
				type_id: type_id,
				study_mode_id: study_mode_id,
				provider_id: provider_id,
				file_id: file_id,
				level_id: level_id,
				summary: summary,
				description: description,
				publish: publish
			}, function(response){
				if (response.message === 'success')
				{
					$("#id").val(response.course);
					course = response.course;
					$.post('/admin/courses/ajax_add_image_to_course', {course_id: course, image: image}, function(response_new){
					}, "json");
				}
				else
				{
					$("#showalert").html(response.error);
				}
			}, "json");
		}
		return false;
	});


    $(document).on("click", ".delete_image", function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $("#btn_delete_course_yes").data('id', id);
        $("#confirm_delete_image").modal();
    });
    $("#btn_delete_course_yes").click(function (ev) {
        ev.preventDefault();
        var id = $(this).data('id');
        $.post('/admin/courses/ajax_remove_course_image', {id: id}, function (data)
		{
			var smg;
            if (data.message === 'success') {
                initTable('#images_table');
                smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Image is successfully removed.</div>';
                $("#main").prepend(smg);
            } else {
                smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">&times;</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                $("#main").prepend(smg);
            }
            $("#confirm_delete_image").modal('hide');

        }, "json");


    });

    $('.save_button').click(function(e){
        if (verify_payment_plans()) {
            $("#redirect").val($(this).data('redirect'));
            $("#form_add_edit_course").submit();
        } else {
            e.preventDefault();
            return false;
        }
    });

    // topics
    $(document).on('click','#topic_select-add',function()
    {
        var topic_id   = parseInt($(this).find(':selected').val());
        var course = parseInt($("#id").val());

        if (course > 0 && topic_id > 0) {
            if ($('#course_topics_table').find('[name="topic_ids[]"][value="'+topic_id+'"]').length) {
                $('#showalert').add_alert('Topic has already been added', 'warning');
            } else {
                $.post('/admin/courses/ajax_add_topic_to_course', {course_id: course, topic_id: topic_id})
                    .done(function() {
                        initCourseTopicsTable('#course_topics_table');
                    });
            }
        }

        return false;
    });

    $('#course_topics_table').on('click', '.delete_course_topic', function()
    {
        var course_id = parseInt($("#id").val());
        var topic_id = parseInt($(this).data('id'));
        if (course_id > 0 && topic_id > 0)
        {
            $.post('/admin/courses/ajax_remove_course_topic', {course_id: course_id, topic_id: topic_id})
                .done(function(data)
                {
                    initCourseTopicsTable('#course_topics_table');
                	var smg = '';
                    if (data.message === 'success') {

                        smg = '<div class="alert alert-success"><a class="close" data-dismiss="alert">&times;</a><strong>Success: </strong> Image is successfully removed.</div>';
                        $("#topic_select").prepend(smg);
                    } else {
                        smg = '<div class="alert alert-error"><a class="close" data-dismiss="alert">&times;</a><strong>Error: </strong> ' + data.error_msg + '</div>';
                        $("#topic_select").prepend(smg);
                    }

                });
        }

        return false;
    });

	$("#is_fulltime").on("change", function(){
		if (this.value == 'YES') {
			$(".fulltime_param").removeClass("hidden");
		} else {
			$(".fulltime_param").addClass("hidden");
		}
	});
    $("#is_fulltime").change();


	var $payment_option_tpl = $("#paymentoptions > tfoot > .payment_option");
	$payment_option_tpl.remove();
    var $custom_payment_plan_table_tpl = $(".custom.payment_plan.hidden.tpl");
    var $custom_plan_option_tpl = $custom_payment_plan_table_tpl.find("tfoot tr.custom_option");
    $custom_plan_option_tpl.remove();
    $custom_payment_plan_table_tpl.removeClass("tpl");
    $custom_payment_plan_table_tpl.remove();

    $("#paymentoptions > tbody > tr .custom.payment_plan .btn.add_custom").on("click", custom_plan_option_add);
    $("#paymentoptions > tbody > tr .custom.payment_plan .btn.remove_custom").on("click", custom_plan_option_remove);

    function payment_option_add()
    {
        var index = 0;
        $("#paymentoptions > tbody > tr").each(function(){
            index = Math.max(index, parseInt($(this).attr("data-index")));
            ++index;
        });
        var $payment_option = $payment_option_tpl.clone();
        var $custom_payment_plan_table = $custom_payment_plan_table_tpl.clone();
        $payment_option.find(".c3").append($custom_payment_plan_table);

        $payment_option.find("input, select").each(function(){
            this.name = this.name.replace("paymentoption[index]", "paymentoption[" + index + "]");
        });

        $payment_option.find(".btn.remove").on("click", payment_option_remove);
        $payment_option.attr("data-index", index);
        $payment_option.removeClass("hidden");

        $payment_option.find('.interest_type').on('change', payment_option_custom_changed);

        $custom_payment_plan_table.find(".btn.add_custom").on("click", custom_plan_option_add);

        $("#paymentoptions > tbody").append($payment_option);
    }

    function payment_option_remove()
    {
        $(this).parents("tr").remove();
    }

    function custom_plan_option_add()
    {
        var $payment_option = $(this).parents("tr.payment_option");
        var index = $payment_option.attr("data-index");
        var index2 = 0;
        $payment_option.find(".custom.payment_plan tbody tr").each(function(){
            index2 = Math.max(index2, parseInt($(this).attr("data-index2")));
            ++index2;
        });

        var $custom_plan_option = $custom_plan_option_tpl.clone();
        $custom_plan_option.attr("data-index2", index2);
        $custom_plan_option.removeClass("hidden");
        $custom_plan_option.find("input").each(function(){
            this.name = this.name.replace("paymentoption[index]", "paymentoption[" + index + "]");
            this.name = this.name.replace("[custom_payments][index2]", "[custom_payments][" + index2 + "]");
        });
        $custom_plan_option.find(".due_date").datepicker(
            {
                autoclose: true,
                orientation: "auto bottom",
                format: 'dd-mm-yyyy'
            }
        );
        $custom_plan_option.find(".btn.remove_custom").on("click", custom_plan_option_remove);
        $custom_plan_option.find(".amount, .interest").on("change", function(){
            var $tr = $(this).parents("tr");
            var total = 0;
            total = parseFloat($tr.find(".amount").val()) + parseFloat($tr.find(".interest").val());
            if(!isNaN(total)) {
                $tr.find(".total").val(total);
            } else {
                $tr.find(".total").val("");
            }
        });

        $payment_option.find(".custom.payment_plan tbody").append($custom_plan_option);
    }

    function custom_plan_option_remove()
    {
        $(this).parents("tr").remove();
    }

    function payment_option_custom_changed()
    {
        var interest_type = this.value;

        var $tr = $(this).parents("tr");
        var index = $tr.data("index");
        if (interest_type == 'Custom') {
            $tr.find(".c4,.c5").addClass("hidden");
            $tr.find(".c3 input.deposit").addClass("hidden");
            $tr.find(".c3 .custom.payment_plan").removeClass("hidden");
            $tr.find(".c3").attr("colspan", "3");
        } else {
            $tr.find(".c4,.c5").removeClass("hidden");
            $tr.find(".c3 input.deposit").removeClass("hidden");
            $tr.find(".c3 .custom.payment_plan").addClass("hidden");
            $tr.find(".c3").attr("colspan", "1");
        }
    }

	$("#paymentoptions .btn.add").on("click", payment_option_add);

    $("#paymentoptions .btn.remove").on("click", payment_option_remove);

    function verify_payment_plans()
    {
        var fulltime_price = parseFloat($("#fulltime_price").val());
        var $plans = $("#paymentoptions .payment_option");
        $plans.each(function(){
            var type = $(this).find(".interest_type").val();
            if (type == "Custom") {
                var amount = 0;
                $(this).find(".amount").each(function(){
                    var amount_pp = parseFloat(this.value);
                    amount += amount_pp;
                });
                if (amount != fulltime_price) {
                    alert("Installment amount do not match course fee");
                    return false;
                }
            }
        });
        return true;
    }
});

function initTable(id)
{
    var course = parseInt($("#id").val());
    var $table = $(id);
    var ajax_source = "/admin/courses/ajax_get_courses_images/?id="+course;
    var settings = {
        "aoColumns": [
            {"mDataProp": "thumbnail", "bSearchable": false, "bSortable": false},
            {"mDataProp": "file_name", "bSearchable": true,  "bSortable": true},
            {"mDataProp": "remove",    "bSearchable": false, "bSortable": false}
        ],
        "bDestroy": true,
        "sPaginationType" : "bootstrap",
        "sServerMethod": "POST",
        "fnDrawCallback": function() {
            var is_empty = (this.fnSettings().fnRecordsTotal() == 0);
            $table.toggleClass('hidden', is_empty);
            $table.parents('.dataTables_wrapper').toggleClass('hidden', is_empty);
        }
    };
    return $table.ib_serverSideTable(ajax_source, settings);
}

function initCourseTopicsTable(id)
{
	var course_id = parseInt($("#id").val()) || 0;
	var $table = $(id);
	var ajax_source = "/admin/courses/ajax_get_topics/?course_id="+course_id;
    var settings = {
        "aoColumns": [
            {"mDataProp": "name", "bSearchable": false, "bSortable": true},
            {"mDataProp": "description", "bSearchable": false, "bSortable": true},
            {"mDataProp": "action", "bSearchable": false, "bSortable": false}
        ],
        "bServerSide": true,
        "bProcessing": false,
        "bInfo": false,
        "bPaginate":false,
        "bDestroy": true,
        "sPaginationType" : "bootstrap",
        "fnServerData": function (sSource, aoData, fnCallback, oSettings)
        {
            oSettings.jqXHR = $.ajax(
                {
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aoData,
                    "success": fnCallback
                });
        }
    };
    var drawback_settings = {
        "draw_callback" : function () {
            $('#course_topics_table_filter').hide();
        }
        };
    return $table.ib_serverSideTable(ajax_source, settings, drawback_settings);
}

// Add an alert to a message area.
// e.g. $('#page_notification_area').add_alert('Page successfully saved', 'success');
(function($)
{
    $.fn.add_alert = function(message, type)
    {
        var $alert = $('<div class="alert'+((type) ? ' alert-'+type : '')+' popup_box">' +
        '<a href="#" class="close" data-dismiss="alert">&times;</a> ' + message +
        '</div>');
        $(this).append($alert);

        // Dismiss the alert after 10 seconds
        setTimeout(function()
        {
            $alert.fadeOut();
        }, 10000);
    };
})(jQuery);
