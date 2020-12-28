<table id="crontasks_datatable" class="datatable table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Plugin</th>
            <th>Action</th>
            <th>Frequency</th>
            <th>Last Run</th>
            <th>Edit</th>
            <th>Publish</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach($tasks as $key=>$task):
        $edit_url = Model_Cron::EDIT_ANCHOR.DIRECTORY_SEPARATOR.$task['id'];
    ?>
        <tr>
            <td><a href="<?=$edit_url;?>"><?=$task['id'];?></a></td>
            <td><a href="<?=$edit_url;?>"><?=$task['title'];?></a></td>
            <td><a href="<?=$edit_url;?>"><?=$task['plugin'];?></a></td>
            <td><a href="<?=$edit_url;?>"><?=$task['action'];?></a></td>
            <td><a href="<?=$edit_url;?>"><?=$task['frequency'];?></a></td>
            <td><a href="<?=$edit_url;?>"><?=$task['last_started'];?></a></td></td>
            <td><a href="<?=$edit_url;?>"><i class="icon-pencil"></i></a></td>
            <td><a href="<?=$edit_url;?>"><?=$task['publish'];?></a></td>
            <td><a href="<?=$edit_url;?>"><?=$task['delete'];?></a></td>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>