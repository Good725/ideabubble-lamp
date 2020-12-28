<?=(isset($alert)) ? $alert : '';?>
<table class='table table-striped dataTable'>
    <thead>
    <tr>
        <th>ID</th>
        <th>Category</th>
        <th>Summary</th>
        <th>Parent</th>
        <th>Published</th>
        <?php if ( ! empty($can_delete_reports)): ?>
            <th>Delete</th>
        <?php endif; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($categories AS $category): ?>
        <tr data-category_id="<?=$category['id']?>">
            <td><a href='<?php echo URL::Site('admin/reports/add_edit_category/'.$category['id']); ?>'><?=$category['id']?></a></td>
            <td><a href='<?php echo URL::Site('admin/reports/add_edit_category/'.$category['id']); ?>'><?=$category['name']?></a></td>
            <td><a href='<?php echo URL::Site('admin/reports/add_edit_category/'.$category['id']); ?>'><?=$category['summary']?></a></td>
            <td><a href='<?php echo URL::Site('admin/reports/add_edit_category/'.$category['id']); ?>'><?=$category['parent']?></a></td>
            <td class="publish"><i class="<?=($category['publish'] == 1 ? 'icon-ok' : 'icon-remove');?>"></i></td>
            <?php if ( ! empty($can_delete_reports)): ?>
                <td class="delete_category"><i class="icon-remove"></i></td>
            <?php endif; ?>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<script>
    $(document).ready(function(){

        $(".save_btn").click(function(){
            $("#action").val($(this).data('action'));
            $("#category_edit_form").submit();
        });

        $("#project_publish_toggle > button").click(function(){
            $("#publish").val($(this).val());
        });

        $(".delete_category").click(function(){
            $.post('/admin/reports/delete_category',{category_id:$(this).parent('tr').data('category_id')},function(result){
                if(result == "1")
                {
                    window.location.href = '/admin/reports/categories';
                }
            });
        });
    });
</script>
