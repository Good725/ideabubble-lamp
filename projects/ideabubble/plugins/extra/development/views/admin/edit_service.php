<h2>Edit Service - <?= $data['url'] ?></h2>
<div class="col-sm-12 tabbable">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab">Editor</a></li>
        <li><a href="#tab2" data-toggle="tab">Info</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab1">
            <?php include 'add_edit_service.php'; ?>
        </div>
        <div class="tab-pane" id="tab2">
            <fieldset>
                <legend>Domain Info</legend>
                <pre><?= @trim(implode("\n", $data['whois_data']['rawdata'])); ?></pre>
            </fieldset>
        </div>
    </div>
</div>