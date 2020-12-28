$(document).ready(function()
{
    $('[name="use_gravatar"]').on('change', function()
    {
        if (this.value == 1)
        {
            $('.hide-for-gravatar').addClass('hidden');
            $('.show-for-gravatar').removeClass('hidden');
        }
        else
        {
            $('.show-for-gravatar').addClass('hidden');
            $('.hide-for-gravatar').removeClass('hidden');
        }
    });
    $('.multiple_select.todo_categories').multiselect();
    $('#profile-report_card-academic_year').on('change', function() {
        $.get('/admin/todos/ajax_get_todo_categories', {'profile': true, 'academic_year_id' : $('#profile-report_card-academic_year').val()}, function (todo_categories, status)
        {
            $('.multiple_select.todo_categories').html('');
            $.each(todo_categories, function (category_id, category_title) {
                $('.multiple_select.todo_categories').append($('<option>', {
                    value: category_id,
                    text : category_title
                }));
            });
            $('.multiple_select.todo_categories').multiselect('rebuild');
        });
    });
});

$('#change-avatar-btn').on('click', function(ev)
{
    ev.preventDefault();
    if ($(this).hasClass('new'))
    {
        $('#change-avatar-upload-modal').modal();
    }
    else
    {
        existing_image_editor(this.src);
    }
});

$(document).on('show.bs.modal', '#select_preset_modal', function(ev)
{
    ev.preventDefault();
    $('#preset_selector_prompt').find('[data-directory="avatars"]').prop('selected');
    $(this).find('#preset_selector_done_btn').click();
    // $(this).trigger('hide.bs.modal');
});

$(document).on(':ib-fileuploaded', '.upload_item', function()
{
    // Get the path to the uploaded image, which has no preset applied
    var src = this.querySelector('img').src.replace('/_thumbs_cms/', '/');

    // Open the image editor, using the chosen image and the products preset
    existing_image_editor(
        src,
        'avatars',
        function(image)
        {
            if (image.file) {
                image = image.file;
            }
            $('#edit-profile-cms-avatar').attr('src', '/'+image+'?ts='+Date.now());
            $('#edit-profile-avatar-filename').val(image.split('/').pop());
        }
    );
    $('#upload_files_modal').modal('hide');
});

$(document).on('click', '#image-edit-save', function()
{
    var $image = $('#edit-profile-cms-avatar');
    $image[0].src = $image[0].src+'?ts='+Date.now();
    $('#edit_image_modal').modal('hide');
});

function set_profile_avatar(file, preview)
{
    $("#edit-profile-avatar-filename").val(file);
    $("#edit-profile-cms-avatar").attr("src", preview);
    $("#edit-profile-use_gravatar").prop("checked", false);
    $("#edit-profile-use_local").prop("checked", true);
}

