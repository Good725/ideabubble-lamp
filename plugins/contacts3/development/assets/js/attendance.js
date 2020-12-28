var today = new Date();
var dd = today.getDate();
var mm = today.getMonth()+1; //January is 0!
var yyyy = today.getFullYear();
if(dd < 10){
    dd = '0' + dd;
}
if(mm < 10){
    mm = '0' + mm;
}
today = yyyy + '-' + mm + '-' + dd ;

$(document).on('ready', function(){
    load_family_members_block();
    load_attendance_block($('#attendance_wrapper').data('my_id'), today);

    $('body').on('click', '.bulk-update-button', function(){
        load_bulk_update_popup($(this).data('contact_id'));
    });

    $('body').on('click', '.cancel-bulk-update', function(){
        $('#bulk_update_popup').modal('hide');
    });

    $('body').on('click', '.bulk-update', function(){
        load_bulk_update_classes($('#bulk_update_popup').data('contact_id'));
    });

    $(document).on('change', '#family_block .family-member-checkbox', function()
    {
        var contact_id = $(this).data('contact_id');

        if (this.checked) {
            load_attendance_block(contact_id, today);
        } else {
            $('#attendance_wrapper').find('#attendance_block_' + contact_id).remove();
        }
    });

    $(document).on('click', '.booking-days li > a', function(ev) {
        ev.preventDefault();

        var $block     = $(this).parents('[id*="attendance_block"]');
        var $note_form = $block.find('.attendance-note-form--day');
        var date       = $(this).data('date');
        var contact_id = $(this).data('contact_id');
        var statuses   = {};

        $block.find('.attendance-filter input[name="attendance_filter[]"]').each(function() {
            if ($(this).is(':checked')) {
                statuses[$(this).data('status')] = $(this).val();
            }
        });

        load_day_classes(contact_id, date, $block, this, statuses);

        // Reset and hide note forms
        $block.find('.attendance-note-form').addClass('hidden');
        $block.find('.attendance-note-form').find('[name="attending"]').val(1);
        $block.find('.attendance-note-form').find('[name="note"]').val('');

        // Show the relevant note form and give it the date
        $note_form.data('date', date).removeClass('hidden');
    });


    $(document).on('click', '.attendance-day-item > a', function()
    {
        var $block     = $(this).parents('[id*="attendance_block"]');
        var $form      = $block.find('.attendance-note-form--class');
        var id         = $(this).data('id');

        $(this).parents('.attendance-day-item').addClass('selected');

        // Reset and hide note forms
        $block.find('.attendance-note-form').addClass('hidden');
        $block.find('.attendance-note-form').find('[name="attending"]').val(1);
        $block.find('.attendance-note-form').find('[name="note"]').val('');

        // Show the relevant note form and give it the class ID
        $form.data('id', id).removeClass('hidden');
    });

    $(document).on('click', '.attendance-note-form-confirm', function() {
        var $form      = $(this).parents('.attendance-note-form');
        var $attending = $form.find('[name="attending"]');
        var $note      = $form.find('[name="note"]');
        var ids        = [];

        if ($form.hasClass('attendance-note-form--class')) {
            ids.push($form.data('id'));
        }
        else if ($form.hasClass('attendance-note-form--day')) {
            $(this).parents('[id*="attendance_block"]').find('.attendance-day-item > a').each(function() {
                ids.push($(this).data('id'));
            });
        }

        save_attendance(ids, $attending.val(), $note.val());

        $form.addClass('hidden');
        $attending.val(1);
        $note.val('');
    });

    $('body').on('click', '.attendance-filter li', function(){
        var statuses = {};
        $(this).parents('.attendance-filter').find('input[name="attendance_filter[]"]').each(function() {
            if($(this).is(':checked')) {
                statuses[$(this).data('status')] = $(this).val();
            }
        });
        load_attendance_block($(this).parents('.attendance-filter').data('contact_id'), $(this).parents('.attendance-filter').data('date'), statuses);
    });

    $('.screenCell .basic_close').click(function(){
        $(".sectioninner").removeClass("zoomIn");
        $('.sectionOverlay').css('display','none');
        return false;
    });

    $('.popup-btn').click(function(){
        $(".sectioninner").addClass("zoomIn");
        $('#popup').css('display','block');
        return false;
    });

    $('body').on('click', '#confirm_bulk_update', function(ev){
        if ( ! confirm('Are you sure you want update ' + $(this).data('count') + ' classes?'))
		{
			ev.preventDefault();
		}
    });

    $('body').on('click', '#add_note_popup-submit', function(){
        $('#add_note_popup').hide();
        save_attendance(
            $('#add_note_popup').data('id'),
            $('#add_note_popup .is_attending').val(),
            $('#add_note_popup .note').val()
        );
        $('#add_note_popup .note').val('');
    });
});

