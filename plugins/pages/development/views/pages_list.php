<?php
if (isset($alert))
{
    echo $alert;
}
?>
<?php
if(isset($alert)){
    ?>
    <script>
        remove_popbox();
    </script>
    <?php
}
?>
<table class='table table-striped dataTable'>
    <thead>
        <tr>
            <th>ID</th>
            <th>Page name</th>
            <th>Title</th>
            <th>Mode</th>
            <th>Category</th>
            <th>Layout</th>
            <th>Modified</th>
            <th>Publish</th>
            <th>Options</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pages as $id => $page): ?>
            <?php
            if($page['publish'] == '1')
                $icon = '<span class="hidden">0</span><span class="icon-ok"></span>';
            else
                $icon = '<span class="hidden">1</span><span class="icon-remove"></span>';

            if (strrpos($page['name_tag'], '.')){
                $page_tag = $page['name_tag'];
                $found_position = strrpos($page_tag, '.');
                $page['name_tag'] = substr($page_tag, 0, $found_position);
            }
            ?>
            <tr data-id="<?= $page['id']?>" data-draft_of="<?= $page['draft_of'] ?>">
                <td><a href='<?= URL::Site('admin/pages/edit_pag/' . $page['id']); ?>'><?= $page['id']; ?></a></td>
                <td><a href='<?= URL::Site('admin/pages/edit_pag/' . $page['id']); ?>'><?= $page['name_tag']; ?></a></td>
                <td><a href='<?= URL::Site('admin/pages/edit_pag/' . $page['id']); ?>'><?= $page['title']; ?></a></td>
                <td><a href='<?= URL::Site('admin/pages/edit_pag/' . $page['id']); ?>'><?= $page['draft_of'] ? 'Draft of #'.$page['draft_of'] : 'Live'; ?></a></td>
                <td><a href='<?= URL::Site('admin/pages/edit_pag/' . $page['id']); ?>'><?= $page['category']; ?></a></td>
                <td><a href='<?= URL::Site('admin/pages/edit_pag/' . $page['id']); ?>'><?= $page['layout']; ?></a></td>

                <td><a href='<?= URL::Site('admin/pages/edit_pag/' . $page['id']); ?>'><?= $page['last_modified']; ?></a></td>
                <td id="publish_<?= $page['id'] ?>" class="publish"><?= $icon ?></td>
                <td><a href="/<?= $page['name_tag'].($page['draft_of'] ? '?draft=1' : '') ?>" target="_blank">View</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
