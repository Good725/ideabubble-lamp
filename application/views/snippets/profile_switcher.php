<div class="form-group vertically_center">
    <div class="col-xs-12 col-sm-6">
        <?php
        echo View::factory('snippets/btn_dropdown')
            ->set('btn_type',      'success btn-lg')
            ->set('fullwidth',     true)
            ->set('group_classes', 'profile-section-selector')
            ->set('title',          [
                    'html' => true,
                    'text' => $title.'<span class="arrow_caret-down"></span>'
                ]
            )
            ->set('options',        $options)
        ?>
    </div>

    <?php if ($section == 'contact'): ?>
        <div class="hidden-xs col-sm-6">
            <div class="right">
                <div class="badge text-uppercase"><?= $role->role ?></div>
            </div>
        </div>
    <?php endif; ?>
</div>