// Add new contact method
$(document).on('click', '.add_contact_type .submit_item', function()
{
    var add_contact_type = $(this).parents('.add_contact_type');
    var type             = add_contact_type.find('.select_type :selected');
    var type_stub        = type.data('stub');
    var type_text        = type.html().trim();
    var type_id          = type.val();
    var value            = add_contact_type.find('.enter_value').val();
    var valid            = true;
    var pattern          = /[0-9]*/;
    var email_pattern = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{1,4}$/i;
    var message          = '';

    if (type_stub == 'mobile' || type_stub == 'landline')
    {
        if (value != value.match(pattern) || value == '')
        {
            valid = false;
            message += 'A phone number must contain only numbers.\n';
        }
        if (type_stub == 'mobile' )
        {
            if (! (value.indexOf('083') === 0 || value.indexOf('085') === 0 ||  value.indexOf('086') === 0 ||
                value.indexOf('087') === 0 ||  value.indexOf('088') === 0 ||  value.indexOf('089') === 0) )
            {
                valid = false;
                message += 'A mobile number must start with a valid provider prefix\n(083, 085, 086, 087, 088, 089)\n';
            }
            if (value.length < 10)
            {
                valid = false;
                message += 'A mobile number must contain at least 10 digits.';
            }
            if (value.length > 10) {
                if (!confirm("You have entered a mobile number with more than 10 digits. Do you wish to continue?")){
                    return;
                }
            }
        }
        if (valid)
        {
            valid = new_phone_number(value);
        }
        else
        {
            alert(message);
        }
    }
    else if (type_stub == 'email')
    {
        if ( ! email_pattern.test(value) || value == '')
        {
            valid = false;
            alert('This is not a valid email address');
        }
        else
        {
            value = value.toLowerCase();
        }
    }

    if (valid)
    {
        var icon = '';
        switch(type_stub)
        {
            case 'email':    icon = 'envelope'; break;
            case 'mobile':   icon = 'mobile';   break;
            case 'landline': icon = 'phone';    break;
            case 'web':      icon = 'globe';    break;
            case 'skype':    icon = 'skype';    break;
            case 'facebook': icon = 'facebook'; break;
            case 'twitter':  icon = 'twitter';  break;
        }

        // TODO: Load from views/admin/profile/snippets/add_edit_contact_method
        add_contact_type.find('.contact_types_list').append(
            '<div class="form-group no-guters contactdetail_wrapper" data-stub="'+type_stub+'">\n' +
            '   <input type="hidden" name="contactdetail_id[]" value="new">\n'+
            '   <input type="hidden" name="contactdetail_type_id[]" value="'+type_id+'">'+

            '   <label class="input_group">\n' +
            '       <span class="input_group-icon" title="'+type_stub+'" data-stub="'+type_stub+'"><span class="icon-'+icon+'"></span></span>\n' +

            '       <input type="text" name="contactdetail_value[]" value="'+value+'" class="form-input contactdetail_value'+((type_stub == 'email') ? ' validate[custom[email]]' : '')+'" />\n' +

            '       <span class="input_group-icon">\n' +
            '           <button type="button" class="remove-contactdetail-button"><span class="icon-remove"></span></button>\n' +
            '       </span>\n' +
            '   </label>\n'+
            '</div>');

        add_contact_type.find('.enter_value').val('');
        if (type_stub == 'mobile' || type_stub == 'landline')
        {
            add_contact_type.find('.contactdetail_value').phoneField();
        }

        if (type_stub == 'mobile') {
            $(".add_contact_type .select_type").val("1");
        }
    }
});

// Remove a contact method
$(document).on('click', '.remove-contactdetail-button', function()
{
    var primary = $('#contact_is_currently_primary').val();
    var stub = $(this).closest('div').find().data('stub');
    var contact_notification_id = $(this).closest('div').find('input[name="contactdetail_id[]"]').val();
    var $mobile_numbers = $('.contactdetail_wrapper[data-stub="mobile"]');

    // Cannot delete a primary contact's only mobile number
    if (primary == 1 && stub == 'mobile' && $mobile_numbers.length < 2)
    {
        $('#contact_mobile_delete_primary').modal();
    }
    else
    {
        $('#contact_mobile_delete_number_id').val(contact_notification_id);
        $(this).parents('.contactdetail_wrapper').addClass('contactdetail-delete-target');
        var title = $(this).parents('.contactdetail_wrapper').find('.input-group-addon').attr('title');
        $('#delete_message').html('You are about to delete the contact '+title+'.')
        $('#contact_mobile_delete').modal();
    }
});

/*
 * If the specified phone number has not previously been used, returns "true"
 * If the specified phone number has been used, displays a list of contacts using it and asks the user if the want to use it again
 ** Returns "true", if they click "OK" or "false", if they click "Cancel"
 */
function new_phone_number(number)
{
    var response = true;
    $.ajax({
        url     : '/admin/contacts3/ajax_get_contacts_by_phone_number/',
        data    : {
            'number': number
        },
        type     : 'post',
        dataType : 'json'
    }).success(function(result)
    {
        console.log(result);
        if (result.length > 0)
        {
            var message = 'The phone number '+number+' is used by the following contacts:\n';
            for (var i = 0; i < result.length; i++)
            {
                message += (result[i]['title']+' '+result[i]['first_name']+' '+result[i]['last_name']).trim()+'\n';
            }
            message += '\nDo you want to want to use it again?';
            response = confirm(message);
        }
    });
    return response;
}

