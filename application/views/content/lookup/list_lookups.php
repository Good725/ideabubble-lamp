<div class="right">
	<a href="/admin/lookup/create_lookup" class="btn btn-primary"><span class="plus-icon"></span> <?= __('Create Lookup') ?></a>
</div>


<?=(isset($alert)) ? $alert : ''?>
	<table class="table table-striped dataTable table-condensed " id="list-lookups-table">
		<thead>
		<tr>
			<th scope="col"><?= __('ID') ?></th>
			<th scope="col"><?= __('Field') ?></th>
			<th scope="col"><?= __('Label') ?></th>
			<th scope="col"><?= __('Value') ?></th>
			<th scope="col"><?= __('Default') ?></th>
			<th scope="col"><?= __('Created') ?></th>
			<th scope="col"><?= __('Updated') ?></th>
			<th scope="col"><?= __('Autor') ?></th>
			<th scope="col"><?= __('Actions') ?></th>
			<th scope="col"><?= __('Public') ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ($lookups as $lookup) {
			?>
			<tr>
				<td><?=$lookup['id'] ? ($lookupsUpdate ? '<a href="/admin/events/lookup_update/' . $lookup['id'] . '">' . $lookup['id'] . '</a>' : $lookup['id']) : ''?></td>
				<td><?=$lookup['fname']?></td>
				<td><?=$lookup['label']?></td>
				<td><?=$lookup['value']?></td>
				<td><?=$lookup['is_default'] ? 'yes' : 'no' ?></td>
				<td><?=$lookup['created']?></td>
				<td><?=$lookup['updated']?></td>
				<td><?=$lookup['author']?></td>
				<td>
					<div class="dropdown">
					<button class="btn btn-default dropdown-toggle btn-actions" type="button" data-toggle="dropdown">
						<?= __('Actions') ?>
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li>
							<a href="/admin/lookup/edit_lookup/<?= $lookup['id'] ?>">
								<span class="icon-pencil"></span> <?= __('Edit') ?>
							</a>
						</li>
                        <li>
                            <a href="/admin/lookup/clone_lookup/<?= $lookup['id'] ?>">
                                <span class="icon-copy"></span> <?= __('Clone') ?>
                            </a>
                        </li>
						<?php if (!$lookup['is_default']): ?>
                        <li>
                            <a href="/admin/lookup/make_default_lookup/<?= $lookup['id'] ?> ?>">
                                <span class="icon-check"></span> <?= __('Make Default') ?>
                            </a>
                        </li>
						<?php endif; ?>
						<?php if (!$lookup['public']): ?>
						<li>
							<a href="/admin/lookup/publish_lookup/<?= $lookup['id'] ?> ">
								<span class="icon-thumbs-up"></span> <?= __('Publish') ?>
							</a>
						</li>
						<?php endif; ?>
                        <li>
                            <a href="/admin/lookup/set_unique_value_lookup/<?= $lookup['id'] ?>">
                                <span class="icon-upload"></span> <?= __('Set unique value') ?>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/lookup/delete_lookup/<?= $lookup['id'] ?>">
                                <span class="icon-ban-circle"></span> <?= __('Delete') ?>
                            </a>
                        </li>
					</ul>
					</div>
				</td>
				<td><?=$lookup['public'] ? 'yes' : 'no' ?></td>
			</tr>
			<?php
			//}
		}
		?>
		</tbody>
	</table>
	<script>
		$("#list-lookups-table").on("click", ".list-email-button", function(){
			var url = this.href;
			$.post(
				url,
				{},
				function (response) {
					if (response.success) {
						alert('Lookup has been emailed.');
					} else {
						alert("Unable to send email");
					}
				}
			);
			return false;
		});
	</script>