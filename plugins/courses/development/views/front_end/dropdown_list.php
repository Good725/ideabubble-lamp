<?php foreach($items as $item => $val):?>
    <option value='<?=$val['id']?>' <?=(@$selected && $selected == $val['id'])?'selected="selected"':''?>><?=$val['name']?></option>
<?php endforeach;?>