<table class="table table-striped dataTable">
	<thead>
		<tr>
			<th scope="col">Doc ID</th>
			<?php if ($show_contact_id_column): ?>
				<th scope="col">Contact ID</th>
			<?php endif; ?>
			<th scope="col">Name</th>
<!--			<th scope="col">Created</th>-->
			<th scope="col">Updated</th>
			<th scope="col">Size (KB)</th>
			<th scope="col">Type</th>
            <?php if(Settings::instance()->get('share_document') && $show_share_link):?>
                <th scope="col">Actions</th>
            <?php else:?>
			    <th scope="col">Download</th>
            <?php endif?>
		</tr>
	</thead>
	<tbody>
		<?php if (isset($doc_array['document'])): ?>
			<?php foreach ($doc_array['document'] as $id => $doc): ?>
				<tr>
					<td><?= $doc['id'] ?></td>
					<?php if ($show_contact_id_column): ?>
						<td><?= $doc['contact_id'] ?></td>
					<?php endif; ?>
					<td><?= $doc['name'] ?></td>
<!--					<td>--><?//= DATE::ymdh_to_dmyh($doc['created']) ?><!--</td>-->
					<td><?= DATE::ymdh_to_dmyh($doc['last modified']) ?></td>
					<td><?= $doc['size'] ?></td>
					<td><?= pathinfo($doc['filename'], $pathinfo_extension) ?></td>
                    <?php if(Settings::instance()->get('share_document') && $show_share_link):?>
                        <td>
                            <span title="Location: <?= $doc['path'] ?>"><?= $doc['download'] ?></span>
                            <span><?= $doc['share'] ?></span>
                        </td>

                    <?php else:?>
                        <td title="Location: <?= $doc['path'] ?>"><?= $doc['download'] ?></td>
                    <?php endif?>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	<tbody>
</table>
<script type="text/javascript">
    $(document).ready(function(){
        $('.share-document').click(function(e){
            e.preventDefault();
            var data = {};
            data.id = $(this).attr('id').replace('share_document_', '');
            data.contact_id = $('#family_member-contact_id').val()
            $.ajax({
                type : 'POST',
                url: '/admin/documents/ajax_share_document/',
                data: data,
                dataType: 'json'
            }).done(function(result)
            {
                $('[href="#family-member-documents-tab"]').click();
            }).fail(function(result)
            {
                $('[href="#family-member-documents-tab"]').click();
            });
        });
    });
</script>