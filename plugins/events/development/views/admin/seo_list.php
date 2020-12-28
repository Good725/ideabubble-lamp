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

<form method="post">
  <div class="form-action-group fixedMenu">
    <button type="submit" class="btn" id="btn_save">Save</button>
  </div>

  <table class='table table-striped dataTable'>
    <thead>
      <tr>
        <th>Page Name</th>
        <th>Title</th>
        <th>SEO Keywords</th>
        <th>Meta Description</th>
        <th>Footer Text</th>
        <th>X-Robots-Tag</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($events as $id => $event): ?>
        <tr>
          <input type="hidden" value="<?= $event['id'] ?>" name="seo[<?=$id?>][id]">
          <td><a href="/admin/events/edit_event/<?= $event['id'] ?>" class="edit-link"><?= $event['name'] ?></a></td>
          <td><input value="<?php echo $event['name']; ?>" name="seo[<?=$id?>][name]" /></td>
          <td><input value="<?php echo trim($event['seo_keywords']); ?>" name="seo[<?=$id?>][seo_keywords]"</td>
          <td><input value="<?php echo trim($event['seo_description']); ?>" name="seo[<?=$id?>][seo_description]"</td>
          <td><textarea name="seo[<?=$id?>][footer]"><?php echo $event['footer']; ?></textarea></td>
          <td>
            <select class="form-control" name="seo[<?=$id?>][x_robots_tag]" id="edit_page_x_robots_tag">
              <option value=""></option>
              <?=HTML::optionsFromArray(
                  array(
                      'noindex' => 'noindex',
                      'nofollow' => 'nofollow',
                      'noarchive' => 'noarchive',
                      'noindex,nofollow' => 'noindex,nofollow'
                  ),
                  @$event['x_robots_tag']
              )?>
            </select>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</form>
