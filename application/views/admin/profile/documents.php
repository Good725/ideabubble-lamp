<?php if (isset($alert)): ?>
    <?= $alert ?>
    <script>remove_popbox();</script>
<?php endif; ?>
<div class="edit_profile_wrapper">
    <form class="form-horizontal" id="edit-profile-form" action="/admin/profile/save?section=documents" method="post">
        <section>
            <h3> <?= __('Documents') ?></h3>
                <?php if($has_certificate_template):?>
                    <?php if(!empty($doc_array)):?>
                        <?=$doc_array?>
                    <?php else:?>
                        No completed courses

                    <?php endif?>
                    <?php else:?>
                        No completed courses

                <?php endif?>
        </section>
    </form>
</div>
