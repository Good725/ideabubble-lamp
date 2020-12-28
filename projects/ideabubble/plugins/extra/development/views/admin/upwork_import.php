<h2>Upwork Import</h2>
<?php if(1) { ?>
<div class="col-sm-12">
    <form method="post" enctype="multipart/form-data">
        <label>upwork log json</label><input type="file" name="upwork" />
        <button type="submit">continue</button>
    </form>
</div>

    <table id="upwork-log">
    <thead>
        <tr>
            <th>Task</th>
            <th>Comment</th>
            <th>Date</th>
            <th>Duration</th>
            <th>Id</th>
            <th><button type="button" class="sync all">sync all</button> </th>
        </tr>
    </thead>
    <tbody>
    <tr class="sample">
        <td><input type="text" class="key" name="worklog[index][key]" value=""></td>
        <td><input type="text" class="comment" name="worklog[index][comment]" value=""></td>
        <td><input type="text" class="date" name="worklog[index][date]" value=""></td>
        <td><input type="text" class="duration" name="worklog[index][duration]" value=""></td>
        <td><input type="text" class="id" readonly="readonly" name="worklog[index][id]" value=""></td>
        <td><button type="button" class="sync">sync</button></td>
    </tr>
    <?php
    if (@$upworklog)
    foreach ($upworklog as $i => $row) {
        preg_match('/^(.+\-\d+)(\s*\-\s*(.*))?$/mi', $row[1], $key_comment);
        //var_dump($key_comment);
        $hours = floor($row[3]);
        $minutes = round(($row[3] - $hours) * 60);
        $duration = trim(($hours > 0 ? $hours . 'h' : '') . ' ' . ($minutes > 0 ? $minutes . 'm' : ''));
    ?>
        <tr>
            <td><input type="text" class="key" name="worklog[<?=$i?>][key]" value="<?=@$key_comment[1]?>"></td>
            <td><input type="text" class="comment" name="worklog[<?=$i?>][comment]" value="<?=@$key_comment[3]?>"></td>
            <td><input type="text" class="date" name="worklog[<?=$i?>][date]" value="<?=$row[2]?>"></td>
            <td><input type="text" class="duration" name="worklog[<?=$i?>][duration]" value="<?=$duration?>"></td>
            <td><input type="text" class="id" readonly="readonly" name="worklog[<?=$i?>][id]" value=""></td>
            <td><button type="button" class="sync">sync</button></td>
        </tr>
    <?php
    }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6"><button type="button" id="newrow">new</button> </th>
        </tr>
    </tfoot>
</table>
<script>
var $trsample = $(".sample");
$trsample.remove();
$("#newrow").on("click", function(){
    var $tr = $trsample.clone();
    $tr.find("input").each(function(){
        var index = $("#upwork-log tbody tr").length;
        this.name = this.name.replace("[index]", "[" + index + "]");
    });

    $("#upwork-log tbody").append($tr);
});

$(document).on("ready", function(){
    $(".sync.all").on("click", function(){
        if (!this.disabled) {
            this.innerHTML == '...';
            this.value = '...'
            this.disabled = true;
            var buttons = $("tbody .sync");
            var i = 0;

            function sync_next()
            {
                if (i < buttons.length) {
                    sync_log(buttons[i], sync_next);
                    ++i;
                }
            }

            sync_next();
        }
    });

    function sync_log(button, callback){
        if (!button.disabled) {
            button.innerHTML == '...';
            button.value = '...'
            button.disabled = true;
            var $tr = $(button).parents("tr");
            $.post(
                "/admin/extra/jira_worklog_add",
                {
                    key: $tr.find(".key").val(),
                    comment: $tr.find(".comment").val(),
                    date: $tr.find(".date").val(),
                    duration: $tr.find(".duration").val(),
                },
                function (response) {
                    console.log(response);
                    if (response.id) {
                        $tr.find(".id").val(response.id);
                    }
                    if (callback) {
                        callback();
                    }
                }
            );
        }
    }

    $("tbody button.sync").on("click", function(){
        sync_log(this);
    });
});
</script>
<?php } else { ?>
<div class="col-sm-12">
    <form method="post" enctype="multipart/form-data">
        <label>upwork log json</label><input type="file" name="upwork" />
        <button type="submit">continue</button>
    </form>
</div>
<?php } ?>