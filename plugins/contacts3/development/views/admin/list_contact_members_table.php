<?php $selected_member_id = isset($selected_member_id) ? $selected_member_id : $contact->get_id() ?>
<table id="list_family_members_table" class="table dataTable">
	<thead>
		<tr>
			<th scope="col">ID</th>
			<th scope="col">Name</th>
            <th scope="col">Role</th>
			<th scope="col">Type</th>
			<th scope="col">Mobile</th>
			<th scope="col">Address</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($family_members AS $member):
            if(@$family_not_needed && $contact_id == $member['id']) {
                continue;
            }?>
			<tr data-id="<?= $member['id'] ?>"<?= ($member['id'] == $selected_member_id) ? ' class="selected"' : '' ?>>
				<td><a href="/admin/contacts3/add_edit_contact/<?=$member['id'];?>"><?=$member['id'];?></a></td>
				<td><a href="/admin/contacts3/add_edit_contact/<?=$member['id'];?>"><?=$member['title'].' '.$member['first_name'].' '.$member['last_name'];?></a></td>
                <td><a href="/admin/contacts3/add_edit_contact/<?=$member['id'];?>"><?= $member['role'] ; ?></a></td>
				<td><a href="/admin/contacts3/add_edit_contact/<?=$member['id'];?>"><?=$member['type'];?></a></td>
				<td><a href="/admin/contacts3/add_edit_contact/<?=$member['id'];?>"><?=$member['mobile'];?></a></td>
				<td><a href="/admin/contacts3/add_edit_contact/<?=$member['id'];?>"><?=trim(trim(trim($member['address1'].', '.$member['address2']), ','));?></a></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>