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
<div id="list_signatures_wrapper">
    <table id="list_signatures_table" class="table table-striped dataTable">
        <thead>
            <tr>
                <th scope="col">ID</th>
				<th scope="col">Title</th>
                <th scope="col">Created</th>
				<th scope="col">Updated</th>
				<th scope="col">Edit</th>
				<th scope="col">Delete</th>
            </tr>
        </thead>
		<tbody>
		<?php foreach($signatures as $signature){ ?>
			<tr id="signatures-<?=$signature['id']?>">
				<td><a href="/admin/messaging/signature/<?=$signature['id']?>"><?=$signature['id']?></a></td>
                <td><a href="/admin/messaging/signature/<?=$signature['id']?>"><?=$signature['title']?></a></td>
				<td><a href="/admin/messaging/signature/<?=$signature['id']?>"><?=IbHelpers::relative_time_with_tooltip($signature['created'])?></a></td>
				<td><a href="/admin/messaging/signature/<?=$signature['id']?>"><?=IbHelpers::relative_time_with_tooltip($signature['updated'])?></a></td>
                <td><a href="/admin/messaging/signature/<?=$signature['id']?>">edit</a></td>
				<td><a class="delete" data-id="<?=$signature['id']?>">delete</a></td>
			</tr>
		<?php } ?>
		</tbody>
    </table>
	<script>
	$("#list_signatures_table a.delete").on("click", function(){
		var a = this;
		var id = $(this).data("id");
		if(confirm('Are you sure you want to delete?')){
			var $tr = $(this).parents("tr");
			$tr.css("opacity", 0.2);
			$.post(
				"/admin/messaging/signature/" + id,
				{ "id": id, "delete": 1},
				function(response){
					if(response.id){
						$tr.remove();
					} else {
						$tr.css("opacity", 1);
					}
				}
			);
		}
	});
	</script>
</div>
