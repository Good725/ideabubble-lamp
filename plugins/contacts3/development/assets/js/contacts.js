var notifications_html = $('#contact_contact_information_section').find('.contact_types_list').html();
var selected_categories = 0 ; var categories = [];
var selected_subjects   = 0 ; var subject    = [];
var selected_courses    = 0 ; var courses    = [];
var staff_roles         = [];
var guardian_selected   = false ;
$(document).ready(function()
{
    if (document.getElementById('add_edit_contact')) {
        contact_form_loaded();
    }
    get_family_autocomplete();

    // Position the form action buttons
    actionBarScroller();
    $(document).scroll(function (e) {
        actionBarScroller();
    });
    $(document).resize(function () {
        actionBarScroller();
    });

    $(document).on('click','#add_family_todo_btn',function(ev){
        ev.preventDefault();
        $("#add_edit_todos_modal").find(".modal-content").load("/admin/contacts3/create_todo?family_id="+$("#family_menu_wrapper #family_id").val()+'&contact_id=0',function(){
            $("#add_edit_todos_modal").modal();
            related_to_autocomplete();
        });
    });

    $(document).on('click','#add_family_member_todo_btn',function(ev){
		ev.preventDefault();
        $("#add_edit_todos_modal").find(".modal-content").load("/admin/contacts3/create_todo?family_id=0&contact_id="+$("#edit_family_member_wrapper #contact_id").val(),function(){
            $("#add_edit_todos_modal").modal();
            related_to_autocomplete();
        });
    });
});

// Only show the booking section, when in the bookings tab
$(document).on('click', '.nav-tabs-family a, .nav-tabs-family_member a', function()
{
    var booking_form = document.getElementById('contact_booking_form_wrapper');
    if (booking_form)
    {
        var href = this.getAttribute('href');
        booking_form.style.display = (href == '#family-member-bookings-tab' || href == '#family-bookings-tab') ? 'block' : 'none';
    }
});

// JavaScript that also needs to be ran after the contact-editing form has been loaded dynamically should go inside this function
function contact_form_loaded()
{
    set_linked_organisation_autocomplete();
    set_linked_department_autocomplete();
    set_primary_biller_autocomplete();
    var organisation_id = $('#contact_id').val();
    get_external_organisations(organisation_id);

    var family_form = $('#add_edit_family');
    family_form.find('.btn[data-content]').popover({placement:'top',trigger:'hover'});
    var form = $('#add_edit_contact');
    $('#primary_contact_div').hide();
    if ($('#contact_family').val() === '')
    {
        $('#contact_is_primary_yes').click();
        $('.contact_role_id option[value="2"]').hide();
    }

    //var selected_courses = $('#contact_course_subject_teaching_preferences  option:selected').val();
    $('#contact_course_subject_teaching_preferences > option:selected').each(function(n)
    {
        courses[n] = $(this).val();
    });

    if ($('#contact_id').val() === '')
    {
        var role = [];
        $('#contact_role_id_1 :selected').each(function(i, selected){
            role[i] = $(selected).val();
        });
        if ($.inArray(1,role)>-1 || $.inArray(3,role)>-1)
        {
            var options = ['emergency', 'absentee', 'accounts', 'reminders', 'sms_marketing', 'email_marketing', 'marketing_updates'];
            $('#notification_preferences li input').each(function ()
            {
                $(this).prop('checked', options.indexOf($(this).data('name')) > -1 ? true : false);
            });
        }
    }

    update_edit_contact_form();
    $('.multiple_select').multiselect();
    $('.ib-combobox:not(.has-custom-combobox)').combobox();

    // Check if inputs have already been made datepickers, before making them datepickers again
    setTimeout(function()
    {
        $('input.datepicker').each(function()
        {
            if ($._data(this, 'events') && $._data(this, 'events').blur) {
                if (typeof $.fn.datetimepicker == 'function') {
                    $(this).datetimepicker('destroy');
                }
                $(this).datepicker('destroy');
            }

            $(this).datepicker({format: 'dd-mm-yyyy', orientation: 'bottom'});
        });

    }, 3000);
    $('.enter_value, [data-stub="landline"] .contactdetail_value, [data-stub="mobile"] .contactdetail_value').phoneField();

    // Needed to get validation bubbles to work with bootstrap multiselect
    form.find('.btn[data-content]').popover({placement:'top',trigger:'hover'});

    /* Autocomplete for linking familyless contacts to the current family */
    var $link_ac = $('#link_contact_to_family_autocomplete');
    var link_contact_id_field = document.getElementById('link_contact_to_family_contact_id');
    $link_ac.autocomplete(
    {
        source : '/admin/contacts3/ajax_get_all_contacts_ui?no_family=1',
        open   : function () {
			//$(this).data("uiAutocomplete").menu.element.addClass('educate_ac');
		},
        select : function (event, ui)
        {
            link_contact_id_field.value = ui.item.id;
        }
    });
    $link_ac.on('change', function()
    {
        if (this.value.trim() == '')
        {
            link_contact_id_field.value = '';
        }
    });
    $('#link_contact_to_family').on('click', function()
    {
        var data = {};
        data.contact_id = link_contact_id_field.value;
        if (data.contact_id.trim() == '')
        {
            $('#link_contact_to_family_modal_alerts').add_alert('You must select a contact', 'warning');
        }
        else
        {
            data.family_id  = document.getElementById('family_id').value;
            $.ajax({ url : '/admin/contacts3/ajax_link_to_family/', dataType : 'json', type : 'post', data : data })
                .done(function(results)
                {
                    if (results == false)
                    {
                        $('#family-details-tab').find('\+ .alert-area').add_alert('Error adding contact to family.', 'danger');
                    }
                    else
                    {
                        var $table      = $('#list_family_members_table');
                        var selected_id = $table.find('tr.selected').data('id');

                        $('#family-details-tab').find('\+ .alert-area').add_alert('New family member linked.', 'success');
                        // Refresh the table
                        $table.load('/admin/contacts3/ajax_refresh_family_members/'+data.family_id+'?selected='+selected_id, function()
                        {
                            $table.dataTable().fnDestroy();
                            $table.dataTable({"bPaginate": false, "bInfo": false, "bFilter": false});
                        });
                    }
                    $('#link_contact_to_family_modal').modal('hide');
                });
        }
    });

    // get the age from DOB
    var age = calculate_age($('#contact_date_of_birth').val());
    if(!isNaN(age)) {
        $('label[for="contact_date_of_birth"]').text(`Date of Birth: (age: ${age})`);
    }
    //display_balance();
    render_tx_balances(window.tx_balances);

    var dial_codes = $('#dial_code_mobile');
    $.each(dial_codes, function(key, item){
        $(item).closest('label.form-select').addClass('area_code');
    });
}

$(document).on('change', '#contact_type', function()
{
    update_edit_contact_form();
});

// Toggle the staff roles options and remove the required field for notification absentee
$(document).on('change', '[name=staff_member]', function()
{
	if(this.value == "1" && this.checked)
    {
		$("#staff_role").show();
        $('#notification_preferences li input').each(function()
        {
            if ($(this).data('name') == 'absentee' || $(this).data('name') == 'emergency')
            {
                $(this).removeClass('validate[funcCall[validate_preferences]]');
            }
            var role = [];
            $('#contact_role_id_1 :selected').each(function(i, selected){
                role[i] = $(selected).val();
            });
            if ($.inArray(1,role)==-1 || $.inArray(3,role)==-1)
            {
                $('#notification_preferences li input').each(function ()
                {
                    $(this).prop('checked', false);
                });
                var options = ['accounts'];
                $('#notification_preferences li input').each(function ()
                {
                    $(this).prop('checked', options.indexOf($(this).data('name')) > -1 ? true : false);
                });
            }
        });
    }
    else
    {
        $('#notification_preferences li input').each(function()
        {
            if ($(this).data('name') == 'absentee' || $(this).data('name') == 'emergency')
            {
                $(this).addClass('validate[funcCall[validate_preferences]]');
            }
        });
        $(staff_roles).each(function(n,role)
        {
            $('#contact_role_id_2').multiselect('deselect',role);
        });
        $('#contact_role_id_2').multiselect('refresh');
        $("#staff_role").hide();
        update_edit_contact_form();
	}
});

$(document).on('change', '[name=host_application-pet_details_button]', function ()
{
    if(this.value == "1") {
        $(".host_application-pet_details").disableAndHide(false);
    } else{
        $(".host_application-pet_details").disableAndHide(true);
    }
});

