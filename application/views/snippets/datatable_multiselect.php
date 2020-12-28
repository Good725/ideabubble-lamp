<div class="dropdown datatable-multiselect" id="datatable-multiselect-template" data-autodismiss="false">
    <button class="btn-dropdown btn-link" type="button" data-toggle="dropdown">
        <span class="datatable-multiselect-label"></span>
        <span class="caret"></span>
    </button>

    <div class="dropdown-menu collapse" role="menu">
        <div class="datatable-multiselect-search-wrapper hidden">
            <?= Form::ib_input(
                null,
                '',
                '',
                array('class' => 'datatable-multiselect-search', 'placeholder' => __('Search')),
                array('icon'  => '<span class="icon-search"></span>')
            ) ?>

            <div class="datatable-multiselect-found hidden">
                <p class="singular_text hidden"><?= __('1 record found') ?></p>
                <p class="plural_text hidden"><?= __('X records found', array('X' => '<span class="datatable-multiselect-found-number"></span>')) ?></p>
            </div>

        </div>

        <?php // Hidden template option. The HTML from this section is cloned to populate the actual list. ?>
        <ul class="datatable-multiselect-options-template hidden">
            <li class="datatable-multiselect-li">
               <?= Form::ib_checkbox(
                    '<span class="datatable-multiselect-li-label"></span>',
                    '',
                    '',
                    false,
                    array('class' => 'datatable-multiselect-checkbox')
                ) ?>
            </li>
        </ul>

        <?php // Actual options go here ?>
        <ul class="datatable-multiselect-options"></ul>

        <div class="text-center">
            <button type="button" class="btn-link datatable-multiselect-clear_all"><?=  __('clear all') ?></button> |
            <button type="button" class="btn-link datatable-multiselect-select_all"><?= __('select all') ?></button>
        </div>
    </div>
</div>
