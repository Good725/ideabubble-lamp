<form method="post">
<table id="auto_feature_criterias" class="table">
    <thead>
        <tr><th>Manufacturer</th><th>Distributor</th><th>Min Price</th><th>Max Price</th><th>Number Of Products</th><th><button type="button" id="addc">Add</button></th></tr>
    </thead>
    <tbody>
    <?php
    foreach ($aflist as $autofeature) {
    ?>
        <tr>
            <td>
                <select name="manufacturer_id[]">
                    <option value="">   </option>
                    <?php
                    echo html::optionsFromArray($manufacturers, $autofeature['manufacturer_id'])
                    ?>
                </select>
            </td>
            <td>
                <select name="distributor_id[]">
                    <option value="">   </option>
                    <?php
                    echo html::optionsFromArray($distributors, $autofeature['distributor_id'])
                    ?>
                </select>
            </td>
            <td><input type="text" name="min_price[]" value="<?=$autofeature['min_price']?>" /></td>
            <td><input type="text" name="max_price[]" value="<?=$autofeature['max_price']?>" /></td>
            <td><input type="text" name="numbers[]" value="<?=$autofeature['numbers']?>" /></td>
            <td><button onclick="$(this).parent().parent().remove();" type="button">Remove</button></td>
        </tr>
    <?php
    }
    ?>
    </tbody>
    <tfoot>
        <tr><th colspan="5"><button type="submit" name="save">Save</button></th> </tr>
    </tfoot>
</table>
<script>
    $(document).ready(function() {
        $("#addc").on("click", function () {
            $("#auto_feature_criterias tbody").append(
                "<tr>" +
                    '<td><select name="manufacturer_id[]"><option value="">   </option><?php
                    echo html::optionsFromArray($manufacturers, null)
                     ?></select></td>' +
                    '<td><select name="distributor_id[]"><option value="">   </option><?php
                     echo html::optionsFromArray($distributors, null)
                     ?></select></td>' +
                    '<td><input type="text" name="min_price[]" /></td>' +
                    '<td><input type="text" name="max_price[]" /></td>' +
                    '<td><input type="text" name="numbers[]" /></td>' +
                    '<td><button onclick="$(this).parent().parent().remove();" type="button">Remove</button></td>' +
                "</tr>"
            )
        });
    });
</script>
</form>