$('#host-application-child-add').on('click', function () {
    var children_section = document.getElementById('host-application-children');

    var $clone = $('#host_application-child-template').find('.host_application-child').clone();
    var number_of_children = children_section.getElementsByClassName('host_application-child').length;
    $clone.find("h2 > span").append(" " + (number_of_children + 1));
    $clone.find(':disabled').prop('disabled', false).removeAttr('disabled');
    $clone.find(':input').each(function () {
        this.name = this.name.replace('[index]', '[' + number_of_children + ']');
        this.id = this.id.replace('_index_', '_' + number_of_children + '_');
        this.disabled = false;
        this.removeAttribute('disabled');
    });

    $clone.find('[for]').each(function () {
        this.setAttribute('for', this.getAttribute('for').replace('_index_', '_' + number_of_children + '_'));
    });

    $(children_section).append($clone);

    if (typeof $.fn.datetimepicker === 'function') {
        $clone.find('.datepicker').datetimepicker({format: 'd/m/Y', timepicker: false, scrollInput: false});
    }
});

var lastClickedSaveButton = null;
$(document).on('click', '#add_edit_contact .save_button', function(e)
{
    var mobile_check = (this.id != 'contact_mobile_reminder_proceed_no_contact');
    var email_check = (this.id != 'contact_mobile_reminder_proceed_no_contact');
    var contactType = $('#contact_type').val();

    var action = this.getAttribute('data-action');

    var valid  = validate_contact_before_save(action, mobile_check, email_check);

    if ($('#contact_role_id_1').data("modified") && parseInt($('#contact_id').val()) > 0) {
        lastClickedSaveButton = e.currentTarget;
        $("#contact_role_changed_modal").modal();
        e.preventDefault();
        return false;
    }
    if (valid)
    {
        this.disabled = true;
        if ( ! $('#contact_preference_accounts').prop('checked') && $('#contact_type').find(':selected').data('name') != 'organisation')
        {
            check_family_account_contacts(action);
        }
        else
        {
            create_new_family_prompt(action);
        }
    }

});

// At least one family member should have "accounts", but it is not mandatory
function check_family_account_contacts(action)
{
    var family_id       = $('#contact_family_id').val();
    var accounts_needed = Boolean(family_id);

    if (accounts_needed)
    {
        $.ajax({
            url      : '/admin/contacts3/ajax_get_family_account_supervisors/'+family_id,
            dataType : 'json'
        }).success(function(results) {
            //console.log(results);
            accounts_needed = (results.length == 0 || (results.length == 1 && results[0].id == $('#contact_id').val()));
            if (accounts_needed)
            {
                var account_modal = $('#contact_no_accounts_modal');
                account_modal.find('.modal-family-name').html($('#family_id'));
                account_modal.find('.btn-primary').attr('data-action', action);
                account_modal.modal();
            }
            else
            {
                create_new_family_prompt(action);
            }
        });
    } else {
        create_new_family_prompt(action);
    }
}

var contacts3SelectSave = false;
// Save contact through the "no accounts modal" save button
$(document).on('click', '#contact_no_accounts_modal .btn-primary', function()
{
    var action = $(this).data('action');
    $('#contact_no_accounts_modal').modal('hide');
    // if new "general" contact, with no family; ask if the user wants to create a new family
    if ($('#contact_family_id').val() == '' && $('#contact_type').find(':selected').data('name') == 'student' && $('#contact_id').val() == '')
    {
        create_new_family_prompt(action);
    }
    else
    {
        save_contact(action, false);
    }
});

function create_new_family_prompt(action)
{
    if ($('#contact_family_id').val() == ''
        && $('#contact_type').find(':selected').data('name') != 'organisation'
        && $('#contact_id').val() == ''
        && window.ibcms.settings.contacts_create_family == 1
    )
    {
        var modal = $('#contact_create_new_family_modal');
        modal.find('.new-family-surname').html($('#contact_last_name').val());
        modal.find('.btn[data-choice]').attr('data-action', action);
        modal.modal();
    } // Organisations will automatically create a "family" so its members can be accessed
    else if($('#contact_type').find(':selected').data('name') == 'organisation') {
        $('#contact_new_family').val('1');
        save_contact(action, true);
    } else {
        save_contact(action, false);
    }
}

// Save a new contact through the "Create New Family?" modal
$(document).on('click', '#contact_create_new_family_modal .btn[data-choice]', function()
{
    $('#contact_new_family').val($(this).data('choice'));
    save_contact($(this).data('action'), true);
    $(this).parents('.modal').modal('hide');
    $(this).parents('.modal').find('.new-family-surname').html('');
});

$(document).on('change','#primary_contact_id',function()
{
    //var family_id = $.parseJSON(document.getElementById('family_id').value);
    //var contact_id = $(this).val();
    $.ajax({
        url         : '/admin/contacts3/ajax_set_family_primary_contact/',
        data        : {contact_id:$.parseJSON($('#primary_contact_id').val()),family_id:$.parseJSON(document.getElementById('family_id').value)},
        //contact_id  : contact_id,
        //family_id   : family_id,
        type        : 'post',
        dataType    :'json'
    }).done(function(results)
        {
            if (results.status == 'success')
            {

            }
            else
            {

            }
        })
        .error(function()
        {

        });
});

// Save contact. Use ajax and reload if on the list page
function save_contact(action, new_family)
{
    $('#contact_mobile_reminder_modal').modal('hide');
    var list_page = $('#list_contacts_table')[0] ? true : false;
    var data = $('#add_edit_contact').serialize();
    if (((action == 'save' || action == 'save_and_add') && list_page) || action == 'save-and-select' || contacts3SelectSave)
    {
        $.ajax({
            url      : '/admin/contacts3/ajax_save_contact/',
            data     : data,
            type     : 'post',
            dataType : 'json'
        }).success(function(result)
            {
                if (action == 'save-and-select' || contacts3SelectSave) {
                    if (window != window.top) {
                        var msg = {};
                        msg.action = "selected";
                        msg.contactId = result.id;
                        msg.contactName = result.name;
                        window.top.postMessage(JSON.stringify(msg), "*");
                    }
                } else {
                    if (document.getElementById('contact_id').value) {
                        // editing existing contact
                        load_family_for_contact(document.getElementById('contact_id').value);
                        var tab = $('#family-member-details-tab');
                        tab.find('.alert').remove();
                        tab.prepend(result.alerts);
                    }
                    else {
                        // adding a new contact
                        window.location = '/admin/contacts3/?contact=' + result.id +
                            (action == 'save_and_add' ? '&add_new=yes' : '');
                    }
                }
            });
    }
    else
    {
        $('#contact_action').val(action);
        $('#add_edit_contact').submit();
    }
}

function validate_contact_before_save(action, mobile_check, email_check)
{
    if (typeof mobile_check == 'undefined') mobile_check = true;
    if (typeof action       == 'undefined') action       = 'save';
    if (typeof email_check  == 'undefined') email_check  = true;

    var $form  = $('#add_edit_contact');

	var $multiselects_to_validate = $('.multiple_select[class*=validate]');

	// Multiselect tags are hidden, while a ui-friendly list is shown.
	// Input tags with "display: none;" are ignored during validation.
	// This will make the tags visible to the JS, but not to humans,
	// while keeping them in the same position on the page, so the error bubbles appear in the correct location
	$multiselects_to_validate.css('position', 'absolute').css('z-index', '-9999').show();

    if ($("#staff_member_yes").prop("checked") && $("#contact_role_id_2").val() == null && $("#staff_information_section").css("display") != 'none') {
        $("#stuff_role_not_selected_modal").modal();
        return false;
    }

    var has_role = $(".contact_role_id ");
    if(has_role.find('[data-name=teacher]:selected').length > 0 ||
            has_role.find('[data-name=admin]:selected').length > 0 ||
            has_role.find('[data-name=supervisor]:selected').length > 0)
    {
        $('#contact_role_id_1').hasClass('validate[funcCall[validate_role]]') ? $('#contact_role_id_1').removeClass('validate[funcCall[validate_role]]') : '';
        $('#contact_role_id_1').multiselect('rebuild');
        $('#contact_role_id_2').hasClass('validate[funcCall[validate_role]]') ? '' : $('#contact_role_id_2').addClass('validate[funcCall[validate_role]]');
        $('#contact_role_id_2').multiselect('rebuild');

        if(has_role.find('[data-name=teacher]:selected').length > 0)
        {
            $("#teacher_subject_teaching_preferences").addClass('validate[required]');
            $("#teacher_subject_teaching_preferences").multiselect('rebuild');

            $("#teacher_course_type_teaching_preferences").addClass('validate[required]');
            $("#teacher_course_type_teaching_preferences").multiselect('rebuild');

            $("#teacher_course_subject_teaching_preferences").addClass('validate[required]');
            $("#teacher_course_subject_teaching_preferences").multiselect('rebuild');
        }
    }
    else
    {
        $('#contact_role_id_1').hasClass('validate[funcCall[validate_role]]') ? '' : $('#contact_role_id_1').addClass('validate[funcCall[validate_role]]');
        $('#contact_role_id_1').multiselect('rebuild');
        $('#contact_role_id_2').hasClass('validate[funcCall[validate_role]]') ? $('#contact_role_id_1').removeClass('validate[funcCall[validate_role]]') : '';
        $('#contact_role_id_2').multiselect('rebuild');
    }
	// Perform the validation
	var valid  = $form.validationEngine('validate');

	// Reset multiselect styling
	$multiselects_to_validate.hide().css('position', '').css('z-index', '');

    var contact_details = false;
    if ( ! valid)
    {
        //setTimeout("$('.formError').remove()", 10000);
    }
    else {
        if (mobile_check)
        {
            var $mobile_section = $form.find('#contact-mobile');
            document.getElementById('contact_mobile_reminder_proceed_no_contact').setAttribute('data-action', action);
            if ($mobile_section.val() == "") {
                $('#no_mobile_number').show();
                contact_details = true;
                valid = false;
            }
            else {
                $('#no_mobile_number').hide();
            }
        }
        if (email_check)
        {
            var $email_section = $form.find('#contact-email');
            document.getElementById('contact_mobile_reminder_proceed_no_contact').setAttribute('data-action', action);
            if ($email_section.val() == "") {
                $('#no_email_address').show();
                contact_details = true;
                valid = false;
            }
            else {
                $('#no_email_address').hide();
            }
        }
    }
    if (contact_details)
    {
        $("#contact_mobile_reminder_proceed_no_contact").prop("disabled", false);
        $('#contact_mobile_reminder_modal').modal();
    }

    return valid;
}


