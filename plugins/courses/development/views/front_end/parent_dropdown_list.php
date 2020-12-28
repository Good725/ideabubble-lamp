<?php foreach($parents as $item => $val):?>
    <option value='<?=$val['id']?>' <?=(@$location && $location == $val['id'])?'selected="selected"':''?> class='option-parent'><?=$val['name']?></option>
    <?php if (is_array($childs) AND count($childs) > 0):?>
        <?php foreach ($childs as $child => $vch):?>
            <?php if ($vch['parent_id'] == $val['id']): ?>
            <option value='<?=$vch['id']?>' <?=(@$location && $location == $vch['id'])?'selected="selected"':''?> class='option-child'><?=$vch['name']?></option>
                <?php endif;?>
            <?php endforeach;?>
        <?php endif;?>
<?php endforeach;?>