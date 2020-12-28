<?php
/**
 * Created by PhpStorm.
 * User: dale
 * Date: 01/12/2014
 * Time: 10:22
 */
?>

<table class="dataTable table table-striped">
<thead>
    <tr>
        <th>ID</th>
        <th>Title</th>
    </tr>
</thead>
    <tbody>
    <?php
    foreach($csvs as $key=>$csv):
    $url = Model_CSV::EDIT_URL;
    ?>
        <tr>
            <td><a href="<?=$url.$csv['id'];?>"><?=$csv['id'];?></a></td>
            <td><a href="<?=$url.$csv['id'];?>"><?=$csv['title'];?></a></td>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>