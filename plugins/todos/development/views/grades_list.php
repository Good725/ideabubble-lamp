<?= (isset($alert)) ? $alert : '' ?>
<?php
if(isset($alert)){
    ?>
    <script>
        remove_popbox();
    </script>
    <?php
}
?>

<div>
<form id="grades-form" name="grades-form" method="post">
    <table>
        <thead>
            <tr>
                <th><?=__('Grade')?></th>
                <th><?=__('% Min.')?></th>
                <th><?=__('% Max.')?></th>
                <th><?=__('Points(H)')?></th>
                <th><?=__('Points(O)')?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($grades as $grade_index => $grade) { ?>
            <tr>
                <td>
                    <input type="hidden" name="grade[<?=$grade_index?>][id]" class="grade_id" value="<?=$grade['id']?>" />
                    <input type="text" name="grade[<?=$grade_index?>][grade]" class="grade" value="<?=$grade['grade']?>" />
                </td>
                <td>
                    <input type="text" name="grade[<?=$grade_index?>][percent_min]" class="percent_min" value="<?=$grade['percent_min']?>" />
                </td>
                <td>
                    <input type="text" name="grade[<?=$grade_index?>][percent_max]" class="percent_max" value="<?=$grade['percent_max']?>" />
                </td>
                <td>
                    <input type="text" name="grade[<?=$grade_index?>][points_h]" class="points_h" value="<?=$grade['points_h']?>" />
                </td>
                <td>
                    <input type="text" name="grade[<?=$grade_index?>][points_o]" class="points_o" value="<?=$grade['points_o']?>" />
                </td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
            <tr><th colspan="5"><button type="submit" name="save" class="btn btn=primary" value="save"><?=__('Save')?></button> </th> </tr>
        </tfoot>
    </table>
</form>
</div>
