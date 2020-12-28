<div class="col-sm-12">
    <form method="post">

        <table>
            <thead>
                <tr><th>Role</th><th> </th></tr>
            </thead>
            <tbody>
            <?php foreach ($groups as $group) { ?>
                <tr>
                    <td><?=$group['role']?></td>
                    <td><input type="checkbox" name="group_id[]" value="<?=$group['id']?>" checked="checked" /></td>
                </tr>
            <?php }?>
            </tbody>
            <tfoot>
                <tr><th colspan="2"><button type="submit" name="export" value="export">Export Permissions for selected roles</button> </th> </tr>
            </tfoot>
        </table>
    </form>
</div>