function load_bulk_update_popup(contact_id, callback){
    $.ajax({
        url: '/frontend/contacts3/ajax_load_bulk_update_popup/',
        type: 'post'
    }).done(function(data) {
        $('#bulk_update_popup').html('').html(data);

        $('.attendance_datepicker_from').datetimepicker({
            format: 'Y-m-d',
            timepicker: false,
            onShow:function( ct ){
                this.setOptions({
                    maxDate:$('.attendance_datepicker_to').val() ? $('.attendance_datepicker_to').val() : false
                })
            },
            onSelectDate:function(dp){
                load_bulk_update_classes(contact_id)
            }
        });
        $('.attendance_datepicker_to').datetimepicker({
                format: 'Y-m-d',
                timepicker: false,
            onShow:function( ct ){
                this.setOptions({
                    minDate:$('.attendance_datepicker_from').val()?$('.attendance_datepicker_from').val():false
                })
            },
            onSelectDate:function(dp){
                load_bulk_update_classes(contact_id)
            }
        });

        $('#bulk_update_popup').data('contact_id', contact_id).modal();
        if (callback) {
            callback();
        }
    });
}

function load_bulk_update_classes(contact_id){
    $.ajax({
        url: '/frontend/contacts3/ajax_load_bulk_update_classes/?contact_id=' + contact_id + '&' + $('#bulk_update_form').serialize(),
        type: 'post'
    }).done(function(data) {
        $('.bulk-update-classes').html('').html(data);
    });
}

function save_attendance(id, is_attending, note){
    $.ajax({
        url: '/admin/contacts3/ajax_save_attendance/',
        data: {
            id: id,
            is_attending: is_attending,
            note: note
        },
        type: 'post'
    }).done(function(data) {
        $('.booking-days .selected a').click();
    });
}

function save_note(id, note){
    var ids = id.isArray() ? id : {0: id};

    $.ajax({
        url: '/frontend/contacts3/timetables_save_note/',
        data: {
            booking_item_ids: ids,
            note: note,
            action: 'update'
        },
        type: 'post'
    });
}

function load_family_members_block()
{
    var $family_block = $('#family_block');
    if ($family_block.length) {
        $.ajax({url: '/frontend/contacts3/ajax_get_family_members/'}).done(function(data) {
            $family_block.html(data);
            $family_block.find('.family-member-checkbox').trigger('change');
        });
    }
}

function load_attendance_block(contact_id, date, filters){
    $.ajax({
        url: '/admin/contacts3/ajax_get_contact_attendance_block/',
        data: {
            filters: filters,
            contact_id: contact_id,
            date: date
        },
        type: 'post'
    }).done(function(data) {
        if ($('#attendance_block_' + contact_id).length) {
            $('#attendance_block_' + contact_id).html(data);
        } else {
            $('#attendance_wrapper').append($('<div>').attr('id', 'attendance_block_' + contact_id).html(data).attr('class', 'form-row'));
        }

        if ($('#attendance_block_' + contact_id).find('.swiper-container').length) {
            var $contact_block = $('#contact_attendance_block_' + contact_id);
            var $active_slide = $contact_block.find('.booking-days .swiper-slide.selected');

            $active_slide = $active_slide.length ? $active_slide : $contact_block.find('.booking-days .swiper-slide:first');

            var days_slider = new Swiper('#attendance_block_' + contact_id+' .booking-days.swiper-container', {
                slidesPerView : 3,
                watchSlidesVisibility: true,
                navigation    : {
                    prevEl        : '#attendance_block_' + contact_id+' .booking-days .timeline-swiper-prev',
                    nextEl        : '#attendance_block_' + contact_id+' .booking-days .timeline-swiper-next'
                },
                spaceBetween  : 0
            });

            days_slider.slideTo($active_slide.index());

            $active_slide.find('a').click();
        }

    });
}

function load_day_classes(contact_id, date, target, element, filters){
    $.ajax({
        url: '/admin/contacts3/ajax_get_day_classes/',
        data: {
            contact_id: contact_id,
            date: date,
            filters: filters
        },
        type: 'post'
    }).done(function(data) {
        $('#attendance_block_' + contact_id+' .booking-days .swiper-slide.selected').removeClass('selected');
        $(element).parents('.swiper-slide').addClass('selected');

        $(target).find('.booking-classes').html(data);

        var number_of_classes = $(data).find('.attendance-day-item').length;

        // Show relevant text depending on whether there is "1 class" or "X classes"
        $(target).find('.attendance-note-form--day').find('.singular, .plural').addClass('hidden');
        if (number_of_classes == 1) {
            $(target).find('.attendance-note-form--day .singular').removeClass('hidden');
        } else {
            $(target).find('.attendance-note-class_count').html(number_of_classes);
            $(target).find('.attendance-note-form--day .plural').removeClass('hidden');
        }

        $('.attendance-day-item [data-toggle="popover"]').popover({'container': 'body'});

        sliderwrapper_set_size($('.booking-days ul'));
        sliderwrapper_set_size($('.booking-classes ul'));

        var classes_slider = new Swiper('#attendance_block_' + contact_id+' .booking-classes.swiper-container', {
            slidesPerView : 3,
            watchSlidesVisibility: true,
            navigation    : {
                prevEl        : '#attendance_block_' + contact_id+' .booking-classes .timeline-swiper-prev',
                nextEl        : '#attendance_block_' + contact_id+' .booking-classes .timeline-swiper-next'
            },
            spaceBetween  : 0
        });

    });
}

function sliderwrapper_set_size($ul)
{
    $ul.each(function() {
        var width = 0;
        var $ul    = $(this);
        $ul.find('>li').each(function() {
            width += $(this).width();
        });

        if (!$ul.find('.edit-attendance-menu')) {
            $ul.css('width', width + 'px');
            $ul.css('position', 'absolute');
            $ul.css('left', '0px');
        }
    });
}