$(".save-and-select").on("click", function(){
    contacts3SelectSave = true;
    if (validate_contact_before_save()){
        save_contact("save-and-select", false);
    }
});

$(".close-dialog").on("click", function (){
    if (window != window.top) {
        var msg = {};
        msg.action = "close-dialog";
        window.top.postMessage(JSON.stringify(msg), "*");
    }
});

// Custom rules for class="validate[funcCall[...]]"
function validate_primary(field, rules, i, options)
{
    // valid if
    // - primary contact is checked
    // - or - the contact has no family
    // - or - the contact's family has at least one primary contact, who is not the current contact
    if (($('#contact_is_primary').find(':checked').val() != 1))
    {
        var family_id = $('#contact_family_id').val();
        var valid     = (family_id == '');
        if ( ! valid)
        {
            $.ajax({
                url      : '/admin/contacts3/ajax_get_primary_contacts/'+family_id,
                type     : 'post',
                dataType : 'json'
            }).success(function(results)
                {
                    valid = (results.length > 1 || (results.length == 1 && results[0].id != $('#contact_id').val()));
                });
        }

        if ( ! valid)
        {
            rules.push('maxCheckBox[2]');
            return '* A family must have at least one primary contact';
        }
    }
}
function validate_role(field, rules, i, options)
{
    if ($("#contact_type").val() == 1) {
        var $has_role = $('.contact_role_id ');
        var is_primary = ($('#contact_is_primary').find(':checked').val() == 1);
        if ($has_role.find('option:selected').length == 0) {
            rules.push('required');
            return '* At least one role must be selected.';
        }
        else if (is_primary && $has_role.find('[data-name=student]:selected').length > 0) {
            rules.push('required');
            return '* A child can not be a primary contact.';
        }
    }
}
function validate_school_year(field, rules, i, options)
{
    if ($('.contact_role_id ').find('[data-name=student]:selected').length > 0 && $('#contact_year_id').val() == '')
    {
        rules.push('required');
        return 'Required for children';
    }
}
function validate_preferences(field, rules, i, options)
{
    var name          = field.data('name');
    var checked_count = field.parents('fieldset').find('[name="preferences[]"]:checked').size() + 1;

    // Primary contact must have "emergency" and "absentee"
    if ( ! field.prop('checked') && (name == 'emergency' || name == 'absentee') && $('#contact_is_primary').find(':checked').val() == 1 && $("#contact_type").val() == "1")
    {
        if (name == 'emergency') {
            return '* "Emergency" is required for primary contacts.';
        }
        if (name == 'absentee') {
            return '* "Absentee SMS + CALLS" is required for primary contacts.';
        }
    }
}
function validate_county(field, rules, i, options)
{
    if ($('#contact_country').find(':selected').val() == 'IE' && $('#contact_county').find(':selected').val() == '')
    {
        rules.push('required');
        return 'Required for Irish addresses.';
    }
}

function validate_dob(field, rules, i, options)
{    
    var date_string = $('#contact_date_of_birth').val();
    if ( date_string != '')
    {
        var parts = date_string.split('/');
        
        if(parts[2] && parts[1] && parts[0]){

            var year = parseInt(parts[2]) , month = (parseInt(parts[1]) - 1) , day = parseInt(parts[0]);
            var dob =  new Date( year, month , day) , date_now =  new Date(Date.now());
            if( dob.getTime() > date_now.getTime() ){
                rules.push('required');
                return 'Date of Birth can not be greater than today\'s date';
            }
            
        }
        else{
             rules.push('required');
            return 'Wrong Date of Birth value';
        }       
    }
}

function validate_contact_enter_value(field, rules, i, options)
{
    if (field.val() != "") {
        return 'You need to click add button';
    }
}

function validate_code_select_not_empty(field, rules, i, options){
    if(field.val() == "") {
        return "Code is required";
    }
}

// get the age from DOB
function calculate_age(dob)
{
    var mnow = moment();
    var mdob = moment(dob, "DD-MM-YYYY");
    return mnow.diff(mdob, 'years');
}

$(document).on('change', '#contact_date_of_birth', function() {
    var age = calculate_age($('#contact_date_of_birth').val());
    if (!isNaN(age)) {
        $('label[for="contact_date_of_birth"]').text(`Date of Birth (age: ${age})`);
    } else {
        $('label[for="contact_date_of_birth"]').text(`Date of Birth`);
    }
});

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
    var count_new_contact_options = $('input[type=hidden].notification_new').length;
    var new_created_id = 0;
    console.log(count_new_contact_options);
    if (type_stub == 'mobile' || type_stub == 'landline')
    {
        if (type_stub == 'mobile' )
        {

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
        if (count_new_contact_options == 1) {
            new_created_id = 1;
        } else {
            new_created_id = count_new_contact_options + 1;
        }

        // TODO: Load from views/admin/snippets/add_edit_contact_method
        add_contact_type.find('.contact_types_list').append(
        '<div class="contactdetail_wrapper mb-3" data-type="' + type_stub + '">' +
            '<div class="input_group">\n' +
                '<span class="input_group-icon" title="'+type_stub+'" data-stub="'+type_stub+'"><span class="icon-'+icon+'"></span></span>\n' +
                '<input type="hidden" id="notification_type_new_' + new_created_id+ ' " class="notification_new" name="contactdetail_value[new_' + new_created_id + '][notification_id]" value="' + type_id + '"/>' +
                '<input class="form-input contactdetail_value'+((type_stub == 'email') ? ' validate[custom[email]]' : '') + '" name="contactdetail_value[new_' + new_created_id + '][value]" ' +
                     ((type_stub == 'email') ? ' id="contact_email" ' : '') + ' type="text" value="'+value+'" />'+
                '<input name="contactdetail_type_id[new_'+new_created_id+']" type="hidden" value="'+type_id+'">'+
                '<span class="input_group-icon">' +
                    '<button type="button" class="btn btn-link remove-contactdetail-button">' +
                        '<span class="icon-remove"></span>' +
                    '</button>' +
                '</span>\n' +
            '</div>' +
        '</div>');

        add_contact_type.find('.enter_value').val('');

        if (type_stub == 'mobile') {
            $(".add_contact_type .select_type").val("1");
        }
    }
});

