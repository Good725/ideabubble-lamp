$(document).ready(function(){
    var form_designer        = $("#form_designer");
    var renderer             = $("#form_renderer_complete_form");
    var hidden_fields        = $('#hidden_fields');
    var new_object_type      = $("#new_object_type");
    var hidden_field_display = '<p class="hidden_field_display"><span>[ Hidden field ]</span></p>';
    var remove_icon          = '<a class="remove" title="Delete this field">x</a>';

    var oldContainer;
    renderer.find('> ul').sortable(
    {
        group: 'form_renderer_complete_form',
        afterMove: function (placeholder, container)
        {
            if (oldContainer != container)
            {
                if (oldContainer)
                    oldContainer.el.removeClass("active");
                container.el.addClass("active");

                oldContainer = container;
            }

        },
        onDrop: function (item, container, _super)
        {
            container.el.removeClass("active");
            _super(item);
        }
    });

    $("#preview_object").hide();
    $(".amend_item").hide();
    new_object_type.on("change",function()
	{
		var $clone = $('.amending').clone();
		$clone
			.removeClass('amending')
			.attr('id', 'formbuilder-preview-'+$clone.attr('id')); // Prefix the ID to make it unique

        var item = $(this).val();
        hide_options("options_text");
        hide_options("options_textarea");
        hide_options("options_dropdown");
        hide_options("options_checkbox");
        hide_options("options_captcha");
        hide_options("options_button");
        hide_options("options_submit_button");
        hide_options("options_hidden");
        hide_options("options_fieldset");
        hide_options('options_files');
        hide_options('options_datepicker');
        $(".amend_item").hide();
        if(item != "")
        {
            $("#preview_object").show();
            $("#preview_object").html("<h6>Preview Object</h6>");
        }
        else
        {
            $("#preview_object").html("<h6>Preview Object</h6>");
            $("#preview_object").hide();
        }

        if(item == "text")
        {
            $(".options_text").addClass("active");
            $("#options_area").append($(".options_text"));
            $(".options_text").show();

			if (typeof $clone[0] == 'undefined' || $clone.attr('type', '!=', 'text'))
			{
				$("#preview_object").append('<input type="text"/>');
			}
			else
			{
				$("#preview_object").append($clone);
			}
        }
        if(item == "textarea")
        {
            $(".options_textarea").addClass("active");
            $("#options_area").append($(".options_textarea"));
            $(".options_textarea").show();

			if (typeof $clone[0] == 'undefined' || $clone[0].nodeName != 'TEXTAREA')
			{
				$("#preview_object").append('<textarea></textarea>');
			}
			else
			{
				$("#preview_object").append($clone);
			}

        }
        if(item == "select")
        {
            $(".options_dropdown").addClass("active");
            $("#options_area").append($(".options_dropdown"));
            $(".options_dropdown").show();

			if (typeof $clone[0] == 'undefined' || $clone[0].nodeName != 'SELECT')
			{
				$("#preview_object").append('<select></select>');
			}
			else
			{
				$("#preview_object").append($clone);
			}
        }
        if(item == "checkbox")
        {
            $(".options_checkbox").addClass("active");
            $("#options_area").append($(".options_checkbox"));
            $(".options_checkbox").show();

			if (typeof $clone[0] == 'undefined' || $clone.attr('type', '!=', 'checkbox'))
			{
				$("#preview_object").append('<input type="checkbox"/>');
			}
			else
			{
				$("#preview_object").append($clone);
			}
			$clone.prop('checked', false);
        }
        if(item == "button")
        {
            $(".options_button").addClass("active");
            $("#options_area").append($(".options_button"));
            $(".options_button").show();

			if (typeof $clone[0] == 'undefined' || $clone[0].nodeName != 'BUTTON')
			{
				$("#preview_object").append('<button></button>');
			}
			else
			{
				$("#preview_object").append($clone);
			}
        }
        if(item == "submit button")
        {
            $(".options_submit_button").addClass("active");
            $("#options_area").append($(".options_submit_button"));
            $(".options_submit_button").show();

			if (typeof $clone[0] == 'undefined' || $clone[0].nodeName != 'INPUT' || $clone.attr('type', '!=', 'submit'))
			{
				$("#preview_object").append('<input type="submit"/>');
			}
			else
			{
				$("#preview_object").append($clone);
			}
        }
        if(item == "datepicker")
        {
            $(".options_datepicker").addClass("active");
            $("#options_area").append($(".options_datepicker"));
            $(".options_datepicker").show();

			if (typeof $clone[0] == 'undefined' || ! $clone.hasClass('.datepicker'))
			{
				$("#preview_object").append('<input type="text" class="datepicker"/>');
			}
			else
			{
				$("#preview_object").append($clone);
			}
        }
        if(item == "captcha")
        {
            $(".options_captcha").addClass("active");
            $("#options_area").append($(".options_captcha"));
            $(".options_captcha").show();
            $("#preview_object").append('<span>[CAPTCHA]</span>');
        }
        if(item == "hidden")
        {
            $(".options_hidden").addClass("active");
            $("#options_area").append($(".options_hidden"));
            $(".options_hidden").show();

			if (typeof $clone[0] == 'undefined' || $clone.attr('type', '!=', 'hidden'))
			{
				$("#preview_object").append('<input type="hidden" />');
			}
			else
			{
				$("#preview_object").append($clone);
			}
        }
        if(item == "fieldset")
        {
            $(".options_fieldset").addClass("active");
            $("#options_area").append($(".options_fieldset"));
            $(".options_fieldset").show();

			if (typeof $clone[0] == 'undefined' || ! $clone.hasClass('fieldset'))
			{
				$("#preview_object").append('<ul class="fieldset" data-name=""></ul>');
			}
			else
			{
				$("#preview_object").append($clone);
			}
        }
        if(item == "file")
        {
            $(".options_files").addClass("active");
            $("#options_area").append($(".options_files"));
            $(".options_files").show();

			if (typeof $clone[0] == 'undefined' || $clone.attr('type', '!=', 'file'))
			{
				$("#preview_object").append('<input type="file" name="formbuilder_files[]" multiple=""/>');
			}
			else
			{
				$("#preview_object").append($clone);
			}
        }

    });

    $('#form_renderer_complete_form').find('input[type=hidden]').each(function(){
        $(this).before(hidden_field_display);
    });
    $('#form_renderer_complete_form li, .hidden_field_display').each(function(){
        $(this).prepend(remove_icon);
    });
    renderer.find('fieldset').each(function()
    {
        var legend_name = $(this).find('legend').html();
        $(this).find('legend').remove();
        $(this).find('> ul').addClass('fieldset').attr('data-name', legend_name).attr('id', this.id);
        $(this).find('> ul').unwrap();
    });

    $("#publish_toggle").find("button").on('click',function(){
        $("#publish").val($(this).val());
    });

    $("#add_new_form_object").click(function(event)
    {
        event.preventDefault();
        var use_label = true;
        switch(new_object_type.val())
        {
            case 'select':
                $("#select_add_options").find("span").each(function(){
                    if($(this).children(".select_text") != "")
                    {
                        $("#preview_object").children(":nth-child(2)").prepend('<option value="'+$(this).children(".select_value").val()+'">'+$(this).children(".select_text:nth-child(2)").val()+'</option>');
                    }
                });
                break;
            case 'fieldset':
                $("#preview_object").find('ul').attr('data-name', $('.fieldset_legend').val());
                renderer.find('ul').append("<li></li>");
                renderer.find("li:last").html($("#preview_object").children(":nth-child(2)"));
                renderer.find(" > ul > li:last").prepend(remove_icon);
                use_label = false;

                break;

            case 'hidden':
                hidden_fields.append($("#preview_object").children(":nth-child(2)"));
                hidden_fields.find(':last').before(hidden_field_display);
                hidden_fields.find("p:last").prepend(remove_icon);
                use_label = false;
                break;

            case 'button':
                $('#preview_object').find('button').html($('.options_button .options_buttontext').val());
        }

        if (use_label)
        {
            renderer.find('> ul').append("<li></li>");
            var id    = form_designer.find('.options_id').val();
            var label = '<label for="'+id+'">'+$("#form_designer").find(".active .field_label").val()+'</label>';
            renderer.find("li:last").html($("#preview_object").children(":nth-child(2)"));
            renderer.find("li:last").prepend(label);
            renderer.find("li:last").prepend(remove_icon);
        }
        $("#render_form").get(0).reset();
        new_object_type.change();
    });
    $("#add_extra_select_option").click(function(event){
        event.preventDefault();
        $("#select_add_options").prepend('<span><label>Dropdown text</label><input type="text" class="select_text"/><label>Dropdown value</label><input type="text" style="width:30px;" class="select_value"/></span>');
    });
    $(".options_name").change(function(){
        $("#preview_object").children(":nth-child(2)").attr("name",$(this).val());
    });
    $(".options_id").change(function(){
        $("#preview_object").children(":nth-child(2)").attr("id",$(this).val());
    });
    $(".options_validation").change(function(){
        $("#preview_object").children(":nth-child(2)").addClass($(this).val());
    });
    $(".options_width").change(function(){
        $("#preview_object").children(":nth-child(2)").attr("style","width:"+$(this).val()+"px;");
    });
    $(".options_rows").change(function(){
        $("#preview_object").children(":nth-child(2)").attr("rows",$(this).val());
    });
    $(".options_cols").change(function(){
        $("#preview_object").children(":nth-child(2)").attr("cols",$(this).val());
    });
    $(".options_select").change(function(){
        $("#preview_object").children(":nth-child(2)").attr("cols",$(this).val());
    });
    $(".options_value").change(function(){
        $("#preview_object").children(":nth-child(2)").attr("value",$(this).val());
    });
    $(".select_value").change(function(){
        $("#preview_object").children(":nth-child(2)").children("selected").remove();
        if($(".options_select_default").val() != "" && $(this).val() != "")
        {
            $("#preview_object").children(":nth-child(2)").prepend('<option selected value="'+$(this).val()+'">'+$(".options_select_default").val()+'</option>');
        }
    });
    $(".options_select_default").change(function(){
        $("#preview_object").children(":nth-child(2)").children("selected").remove();
        if($(".select_value").val() != "" && $(this).val() != "")
        {
            $("#preview_object").children(":nth-child(2)").prepend('<option selected value="'+$(".options_select_default").val()+'">'+$(this).val()+'</option>');
        }
    });
    $(".default_checkbox").change(function(){
        if($(this).val() == "yes") $("#preview_object").children(":nth-child(2)").prop("defaultChecked",true);
        if($(this).val() == "no") $("#preview_object").children(":nth-child(2)").prop("defaultChecked",false);
    });

    $("#form_action").change(function(){
        if($(this).val() == "custom")
        {
            $("#custom_action").show();
        }
        else
        {
            $("#custom_action").hide();
        }
    });
    $(".save_form").click(function(event){
        event.preventDefault();

		// Validate the form in the "Details" tab, before saving
		$('[href="#details_tab"]').tab('show');
		if ( ! $('#form_event_edit').validationEngine('validate'))
		{
			return false;
		}

        var clone = renderer.clone();

        // Remove/change design-only elements
        clone.find('.hidden_field_display').remove();
        clone.find('.remove').remove();
        clone.find('.amending').removeClass('amending');
        clone.find('ul.fieldset').each(function()
        {
            $(this).wrap('<fieldset></fieldset>');
            if ($(this).attr('id').length)
            {
                var id = this.id;
                $(this).removeAttr('id');
                $(this).parent().attr('id', id);
            }
            $(this).before('<legend>'+$(this).data('name')+'</legend>');
            $(this).removeClass('fieldset').removeAttr('data-name');
        });
        clone.find('> ul').sortable('disable');
        clone.find('[draggable]').removeAttr('draggable');
        clone.find('[class=""]').removeAttr('class');

        var action = $(this).data('action');
        var form_html = '';
        clone.find('input[type="hidden"]').each(function(){
            form_html += $(this).prop('outerHTML');
        });
        form_html += clone.find('> ul').html().trim();
        var form = $("#form_type").val();
        $("#form_html").val(form_html);
        var form_details = $("#form_event_edit");
        $("#return_action").val($(this).data('action'));
        $.post("/admin/formbuilder/save/"+form,$("#form_event_edit").serialize()).done(function(results){
            if(action == "save_and_exit")
            {
                window.location = "/admin/formbuilder/";
            }
            else
            {
                location.reload();
            }
        });
        clone.remove();
    });

    renderer.on("click", "ul.fieldset",function(e){
        if(e.target != this) return;
        $(".amending").removeClass("amending");
        $(this).addClass("amending");
        new_object_type.find('option[value="fieldset"]').prop('selected', true).change();
        $(".options_fieldset .fieldset_legend").val($(this).data('name'));
        $(".options_fieldset .options_id").val(this.id);
        $(".amend_item").show();
    });

    renderer.on("click","input[type='text']",function(){
        $(".amending").removeClass("amending");
        $(this).addClass("amending");
        new_object_type.find('option[value="text"]').prop('selected', true).change();
        $(".options_text .field_label").val($(this).prev().text());
        $(".options_text .options_name").val($(this).attr('name'));
        $(".options_text .options_id").val($(this).attr('id'));
        $(".options_text .options_width").val($(this).attr('style'));
        $(".options_text .options_validation").val($(this).attr('class').replace('amending', '').trim());
        $("#preview_object input[type='text']").attr('name',$(this).attr('name'));
        $("#preview_object input[type='text']").attr('id',$(this).attr('id'));
        $("#preview_object input[type='text']").attr('style',$(this).attr('style'));
        $("#preview_object input[type='text']").attr('class',$(this).attr('class'));
        $(".amend_item").show();
    });

    renderer.on("click","input[type='checkbox']",function(ev){
        ev.preventDefault();
        $(".amending").removeClass("amending");
        $(this).addClass("amending");
        new_object_type.find('option[value="checkbox"]').prop('selected', true).change();
        $(".options_checkbox .field_label").val($(this).prev().text());
        $(".options_checkbox .options_name").val($(this).attr('name'));
        $(".options_checkbox .options_id").val($(this).attr('id'));
        $(".options_checkbox .default_checked").val(this.checked);
        $(".amend_item").show();
    });

    renderer.on("click","textarea",function(){
        $(".amending").removeClass("amending");
        $(this).addClass("amending");
        $(this).focus();
        new_object_type.find('option[value="textarea"]').prop('selected', true).change();
        $(".options_textarea .field_label").val($(this).prev().text());
        $(".options_textarea .options_name").val($(this).attr('name'));
        $(".options_textarea .options_id").val($(this).attr('id'));
        $(".options_textarea .options_rows").val($(this).attr('rows'));
        $(".options_textarea .options_cols").val($(this).attr('cols'));
        $(".options_text .options_validation").val($(this).attr('class').replace('amending', '').trim());
        $("#preview_object textarea").attr('name',$(this).attr('name'));
        $("#preview_object textarea").attr('id',$(this).attr('id'));
        $("#preview_object textarea").attr('style',$(this).attr('style'));
        $("#preview_object textarea").attr('class',$(this).attr('class'));
        $(".amend_item").show();
    });

    renderer.on("click",".hidden_field_display",function(){
        $(".amending").removeClass("amending");
        var field = $(this).find(' \+ input[type="hidden"]');
        field.addClass("amending");
        new_object_type.find('option[value="hidden"]').prop('selected', true).change();
        $(".options_hidden .options_name").val(field.attr('name'));
        $(".options_hidden .options_value").val(field.val());
        $(".options_hidden .options_id").val(field.attr('id'));
        $(".amend_item").show();
    });

    renderer.on("click", "button",function(event){
        event.preventDefault();
        $(".amending").removeClass("amending");
        $(this).addClass("amending");
        new_object_type.find('option[value="button"]').prop('selected', true).change();
        $(".options_button .options_type").val($(this).attr('type'));
        $(".options_button .options_buttontext").val($(this).text());
        $(".options_button .options_name").val($(this).attr('name'));
        $(".options_button .options_id").val($(this).attr('id'));
        $(".options_button .options_value").val($(this).val());
        $(".amend_item").show();
    });

    renderer.on("click","input[type='submit']",function(event){
        event.preventDefault();
        $(".amending").removeClass("amending");
        $(this).addClass("amending");
        new_object_type.find('option[value="submit button"]').prop('selected', true).change();
        $(".options_submit_button .options_value").val($(this).val());
        $(".options_submit_button .options_id").val($(this).attr('id'));
        $(".amend_item").show();
    });


    $(".amend_item").click(function(event){
        event.preventDefault();

		var $amending = $('.amending');
		
        var id = form_designer.find('.options_id').val();
        if (new_object_type.val() == "hidden")
        {
            var name = form_designer.find('.options_name').val();
            var value = form_designer.find('.options_value').val();
            $(".amending")
                .attr('id', id)
                .attr('name', name)
                .val(value);
        }
        else
        {
            if(new_object_type.val() == "select")
            {
                $("#select_add_options span").each(function(){
                    if($(this).children(".select_text") != "")
                    {
                        $("#preview_object").children(":nth-child(2)").prepend('<option value="'+$(this).children(".select_value").val()+'">'+$(this).children(".select_text:nth-child(2)").val()+'</option>');
                    }
                });
            }
            var label = '<label for="'+id+'">'+$("#form_designer").find(".active .field_label").val()+'</label>';
            label = "<li class='active_li'>"+label+"</li>";
            var replacement_object = $("#preview_object").children(":nth-child(2)");
            $(replacement_object).addClass($("#render_form .active .options_validation").val());
            $("#form_renderer_complete_form .amending").parent().replaceWith(label);
            $("#form_renderer_complete_form .active_li").append(replacement_object);
            $(".active_li").prepend(remove_icon);
            $(".active_li").removeClass("active_li");
        }

		// Remove the ID prefix
		if ($amending.attr('id'))
		{
			$amending.attr('id', $amending.attr('id').replace('formbuilder-preview-', ''));
		}

        $amending.removeClass("amending");

        $("#amend_item").hide();
        $("#render_form").get(0).reset();
        new_object_type.find('option:eq(0)').prop('selected', true).change();
    });

    $('#form_renderer_complete_form').on('click', '.remove', function(){
        if ($(this).parent().hasClass('hidden_field_display'))
        {
            $(this).parent().find(' \+ input[type="hidden"]').remove();
        }
        $(this).parent().remove();
    });

    $("captcha_button").click(function(){
        $("#enable_captcha").val($(this).val());
    });

    $("#captcha_toggle button").click(function(){
        $("#captcha_enabled").val($(this).val());
    });

    $("#cancel_button").click(function(){
        window.location = "/admin/formbuilder/";
    });

});

function hide_options(class_name)
{
    $("."+class_name).hide();
    $("."+class_name).removeClass("active");
    $("#formbuilder_options").append($("."+class_name));
}