<link rel="stylesheet" type="text/css" href="<?= URL::get_engine_assets_base() ?>/css/swiper.min.css" />

<div class="form-group" id="family_block"></div>
    <script>
        $(document).on('click', '.edit-attendance-menu', function(){
            $(this).addClass("hidden");
        });

        $(document).on('click', '#view_note_popup .popup_close', function(){
            $("#view_note_popup .note-txt").html("");
            $('#view_note_popup').css('display','none');
        });

        $(document).on('click', '.edit-attendance-one', function(){
            var a = $(this).parents(".attendance-day-item").find(">a");
            if(!$.isNumeric($(a).data('id'))) {
                return false;
            }

            //invert attendance value before save
            var is_attending = $(a).data('attending') ? 0 : 1;
            var id = $(a).data('id');
            $('#add_note_popup').data('id', id);
            $('#add_note_popup .is_attending').val(is_attending);
            $('#add_note_popup .note').val($(a).prop('title'));
            $('#add_note_popup').show();
            $(".edit-attendance-menu").addClass("hidden");
            return false;
        });

        $(document).on('click', '.edit-attendance-until', function(){
            var a = $(this).parents(".attendance-day-item").find(">a");
            if(!$.isNumeric($(a).data('id'))) {
                return false;
            }

            //invert attendance value before save
            var is_attending = $(a).data('attending') ? 1 : 0;
            var contact_id = $(a).data('contact_id');
            load_bulk_update_popup(contact_id, function(){
                $("#bulk_update_form .days-in-week").addClass("hidden");
                $("#bulk_update_form [name=date_from]").val($(a).data("date"));
                if (is_attending == 1) {
                    $(".bulk-attending-selector.no").click();
                    $(".bulk-attending-selector.no").prop("checked", true);
                } else {
                    $(".bulk-attending-selector.yes").click();
                    $(".bulk-attending-selector.yes").prop("checked", true);
                }
            });

            $(".edit-attendance-menu").addClass("hidden");
            return false;
        });

        $(document).on('click', '.edit-attendance-weekly', function(){
            var a = $(this).parents(".attendance-day-item").find(">a");
            if(!$.isNumeric($(a).data('id'))) {
                return false;
            }

            //invert attendance value before save
            var is_attending = $(a).data('attending') ? 1 : 0;
            var contact_id = $(a).data('contact_id');
            load_bulk_update_popup(contact_id, function(){
                $("#bulk_update_form [name=date_from]").val($(a).data("date"));
                if (is_attending == 1) {
                    $(".bulk-attending-selector.no").click();
                    $(".bulk-attending-selector.no").prop("checked", true);
                } else {
                    $(".bulk-attending-selector.yes").click();
                    $(".bulk-attending-selector.yes").prop("checked", true);
                }
            });

            $(".edit-attendance-menu").addClass("hidden");
            return false;
        });

        $(document).on('click', '#attendance-auth-send', function(){
            $.post(
                '/admin/contacts3/send_attendance_parent_auth_code',
                {

                },
                function (response) {
                    $("#attendance_auth_id").val(response.id);
                    $("#attendance-auth-send-message").removeClass("hidden");
                    $("#attendance-auth-send").addClass("hidden");
                }
            )
        });

        $(document).on('click', '#attendance-auth-confirm', function(){
            $.post(
                '/admin/contacts3/attendance_auth_confirm',
                {
                    auth_id: $("#attendance_auth_id").val(),
                    code: $("#attendance_edit_auth_code").val()
                },
                function (response) {
                    if (response.id) {
                        window.location.reload();
                    } else {
                        $("#attendance_auth_failed_modal").modal();
                    }
                }
            )
        });
    </script>


<div class="clearfix"></div>
<div class="form-horizontal" id="attendance_wrapper" data-my_id="<?= $contact->get_id() ?>"></div>

<?php if (Auth::instance()->has_access('contacts3_limited_family_access')): ?>
    <div id="bulk_update_popup" class="modal fade">

    </div>
<?php endif; ?>

<div id="attendance_auth_failed_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Failed</h3>
            </div>
            <div class="modal-body form-horizontal">
                <div class="alert-area"></div>
                <h3 style="color:#F00" ><?=__('Authorization failed!')?></h3>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal"
                   data-content="Dismiss">Dismiss</a>
            </div>
        </div>
    </div>
</div>

<?php include_once 'snippets/view_note_popup_calender.php'; ?>


<script type="text/javascript" src="<?= URL::get_engine_assets_base() ?>js/swiper.min.js"></script>