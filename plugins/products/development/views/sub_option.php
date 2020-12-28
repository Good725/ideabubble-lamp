<table class="table">
    <thead>
        <th></th>
        <th></th>
    </thead>
    <tbody>
        <?php
        foreach($options AS $key=>$option):
        ?>
        <tr>
            <td><?=$option['label'];?></td>
            <td data-option_1_id="<?=$option1;?>" data-option_2_id="<?=$option['id'];?>">
                <span class="icon-remove" data-price_adjustment="0" data-price_adjustment="" data-price=""></span>
                <input type="hidden" class="secondary_matrix"/>
                <a href="#" class="add_edit_option" data-option_2_id="" data-option_1_id="">add</a></td>
        </tr>
        <?php
        endforeach;
        ?>
    </tbody>
</table>