<table id="resources_table" class="table table-striped dataTable">
    <thead>
		<tr>
			<th scope="col">Resource ID</th>
			<th scope="col">Name</th>
			<th scope="col">Alias</th>
			<th scope="col">Controller</th>
			<th scope="col">Type</th>
			<th scope="col">Edit</th>
		</tr>
    </thead>
    <tbody>

    <?php foreach($resources as $resource): ?>

        <tr>
            <td><?=$resource->id; ?></td>
            <td><?=$resource->name; ?></td>
            <td><?=$resource->alias; ?></td>
            <td><?=$resource->get_controller_name(); ?></td>
            <td><?=$resource_types[$resource->type_id]; ?></td>
            <td><a class="" href="/admin/settings/add_resources/<?=$resource->id;?>"><i class="icon-pencil"></i></a></td>
        </tr>

    <?php endforeach; ?>


    </tbody>
</table>
