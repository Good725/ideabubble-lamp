$(document).on('click', '[href="#family-documents-tab"]', function(){
    $('#family_header_buttons').empty().load('/admin/documents/ajax_load_doc_actions?level=family');
    $('.modal_boxes').html('');
    loadDocuments('family');
});

$(document).on('click', '[href="#family-member-documents-tab"]', function(){
    $('#header_buttons').empty().load('/admin/documents/ajax_load_doc_actions?level=contact');
    $('.modal_boxes').html('');
    loadDocuments('member');
});

$(document).on('click', '.upload_document', function(ev)
{
    ev.preventDefault();
    $.ajax({
        url:'/admin/documents/ajax_get_upload_locations/?ajax=1',
        type:'GET',
        dataType:'html'
    }).done(function (results)
    {
        if ( ! document.getElementById('upload_document_modal'))
        {
            $('body').append(results);
        }
        $('#upload_document_modal').modal();
    });
});

// Generate a document
$(document).on('click', '.generate_documents', function(ev)
{
    ev.preventDefault();
    $('#generate_documents_contact_id').html($('#contact_id').val());
    $('#generate_documents_modal').modal();
    disable_blocks();
});


// Save a document from the generate modal box.
// Reload the list of the contact's documents afterwards
$(document).on('click', '#generate_save', function()
{
    var data = get_document_data();
    if ( typeof data !== 'undefined')
    {
        data.direct_download = 0;
        generate_document(data);
    }
});

$(document).on('click', '#generate_save_download', function()
{
    var data = get_document_data();
    if ( typeof data !== 'undefined')
    {
        var query = $.param(data);
        window.location = '/admin/documents/ajax_generate_kes_document?direct_download=1&'+query;
    }
});

$(document).on('change','#doc_template', function()
{
    disable_blocks();
});


$.fn.disableAndHide = function(action)
{
    var $this = $(this);
    (action) ? $this.hide() : $this.show();
    ($this.is('fieldset, :input')) ? $this.prop('disabled', action) : '';
    $this.find('fieldset, :input').prop('disabled', action);
    if ( ! action) $this.find('.disabled').removeClass('disabled');
};


$(document).on('click','#upload_document_modal_btn', function(ev)
{
    ev.preventDefault();

    // Perform validation
    if (document.getElementById('upload_document_modal_file').value)// && document.getElementById('upload_document_modal_import_to_directory').value)
    {
        // Get the data, including the files
        var data = new FormData();
        var file = document.getElementById('upload_document_modal_file').files[0];
        data.append('file', file);
        var contact_id = (document.getElementById('contact_id') != undefined) ?
            document.getElementById('contact_id').value : $('.selected-booking-contact').attr('data-contact_id');
        data.append('contact_id', contact_id);
        //data.append('import_to_directory', document.getElementById('upload_document_modal_import_to_directory').value);

        // Send to the server
        $.ajax({
            url         :'/admin/documents/ajax_upload_document',
            type        : 'POST',
            data        : data,
            contentType : false,
            processData : false
        })
            .done(function()
            {
                // Reload the tab with the new document and success message
                $('[href="#family-member-documents-tab"]').click();
                if (document.getElementById('family-member-documents-tab') != undefined)
                    document.getElementById('family-member-documents-tab').scrollIntoView();
                $('#family-member-documents-tab').find('.alert-area').add_alert('Document successfully uploaded', 'success popup_box');
                remove_popbox();
                $('#upload_document_modal').modal('hide');

            })
            .fail(function()
            {
                // Reload the tab with a failure notice
                $('[href="#family-member-documents-tab"]').click();
                document.getElementById('family-member-documents-tab').scrollIntoView();
                $('#family-member-documents-tab').find('.alert-area').add_alert('Failed to upload document', 'error popup_box');
                remove_popbox();
                $('#upload_document_modal').modal('hide');
            });
    }
    else
    {
        $('#upload_document_modal').find('.alert-area').add_alert('Please choose both an upload directory and a file', 'error popup_box');
        remove_popbox();
    }
});

//$(document).on('click','#generate_documents_modal_btn', function()
//{
//    $.ajax({
//        type: 'POST',
//        url: '/admin/documents/ajax_generate_document',
//        data: $('#form-generate-doc').serialize(),
//        dataType: 'json'
//    });
//});

/**
 *
 * FUNCTIONS
 */

