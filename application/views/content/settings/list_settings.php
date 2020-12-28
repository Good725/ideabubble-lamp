<div class="header">
    <?php
    if (isset($alert)) {
        echo $alert;
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
</div>


<form class="form-horizontal" method="post">
    <?php if (is_array($forms)): ?>
        <?php foreach ($forms as $group => $fieldsets): ?>
            <?php
            if ($display_group != '' && $display_group != $group) {
                continue;
            }
            ?>
            <a id="<?= $group ?>"></a>
            <fieldset>
                <legend><?= $group ?></legend>

                <?php if (!empty($is_microsite)): ?>
                <abbr
                    title="Check this box to set a value for this setting for this microsite only. If unchecked, the selected value will be shared by all sites using this database."
                    class="popinit" rel="popover" data-trigger="hover">Overwrite</abbr>
                <?php endif; ?>

                <?php
                if (is_array($fieldsets)) {
                    foreach ($fieldsets as $fieldset) {
                        echo $fieldset;
                    }
                }
                ?>
            </fieldset>
        <?php endforeach ?>
    <?php endif ?>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit">Save</button>
        <button class="btn-cancel">Cancel</button>
    </div>
</form>

