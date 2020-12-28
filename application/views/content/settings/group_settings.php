<?php if (is_array($forms)): ?>
    <? foreach ($forms as $group => $fieldsets): ?>
        <a id="<?php echo $group; ?>"></a>
        <fieldset>
            <legend><?php echo $group; ?></legend>
            <?php if (is_array($fieldsets)): ?>
                <? foreach ($fieldsets as $fieldset): ?>

                    <?php echo $fieldset; ?>

                <? endforeach ?>
            <? endif ?>

        </fieldset>

    <? endforeach ?>
<? endif ?>