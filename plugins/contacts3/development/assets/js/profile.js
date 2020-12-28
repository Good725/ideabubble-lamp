$(document).ready(function(){
    $('.tabs_content').on('change', '#db-u-add-st', function(){
        setFamilyAddress(this);
    });

    // tabbed content
    $(".tabs_content").hide();

    show_contact_tab($('#contact_id').data('contact_id'));

    $('body').on('click', '#profile-select_contact .btn', function() {
        $('#profile-select_contact .btn').removeClass('active');
        $('.tabs_content').hide();
        show_contact_tab($(this).data('contact_id'));
        return false;
    });

    $('body').on('click', '#db-student', function () {
        $('#student-info').toggle();
    });

    $('body').on('click', '#db-login', function () {
        $('#account_block').toggle();
    });

    $('body').on('click', '.new-profile', function(){
        $('.tabs_content').hide();
        $('#profile-select_contact').hide();
        show_new_contact_tab($(this).data('type'), $(this).data('family_id'));
        return false;
    });

    $('body').on('click', '.db-save-btn', function(){
        if ( ! $('.validate-on-submit').validationEngine('validate')){
            return false;
        }

        var required_checkboxes =  $('#edit_profile_form').find("input.contact_profile_required:checkbox");

        required_checkboxes.push( $('#add_profile_student_form').find("input.contact_profile_required:checkbox"));
        required_checkboxes.push( $('#add_profile_guardian_form').find("input.contact_profile_required:checkbox"));


        for(i = 0; i < required_checkboxes.length; i++) {
            var item=$(required_checkboxes[i]);
            if(item.prop("checked") == false)
            {
                alert(item.siblings('.contact_profile_required').text()+" is required.");
                return false;
            }
        }

        if(!$('#edit_profile_form').length){
            $(this).closest('form').submit();
            return false;
        }

        $.post('/frontend/contacts3/ajax_save_profile', $(this).closest('form').serialize()).done(function(data){
            var result = JSON.parse(data);
            if (window.redirect_after_save) {
                window.location.href = window.redirect_after_save;
            } else {
                show_contact_tab(result.contact_id, (result.message == ''));
            }
            if (result.messages) {
                $('body').append(result.messages);
                remove_popbox();
            }
        });

        return false;
    });

    function setFamilyAddress(checkboxOject){
        $('.db-main-address input[type="text"]').prop('readonly', function(i, v) { return !v; });
        if($('.db-main-address select:disabled').length){
            setReadOnlyOnSelect(false, $('.db-main-address select'));
        }else{
            setReadOnlyOnSelect(true, $('.db-main-address select'));
        }

        if($(checkboxOject).is(':checked')){
            $('.db-main-address .form-input').addClass('disabled');

            $.post('/frontend/contacts3/ajax_get_family_address', {family_id: $('#profile-select_contact').data('family_id')}
            ).done(function (data) {
                data = JSON.parse(data);
                $.each(data, function(name, value){
                    var inputSelector = '.db-main-address input[name="' + name + '"]';
                    if($(inputSelector)){
                        $(inputSelector).val(value).trigger('change');
                    }
                    var selectSelector = '.db-main-address select[name="' + name + '"]';
                    if($(selectSelector)){
                        $(selectSelector).val(value).trigger('change');
                    }
                });
            });
        } else {
            $('.db-main-address .form-input').removeClass('disabled');
        }
    }

    function setReadOnlyOnSelect(readonlyFlag, select) {
        if (readonlyFlag) {
            var input = $('<input />', {
                type: "hidden",
                name: select.attr("name"),
                value: select.val()
            });
            select.attr("disabled", true).before(input);
        }else{
            $(select).prev().remove();
            select.attr("disabled", false);
        }
    }

    function show_contact_tab(id, show_updated_message){
        $.ajax({
            url: '/frontend/contacts3/ajax_display_contact_details/',
            data: {contact_id: id},
            type: 'post'
        }).done(function(data) {
            $('.tabs_content').html('').html(data);
            $('.tabs_content').find('#contact_id').val(id);
            $('.tabs_content').show();

            //Make tab li active for current contact
            $('#profile-select_contact').find('.btn[data-contact_id="' + id + '"]').addClass('active');

            $("a[data-contact_id=" + $("#edit_profile_form [name=contact_id]").val() + "]").html($("#edit_profile_form [name=first_name]").val() + " " + $("#edit_profile_form [name=last_name]").val());

            $('.date-of-birth')
				.datetimepicker({
					format: 'd-m-Y',
					timepicker: false,
					scrollInput: false,
					onShow:function( ct ){
						this.setOptions({
							maxDate: new Date()
						})
					}
				})
				.on('keyup', function()
				{
					// Update the calendar, as the user types
					var $this = $(this);
					this.defaultValue = this.value;
					$this.datetimepicker('reset');
					$this.data('xdsoft_datetimepicker').trigger('open.xdsoft');
				});

            //title management
            var isPrimary = $('#isPrimary').val() == 1 ? '(Primary)' : '';
            setTitle($('#mainRole').val() + ' Profile ' + isPrimary);
            if (show_updated_message) {
                alert("Profile has been updated");
            }
        });

    }

    function show_new_contact_tab(type, family_id){
        $.ajax({
            url: '/frontend/contacts3/ajax_new_profile/',
            data: {type: type, family_id: family_id, redirect: window.redirect_after_save},
            type: 'post'
        }).done(function(data) {
            $('.tabs_content').html('').html(data);
            $('.tabs_content').show();
            $('.date-of-birth')
				.datetimepicker({
					format: 'd-m-Y',
					timepicker: false,
					scrollInput: false,
					onShow:function( ct ){
						this.setOptions({
							maxDate: new Date()
						})
					}
				})
				.on('keyup', function()
				{
					var $this = $(this);
					this.defaultValue = this.value;
					$this.datetimepicker('reset');
					$this.data('xdsoft_datetimepicker').trigger('open.xdsoft');
				});

            setFamilyAddress($('#db-u-add-st'));
            (type == 'guardian') ? setTitle('Add Guardian') : setTitle('Add student educational information');
        });

    }

    function setTitle(title){
        $('#edit-profile-title').text(title);
    }

    $(document).ready(function () {
        $('body').on('click', '.db-save-btn', function(){
            var mobile_length = $('#profile_mobile_number').val();
            if(mobile_length.length > 10){
                if (!confirm("You have entered a mobile number with more than 10 digits. Do you wish to continue?")){
                    return;
                }
            }
        })
    });


    $(document).on('change', '#special-preferences-text + .db-check-options [type="checkbox"]', function()
    {
        var $text     = $('#special-preferences-text').find('.dropdown-selected-text');
        var $checkbox = $(this);
        var new_text;

        if($checkbox.prop('checked')) {
            new_text = $text.html() + '<span>' + $checkbox.data('text') + '</span>';
        } else {
            new_text = $text.html().replace('<span>' + $checkbox.data('text') + '</span>', '');
        }

        $text.html(new_text);
    });

});