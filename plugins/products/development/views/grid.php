<?php
$x_size = count($x);
?>

<table class="datatable table" id="matrix_table">
    <thead>
        <th></th>
        <?php
        foreach($x AS $value):
            ?>
            <th><?=$value['label'];?></th>
        <?php
        endforeach;
        ?>
    </thead>
    <tbody>
    <?php
    foreach($y AS $key=>$item):
    ?>
        <tr>
            <td><b><?=$item['label'];?></b></td>
            <?php
            foreach($x AS $value):
            ?>
                <td data-option_2_id="<?=$item['id'];?>" data-option_1_id="<?=$value['id'];?>" class="matrix_item">
                    <span class="icon-remove"
                          data-option_2_id="<?=$value['id'];?>" data-option_2_label="<?= $value['label'] ?>"
                          data-option_1_id="<?=$item['id'];?>" data-option_1_label="<?= $item['label'] ?>"
                          data-price_adjustment="0" data-price="0" data-image=""></span><div class="display_price"></div>
                    <input type="hidden" class="secondary_matrix" value=""/>
                    <a href="#" class="add_edit_option" data-option_2_id="<?=$value['id'];?>" data-option_1_id="<?=$item['id'];?>">add</a>
                </td>
            <?php
            endforeach;
            ?>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>