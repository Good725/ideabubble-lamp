<div class="well parameter form-inline clearfix">
    <div class="form-group">
		<?php $parameter_type = $parameter->get_type();?>
		
        <input type="hidden" value="parameter_id_<?=$parameter->get_id();?>"/>
        <select class="form-control parameter_picker">
            <option value="text"      <?= ($parameter->get_type() == 'text'      ? 'selected' : '') ?>>Text</option>
            <option value="date"      <?= ($parameter->get_type() == 'date'      ? 'selected' : '') ?>>Date</option>
            <option value="sql"       <?= ($parameter->get_type() == 'sql'       ? 'selected' : '') ?>>SQL</option>
            <option value="dropdown"  <?= ($parameter->get_type() == 'dropdown'  ? 'selected' : '') ?>>Dropdown ( split by ; )</option>
            <option value="custom"    <?= ($parameter->get_type() == 'custom'    ? 'selected' : '') ?>>Custom</option>
            <option value="month"     <?= ($parameter->get_type() == 'month'     ? 'selected' : '') ?>>Month</option>
			<option value="user_role" <?= ($parameter->get_type() == 'user_role' ? 'selected' : '') ?>>User Role</option>
            <option value="user_id" <?= ($parameter->get_type() == 'user_id' ? 'selected' : '') ?>>User Id</option>
        </select>
        <input type="text" class="form-control" value="<?= $parameter->get_name() ?>" />
		
        <?=Model_Reports::get_sql_parameter_options($parameter->get_value(), $parameter_type != 'sql' ? 'style="display:none"' : '');?>

        <?php
        $date_format = Settings::instance()->get('date_format');
        if (!$date_format) {
            $date_format = 'd-m-Y';
        }

        ?>
        <input type="text" class="form-control value_input datepicker input_date" value="<?=date($date_format, (strtotime($parameter->get_value()) ? strtotime($parameter->get_value()) : time()));?>" style="<?=!in_array($parameter_type, array('date', 'dropdown')) ? 'display:none' : ''?>"/>
		
        <input type="text" class="form-control value_input input_text" value="<?=$parameter->get_value();?>" style="<?=$parameter_type != 'text' ? 'display:none' : ''?>"/>
		
        <? if ($parameter->get_type() != "month") { ?>
        <textarea rel="popover" data-original-title="Custom" title="Will be selected only first field." data-content="Will be selected only first field." class="input_textarea form-control popinit" style="<?=$parameter_type != 'custom' ? 'display:none' : ''?>"><?php
                echo preg_replace('#^\((.*)\)$#', '$1', $parameter->get_value());
            ?></textarea>
        <? } ?>
		
        <? if ($parameter->get_type() == "month") { ?>
        <input type="hidden" value="<?=$parameter->get_id();?>" name="month_id" />
        <? } ?>
		
		<span>
			<label>Is Multi Select</label>
			<input type="checkbox" class="is_multiselect" value="1" <?=$parameter->get_is_multiselect() ? 'checked="checked"' : ''?> />
		</span>

        <?php if ($parameter->get_type() == 'date') { ?>
        <br />
        <span class="always_today">
			<label>Always Display Today</label>
			<input type="checkbox" class="always_today" value="1" <?=$parameter->get_type() == 'date' && $parameter->get_value() == '' ? 'checked="checked"' : ''?> />
		</span>
        <?php } ?>

        <br />
        <span class="delete">X</span>
    </div>
</div>