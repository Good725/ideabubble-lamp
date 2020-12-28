<?php 
$family_permission = Auth::instance()->has_access('family_tab'); 
 
if (!$family_permission) { 
	Request::current()->redirect('/admin/profile'); 
} 
 
?> 
<div class="edit_profile_wrapper">
    <section class="form-horizontal">
        <?= $section_switcher ?>

        <div class="btn-group btn-group-lg btn-group-pills contacts">
            <?php foreach ($family_members3 as $member) { ?>
                <a
                    <?php if ($contact3->has_role('guardian')) { ?>
                    href="/admin/profile/edit?contact_id=<?=$member['id']?>&section=contact"
                    <?php } else { ?>
                    style="cursor: default"
                    <?php } ?>
                    class="btn btn-default"
                    data-contact_id="<?= $member['id'] ?>"
                    data-is_primary="<?= $member['is_primary'] ?>"
                >
                    <span>
                        <?= trim($member['first_name'].' '.$member['last_name']) ?>
                        <br class="hidden-xs" /><span class="hidden-sm">&ndash;</span>
                        <?= $contact_role ?>
                    </span>
                </a>
            <?php } ?>
        </div>

        <p><?= __('Invite family members') ?></p>

        <form id="invite-member-form" method="post">
            <input type="hidden" name="contact_id" value="<?= $contact_id ?>" />
            <div class="form-group">
                <div class="col-xs-9 col-sm-5">
                    <?= Form::ib_input(__('Add email'), 'invite_email', null, array('id' => 'invite_email')); ?>
                </div>

                <div class="col-xs-3">
                    <button class="btn btn-primary form-btn btn--full invite-member" type="submit"><?= __('Add') ?></button>
                </div>
            </div>
        </form>
    </section>
</div>

<div id="member_invited_modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Invitation has been sent</h3>
            </div>

            <div class="modal-body">
                <p>Invitation has been sent</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" data-content="">Okay</button>
            </div>
        </div>
    </div>
</div>