$(document).on('change', '.add_contact_type .select_type', function (){
    var selected_type_el = $(this);
    var selected_type = $(this).val();
    var inputs = $(this).closest('.input_group').find('.form-input');
    var contact_type_el = $(this).closest('.add_contact_type');
    var new_item = '';
    var count_new_contact_options = $('input[type=hidden].notification_new').length;
    var new_created_id = 0;
    console.log(count_new_contact_options);
    if (count_new_contact_options == 1) {
        new_created_id = 1;
    } else {
        new_created_id = count_new_contact_options + 1;
    }

    if (selected_type == 2 || selected_type == 3) {
        var selected_type_string = selected_type == 2 ? 'mobile'  : 'landline';
        var codes = selected_type == 2 ? mobile_codes : landline_codes;
        if (selected_type == 2) {
            if(contact_type_el.find('#mobile-international_code').length > 0) {
                $('.alert_area').add_alert(
                    'You can add only one mobile number per contact',
                     'danger popup_box');
                selected_type_el.val(1);
                return false;
            }
        }
        new_item = '<div class="contactdetail_wrapper mb-3 phone_contact_data" >' +
            '<input type="hidden" id="notification_type_new_' + new_created_id + '" class="notification_new notification_type" name="contactdetail_value[new_' + new_created_id + '][notification_id]" value="' + selected_type  + '"> ' +
            '<input name="contactdetail_id[new_'+new_created_id+']" type="hidden" value="new_'+new_created_id+'"/>' +
           '<div class="col-sm-3 notification_new" style="padding-left: 0; height: 3em!important; padding-right:0; margin-bottom: 3px; font-size: 12px;">' +
                '<label class="form-select">' +
                '<span class="form-input form-input--select form-input--pseudo form-input--active">' +
                    '<span class="form-input--pseudo-label">Country</span>' +
                        '<select id="' + selected_type_string + '-international_code" name="contactdetail_value[new_' + new_created_id + '][country_dial_code_' + selected_type_string + ']" class="' + selected_type_string + '-international_code validate[required]" readonly="" style="height: 3em!important; padding-right: 0;">' +
                            countries_options +
                        '</select>' +
                    '</span>'+
                '</label>' +
            '</div>';
        if (selected_type == 3) {
            new_item +=
                '<div class="notification_new col-sm-3 dial_code" style="padding-left: 0; height: 3em!important; padding-right:0; margin-bottom: 3px; font-size: 12px;">' +
                '<label class="area_code form-input form-input--text form-input--pseudo form-input--active" style="padding-right: 1em;">' +
                '        <span class="form-input--pseudo-label label--mandatory">Code</span>' +
                '        <input type="text" id="dial_code_'+selected_type_string+'" ' +
                            'name="contactdetail_value[new_' + new_created_id + '][dial_code_'+selected_type_string+']" ' +
                            'value="" ' +
                            'class="mobile-code validate[required]" placeholder="Code: *" ' +
                            'style="height: 3em!important;">' +
                '    </label>' +
                '</div>';
        } else {
            new_item +='' +
                '<div class="notification_new col-sm-3 dial_code" style="padding-left: 0; height: 3em!important; padding-right:0; margin-bottom: 3px; font-size: 12px;">' +
                        '<label class="form-select area_code">' +

                            '<span class="form-input form-input--select form-input--pseudo form-input--active">' +
                                '<span class="form-input--pseudo-label">Code</span>' +
                                 '<select id="dial_code_'+selected_type_string+'" name="contactdetail_value[new_' + new_created_id + '][dial_code_'+selected_type_string+']" class="'+selected_type_string+'-code validate[required]" readonly="" style="height: 3em!important; padding-right: 0;" >' +
                                    codes +
                                '</select>' +
                            '</span>' +
                        '</label>' +
                '</div>';
        }

        new_item += '<div class="notification_new col-sm-4" style="padding-left: 0; height: 3em!important; padding-right:0; margin-bottom: 3px; font-size: 12px;">' +
            '<label class="form-input form-input--text form-input--pseudo form-input--active">' +
                '<span class="form-input--pseudo-label">' + selected_type_string + '</span>' +
                '<input type="text" id="edit_profile_phone" name="contactdetail_value[new_'+  new_created_id +'][' + selected_type_string + ']" value="" placeholder="'+selected_type_string+':" style="height: 3em!important; padding-right: 0;">' +
            '</label>' +
        '</div>'+
            '<div class="notification_new col-sm-1" style="padding-left: 0; margin-bottom: 10px; margin-top: 0;">' +
                '<button class="btn btn-link remove-contactdetail-button" ' +
                    'type="button" style="    height: 2.9em; width: 100%; padding-right: 25px;padding-left: 15px;background-color: white;border-radius: 0;border-color: #ccc; color: var(--primary);"><span class="icon-remove"></span></button>' +
            '</div>'+
        '</div>'

        $(new_item).insertBefore(selected_type_el.closest('.input_group'));
        var select_name = 'contactdetail_value[new_' + new_created_id + '][country_dial_code_' + selected_type_string + ']';
        $("select[name='" + select_name+"']").val('353');
        selected_type_el.val(1);
    } else {
        if (!$('.form-input.enter_value')) {
            new_item = '<input class="form-input border-left rounded-0 enter_value validate[funcCall[validate_contact_enter_value]]" name="tmp_contact_enter_value" type="text" id="contact_enter_value" style="width:155px;width:calc(100% - 80px);" />';
            $(new_item).insertBefore(selected_type_el.closest('.input_group').find('.input_group-icon'));
        }
    }
});

$(document).on('change', '#mobile-international_code', function(){
    var country_code = $(this).val();
    var country_code_element = $(this);
    var selected_type = $(this).closest('.contactdetail_wrapper').find('.notification_type').val();
    var selected_type_string = '';
    if (selected_type == 3) {
        selected_type_string = 'landline';
    } else {
        selected_type_string = 'mobile';
    }
    var notification_id = $(this).closest('.contactdetail_wrapper').find('.notification_type').attr('id').replace('notification_type_','');
    console.log(notification_id);
    var dial_code_element = $(this).closest('.contactdetail_wrapper').find('#dial_code_' + selected_type_string);
    if (country_code) {
        $.ajax({
            url:'/admin/login/ajax_get_dial_codes',
            data:{
                country_code : country_code,
                phone_type: selected_type_string
            },
            type: 'POST',
            dataType:'json'
        }).done(function(data){
            if (data.length == 0) {
                country_code_element.closest('.contactdetail_wrapper').find('.area_code').remove();
                var input =
                    '   <label class="area_code form-input form-input--text form-input--pseudo form-input--active" style="padding-right: 1em;">' +
                    '        <span class="form-input--pseudo-label label--mandatory">Code</span>' +
                    '        <input type="text" id="dial_code_mobile" ' +
                                     'name="contactdetail_value[' + notification_id + '][dial_code_'+selected_type_string+']" ' +
                                     'value="" ' +
                                    'class="mobile-code validate[required]" placeholder="Code: *" ' +
                    'style="height: 3em!important;">' +
                    '    </label>';
                country_code_element.closest('.contactdetail_wrapper').find('.col-sm-3.dial_code').append(input);
            } else {
                if (!dial_code_element.is("select")) {
                    country_code_element.closest('.contactdetail_wrapper').find('.area_code').remove();
                    var select = '<label class="form-select area_code">' +
                        '        <span class="form-input form-input--select form-input--pseudo form-input--active" style="padding-right: 1em;">\n' +
                        '            <span class="form-input--pseudo-label">Code</span>' +
                                        '<select id="dial_code_'+selected_type_string+'" ' +
                                            'name="contactdetail_value[' + notification_id + '][dial_code_'+selected_type_string+']" ' +
                                            'class="'+selected_type_string+'-code validate[required]" readonly="" style="height: 3em!important;">' +
                                         '</select>' +
                        '        </span>' +
                        '    </label>';
                    country_code_element.closest('.contactdetail_wrapper').find('.col-sm-3.dial_code').append(select);
                }
                country_code_element.closest('.contactdetail_wrapper').find('#dial_code_' + selected_type_string).find('option').remove();
                country_code_element.closest('.contactdetail_wrapper').find('#dial_code_' + selected_type_string).append('<option value=""></option>');
                $.each(data, function(key, code){
                    var option = '<option value="' + code.dial_code+'">'+code.dial_code+'</option>';
                    country_code_element.closest('.contactdetail_wrapper').find('#dial_code_' + selected_type_string).append(option);
                });
            }
        });
    }
});
$(document).on('change', '#landline-international_code', function(){
    var country_code = $(this).val();
    var country_code_element = $(this);
    var selected_type = $(this).closest('.contactdetail_wrapper').find('.notification_type').val();
    var selected_type_string = 'landline';
    var notification_id = $(this).closest('.contactdetail_wrapper').find('.notification_type').attr('id').replace('notification_type_','');
    var dial_code_element = $(this).closest('.contactdetail_wrapper').find('#dial_code_' + selected_type_string);
    country_code_element.closest('.contactdetail_wrapper').find('.area_code').remove();
    var input =
        '   <label class="area_code form-input form-input--text form-input--pseudo form-input--active" style="padding-right: 1em;">' +
        '        <span class="form-input--pseudo-label label--mandatory">Code</span>' +
        '        <input type="text" id="dial_code_mobile" ' +
        'name="contactdetail_value[' + notification_id + '][dial_code_'+selected_type_string+']" ' +
        'value="" ' +
        'class="mobile-code validate[required]" placeholder="Code: *" ' +
        'style="height: 3em!important;">' +
        '    </label>';
    country_code_element.closest('.contactdetail_wrapper').find('.col-sm-3.dial_code').append(input);
});
// Remove a contact method
$(document).on('click', '.add_contact_type .remove-contactdetail-button', function()
{
    var primary = $('#contact_is_currently_primary').val();
    var stub = $(this).closest('div').find().data('stub');
    var contact_notification_id = $(this).closest('.contactdetail_wrapper').find('input[name*="contactdetail_id"]').val();
    var $mobile_numbers = $('.contactdetail_wrapper [data-stub="mobile"]');

    // Cannot delete a primary contact's only mobile number
    if (primary == 1 && stub == 'mobile' && $mobile_numbers.length < 2) {
        $('#contact_mobile_delete_primary').modal();
    }
    else {
        $('#contact_mobile_delete_number_id').val(contact_notification_id);
        console.log($(this).closest('.contactdetail_wrapper'));
        $(this).closest('.contactdetail_wrapper').addClass('contactdetail-delete-target');
        var title = $(this).closest('.contactdetail_wrapper').data('type');
        $('#delete_message').text('You are about to delete the contact '+title+'.');
        $('#contact_mobile_delete').modal();
    }
});

