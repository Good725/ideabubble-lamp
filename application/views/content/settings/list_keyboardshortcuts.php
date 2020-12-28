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
<div id="list_shortcuts_wrapper">
	<form name="shortcuts" method="post">
    <table id="list_shortcuts_table" class="table table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
				<th scope="col">Name</th>
                <th scope="col">Url</th>
                <th scope="col">Key Sequence</th>
				<th scope="col"><a class="add">Add</a></th>
            </tr>
        </thead>
		<tbody>
		<?php foreach($shortcuts as $shortcut){ ?>
		<tr>
			<td><?=$shortcut['id']?><input type="hidden" name="shortcut_id[]" value="<?=$shortcut['id']?>" /></td>
			<td><input type="text" name="shortcut_name[]" value="<?=htmlspecialchars($shortcut['name'])?>" /></td>
			<td><input type="text" name="shortcut_url[]" value="<?=htmlspecialchars($shortcut['url'])?>" /></td>
			<td><input type="text" name="shortcut_keysequence[]" value="<?=htmlspecialchars($shortcut['keysequence'])?>" /></td>
			<td><a class="delete" data-shortcut-id="<?=$shortcut['id']?>">delete</a></td>
		</tr>
		<?php } ?>
		</tbody>
		<tfoot>
			<tr><th colspan="5">
				<div class="form-action-group text-left"><button type="submit" name="save" class="btn btn-primary ">Save</button></div></th></tr>
		</tfoot>
    </table>
	</form>
	<script>
	function shortcut_delete()
	{
		var f = $("form[name=shortcuts]");
		if($(this).data("shortcut-id")){
			var input = document.createElement("input");
			input.type = "hidden";
			input.value = $(this).data("shortcut-id");
			input.name = "shortcut_deleted[]";
			f[0].appendChild(input);
		}
		$(this.parentNode.parentNode).remove();
	}

	$("#list_shortcuts_table .add").on("click", function(){
		var tbody = $("#list_shortcuts_table tbody")[0];
		var tr = document.createElement("tr");
		var td = null;
		var input = null;
		
		td = document.createElement("td");
		td.innerHTML = "new";
		input = document.createElement("input");
		input.name = "shortcut_id[]";
		input.value = "new";
		input.type = "hidden";
		td.appendChild(input);
		tr.appendChild(td);
		
		td = document.createElement("td");
		input = document.createElement("input");
		input.name = "shortcut_name[]";
		input.value = "";
		input.type = "text";
		td.appendChild(input);
		tr.appendChild(td);
		
		td = document.createElement("td");
		input = document.createElement("input");
		input.name = "shortcut_url[]";
		input.value = "";
		input.type = "text";
		td.appendChild(input);
		tr.appendChild(td);
		
		td = document.createElement("td");
		input = document.createElement("input");
		input.name = "shortcut_keysequence[]";
		input.value = "";
		input.type = "text";
		td.appendChild(input);
		tr.appendChild(td);
		
		td = document.createElement("td");
		input = document.createElement("a");
		input.name = "shortcut_keysequence[]";
		input.class = "delete";
		input.innerHTML = "delete";
		$(input).on("click", shortcut_delete);
		td.appendChild(input);
		tr.appendChild(td);
		
		tbody.appendChild(tr);
	});
	$("#list_shortcuts_table .delete").on("click", shortcut_delete);
	</script>
</div>
