<?= (isset($alert)) ? $alert : '' ?>
<?php
if (isset($alert)) {
    ?>
    <script>
        remove_popbox();
    </script>
    <?php
}
?>
<div id="list_contact_generic_type_wrapper">
    <table id="list_contact_generic_type_table" class="table dataTable dataTable-collapse">
        <thead>
        <tr>
            <?php foreach ($table_columns as $table_column) : ?>
                <?= "<th scope='col'>{$table_column['label']}</th>" ?>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div id="family_menu_wrapper" class="family_menu_wrapper"></div>
</div>