$(document).on('click','#contact_mobile_delete_proceed',function() {
    var contact_id     = $('#contact_id').val();
    var $target        = $('.contactdetail-delete-target');
    var contact_number = $('#contact_mobile_delete_number_id').val();
    console.log(contact_number);
    if ($target.length == 0) {
        $target = $('#notification_type_' + contact_number).closest('.contactdetail_wrapper');
    }
	// If this is a new contact, we don't need to touch the server
	if (contact_id == '' || contact_number == 'new' || contact_number.indexOf('new') !== -1) {
		$target.remove();
	}
	else {
		$.ajax({
			url     : '/admin/contacts3/ajax_remove_contact_info/',
			data    : {
				number_id: contact_number,
				contact_id: contact_id
			},
			type     : 'post',
			dataType : 'json'
		})
			.done(function(result) {
				if (result.status == 'success') {
					$('#contact_mobile_delete').modal('hide');
					$('#contact_mobile_reminder_proceed').removeAttr("disabled");
					$target.remove();
				}
				$target.removeClass('contactdetail-delete-target');
                $('#contact_mobile_delete_number_id').val('');
			})
			.fail(function() {
                $target.removeClass('contactdetail-delete-target');
                $('#contact_mobile_delete_number_id').val('');
			});
	}
	$('#contact_mobile_delete').modal('hide');
});

$(document).on('hide.bs.modal', '#contact_mobile_delete', function()
{
	$('.contactdetail-delete-target').removeClass('contactdetail-delete-target');
});


$(document).on('click','#contact_mobile_reminder_proceed_no_contact',function()
{
    $('#contact_mobile_reminder_modal').modal('hide');
});


// Validate a change to a contact method
$(document).on('blur', '.contactdetail_value:not([readonly])', function()
{
    var detail     = $(this).parents('.contactdetail_wrapper');
    var type_stub  = detail.find('[data-stub]').data('stub');
    var value      = $(this).val().replace(/\s/g, '');
    var pattern    = /[0-9]*/;
    var email_pattern = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{1,4}$/i;

    if ((type_stub == 'mobile' || type_stub == 'landline'))
    {
        if ((value != value.match(pattern)) || value == '')
        {
            alert('A phone number must contain only numbers.');

            // Only keep the portion of the number that matches the pattern
            detail.find('.contactdetail_value').val(value.match(pattern));
        }
        else
        {
            new_phone_number(value);
        }
    }
    else if (type_stub == 'email')
    {
        if ( ! email_pattern.test(value) || value == '')
        {
            alert('This is not a valid email address');
        }
        else
        {
            detail.find('.contactdetail_value').val(value.toLowerCase());
        }
    }
});

