<?= isset($alert) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<script>
window.periods = <?=json_encode($periods);?>;
window.ratecard_data = <?=json_encode($ratecard);?>;
if (!window.ratecard_data) {
    window.ratecard_data = {};
    window.ratecard_data.dateRanges = [];
    window.ratecard_data.calendar = [];
}
window.propman_minstay_high = <?=json_encode(Settings::instance()->get('propman_minstay_high'));?>;
window.propman_arrival_high = <?=json_encode(Settings::instance()->get('propman_arrival_high'));?>;
window.propman_minstay_low = <?=json_encode(Settings::instance()->get('propman_minstay_low'));?>;
window.propman_arrival_low = <?=json_encode(Settings::instance()->get('propman_arrival_low'));?>;
</script>
<form id="ratecard-form-serialized" action="/admin/propman/edit_rate_card/<?=@$ratecard['id']?>" method="post">
    <input type="hidden" name="data" value="" />
    <input type="hidden" name="serialized" value="www" />
    <input type="hidden" name="action" value="save" />
</form>
<form name="ratecard-edit" action="/admin/propman/edit_rate_card/<?=@$ratecard['id']?>" method="post">
    <input type="hidden" name="id" value="<?=@$ratecard['id']?>" />

    <div class="form-group clearfix">
        <label class="sr-only" for="edit-ratecard-title"><?= __('Enter rate card title') ?></label>
        <div class="col-sm-10">
            <input type="text" class="form-control required" id="edit-ratecard-title" name="name" placeholder="<?= __('Enter rate card title') ?>" value="<?=htmlspecialchars(@$ratecard['name'])?>" required="required"/>
        </div>
        <div class="col-sm-2">
            <label>
                <span class="sr-only"><?= __('Publish') ?></span>
                <input type="hidden" name="published" value="0" /><?php // If the checkbox is unticked, this value will get sent to the server  ?>
                <input type="checkbox" name="published" value="1" <?=@$ratecard['published'] ? 'checked="checked"' : ''?> data-toggle="toggle" data-onstyle="success" data-on="<?= __('Published') ?>" data-off="<?= __('Unpublished') ?>" />
            </label>
        </div>
    </div>

    <div class="col-sm-12">

        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#edit-ratecard-tab-details" aria-controls="edit-ratecard-tab-details" role="tab" data-toggle="tab"><?= __('Details') ?></a></li>
            <li role="presentation"><a href="#edit-ratecard-tab-calendar" aria-controls="edit-ratecard-tab-calendar" role="tab" data-toggle="tab"><?= __('Calendar') ?></a></li>
            <li role="presentation"><a href="#edit-ratecard-tab-date-ranges"   aria-controls="edit-ratecard-tab-date-ranges"   role="tab" data-toggle="tab"><?= __('Rates')   ?></a></li>
            <li role="presentation"><a href="#edit-ratecard-tab-groups"  aria-controls="edit-ratecard-tab-groups"  role="tab" data-toggle="tab"><?= __('Groups')   ?></a></li>
        </ul>

        <div class="tab-content">
            <!-- Details tab -->
            <div role="tabpanel" class="tab-pane active" id="edit-ratecard-tab-details">
                <div class="form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="edit-ratecard-period_id"><?= __('Period') ?></label>
                        <div class="col-sm-5">
                            <select class="form-control required" id="edit-ratecard-period_id" name="period_id">
                                <option value=""><?= __('-- Please select --') ?></option>
                                <?php
                                foreach ($periods as $period) {
                                    ?>
                                    <option value="<?=$period['id']?>"
                                        <?=
                                        @$ratecard['period_id'] == $period['id'] ? 'selected="selected"' : ''
                                        ?>
                                            data-starts="<?=$period['starts']?>"
                                            data-ends="<?=$period['ends']?>"><?=
                                        $period['name'] . ' (' . $period['starts'] . ' -> ' . $period['ends'] . ')'
                                        ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="edit-ratecard-property_type_id"><?= __('Property Type') ?></label>
                        <div class="col-sm-5">
                            <select class="form-control" id="edit-ratecard-property_type_id" name="property_type_id">
                                <option value=""><?= __('-- Please select --') ?></option>
                                <?php
                                echo HTML::optionsFromRows('id', 'name', $propertyTypes, @$ratecard['property_type_id']);
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar tab -->
            <div role="tabpanel" class="tab-pane" id="edit-ratecard-tab-calendar">
                <div class="col-sm-12 form-horizontal">

                    <div class="col-sm-12">
                        <span class="ib-calendar-key ib-calendar-key-available"></span> <?= __('No Rates Set') ?>
                        <span class="ib-calendar-key ib-calendar-key-unavailable"></span> <?= __('Rates Set') ?>
                    </div>

                    <div class="col-sm-12 ib-calendar" id="edit-ratecard-calendar">
                        <input type="hidden" name="calendar" value="<?=isset($ratecard['calendar']) ? HTML::chars(json_encode($ratecard['calendar'])) : ''?>" />
                        <?php
                        foreach ($periods as $pi => $period) {

                            ?>
                            <div class="ib-calendar-period" id="ib-calendar-period-<?=$period['id']?>" style="display: <?=@$ratecard['period_id'] == $period['id'] ? '' : 'none' ?>;">
                                <?php
                                foreach ($period['calendar']['months'] as $pmonth) {
                                    $year = $pmonth['year'];
                                    $month = $pmonth['month'];
                                    $mfirst = $pmonth['start'];
                                    $mlast = $pmonth['end'];
                                    $skipfirst = date('w', mktime(0, 0, 0, $month, 1, $year));
                                    $skiplast = 7 - date('w', mktime(0, 0, 0, $month, $mlast, $year));

                                    ?>
                                    <div class="ib-calendar-month">
                                        <div class="ib-calendar-month-header"><?= date('F Y',
                                                mktime(0, 0, 0, $month, 1, $year)) ?></div>
                                        <div class="ib-calendar-day-headers">
                                            <span class="ib-calendar-day-header">Sun</span>
                                            <span class="ib-calendar-day-header">Mon</span>
                                            <span class="ib-calendar-day-header">Tue</span>
                                            <span class="ib-calendar-day-header">Wed</span>
                                            <span class="ib-calendar-day-header">Thu</span>
                                            <span class="ib-calendar-day-header">Fri</span>
                                            <span class="ib-calendar-day-header">Sat</span>
                                        </div>

                                        <?php
                                        $mday = 1;
                                        for ($week = 1 ; $week <= 6 ; ++$week) {
                                            ?>
                                            <div class="ib-calendar-week">
                                                <?php
                                                for ($wday = 0 ; $wday < 7 ; ++$wday) {
                                                    if (($week == 1 && $wday < $skipfirst) || ($mday > $mlast) || ($mday < $mfirst)) {
                                                        ?>
                                                        <span class="ib-calendar-day"></span>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <span tabindex="0" class="ib-calendar-day" data-date="<?=$year .'-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($mday, 2, '0', STR_PAD_LEFT)?>"><?=$mday?></span>
                                                        <?php
                                                        ++$mday;
                                                    }
                                                    ?>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                </div>
            </div>

            <!-- Rates tab -->
            <div role="tabpanel" class="tab-pane" id="edit-ratecard-tab-date-ranges">
                <div class="col-sm-12" style="margin-bottom: 1em;">
                    <div class="pull-right">
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#bulk-change-rates-modal" id="bulk-change-rates-modal-button"><?= __('Bulk change rates') ?></button>
                    </div>
                </div>

                <div class="col-sm-12">
                    <table class="table table-striped dataTable ratecard-date-ranges-table" id="ratecard-date-ranges-table">
                        <thead>
                        <tr>
                            <th scope="col"><?= __('Deal') ?></th>
                            <th scope="col"><?= __('Start') ?></th>
                            <th scope="col"><?= __('Finish') ?></th>
                            <th scope="col"><?= __('Weekly') ?></th>
                            <th scope="col"><?= __('Short Stay') ?></th>
                            <th scope="col"><?= __('Additional') ?></th>
                            <th scope="col"><?= __('Min. stay') ?></th>
                            <th scope="col"><?= __('Price type') ?></th>
                            <th scope="col"><?= __('Weekly discount') ?></th>
                            <th scope="col"><?= __('Arrival') ?></th>
                            <th scope="col"><?= __('Actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (@$ratecard['dateranges']) {
                            foreach ($ratecard['dateranges'] as $daterange) {
                                ?>
                                <tr>
                                    <td><input type="checkbox" name="ratecard_week[<?=$daterange['period_id']?>][<?=$daterange['starts']?>][is_deal]" value="1" <?=$daterange['is_deal'] ? 'checked="checked"' : ''?> /></td>
                                    <td><?=$daterange['starts']?></td>
                                    <td><input type="text" name="ratecard_week[<?=$daterange['period_id']?>][<?=$daterange['starts']?>][weekly_price]" value="<?=$daterange['weekly_price']?>" size="4" /></td>
                                    <td><input type="text" name="ratecard_week[<?=$daterange['period_id']?>][<?=$daterange['starts']?>][midweek_price]" value="<?=$daterange['midweek_price']?>" size="4" /></td>
                                    <td><input type="text" name="ratecard_week[<?=$daterange['period_id']?>][<?=$daterange['starts']?>][weekend_price]" value="<?=$daterange['weekend_price']?>" size="4" /></td>
                                    <td><input type="text" class="ratecard_dt_min_stay" name="ratecard_week[<?=$daterange['period_id']?>][<?=$daterange['starts']?>][min_stay]" value="<?=$daterange['min_stay']?>" size="4" /></td>
                                    <td><select class="ratecard_dt_pricing" name="ratecard_week[<?=$daterange['period_id']?>][<?=$week['starts']?>][pricing]">
                                            <option value="Low" <?=$week['pricing'] =='Low' ? 'selected="selected"' : '' ?>>Low</option>
                                            <option value="High" <?=$week['pricing'] =='High' ? 'selected="selected"' : '' ?>>High</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="ratecard_week[<?=$daterange['period_id']?>][<?=$daterange['starts']?>][discount]" value="<?=$daterange['discount']?>" size="4" /></td>
                                    <td>
                                        <select name="ratecard_week[<?=$ratecard['period_id']?>][<?=$daterange['starts']?>][arrival]">
                                            <option value=""></option>
                                            <?=HTML::optionsFromArray(
                                                array(
                                                    'Any' => __('Any'),
                                                    'Monday' => __('Monday'),
                                                    'Tuesday' => __('Tuesday'),
                                                    'Wednesday' => __('Wednesday'),
                                                    'Thursday' => __('Thursday'),
                                                    'Friday' => __('Friday'),
                                                    'Saturday' => __('Saturday'),
                                                    'Sunday' => __('Sunday')
                                                ),
                                                $daterange['arrival']
                                            );?>
                                        </select>
                                    </td>
                                    <td></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

                <!-- Groups tab -->
                <div role="tabpanel" class="tab-pane form-horizontal" id="edit-ratecard-tab-groups">
                    <div class="col-sm-12">

                        <div class="form-group">
                            <label class="col-sm-12" for="link-ratecard-group"><?= __('Group') ?></label>
                            <div class="col-sm-6">
                                <select class="form-control ib-combobox" id="link-ratecard-group" data-placeholder="<?= __('Select a Group') ?>">
                                    <option value=""></option>
                                    <?php
                                    foreach ($groups as $group) {
                                        ?>
                                        <option value="<?=$group['id']?>"
                                                data-name="<?=$group['name']?>"
                                                ><?=$group['name']?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <button type="button" class="btn btn-default" id="link-ratecard-group-button"><?=__('Link')?></button>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <table class="table table-striped dataTable ratecard-groups-table" id="ratecard-groups-table">
                                <thead>
                                <tr>
                                    <th scope="col"><?= __('ID') ?></th>
                                    <th scope="col"><?= __('Group') ?></th>
                                    <th scope="col"><?= __('Actions') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (isset($ratecard['groups']))
                                    foreach ($ratecard['groups'] as $group) {
                                        ?>
                                        <tr data-group-id="<?=$group['id']?>">
                                            <td><a href="/admin/propman/edit_group/<?=$group['id']?>"><?=$group['id']?></a></td>
                                            <td><a href="/admin/propman/edit_group/<?=$group['id']?>"><?=$group['name']?></a></td>
                                            <td>
                                                <input type="hidden" name="has_group_id[]" value="<?=$group['id']?>" />
                                                <button
                                                    type="button"
                                                    class="btn-link list-delete-button"
                                                    title="<?= __('Delete') ?>"
                                                    data-group-id="<?=$group['id']?>"
                                                    onclick="$(this).parents('tr').remove();">
                                                    <span class="icon-times"></span> <?= __('Delete') ?>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
        </div>
    </div>

    <div class="col-sm-12" style="clear: both;">
        <div class="well">
            <button type="submit" class="btn btn-primary" name="action" value="save"><?= __('Save') ?></button>
            <button type="submit" class="btn btn-primary" name="action" value="save_and_exit"><?= __('Save & Exit') ?></button>
            <button type="reset" class="btn btn-default"><?= __('Reset') ?></button>
            <?php
            if (is_numeric(@$ratecard['id'])) {
            ?>
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete-ratecard-modal"><?= __('Delete') ?></button>
            <?php
            }
            ?>
            <a href="/admin/propman/rate_cards" class="btn btn-default"><?= __('Cancel') ?></a>
        </div>
    </div>

</form>

<script>
// serialize can not detect clicked button. manually set it.
$("form[name='ratecard-edit'] button[name=action]").on("click", function(){
    $("#ratecard-form-serialized [name=action]").val(this.value);
});

// serialize form data into one hidden input to overcome php max post vars limitation
$("form[name='ratecard-edit']").on("submit", function(e){
    var data = $(this).serialize();
    $("#ratecard-form-serialized [name=data]").val(data);
    $("#ratecard-form-serialized").submit();
    return false;
});

</script>
<div class="modal fade" tabindex="-1" role="dialog" id="delete-ratecard-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/propman/delete_rate_card" method="post">
                <input type="hidden" name="id" value="<?=@$ratecard['id']?>" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= __('Delete rate card') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?= __('Are you sure you want to delete this rate card?') ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="delete-ratecard-button"><?= __('Delete') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-used-ratecard-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="delete-used-ratecard" method="post" action="/admin/propman/delete_used_ratecard">
                <input type="hidden" name="id" value="" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?= __('Delete rate card') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?= __('This Rate Card is currently linked to a Group.') ?></p>
                    <p><?= __('Do you wish to continue?') ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="delete-used-ratecard-button"><?= __('Delete') ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="bulk-change-rates-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= __('Bulk Change Rates') ?></h4>
            </div>
            <div class="modal-body form-horizontal">
                <?php
                $input_prefix = 'bulk-change-rates-';
                include 'includes/rate_fields.php';
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="bulk-change-rates-update" data-dismiss="modal"><?= __('Update All') ?></button>
                <button type="button" class="btn btn-default" id="bulk-change-rates-cancel" data-dismiss="modal"><?= __('Cancel') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="date-range-conflict-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= __('Date ranges conflict') ?></h4>
            </div>
            <div class="modal-body form-horizontal">
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Cancel') ?></button>
            </div>
        </div>
    </div>
</div>
