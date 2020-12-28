$(document).ready(function(){
    if(document.getElementById('frequency').value != '')
    {
        var data = $.parseJSON($("#frequency").val());
        $.each(data.minute,function(){
           $("#minute option[value='"+this+"']").prop('selected',true);
        });

        $.each(data.hour,function(){
            $("#hour option[value='"+this+"']").prop('selected',true);
        });

        $.each(data.day_of_month,function(){
            $("#day_of_month option[value='"+this+"']").prop('selected',true);
        });

        $.each(data.month,function(){
            $("#month option[value='"+this+"']").prop('selected',true);
        });

        $.each(data.day_of_week,function(){
            $("#day_of_week option[value='"+this+"']").prop('selected',true);
        });
    }

    $("#btn_save,#btn_save_exit").on('click',function(){
        if($("#title").val() == ""){
            alert("Please set title");
            $("#title").focus();
            return false;
        }
		if($("#plugin_id").val() == ""){
			alert("Please set plugin");
            $("#plugin_id").focus();
			return false;
		}
		if($("#minute").val() === null){
			alert("Please set minute");
            $("#minute").focus();
			return false;
		}
		if($("#hour").val() === null){
			alert("Please set hour");
            $("#hour").focus();
			return false;
		}
		if($("#day_of_month").val() === null){
			alert("Please set day of month");
            $("#day_of_month").focus();
			return false;
		}
		if($("#month").val() === null){
			alert("Please set month");
            $("#month").focus();
			return false;
		}
		if($("#day_of_week").val() === null){
			alert("Please set day of week");
            $("#day_of_week").focus();
			return false;
		}

        var values = {minute: $("#minute").val(),hour: $("#hour").val(),day_of_month: $("#day_of_month").val(),month:$("#month").val(),day_of_week:$("#day_of_week").val()};
        $("#frequency").val(JSON.stringify(values));
        $("#manage_cron").submit();
    });
	
	$("#btn_run").on('click',function(){
		var btn = this;
		var plugin = $("#plugin_id option:selected").data("plugin");
        var action = $("#controller_action").val();
		if(plugin){
			btn.innerHTML = plugin +' is running...';
			$.get('/frontend/' + plugin + '/' + action, function(){
				btn.innerHTML = 'Run';
			});
		} else {
			alert("no plugin is selected");
		}
		return false;
    });

    $("#plugin_id").on('change',function(){
       if($(this).val() != "")
       {

       }
    });

    $("#plugin_settings").on('click',function(){
        $.post('/admin/settings/get_group_settings',{group_name:$("#plugin_id option:selected").text()},function(data){
            $("#modal_content").html(data);
            $("#myModal").modal();
        });
    });

    $("#update_settings").on('click',function(){
        $("#modal_content").ajaxSubmit({url:'/admin/settings/',type: 'post'});
    });

    $("#plugin_id").on("change", function(){
        var controllerAction = $("#controller_action")[0];
        for (var i = controllerAction.options.length - 1 ; i >= 0 ; --i) {
            controllerAction.options[i] = null;
        }
        if (this.selectedIndex > 0) {
            var selectedPluginName = $(this.options[this.selectedIndex]).data("plugin");
        }
        if (window.availableCronActions[selectedPluginName]) {
            for (var action in window.availableCronActions[selectedPluginName]) {
                controllerAction.options[controllerAction.options.length] = new Option(action, action);
            }
        }
    });
});