$(document).on('change','#contact_school_id',function()
{
    var valueSelected = $(this).val();
    if (valueSelected == 'new')
    {
        $('#new_school').show();
    }
    else
    {
        $('#new_school').hide();
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

// Only allow numbers to be typed in mobile and landline fields
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

$(document).on("click", "#contact_role_mistake_continue", function(){
    update_edit_contact_form(true);
});

$(document).on("click", "#contact_role_mistake_cancel", function(){
    $(".contact_role_id option").each (function(){
        this.selected = false;
    });
    for (var i = 0 ; i < currentContactRoles.length ; ++i) {
        $(".contact_role_id option[value=" + currentContactRoles[i] + "]").prop("selected", true);
    }
    $(".contact_role_id").multiselect('rebuild');
    update_edit_contact_form();
});

// Add a new tag to a contact
$(document).on('click', '#contact-tag-add', function() {
    const $clone = $('#contact-tag-template').clone();
    const $input = $('#contact-tag-selector-input');
    const $select = $('#contact-tag-selector');

    if ($select.val()) {
        // Adding an existing tag (Pass its ID to the server)
        $clone.find('.contact-tag-id').val($select.val());
        $clone.find('.contact-tag-title').val($select.find(':selected').html());
        $clone.find('.contact-tag-title-text').html($select.find(':selected').html());
    } else {
        // Adding a new tag (Don't pass an ID to the server)
        $clone.find('.contact-tag-id').val(0);
        $clone.find('.contact-tag-title').val($input.val());
        $clone.find('.contact-tag-title-text').text($input.val());
    }

    // Remove template-only properties
    $clone.find(':disabled').prop('disabled', false);
    $clone.find('[disabled]').removeAttr('disabled');
    $clone.removeClass('hidden').removeAttr('id');

    // Reset the input
    $input.val('');
    $select.val('');

    // Add the tag to the list
    $('#contact-tags-list').append($clone);
});

// Remove a tag from a contact
$(document).on('click', '.contact-tag-remove', function() {
    $(this).parents('.contact-tag').remove();
});

function update_edit_contact_form(continue_on_mistake)
{
    var contact                 = {"type" : $('#contact_type').find(':selected').data('name')};
    var education               = $('#educational_details_section'),
        subject_teaching        = $('#subject_teaching_preferences_section'),
        course_type_teaching    = $('#course_type_teaching_preferences_section'),
        course_subject_teaching = $('#courses_subject_preferences_section'),
        pps_number              = $('#contact_pps_details'),
        notification            = $('#notification_preferences'),
        note                    = $('#contact_note_section'),
		staff_information       = $('#staff_information_section'),
        member_information      = $('#member_information_section'),
        special_preferences     = $('#special_preferences_section'),
        organisation_section    = $('#organisation_section'),
        student_info            = $('#educational_student_info'),
        $has_role               = $(".contact_role_id"),
        host_app_section        = $("#host-family-section"),
        pet_details_button      = $(".host_application-pet_details_button"),
        pet_details             = $(".host_application-pet_details");

    var mistake_warning = false;

    var has_role_child = false;
    var has_role_nonchild = false;
    var selected_roles = [];
    $has_role.find("option").each (function(){
        if (this.selected) {
            if (this.value == 2) {
                has_role_child = true;
            } else {
                has_role_nonchild = true;
            }
            selected_roles.push(this.innerHTML)
        }
    });
    if ($("#staff_member_yes").checked){
        has_role_nonchild = true;
    }
    if (has_role_nonchild && has_role_child) {
        mistake_warning = true;
        $("#contact_role_mistake_modal .modal-body p span").html(selected_roles.join(', '));
    }

    if (!continue_on_mistake && mistake_warning) {
        $("#contact_role_mistake_modal").modal();
        return false;
    } else {

    }

    currentContactRoles = [];
    $has_role.find("option").each (function(){
        if (this.selected) {
            currentContactRoles.push(this.value);
        }
    });

    host_app_section.disableAndHide(true);
    $('.toggleable-block').disableAndHide(true);
    $('#other_section').disableAndHide(false);

    pps_number.disableAndHide(false);
    // By default, all contact types cannot have their marketing preferences enabled/disabled
    notification.disableAndHide(false);
    $('#notification_preferences').find('.contact_preferences').not('.contact_preference_marketing_updates_item').disableAndHide(true);

	if($has_role.find('[data-name=guardian]:selected').length > 0){
		notification.disableAndHide(false);
		set_primary_guardian();
	}

	if($has_role.find('[data-name=student]:selected').length > 0){
		education.disableAndHide(false);
		student_info.disableAndHide(false);
		note.disableAndHide(false);
        special_preferences.disableAndHide(false);
	}

    if($has_role.find('[data-name=mature]:selected').length > 0){
        notification.disableAndHide(false);
        education.disableAndHide(false);
        note.disableAndHide(false);
        special_preferences.disableAndHide(false);
    }
	
	if($has_role.find('[data-name=teacher]:selected').length > 0){
		subject_teaching.disableAndHide(false);
        course_type_teaching.disableAndHide(false);
		notification.disableAndHide(false);
		set_primary_guardian();
        if ($('#contact_id').val() == '')
        {
            teachers_multiselect();
            teacher_preferences();
        }
        else
        {
            course_subject_teaching.disableAndHide(false);
            teachers_multiselect();
            teacher_preferences();
        }
	}
	
	if($has_role.find('[data-name=admin]:selected').length > 0){
        notification.disableAndHide(false);
		set_primary_guardian();
	}
	
	if($has_role.find('[data-name=supervisor]:selected').length > 0){
        notification.disableAndHide(false);
		set_primary_guardian();
	}
    $("#staff_role").after($('.organisation'));
    $(".type-all").disableAndHide(false);
    $("#family_information_section").disableAndHide(false);
    $("#member_information_section").disableAndHide(false);

    switch (contact.type) {
        case 'family':
        case 'staff':
            $(".type-general").disableAndHide(false);
            staff_information.disableAndHide(false);
            $(".contact-type-general").disableAndHide(false);
            break;
        case 'billed':
            $(".type-billed").disableAndHide(false);
            staff_information.disableAndHide(true);
            $(".contact-type-general").disableAndHide(true);
            set_primary_guardian();
            break;
        case 'organisation':
            $(".organisation_contact").disableAndHide(false);
            $(".type-subtype-prop").disableAndHide(false);
            $(".toggleable-block").disableAndHide(true);
            $("#member_information_section").disableAndHide(false);
            $(".type-organisation").disableAndHide(false);
            $(".timeoff_hours").disableAndHide(false);
            $("#family_information_section").addClass('hidden');
            $("#contact-email-invite").addClass('hidden');
            email_check = false;
            mobile_check = false;
            break;
        case 'department':
            $('.type-organisation.type-department').find('.border-title').after($('.organisation'))
            $(".toggleable-block").disableAndHide(true);
            $(".type-department").disableAndHide(false);
            email_check = false;
            mobile_check = false;
            break;
        case "host":
            $(".type-billed").disableAndHide(false);
            staff_information.disableAndHide(true);
            $(".contact-type-general").disableAndHide(true);
            set_primary_guardian();
            notification.disableAndHide(true);
            host_app_section.disableAndHide(false);
            //pet_details.disableAndHide(true);
            break;
        case 'student':
            $(".type-billed").disableAndHide(false);
            staff_information.disableAndHide(false);
            set_primary_guardian();
            host_app_section.disableAndHide(true);
            break;
        default:
            $(".type-billed").disableAndHide(false);
            staff_information.disableAndHide(false);
            $(".contact-type-general").disableAndHide(true);
            set_primary_guardian();
            host_app_section.disableAndHide(true);
            break;
    }
}

function set_primary_guardian()
{
	if( ! guardian_selected){ // set a guardian if not selected any role
        guardian_selected = true ;
	}
}

// If a "Use Family" checkbox is clicked, change relevant form details to match the family
// Form fields become disabled when they are changed to match the family
// Uncheck to make the fields editable again
$(document).on('change', '.use_family_box', function(ev)
{
    var use_family = $(this).prop('checked');
    var fieldset   = $(this).parents('.family_sharable_section');

    switch (fieldset.attr('id'))
    {
        case 'contact_address_information_section':
            toggle_use_family_address(fieldset, use_family);
            break;
        case 'contact_contact_information_section':
            toggle_use_contact_information(fieldset, use_family);
            break;
    }

    fieldset.find(':input:not(.use_family_box)').prop('readonly', use_family);
	fieldset.find('button').prop('disabled', use_family);
    (use_family) ? fieldset.find('select').addClass('readonly') : fieldset.find('select').removeClass('readonly');
});

/**
 * @Purpose: Change the "Contact information" fields to match the family or undo said changes
 * @Params:
 *      fieldset   - the fieldset containing the relevant fields
 *      use_family - true: change the fields to match the family's details. false: change the fields to what they were on the page load
 */
function toggle_use_contact_information(fieldset, use_family)
{
    var group_id_field = $('#contact_notifications_group_id');
    if (use_family)
    {
        // notifications_html = fieldset.find('.contact_types_list').html();
        $.ajax({
            url      : '/admin/contacts3/ajax_load_notifications',
            data     : { family_id : $('#contact_family_id').val() },
            type     : 'post',
            dataType : 'json'
        }).success(function(result)
            {
                fieldset.find('.contact_types_list').html(result['html']);
                group_id_field.val(result['group_id']);
                // fieldset.find('.btn').prop('disabled', true).addClass('readonly');
            });
    }
    else
    {
        var notification_group_id = '';
        if ($('#contact_is_currently_primary').val() == 1)
        {
            notification_group_id = $('#contact_notifications_group_id').val() ;
        }
        else
        {
            $('#contact_contact_information_section .contact_types_list').each('.contactdetails_wrapper',function(){$(this).remove();});
            notification_group_id = $('#contact_notifications_group_id').val() == $('#family_notifications_group_id').val() ? 'new' : $('#contact_notifications_group_id').val();
        }
        $('#contact_notifications_group_id').val(notification_group_id);
    }
}

/**
 * @Purpose: Change the "Address information" fields to match the family or undo said changes
 * @Params:
 *      fieldset   - the fieldset containing the relevant fields
 *      use_family - true: change the fields to match the family's details. false: change the fields to what they were on the page load
 */
function toggle_use_family_address(fieldset, use_family)
{
    var address_id_field = $('#contact_address_id');
    if (use_family)
    {
        $.ajax({
            url      : '/admin/contacts3/ajax_get_residence',
            data     : { family_id : $('#contact_family_id').val() },
            type     : 'post',
            dataType : 'json'
        }).success(function(result)
            {
                fieldset.find('[name="address1"]').val(result.address1);
                fieldset.find('[name="address2"]').val(result.address2);
                fieldset.find('[name="address3"]').val(result.address3);
                fieldset.find('[name="town"]')    .val(result.town);
                fieldset.find('[name="county"]')  .val(result.county);
                fieldset.find('[name="country"]') .val(result.country);
                fieldset.find('[name="postcode"]').val(result.postcode);
                address_id_field.val(result.address_id);
            });
    }
    else
    {
        address_id_field.val('');
        var address_id = ($('#contact_is_currently_primary').val() == 1) ? $('#contact_address_id').val() : '' ;
        $('#contact_address_id').val(address_id);
    }
}

$('#contact_find').autocomplete({
    source : '/admin/contacts3/ajax_get_all_contacts_ui',
    open   : function () {
		//$(this).data("uiAutocomplete").menu.element.addClass('educate_ac');
	},
    select : function (event, ui)
    {
		// Store data regarding this choice in the modal box prompt and show the prompt.
		$('#contact_data_overwrite_name').html('').html(ui.item.value);
		$('#contact_data_overwrite_confirm').attr('data-ui_item', '').attr('data-ui_item', JSON.stringify(ui.item));
		$('#contact_data_overwrite_modal').modal();
    }
});

$(document).on('click', '#contact_data_overwrite_confirm', function()
{
	var ui_item = JSON.parse(this.getAttribute('data-ui_item'));

	// Fill the form with data from the chosen contact
	$('#contact_type').find('[data-name="'+ui_item.type.toLowerCase()+'"]').prop('selected', true);
	update_edit_contact_form();
	var form = $('#add_edit_contact');

	var update_values = ['address1', 'address2', 'address3', 'first_name', 'last_name', 'town', 'postcode', 'coordinates'];
	for (var i = 0; i < update_values.length; i++)
	{
		form.find('[name="'+update_values[i]+'"]').val(ui_item[update_values[i]]);
	}

	var update_choices = ['family_id', 'title', 'county', 'country'];
	for (i = 0; i < update_choices.length; i++)
	{
		form.find('[name="'+update_choices[i]+'"] [value="'+ui_item[update_choices[i]]+'"]').prop('selected', true);
	}

    form.find('#contact_enter_value').val(ui_item['mobile']);

	// Reset data entered in the modal box and hide it
	$('#contact_data_overwrite_modal').modal('hide');
	$('#contact_data_overwrite_name').html('');
	$('#contact_data_overwrite_confirm').attr('data-ui_item', '');
});

// Position the form action buttons
function actionBarScroller()
{
    $('.action-buttons').each(function() {
        var marker = $(this).find('\+ .floating-nav-marker');

        if (typeof marker[0] != 'undefined') {
            var viewportHeight   = window.innerHeight ? window.innerHeight : $(window).height();
            var markerIsOnscreen = (marker[0].getBoundingClientRect().top <= viewportHeight);

            $(this).toggleClass('floatingMenu', !markerIsOnscreen).toggleClass('fixedMenu', markerIsOnscreen);
        }
    });
}

// Display a modal box when the user clicks "Delete"
$(document).on('click', '#add_edit_contact .action-buttons .delete_button', function()
{
    var contact_id = $('#contact_id').val();
    $.ajax({
        type: 'POST',
        url: '/admin/bookingstransactions/ajax_check_outstanding_transactions',
        data: {"contact_id":contact_id},
        dataType: 'json'
    })
        .done(function(results)
        {
            if(results.status == 'success' )
            {
                if (results.outstanding || results.bookings)
                {
                    $('#cannot_delete_message').html(results.message);
                    $('#contact_cannot_delete_contact').modal();
                }
                else
                {
                    $.ajax({
                        type: 'POST',
                        url: '/admin/contacts3/test_contact_delete',
                        data: {contact_id: contact_id},
                        dataType: 'json'
                    }).done(function(results){
                        if (results.ok) {
                            $('#contact_confirm_delete').modal();
                        } else {
                            $('#cannot_delete_message').html(results.message);
                            $('#contact_cannot_delete_contact').modal();
                        }
                    }).error(function(){

                    });
                }
            }
            else {}
        })
        .error(function() {});
});

// "If guardian is selected make primary option YES automatically."
var currentContactRoles = [];
$(document).on('change', '.contact_role_id ', function()
{
	update_edit_contact_form();
});

function get_family_autocomplete()
{
    if (document.getElementById('contact_family'))
    {
        $('#contact_family').autocomplete(
            {
                source: '/admin/contacts3/ajax_get_all_families_ui/',
                open: function () {

                },
                select: function (event, ui) {
                    $('#contact_family_id').val(ui.item.id);
                    $('#contact_is_primary_no').click();
                    $('#contact_family_primary_contact_id').val(ui.item.primary_contact_id);
                    if ($('#contact_family_primary_contact_id').val() !== '')
                    {
                        $('.contact_role_id option[value="2"]').show();
                    }
                }
            });
    }
}

$(document).on('keypress','#contact_family', function()
{

});

$(document).on('keypress', '.enforce_ucfirst', function(ev)
{
	var input = this;
	var $toggle = $('.enforce_ucfirst_toggle[data-input="'+input.id+'"]');

	// If there is a toggle checkbox and it is unchecked, do not run this function
	if ($toggle.length == 0 || $toggle.is(':checked'))
	{
		// Timer, so that highlighted text gets a chance to be replaced before the next character is added
		setTimeout(function()
		{
			input.value = input.value.toLowerCase();
			// Capitalise the first letter in each word
			input.value = input.value.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1);});
			// Capitalise the letter after "Mc" or "O'". e.g. "McMahon" and "O'Mahony"
			input.value = input.value.replace(/(Mc|O')([a-z])/g, function(txt, $1, $2){return $1+$2.toUpperCase();});
		}, 500);
	}
});
// When the capitalisation checkbox is switched on, apply the uc enforcement to the currently entered text
$(document).on('change', '.enforce_ucfirst_toggle', function()
{
	if ($(this).is(':checked'))
	{
		$('#'+this.getAttribute('data-input')).trigger('keypress');
	}
});

var contact_role_1_prev = [];

function contact_role_id_1_onchange(selected_roles)
{
    var prev_child = false;
    var new_child = false;
    var new_non_child = false;
    for (var i = 0 ; i < contact_role_1_prev.length ; ++i) {
        if (contact_role_1_prev[i] == 2) {
            prev_child = true;
        }
    }
    guardian_selected = false;
    selected_roles.each(function ()
    {
        if ($(this).val() == 1)
        {
            guardian_selected = true ;
        }
        if ($(this).val() == 2)
        {
            new_child = true ;
        }

        if ($(this).val() != 2)
        {
            new_non_child = true ;
        }
    });

    contact_role_1_prev = [];
    selected_roles.each(function ()
    {
        contact_role_1_prev.push($(this).val());
    });

    if (prev_child && new_non_child) {
        $("#contact_role_id_1")[0].options[1].selected = false;
        $("#contact_role_id_1").multiselect('rebuild');
    }
    if (prev_child == false && new_child == true) {
        $("#contact_role_id_1")[0].options[0].selected = false;
        $("#contact_role_id_1")[0].options[2].selected = false;
        $("#contact_role_id_1").multiselect('rebuild');
    }
}

$('#contact_role_id_1').multiselect({onChange:contact_role_id_1_onchange});

$(document).on('change', '#contact_role_id_1', function(){
    $('#contact_role_id_1').data("modified", true);
});

$(document).on('click', '#contact_role_changed_confirm', function(){
    $('#contact_role_id_1').data("modified", false);
    $(lastClickedSaveButton).click();
});

$('#contact_role_id_2').multiselect({
    buttonText: function(selected_roles)
    {
        staff_roles = [];
        if (selected_roles.length == 0)
        {
            return 'None selected ';
        }
        else
        {
            var labels = [] ;
            selected_roles.each(function (n) {
                staff_roles[n] = $(this).val();
                if ($(this).attr('label') !== undefined) {
                    labels.push($(this).attr('label'));
                }
                else {
                    labels.push($(this).html());
                }
            });
            console.log(staff_roles);
            return labels.join(', ') + ' ';
        }
    }
});

function teachers_multiselect()
{

    $('#teacher_subject_teaching_preferences').multiselect({
        buttonText: function (subject_options) {
            subject = [];
            selected_subjects = 0;
            var selected = 0;
            if (subject_options.length == 0)
            {
                teacher_preferences();
                return 'None selected ';
            }
            else
            {
                subject_options.each(function (n)
                {
                    selected_subjects += 1;
                    subject[n] = $(this).val();
                });
                teacher_preferences();
                return selected_subjects + ' Selected  ';
            }
        },
        enableCaseInsensitiveFiltering: true,
        onInitialized: function(select, container) {
            $(container).find('[class*="glyphicon-"]').each(function() {
                $(this).attr('class', $(this).attr('class').replace('glyphicon-', 'icon-'));
            });
        }
    });

    $('#teacher_course_type_teaching_preferences').multiselect(
        {
            buttonWidth: '150px',
            buttonText: function (options)
            {
                categories = [];
                selected_categories = 0;
                var selected = 0;
                if (options.length == 0)
                {
                    teacher_preferences();
                    return 'None selected ';
                }
                else
                {
                    options.each(function (n)
                    {
                        selected_categories += 1;
                        categories[n] = $(this).val();
                    });
                    teacher_preferences();
                    return selected_categories + ' Selected  ';
                }
            }
        });

}

function teacher_preferences()
{
    $("#teacher_course_subject_teaching_preferences").multiselect(
        {
            buttonText: function (courses_options)
            {
                courses = [];
                selected_courses = 0;
                if (courses_options.length == 0) {
                    return 'None selected ';
                }
                else {
                    courses_options.each(function (n) {
                        selected_courses += 1;
                        courses[n] = $(this).val();
                    });
                }
                return selected_courses + ' Selected  ';
            }
        });

    if(selected_subjects <= 0 || selected_categories <= 0)
    {
        $('#courses_subject_preferences_section').disableAndHide(true);
        $("#teacher_course_subject_teaching_preferences").empty();
    }
    else
    {
        $('#courses_subject_preferences_section').disableAndHide(false);
        var data = {categories: JSON.stringify(categories), subjects: JSON.stringify(subject),courses:JSON.stringify(courses)};
        $.post('/admin/courses/get_course_by_selected_categories_subjects', data, function (results) {
            //$("#contact_course_subject_teaching_preferences").multiselect('destroy');
            $("#teacher_course_subject_teaching_preferences").empty();
            $("#teacher_course_subject_teaching_preferences").append(results);
            $("#teacher_course_subject_teaching_preferences").multiselect('rebuild');
            $('#courses_subject_preferences_section').show();
        });
    }
}

function set_linked_organisation_autocomplete()
{
    $("#linked_organisation").autocomplete({
        select: function(e, ui) {
            $('#linked_organisation').val(ui.item.label);
            $('#linked_organisation_id').val(ui.item.value);
            return false;
        },

        source: function(data, callback){
            data.type = "Organisation";
            $.get("/admin/contacts3/autocomplete_contacts",
                data,
                function(response){
                    callback(response);
                }
            );
        }
    });

    $("#timeoff_hours .timepicker").datetimepicker({
        datepicker : false,
        format: 'H:00',
        formatTime: 'H:00',
    });
}

function set_linked_department_autocomplete()
{
    $("#linked_department").autocomplete({
        select: function(e, ui) {
            $('#linked_department').val(ui.item.label);
            $('#linked_department_id').val(ui.item.value);
            return false;
        },

        source: function(data, callback){
            data.contact_type = "Department";
            if($('#linked_organisation_id').val()){
                data.linked_organisation_id = $('#linked_organisation_id').val();
            }
            $.get("/admin/contacts3/find_contact",
                data,
                function(response){
                    callback(response);
                }
            );
        }
    });
}

function set_primary_biller_autocomplete() {
    $("#primary_biller").autocomplete({
        select: function (e, ui) {
            $('#primary_biller').val(ui.item.label);
            $('#primary_biller_id').val(ui.item.id);
            return false;
        },

    source: function (data, callback) {
        $.getJSON(
            "/admin/contacts3/find_contact", {
                term: data.term,
                linked_organisation_id: $('#contact_id').val()
            },
            callback
        );
    },
});
}


$(document).on('change', '#domain_name', function(){
    var domain_name = $(this).val();
    var domain_name_pattern          = /^(\w+\.\w+)$/i;
    if (!domain_name_pattern.test(domain_name)) {
        $('.alert_area').add_alert(
            'Domain name has incorrect format',
            'danger popup_box');
        return false;
    }
    var organisation_id = $('#contact_id').val();
    get_external_organisations(organisation_id);
});

$(document).on('click', '#find_external_accounts', function(e){
    e.preventDefault();
    var email = $('#contact_email').val();
    var domain_name = $('#domain_name').val();
    if (domain_name == undefined || domain_name == '' || domain_name == null) {
        get_and_fill_domain_name(email);
    }
    var organisation_id = $('#contact_id').val();
    get_external_organisations(organisation_id);
});

$(document).on('change', '#external_api_account', function(e){
    var organisation_id = $('#external_api_account').val();
    get_external_organisation(organisation_id);
});

function get_external_organisation(organisation_id){
    if (organisation_id === null
        || organisation_id === undefined
        || organisation_id === ''
        || organisation_id === 0) {
        toggle_organisation_data(false);
        return;
    }
    $.ajax({
        type     : 'POST',
        url      : '/admin/contacts3/ajax_get_external_organisations',
        data     : {
            external_api_organisation_id: organisation_id,
            include_public: true
        },
        dataType : 'json'
    }).success(function(results) {
        if (results) {
            var organisation_data = results[0];
            if (organisation_data !== undefined) {
                $('#contact2_billing_address\\[address1\\]').val(organisation_data.address1);
                $('#contact2_billing_address\\[address2\\]').val(organisation_data.address2);
                $('#contact2_billing_address\\[address3\\]').val(organisation_data.address3);
                $('#contact2_billing_address\\[postcode\\]').val(organisation_data.postcode);
                $('#contact2_billing_address\\[town\\]').val(organisation_data.city);
                $('#contact2_country').val(organisation_data.country).trigger('change');
                $('#contact2_county').val(organisation_data.county).trigger('change');
                if (organisation_data.is_member) {
                    $('#special_member_yes').prop("checked", true);
                    $('#special_member_yes').closest('.btn-plain').addClass('active');
                    $('#special_member_no').closest('.btn-plain').removeClass('active');
                } else {
                    $('#special_member_no').prop("checked", true);
                    $('#special_member_no').closest('.btn-plain').addClass('active');
                    $('#special_member_yes').closest('.btn-plain').removeClass('active');
                }
                toggle_organisation_data(true);
            }
        } else {
            toggle_organisation_data(false);
        }
    });
}

function toggle_organisation_data(is_external, keep_membership = false) {
    if (is_external) {
        $('#contact2_billing_address\\[address1\\]').attr('readonly', true);
        $('#contact2_billing_address\\[address2\\]').attr('readonly', true);
        $('#contact2_billing_address\\[address3\\]').attr('readonly', true);
        $('#contact2_billing_address\\[postcode\\]').attr('readonly', true);
        $('#contact2_billing_address\\[town\\]').attr('readonly', true);
        $('#contact2_country').attr('readonly', true);
        $('#contact2_country-input').attr('readonly', true);
        $('#contact2_county').attr('readonly', true);
        $('#contact2_county-input').attr('readonly', true);
    } else {
        $('#contact2_billing_address\\[address1\\]').removeAttr('readonly');
        $('#contact2_billing_address\\[address2\\]').removeAttr('readonly');
        $('#contact2_billing_address\\[address3\\]').removeAttr('readonly');
        $('#contact2_billing_address\\[postcode\\]').removeAttr('readonly');
        $('#contact2_billing_address\\[town\\]').removeAttr('readonly');
        $('#contact2_country').removeAttr('readonly');
        $('#contact2_county').removeAttr('readonly');
        $('#contact2_country-input').removeAttr('readonly');
        $('#contact2_county-input').removeAttr('readonly');
        if (!keep_membership) {
            $('#special_member_no').prop("checked", true);
            $('#special_member_no').closest('.btn-plain').addClass('active');
            $('#special_member_yes').closest('.btn-plain').removeClass('active');
        }

    }
}
function get_external_organisations(organisation_id) {
    if ($("#external_api_account") == undefined) {
        toggle_organisation_data(false, true);
        return;
    }
    var remote_ids = [];
    $("#external_api_account").find('option').remove();
    $.ajax({
        type     : 'POST',
        url      : '/admin/contacts3/ajax_get_external_organisations',
        data     : {
            domain_name: $('#domain_name').val(),
            organisation_id: organisation_id
        },
        dataType : 'json'
    }).success(function(results) {
        var option = '<option value="">Please select...</option>';
        $("#external_api_account").append(option);
        $.each(results, function(key, result){
            option = '<option value="' + result.id + '">' + result.name + '</option>';
            remote_ids.push(result.id);
            $("#external_api_account").append(option);
        });
        $.ajax({
            type     : 'POST',
            url      : '/admin/contacts3/ajax_get_linked_organisation',
            data     : {
                contact_id: $('#contact_id').val()
            },
            dataType : 'json'
        }).success(function(results) {
            if (results !== null && results.remote_id !== undefined) {
                if ($.inArray(results.remote_id, remote_ids) !== -1) {
                    $("#external_api_account").val(results.remote_id);
                    toggle_organisation_data(true);
                } else {
                    $("#external_api_account").val("");
                    toggle_organisation_data(false);
                }
            } else {
                $("#external_api_account").val("");
                toggle_organisation_data(false);
            }
        });
    });
}



$(document).on('change', '#contact_email' , function(){
    var email = $('#contact_email').val();
    get_and_fill_domain_name(email);
});

function get_and_fill_domain_name(email) {
    var ind = email.indexOf("@");
    domain_name = email.substr(ind+1);
    $('#domain_name').val(domain_name);
    $("#domain_name").trigger("change");
}
function related_to_autocomplete() {
    var autocomplete_input_id = "#related_to";
    var input = $(autocomplete_input_id)[0];
    $(autocomplete_input_id).autocomplete({
        source: function (data, callback) {
            $.getJSON(
                "/admin/todos/ajax_autocomplete_regarding", {
                    term: $(input).val(),
                    regarding_id: $("#related_to_id").val()
                },
                callback
            );
        },
        open: function () {

        },
        select: function (event, ui) {
            $("#related_to_value").val(ui.item.id);
        }
    });
}

$(document).on("click", "#contact-email-invite", function() {
    var invite_email = $('#contact-email').val();
    var contact_id = $("#contact_id").val();

    $.ajax({
        url: '/admin/contacts3/ajax_invite_contact_popup_details',
        data: {
            contact_id: $('#contact_id').val(),
        },
        type: "POST",
        success: function (response) {
            if (response.reply == 'valid') {
                $("#contact_email_invite_modal").modal();
                var invite_user_modal = $('#contact_email_invite_modal');
                var contact_type_name = $('#contact_type option:selected').text();
                let role_name;

                // Pre-select the role corresponding to the contact type
                $('#user_group_role_id option').each(function () {
                    role_name = $(this).text();
                    if (role_name == contact_type_name || (role_name == 'Teacher' && contact_type_name == 'Trainer')) {
                        invite_user_modal.find('#user_group_role_id').val(invite_user_modal.find('#user_group_role_id').find('option:contains("' + $(this).text() + '")').val()).change();
                        return true;
                    }
                });
                $("#contact-emails").val(response.contact_email).prop("readonly", true);
                $("#contact_email_invite_modal").find('#message').ckeditor_email().val(response.message);
            }
            // Display message
            for (var i = 0; i < response.popup_messages.length; i++) {
                $('.alert_area').add_alert(
                    response.popup_messages[i].message,
                    (response.popup_messages[i].success ? 'success' : 'danger') + ' popup_box');
            }
        },
        error: function (x) {

        }
    })
    return false;
});

$(document).on("click", "#contact-invite-user-submit", function() {
    var invite_user_modal = $('#contact_email_invite_modal');
    $.ajax({
        url: '/admin/usermanagement/ajax_invite_user',
        data: {
            emails: invite_user_modal.find('#contact-emails').first().val(),
            role_id: invite_user_modal.find('#user_group_role_id').first().val(),
            message: invite_user_modal.find('#message').first().val(),
            send: '1',
            action: 'save',
        },
        type: "POST",
        success: function(response) {
            $('#contact_email_invite_modal').modal('hide');
            $("#contact-email-invite").addClass('hidden');
            for (var i = 0; i < response.popup_messages.length; i++) {
                $('.alert_area').add_alert(
                    response.popup_messages[i].message,
                    (response.popup_messages[i].success ? 'success' : 'danger') + ' popup_box');
            }
        },
        error: function(response) {
            for (var i = 0; i < response.popup_messages.length; i++) {
                $('.alert_area').add_alert(
                    response.popup_messages[i].message,
                    (response.popup_messages[i].success ? 'success' : 'danger') + ' popup_box');
            }
        }
    });
});