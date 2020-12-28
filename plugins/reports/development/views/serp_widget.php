<span style='font-family: "Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif;font-size: 16px;color: #3E576F;fill: #3E576F;display:block;width:100%;text-align:center;'><?=$widget_title;?></span>
<h6><?=ucwords($widget_engine);?></h6>
<?php
if(count($keywords) > 0 AND $results):
?>
<table class="table serp_widget_table" style="max-height:258px;overflow-y:scroll;">
    <thead>
    <th>Keyword</th>
    <th>Previous</th>
    <th>Current</th>
    <th>Change</th>
    </thead>
    <tbody>
    <?php
    foreach($keywords AS $key=>$value):
    ?>
        <tr>
            <td><?=$value['keyword'];?></td>
            <td><?=(intval($value['last_position'])) < 1 ? 'n/a' :$value['last_position'];?></td>
            <td><?=(intval($value['current_position']) <= 1) ? $value['current_position'] : 'n/a';?></td>
            <td><?=($value['change'] > 0 ? '+'.$value['change'] : $value['change']);?></td>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>
<?php
else:
?>
    <div style="disply:block;height:150px;">
    <span style="display: inline-block;margin-top: 55px;font-size: 16px;font-weight: bold;text-align:center;width:100%;">
        Signup for this service!<br/>
        <a href="mailto:sales@ideabubble.ie">Sales@Ideabubble.ie</a><br/> - only €49 per month!
    </span>
    </div>
<?php
endif;
?>