$.fn.phoneField = function()
{
    // stop non-numeric characters being typed
    $(this).on('keypress', function(ev)
    {
        var new_detail = $(this).hasClass('enter_value');
        var type       = $(this).parents('.add_contact_type').find('.select_type :selected').data('stub');
        if ( ! new_detail || type == 'mobile' || type == 'landline')
        {
            var charCode = (ev.which) ? ev.which : ev.keyCode;
            return ! (charCode > 31 && (charCode < 48 || charCode > 57));
        }

    });
    // when the user focuses out, remove non-numeric characters that might have slipped in
    $(this).on('focusout', function()
    {
        var new_detail = $(this).hasClass('enter_value');
        var type       = $(this).parents('.add_contact_type').find('.select_type :selected').data('stub');
        if ( ! new_detail || type == 'mobile' || type == 'landline')
        {
            var value = $(this).val();
            $(this).val(value.replace(/[^0-9]/g, ''));
        }
    });
};

// Hides a block of form fields.
// Disables the individual fields, so their values aren't use on submit.
// action: true = disable and hide, false = enable and show
$.fn.disableAndHide = function(action)
{
    var $this = $(this);
    (action) ? $this.hide() : $this.show();
    ($this.is('fieldset, :input')) ? $this.prop('disabled', action) : '';
    $this.find('fieldset, :input').prop('disabled', action);
    if ( ! action) $this.find('.disabled').removeClass('disabled');
};

$(document).on("ready", function(){
    $("#invite-member-form").on("submit", function(){
        try {
            if ($('#invite-member-form').validationEngine('validate')) {
                var invite_email = $('#invite-member-form [name=invite_email]').val();
                $.ajax(
                    {
                        url: "/frontend/contacts3/invite_member",
                        data: {
                            email: invite_email
                        },
                        type: "POST",
                        success: function (response) {
                            $('#invite-member-form [name=invite_email]').val("");
                            $("#member_invited_modal").modal();
                        },
                        error: function (x) {
                        }
                    }
                );
            }
            return false;
        } catch (exc) {
            console.log(exc);
        }
    });

    $('#profile-data_cleanse-btn').on('click', function() {
        $.ajax('/admin/profile/request_data_cleanse')
            .done(function(data) {
                data = JSON.parse(data);

                if (data.success) {
                    $('#profile-data_cleanse-confirm-modal').modal('hide');
                    $('#profile-data_cleanse-submitted-modal').modal();
                } else {
                    $('body').add_alert(data.message, 'danger');
                }
            })
            .fail(function() {
                $('body').add_alert('Error submitting request', 'danger');
            });
    });


});


$(document).on('change', '#mobile-international_code', function(){
    var country_code = $(this).val();
    if (country_code) {
        $.ajax({
            url:'/admin/login/ajax_get_dial_codes',
            data:{
                country_code : country_code
            },
            type: 'POST',
            dataType:'json'
        }).done(function(data){
            if (data.length == 0) {
                $('#dial_code_mobile').closest('.form-select').remove();
                $('#dial_code_mobile').closest('.form-input').remove();
                var input =
                    '   <label class="form-input form-input--text form-input--pseudo form-input--active">' +
                    '        <span class="form-input--pseudo-label label--mandatory">Code</span>' +
                    '        <input type="text" id="dial_code_mobile" name="dial_code_mobile" maxlength="5" value="" class="mobile-code validate[required]" placeholder="Code: *">' +
                    '    </label>';
                $('.col-sm-4.dial_code').append(input);
            } else {
                if (!$('#dial_code_mobile').is("select")) {
                    $('#dial_code_mobile').closest('.form-select').remove();
                    $('#dial_code_mobile').closest('.form-input').remove();
                    var select = '<label class="form-select">' +
                        '        <span class="form-input form-input--select form-input--pseudo form-input--active">\n' +
                        '            <span class="form-input--pseudo-label">Code</span>' +
                        '               <select id="dial_code_mobile" name="dial_code_mobile" class="mobile-code validate[required]" readonly="">\n' +
                                        '</select>       ' +
                        '        </span>' +
                        '    </label>';
                    $('.col-sm-4.dial_code').append(select);
                }
                $('#dial_code_mobile').find('option').remove();
                $('#dial_code_mobile').append('<option value=""></option>');
                $.each(data, function(key, code){
                    var option = '<option value="' + code.dial_code+'">'+code.dial_code+'</option>';
                    $('#dial_code_mobile').append(option);
                });

            }
        });
    }
});

