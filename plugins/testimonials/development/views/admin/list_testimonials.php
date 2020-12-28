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
<table class='table table-striped dataTable dataTable-collapse'>
	<thead>
		<tr>
            <th scope="col">Image (thumb)</th>
            <th scope="col">Title</th>
            <th scope="col">Category</th>
            <th scope="col">Summary</th>
            <th scope="col">Date</th>
            <th scope="col">Company</th>
            <th scope="col">Signature</th>
            <th scope="col">Last Modified</th>
            <th scope="col">Modified By</th>
            <th scope="col">Publish</th>
            <th scope="col">Delete</th>
            <th scope="col">Options</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($testimonials as $id => $testimonial): ?>
		<tr>
		<td data-label="Image">
			<a href='<?php echo URL::Site('/admin/testimonials/add_edit_item/' . $testimonial['id']); ?>'>
                <?php if ( ! empty($testimonial['image'])): ?>
                    <img src="<?php echo Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$testimonial['image'], 'testimonials/_thumbs_cms');?>"
                         width="120" alt="<?= $testimonial['image'] ;?>"/>
                <?php endif; ?>
			</a>
		</td>
		<td data-label="Title"><a href='<?php echo URL::Site('/admin/testimonials/add_edit_item/' . $testimonial['id']); ?>'><?php echo $testimonial['title']; ?></a></td>
		<td data-label="Category"><a href='<?php echo URL::Site('/admin/testimonials/add_edit_item/' . $testimonial['id']); ?>'><?php echo $testimonial['category']; ?></a></td>
		<td data-label="Summary"><a href='<?php echo URL::Site('/admin/testimonials/add_edit_item/' . $testimonial['id']); ?>'><?php echo Text::limit_chars($testimonial['summary'],25," ...",true) ?></a></td>
		<td data-label="Date">
			<a href='<?php echo URL::Site('/admin/testimonials/add_edit_item/' . $testimonial['id']); ?>'>
				<?php echo (!empty($testimonial['event_date']))? date('d-m-Y', strtotime($testimonial['event_date'])) : '&nbsp;'; ?>
			</a>
		</td>
		<td data-label="Company"><a href='<?php echo URL::Site('/admin/testimonials/add_edit_item/' . $testimonial['id']); ?>'><?php echo $testimonial['item_company']; ?></a></td>
<!--		<td><a href='--><?php //echo URL::Site('/admin/testimonials/add_edit_item/' . $testimonial['id']); ?><!--'>--><?php //echo date('d-m-Y', strtotime($testimonial['date_publish'])); ?><!--</a></td>-->
		<td data-label="Signature"><a href='<?php echo URL::Site('/admin/testimonials/add_edit_item/' . $testimonial['id']); ?>'><?php echo $testimonial['item_signature']; ?></a></td>
<!--		<td><a href='--><?php //echo URL::Site('/admin/testimonials/add_edit_item/' . $testimonial['id']); ?><!--'>--><?php //echo date('d-m-Y', strtotime($testimonial['date_remove'])); ?><!--</a></td>-->
		<td data-label="Last Modified"><a href='<?php echo URL::Site('/admin/testimonials/add_edit_item/' . $testimonial['id']); ?>'><?php echo $testimonial['date_modified']; ?></a></td>
		<td data-label="Modified By">
			<a href='<?php echo URL::Site('/admin/testimonials/add_edit_item/' . $testimonial['id']); ?>'>
				<?php echo $testimonial['modified_by_role'].' '.$testimonial['modified_by_name']; ?>
			</a>
		</td>
		<td data-label="Publish" id="publish_<?=$testimonial['id']?>" class="publish" data-item_publish="<?php echo $testimonial['publish'];?>">
			<?php echo (($testimonial['publish'] == '1')? '<i class="icon-ok"></i>' : '<i class="icon-ban-circle"></i>')?>
		</td>
		<td data-label="Delete" id="delete_<?=$testimonial['id']?>" class="delete"><i class="icon-remove-circle"></i></td>
		<td data-label="Options"><a href='<?php echo URL::Site('/admin/testimonials/add_edit_item/' . $testimonial['id']); ?>'><i class="icon-edit"></i></a></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
