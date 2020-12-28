<?php if (isset($alert)): ?>
    <?= $alert ?>
    <script>remove_popbox();</script>
<?php endif; ?>

<div class="edit_profile_wrapper">
    <form class="form-horizontal" id="edit-profile-form" action="/admin/profile/save?section=cards" method="post">
        <?= $section_switcher ?>

        <input type="hidden" name="contact_id" value="<?=$contact_id;?>" />

        <section>
            <div class="form-group">
                <div class="col-sm-6">
                    <h2><?=__('Saved Cards')?></h2>
                </div>
            </div>

            <table class="table">
                <thead><tr><th>Card</th><th>Expires</th><th>Select</th></tr></thead>
                <tbody>
                    <?php foreach ($cards as $card) { ?>
                    <tr>
                        <td>**** **** *** <?=$card['last_4']?></td>
                        <td><?=$card['exp_month'] != '' ? $card['exp_month'] . '-' . $card['exp_year'] : ''?></td>
                        <td><input type="checkbox" name="id[]" value="<?=$card['id']?>" /> </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

        <div class="form-action-group" id="ActionMenu">
            <button type="submit" name="redirect" class="btn btn-primary profile_save_btn" data-redirect="save" value="save"><?=__('Delete selected cards')?></button>
            <button type="submit" name="redirect" class="btn btn-primary profile_save_btn" data-redirect="save_and_exit" value="save_and_exit"><?=__('Delete selected cards & Exit')?></button>
            <button type="reset" class="btn btn-default"><?=__('Reset')?></button>
            <a href="/admin" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>