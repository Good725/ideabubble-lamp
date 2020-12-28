<style>
    #db-audit td, #db-audit td * {
        font-size: 10px !important;
    }
</style>
<div id="db-audit" style="position: absolute; top: 0px; left: 0px; z-index: 99999; background-color: #fff;">
    <a href="/admin/settings/dalm_audit?refresh=1">Refresh Report</a><span>(last updated *<?=date('Y-m-d H:i:s', filemtime(APPPATH . '/cache/db_report.txt'))?>)</span>
<table class="table dataTable">
    <thead>
        <tr>
            <th>Type</th><th>Name</th><th>Model</th><th>Used By</th><th>Sql(From Db)</th><th>Suggested Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach($report as $object){
        if (@$_GET['suggestions'] == 1 && @$object['suggest'] == '') {
            continue;
        }
    ?>
        <tr data-name="<?=$object['name']?>">
            <td><?=$object['type']?></td>
            <td><?=$object['name']?></td>
            <td><ul><!-- style="height:50px; overflow: auto;" onclick="if(this.expanded){this.style.height='50px'} else {this.expanded=true;this.style.height='auto'}" --><?php
            foreach ($object['model'] as $model) {
                echo '<li style="white-space: nowrap; text-wrap: none;" data-file="' . $model . '">' . str_replace(array(PROJECTPATH, ENGINEPATH), array('engine/', ''), $model) . '</li>';
            }?></ul></td>
            <td><ul><?php
            foreach ($object['usedBySql'] as $sql) {
                echo '<li style="white-space: nowrap; text-wrap: none;" data-file="' . $sql . '">' . str_replace(array(PROJECTPATH, ENGINEPATH), array('engine/', ''), $sql) . '</li>';
            }
            foreach ($object['usedByPhp'] as $php) {
                echo '<li style="white-space: nowrap; text-wrap: none;" data-file="' . $php . '">' . str_replace(array(PROJECTPATH, ENGINEPATH), array('engine/', ''), $php) . '</li>';
            }
            ?></ul></td>
            <td><pre style="max-width: 400px;"><?=$object['sql']?></pre></td><!-- style="width:50px; height:50px;" onclick="if(this.expanded){this.style.width='50px';this.style.height='50px'} else {this.expanded=true;this.style.width='auto';this.style.height='auto'}" -->
            <td><pre style="height:70px; display: block; overflow: auto;" onclick="if(this.expanded){this.style.height='70px'} else {this.expanded=true;this.style.height='auto'}"><?=@$object['suggest']?></pre></td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>
</div>
<div id="feditor" style="position: fixed; top: 40%; left: 20%; width: 60%; height: 60%; background-color: #fff; border-style: solid; border-width: 1px; display: none; z-index: 999999999">
    <textarea id="feditor-content" style="width: 100%; height: 90%; overflow: auto;"></textarea>
    <button type="button" id="feditor-close" style="display: inline-block; margin: 5px auto 0px auto;height: 9% ">CLOSE</button>
    <button type="button" id="feditor-save" style="width: auto; display: inline-block; margin: 5px auto 0px auto; height: 9%">SAVE</button>
</div>
<script>
    var w = $(window).width();
    $('#db-audit').css('width', w);
    $('#db-audit li').on('click', function(){
        var file = $(this).data('file');
        var name = $(this).parents('tr').data('name');
        $.get('/admin/settings/direct_edit', {'file': file, 'find': name}, function(response){
            $("#feditor").css("display", "block");
            $("#feditor-content").val(response);
            $("#feditor-content").data("file", file);
            $("#feditor-save").html("Save: "+ file);
            $("#feditor-content").focus();
        });
    });
    $("#feditor-close").on("click", function(){
        $("#feditor").css("display", "none");
    });
    $("#feditor-save").on("click", function(){
        if (confirm('save?')) {
            var file = $("#feditor-content").data("file");
            var content = $("#feditor-content").val();
            $.post('/admin/settings/direct_edit', {'file': file, 'content': content}, function(response){
                alert(response);
                $("#feditor-content").focus();
            })
        }
    })
</script>
