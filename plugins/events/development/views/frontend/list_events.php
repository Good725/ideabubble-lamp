<?=(isset($alert)) ? $alert : ''?>
<form method="get">
    <h1>Search Events</h1>
    <div class="form-group">
        <label class="col-sm-2">Category</label>
        <div class="col-sm-4">
            <select name="category_id">
                <option value=""><?=__('All')?></option>
                <?=html::optionsFromRows('value', 'label', $categories, @$_REQUEST['category_id']);?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2">Tags (separated by comma)</label>
        <div class="col-sm-4">
            <input type="text" name="tags" value="<?=html::chars(@$_REQUEST['tags'])?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2">Date</label>
        <div class="col-sm-4">
            <input type="text" name="date_after" value="<?=html::chars(@$_REQUEST['date_after'])?>" />
            <input type="text" name="date_before" value="<?=html::chars(@$_REQUEST['date_before'])?>" />
        </div>
    </div>

    <div class="well">
        <button type="submit"><?=__('Filter')?></button>
    </div>
</form>
<table class="table">
    <thead>
        <tr>
            <th>Event Name</th>
            <th>Category</th>
            <th>Venue</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach ($events as $event) {
    ?>
        <tr>
            <td><a href="/event/<?=$event['url']?>"><?=html::entities($event['name'])?></a></td>
            <td><?=$event['category']?></td>
            <td><?=$event['venue']?></td>
            <td><?=$event['dates']?></td>
        </tr>
    <?php
    }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">Total <?=$total?> events</th>
        </tr>
    </tfoot>
</table>