function loadDocuments(table)
{
    var params,
        tab,
        tableId,
        header;
    if (table == 'family')
    {
        params = 'family_id=' + document.getElementById('family_id').value;
        tab = $('#family-documents-tab');
        tableId = 'family_documents_table';
        header = 'edit_family_heading';
    }
    else
    {
        params = 'contact_id=' + document.getElementById('contact_id').value;
        tab = $('#family-member-documents-tab');
        tableId = 'family_member_documents_table';
        header = 'edit_family_member_heading';
    }

    // load data table
    tab.find('.content-area').load('/admin/documents/ajax_get_kes_documents?' + params, function()
    {
        $(this).find('.dataTable').attr('id', tableId).dataTable({"aaSorting": []});
    });
}

// Generate documents function
function load_generate() {
    // Takes the ID of the contact from the 'Contact #' label
    var contact_id = $('#contact_id').val();

    var parameter1 = 'contact_id=' + contact_id[1];

    popup('open', '800px');
    $.ajax({
        url:'/admin/documents/ajax_get_generate_documents/?ajax=1&' + parameter1,
        type:'GET',
        dataType:'html'
    }).done(function (data) {
        $('#new_js').html(data);
    });
}

function generate_document(data)
{
    $.ajax({
        type : 'POST',
        url: '/admin/documents/ajax_generate_kes_document/',
        data: data,
        dataType: 'json'
    }).done(function(result)
    {
        // Display the result in a message box
        var $alert_area = $('#family-member-documents-tab').find('.alert-area');
        $alert_area.add_alert(result, 'success popup_box');
        remove_popbox();

        // Reopen (and refresh) the documents tab and ensure it appears in the screen
        $('[href="#family-member-documents-tab"]').click();
    }).fail(function(result)
	{
		$('[href="#family-member-documents-tab"]').click();
	});
}

/**
 * Retrieve the data required to generate a Document From a Booking or transaction Or contact
 * @returns {{contact_id: (Node.value|*), document_name: (Node.value|*)}}
 */
function get_document_data()
{
    var data = {
        contact_id: document.getElementById('contact_id').value,
        document_name: document.getElementById('doc_template').value
    };
    var alerts = $('#generate_documents_modal .alert-area');
    if (data.document_name == '') {
        alerts.add_alert('<div class="alert">Please Select a Document Template</div>');
    }
    else {
        var valid = true;
        switch (data.document_name.toLowerCase()) {
            case 'certificate_of_attendance':
                data.booking_id = $('#document-templates-param-all_booking').val();
                if (data.booking_id == '') {
                    valid = false;
                    alerts.add_alert('<div class="alert">Please select a booking</div>');
                }
                break;

            case 'payment_reminder':
                data.transaction_id =  $('#outstanding_transaction option:selected').val();
                if (data.transaction_id == '') {
                    valid = false;
                    alerts.add_alert('<div class="alert">Please Select an Outstanding Transaction</div>');
                }
                break;
            case 'teacher_booking_confirmation':
                data.booking_id = $('#payg_transaction option:selected').val();
                if (data.transaction_id == '') {
                    valid = false;
                    alerts.add_alert('<div class="alert">Please Select a PAYG Booking</div>');
                }
                break;
            case 'teacher_booking_cancellation':
                data.booking_id = $('#cancel_payg_transaction option:selected').val();
                if (data.transaction_id == '') {
                    valid = false;
                    alerts.add_alert('<div class="alert">Please Select a cancelled PAYG Booking</div>');
                }
                break;
            case 'booking-receipt':
            case 'payment_receipt':
                data.payment_id = $('#payment_made option:selected').val();
                if (data.payment_id == '') {
                    valid = false;
                    alerts.add_alert('<div class="alert">Please Select a Payment</div>');
                }
                break;
            case 'booking_confirmation':
            case 'booking_alteration':
                data.booking_id = $('#confirmed_booking option:selected').val();
                if (data.booking_id == '') {
                    valid = false;
                    alerts.add_alert('<div class="alert">Please Select a Confirmed Booking</div>');
                }
                break;
            case 'booking':
                data.booking_id = $('#confirmed_booking option:selected').val();
                if (data.booking_id == '') {
                    valid = false;
                    alerts.add_alert('<div class="alert">Please Select a Confirmed Booking</div>');
                }
                break;
            case 'booking_cancellation':
                data.transaction_id = $('#cancelled_booking').val();
                if (data.transaction_id == '') {
                    valid = false;
                    alerts.add_alert('<div class="alert">Please Select a cancelled Booking</div>');
                }
                break;
            case 'student_provisional_letter':
                data.academic_year_id = $('#document-template-param-academic_year').val();
                data.todo_categories = $('.document-template-param-todo_categories').val();
                if (data.academic_year_id == '') {
                    valid = false;
                    alerts.add_alert('<div class="alert">Please select an academic year</div>');
                }
                if (data.todo_categories == null) {
                    valid = false;
                    alerts.add_alert('<div class="alert">Please select at least one category</div>');
                }
                data.card_type = 'student_provisional_letter';
                break;
            case 'student_report_card':
                data.academic_year_id = $('#document-template-param-academic_year').val();
                data.todo_categories = $('.document-template-param-todo_categories').val();
                if (data.academic_year_id == '') {
                    valid = false;
                    alerts.add_alert('<div class="alert">Please select an academic year</div>');
                }
                if (data.todo_categories == null) {
                    valid = false;
                    alerts.add_alert('<div class="alert">Please select at least one category</div>');
                }
                data.card_type = 'student_report_card';
                break;
                case 'tutor_meeting':
                data.academic_year_id = $('#document-template-param-academic_year').val();
                data.exam_id = $('#document-template-param-exam').val();
                data.tutor_id = $('#document-template-param-tutor').val();

                if (data.academic_year_id == '') {
                    valid = false;
                    alerts.add_alert('<div class="alert">Please Select an academic year</div>');
                }
                if (data.exam_id == '') {
                    valid = false;
                    alerts.add_alert('<div class="alert">Please Select an exam</div>');
                }
                break;
            case 'course_brochure':
                    data.course_id = $('#course_id').val();
                    if (data.course_id == '') {
                        valid = false;
                        alerts.add_alert('<div class="alert">Please Select a course</div>');
                    }
                break;

        }

        if (valid) {
            return data;
        }
    }
}

