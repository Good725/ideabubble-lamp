
<div>
    <form method="post">
    <table class="table">
        <thead>
            <tr>
                <th>Trigger</th>
                <th>Enabled</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($triggers as $trigger) {
            $checked = in_array($trigger->name, $enabled_triggers);
        ?>
            <tr>
                <td><?=$trigger->name?></td>
                <td><input type="checkbox" name="trigger[]" value="<?=$trigger->name?>" <?=$checked ? 'checked="checked"' : ''?>/> </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">
                    <button type="submit" name="action" value="save">Save</button>
                </th>
            </tr>
        </tfoot>
    </table>
    </form>
</div>