<?= isset($alert) ? $alert : '' ?>
<div id="contact_id" data-contact_id="<?= $select_contact_id ? $select_contact_id : $contact->get_id() ?>"></div>

<div class="profile_page row gutters" style="margin-bottom: 2em;">
    <div class="<?= ($contact->get_is_primary()) ? 'col-sm-6 col-lg-8' : 'col-sm-12' ?>">
        <h1 id="edit-profile-title"></h1>
    </div>

    <?php if ($contact->get_is_primary()): ?>
        <div class="col-sm-3 col-lg-2 text-right text-nowrap">
            <button type="button" class="btn-link new-profile" data-family_id="<?= $contact->get_family_id() ?>" data-type="student">
                <span class="flaticon-plus-circle"></span> Add Student
            </button>
        </div>

        <div class="col-sm-3 col-lg-2 text-right text-nowrap">
            <button type="button" class="btn-link new-profile" data-family_id="<?= $contact->get_family_id() ?>" data-type="guardian">
                <span class="flaticon-plus-circle"></span> Add Guardian
            </button>
        </div>
    <?php endif; ?>

    <div class="col-sm-3 col-lg-2 text-right text-nowrap">
        <button type="button" class="btn-link invite-member">
            <span class="flaticon-plus-circle"></span> Invite Member
        </button>
    </div>
</div>

<?php if ($contact->get_is_primary() || $can_view_other_contacts): ?>
    <div
        class="btn-group btn-group-lg btn-group-pills contacts"
        id="profile-select_contact"
        data-family_id="<?= $contact->get_family_id() ?>"
        data-primary_contact_id="<?= $family->get_primary_contact_id() ?>"
        >
        <?php foreach ($family_members as $member): ?>
            <button
                type="button"
                class="btn btn-default"
                data-contact_id="<?= $member['id'] ?>"
                data-is_primary="<?= $member['is_primary'] ?>"><?= trim($member['first_name'].' '.$member['last_name']) ?></button>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="tabs_content">

</div>

<?php if (@$error == 'duplicate_login_email') { ?>
    <script>
        $(document).on("ready", function(){
            $('#user_duplicate_popup').show();
        })
    </script>
    <div id="user_duplicate_popup" class="sectionOverlay" style="display:none;">
        <div class="overlayer"></div>
        <div class="screenTable">
            <div class="screenCell">
                <div class="sectioninner" style="width: 40%">
                    <div class="popup-header">
                        <span class="popup-title">Unable to create login</span>
                        <a class="basic_close popup_close"><i class="fa fa-times" aria-hidden="true"></i></a>
                    </div>

                    <div class="popup-content">
                        <form>
                            <div class="colm" style="text-align: center">
                                Email has already been used by another contact.<br/>
                                Please, change email address.
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<style>
    [id="edit_profile_form"] h3 {
        margin: 45px 0 10px;
    }

    [id="edit_profile_form"] > h3:first-child {
        margin-top: 10px;
    }

    [id="edit_profile_form"] .form-row {
        margin-top: 10px;
    }
</style>

<?php if (@$add_student == 'yes') { ?>
<script>
    $(document).on("ready", function(){
        $("a.new-profile[data-type='student']").click();
    });
    window.redirect_after_save = <?=json_encode($redirect)?>;
</script>
<?php } ?>


<div id="invite_member_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Invite Member</h3>
            </div>
            <form id="invite-member-form" method="post">
            <div class="modal-body">

                    <div class="form-row gutters">
                        <div class="col-sm-12">
                            <?php
                            echo Form::ib_input(__('Email'), 'invite_email', '', array('class' => 'validate[required,custom[email]]'));
                            ?>
                        </div>
                    </div>


            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Cancel</a>
                <button class="btn btn-success" type="submit">Invite</button>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
$(document).on("ready", function(){
    $("button.invite-member").on("click", function(){
        $('#invite_member_modal').modal();
    });

    $("#invite-member-form").on("submit", function(){
        if ($('#invite-member-form').validationEngine('validate')){
            var invite_email = $('#invite-member-form [name=invite_email]').val();

            $.ajax(
                {
                    url: "/frontend/contacts3/invite_member",
                    data: {
                        email: invite_email
                    },
                    type: "POST",
                    success: function(response) {
                        $('#invite-member-form [name=invite_email]').val("");
                        $('#invite_member_modal').modal('hide');
                        console.log("s");
                    },
                    error: function(x) {
                        console.log("e");
                    }
                }
            );
        }
        return false;
    });
});
</script>