function disable_blocks()
{
    var valueSelected        = $('#doc_template').val();

    var all_bookings         = $('#document-templates-param-all_bookings-wrapper');
    var booking              = $('#modal_confirmed_bookings');
    var cancellation         = $('#modal_cancelled_bookings');
    var exam                 = $('#document-templates-param-exam-wrapper');
    var outstanding          = $('#outstanding_transactions');
    var payment_receipt      = $('#modal_payments_made');
    var report_card          = $('#document-templates-param-report_card_wrapper');
    var teacher_cancel       = $('#cancel_payg_transactions');
    var teacher_confirmation = $('#payg_transactions');
    var tutor                = $('#document-templates-param-tutor-wrapper');
    var courses              = $('#courses-list');

    all_bookings.disableAndHide(true);
    booking.disableAndHide(true);
    cancellation.disableAndHide(true);
    exam.disableAndHide(true);
    outstanding.disableAndHide(true);
    payment_receipt.disableAndHide(true);
    report_card.disableAndHide(true);
    teacher_cancel.disableAndHide(true);
    teacher_confirmation.disableAndHide(true);
    tutor.disableAndHide(true);
    courses.disableAndHide(true);

    switch (valueSelected) {
        case 'certificate_of_attendance':
            all_bookings.disableAndHide(false);
            break;
        case 'course_brochure':
            courses.disableAndHide(false);
            break;

        case 'Payment_Reminder' :
            outstanding.disableAndHide(false);
            break;

        case 'Teacher_Booking_Confirmation' :
            teacher_confirmation.disableAndHide(false);
            break;

        case 'Teacher_Booking_Cancellation' :
            teacher_cancel.disableAndHide(false);
            break;

        case 'Payment_Receipt' :
            payment_receipt.disableAndHide(false);
            break;

        case 'Booking' :
        case 'Booking_Alteration':
        case 'Booking_Confirmation' :
            booking.disableAndHide(false);
            break;

        case 'Booking_Cancellation' :
            cancellation.disableAndHide(false);
            break;
        case 'student_provisional_letter':
        case 'student_report_card' :
            report_card.disableAndHide(false);
            $('.document-template-param-todo_categories').multiselect();
            $('#document-template-param-academic_year').on('change', function() {
                $.get('/admin/todos/ajax_get_todo_categories', {'academic_year_id' : $('#document-template-param-academic_year').val()}, function (todo_categories, status)
                {
                    $('.document-template-param-todo_categories').html('');
                    $.each(todo_categories, function (category_id, category_title) {
                        $('.document-template-param-todo_categories').append($('<option>', {
                            value: category_id,
                            text : category_title
                        }));
                    });
                    $('.document-template-param-todo_categories').multiselect('rebuild');
                });
            });
            break;

        case 'tutor_meeting':
            report_card.disableAndHide(false);
            $('.document-template-param-todo_categories-wrapper').disableAndHide(true);
            exam.disableAndHide(false);
            tutor.disableAndHide(false);
            break;
    }
}
