<div class="pull-right">
	<div class="btn-group" id="add_new_dropdown_group">
		<button class="btn dropdown-toggle" data-toggle="dropdown">Add new&#8230; <span class="caret"></span>
		</button>
		<ul class="dropdown-menu pull-right" id="add_new_dropdown">
			<?php foreach ($dropdown_items as $key => $value): ?>
			<li>
				<a href="<?php echo URL::Site('admin/contacts/add/' . URL::query(array('type' => $value['contact_type']))); ?>"><?php echo $value['contact_type'] ?></a>
			</li>
			<?php endforeach ?>
		</ul>
	</div>
</div>
