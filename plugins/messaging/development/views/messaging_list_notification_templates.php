<?= (isset($alert)) ? $alert : '' ?>
<?php
if(isset($alert)){
    ?>
    <script>
        remove_popbox();
    </script>
    <?php
}
?>
<div id="list_notification_templates_wrapper">
    <table id="list_notification_templates_table" class="table table-striped dataTable dataTable-collapse">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Type</th>
                <th scope="col">Title</th>
                <th scope="col">Category</th>
                <th scope="col">Subject</th>
                <th scope="col">Created</th>
                <th scope="col">Updated</th>
                <th scope="col">Last Sent</th>
                <th scope="col">Publish</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($notification_templates as $template){ ?>
                <tr id="notification-template-<?=$template['id']?>">
                    <td data-label="ID"><?=$template['id']?></td>
                    <td data-label="Type"><?=$template['type']?></td>
                    <td data-label="Title"><?=$template['name']?></td>
                    <td data-label="Category"><?= htmlspecialchars($template['category']) ?></td>
                    <td data-label="Subject"><?=$template['subject']?></td>
                    <td data-label="Created"><?=IbHelpers::relative_time_with_tooltip($template['date_created'])?></td>
                    <td data-label="Updated"><?=IbHelpers::relative_time_with_tooltip($template['date_updated'])?></td>
                    <td data-label="Last sent"><?=$template['last_sent']?></td>
                    <td data-label="Publish">
                        <a class="publish" data-id="<?=$template['id']?>" data-publish="<?=$template['publish']?>">
                            <i class="<?=$template['publish'] ? 'icon-ok' : 'icon-ban-circle'?>"></i>
                        </a>
                    </td>
                    <td data-label="Actions">
                        <?php

                        $options = [
                            ['type' => 'link', 'icon' => 'pencil', 'title' => 'Edit',  'attributes' => ['class' => 'edit-link', 'href' => '/admin/messaging/notification_template/?id='.$template['id']]],
                            ['type' => 'link', 'icon' => 'copy',   'title' => 'Clone', 'attributes' => ['href' => '/admin/messaging/clone_notification_template/?id='.$template['id']]],
                        ];

                        if (Auth::instance()->has_access('messaging_delete_template')) {
                            if (!$template['is_system'] || Auth::instance()->has_access('messaging_delete_system_template')) {
                                $options[] = [
                                    'type'       => 'button',
                                    'icon'       => 'close',
                                    'title'      => 'Delete',
                                    'attributes' => ['class' => 'delete', 'data-id' => $template['id']]
                                ];
                            }
                        }

                        echo View::factory('snippets/btn_dropdown')
                            ->set(['type' => 'actions', 'options' => $options])
                            ->render();
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <script>
        $("#list_notification_templates_table a.publish").on("click", function(){
            var a = this;
            var id = $(this).data("id");
            var publish = parseInt($(this).data("publish"));
            if(publish){
                publish = 0;
            } else {
                publish = 1;
            }
            var $i = $(this).find("i");
            $i.css("opacity", 0.2);
            $.post("/admin/messaging/notification_template_set_publish",
                { "id": id, "publish": publish },
                function(response){
                    publish = response.publish;
                    if(publish){
                        $i.attr("class", "icon-ok");
                    } else {
                        $i.attr("class", "icon-ban-circle");
                    }
                    $(a).data("publish", publish);
                    $i.css("opacity", 1);
                });
        });
        $("#list_notification_templates_table .delete").on("click", function(){
            var a = this;
            var id = $(this).data("id");
            if(confirm('Are you sure you want to delete?')){
                var $tr = $(this).parents("tr");
                $tr.css("opacity", 0.2);
                $.post("/admin/messaging/notification_template_delete",
                    { "id": id, "delete": 1},
                    function(response){
                        if(response.id){
                            $tr.remove();
                        } else {
                            $tr.css("opacity", 1);
                        }
                    });
            }
        });
    </script>
</div>
