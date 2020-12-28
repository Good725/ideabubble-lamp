<?= isset($alert) ? $alert : '' ?>
<table class="table table-striped dataTable">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Title</th>
            <th scope="col">Make</th>
            <th scope="col">Model</th>
            <th scope="col">Price</th>
            <th scope="col">Engine</th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach($cars as $key=>$car):
    $url = Model_Cars::EDIT_ACTION.$car['id'];
    ?>
        <tr>
            <td><a href="<?=$url;?>"><?=$car['id'];?></a></td>
            <td><a href="<?=$url;?>"><?=$car['title'];?></a></td>
            <td><a href="<?=$url;?>"><?=$car['make'];?></a></td>
            <td><a href="<?=$url;?>"><?=$car['model'];?></a></td>
            <td><a href="<?=$url;?>"><?=$car['price'];?></a></td>
            <td><a href="<?=$url;?>"><?=$car['engine'];?></a></td>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>